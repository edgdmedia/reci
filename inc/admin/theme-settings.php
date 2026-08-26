<?php

/**
 * RECI Media Hub — Theme Settings Page.
 *
 * Registers an admin settings page under Appearance > RECI Settings with
 * tabbed sections covering all 8 configuration categories.
 *
 * Usage in templates:
 *   reci_setting( 'branding_primary_color', '#003594' )
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Helper
// ---------------------------------------------------------------------------

/**
 * Retrieve a theme setting value, falling back to $default when not set.
 *
 * @param string $key     Option key (without the `reci_` prefix).
 * @param mixed  $default Fallback value.
 * @return mixed
 */
function reci_setting( string $key, $default = '' ) {
	$options = get_option( 'reci_theme_settings', [] );
	return isset( $options[ $key ] ) && $options[ $key ] !== '' ? $options[ $key ] : $default;
}

// ---------------------------------------------------------------------------
// Admin menu
// ---------------------------------------------------------------------------

add_action( 'admin_menu', 'reci_register_settings_menu' );

function reci_register_settings_menu(): void {
	add_menu_page(
		'RECI Settings',
		'RECI Settings',
		'manage_options',
		'reci-settings',
		'reci_settings_page_html',
		'dashicons-admin-generic',
		58
	);
}

// ---------------------------------------------------------------------------
// Register settings
// ---------------------------------------------------------------------------

add_action( 'admin_init', 'reci_register_settings' );

