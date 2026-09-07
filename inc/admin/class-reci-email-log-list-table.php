<?php
/**
 * Email log, as a standard WordPress list table.
 *
 * Was a fixed 200-row dump with no paging, sorting or filters — unusable once
 * the log has any history in it.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Reci_Email_Log_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct(
			[
				'singular' => 'email',
				'plural'   => 'emails',
				'ajax'     => false,
			]
		);
	}

	public function get_columns() {
		return [
			'created_at' => __( 'When', 'reci-media-hub' ),
			'recipient'  => __( 'To', 'reci-media-hub' ),
			'subject'    => __( 'Subject', 'reci-media-hub' ),
			'transport'  => __( 'Via', 'reci-media-hub' ),
			'status'     => __( 'Result', 'reci-media-hub' ),
		];
	}

	public function get_sortable_columns() {
		return [
			'created_at' => [ 'created_at', true ],
			'recipient'  => [ 'recipient', false ],
			'status'     => [ 'status', false ],
		];
	}

	private function current_status(): string {
		$status = isset( $_GET['log_status'] ) ? sanitize_key( wp_unslash( $_GET['log_status'] ) ) : 'any';

		return in_array( $status, [ 'sent', 'failed', 'any' ], true ) ? $status : 'any';
	}

	protected function get_views() {
		global $wpdb;

		$table   = reci_email_log_table();
		$base    = admin_url( 'admin.php?page=reci-email-log' );
		$current = $this->current_status();

		$counts = [
			'any'    => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ), // phpcs:ignore WordPress.DB
			'sent'   => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'sent'" ), // phpcs:ignore WordPress.DB
			'failed' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status <> 'sent'" ), // phpcs:ignore WordPress.DB
		];

		$views = [];

		foreach ( [ 'any' => __( 'All', 'reci-media-hub' ), 'sent' => __( 'Sent', 'reci-media-hub' ), 'failed' => __( 'Failed', 'reci-media-hub' ) ] as $key => $label ) {
			$views[ $key ] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				esc_url( add_query_arg( 'log_status', $key, $base ) ),
				$current === $key ? ' class="current" aria-current="page"' : '',
				esc_html( $label ),
				$counts[ $key ]
			);
		}

		return $views;
	}

	public function prepare_items() {
		global $wpdb;

		$table    = reci_email_log_table();
		$per_page = 30;
		$paged    = max( 1, $this->get_pagenum() );
		$offset   = ( $paged - 1 ) * $per_page;

		$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'created_at';
		$orderby = in_array( $orderby, [ 'created_at', 'recipient', 'status' ], true ) ? $orderby : 'created_at';
		$order   = isset( $_GET['order'] ) && 'asc' === strtolower( (string) wp_unslash( $_GET['order'] ) ) ? 'ASC' : 'DESC';

		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$status = $this->current_status();

		// Built with placeholders; only the column and direction are interpolated,
		// and both are constrained to a fixed list above.
		$where  = [ '1=1' ];
		$params = [];

		if ( 'sent' === $status ) {
			$where[] = 'status = %s';
			$params[] = 'sent';
		} elseif ( 'failed' === $status ) {
			$where[] = 'status <> %s';
			$params[] = 'sent';
		}

		if ( '' !== $search ) {
			$where[]  = '(recipient LIKE %s OR subject LIKE %s)';
			$like     = '%' . $wpdb->esc_like( $search ) . '%';
			$params[] = $like;
			$params[] = $like;
		}

		$clause = implode( ' AND ', $where );

		$total_sql = "SELECT COUNT(*) FROM {$table} WHERE {$clause}";
		$total     = (int) ( $params ? $wpdb->get_var( $wpdb->prepare( $total_sql, $params ) ) : $wpdb->get_var( $total_sql ) ); // phpcs:ignore WordPress.DB

		$rows_sql       = "SELECT * FROM {$table} WHERE {$clause} ORDER BY {$orderby} {$order}, id DESC LIMIT %d OFFSET %d";
		$rows_params    = array_merge( $params, [ $per_page, $offset ] );
		$this->items    = (array) $wpdb->get_results( $wpdb->prepare( $rows_sql, $rows_params ) ); // phpcs:ignore WordPress.DB

		$this->set_pagination_args(
			[
				'total_items' => $total,
				'per_page'    => $per_page,
				'total_pages' => (int) ceil( $total / $per_page ),
			]
		);

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'created_at':
				return esc_html( mysql2date( 'j M Y, H:i', $item->created_at ) );

			case 'recipient':
				return esc_html( $item->recipient );

			case 'subject':
				return esc_html( $item->subject );

			case 'transport':
				return esc_html( $item->transport );

			case 'status':
				$sent = 'sent' === $item->status;

				return sprintf(
					'<span style="color:%s;font-weight:600;">%s</span>%s',
					$sent ? '#1f7a5a' : '#9d2f45',
					esc_html( $sent ? __( 'Sent', 'reci-media-hub' ) : __( 'Failed', 'reci-media-hub' ) ),
					'' !== (string) $item->error
						? '<br /><span style="color:#6A6D70;font-size:12px;">' . esc_html( $item->error ) . '</span>'
						: ''
				);
		}

		return '';
	}

	public function no_items() {
		esc_html_e( 'Nothing sent yet.', 'reci-media-hub' );
	}
}
