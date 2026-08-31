<?php
/**
 * Template Name: Become a Collaborator
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$status = reci_get_collaborator_status();

// A guest who just applied is not signed in yet, so their status still reads as
// `guest`. Show the review notice rather than re-rendering an empty form.
if ( 'approved' !== $status && reci_collaborator_application_just_submitted() ) {
	$status = 'pending';
}

get_header();
?>

<main class="layout-page bg-slate-100">
	<?php
	get_template_part(
		'template-parts/common/page-title-card',
		null,
		[
			'title'    => 'Become a Collaborator',
			'subtitle' => 'Join the RECI community of contributors and share research, reflections, documents, and media.',
		]
	);
	?>
	<section class="reci-container-full bg-slate-100">
		<div class="reci-container py-12 lg:py-14">
			<?php reci_render_collaborator_application_notices(); ?>

			<?php if ( 'pending' === $status ) : ?>
				<div class="max-w-4xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
					<h2 class="font-heading text-2xl font-bold text-zinc-900"><?php esc_html_e( 'Your Collaborator Application Is Under Review', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'Thank you for applying. Your member account is active, and our team is reviewing your collaborator onboarding form. Once approved, your application details will seed your public Collaborator profile and unlock contribution tools.', 'reci-media-hub' ); ?></p>
					<div class="mt-6 flex flex-wrap gap-3">
						<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn btn-primary btn-md"><?php esc_html_e( 'Go to Dashboard', 'reci-media-hub' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/community/' ) ); ?>" class="btn btn-outline-primary btn-md"><?php esc_html_e( 'Back to Community', 'reci-media-hub' ); ?></a>
					</div>
				</div>
			<?php elseif ( 'approved' === $status ) : ?>
				<div class="max-w-4xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
					<h2 class="font-heading text-2xl font-bold text-zinc-900"><?php esc_html_e( 'You Are Already a Collaborator', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'Your account is approved to contribute. Head to your dashboard to manage your feed, profile, and contribution tools.', 'reci-media-hub' ); ?></p>
					<div class="mt-6 flex flex-wrap gap-3">
						<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn btn-primary btn-md"><?php esc_html_e( 'Go to Dashboard', 'reci-media-hub' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>" class="btn btn-outline-primary btn-md"><?php esc_html_e( 'Submit Content', 'reci-media-hub' ); ?></a>
					</div>
				</div>
			<?php else : ?>
				<?php
				get_template_part(
					'template-parts/collaborator/application-form',
					null,
					[ 'context' => 'collaborator' ]
				);
				?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