function reci_register_settings(): void {

	register_setting(
		'reci_theme_settings_group',
		'reci_theme_settings',
		[ 'sanitize_callback' => 'reci_sanitize_settings' ]
	);

	// ── 1. Branding ──────────────────────────────────────────────────────
	add_settings_section( 'reci_branding', 'Branding', '__return_false', 'reci-settings-branding' );

	reci_add_field( 'branding_reci_logo',       'RECI Logo',             'image',  'reci-settings-branding', 'reci_branding' );
	reci_add_field( 'branding_partner_logo',    'Partner Logo (Pitt)',   'image',  'reci-settings-branding', 'reci_branding' );
	reci_add_field( 'branding_hub_subtitle',    'Site Subtitle',         'text',   'reci-settings-branding', 'reci_branding', 'e.g. Media Hub' );
	reci_add_field( 'branding_primary_color',   'Primary Colour',        'color',  'reci-settings-branding', 'reci_branding' );
	reci_add_field( 'branding_accent_color',    'Accent Colour',         'color',  'reci-settings-branding', 'reci_branding' );

	// ── 2. Key Pages ─────────────────────────────────────────────────────
	add_settings_section( 'reci_pages_sec', 'Key Pages', '__return_false', 'reci-settings-pages' );

	reci_add_field( 'pages_sign_in',    'Sign In Page',         'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_sign_up',    'Sign Up Page',         'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_become_collaborator', 'Become a Collaborator Page', 'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_forgot_pw',  'Forgot Password Page', 'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_donate',     'Donate Page',          'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_community',  'Community Page',       'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_reflection', 'Reflection Gallery',   'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_home',       'Homepage',             'page', 'reci-settings-pages', 'reci_pages_sec' );

	// ── 3. Social & Platform Links ────────────────────────────────────────
	add_settings_section( 'reci_social', 'Social & Platform Links', '__return_false', 'reci-settings-social' );

	reci_add_field( 'social_facebook',  'Facebook URL',  'url', 'reci-settings-social', 'reci_social' );
	reci_add_field( 'social_twitter',   'X / Twitter URL', 'url', 'reci-settings-social', 'reci_social' );
	reci_add_field( 'social_instagram', 'Instagram URL', 'url', 'reci-settings-social', 'reci_social' );
	reci_add_field( 'social_youtube',   'YouTube URL',   'url', 'reci-settings-social', 'reci_social' );
	reci_add_field( 'social_linkedin',  'LinkedIn URL',  'url', 'reci-settings-social', 'reci_social' );

	// ── 4. Authentication ─────────────────────────────────────────────────
	add_settings_section( 'reci_auth', 'Authentication', '__return_false', 'reci-settings-auth' );

	reci_add_field( 'auth_enable_registration', 'Enable Registration',      'checkbox', 'reci-settings-auth', 'reci_auth', 'Allow new users to self-register' );
	reci_add_field( 'auth_google_client_id',    'Google OAuth Client ID',   'text',     'reci-settings-auth', 'reci_auth' );
	reci_add_field( 'auth_login_redirect',      'Post-Login Redirect URL',  'url',      'reci-settings-auth', 'reci_auth', 'Leave blank to redirect to homepage' );

	// ── 5. Homepage ───────────────────────────────────────────────────────
	add_settings_section( 'reci_homepage', 'Homepage', '__return_false', 'reci-settings-homepage' );

	reci_add_field( 'hp_today_count',       '"Today at RECI" carousel items',   'number', 'reci-settings-homepage', 'reci_homepage', 'Default: 5' );
	reci_add_field( 'hp_quotes_count',      '"Quote of the Day" carousel items', 'number', 'reci-settings-homepage', 'reci_homepage', 'Default: 3' );
	reci_add_field( 'hp_community_count',   '"Community Pulse" carousel items',  'number', 'reci-settings-homepage', 'reci_homepage', 'Default: 3' );
	reci_add_field( 'hp_featured_method',   'Featured Article Selection',        'select', 'reci-settings-homepage', 'reci_homepage' );

	// ── 6. About Page ─────────────────────────────────────────────────────
	add_settings_section( 'reci_about', 'About Page Cards', '__return_false', 'reci-settings-about' );

	reci_add_field( 'about_c1_title', 'Card 1 Title', 'text',     'reci-settings-about', 'reci_about' );
	reci_add_field( 'about_c1_copy',  'Card 1 Copy',  'textarea', 'reci-settings-about', 'reci_about' );
	reci_add_field( 'about_c1_icon',  'Card 1 Icon/Image',  'image',     'reci-settings-about', 'reci_about' );

	reci_add_field( 'about_c2_title', 'Card 2 Title', 'text',     'reci-settings-about', 'reci_about' );
	reci_add_field( 'about_c2_copy',  'Card 2 Copy',  'textarea', 'reci-settings-about', 'reci_about' );
	reci_add_field( 'about_c2_icon',  'Card 2 Icon/Image',  'image',     'reci-settings-about', 'reci_about' );

	reci_add_field( 'about_c3_title', 'Card 3 Title', 'text',     'reci-settings-about', 'reci_about' );
	reci_add_field( 'about_c3_copy',  'Card 3 Copy',  'textarea', 'reci-settings-about', 'reci_about' );
	reci_add_field( 'about_c3_icon',  'Card 3 Icon/Image',  'image',     'reci-settings-about', 'reci_about' );

	// ── 7. Footer ─────────────────────────────────────────────────────────
	add_settings_section( 'reci_footer', 'Footer', '__return_false', 'reci-settings-footer' );

	reci_add_field( 'footer_tagline',   'About / Tagline',        'textarea', 'reci-settings-footer', 'reci_footer' );
	reci_add_field( 'footer_email',     'Contact Email',          'email',    'reci-settings-footer', 'reci_footer' );
	reci_add_field( 'footer_phone',     'Contact Phone',          'text',     'reci-settings-footer', 'reci_footer' );
	reci_add_field( 'footer_address',   'Physical Address',       'textarea', 'reci-settings-footer', 'reci_footer' );
	reci_add_field( 'footer_copyright', 'Copyright Text Override','text',     'reci-settings-footer', 'reci_footer', 'Leave blank to use "© {year} RECI. All rights reserved."' );

	// ── 7. Analytics ──────────────────────────────────────────────────────
	add_settings_section( 'reci_analytics', 'Analytics', '__return_false', 'reci-settings-analytics' );

	reci_add_field( 'analytics_ga4_id',   'GA4 Measurement ID',       'text', 'reci-settings-analytics', 'reci_analytics', 'e.g. G-XXXXXXXXXX' );
	reci_add_field( 'analytics_gtm_id',   'GTM Container ID',         'text', 'reci-settings-analytics', 'reci_analytics', 'e.g. GTM-XXXXXXX' );
	reci_add_field( 'analytics_pixel_id', 'Meta / Facebook Pixel ID', 'text', 'reci-settings-analytics', 'reci_analytics' );

	// ── 8. Content Defaults ───────────────────────────────────────────────
	add_settings_section( 'reci_content', 'Content Defaults', '__return_false', 'reci-settings-content' );

	reci_add_field( 'content_articles_per_page',  'Articles per page',  'number', 'reci-settings-content', 'reci_content', 'Default: 12' );
	reci_add_field( 'content_podcasts_per_page',  'Podcasts per page',  'number', 'reci-settings-content', 'reci_content', 'Default: 12' );
	reci_add_field( 'content_videos_per_page',    'Videos per page',    'number', 'reci-settings-content', 'reci_content', 'Default: 12' );
	reci_add_field( 'content_fallback_thumbnail', 'Default Thumbnail',  'image',  'reci-settings-content', 'reci_content' );
}

