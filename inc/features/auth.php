<?php

/**
 * Custom authentication system.
 *
 * - Redirects wp-login.php display (GET) to custom pages.
 * - Handles custom registration via admin-post.php.
 * - Redirects non-admins away from wp-admin.
 * - Hides the admin bar for non-admins.
 * - Redirects already-logged-in users away from auth pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Return the page ID for a given auth-page slug.
 * Checks the option stored at activation; falls back to get_page_by_path().
 */
function reci_get_auth_page_id( string $slug ): int {
	$pages = (array) get_option( 'reci_pages', [] );
	if ( ! empty( $pages[ $slug ] ) ) {
		return (int) $pages[ $slug ];
	}
	$page = get_page_by_path( $slug );
	return $page ? (int) $page->ID : 0;
}

/**
 * Return the permalink for a given auth-page slug, or empty string.
 */
function reci_get_auth_page_url( string $slug ): string {
	$id = reci_get_auth_page_id( $slug );
	return $id ? ( get_permalink( $id ) ?: '' ) : '';
}

// ── URL filters ───────────────────────────────────────────────────────────────

/**
 * wp_login_url() → custom sign-in page.
 */
add_filter( 'login_url', function ( string $url, string $redirect, bool $force_reauth ): string {
	$custom = reci_get_auth_page_url( 'sign-in' );
	if ( ! $custom ) {
		return $url;
	}
	if ( $redirect ) {
		$custom = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $custom );
	}
	return $custom;
}, 10, 3 );

/**
 * wp_registration_url() → custom sign-up page.
 */
add_filter( 'register_url', function ( string $url ): string {
	$custom = reci_get_auth_page_url( 'sign-up' );
	return $custom ?: $url;
} );

add_filter( 'lostpassword_url', function ( string $url, string $redirect ): string {
	$custom = reci_get_auth_page_url( 'forgot-password' );
	if ( ! $custom ) {
		return $url;
	}
	if ( $redirect ) {
		$custom = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $custom );
	}
	return $custom;
}, 10, 2 );

/**
 * Redirect after successful password reset request → back to forgot-password with ?reset=sent
 */
add_filter( 'lostpassword_redirect', function ( string $url ): string {
	$custom = reci_get_auth_page_url( 'forgot-password' );
	if ( $custom ) {
		return add_query_arg( 'reset', 'sent', $custom );
	}
	return $url;
} );

// ── Redirect wp-login.php display requests ────────────────────────────────────

/**
 * On GET requests to wp-login.php, redirect to the appropriate custom page.
 * POST requests (form submissions) pass through so WP can process them.
 */
add_action( 'login_init', function (): void {
	if ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
		return;
	}

	$action = sanitize_key( $_REQUEST['action'] ?? 'login' );

	$map = [
		'login'        => 'sign-in',
		'register'     => 'sign-up',
		'lostpassword' => 'forgot-password',
		'rp'           => 'reset-password',
		'resetpass'    => 'reset-password',
	];

	if ( ! isset( $map[ $action ] ) ) {
		return;
	}

	$custom = reci_get_auth_page_url( $map[ $action ] );
	if ( ! $custom ) {
		return;
	}

	if ( ! empty( $_GET['redirect_to'] ) ) {
		$custom = add_query_arg( 'redirect_to', rawurlencode( $_GET['redirect_to'] ), $custom );
	}

	if ( ! empty( $_GET['resetpass'] ) && $_GET['resetpass'] === 'complete' ) {
		$custom = add_query_arg( 'resetpass', 'complete', $custom );
	}

	if ( in_array( $action, [ 'rp', 'resetpass' ], true ) ) {
		$custom = add_query_arg( [
			'key'   => rawurlencode( wp_unslash( $_GET['key'] ?? '' ) ),
			'login' => rawurlencode( wp_unslash( $_GET['login'] ?? '' ) ),
		], $custom );
	}

	wp_safe_redirect( $custom, 302 );
	exit;
} );

// ── Redirect failed logins back to custom sign-in page ───────────────────────

add_action( 'wp_login_failed', function ( string $username, \WP_Error $error = null ): void {
	$sign_in = reci_get_auth_page_url( 'sign-in' );
	if ( ! $sign_in ) {
		return;
	}
	
	$error_code = $error ? $error->get_error_code() : '1';
	$url = add_query_arg( [
		'login_error' => $error_code,
		'u'           => rawurlencode( $username ),
	], $sign_in );
	
	wp_safe_redirect( $url );
	exit;
}, 10, 2 );

// ── Redirect logged-in users away from auth pages ────────────────────────────

