<?php
/**
 * Client theme setup screen.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'reci_register_client_setup_page' );
add_action( 'wp_ajax_reci_client_setup_start_import', 'reci_client_setup_start_import' );
add_action( 'admin_post_reci_setup_plugin_action', 'reci_handle_setup_plugin_action' );
add_action( 'admin_notices', 'reci_render_setup_reminder_notice' );
add_action( 'admin_post_reci_dismiss_setup_notice', 'reci_handle_dismiss_setup_notice' );

function reci_register_client_setup_page(): void {
	add_submenu_page(
		'themes.php',
		__( 'RECI Theme Setup', 'reci-media-hub' ),
		__( 'RECI Theme Setup', 'reci-media-hub' ),
		'manage_options',
		'reci-client-setup',
		'reci_render_client_setup_page'
	);
}

function reci_required_plugins(): array {
	return [
		'classic-editor' => [
			'label'       => 'Classic Editor',
			'tier'        => 'required',
			'description' => 'Keeps the editorial experience aligned with the templates and content workflows used by this theme.',
		],
		'ewww-image-optimizer' => [
			'label'       => 'EWWW Image Optimizer',
			'tier'        => 'required',
			'description' => 'Optimizes uploaded images so the media-heavy homepage and archive pages stay fast.',
		],
		'wordpress-seo' => [
			'label'       => 'Yoast SEO',
			'tier'        => 'required',
			'description' => 'Provides SEO metadata and search preview support expected for published site content.',
		],
		'wp-super-cache' => [
			'label'       => 'WP Super Cache',
			'tier'        => 'recommended',
			'description' => 'Improves frontend performance once the site is launched and content is stable.',
		],
		'really-simple-ssl' => [
			'label'       => 'Really Simple Security',
			'tier'        => 'recommended',
			'description' => 'Helps harden SSL and security settings, but may depend on hosting and deployment choices.',
		],
		'all-in-one-wp-security-and-firewall' => [
			'label'       => 'All-in-One WP Security',
			'tier'        => 'recommended',
			'description' => 'Adds security controls and firewall tooling, best reviewed before enabling in production.',
		],
	];
}

function reci_plugin_status_map(): array {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	$statuses = [];
	foreach ( reci_required_plugins() as $slug => $plugin ) {
		$plugin_file = null;
		foreach ( array_keys( get_plugins() ) as $file ) {
			if ( 0 === strpos( $file, $slug . '/' ) ) {
				$plugin_file = $file;
				break;
			}
		}

		$statuses[ $slug ] = [
			'label'     => $plugin['label'],
			'tier'      => $plugin['tier'],
			'description' => $plugin['description'],
			'installed' => null !== $plugin_file,
			'active'    => $plugin_file ? is_plugin_active( $plugin_file ) : false,
			'file'      => $plugin_file,
		];
	}

	return $statuses;
}

function reci_handle_setup_plugin_action(): void {
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_die( esc_html__( 'Unauthorized.', 'reci-media-hub' ) );
	}

	check_admin_referer( 'reci_setup_plugin_action' );

	$slug   = sanitize_key( wp_unslash( $_REQUEST['plugin'] ?? '' ) );
	$action = sanitize_key( wp_unslash( $_REQUEST['plugin_action'] ?? '' ) );
	$plugins = reci_required_plugins();

	if ( '' === $slug || ! isset( $plugins[ $slug ] ) ) {
		wp_safe_redirect( admin_url( 'themes.php?page=reci-client-setup&plugin_notice=invalid' ) );
		exit;
	}

	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/misc.php';
	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	$plugin_file = null;
	foreach ( array_keys( get_plugins() ) as $file ) {
		if ( 0 === strpos( $file, $slug . '/' ) ) {
			$plugin_file = $file;
			break;
		}
	}

	if ( 'install' === $action && null === $plugin_file ) {
		$api = plugins_api( 'plugin_information', [ 'slug' => $slug, 'fields' => [ 'sections' => false ] ] );
		if ( is_wp_error( $api ) ) {
			wp_safe_redirect( admin_url( 'themes.php?page=reci-client-setup&plugin_notice=install_failed' ) );
			exit;
		}

		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );
		if ( is_wp_error( $result ) || ! $result ) {
			wp_safe_redirect( admin_url( 'themes.php?page=reci-client-setup&plugin_notice=install_failed' ) );
			exit;
		}

		foreach ( array_keys( get_plugins() ) as $file ) {
			if ( 0 === strpos( $file, $slug . '/' ) ) {
				$plugin_file = $file;
				break;
			}
		}
	}

	if ( 'activate' === $action && $plugin_file ) {
		activate_plugin( $plugin_file );
	}

	wp_safe_redirect( admin_url( 'themes.php?page=reci-client-setup&plugin_notice=updated' ) );
	exit;
}

function reci_setup_page_status_map(): array {
	$pages = [
		'sign-in'               => __( 'Sign In', 'reci-media-hub' ),
		'sign-up'               => __( 'Sign Up', 'reci-media-hub' ),
		'become-a-collaborator' => __( 'Become a Collaborator', 'reci-media-hub' ),
		'community'             => __( 'Community', 'reci-media-hub' ),
		'submit'                => __( 'Submit Content', 'reci-media-hub' ),
		'dashboard'             => __( 'Dashboard', 'reci-media-hub' ),
	];

	$statuses = [];
	foreach ( $pages as $slug => $label ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		$statuses[ $slug ] = [
			'label'   => $label,
			'exists'  => $page instanceof WP_Post,
			'url'     => $page instanceof WP_Post ? ( get_permalink( $page ) ?: '' ) : '',
		];
	}

	return $statuses;
}

function reci_render_client_setup_page(): void {
	$plugin_statuses = reci_plugin_status_map();
	$page_statuses   = reci_setup_page_status_map();
	$remote_manifest = function_exists( 'reci_fetch_remote_demo_manifest' ) ? reci_fetch_remote_demo_manifest() : [];
	$content_sets    = function_exists( 'reci_remote_demo_content_sets' ) ? reci_remote_demo_content_sets() : [];
	$job_state       = function_exists( 'reci_demo_get_job' ) ? reci_demo_present_job_state( reci_demo_get_job() ) : [];
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'RECI Theme Setup', 'reci-media-hub' ); ?></h1>
		<p><?php esc_html_e( 'Use this guided setup to install required plugins, import demo content, and configure the theme.', 'reci-media-hub' ); ?></p>

		<h2><?php esc_html_e( 'Plugin Setup', 'reci-media-hub' ); ?></h2>
		<p><?php esc_html_e( 'Required plugins support the editorial and content experience. Recommended plugins improve performance or security, but may need environment-specific review.', 'reci-media-hub' ); ?></p>
		<ul>
			<?php foreach ( $plugin_statuses as $slug => $status ) : ?>
				<li>
					<strong><?php echo esc_html( $status['label'] ); ?></strong>
					(<?php echo esc_html( ucfirst( $status['tier'] ) ); ?>)
					- <?php echo $status['active'] ? esc_html__( 'Active', 'reci-media-hub' ) : ( $status['installed'] ? esc_html__( 'Installed, inactive', 'reci-media-hub' ) : esc_html__( 'Not installed', 'reci-media-hub' ) ); ?>
					<?php if ( ! empty( $status['description'] ) ) : ?>
						<br><span class="description"><?php echo esc_html( $status['description'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! $status['installed'] ) : ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block; margin-left:8px;">
							<?php wp_nonce_field( 'reci_setup_plugin_action' ); ?>
							<input type="hidden" name="action" value="reci_setup_plugin_action">
							<input type="hidden" name="plugin" value="<?php echo esc_attr( $slug ); ?>">
							<input type="hidden" name="plugin_action" value="install">
							<button type="submit" class="button button-secondary"><?php esc_html_e( 'Install', 'reci-media-hub' ); ?></button>
						</form>
					<?php elseif ( ! $status['active'] ) : ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block; margin-left:8px;">
							<?php wp_nonce_field( 'reci_setup_plugin_action' ); ?>
							<input type="hidden" name="action" value="reci_setup_plugin_action">
							<input type="hidden" name="plugin" value="<?php echo esc_attr( $slug ); ?>">
							<input type="hidden" name="plugin_action" value="activate">
							<button type="submit" class="button button-secondary"><?php esc_html_e( 'Activate', 'reci-media-hub' ); ?></button>
						</form>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<h2><?php esc_html_e( 'Page & Route Checklist', 'reci-media-hub' ); ?></h2>
		<ul>
			<?php foreach ( $page_statuses as $status ) : ?>
				<li>
					<strong><?php echo esc_html( $status['label'] ); ?></strong>
					- <?php echo $status['exists'] ? esc_html__( 'Page exists', 'reci-media-hub' ) : esc_html__( 'Missing', 'reci-media-hub' ); ?>
					<?php if ( ! empty( $status['url'] ) ) : ?>
						( <a href="<?php echo esc_url( $status['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $status['url'] ); ?></a> )
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="notice notice-warning inline-block" style="padding:12px 16px; margin:12px 0 24px;">
			<p style="margin:0;"><strong><?php esc_html_e( 'Permalinks:', 'reci-media-hub' ); ?></strong> <?php esc_html_e( 'After activation or major route changes, visit Settings > Permalinks and click Save Changes once if dashboard, collaborator, auth, or community routes do not resolve correctly.', 'reci-media-hub' ); ?></p>
		</div>

		<h2><?php esc_html_e( 'Demo Content', 'reci-media-hub' ); ?></h2>
		<?php if ( ! empty( $remote_manifest ) ) : ?>
			<p><?php esc_html_e( 'Remote demo content manifest detected.', 'reci-media-hub' ); ?></p>
			<?php if ( ! empty( $content_sets ) ) : ?>
				<form id="reci-client-setup-import-form">
					<p>
						<label for="reci-client-content-set"><strong><?php esc_html_e( 'Starter content set', 'reci-media-hub' ); ?></strong></label>
						<select id="reci-client-content-set" name="content_set" class="regular-text">
							<?php foreach ( $content_sets as $content_set ) : ?>
								<option value="<?php echo esc_attr( $content_set['id'] ?? '' ); ?>"><?php echo esc_html( $content_set['label'] ?? '' ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p class="description"><?php esc_html_e( 'This will run the packaged RECI importer using the groups declared by the selected remote manifest set.', 'reci-media-hub' ); ?></p>
					<p>
						<button type="submit" class="button button-primary" id="reci-client-start-import"><?php esc_html_e( 'Import Starter Content', 'reci-media-hub' ); ?></button>
					</p>
				</form>

				<div id="reci-client-import-progress" style="margin-top:20px; border:1px solid #dcdcde; border-radius:6px; padding:16px; background:#fff; <?php echo empty( $job_state ) ? 'display:none;' : ''; ?>">
					<h3 style="margin-top:0;"><?php esc_html_e( 'Import Progress', 'reci-media-hub' ); ?></h3>
					<p><strong id="reci-client-progress-label"><?php echo esc_html( $job_state['current_label'] ?? 'Idle' ); ?></strong></p>
					<progress id="reci-client-progress-bar" value="<?php echo esc_attr( (string) ( $job_state['percent'] ?? 0 ) ); ?>" max="100" style="width:100%;height:18px;"></progress>
					<p id="reci-client-progress-meta" style="margin:8px 0 16px;"><?php echo esc_html( $job_state['progress_text'] ?? '' ); ?></p>
					<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
						<div>
							<h4 style="margin:0 0 8px;"><?php esc_html_e( 'Activity', 'reci-media-hub' ); ?></h4>
							<ul id="reci-client-activity-log" style="max-height:220px; overflow:auto; margin:0; padding-left:18px;"></ul>
						</div>
						<div>
							<h4 style="margin:0 0 8px;"><?php esc_html_e( 'Results', 'reci-media-hub' ); ?></h4>
							<div id="reci-client-completed"></div>
							<div id="reci-client-failed" style="margin-top:12px;"></div>
							<div id="reci-client-skipped" style="margin-top:12px;"></div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Remote demo content manifest not detected. Local bundled demo content remains available.', 'reci-media-hub' ); ?></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Branding', 'reci-media-hub' ); ?></h2>
		<p><?php esc_html_e( 'Core branding remains configurable through RECI Settings after setup.', 'reci-media-hub' ); ?></p>
	</div>
	<script>
	(function() {
		const form = document.getElementById('reci-client-setup-import-form');
		if (!form) return;

		const startBtn = document.getElementById('reci-client-start-import');
		const progressWrap = document.getElementById('reci-client-import-progress');
		const label = document.getElementById('reci-client-progress-label');
		const bar = document.getElementById('reci-client-progress-bar');
		const meta = document.getElementById('reci-client-progress-meta');
		const activity = document.getElementById('reci-client-activity-log');
		const completed = document.getElementById('reci-client-completed');
		const failed = document.getElementById('reci-client-failed');
		const skipped = document.getElementById('reci-client-skipped');

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
			startBtn.disabled = !!state.running;
			startBtn.textContent = state.running ? 'Import Running…' : 'Import Starter Content';
		}

		async function postAction(action, extra) {
			const body = new URLSearchParams();
			body.set('action', action);
			body.set('nonce', config.nonce);
			Object.entries(extra || {}).forEach(function(entry) {
				body.set(entry[0], entry[1]);
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
			const contentSet = form.querySelector('[name="content_set"]').value;
			startBtn.disabled = true;
			try {
				const payload = await postAction('reci_client_setup_start_import', { content_set: contentSet });
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
			runJob().catch(function(error) {
				label.textContent = 'Import Error';
				meta.textContent = error.message || 'Unknown error.';
				startBtn.disabled = false;
				startBtn.textContent = 'Import Starter Content';
			});
		} else if (config.initialState && Object.keys(config.initialState).length) {
			renderState(config.initialState);
		}
	})();
	</script>
	<?php
}

function reci_is_setup_notice_dismissed(): bool {
	return (bool) get_option( 'reci_setup_notice_dismissed', false );
}

function reci_theme_setup_needs_attention(): bool {
	$required_paths = [
		'sign-in',
		'sign-up',
		'become-a-collaborator',
		'community',
		'submit',
		'dashboard',
		'articles',
		'learn',
		'framework',
		'glossary',
		'privacy-policy',
		'terms-of-use',
		'cookies',
	];

	foreach ( $required_paths as $path ) {
		if ( ! ( get_page_by_path( $path, OBJECT, 'page' ) instanceof WP_Post ) ) {
			return true;
		}
	}

	if ( (int) get_option( 'page_on_front' ) <= 0 || (int) get_option( 'page_for_posts' ) <= 0 ) {
		return true;
	}

	foreach ( reci_plugin_status_map() as $status ) {
		if ( ( $status['tier'] ?? '' ) === 'required' && empty( $status['active'] ) ) {
			return true;
		}
	}

	return false;
}

function reci_render_setup_reminder_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( reci_is_setup_notice_dismissed() || ! reci_theme_setup_needs_attention() ) {
		return;
	}

	$setup_url   = admin_url( 'themes.php?page=reci-client-setup' );
	$dismiss_url = wp_nonce_url( admin_url( 'admin-post.php?action=reci_dismiss_setup_notice' ), 'reci_dismiss_setup_notice' );
	?>
	<div class="notice notice-warning is-dismissible">
		<p><strong><?php esc_html_e( 'RECI Theme Setup is not complete.', 'reci-media-hub' ); ?></strong> <?php esc_html_e( 'Finish plugin activation, page setup, and starter content import before launch.', 'reci-media-hub' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $setup_url ); ?>"><?php esc_html_e( 'Open RECI Theme Setup', 'reci-media-hub' ); ?></a>
			<a class="button button-secondary" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss', 'reci-media-hub' ); ?></a>
		</p>
	</div>
	<?php
}

function reci_handle_dismiss_setup_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized.', 'reci-media-hub' ) );
	}

	check_admin_referer( 'reci_dismiss_setup_notice' );
	update_option( 'reci_setup_notice_dismissed', 1, false );
	wp_safe_redirect( wp_get_referer() ?: admin_url() );
	exit;
}

function reci_client_setup_start_import(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'Unauthorized.' ], 403 );
	}

	check_ajax_referer( 'reci_demo_action', 'nonce' );

	$content_set = isset( $_POST['content_set'] ) ? sanitize_key( wp_unslash( $_POST['content_set'] ) ) : '';
	if ( '' === $content_set ) {
		wp_send_json_error( [ 'message' => 'Select a content set.' ], 400 );
	}

	$groups = function_exists( 'reci_remote_demo_content_groups' ) ? reci_remote_demo_content_groups( $content_set ) : [];
	if ( empty( $groups ) ) {
		wp_send_json_error( [ 'message' => 'The selected content set does not declare any import groups.' ], 400 );
	}

	$job = [
		'id'            => wp_generate_uuid4(),
		'selected'      => $groups,
		'queue'         => reci_demo_build_import_queue( $groups ),
		'cursor'        => 0,
		'completed'     => [],
		'failed'        => [],
		'skipped'       => [],
		'activity'      => [],
		'current_label' => 'Preparing import queue…',
		'running'       => true,
		'finished'      => false,
		'started'       => time(),
		'updated'       => time(),
	];
	$job = reci_demo_append_activity( $job, 'Remote starter import job created.' );

	if ( empty( $job['queue'] ) ) {
		$job['running']       = false;
		$job['finished']      = true;
		$job['current_label'] = 'Nothing to import';
		$job = reci_demo_append_activity( $job, 'No import steps were generated.' );
	}

	reci_demo_set_job( $job );
	wp_send_json_success( reci_demo_present_job_state( $job ) );
}
