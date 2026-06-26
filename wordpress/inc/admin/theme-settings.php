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
	reci_add_field( 'pages_forgot_pw',  'Forgot Password Page', 'page', 'reci-settings-pages', 'reci_pages_sec' );
	reci_add_field( 'pages_donate',     'Donate Page',          'page', 'reci-settings-pages', 'reci_pages_sec' );
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

	// ── 6. Footer ─────────────────────────────────────────────────────────
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
	];
	$url_fields = [
		'social_facebook', 'social_twitter', 'social_instagram', 'social_youtube',
		'social_linkedin', 'auth_login_redirect',
	];
	$email_fields = [ 'footer_email' ];
	$textarea_fields = [ 'footer_tagline', 'footer_address' ];
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
		'footer'    => 'Footer',
		'analytics' => 'Analytics',
		'content'   => 'Content Defaults',
		'demo'      => 'Demo Content',
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

		<?php if ( $active === 'demo' ) : ?>
			<div style="margin-top:20px;">
				<?php reci_settings_demo_tab_html(); ?>
			</div>
		<?php else : ?>
			<form method="post" action="options.php" style="margin-top:16px;">
				<?php
				settings_fields( 'reci_theme_settings_group' );
				do_settings_sections( 'reci-settings-' . $active );
				submit_button( 'Save Settings' );
				?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