// ---------------------------------------------------------------------------
// Field registration helper
// ---------------------------------------------------------------------------

function reci_add_field(
	string $key,
	string $label,
	string $type,
	string $page,
	string $section,
	string $description = ''
): void {
	add_settings_field(
		'reci_' . $key,
		$label,
		'reci_render_field',
		$page,
		$section,
		[
			'key'         => $key,
			'type'        => $type,
			'description' => $description,
			'label_for'   => 'reci_' . $key,
		]
	);
}

// ---------------------------------------------------------------------------
// Field renderer
// ---------------------------------------------------------------------------

function reci_render_field( array $args ): void {
	$key     = $args['key'];
	$type    = $args['type'];
	$desc    = $args['description'] ?? '';
	$id      = 'reci_' . $key;
	$name    = 'reci_theme_settings[' . $key . ']';
	$options = get_option( 'reci_theme_settings', [] );
	$val     = $options[ $key ] ?? '';

	switch ( $type ) {

		case 'text':
		case 'email':
		case 'url':
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" placeholder="%s" />',
				esc_attr( $type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $val ),
				esc_attr( $desc )
			);
			break;

		case 'number':
			printf(
				'<input type="number" id="%s" name="%s" value="%s" class="small-text" min="1" max="100" placeholder="%s" />',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $val ),
				esc_attr( $desc )
			);
			if ( $desc ) {
				echo '<p class="description">' . esc_html( $desc ) . '</p>';
			}
			break;

		case 'color':
			printf(
				'<input type="color" id="%s" name="%s" value="%s" />',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $val ?: '#003594' )
			);
			break;

		case 'textarea':
			printf(
				'<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $val )
			);
			break;

		case 'checkbox':
			printf(
				'<label><input type="checkbox" id="%s" name="%s" value="1" %s /> %s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( $val, '1', false ),
				esc_html( $desc )
			);
			break;

		case 'select':
			// Currently only used for featured article selection method.
			$choices = [
				'latest'   => 'Latest post',
				'sticky'   => 'Sticky post',
				'manual'   => 'Manually selected post',
			];
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( $choices as $choice_val => $choice_label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $choice_val ),
					selected( $val, $choice_val, false ),
					esc_html( $choice_label )
				);
			}
			echo '</select>';
			break;

		case 'page':
			// Dropdown of published pages.
			wp_dropdown_pages( [
				'name'              => $name,
				'id'                => $id,
				'selected'          => (int) $val,
				'show_option_none'  => '— select a page —',
				'option_none_value' => '0',
			] );
			break;

		case 'image':
			$attachment_id = (int) $val;
			$thumb_url     = $attachment_id
				? wp_get_attachment_image_url( $attachment_id, 'thumbnail' )
				: '';
			?>
			<div class="reci-image-field" style="display:flex;align-items:center;gap:12px;">
				<?php if ( $thumb_url ) : ?>
					<img
						src="<?php echo esc_url( $thumb_url ); ?>"
						alt=""
						style="max-height:60px;width:auto;border:1px solid #ddd;" />
				<?php endif; ?>
				<input
					type="hidden"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $val ); ?>" />
				<button
					type="button"
					class="button reci-media-upload"
					data-target="<?php echo esc_attr( $id ); ?>"
					data-preview="<?php echo esc_attr( $id ); ?>-preview">
					<?php echo $thumb_url ? 'Change Image' : 'Upload / Select Image'; ?>
				</button>
				<?php if ( $thumb_url ) : ?>
					<button
						type="button"
						class="button reci-media-remove"
						data-target="<?php echo esc_attr( $id ); ?>"
						style="color:#a00;">
						Remove
					</button>
				<?php endif; ?>
			</div>
			<?php
			break;
	}

	// Generic description for non-special types.
	if ( $desc && ! in_array( $type, [ 'checkbox', 'number' ], true ) ) {
		echo '<p class="description">' . esc_html( $desc ) . '</p>';
	}
}

