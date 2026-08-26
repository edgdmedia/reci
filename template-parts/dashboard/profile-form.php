<?php
/**
 * Dashboard profile form.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user_id = get_current_user_id();
$user            = wp_get_current_user();
$message         = '';

$first_name   = (string) get_user_meta( $current_user_id, 'first_name', true );
$last_name    = (string) get_user_meta( $current_user_id, 'last_name', true );
$display_name = (string) $user->display_name;
$email        = (string) $user->user_email;
$website      = (string) $user->user_url;
$bio          = (string) get_user_meta( $current_user_id, 'description', true );
$role_title   = (string) get_user_meta( $current_user_id, 'user_title', true );
$organization = (string) get_user_meta( $current_user_id, 'organization', true );

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['reci_profile_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['reci_profile_nonce'] ) ), 'reci_update_profile' ) ) {
	$first_name   = sanitize_text_field( wp_unslash( $_POST['reci_first_name'] ?? '' ) );
	$last_name    = sanitize_text_field( wp_unslash( $_POST['reci_last_name'] ?? '' ) );
	$display_name = sanitize_text_field( wp_unslash( $_POST['reci_display_name'] ?? '' ) );
	$website      = esc_url_raw( wp_unslash( $_POST['reci_website'] ?? '' ) );
	$bio          = sanitize_textarea_field( wp_unslash( $_POST['reci_bio'] ?? '' ) );
	$role_title   = sanitize_text_field( wp_unslash( $_POST['reci_role_title'] ?? '' ) );
	$organization = sanitize_text_field( wp_unslash( $_POST['reci_organization'] ?? '' ) );

	wp_update_user(
		[
			'ID'           => $current_user_id,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
			'display_name' => $display_name !== '' ? $display_name : trim( $first_name . ' ' . $last_name ),
			'user_url'     => $website,
		]
	);

	update_user_meta( $current_user_id, 'description', $bio );
	update_user_meta( $current_user_id, 'user_title', $role_title );
	update_user_meta( $current_user_id, 'organization', $organization );

	$message = __( 'Profile updated.', 'reci-media-hub' );
	$user    = wp_get_current_user();
	$email   = (string) $user->user_email;
}
?>

<?php if ( $message ) : ?>
<div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"><?php echo esc_html( $message ); ?></div>
<?php endif; ?>

<form method="post" class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
	<?php wp_nonce_field( 'reci_update_profile', 'reci_profile_nonce' ); ?>

	<div class="grid gap-5 md:grid-cols-2">
		<div>
			<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-first-name"><?php esc_html_e( 'First Name', 'reci-media-hub' ); ?></label>
			<input id="reci-first-name" name="reci_first_name" type="text" value="<?php echo esc_attr( $first_name ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
		</div>
		<div>
			<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-last-name"><?php esc_html_e( 'Last Name', 'reci-media-hub' ); ?></label>
			<input id="reci-last-name" name="reci_last_name" type="text" value="<?php echo esc_attr( $last_name ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
		</div>
	</div>

	<div class="grid gap-5 md:grid-cols-2">
		<div>
			<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-display-name"><?php esc_html_e( 'Display Name', 'reci-media-hub' ); ?></label>
			<input id="reci-display-name" name="reci_display_name" type="text" value="<?php echo esc_attr( $display_name ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
		</div>
		<div>
			<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-email"><?php esc_html_e( 'Email', 'reci-media-hub' ); ?></label>
			<input id="reci-email" type="email" value="<?php echo esc_attr( $email ); ?>" class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-zinc-500" disabled />
		</div>
	</div>

	<div class="grid gap-5 md:grid-cols-2">
		<div>
			<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-role-title"><?php esc_html_e( 'Role / Title', 'reci-media-hub' ); ?></label>
			<input id="reci-role-title" name="reci_role_title" type="text" value="<?php echo esc_attr( $role_title ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
		</div>
		<div>
			<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-organization"><?php esc_html_e( 'Organization', 'reci-media-hub' ); ?></label>
			<input id="reci-organization" name="reci_organization" type="text" value="<?php echo esc_attr( $organization ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
		</div>
	</div>

	<div>
		<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-website"><?php esc_html_e( 'Website', 'reci-media-hub' ); ?></label>
		<input id="reci-website" name="reci_website" type="url" value="<?php echo esc_attr( $website ); ?>" class="w-full rounded-lg border border-zinc-300 px-4 py-3" />
	</div>

	<div>
		<label class="mb-2 block text-sm font-medium text-zinc-800" for="reci-bio"><?php esc_html_e( 'Bio', 'reci-media-hub' ); ?></label>
		<textarea id="reci-bio" name="reci_bio" rows="6" class="w-full rounded-lg border border-zinc-300 px-4 py-3"><?php echo esc_textarea( $bio ); ?></textarea>
	</div>

	<div>
		<button type="submit" class="btn btn-primary btn-md"><?php esc_html_e( 'Save Profile', 'reci-media-hub' ); ?></button>
	</div>
</form>
