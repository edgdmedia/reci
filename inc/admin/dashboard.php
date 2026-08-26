<?php
/**
 * Dashboard — rewrite rules, auth guards, AJAX handlers, assets.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Rewrite rules
// ---------------------------------------------------------------------------

add_action( 'init', 'reci_dashboard_rewrite_rules' );
function reci_dashboard_rewrite_rules(): void {
	$pages = [
		'my-content' => 'template-dashboard-my-content.php',
		'submit'     => 'template-dashboard-submit.php',
		'bookmarks'  => 'template-dashboard-bookmarks.php',
		'notifications' => 'template-dashboard-notifications.php',
		'journal'    => 'template-dashboard-journal.php',
		'comments'   => 'template-dashboard-comments.php',
		'profile'    => 'template-dashboard-profile.php',
		'settings'   => 'template-dashboard-settings.php',
	];

	foreach ( $pages as $slug => $template_file ) {
		add_rewrite_rule(
			'^dashboard/' . $slug . '/?$',
			'index.php?pagename=dashboard&dashboard_page=' . $slug . '&dashboard_template=' . $template_file,
			'top'
		);
	}
	add_rewrite_rule( '^dashboard/?$', 'index.php?pagename=dashboard', 'top' );
}

add_filter( 'query_vars', 'reci_dashboard_query_vars' );
function reci_dashboard_query_vars( array $vars ): array {
	$vars[] = 'dashboard_page';
	$vars[] = 'dashboard_template';
	return $vars;
}

// ---------------------------------------------------------------------------
// Page template loader
// ---------------------------------------------------------------------------

add_filter( 'template_include', 'reci_dashboard_template_loader' );
function reci_dashboard_template_loader( string $template ): string {
	if ( get_query_var( 'pagename' ) !== 'dashboard' ) {
		return $template;
	}

	$template_file = get_query_var( 'dashboard_template' );
	if ( $template_file ) {
		$path = get_template_directory() . '/templates/page/dashboard/' . $template_file;
		if ( file_exists( $path ) ) {
			return $path;
		}
	}

	$base = get_template_directory() . '/templates/page/dashboard/template-dashboard.php';
	return file_exists( $base ) ? $base : $template;
}

// ---------------------------------------------------------------------------
// Auth guards
// ---------------------------------------------------------------------------

add_action( 'template_redirect', 'reci_dashboard_auth_check' );
function reci_dashboard_auth_check(): void {
	if ( get_query_var( 'pagename' ) !== 'dashboard' ) {
		return;
	}
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url( home_url( '/dashboard/' ) ) );
		exit;
	}
}

add_action( 'template_redirect', 'reci_dashboard_author_guard' );
function reci_dashboard_author_guard(): void {
	if ( get_query_var( 'pagename' ) !== 'dashboard' ) {
		return;
	}
	if ( 'my-content' === get_query_var( 'dashboard_page' ) && ( ! function_exists( 'reci_user_is_collaborator' ) || ! reci_user_is_collaborator() ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}
}

// ---------------------------------------------------------------------------
// Bookmark & Like helpers
// ---------------------------------------------------------------------------

function reci_get_user_bookmarks( int $user_id ): array {
	$bookmarks = get_user_meta( $user_id, 'reci_bookmarks', true );
	return is_array( $bookmarks ) ? $bookmarks : [];
}

function reci_get_user_likes( int $user_id ): array {
	$likes = get_user_meta( $user_id, 'reci_likes', true );
	return is_array( $likes ) ? $likes : [];
}

function reci_get_user_followed_term_ids( int $user_id, string $meta_key ): array {
	$values = get_user_meta( $user_id, $meta_key, true );
	if ( ! is_array( $values ) ) {
		return [];
	}

	return array_values( array_filter( array_map( 'absint', $values ) ) );
}

function reci_get_user_personalization_preferences( int $user_id ): array {
	return [
		'topics'          => reci_get_user_followed_term_ids( $user_id, 'reci_followed_topics' ),
		'spheres'         => reci_get_user_followed_term_ids( $user_id, 'reci_followed_spheres' ),
		'practice_focus'  => reci_get_user_followed_term_ids( $user_id, 'reci_followed_practice_focus' ),
		'target_audience' => reci_get_user_followed_term_ids( $user_id, 'reci_followed_target_audience' ),
		'collaborators'   => function_exists( 'reci_get_user_followed_collaborator_ids' ) ? reci_get_user_followed_collaborator_ids( $user_id ) : [],
	];
}

function reci_get_personalized_dashboard_posts( int $user_id, int $limit = 6 ): array {
	$preferences = reci_get_user_personalization_preferences( $user_id );
	$tax_query   = [ 'relation' => 'OR' ];
	$post_ids    = [];

	if ( ! empty( $preferences['topics'] ) ) {
		$tax_query[] = [
			'taxonomy' => 'reci_topic',
			'field'    => 'term_id',
			'terms'    => $preferences['topics'],
		];
	}

	if ( ! empty( $preferences['spheres'] ) ) {
		$tax_query[] = [
			'taxonomy' => 'reci_sphere',
			'field'    => 'term_id',
			'terms'    => $preferences['spheres'],
		];
	}

	if ( ! empty( $preferences['practice_focus'] ) ) {
		$tax_query[] = [
			'taxonomy' => 'reci_practice_focus',
			'field'    => 'term_id',
			'terms'    => $preferences['practice_focus'],
		];
	}

	if ( ! empty( $preferences['target_audience'] ) ) {
		$tax_query[] = [
			'taxonomy' => 'reci_target_audience',
			'field'    => 'term_id',
			'terms'    => $preferences['target_audience'],
		];
	}

	if ( count( $tax_query ) > 1 ) {
		$taxonomy_posts = get_posts(
			[
				'post_type'           => [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_course', 'reci_reflection', 'reci_document' ],
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'ignore_sticky_posts' => true,
				'tax_query'           => $tax_query,
				'fields'              => 'ids',
			]
		);
		$post_ids = array_merge( $post_ids, array_map( 'absint', $taxonomy_posts ) );
	}

	if ( ! empty( $preferences['collaborators'] ) && function_exists( 'reci_media_hub_get_authored_content_ids' ) ) {
		foreach ( $preferences['collaborators'] as $profile_id ) {
			$post_ids = array_merge( $post_ids, reci_media_hub_get_authored_content_ids( (int) $profile_id, [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_course', 'reci_reflection', 'reci_document' ] ) );
		}
	}

	$post_ids = array_values( array_unique( array_filter( array_map( 'absint', $post_ids ) ) ) );
	if ( empty( $post_ids ) ) {
		return [];
	}

	return get_posts(
		[
			'post_type'           => [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_course', 'reci_reflection', 'reci_document' ],
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'ignore_sticky_posts' => true,
			'post__in'            => $post_ids,
			'orderby'             => 'date',
			'order'               => 'DESC',
		]
	);
}

/**
 * Render Like and Bookmark buttons for a post.
 */
