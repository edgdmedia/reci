<?php
/**
 * Dashboard profile form.
 *
 * Edits the canonical contributor profile fields shared with collaborator
 * onboarding and the /submit/ flow. Application-only fields (uploads, membership
 * objective) deliberately stay out of this form.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user_id = get_current_user_id();
$user            = wp_get_current_user();
$message         = '';

$fields = reci_collaborator_profile_field_definitions();

// Email is managed by the account, not this form.
unset( $fields['user_email'] );

// Profile editing allows partial completion — the application form is where the
// required-field contract is enforced.
foreach ( $fields as $key => $field ) {
	$fields[ $key ]['required']      = false;
	$fields[ $key ]['optional_hint'] = false;
}

$values = reci_get_user_collaborator_profile_data( $current_user_id );
$display_name = (string) $user->display_name;

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['reci_profile_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['reci_profile_nonce'] ) ), 'reci_update_profile' ) ) {
	$sanitizers = [
		'textarea' => 'sanitize_textarea_field',
		'url'      => 'esc_url_raw',
	];

	$submitted = [];
	foreach ( $fields as $key => $field ) {
		$raw               = wp_unslash( $_POST[ $key ] ?? '' );
		$sanitizer         = $sanitizers[ $field['type'] ?? 'text' ] ?? 'sanitize_text_field';
		$submitted[ $key ] = call_user_func( $sanitizer, (string) $raw );
	}

	$display_name = sanitize_text_field( wp_unslash( $_POST['reci_display_name'] ?? '' ) );
	if ( '' !== $display_name ) {
		$submitted['display_name'] = $display_name;
	}

	reci_save_user_collaborator_profile_data( $current_user_id, $submitted );

	$message      = __( 'Profile updated.', 'reci-media-hub' );
	$user         = wp_get_current_user();
	$display_name = (string) $user->display_name;
	$values       = reci_get_user_collaborator_profile_data( $current_user_id );
}

$email = (string) $user->user_email;
?>

<?php if ( $message ) : ?>
<div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-800"><?php echo esc_html( $message ); ?></div>
<?php endif; ?>

<form method="post" class="max-w-4xl space-y-8 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
	<?php wp_nonce_field( 'reci_update_profile', 'reci_profile_nonce' ); ?>

	<fieldset class="space-y-5">
		<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e( 'Account', 'reci-media-hub' ); ?></legend>
		<div class="grid gap-5 sm:grid-cols-2">
			<div>
				<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-display-name"><?php esc_html_e( 'Display Name', 'reci-media-hub' ); ?></label>
				<input id="reci-display-name" name="reci_display_name" type="text" value="<?php echo esc_attr( $display_name ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" />
			</div>
			<div>
				<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-email"><?php esc_html_e( 'Email', 'reci-media-hub' ); ?></label>
				<input id="reci-email" type="email" value="<?php echo esc_attr( $email ); ?>" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-zinc-500" disabled />
			</div>
		</div>
	</fieldset>

	<fieldset class="space-y-5">
		<legend class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e( 'Contributor profile', 'reci-media-hub' ); ?></legend>
		<?php reci_render_collaborator_fields( $fields, $values ); ?>
	</fieldset>

	<div>
		<button type="submit" class="btn btn-primary btn-md"><?php esc_html_e( 'Save Profile', 'reci-media-hub' ); ?></button>
	</div>
</form>
