<?php
/**
 * Admin list table for Journals.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Reci_Journals_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => 'journal',
			'plural'   => 'journals',
			'ajax'     => false,
		] );
	}

	public function get_columns() {
		return [
			'cb'            => '<input type="checkbox" />',
			'user_id'       => __( 'User', 'reci-media-hub' ),
			'reflection_id' => __( 'Reflection', 'reci-media-hub' ),
			'prompt'        => __( 'Prompt', 'reci-media-hub' ),
			'response'      => __( 'Response', 'reci-media-hub' ),
			'created_at'    => __( 'Date', 'reci-media-hub' ),
			'status'        => __( 'Status', 'reci-media-hub' ),
			'flagged_terms' => __( 'Flagged', 'reci-media-hub' ),
		];
	}

	public function get_sortable_columns() {
		return [
			'created_at' => [ 'created_at', true ],
			'user_id'    => [ 'user_id', false ],
		];
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'prompt':
				return esc_html( wp_trim_words( $item->prompt, 10, '...' ) );
			case 'response':
				return esc_html( wp_trim_words( $item->response, 15, '...' ) );
			case 'created_at':
				return esc_html( wp_date( 'Y/m/d g:i a', strtotime( $item->created_at ) ) );
			default:
				return print_r( $item, true );
		}
	}

	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="journal[]" value="%s" />',
			$item->id
		);
	}

	protected function column_user_id( $item ) {
		// Goes through the identity gate rather than reading display_name
		// directly: this screen is reachable by every role holding
		// reci_moderate_journals, and the gate is what decides who may see an
		// anonymous author.
		$identity = reci_journal_author_for_display( (int) $item->id );
		$name     = $identity['name'];

		if ( $identity['is_masked'] || ! $identity['user_id'] ) {
			return esc_html( $name );
		}

		$link = get_edit_user_link( $identity['user_id'] );

		if ( ! $link ) {
			return esc_html( $name );
		}

		$suffix = (int) ( $item->is_anonymous ?? 0 )
			? ' <em>' . esc_html__( '(shared anonymously)', 'reci-media-hub' ) . '</em>'
			: '';

		return sprintf( '<a href="%s">%s</a>%s', esc_url( $link ), esc_html( $name ), $suffix );
	}

	protected function column_reflection_id( $item ) {
		$title = get_the_title( $item->reflection_id );
		$edit_link = get_edit_post_link( $item->reflection_id );
		if ( $edit_link ) {
			return sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html( $title ) );
		}
		return esc_html( $title );
	}

	/**
	 * Render the status column.
	 */
	public function column_status( $item ): string {
		$status = (string) ( $item->status ?? 'private' );
		$label  = reci_journal_status_label( $status );

		$colours = [
			'private'  => '#f0f0f1;color:#50575e',
			'pending'  => '#fcf0dd;color:#8a6116',
			'approved' => '#e4f5ea;color:#1c6b3f',
			'rejected' => '#fbeaea;color:#8a1f1f',
		];

		return sprintf(
			'<span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600;background:%s">%s</span>',
			esc_attr( $colours[ $status ] ?? $colours['private'] ),
			esc_html( $label )
		);
	}

	protected function column_response( $item ) {
		$content = esc_html( wp_trim_words( $item->response, 15, '...' ) );

		$status = (string) ( $item->status ?? 'private' );

		// A private entry has not been shared, so there is nothing to moderate.
		// Everything else can be moved either way: a rejection is not final and
		// an approval can be pulled back.
		if ( ! current_user_can( 'reci_moderate_journals' ) || 'private' === $status ) {
			return $content;
		}

		$actions = [];

		// Keys come from reci_journal_row_action_keys(): 'approve' collides with
		// an unscoped `.approve { display: none; }` in wp-admin's common.css,
		// which renders the link into the HTML and hides it on screen.
		if ( 'approved' !== $status ) {
			$actions['reci-approve'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=reci_journal_approve&journal_id=' . (int) $item->id ), 'reci_journal_moderate_' . (int) $item->id ) ),
				esc_html__( 'Approve', 'reci-media-hub' )
			);
		}

		if ( 'rejected' !== $status ) {
			$actions['reci-reject'] = sprintf(
				'<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
				esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=reci_journal_reject&journal_id=' . (int) $item->id ), 'reci_journal_moderate_' . (int) $item->id ) ),
				esc_js( __( 'Reject this shared reflection?', 'reci-media-hub' ) ),
				esc_html__( 'Reject', 'reci-media-hub' )
			);
		}

		return $content . $this->row_actions( $actions );
	}

	/**
	 * Render the flagged-term column, so a moderator sees why an entry
	 * surfaced rather than having to guess.
	 */
	public function column_flagged_terms( $item ): string {
		$terms = array_values( array_filter( explode( "\n", (string) ( $item->flagged_terms ?? '' ) ) ) );

		if ( [] === $terms ) {
			return '—';
		}

		$badges = array_map(
			static function ( string $term ): string {
				return '<span class="reci-flag-badge">' . esc_html( $term ) . '</span>';
			},
			$terms
		);

		return implode( ' ', $badges );
	}

	/**
	 * The status currently being filtered on, or '' for all.
	 */
	protected function current_status(): string {
		$status = isset( $_GET['journal_status'] ) ? sanitize_key( wp_unslash( $_GET['journal_status'] ) ) : '';

		return in_array( $status, reci_journal_moderatable_statuses(), true ) ? $status : '';
	}

	/**
	 * Status filter links, with a count each.
	 *
	 * Counts come from one grouped query rather than one per status.
	 */
	protected function get_views() {
		global $wpdb;

		$table = $wpdb->prefix . 'reci_journals';
		$rows  = $wpdb->get_results( sprintf(
			"SELECT status, COUNT(id) AS total FROM {$table} WHERE status IN ( %s ) GROUP BY status",
			implode( ', ', array_map( static fn( $st ) => "'" . esc_sql( $st ) . "'", reci_journal_moderatable_statuses() ) )
		) );

		$counts = [];
		$all    = 0;

		foreach ( $rows as $row ) {
			$counts[ (string) $row->status ] = (int) $row->total;
			$all                            += (int) $row->total;
		}

		$current = $this->current_status();
		$base    = admin_url( 'admin.php?page=reci-journals' );

		$views = [
			'all' => sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( $base ),
				'' === $current ? ' class="current"' : '',
				esc_html__( 'All', 'reci-media-hub' ),
				$all
			),
		];

		foreach ( reci_journal_moderatable_statuses() as $status ) {
			$views[ $status ] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( add_query_arg( 'journal_status', $status, $base ) ),
				$current === $status ? ' class="current"' : '',
				esc_html( reci_journal_status_label( $status ) ),
				$counts[ $status ] ?? 0
			);
		}

		return $views;
	}

	/**
	 * Bulk actions.
	 *
	 * The table has always rendered a checkbox column, so without these the
	 * screen offered a selection that could not be acted on.
	 */
	public function get_bulk_actions() {
		if ( ! current_user_can( 'reci_moderate_journals' ) ) {
			return [];
		}

		return [
			'reci-approve' => __( 'Approve', 'reci-media-hub' ),
			'reci-reject'  => __( 'Reject', 'reci-media-hub' ),
		];
	}

	/**
	 * Apply a bulk action to the selected entries.
	 *
	 * Only entries actually awaiting review are touched: the transition table
	 * rejects the rest, but filtering here keeps the reported count honest.
	 */
	public function process_bulk_action(): void {
		$action = $this->current_action();

		if ( ! in_array( $action, [ 'reci-approve', 'reci-reject' ], true ) ) {
			return;
		}

		if ( ! current_user_can( 'reci_moderate_journals' ) ) {
			wp_die( esc_html__( 'You are not allowed to moderate journal entries.', 'reci-media-hub' ) );
		}

		// WP_List_Table nonces bulk submissions as 'bulk-' . $plural.
		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		$ids = isset( $_REQUEST['journal'] ) ? array_map( 'absint', (array) wp_unslash( $_REQUEST['journal'] ) ) : [];
		$ids = array_values( array_filter( $ids ) );

		if ( [] === $ids ) {
			return;
		}

		$done = 0;

		foreach ( $ids as $journal_id ) {
			$ok = ( 'reci-approve' === $action )
				? reci_approve_journal( $journal_id )
				: reci_reject_journal( $journal_id );

			if ( ! $ok ) {
				continue;
			}

			$done++;

			// Keep the mirror comment in step, so both moderation surfaces
			// report the same state.
			$journal = reci_get_journal_row( $journal_id );

			if ( $journal && (int) $journal['comment_id'] ) {
				wp_set_comment_status(
					(int) $journal['comment_id'],
					( 'reci-approve' === $action ) ? 'approve' : 'trash'
				);
			}
		}

		wp_safe_redirect(
			add_query_arg(
				[
					'page'      => 'reci-journals',
					'moderated' => ( 'reci-approve' === $action ) ? 'approved' : 'rejected',
					'count'     => $done,
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	public function prepare_items() {
		global $wpdb;

		$this->process_bulk_action();

		$table_name = $wpdb->prefix . 'reci_journals';

		$per_page = 20;
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$orderby = ! empty( $_GET['orderby'] ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'created_at';
		$order   = ! empty( $_GET['order'] ) && strtolower( $_GET['order'] ) === 'asc' ? 'ASC' : 'DESC';
		
		// Map simple sortable names to columns
		$allowed_orderbys = [ 'created_at', 'user_id' ];
		if ( ! in_array( $orderby, $allowed_orderbys, true ) ) {
			$orderby = 'created_at';
		}

		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$status = $this->current_status();

		// Private entries never appear. They were not shared with anyone, and
		// this screen exists to moderate what was.
		$where = '' !== $status
			? $wpdb->prepare( 'WHERE status = %s', $status )
			: sprintf(
				"WHERE status IN ( %s )",
				implode( ', ', array_map( static fn( $st ) => "'" . esc_sql( $st ) . "'", reci_journal_moderatable_statuses() ) )
			);

		$total_items = (int) $wpdb->get_var( "SELECT COUNT(id) FROM $table_name $where" );

		$query = "SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT %d OFFSET %d";
		$this->items = $wpdb->get_results( $wpdb->prepare( $query, $per_page, $offset ) );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		] );
	}
}

function reci_media_hub_journals_admin_page() {
	$list_table = new Reci_Journals_List_Table();
	$list_table->prepare_items();
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Journals', 'reci-media-hub' ); ?></h1>
		<hr class="wp-header-end">
		<?php
		// views() is not called by display(); the screen has to render it.
		$list_table->views();
		?>
		<form method="get">
			<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
			<?php
			// Carry the active filter through sorting, pagination and bulk
			// submissions, which all post this form back.
			$reci_journal_status = isset( $_GET['journal_status'] ) ? sanitize_key( wp_unslash( $_GET['journal_status'] ) ) : '';
			if ( '' !== $reci_journal_status ) {
				printf( '<input type="hidden" name="journal_status" value="%s" />', esc_attr( $reci_journal_status ) );
			}
			$list_table->display();
			?>
		</form>
	</div>
	<?php
}

// Under Submissions: a journal entry is member-written material arriving for
// staff to read, which is the same job as the rest of that menu. It ran late so
// the Submissions parent exists by the time this attaches to it.
add_action( 'admin_menu', function() {
	add_submenu_page(
		'reci-submissions',
		__( 'Journals', 'reci-media-hub' ),
		__( 'Journals', 'reci-media-hub' ),
		'reci_moderate_journals',
		'reci-journals',
		'reci_media_hub_journals_admin_page'
	);
}, 20 );