function reci_render_post_actions( int $post_id = 0 ): string {
	if ( ! $post_id ) { $post_id = get_the_ID(); }
	if ( ! is_user_logged_in() ) { return ''; }

	$user_id   = get_current_user_id();
	$bookmarks = reci_get_user_bookmarks( $user_id );
	$likes     = reci_get_user_likes( $user_id );

	$is_bookmarked = false;
	foreach ( $bookmarks as $b ) {
		if ( (int) $b['post_id'] === $post_id ) { $is_bookmarked = true; break; }
	}

	$is_liked = in_array( $post_id, $likes );

	$out = '<div class="reci-post-actions flex items-center gap-2">';
	
	// Like Button
	$out .= sprintf(
		'<button class="reci-like-btn %s flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full border transition-all" data-post-id="%d" data-liked="%d">',
		$is_liked ? 'liked bg-red-50 text-red-600 border-red-200' : 'bg-zinc-50 text-zinc-600 border-zinc-200 hover:border-red-300 hover:text-red-600',
		$post_id,
		$is_liked ? 1 : 0
	);
	$out .= '<svg class="w-3.5 h-3.5 ' . ( $is_liked ? 'fill-current' : '' ) . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>';
	$out .= '<span class="like-label">' . ( $is_liked ? 'Liked' : 'Like' ) . '</span>';
	$out .= '</button>';

	// Bookmark Button
	$out .= sprintf(
		'<button class="reci-bookmark-btn %s flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full border transition-all" data-post-id="%d" data-bookmarked="%d">',
		$is_bookmarked ? 'bookmarked bg-amber-50 text-amber-700 border-amber-200' : 'bg-zinc-50 text-zinc-600 border-zinc-200 hover:border-amber-300 hover:text-amber-700',
		$post_id,
		$is_bookmarked ? 1 : 0
	);
	$out .= '<svg class="w-3.5 h-3.5 ' . ( $is_bookmarked ? 'fill-current' : '' ) . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>';
	$out .= '<span class="bookmark-label">' . ( $is_bookmarked ? 'Saved' : 'Save' ) . '</span>';
	$out .= '</button>';

	$out .= '</div>';

	return $out;
}

// ---------------------------------------------------------------------------
// AJAX — bookmark toggle
// ---------------------------------------------------------------------------

