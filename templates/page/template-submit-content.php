<?php
/**
 * Template Name: Submit Content
 *
 * The canonical RECI contribution entry point. Renders one continuous flow whose
 * shape depends on where the visitor is: guest, member without collaborator
 * access, pending collaborator, or approved collaborator. Backend persistence
 * stays staged — account creation, collaborator application, and content
 * submission are separate saves.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$submit_state = reci_get_submit_experience_state();

// A guest who just applied is not signed in yet, so their state still reads as
// `guest`. Move them straight to the review stage rather than re-rendering the form.
if ( 'approved_collaborator' !== $submit_state && reci_collaborator_application_just_submitted() ) {
	$submit_state = 'pending_collaborator';
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

			<?php reci_render_collaborator_application_notices(); ?>

			<?php if ( 'approved_collaborator' !== $submit_state ) : ?>
				<?php reci_render_submit_flow_progress( $submit_state ); ?>
			<?php endif; ?>

			<?php if ( 'approved_collaborator' === $submit_state ) : ?>

				<div id="reci-submission-root"></div>

			<?php elseif ( 'pending_collaborator' === $submit_state ) : ?>

				<div class="max-w-4xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
					<span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-800"><?php esc_html_e( 'In Review', 'reci-media-hub' ); ?></span>
					<h2 class="mt-4 font-heading text-2xl font-bold text-zinc-900"><?php esc_html_e( 'Your Collaborator Application Is Under Review', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'We have saved everything you submitted. Content submission unlocks on this same page as soon as our team approves your application — you will get a notification when it does.', 'reci-media-hub' ); ?></p>
					<div class="mt-6 flex flex-wrap gap-3">
						<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn btn-primary btn-md"><?php esc_html_e( 'Go to Dashboard', 'reci-media-hub' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/dashboard/profile/' ) ); ?>" class="btn btn-outline-primary btn-md"><?php esc_html_e( 'Update Your Profile', 'reci-media-hub' ); ?></a>
					</div>
				</div>

			<?php else : ?>

				<?php
				$is_guest = 'guest' === $submit_state;

				get_template_part(
					'template-parts/collaborator/application-form',
					null,
					[
						'context'      => 'submit',
						'heading'      => $is_guest
							? __( 'Start Your Contribution', 'reci-media-hub' )
							: __( 'Complete Your Contributor Profile', 'reci-media-hub' ),
						'intro'        => $is_guest
							? __( 'Contributing to RECI starts here. Create your member account and complete your contributor profile — we save each step as you go, then open the content submission tools once your application is approved.', 'reci-media-hub' )
							: __( 'You are signed in as a member. Complete your contributor profile below to apply for submission access. We save your details as you go, and content submission opens on this page once you are approved.', 'reci-media-hub' ),
						'submit_label' => $is_guest
							? __( 'Create Account and Continue', 'reci-media-hub' )
							: __( 'Submit for Review', 'reci-media-hub' ),
					]
				);
				?>

			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