// ---------------------------------------------------------------------------
// Sanitization
// ---------------------------------------------------------------------------

function reci_sanitize_settings( $input ): array {
	if ( ! is_array( $input ) ) {
		return [];
	}

	$clean = [];

	$text_fields = [
		'branding_hub_subtitle', 'branding_primary_color', 'branding_accent_color',
		'analytics_ga4_id', 'analytics_gtm_id', 'analytics_pixel_id',
		'auth_google_client_id', 'footer_phone', 'footer_copyright',
		'hp_featured_method',
		'about_c1_title', 'about_c1_icon',
		'about_c2_title', 'about_c2_icon',
		'about_c3_title', 'about_c3_icon',
	];
	$url_fields = [
		'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube',
		'social_linkedin', 'auth_login_redirect',
	];
	$email_fields = [ 'footer_email' ];
	$textarea_fields = [ 'footer_tagline', 'footer_address', 'about_c1_copy', 'about_c2_copy', 'about_c3_copy' ];
	$number_fields = [
		'hp_today_count', 'hp_quotes_count', 'hp_community_count',
		'content_articles_per_page', 'content_podcasts_per_page', 'content_videos_per_page',
	];
	$page_fields = [
		'pages_sign_in', 'pages_sign_up', 'pages_forgot_pw', 'pages_donate',
		'pages_reflection', 'pages_home',
	];
	$image_fields = [
		'branding_reci_logo', 'branding_partner_logo', 'content_fallback_thumbnail',
	];
	$checkbox_fields = [ 'auth_enable_registration' ];

	foreach ( $text_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = sanitize_text_field( $input[ $field ] );
		}
	}
	foreach ( $url_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = esc_url_raw( $input[ $field ] );
		}
	}
	foreach ( $email_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = sanitize_email( $input[ $field ] );
		}
	}
	foreach ( $textarea_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = sanitize_textarea_field( $input[ $field ] );
		}
	}
	foreach ( $number_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$val = (int) $input[ $field ];
			$clean[ $field ] = $val > 0 ? $val : '';
		}
	}
	foreach ( $page_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = (int) $input[ $field ] ?: '';
		}
	}
	foreach ( $image_fields as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = (int) $input[ $field ] ?: '';
		}
	}
	foreach ( $checkbox_fields as $field ) {
		$clean[ $field ] = ! empty( $input[ $field ] ) ? '1' : '0';
	}

	return $clean;
}

// ---------------------------------------------------------------------------
// Admin scripts (media uploader)
// ---------------------------------------------------------------------------

add_action( 'admin_enqueue_scripts', function ( string $hook ) {
	if ( $hook !== 'appearance_page_reci-settings' ) {
		return;
	}
	wp_enqueue_media();
	wp_add_inline_script( 'jquery-core', reci_media_uploader_js() );
} );

add_action('admin_head-post.php', 'reci_media_hub_quote_admin_head');
add_action('admin_head-post-new.php', 'reci_media_hub_quote_admin_head');
function reci_media_hub_quote_admin_head(): void {
	$screen = get_current_screen();
	if (!$screen || $screen->post_type !== 'reci_quote') {
		return;
	}
	?>
	<style>
		#post-body-content { display: none; }
		#postdivrich { display: none; }
		#edit-slug-box { display: none; }
	</style>
	<?php
}

