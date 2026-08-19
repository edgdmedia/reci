<?php

/**
 * Theme support and setup hooks.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_setup')) {
	function reci_media_hub_setup(): void
	{
		add_theme_support('title-tag');
		add_theme_support('post-thumbnails');
		add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			]
		);

		register_nav_menus(
			[
				'primary'        => __('Primary Menu', 'reci-media-hub'),
				'footer_primary' => __('Footer Primary Menu', 'reci-media-hub'),
				'footer_legal'   => __('Footer Legal Menu', 'reci-media-hub'),
			]
		);
	}
}

add_action('after_setup_theme', 'reci_media_hub_setup');

/**
 * Output favicon link tag.
 */
function reci_media_hub_favicon(): void {
	$favicon = get_template_directory_uri() . '/favicon.png';
	printf('<link rel="icon" type="image/png" href="%s">' . "\n", esc_url($favicon));
}
add_action('wp_head', 'reci_media_hub_favicon', 1);

if (! function_exists('reci_media_hub_enqueue_assets')) {
	function reci_media_hub_enqueue_assets(): void
	{
		wp_enqueue_style(
			'reci-media-hub-fonts',
			'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Roboto:wght@400;700&family=Instrument+Serif&display=swap',
			[],
			null
		);

		wp_enqueue_style(
			'reci-media-hub-style',
			get_stylesheet_uri(),
			['reci-media-hub-fonts'],
			wp_get_theme()->get('Version')
		);

		$tailwind_relative_path = '/assets/css/tailwind.css';
		$tailwind_file_path     = get_template_directory() . $tailwind_relative_path;

		if (file_exists($tailwind_file_path)) {
			wp_enqueue_style(
				'reci-media-hub-tailwind',
				get_template_directory_uri() . $tailwind_relative_path,
				['reci-media-hub-style'],
				(string) filemtime($tailwind_file_path)
			);
		}

		$js_relative_path = '/assets/js/theme.js';
		$js_file_path     = get_template_directory() . $js_relative_path;

		if (file_exists($js_file_path)) {
			wp_enqueue_script(
				'reci-media-hub-theme',
				get_template_directory_uri() . $js_relative_path,
				[],
				(string) filemtime($js_file_path),
				true
			);

			if (is_singular('reci_assessment')) {
				$current_user = wp_get_current_user();
				wp_add_inline_script(
					'reci-media-hub-theme',
					'window.RECIAssessmentConfig = ' . wp_json_encode([
						'restEndpoint'     => esc_url_raw(rest_url('reci/v1/assessment-submit')),
						'progressEndpoint' => esc_url_raw(rest_url('reci/v1/assessment-progress')),
						'restNonce'        => wp_create_nonce('wp_rest'),
						'loginUrl'         => esc_url_raw(wp_login_url(get_permalink())),
						'registerUrl'      => esc_url_raw(wp_registration_url()),
						'currentUser'      => [
							'isLoggedIn' => is_user_logged_in(),
							'name'       => is_user_logged_in() ? $current_user->display_name : '',
						],
					]) . ';',
					'before'
				);
			}
		}

		$live_search_path = get_template_directory() . '/assets/js/live-search.js';
		if (file_exists($live_search_path)) {
			wp_enqueue_script(
				'reci-live-search',
				get_template_directory_uri() . '/assets/js/live-search.js',
				[],
				(string) filemtime($live_search_path),
				true
			);
		}

		$submit_templates = [
			'templates/page/template-submit-content.php',
			'templates/page/dashboard/template-dashboard-submit.php',
		];
		$is_dashboard_submit_page = get_query_var( 'pagename' ) === 'dashboard' && get_query_var( 'dashboard_page' ) === 'submit';
		$is_submit_page = $is_dashboard_submit_page || (bool) array_reduce( $submit_templates, fn( $carry, $t ) => $carry || is_page_template( $t ), false );
		if ( $is_submit_page ) {
			$submission_path = get_template_directory() . '/assets/js/submission-form.js';
			if (file_exists($submission_path)) {
				$current_user = wp_get_current_user();
				$is_logged_in_user = is_user_logged_in() && ($current_user instanceof WP_User) && $current_user->exists();

				$first_name = '';
				$last_name  = '';
				$email      = '';
				$organization = '';
				$role         = '';
				$bio          = '';
				$website      = '';

				if ($is_logged_in_user) {
					$first_name_meta = (string) get_user_meta($current_user->ID, 'first_name', true);
					$last_name_meta  = (string) get_user_meta($current_user->ID, 'last_name', true);

					$first_name = $first_name_meta !== '' ? $first_name_meta : (string) ($current_user->user_firstname ?? '');
					$last_name  = $last_name_meta !== '' ? $last_name_meta : (string) ($current_user->user_lastname ?? '');
					$email      = (string) $current_user->user_email;

					$organization_candidates = [
						(string) get_user_meta($current_user->ID, 'organization', true),
						(string) get_user_meta($current_user->ID, 'company', true),
						(string) get_user_meta($current_user->ID, 'institution', true),
						(string) get_user_meta($current_user->ID, 'affiliation', true),
						(string) get_user_meta($current_user->ID, 'reci_organization', true),
					];
					foreach ($organization_candidates as $candidate) {
						if ($candidate !== '') {
							$organization = $candidate;
							break;
						}
					}

					$role_candidates = [
						(string) get_user_meta($current_user->ID, 'user_title', true),
						(string) get_user_meta($current_user->ID, 'title', true),
						(string) get_user_meta($current_user->ID, 'job_title', true),
						(string) get_user_meta($current_user->ID, 'role_label', true),
						(string) get_user_meta($current_user->ID, 'reci_role', true),
					];
					foreach ($role_candidates as $candidate) {
						if ($candidate !== '') {
							$role = $candidate;
							break;
						}
					}

					$bio = (string) get_user_meta($current_user->ID, 'description', true);
					$website = (string) $current_user->user_url;
				}

				wp_enqueue_script(
					'reci-submission-form',
					get_template_directory_uri() . '/assets/js/submission-form.js',
					[],
					(string) filemtime($submission_path),
					true
				);

				wp_add_inline_script(
					'reci-submission-form',
					'window.RECISubmissionConfig = ' . wp_json_encode([
						'submitEndpoint' => admin_url('admin-post.php'),
						'submitNonce'    => wp_create_nonce('reci_submit_content'),
						'returnUrl'      => home_url('/'),
						'contentTypeOptions' => function_exists('reci_media_hub_submission_type_definitions') ? reci_media_hub_submission_type_definitions() : [],
						'spheres'        => function_exists('reci_media_hub_get_submission_spheres') ? reci_media_hub_get_submission_spheres() : [],
						'practiceOptions' => function_exists('reci_media_hub_get_taxonomy_terms_for_submission') ? reci_media_hub_get_taxonomy_terms_for_submission('reci_practice_focus') : [],
						'targetAudienceOptions' => function_exists('reci_media_hub_get_taxonomy_terms_for_submission') ? reci_media_hub_get_taxonomy_terms_for_submission('reci_target_audience') : [],
						'currentUser'    => [
							'isLoggedIn'   => $is_logged_in_user,
							'firstName'    => $first_name,
							'lastName'     => $last_name,
							'email'        => $email,
							'organization' => $organization,
							'role'         => $role,
							'bio'          => $bio,
							'website'      => $website,
						],
					]) . ';',
					'before'
				);

				add_filter('script_loader_tag', static function (string $tag, string $handle): string {
					if ($handle === 'reci-submission-form') {
						return str_replace(' src=', ' type="module" src=', $tag);
					}
					return $tag;
				}, 10, 2);
			}
		}
	}
}

