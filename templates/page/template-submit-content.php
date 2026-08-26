<?php
/**
 * Template Name: Submit Content
 *
 * Mounts the RECMH multi-step React submission experience.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="layout-page">
	<?php
	get_template_part(
		'template-parts/common/page-title-card',
		null,
		[
			'title'    => 'Submit Content',
			'subtitle' => 'Share evidence-based, process-oriented work that advances racial equity consciousness.',
		]
	);
	?>
	<section class="reci-container-full bg-slate-100">
		<div class="reci-container py-12 lg:py-14">
			<?php if ( isset( $_GET['submit_error'] ) && 'collaborator_required' === sanitize_key( wp_unslash( $_GET['submit_error'] ) ) ) : ?>
				<div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
					<?php esc_html_e( 'You need an approved Collaborator account to submit content.', 'reci-media-hub' ); ?>
				</div>
			<?php endif; ?>
			<?php if ( function_exists( 'reci_user_is_collaborator' ) && reci_user_is_collaborator() ) : ?>
				<div id="reci-submission-root"></div>
			<?php else : ?>
				<?php reci_render_submit_gate( 'public' ); ?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