function reci_media_uploader_js(): string {
	return <<<'JS'
jQuery(function($){
	$(document).on('click', '.reci-media-upload', function(e){
		e.preventDefault();
		var btn      = $(this);
		var targetId = btn.data('target');
		var frame    = wp.media({ title: 'Select Image', button: { text: 'Use this image' }, multiple: false });
		frame.on('select', function(){
			var attachment = frame.state().get('selection').first().toJSON();
			$('#' + targetId).val(attachment.id);
			var preview = btn.siblings('img');
			if(preview.length){
				preview.attr('src', attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);
			} else {
				btn.before('<img src="'+(attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url)+'" alt="" style="max-height:60px;width:auto;border:1px solid #ddd;" />');
			}
			btn.text('Change Image');
			if(!btn.next('.reci-media-remove').length){
				btn.after('<button type="button" class="button reci-media-remove" data-target="'+targetId+'" style="color:#a00;">Remove</button>');
			}
		});
		frame.open();
	});
	$(document).on('click', '.reci-media-remove', function(e){
		e.preventDefault();
		var targetId = $(this).data('target');
		$('#' + targetId).val('');
		$(this).siblings('img').remove();
		$(this).siblings('.reci-media-upload').text('Upload / Select Image');
		$(this).remove();
	});
});
JS;
}

// ---------------------------------------------------------------------------
// Settings page HTML
// ---------------------------------------------------------------------------

function reci_settings_page_html(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs = [
		'branding'  => 'Branding',
		'pages'     => 'Key Pages',
		'social'    => 'Social Links',
		'auth'      => 'Authentication',
		'homepage'  => 'Homepage',
		'about'     => 'About Page',
		'footer'    => 'Footer',
		'analytics' => 'Analytics',
		'content'   => 'Content Defaults',
	];

	$active = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs )
		? sanitize_key( $_GET['tab'] )
		: 'branding';

	// Demo action notices.
	$demo_notice = isset( $_GET['demo_notice'] ) ? sanitize_key( $_GET['demo_notice'] ) : '';

	?>
	<div class="wrap">
		<h1>RECI Theme Settings</h1>

		<?php if ( $demo_notice === 'installed' ) : ?>
			<div class="notice notice-success is-dismissible"><p>Demo content installed successfully.</p></div>
		<?php elseif ( $demo_notice === 'reset' ) : ?>
			<div class="notice notice-warning is-dismissible"><p>Demo content removed.</p></div>
		<?php endif; ?>

		<nav class="nav-tab-wrapper" style="margin-bottom:0;">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a
					href="<?php echo esc_url( admin_url( 'admin.php?page=reci-settings&tab=' . $slug ) ); ?>"
					class="nav-tab <?php echo $active === $slug ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<form method="post" action="options.php" style="margin-top:16px;">
			<?php
			settings_fields( 'reci_theme_settings_group' );
			do_settings_sections( 'reci-settings-' . $active );
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

// ---------------------------------------------------------------------------
// Analytics snippets (output in <head> / before </body>)
// ---------------------------------------------------------------------------

add_action( 'wp_head', 'reci_output_analytics', 1 );

function reci_output_analytics(): void {
	$ga4_id   = reci_setting( 'analytics_ga4_id' );
	$gtm_id   = reci_setting( 'analytics_gtm_id' );
	$pixel_id = reci_setting( 'analytics_pixel_id' );

	if ( $ga4_id ) {
		$ga4_id = esc_js( $ga4_id );
		echo <<<HTML
<!-- Google Analytics (GA4) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$ga4_id}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$ga4_id}');</script>
HTML;
	}

	if ( $gtm_id ) {
		$gtm_id = esc_js( $gtm_id );
		echo <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$gtm_id}');</script>
HTML;
	}

	if ( $pixel_id ) {
		$pixel_id = esc_js( $pixel_id );
		echo <<<HTML
<!-- Meta Pixel -->
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{$pixel_id}');fbq('track','PageView');</script>
HTML;
	}
}

add_action( 'wp_body_open', 'reci_output_gtm_noscript' );

function reci_output_gtm_noscript(): void {
	$gtm_id = reci_setting( 'analytics_gtm_id' );
	if ( ! $gtm_id ) {
		return;
	}
	$gtm_id = esc_attr( $gtm_id );
	echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $gtm_id . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
}