add_action( 'wp_ajax_reci_toggle_bookmark', 'reci_ajax_toggle_bookmark' );
function reci_ajax_toggle_bookmark(): void {
	check_ajax_referer( 'reci_dashboard_nonce', 'nonce' );

	$post_id = absint( $_POST['post_id'] ?? 0 );
	if ( ! $post_id || ! get_post( $post_id ) ) {
		wp_send_json_error( [ 'message' => 'Invalid post.' ] );
	}

	$user_id   = get_current_user_id();
	$bookmarks = reci_get_user_bookmarks( $user_id );
	$found_key = null;

	foreach ( $bookmarks as $k => $b ) {
		if ( (int) $b['post_id'] === $post_id ) {
			$found_key = $k;
			break;
		}
	}

	if ( null !== $found_key ) {
		unset( $bookmarks[ $found_key ] );
		$bookmarked = false;
	} else {
		$bookmarks[] = [ 'post_id' => $post_id, 'bookmarked_at' => time() ];
		$bookmarked = true;
	}

	update_user_meta( $user_id, 'reci_bookmarks', array_values( $bookmarks ) );
	wp_send_json_success( [ 'bookmarked' => $bookmarked ] );
}

// ---------------------------------------------------------------------------
// AJAX — like toggle
// ---------------------------------------------------------------------------

add_action( 'wp_ajax_reci_toggle_like', 'reci_ajax_toggle_like' );
function reci_ajax_toggle_like(): void {
	check_ajax_referer( 'reci_dashboard_nonce', 'nonce' );

	$post_id = absint( $_POST['post_id'] ?? 0 );
	if ( ! $post_id || ! get_post( $post_id ) ) {
		wp_send_json_error( [ 'message' => 'Invalid post.' ] );
	}

	$user_id = get_current_user_id();
	$likes   = reci_get_user_likes( $user_id );

	if ( ( $key = array_search( $post_id, $likes ) ) !== false ) {
		unset( $likes[ $key ] );
		$liked = false;
	} else {
		$likes[] = $post_id;
		$liked = true;
	}

	update_user_meta( $user_id, 'reci_likes', array_values( $likes ) );
	wp_send_json_success( [ 'liked' => $liked ] );
}

add_action( 'wp_ajax_reci_get_post_state', 'reci_ajax_get_post_state' );
function reci_ajax_get_post_state(): void {
	check_ajax_referer( 'reci_dashboard_nonce', 'nonce' );
	$post_id   = absint( $_POST['post_id'] ?? 0 );
	$user_id   = get_current_user_id();
	
	$bookmarks = reci_get_user_bookmarks( $user_id );
	$bookmarked = false;
	foreach ( $bookmarks as $b ) {
		if ( (int) $b['post_id'] === $post_id ) { $bookmarked = true; break; }
	}

	$likes = reci_get_user_likes( $user_id );
	$liked = in_array( $post_id, $likes );

	wp_send_json_success( [ 'bookmarked' => $bookmarked, 'liked' => $liked ] );
}

add_action( 'wp_ajax_reci_mark_notification_read', 'reci_ajax_mark_notification_read' );
function reci_ajax_mark_notification_read(): void {
	check_ajax_referer( 'reci_dashboard_nonce', 'nonce' );

	$notification_id = absint( $_POST['notification_id'] ?? 0 );
	$user_id         = get_current_user_id();

	if ( ! $notification_id || ! $user_id ) {
		wp_send_json_error( [ 'message' => 'Invalid notification.' ] );
	}

	if ( ! function_exists( 'reci_mark_notification_read' ) || ! reci_mark_notification_read( $notification_id, $user_id ) ) {
		wp_send_json_error( [ 'message' => 'Could not update notification.' ] );
	}

	wp_send_json_success( [ 'notification_id' => $notification_id ] );
}

// ---------------------------------------------------------------------------
// AJAX — reflection modal signin / signup
// ---------------------------------------------------------------------------

add_action( 'wp_ajax_nopriv_reci_modal_signin', 'reci_ajax_modal_signin' );
function reci_ajax_modal_signin(): void {
	check_ajax_referer( 'reci_dashboard_nonce', 'nonce' );
	$email    = sanitize_email( $_POST['email'] ?? '' );
	$password = $_POST['password'] ?? '';
	$user     = wp_signon( [ 'user_login' => $email, 'user_password' => $password ] );
	if ( is_wp_error( $user ) ) {
		wp_send_json_error( [ 'message' => 'Invalid email or password.' ] );
	}
	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID );
	wp_send_json_success( [ 'user_id' => $user->ID, 'rest_nonce' => wp_create_nonce('wp_rest') ] );
}