function reci_settings_demo_tab_html(): void {
	$installed = get_option( 'reci_demo_installed', false );
	$count     = count( get_option( 'reci_demo_slugs', [] ) );
	$job       = function_exists( 'reci_demo_get_job' ) ? reci_demo_get_job() : [];
	$job_state = function_exists( 'reci_demo_present_job_state' ) ? reci_demo_present_job_state( $job ) : [];
	?>
	<h2>Demo Content</h2>

	<?php if ( $installed ) : ?>
		<p>
			<span style="color:#007017;font-weight:600;">&#10003; Demo content is installed</span>
			— <?php echo (int) $count; ?> demo posts across all content types.
		</p>
		<p>To remove all demo posts, click Reset below. This permanently deletes the demo posts.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:24px;">
			<?php wp_nonce_field( 'reci_demo_action' ); ?>
			<input type="hidden" name="action" value="reci_reset_demo" />
			<button
				type="submit"
				class="button button-secondary"
				onclick="return confirm('Remove all demo content? This cannot be undone.');">
				Reset Demo Content
			</button>
		</form>
		<h3>Install Additional Demo Content</h3>
		<p>Check content types to add, then click Install. Existing posts will not be duplicated.</p>
	<?php else : ?>
		<p>Select content types to import, then click Install.</p>
	<?php endif; ?>

	<form id="reci-demo-import-form">
		<table class="widefat" style="margin-bottom:16px;">
			<thead><tr><th style="width:40px;"><input type="checkbox" id="reci-demo-select-all" checked /></th><th>Content Type</th><th>Posts</th></tr></thead>
			<tbody>
				<?php foreach ( reci_demo_content_types() as $pt => $info ) : ?>
					<tr>
						<td><input type="checkbox" name="reci_demo_types[]" value="<?php echo esc_attr( $pt ); ?>" <?php echo $installed ? '' : 'checked'; ?> /></td>
						<td><?php echo esc_html( $info['label'] ); ?></td>
						<td><?php echo (int) $info['count']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<script>document.getElementById('reci-demo-select-all').addEventListener('change',function(){document.querySelectorAll('[name="reci_demo_types[]"]').forEach(function(cb){cb.checked=this.checked;},this);});</script>
		<p style="color:#856404;background:#fff3cd;border-left:4px solid #ffc107;padding:8px 12px;">
			<strong>Note:</strong> Use image groups to import bundled media first. Reflection posts now validate required assets and report missing ones instead of failing silently.
		</p>
		<p>
			<button type="submit" class="button button-primary" id="reci-demo-start-import">Install Selected Demo Content</button>
		</p>
	</form>

	<div id="reci-demo-progress" style="margin-top:20px;border:1px solid #dcdcde;border-radius:6px;padding:16px;background:#fff;<?php echo empty( $job_state ) ? 'display:none;' : ''; ?>">
		<h3 style="margin-top:0;">Import Progress</h3>
		<p><strong id="reci-demo-progress-label"><?php echo esc_html( $job_state['current_label'] ?? 'Idle' ); ?></strong></p>
		<progress id="reci-demo-progress-bar" value="<?php echo esc_attr( (string) ( $job_state['percent'] ?? 0 ) ); ?>" max="100" style="width:100%;height:18px;"></progress>
		<p id="reci-demo-progress-meta" style="margin:8px 0 16px;"><?php echo esc_html( $job_state['progress_text'] ?? '' ); ?></p>
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
			<div>
				<h4 style="margin:0 0 8px;">Activity</h4>
				<ul id="reci-demo-activity-log" style="max-height:220px;overflow:auto;margin:0;padding-left:18px;"></ul>
			</div>
			<div>
				<h4 style="margin:0 0 8px;">Results</h4>
				<div id="reci-demo-completed"></div>
				<div id="reci-demo-failed" style="margin-top:12px;"></div>
				<div id="reci-demo-skipped" style="margin-top:12px;"></div>
			</div>
		</div>
	</div>

	<script>
	(function(){
		const form = document.getElementById('reci-demo-import-form');
		if (!form) return;

		const startBtn = document.getElementById('reci-demo-start-import');
		const progressWrap = document.getElementById('reci-demo-progress');
		const label = document.getElementById('reci-demo-progress-label');
		const bar = document.getElementById('reci-demo-progress-bar');
		const meta = document.getElementById('reci-demo-progress-meta');
		const activity = document.getElementById('reci-demo-activity-log');
		const completed = document.getElementById('reci-demo-completed');
		const failed = document.getElementById('reci-demo-failed');
		const skipped = document.getElementById('reci-demo-skipped');
		const resetBtn = document.querySelector('button[name="action"][value="reci_reset_demo"], form[action*="admin-post.php"] .button-secondary');

		const config = <?php echo wp_json_encode([
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'reci_demo_action' ),
			'initialState' => $job_state,
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>;

		function escapeHtml(value) {
			return String(value ?? '').replace(/[&<>"']/g, function(ch) {
				return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[ch];
			});
		}

		function renderList(target, title, items, tone) {
			if (!target) return;
			if (!Array.isArray(items) || items.length === 0) {
				target.innerHTML = '';
				return;
			}
			const color = tone === 'failed' ? '#b32d2e' : (tone === 'skipped' ? '#996800' : '#007017');
			target.innerHTML = '<strong style="color:' + color + ';">' + escapeHtml(title) + '</strong><ul style="margin:6px 0 0;padding-left:18px;">' + items.map(function(item){
				return '<li><strong>' + escapeHtml(item.label || item.path || item.slug || 'Item') + '</strong>: ' + escapeHtml(item.message || '') + '</li>';
			}).join('') + '</ul>';
		}

		function renderState(state) {
			if (!state || typeof state !== 'object') return;
			progressWrap.style.display = 'block';
			label.textContent = state.current_label || 'Idle';
			bar.value = Number(state.percent || 0);
			meta.textContent = state.progress_text || '';
			activity.innerHTML = (state.activity || []).map(function(entry){
				return '<li>' + escapeHtml(entry) + '</li>';
			}).join('');
			renderList(completed, 'Completed', state.completed || [], 'completed');
			renderList(failed, 'Failed', state.failed || [], 'failed');
			renderList(skipped, 'Skipped', state.skipped || [], 'skipped');
			if (resetBtn) {
				resetBtn.disabled = !!state.running;
			}
			if (startBtn) {
				startBtn.disabled = !!state.running;
				startBtn.textContent = state.running ? 'Import Running…' : 'Install Selected Demo Content';
			}
		}

		async function postAction(action, extra) {
			const body = new URLSearchParams();
			body.set('action', action);
			body.set('nonce', config.nonce);
			Object.entries(extra || {}).forEach(function(entry) {
				const key = entry[0];
				const value = entry[1];
				if (Array.isArray(value)) {
					value.forEach(function(item) { body.append(key + '[]', item); });
				} else {
					body.set(key, value);
				}
			});

			const response = await fetch(config.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
				credentials: 'same-origin'
			});
			return await response.json();
		}

		async function runJob() {
			while (true) {
				const payload = await postAction('reci_demo_process_step');
				if (!payload || !payload.success) {
					throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'Import step failed.');
				}
				renderState(payload.data);
				if (payload.data.finished) break;
			}
		}

		form.addEventListener('submit', async function(event) {
			event.preventDefault();
			const selected = Array.from(form.querySelectorAll('[name="reci_demo_types[]"]:checked')).map(function(input){ return input.value; });
			if (!selected.length) {
				window.alert('Select at least one import group.');
				return;
			}

			startBtn.disabled = true;
			try {
				const payload = await postAction('reci_demo_start_import', { selected: selected });
				if (!payload || !payload.success) {
					throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'Could not start import.');
				}
				renderState(payload.data);
				await runJob();
			} catch (error) {
				progressWrap.style.display = 'block';
				label.textContent = 'Import Error';
				meta.textContent = error.message || 'Unknown error.';
			} finally {
				startBtn.disabled = false;
			}
		});

		if (config.initialState && config.initialState.running) {
			renderState(config.initialState);
			runJob().catch(function(error){
				label.textContent = 'Import Error';
				meta.textContent = error.message || 'Unknown error.';
			});
		} else if (config.initialState && Object.keys(config.initialState).length) {
			renderState(config.initialState);
		}
	})();
	</script>
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