add_action('wp_enqueue_scripts', 'reci_media_hub_enqueue_assets');

if (! function_exists('reci_media_hub_enqueue_reflection_renderer')) {
	function reci_media_hub_enqueue_reflection_renderer(): void
	{
		if (! is_singular('reci_reflection')) {
			return;
		}

		$post_id = get_queried_object_id();
		if ($post_id) {
			$selected_template = (string) get_post_meta($post_id, '_wp_page_template', true);
			if ($selected_template !== '' && $selected_template !== 'default') {
				return;
			}
			if (function_exists('reci_reflection_blueprint_uses_new_system') && reci_reflection_blueprint_uses_new_system($post_id)) {
				$controller_path = get_template_directory() . '/modules/reflection-system/assets/js/reflection-stage-controller.js';
				$controller_version = file_exists($controller_path) ? (string) filemtime($controller_path) : wp_get_theme()->get('Version');
				wp_enqueue_script(
					'reci-reflection-stage-controller',
					get_template_directory_uri() . '/modules/reflection-system/assets/js/reflection-stage-controller.js',
					[],
					$controller_version,
					true
				);

				$interactions = [
					'immersive-dark' => 'reflection-immersive-dark',
					'breaking-chains' => 'reflection-breaking-chains',
					'march-toward-justice' => 'reflection-march-toward-justice',
					'racial-disparities' => 'reflection-racial-disparities',
				];

				foreach ($interactions as $handle => $filename) {
					$js_path = get_template_directory() . '/modules/reflection-system/assets/js/' . $filename . '.js';
					if (file_exists($js_path)) {
						wp_enqueue_script(
							'reci-' . $handle,
							get_template_directory_uri() . '/modules/reflection-system/assets/js/' . $filename . '.js',
							['reci-reflection-stage-controller'],
							(string) filemtime($js_path),
							true
						);
					}
				}

				$vor_css_path = get_template_directory() . '/modules/reflection-system/assets/css/reflection-immersive-dark.css';
				if (file_exists($vor_css_path)) {
					wp_enqueue_style(
						'reci-reflection-immersive-dark-fonts',
						'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Roboto:wght@400;700&family=Instrument+Serif&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&family=Oswald:wght@300;400;500;700&family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&family=Roboto+Mono:wght@300;400;500&family=Space+Grotesk:wght@300;400;600;700&display=swap',
						[],
						null
					);
					wp_enqueue_style(
						'reci-reflection-immersive-dark-style',
						get_template_directory_uri() . '/modules/reflection-system/assets/css/reflection-immersive-dark.css',
						[],
						(string) filemtime($vor_css_path)
					);
				}

				// Breaking Chains style-specific CSS
				$bc_css_path = get_template_directory() . '/modules/reflection-system/assets/css/reflection-breaking-chains.css';
				if (file_exists($bc_css_path)) {
					wp_enqueue_style(
						'reci-reflection-breaking-chains-style',
						get_template_directory_uri() . '/modules/reflection-system/assets/css/reflection-breaking-chains.css',
						['reci-reflection-immersive-dark-style'],
						(string) filemtime($bc_css_path)
					);
				}

				// March Toward Justice style-specific CSS
				$march_css_path = get_template_directory() . '/modules/reflection-system/assets/css/reflection-march-toward-justice.css';
				if (file_exists($march_css_path)) {
					wp_enqueue_style(
						'reci-reflection-march-toward-justice-style',
						get_template_directory_uri() . '/modules/reflection-system/assets/css/reflection-march-toward-justice.css',
						['reci-reflection-immersive-dark-style'],
						(string) filemtime($march_css_path)
					);
				}

				$runtime_path = get_template_directory() . '/modules/reflection-system/assets/js/reflection-system-runtime.js';
				$runtime_version = file_exists($runtime_path) ? (string) filemtime($runtime_path) : wp_get_theme()->get('Version');
				wp_enqueue_script(
					'reci-reflection-system-runtime',
					get_template_directory_uri() . '/modules/reflection-system/assets/js/reflection-system-runtime.js',
					['reci-reflection-stage-controller'],
					$runtime_version,
					true
				);
				$runtime_css_path = get_template_directory() . '/modules/reflection-system/assets/css/reflection-system-runtime.css';
				if (file_exists($runtime_css_path)) {
					wp_enqueue_style(
						'reci-reflection-system-runtime-style',
						get_template_directory_uri() . '/modules/reflection-system/assets/css/reflection-system-runtime.css',
						['reci-reflection-immersive-dark-style'],
						(string) filemtime($runtime_css_path)
					);
				}
				// Execute legacy scripts synchronously to maintain dependencies
				return;
			}
		}

		$path    = get_template_directory() . '/modules/reflection-system/assets/js/reflection-renderer.js';
		$version = file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version');

		wp_enqueue_script(
			'reci-reflection-renderer',
			get_template_directory_uri() . '/modules/reflection-system/assets/js/reflection-renderer.js',
			[],
			$version,
			true
		);
		add_filter('script_loader_tag', static function (string $tag, string $handle): string {
			if ($handle === 'reci-reflection-renderer') {
				return str_replace(' src=', ' type="module" src=', $tag);
			}
			return $tag;
		}, 10, 2);
	}
}
add_action('wp_enqueue_scripts', 'reci_media_hub_enqueue_reflection_renderer');