add_action( 'wp_ajax_nopriv_reci_modal_signup', 'reci_ajax_modal_signup' );
function reci_ajax_modal_signup(): void {
	check_ajax_referer( 'reci_dashboard_nonce', 'nonce' );
	$email    = sanitize_email( $_POST['email'] ?? '' );
	$password = $_POST['password'] ?? '';
	$name     = sanitize_text_field( $_POST['display_name'] ?? '' );

	if ( email_exists( $email ) ) {
		wp_send_json_error( [ 'message' => 'An account with this email already exists.' ] );
	}
	if ( strlen( $password ) < 8 ) {
		wp_send_json_error( [ 'message' => 'Password must be at least 8 characters.' ] );
	}

	$user_id = wp_insert_user( [
		'user_login'   => $email,
		'user_email'   => $email,
		'display_name' => $name,
		'user_pass'    => $password,
		'role'         => 'subscriber',
	] );

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( [ 'message' => $user_id->get_error_message() ] );
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	wp_send_json_success( [ 'user_id' => $user_id, 'rest_nonce' => wp_create_nonce('wp_rest') ] );
}

// ---------------------------------------------------------------------------
// Enqueue dashboard JS
// ---------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'reci_dashboard_enqueue_assets' );
function reci_dashboard_enqueue_assets(): void {
	if ( get_query_var( 'pagename' ) !== 'dashboard' && ! is_singular() ) {
		return;
	}

	$file = get_template_directory() . '/assets/js/dashboard.js';
	$uri  = get_template_directory_uri() . '/assets/js/dashboard.js';
	if ( ! file_exists( $file ) ) {
		return;
	}

	wp_enqueue_script( 'reci-dashboard', $uri, [], filemtime( $file ), true );
	wp_localize_script( 'reci-dashboard', 'reciDashboard', [
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'reci_dashboard_nonce' ),
		'restNonce' => wp_create_nonce( 'wp_rest' ),
		'restUrl'   => esc_url_raw( rest_url() ),
	] );

	if ( is_singular( 'reci_reflection' ) ) {
		$modal_file = get_template_directory() . '/assets/js/dashboard-reflection-modal.js';
		$modal_uri  = get_template_directory_uri() . '/assets/js/dashboard-reflection-modal.js';
		if ( file_exists( $modal_file ) ) {
			wp_enqueue_script( 'reci-dashboard-reflection-modal', $modal_uri, [ 'reci-dashboard' ], filemtime( $modal_file ), true );
		}
	}
}

// ---------------------------------------------------------------------------
// Reflection signup modal HTML
// ---------------------------------------------------------------------------

add_action( 'wp_footer', 'reci_reflection_signup_modal' );
function reci_reflection_signup_modal(): void {
	if ( ! is_singular( 'reci_reflection' ) ) {
		return;
	}
	?>
	<div id="reci-reflection-modal" data-logged-in="<?php echo is_user_logged_in() ? '1' : '0'; ?>" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50" role="dialog" aria-modal="true">
		<div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
			<div class="flex items-center justify-between mb-6">
				<h2 class="text-xl font-bold font-heading text-zinc-800">Save Your Reflection</h2>
				<button type="button" id="reci-modal-close" class="text-zinc-400 hover:text-zinc-600 text-2xl leading-none">&times;</button>
			</div>

			<div id="reci-modal-signin" class="space-y-4">
				<p class="text-sm text-zinc-600">Sign in to save your reflection to your journal.</p>
				<form id="reci-modal-signin-form">
					<input type="email" name="email" placeholder="Email" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm mb-3">
					<input type="password" name="password" placeholder="Password" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm mb-3">
					<button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg">Sign In</button>
				</form>
				<p class="text-xs text-center text-zinc-500">Don't have an account? <button type="button" id="reci-modal-show-signup" class="text-amber-600 hover:text-amber-700 underline">Sign Up</button></p>
			</div>

			<div id="reci-modal-signup" class="space-y-4 hidden">
				<p class="text-sm text-zinc-600">Create a free account to save your reflections.</p>
				<form id="reci-modal-signup-form">
					<input type="text" name="display_name" placeholder="Display Name" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm mb-3">
					<input type="email" name="email" placeholder="Email" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm mb-3">
					<input type="password" name="password" placeholder="Password (min 8 chars)" required minlength="8" class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm mb-3">
					<button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg">Create Account</button>
				</form>
				<p class="text-xs text-center text-zinc-500">Already have an account? <button type="button" id="reci-modal-show-signin" class="text-amber-600 hover:text-amber-700 underline">Sign In</button></p>
			</div>

			<button type="button" id="reci-modal-skip" class="mt-4 w-full text-center text-sm text-zinc-400 hover:text-zinc-600 underline underline-offset-2">Continue without saving</button>
		</div>
	</div>
	<?php
}
