<?php
/**
 * Admin list table for Assessment Submissions.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Reci_Assessments_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( [
			'singular' => 'submission',
			'plural'   => 'submissions',
			'ajax'     => false,
		] );
	}

	public function get_columns() {
		return [
			'cb'            => '<input type="checkbox" />',
			'user_id'       => __( 'Respondent', 'reci-media-hub' ),
			'assessment_id' => __( 'Assessment', 'reci-media-hub' ),
			'score'         => __( 'Score', 'reci-media-hub' ),
			'percentage'    => __( 'Percentage', 'reci-media-hub' ),
			'created_at'    => __( 'Date', 'reci-media-hub' ),
		];
	}

	public function get_sortable_columns() {
		return [
			'created_at' => [ 'created_at', true ],
			'score'      => [ 'score', false ],
			'percentage' => [ 'percentage', false ],
		];
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'score':
				return sprintf( '%d / %d', $item->score, $item->max_score );
			case 'percentage':
				return $item->percentage . '%';
			case 'created_at':
				return esc_html( wp_date( 'Y/m/d g:i a', strtotime( $item->created_at ) ) );
			default:
				return print_r( $item, true );
		}
	}

	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="submission[]" value="%s" />',
			$item->id
		);
	}

	protected function column_user_id( $item ) {
		if ( $item->user_id > 0 ) {
			$user = get_userdata( $item->user_id );
			return $user ? esc_html( $user->display_name ) : esc_html( $item->respondent );
		}
		return esc_html( $item->respondent );
	}

	protected function column_assessment_id( $item ) {
		$title = get_the_title( $item->assessment_id );
		$edit_link = get_edit_post_link( $item->assessment_id );
		if ( $edit_link ) {
			return sprintf( '<a href="%s">%s</a>', esc_url( $edit_link ), esc_html( $title ) );
		}
		return esc_html( $title );
	}

	public function prepare_items() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'reci_assessment_submissions';

		$per_page = 20;
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$orderby = ! empty( $_GET['orderby'] ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'created_at';
		$order   = ! empty( $_GET['order'] ) && strtolower( $_GET['order'] ) === 'asc' ? 'ASC' : 'DESC';
		
		$allowed_orderbys = [ 'created_at', 'score', 'percentage' ];
		if ( ! in_array( $orderby, $allowed_orderbys, true ) ) {
			$orderby = 'created_at';
		}

		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" );
		
		$query = "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d";
		$this->items = $wpdb->get_results( $wpdb->prepare( $query, $per_page, $offset ) );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		] );
	}
}

function reci_media_hub_assessments_admin_page() {
	$list_table = new Reci_Assessments_List_Table();
	$list_table->prepare_items();
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Quiz Submissions', 'reci-media-hub' ); ?></h1>
		<hr class="wp-header-end">
		<form method="get">
			<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
			<?php $list_table->display(); ?>
		</form>
	</div>
	<?php
}

add_action( 'admin_menu', function() {
	add_submenu_page(
		'edit.php?post_type=reci_assessment',
		__( 'Submissions', 'reci-media-hub' ),
		__( 'Submissions', 'reci-media-hub' ),
		'edit_posts',
		'reci-assessment-submissions',
		'reci_media_hub_assessments_admin_page'
	);
} );
