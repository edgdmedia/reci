<?php
/**
 * Shared collaborator application form.
 *
 * Rendered by /become-a-collaborator/ and by the canonical /submit/ flow so both
 * surfaces collect exactly the same contributor identity fields.
 *
 * Args:
 * - context (string) 'collaborator' | 'submit' — controls where the handler redirects back to.
 * - heading (string) Card heading.
 * - intro   (string) Supporting copy under the heading.
 * - submit_label (string) Button label.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'context'      => 'collaborator',
		'heading'      => '',
		'intro'        => '',
		'submit_label' => '',
	]
);

$is_guest       = ! is_user_logged_in();
$current_values = $is_guest ? [] : reci_get_user_collaborator_profile_data( get_current_user_id() );

$heading = $args['heading'] !== ''
	? $args['heading']
	: ( $is_guest
		? __( 'Create Your Member Account and Apply as a Collaborator', 'reci-media-hub' )
		: __( 'Apply to Become a Collaborator', 'reci-media-hub' ) );

$intro = $args['intro'] !== ''
	? $args['intro']
	: ( $is_guest
		? __( 'This form creates your RECI member account and submits your collaborator onboarding application in one step.', 'reci-media-hub' )
		: __( 'This form serves as both your collaborator application and the starting point for your public Collaborator profile. Please provide complete professional details.', 'reci-media-hub' ) );

$submit_label = $args['submit_label'] !== ''
	? $args['submit_label']
	: ( $is_guest
		? __( 'Create Account and Apply', 'reci-media-hub' )
		: __( 'Submit Collaborator Application', 'reci-media-hub' ) );

$sign_in_url = function_exists( 'reci_get_auth_page_url' )
	? ( reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url() )
	: wp_login_url();
?>

<div class="max-w-4xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
	<h2 class="font-heading text-2xl font-bold text-zinc-900"><?php echo esc_html( $heading ); ?></h2>
	<p class="mt-3 text-base leading-7 text-zinc-600"><?php echo esc_html( $intro ); ?></p>

	<?php if ( $is_guest ) : ?>
		<p class="mt-3 text-sm leading-6 text-zinc-500">
			<?php
			printf(
				wp_kses_post( __( 'Already have an account? <a href="%s" class="text-amber-700 underline">Sign in</a> first to continue where you left off.', 'reci-media-hub' ) ),
				esc_url( $sign_in_url )
			);
			?>
		</p>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="mt-8 space-y-8" enctype="multipart/form-data">
		<input type="hidden" name="action" value="reci_collaborator_application" />
		<input type="hidden" name="reci_application_context" value="<?php echo esc_attr( $args['context'] ); ?>" />
		<?php wp_nonce_field( 'reci_collaborator_application', 'reci_collaborator_nonce' ); ?>

		<fieldset class="space-y-5">
			<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e( 'Your details', 'reci-media-hub' ); ?></legend>
			<?php reci_render_collaborator_fields( reci_collaborator_profile_field_definitions(), $current_values ); ?>
		</fieldset>

		<?php if ( $is_guest ) : ?>
			<fieldset class="space-y-5">
				<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e( 'Create your password', 'reci-media-hub' ); ?></legend>
				<?php reci_render_collaborator_fields( reci_collaborator_account_field_definitions() ); ?>
			</fieldset>
		<?php endif; ?>

		<fieldset class="space-y-5">
			<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e( 'Your application', 'reci-media-hub' ); ?></legend>
			<?php reci_render_collaborator_fields( reci_collaborator_application_only_field_definitions() ); ?>
		</fieldset>

		<button type="submit" class="btn btn-primary btn-md"><?php echo esc_html( $submit_label ); ?></button>
	</form>
</div>
