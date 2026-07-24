<?php

/**
 * RECI Media Hub — Admin Branding & Editor Experience.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// 1. Menu Rearrangement (Prioritizing Editor Functions)
// ---------------------------------------------------------------------------

add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'reci_custom_admin_menu_order' );

function reci_custom_admin_menu_order( array $menu_ord ): array {
	if ( ! $menu_ord ) {
		return [];
	}

	$reci_content_types = [
		'edit.php',                                  // Articles (Posts)
		'edit.php?post_type=reci_reflection',
		'edit.php?post_type=reci_podcast',
		'edit.php?post_type=reci_video',
		'edit.php?post_type=reci_event',
		'edit.php?post_type=reci_course',
		'edit.php?post_type=reci_assessment',
		'edit.php?post_type=reci_quote',
		'edit.php?post_type=reci_glossary_term',
		'edit.php?post_type=reci_testimonial',
		'edit.php?post_type=reci_team',
		'edit.php?post_type=reci_author',
		'edit.php?post_type=reci_show',
		'edit.php?post_type=reci_journal',
	];

	$new_order = [ 'index.php', 'separator1' ];
	
	// Add RECI Content Types
	foreach ( $reci_content_types as $pt ) {
		if ( in_array( $pt, $menu_ord, true ) ) {
			$new_order[] = $pt;
		}
	}
	
	// Add Media directly after content
	if ( in_array( 'upload.php', $menu_ord, true ) ) {
		$new_order[] = 'upload.php';
	}
	
	// Add Pages
	if ( in_array( 'edit.php?post_type=page', $menu_ord, true ) ) {
		$new_order[] = 'edit.php?post_type=page';
	}
	
	$new_order[] = 'separator2';
	
	// Append everything else that wasn't already added
	foreach ( $menu_ord as $item ) {
		if ( ! in_array( $item, $new_order, true ) ) {
			$new_order[] = $item;
		}
	}

	return $new_order;
}

// ---------------------------------------------------------------------------
// 2. Custom Dashboard Home Page
// ---------------------------------------------------------------------------

add_action( 'wp_dashboard_setup', 'reci_custom_dashboard_setup', 999 );

function reci_custom_dashboard_setup(): void {
	global $wp_meta_boxes;

	// Remove default WP widgets
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts'] );
	remove_action( 'welcome_panel', 'wp_welcome_panel' );

	// Add custom RECI widget
	wp_add_dashboard_widget(
		'reci_editor_dashboard',
		'Welcome to RECI Media Hub',
		'reci_render_editor_dashboard_widget'
	);
}

function reci_render_editor_dashboard_widget(): void {
	$primary_color = reci_setting( 'branding_primary_color', '#003594' );
	$logo_id       = reci_setting( 'branding_reci_logo' );
	$logo_url      = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	?>
	<div class="reci-dashboard-welcome">
		<div style="background-color: <?php echo esc_attr( $primary_color ); ?>; color: #fff; padding: 24px; border-radius: 8px; margin-bottom: 24px;">
			<?php if ( $logo_url ) : ?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo" style="max-height: 48px; margin-bottom: 16px; display: block;" />
			<?php endif; ?>
			<h2 style="margin: 0 0 8px; font-size: 24px; font-weight: 600; color: #fff;">Hello, <?php echo esc_html( wp_get_current_user()->display_name ); ?>!</h2>
			<p style="margin: 0; font-size: 15px; opacity: 0.9;">Welcome to the RECI Media Hub Editor Dashboard. What would you like to create or edit today?</p>
		</div>

		<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
			<?php
			$front_page_id = get_option( 'page_on_front' );
			$quick_links = [
				[ 'label' => 'Edit Home Page', 'url' => admin_url( 'post.php?post=' . $front_page_id . '&action=edit' ), 'icon' => 'dashicons-admin-home' ],
				[ 'label' => 'New Article',    'url' => admin_url( 'post-new.php' ), 'icon' => 'dashicons-admin-post' ],
				[ 'label' => 'New Reflection', 'url' => admin_url( 'post-new.php?post_type=reci_reflection' ), 'icon' => 'dashicons-art' ],
				[ 'label' => 'New Event',      'url' => admin_url( 'post-new.php?post_type=reci_event' ), 'icon' => 'dashicons-calendar-alt' ],
				[ 'label' => 'New Course',     'url' => admin_url( 'post-new.php?post_type=reci_course' ), 'icon' => 'dashicons-welcome-learn-more' ],
				[ 'label' => 'New Video',      'url' => admin_url( 'post-new.php?post_type=reci_video' ), 'icon' => 'dashicons-video-alt3' ],
				[ 'label' => 'Manage Media',   'url' => admin_url( 'upload.php' ), 'icon' => 'dashicons-admin-media' ],
			];

			foreach ( $quick_links as $link ) {
				printf(
					'<a href="%1$s" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; color: #111827; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
						<span class="dashicons %2$s" style="font-size: 32px; width: 32px; height: 32px; margin-bottom: 12px; color: %3$s;"></span>
						<span style="font-weight: 500;">%4$s</span>
					</a>',
					esc_url( $link['url'] ),
					esc_attr( $link['icon'] ),
					esc_attr( $primary_color ),
					esc_html( $link['label'] )
				);
			}
			?>
		</div>
	</div>
	<style>
		#reci_editor_dashboard { border: none; box-shadow: none; background: transparent; }
		#reci_editor_dashboard .inside { padding: 0; margin: 0; }
		#reci_editor_dashboard h2.hndle { display: none; }
		#reci_editor_dashboard .handlediv { display: none; }
		.reci-dashboard-welcome a:hover { border-color: <?php echo esc_attr( $primary_color ); ?> !important; background: #fff !important; box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important; transform: translateY(-2px); }
	</style>
	<?php
}

// ---------------------------------------------------------------------------
// 3. Login Flow Overhaul (Redirect wp-login.php to Frontend)
// ---------------------------------------------------------------------------

add_action( 'init', 'reci_intercept_wp_login' );

function reci_intercept_wp_login(): void {
	global $pagenow;
	
	// Only intercept on wp-login.php
	if ( 'wp-login.php' !== $pagenow ) {
		return;
	}

	// Don't intercept POST requests (allows default authentication mechanism to work if hit programmatically)
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		return;
	}

	$action = $_GET['action'] ?? '';

	// Allow logout and password reset to function natively for safety/reliability
	if ( in_array( $action, [ 'logout', 'rp', 'resetpass' ], true ) ) {
		return;
	}

	// Redirect all other requests to the frontend sign-in page
	$sign_in_url = reci_get_auth_page_url( 'sign-in' );
	if ( $sign_in_url ) {
		wp_safe_redirect( $sign_in_url );
		exit;
	}
}

// ---------------------------------------------------------------------------
// 4. Admin Color Scheme & CSS
// ---------------------------------------------------------------------------

add_action( 'admin_head', 'reci_custom_admin_styles' );

function reci_custom_admin_styles(): void {
	$primary = reci_setting( 'branding_primary_color', '#003594' );
	$accent  = reci_setting( 'branding_accent_color', '#FFB81C' );
	?>
	<style>
		/* Admin Bar Logo Replacement */
		#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
			content: '' !important;
			display: block;
			width: 20px;
			height: 20px;
			background-color: <?php echo esc_attr( $accent ); ?>;
			border-radius: 50%;
			margin-top: 6px;
		}
		
		/* Buttons */
		.wp-core-ui .button-primary {
			background: <?php echo esc_attr( $primary ); ?> !important;
			border-color: <?php echo esc_attr( $primary ); ?> !important;
			color: #fff !important;
		}
		.wp-core-ui .button-primary:hover {
			opacity: 0.9;
		}
		
		/* Admin Menu Current Item */
		#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, 
		#adminmenu li.current a.menu-top, 
		.wp-core-ui .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
		.wp-core-ui .wp-menu-arrow div {
			background: <?php echo esc_attr( $primary ); ?> !important;
		}
	</style>
	<?php
}
