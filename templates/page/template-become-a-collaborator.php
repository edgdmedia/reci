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
			<?php if ( isset( $_GET['application_success'] ) ) : ?>
				<div class="mb-8 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800">
					<?php
					$success = sanitize_key( wp_unslash( $_GET['application_success'] ) );
					$success_messages = [
						'already_approved'    => __( 'Your collaborator access is already active.', 'reci-media-hub' ),
						'pending'             => __( 'Your collaborator application is under review.', 'reci-media-hub' ),
						'pending_with_account' => __( 'Your member account has been created and your collaborator application is under review. Please verify your email address if prompted.', 'reci-media-hub' ),
					];
					echo esc_html( $success_messages[ $success ] ?? __( 'Your collaborator application is under review.', 'reci-media-hub' ) );
					?>
				</div>
			<?php endif; ?>

			<?php if ( isset( $_GET['application_error'] ) ) : ?>
				<div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
					<?php
					$error = sanitize_key( wp_unslash( $_GET['application_error'] ) );
					$messages = [
						'invalid_nonce'  => __( 'Security check failed. Please try again.', 'reci-media-hub' ),
						'missing_fields' => __( 'Please complete the required fields.', 'reci-media-hub' ),
						'missing_account_fields' => __( 'Please complete the required account fields.', 'reci-media-hub' ),
						'password_mismatch' => __( 'Passwords do not match.', 'reci-media-hub' ),
						'password_too_short' => __( 'Password must be at least 8 characters.', 'reci-media-hub' ),
						'registration_disabled' => __( 'Registration is currently disabled.', 'reci-media-hub' ),
						'existing_user_email' => __( 'An account with this email address already exists.', 'reci-media-hub' ),
						'existing_user_login' => __( 'That username already exists.', 'reci-media-hub' ),
						'save_failed'    => __( 'We could not save your application. Please try again.', 'reci-media-hub' ),
					];
					echo esc_html( $messages[ $error ] ?? __( 'Something went wrong. Please try again.', 'reci-media-hub' ) );
					?>
				</div>
			<?php endif; ?>

			<?php if ( 'guest' === $status ) : ?>
				<div class="max-w-4xl rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-zinc-900"><?php esc_html_e( 'Create Your Member Account and Apply as a Collaborator', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'This form creates your RECI member account and submits your collaborator onboarding application in one step.', 'reci-media-hub' ); ?></p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-8 space-y-5" enctype="multipart/form-data">
						<input type="hidden" name="action" value="reci_collaborator_application" />
						<?php wp_nonce_field( 'reci_collaborator_application', 'reci_collaborator_nonce' ); ?>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-firstname"><?php esc_html_e( 'First Name', 'reci-media-hub' ); ?></label>
								<input id="reci-firstname" name="reci_firstname" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-lastname"><?php esc_html_e( 'Last Name', 'reci-media-hub' ); ?></label>
								<input id="reci-lastname" name="reci_lastname" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-email"><?php esc_html_e( 'Email', 'reci-media-hub' ); ?></label>
								<input id="reci-email" name="user_email" type="email" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-affiliated-pitt"><?php esc_html_e( 'Affiliated with Pitt', 'reci-media-hub' ); ?></label>
								<select id="reci-affiliated-pitt" name="reci_affiliated_with_pitt" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required>
									<option value=""><?php esc_html_e( 'Select one', 'reci-media-hub' ); ?></option>
									<option value="Yes"><?php esc_html_e( 'Yes', 'reci-media-hub' ); ?></option>
									<option value="No"><?php esc_html_e( 'No', 'reci-media-hub' ); ?></option>
								</select>
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-password"><?php esc_html_e( 'Password', 'reci-media-hub' ); ?></label>
								<input id="reci-password" name="user_pass" type="password" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-password-confirm"><?php esc_html_e( 'Confirm Password', 'reci-media-hub' ); ?></label>
								<input id="reci-password-confirm" name="reci_pass_confirm" type="password" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-pitt-affiliation"><?php esc_html_e( 'Pitt Affiliation', 'reci-media-hub' ); ?></label>
								<input id="reci-pitt-affiliation" name="reci_pitt_affiliation" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-organization"><?php esc_html_e( 'Affiliation / Organization', 'reci-media-hub' ); ?></label>
								<input id="reci-organization" name="submission_organization" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-department"><?php esc_html_e( 'Department (School / Organization)', 'reci-media-hub' ); ?></label>
								<input id="reci-department" name="reci_department" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-role"><?php esc_html_e( 'Role / Title', 'reci-media-hub' ); ?></label>
								<input id="reci-role" name="submission_role" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-profile-picture"><?php esc_html_e( 'Profile Picture (Professional headshot)', 'reci-media-hub' ); ?></label>
							<input id="reci-profile-picture" name="reci_profile_picture" type="file" accept="image/*" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-cv-upload"><?php esc_html_e( 'Attach CV', 'reci-media-hub' ); ?></label>
							<input id="reci-cv-upload" name="reci_cv_upload" type="file" accept=".pdf,.doc,.docx" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-bio"><?php esc_html_e( 'Personal Bio (150 words or less)', 'reci-media-hub' ); ?></label>
							<textarea id="reci-bio" name="submission_bio" rows="6" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required></textarea>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-website"><?php esc_html_e( 'Professional Website', 'reci-media-hub' ); ?></label>
								<input id="reci-website" name="submission_website" type="url" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-social-handles"><?php esc_html_e( 'Social Media Handles', 'reci-media-hub' ); ?></label>
								<input id="reci-social-handles" name="reci_social_handles" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" placeholder="LinkedIn, X, Instagram, etc." />
							</div>
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-membership-objective"><?php esc_html_e( 'Main Objective for Membership', 'reci-media-hub' ); ?></label>
							<textarea id="reci-membership-objective" name="reci_membership_objective" rows="4" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required></textarea>
						</div>
						<button type="submit" class="btn btn-primary btn-md"><?php esc_html_e( 'Create Account and Apply', 'reci-media-hub' ); ?></button>
					</form>
			<?php elseif ( 'pending' === $status ) : ?>
				<div class="max-w-4xl rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-zinc-900"><?php esc_html_e( 'Your Collaborator Application Is Under Review', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'Thank you for applying. Your member account is active, and our team is reviewing your collaborator onboarding form. Once approved, your application details will seed your public Collaborator profile and unlock contribution tools.', 'reci-media-hub' ); ?></p>
					<div class="mt-6 flex flex-wrap gap-3">
						<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn btn-primary btn-md"><?php esc_html_e( 'Go to Dashboard', 'reci-media-hub' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/community/' ) ); ?>" class="btn btn-outline-primary btn-md"><?php esc_html_e( 'Back to Community', 'reci-media-hub' ); ?></a>
					</div>
				</div>
			<?php elseif ( 'approved' === $status ) : ?>
				<div class="max-w-4xl rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-zinc-900"><?php esc_html_e( 'You Are Already a Collaborator', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'Your account is approved to contribute. Head to the submit page whenever you are ready to share content.', 'reci-media-hub' ); ?></p>
					<div class="mt-6">
						<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>" class="btn btn-primary btn-md"><?php esc_html_e( 'Go to Submit', 'reci-media-hub' ); ?></a>
					</div>
				</div>
			<?php else : ?>
				<div class="max-w-4xl rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-zinc-900"><?php esc_html_e( 'Apply to Become a Collaborator', 'reci-media-hub' ); ?></h2>
					<p class="mt-3 text-base leading-7 text-zinc-600"><?php esc_html_e( 'This form serves as both your collaborator application and the starting point for your public Collaborator profile. Please provide complete professional details.', 'reci-media-hub' ); ?></p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-8 space-y-5" enctype="multipart/form-data">
						<input type="hidden" name="action" value="reci_collaborator_application" />
						<?php wp_nonce_field( 'reci_collaborator_application', 'reci_collaborator_nonce' ); ?>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-firstname"><?php esc_html_e( 'First Name', 'reci-media-hub' ); ?></label>
								<input id="reci-firstname" name="reci_firstname" type="text" value="<?php echo esc_attr( wp_get_current_user()->first_name ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-lastname"><?php esc_html_e( 'Last Name', 'reci-media-hub' ); ?></label>
								<input id="reci-lastname" name="reci_lastname" type="text" value="<?php echo esc_attr( wp_get_current_user()->last_name ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-email"><?php esc_html_e( 'Email', 'reci-media-hub' ); ?></label>
								<input id="reci-email" name="user_email" type="email" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-affiliated-pitt"><?php esc_html_e( 'Affiliated with Pitt', 'reci-media-hub' ); ?></label>
								<select id="reci-affiliated-pitt" name="reci_affiliated_with_pitt" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required>
									<option value=""><?php esc_html_e( 'Select one', 'reci-media-hub' ); ?></option>
									<option value="Yes"><?php esc_html_e( 'Yes', 'reci-media-hub' ); ?></option>
									<option value="No"><?php esc_html_e( 'No', 'reci-media-hub' ); ?></option>
								</select>
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-pitt-affiliation"><?php esc_html_e( 'Pitt Affiliation', 'reci-media-hub' ); ?></label>
								<input id="reci-pitt-affiliation" name="reci_pitt_affiliation" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-organization"><?php esc_html_e( 'Affiliation / Organization', 'reci-media-hub' ); ?></label>
								<input id="reci-organization" name="submission_organization" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-department"><?php esc_html_e( 'Department (School / Organization)', 'reci-media-hub' ); ?></label>
								<input id="reci-department" name="reci_department" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-role"><?php esc_html_e( 'Role / Title', 'reci-media-hub' ); ?></label>
								<input id="reci-role" name="submission_role" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
							</div>
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-profile-picture"><?php esc_html_e( 'Profile Picture (Professional headshot)', 'reci-media-hub' ); ?></label>
							<input id="reci-profile-picture" name="reci_profile_picture" type="file" accept="image/*" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required />
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-cv-upload"><?php esc_html_e( 'Attach CV', 'reci-media-hub' ); ?></label>
							<input id="reci-cv-upload" name="reci_cv_upload" type="file" accept=".pdf,.doc,.docx" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-bio"><?php esc_html_e( 'Personal Bio (150 words or less)', 'reci-media-hub' ); ?></label>
							<textarea id="reci-bio" name="submission_bio" rows="6" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required></textarea>
						</div>
						<div class="grid gap-5 sm:grid-cols-2">
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-website"><?php esc_html_e( 'Professional Website', 'reci-media-hub' ); ?></label>
								<input id="reci-website" name="submission_website" type="url" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
							</div>
							<div>
								<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-social-handles"><?php esc_html_e( 'Social Media Handles', 'reci-media-hub' ); ?></label>
								<input id="reci-social-handles" name="reci_social_handles" type="text" class="w-full rounded-lg border border-zinc-300 px-4 py-3" placeholder="LinkedIn, X, Instagram, etc." />
							</div>
						</div>
						<div>
							<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-membership-objective"><?php esc_html_e( 'Main Objective for Membership', 'reci-media-hub' ); ?></label>
							<textarea id="reci-membership-objective" name="reci_membership_objective" rows="4" class="w-full rounded-lg border border-zinc-300 px-4 py-3" required></textarea>
						</div>
						<button type="submit" class="btn btn-primary btn-md"><?php esc_html_e( 'Submit Collaborator Application', 'reci-media-hub' ); ?></button>
					</form>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
