<?php
/**
 * Template Name: Dashboard — Edit Content
 *
 * Front-end editor for a collaborator's own submission. Collaborators hold no
 * `edit_posts` capability and never reach wp-admin, so this is the only place
 * they can revise their work.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = (int) get_query_var( 'dashboard_post' );

if ( ! reci_user_can_edit_submission( $post_id ) ) {
	wp_safe_redirect( add_query_arg( 'edit_error', 'not_allowed', home_url( '/dashboard/my-content/' ) ) );
	exit;
}

$edited_post = get_post( $post_id );

get_header( 'dashboard' );
?>
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>

		<div class="flex-1 p-6 lg:p-10">
			<?php
			get_template_part(
				'template-parts/dashboard/page-header',
				null,
				[
					'title'    => 'Edit Submission',
					'subtitle' => get_the_title( $edited_post ),
					'action'   => '<a href="' . esc_url( home_url( '/dashboard/my-content/' ) ) . '" class="text-sm font-semibold text-amber-700 hover:text-amber-800">&larr; Back to My Content</a>',
				]
			);

			get_template_part( 'template-parts/dashboard/edit-content-form', null, [ 'post' => $edited_post ] );
			?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