if (! function_exists('reci_media_hub_enqueue_reflection_stage_styles')) {
	function reci_media_hub_enqueue_reflection_stage_styles(): void
	{
		if (! is_singular('reci_reflection')) {
			return;
		}

		$post_id = get_queried_object_id();
		if (! $post_id || ! function_exists('reci_reflection_blueprint_uses_new_system') || ! reci_reflection_blueprint_uses_new_system($post_id)) {
			return;
		}

		$css = <<<'CSS'
.reci-stage {
	position: fixed;
	inset: 0;
	display: none;
	opacity: 0;
	transition: opacity 0.7s ease;
	background: var(--reflection-bg);
	overflow-y: auto;
	overflow-x: hidden;
}
.reci-stage-shell {
	width: 100%;
	min-height: 100vh;
	display: flex;
	align-items: stretch;
}
.reci-stage-body {
	width: 100%;
	max-width: 1440px;
	margin: 0 auto;
	padding: 6rem 1.25rem 4.75rem;
	display: flex;
	flex-direction: column;
	justify-content: center;
}
.reci-continue {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	padding: 0.95rem 1.35rem;
	border-radius: 999px;
	border: 1px solid var(--reflection-border);
	background: var(--reflection-card);
	color: var(--reflection-text);
	font-family: 'Oswald', sans-serif;
	text-transform: uppercase;
	letter-spacing: 0.1em;
	cursor: pointer;
}
.reci-stage-grid {
	display: grid;
	gap: 1.5rem;
}
: {
	max-height: min(72vh, 820px);
	overflow-y: auto;
	padding-right: 0.5rem;
}
.reci-scroll-panel::-webkit-scrollbar { width: 8px; }
.reci-scroll-panel::-webkit-scrollbar-thumb { background: var(--reflection-border); border-radius: 999px; }
.reci-timeline-world {
	height: 100vh;
	width: 300vw;
	display: flex;
	transform: translateX(0);
	transition: transform 0.85s cubic-bezier(0.23, 1, 0.32, 1);
	will-change: transform;
}
.reci-timeline-panel {
	width: 100vw;
	height: 100vh;
	flex-shrink: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 6rem 1.25rem 4.75rem;
	position: relative;
	border-right: 1px solid var(--reflection-border-soft);
}
.reci-timeline-card {
	width: min(1120px, 100%);
	background: linear-gradient(180deg, var(--reflection-card-strong), var(--reflection-card));
	border: 1px solid var(--reflection-border-soft);
	border-radius: 2rem;
	padding: 2rem;
	box-shadow: 0 24px 60px rgba(0,0,0,0.18);
}
.reci-timeline-controls {
	position: absolute;
	left: 50%;
	bottom: 3.25rem;
	transform: translateX(-50%);
	display: flex;
	gap: 0.85rem;
	z-index: 10;
}
.reci-icon-btn {
	width: 3.1rem;
	height: 3.1rem;
	border-radius: 999px;
	border: 1px solid var(--reflection-border);
	background: var(--reflection-card);
	color: var(--reflection-text);
	cursor: pointer;
}
.reci-stage-panels {
	display: grid;
	grid-template-columns: minmax(280px, 420px) minmax(0, 1fr);
	gap: 1.5rem;
	align-items: stretch;
}
.reci-panel-scroll {
	max-height: min(72vh, 820px);
	overflow-y: auto;
	padding-right: 0.5rem;
}
.reci-panel-scroll::-webkit-scrollbar { width: 8px; }
.reci-panel-scroll::-webkit-scrollbar-thumb { background: var(--reflection-border); border-radius: 999px; }
.reci-stage-menu { display: grid; gap: 1rem; }
.reci-stage-menu button.active { background: var(--reflection-accent); color: var(--reflection-accent-contrast); border-color: transparent; }
.panel-hotspot {
	position: absolute;
	width: 22px;
	height: 22px;
	border-radius: 999px;
	border: 2px solid rgba(255,255,255,0.9);
	background: rgba(167, 199, 150, 0.95);
	color: #1a1713;
	font-size: 0.72rem;
	font-weight: 700;
	display: flex;
	align-items: center;
	justify-content: center;
	transform: translate(-50%, -50%);
	cursor: pointer;
	pointer-events: auto;
	box-shadow: 0 0 0 8px var(--reflection-hotspot-ring);
}
.panel-hotspot.active { background: white; box-shadow: 0 0 0 10px rgba(255,255,255,0.08); }
.annotation-chip.active { border-color: var(--reflection-accent) !important; background: rgba(167, 199, 150, 0.18) !important; }
@media (max-width: 1024px) {
	.reci-stage-body,
	.reci-timeline-panel { padding-top: 4.5rem; }
	.reci-stage-panels,
	.reci-stage-grid { grid-template-columns: 1fr; }
	.reci-timeline-card { padding: 1.5rem; }
}
CSS;

		wp_add_inline_style('reci-media-hub-style', $css);
	}
}
add_action('wp_enqueue_scripts', 'reci_media_hub_enqueue_reflection_stage_styles', 20);
