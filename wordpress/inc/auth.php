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

/**
 * wp_lostpassword_url() → custom forgot-password page.
 */
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

	wp_safe_redirect( $custom, 302 );
	exit;
} );

// ── Redirect failed logins back to custom sign-in page ───────────────────────

add_action( 'wp_login_failed', function ( string $username ): void {
	$sign_in = reci_get_auth_page_url( 'sign-in' );
	if ( ! $sign_in ) {
		return;
	}
	// WP already set $_COOKIE with error; just redirect with a flag.
	wp_safe_redirect( add_query_arg( 'login_error', '1', $sign_in ) );
	exit;
} );

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
	$full_name    = sanitize_text_field( wp_unslash( $_POST['reci_fullname']    ?? '' ) );
	$email        = sanitize_email( wp_unslash( $_POST['user_email']            ?? '' ) );
	$phone        = sanitize_text_field( wp_unslash( $_POST['reci_phone']       ?? '' ) );
	$city         = sanitize_text_field( wp_unslash( $_POST['reci_city']        ?? '' ) );
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
		'first_name'   => $full_name,
	] );

	if ( $phone ) {
		update_user_meta( $user_id, 'reci_phone', $phone );
	}
	if ( $city ) {
		update_user_meta( $user_id, 'reci_city', $city );
	}

	// Sign the user in immediately.
	wp_set_auth_cookie( $user_id );

	// Redirect to verify-email page (if it exists) or home.
	$next = reci_get_auth_page_url( 'verify-email' ) ?: home_url( '/' );
	wp_safe_redirect( $next );
	exit;
}

// ── wp-admin protection ───────────────────────────────────────────────────────

/**
 * Remove the admin bar for non-administrator users.
 */
add_action( 'after_setup_theme', function (): void {
	if ( is_user_logged_in() && ! current_user_can( 'manage_options' ) ) {
		show_admin_bar( false );
	}
} );

/**
 * Redirect non-administrator users away from wp-admin (except AJAX).
 */
add_action( 'admin_init', function (): void {
	if ( ! wp_doing_ajax() && ! current_user_can( 'manage_options' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
} );
