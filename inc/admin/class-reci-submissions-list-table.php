<?php
/**
 * Submissions review queue, as a standard WordPress list table.
 *
 * The screen was hand-rolled markup: a bespoke filter form, a plain table and
 * no pagination, sorting, bulk actions or status counts. WP_List_Table gives
 * all of that and, more usefully, makes the screen behave the way every other
 * list in wp-admin does.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Reci_Submissions_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct(
			[
				'singular' => 'submission',
				'plural'   => 'submissions',
				'ajax'     => false,
			]
		);
	}

	/**
	 * Post types this queue covers.
	 *
	 * @return array<int,string>
	 */
	private function post_types(): array {
		return function_exists( 'reci_media_hub_submission_supported_post_types' )
			? reci_media_hub_submission_supported_post_types()
			: [ 'post' ];
	}

	public function get_columns() {
		return [
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Title', 'reci-media-hub' ),
			'post_type'    => __( 'Type', 'reci-media-hub' ),
			'contributor'  => __( 'Contributor', 'reci-media-hub' ),
			'status'       => __( 'Status', 'reci-media-hub' ),
			'date'         => __( 'Submitted', 'reci-media-hub' ),
		];
	}

	public function get_sortable_columns() {
		return [
			'title' => [ 'title', false ],
			'date'  => [ 'date', true ],
		];
	}

	/**
	 * Status links across the top, with counts.
	 *
	 * Pending first and default, because that is what the queue is for.
	 */
	protected function get_views() {
		$base    = admin_url( 'admin.php?page=reci-submissions' );
		$current = $this->current_status();
		$views   = [];

		foreach ( [ 'pending' => __( 'Pending', 'reci-media-hub' ), 'draft' => __( 'Draft', 'reci-media-hub' ), 'publish' => __( 'Published', 'reci-media-hub' ), 'any' => __( 'All', 'reci-media-hub' ) ] as $status => $label ) {
			$count = $this->count_for_status( $status );
			$url   = add_query_arg( 'post_status', $status, $base );

			$views[ $status ] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( $url ),
				$current === $status ? ' class="current" aria-current="page"' : '',
				esc_html( $label ),
				$count
			);
		}

		return $views;
	}

	private function current_status(): string {
		$status = isset( $_GET['post_status'] ) ? sanitize_key( wp_unslash( $_GET['post_status'] ) ) : 'pending';

		return in_array( $status, [ 'pending', 'draft', 'publish', 'any' ], true ) ? $status : 'pending';
	}

	private function count_for_status( string $status ): int {
		$query = new WP_Query(
			[
				'post_type'              => $this->post_types(),
				'post_status'            => 'any' === $status ? [ 'pending', 'draft', 'publish' ] : $status,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			]
		);

		return (int) $query->found_posts;
	}

	protected function get_bulk_actions() {
		return [
			'publish' => __( 'Publish', 'reci-media-hub' ),
			'pending' => __( 'Move to pending', 'reci-media-hub' ),
		];
	}

	/**
	 * Post type filter, alongside the standard search box.
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		$selected = isset( $_GET['submission_type'] ) ? sanitize_key( wp_unslash( $_GET['submission_type'] ) ) : '';

		echo '<div class="alignleft actions">';
		echo '<label class="screen-reader-text" for="submission_type">' . esc_html__( 'Filter by type', 'reci-media-hub' ) . '</label>';
		echo '<select name="submission_type" id="submission_type">';
		echo '<option value="">' . esc_html__( 'All types', 'reci-media-hub' ) . '</option>';

		foreach ( $this->post_types() as $type ) {
			$object = get_post_type_object( $type );
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $type ),
				selected( $selected, $type, false ),
				esc_html( $object->labels->singular_name ?? $type )
			);
		}

		echo '</select>';
		submit_button( __( 'Filter', 'reci-media-hub' ), '', 'filter_action', false );
		echo '</div>';
	}

	public function prepare_items() {
		$per_page = 20;
		$status   = $this->current_status();
		$type     = isset( $_GET['submission_type'] ) ? sanitize_key( wp_unslash( $_GET['submission_type'] ) ) : '';
		$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$orderby  = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'date';
		$order    = isset( $_GET['order'] ) && 'asc' === strtolower( (string) wp_unslash( $_GET['order'] ) ) ? 'ASC' : 'DESC';

		$query = new WP_Query(
			[
				'post_type'      => $type && in_array( $type, $this->post_types(), true ) ? $type : $this->post_types(),
				'post_status'    => 'any' === $status ? [ 'pending', 'draft', 'publish' ] : $status,
				's'              => $search,
				'orderby'        => in_array( $orderby, [ 'title', 'date' ], true ) ? $orderby : 'date',
				'order'          => $order,
				'posts_per_page' => $per_page,
				'paged'          => $this->get_pagenum(),
			]
		);

		$this->items = $query->posts;

		$this->set_pagination_args(
			[
				'total_items' => (int) $query->found_posts,
				'per_page'    => $per_page,
				'total_pages' => (int) $query->max_num_pages,
			]
		);

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="submission[]" value="%d" />', (int) $item->ID );
	}

	public function column_title( $item ) {
		$edit  = get_edit_post_link( $item->ID );
		$title = get_the_title( $item ) ?: __( '(untitled)', 'reci-media-hub' );

		$actions = [];

		if ( $edit ) {
			$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( $edit ), esc_html__( 'Edit', 'reci-media-hub' ) );
		}

		if ( 'pending' === $item->post_status && current_user_can( 'publish_post', $item->ID ) ) {
			$actions['publish'] = sprintf(
				'<a href="%s" style="color:#1f7a5a;font-weight:600;">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							[ 'action' => 'reci_publish_submission', 'post' => $item->ID ],
							admin_url( 'admin-post.php' )
						),
						'reci_publish_submission_' . $item->ID
					)
				),
				esc_html__( 'Publish', 'reci-media-hub' )
			);
		}

		$actions['view'] = sprintf( '<a href="%s">%s</a>', esc_url( (string) get_permalink( $item ) ), esc_html__( 'View', 'reci-media-hub' ) );

		return sprintf(
			'<strong><a class="row-title" href="%s">%s</a></strong>%s',
			esc_url( (string) ( $edit ?: get_permalink( $item ) ) ),
			esc_html( $title ),
			$this->row_actions( $actions )
		);
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'post_type':
				$object = get_post_type_object( $item->post_type );

				return esc_html( $object->labels->singular_name ?? $item->post_type );

			case 'contributor':
				$user_id = (int) get_post_meta( $item->ID, '_reci_submission_submitter_user_id', true );
				$user    = $user_id > 0 ? get_user_by( 'id', $user_id ) : get_user_by( 'id', (int) $item->post_author );

				return $user ? esc_html( $user->display_name ) : '&mdash;';

			case 'status':
				$labels = [
					'pending' => __( 'Pending', 'reci-media-hub' ),
					'draft'   => __( 'Draft', 'reci-media-hub' ),
					'publish' => __( 'Published', 'reci-media-hub' ),
				];

				return esc_html( $labels[ $item->post_status ] ?? $item->post_status );

			case 'date':
				return esc_html( get_the_date( 'Y-m-d H:i', $item ) );
		}

		return '';
	}

	public function no_items() {
		esc_html_e( 'Nothing waiting here.', 'reci-media-hub' );
	}

	/**
	 * Apply a bulk action.
	 *
	 * Checked per post rather than once for the screen, so a bulk action cannot
	 * move something the user could not move individually.
	 */
	public function process_bulk_action(): int {
		$action = $this->current_action();

		if ( ! in_array( $action, [ 'publish', 'pending' ], true ) ) {
			return 0;
		}

		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		$ids     = isset( $_REQUEST['submission'] ) ? array_map( 'absint', (array) wp_unslash( $_REQUEST['submission'] ) ) : [];
		$changed = 0;

		foreach ( $ids as $id ) {
			if ( $id <= 0 || ! current_user_can( 'edit_post', $id ) ) {
				continue;
			}

			if ( 'publish' === $action && ! current_user_can( 'publish_post', $id ) ) {
				continue;
			}

			$post = get_post( $id );

			if ( ! $post || ! in_array( $post->post_type, $this->post_types(), true ) || $post->post_status === $action ) {
				continue;
			}

			wp_update_post( [ 'ID' => $id, 'post_status' => $action ] );
			++$changed;
		}

		return $changed;
	}
}