add_action( 'template_redirect', function (): void {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$auth_slugs = [ 'sign-in', 'sign-up', 'forgot-password', 'verify-email' ];
	$auth_ids   = array_filter( array_map( 'reci_get_auth_page_id', $auth_slugs ) );

	if ( is_page( $auth_ids ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
} );

// ── Custom registration form handler ─────────────────────────────────────────

add_action( 'admin_post_nopriv_reci_register', 'reci_handle_registration' );
add_action( 'admin_post_reci_register',        'reci_handle_registration' );

function reci_handle_registration(): void {
	$sign_up_url = reci_get_auth_page_url( 'sign-up' ) ?: wp_registration_url();

	// Nonce check.
	if ( empty( $_POST['reci_sign_up_nonce'] ) || ! wp_verify_nonce( $_POST['reci_sign_up_nonce'], 'reci_sign_up' ) ) {
		wp_safe_redirect( add_query_arg( 'reg_error', 'invalid_nonce', $sign_up_url ) );
		exit;
	}

	// Registration must be enabled.
	if ( ! get_option( 'users_can_register' ) ) {
		wp_safe_redirect( add_query_arg( 'reg_error', 'registration_disabled', $sign_up_url ) );
		exit;
	}

	// Collect + sanitize fields.
	$first_name   = sanitize_text_field( wp_unslash( $_POST['reci_firstname']   ?? '' ) );
	$last_name    = sanitize_text_field( wp_unslash( $_POST['reci_lastname']    ?? '' ) );
	$full_name    = trim( $first_name . ' ' . $last_name );
	$email        = sanitize_email( wp_unslash( $_POST['user_email']            ?? '' ) );
	$password     = wp_unslash( $_POST['user_pass']         ?? '' );
	$pass_confirm = wp_unslash( $_POST['reci_pass_confirm'] ?? '' );

	// Required-field validation.
	if ( ! $full_name || ! is_email( $email ) || ! $password ) {
		wp_safe_redirect( add_query_arg( 'reg_error', 'missing_fields', $sign_up_url ) );
		exit;
	}

	if ( $password !== $pass_confirm ) {
		wp_safe_redirect( add_query_arg( 'reg_error', 'password_mismatch', $sign_up_url ) );
		exit;
	}

	if ( strlen( $password ) < 8 ) {
		wp_safe_redirect( add_query_arg( 'reg_error', 'password_too_short', $sign_up_url ) );
		exit;
	}

	// Derive a unique username from the email local-part.
	$base     = sanitize_user( strstr( $email, '@', true ), true );
	$username = $base;
	$suffix   = 1;
	while ( username_exists( $username ) ) {
		$username = $base . $suffix++;
	}

	// Create the WordPress user.
	$user_id = wp_create_user( $username, $password, $email );

	if ( is_wp_error( $user_id ) ) {
		wp_safe_redirect( add_query_arg( 'reg_error', $user_id->get_error_code(), $sign_up_url ) );
		exit;
	}

	// Persist extra fields.
	wp_update_user( [
		'ID'           => $user_id,
		'display_name' => $full_name,
		'first_name'   => $first_name,
		'last_name'    => $last_name,
	] );

	// Require email verification.
	$token = wp_generate_password( 24, false );
	update_user_meta( $user_id, '_reci_verify_token', $token );
	update_user_meta( $user_id, '_reci_is_verified', '0' );

	$verify_url = add_query_arg( [
		'action' => 'reci_verify_email',
		'u'      => $user_id,
		't'      => $token,
	], admin_url( 'admin-post.php' ) );

	$subject = 'Verify your email address';
	$message = sprintf(
		"Hello %s,\n\nPlease verify your email address by clicking the link below:\n\n%s\n\nIf you did not request this, please ignore this email.",
		$full_name,
		$verify_url
	);
	wp_mail( $email, $subject, $message );

	// Redirect to sign-in page with success message.
	$sign_in = reci_get_auth_page_url( 'sign-in' );
	if ( $sign_in ) {
		wp_safe_redirect( add_query_arg( 'reg_success', 'check_email', $sign_in ) );
	} else {
		wp_safe_redirect( home_url( '/' ) );
	}
	exit;
}

// ── Verify email token handler ───────────────────────────────────────────────

add_action( 'admin_post_nopriv_reci_verify_email', 'reci_handle_email_verification' );
add_action( 'admin_post_reci_verify_email',        'reci_handle_email_verification' );

function reci_handle_email_verification(): void {
	$user_id = (int) ( $_GET['u'] ?? 0 );
	$token   = wp_unslash( $_GET['t'] ?? '' );
	$sign_in = reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url();

	if ( ! $user_id || ! $token ) {
		wp_safe_redirect( add_query_arg( 'login_error', 'invalid_verify_link', $sign_in ) );
		exit;
	}

	$stored_token = get_user_meta( $user_id, '_reci_verify_token', true );
	if ( ! $stored_token || ! hash_equals( $stored_token, $token ) ) {
		wp_safe_redirect( add_query_arg( 'login_error', 'invalid_verify_token', $sign_in ) );
		exit;
	}

	update_user_meta( $user_id, '_reci_is_verified', '1' );
	delete_user_meta( $user_id, '_reci_verify_token' );

	wp_set_auth_cookie( $user_id );
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

// ── Resend verification email handler ────────────────────────────────────────

add_action( 'admin_post_nopriv_reci_resend_verify', 'reci_handle_resend_verification' );
add_action( 'admin_post_reci_resend_verify',        'reci_handle_resend_verification' );

function reci_handle_resend_verification(): void {
	$sign_in = reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url();
	$email   = sanitize_email( wp_unslash( $_GET['u'] ?? '' ) ); // We passed username/email via 'u'
	
	if ( ! $email ) {
		wp_safe_redirect( $sign_in );
		exit;
	}
	
	$user = get_user_by( 'login', $email ) ?: get_user_by( 'email', $email );
	if ( ! $user ) {
		// Fail silently for security
		wp_safe_redirect( add_query_arg( 'reg_success', 'email_resent', $sign_in ) );
		exit;
	}
	
	$is_verified = get_user_meta( $user->ID, '_reci_is_verified', true );
	if ( $is_verified !== '0' ) {
		// Already verified
		wp_safe_redirect( $sign_in );
		exit;
	}
	
	$token = wp_generate_password( 24, false );
	update_user_meta( $user->ID, '_reci_verify_token', $token );
	
	$verify_url = add_query_arg( [
		'action' => 'reci_verify_email',
		'u'      => $user->ID,
		't'      => $token,
	], admin_url( 'admin-post.php' ) );
	
	$subject = 'Verify your email address';
	$message = sprintf(
		"Hello %s,\n\nPlease verify your email address by clicking the link below:\n\n%s\n\nIf you did not request this, please ignore this email.",
		$user->display_name ?: $user->user_login,
		$verify_url
	);
	wp_mail( $user->user_email, $subject, $message );
	
	wp_safe_redirect( add_query_arg( 'reg_success', 'email_resent', $sign_in ) );
	exit;
}

// ── Restrict unverified logins ───────────────────────────────────────────────

add_filter( 'wp_authenticate_user', function ( $user ) {
	if ( is_wp_error( $user ) ) {
		return $user;
	}
	$is_verified = get_user_meta( $user->ID, '_reci_is_verified', true );
	if ( $is_verified === '0' ) {
		return new WP_Error( 'unverified_email', 'Please verify your email address before logging in.' );
	}
	return $user;
}, 10, 1 );

// ── wp-admin protection ───────────────────────────────────────────────────────

/**
 * Remove the admin bar for non-editors.
 */
add_action( 'after_setup_theme', function (): void {
	if ( is_user_logged_in() && ! current_user_can( 'edit_pages' ) ) {
		show_admin_bar( false );
	}
} );

/**
 * Redirect non-editors away from wp-admin (except AJAX).
 */
add_action( 'admin_init', function (): void {
	if ( ! wp_doing_ajax() && ! current_user_can( 'edit_pages' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
} );

// ── Custom Reset Password Handler ──────────────────────────────────────────

add_action( 'admin_post_nopriv_reci_reset_password', 'reci_handle_reset_password' );
add_action( 'admin_post_reci_reset_password',        'reci_handle_reset_password' );

function reci_handle_reset_password(): void {
	$login = wp_unslash( $_POST['rp_login'] ?? '' );
	$key   = wp_unslash( $_POST['rp_key'] ?? '' );
	
	$reset_url = reci_get_auth_page_url( 'reset-password' );
	if ( ! $reset_url ) {
		$reset_url = wp_login_url();
	}
	$reset_url = add_query_arg( [ 'login' => rawurlencode( $login ), 'key' => rawurlencode( $key ) ], $reset_url );

	if ( empty( $_POST['reci_reset_nonce'] ) || ! wp_verify_nonce( $_POST['reci_reset_nonce'], 'reci_reset_password' ) ) {
		wp_safe_redirect( add_query_arg( 'error', 'invalidkey', $reset_url ) );
		exit;
	}

	$user = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) ) {
		wp_safe_redirect( add_query_arg( 'error', 'expiredkey', $reset_url ) );
		exit;
	}

	$pass1 = wp_unslash( $_POST['pass1'] ?? '' );
	$pass2 = wp_unslash( $_POST['pass2'] ?? '' );

	if ( empty( $pass1 ) || $pass1 !== $pass2 ) {
		wp_safe_redirect( add_query_arg( 'error', 'mismatch', $reset_url ) );
		exit;
	}

	reset_password( $user, $pass1 );

	$sign_in = reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url();
	wp_safe_redirect( add_query_arg( 'resetpass', 'complete', $sign_in ) );
	exit;
}
