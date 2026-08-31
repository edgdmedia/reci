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
		'reci-settings',
		__( 'RECI Theme Setup', 'reci-media-hub' ),
		__( 'Setup', 'reci-media-hub' ),
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
		wp_safe_redirect( admin_url( 'admin.php?page=reci-client-setup&plugin_notice=invalid' ) );
		exit;
	}

	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/misc.php';
	include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
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
			wp_safe_redirect( admin_url( 'admin.php?page=reci-client-setup&plugin_notice=install_failed' ) );
			exit;
		}

		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );
		if ( is_wp_error( $result ) || ! $result ) {
			wp_safe_redirect( admin_url( 'admin.php?page=reci-client-setup&plugin_notice=install_failed' ) );
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

	wp_safe_redirect( admin_url( 'admin.php?page=reci-client-setup&plugin_notice=updated' ) );
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
	$group_statuses  = function_exists( 'reci_demo_group_status_map' ) ? reci_demo_group_status_map() : [];
	$required_plugins = array_filter( $plugin_statuses, static fn( $status ) => ( $status['tier'] ?? '' ) === 'required' );
	$recommended_plugins = array_filter( $plugin_statuses, static fn( $status ) => ( $status['tier'] ?? '' ) === 'recommended' );
	$required_total = count( $required_plugins );
	$required_active = count( array_filter( $required_plugins, static fn( $status ) => ! empty( $status['active'] ) ) );
	$page_total = count( $page_statuses );
	$page_ready = count( array_filter( $page_statuses, static fn( $status ) => ! empty( $status['exists'] ) ) );
	$manifest_detected = ! empty( $remote_manifest );
	$progress_percent = $required_total > 0 ? (int) round( ( $required_active / $required_total ) * 100 ) : 0;
	?>
	<script src="https://cdn.tailwindcss.com"></script>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<script>
		tailwind.config = {
			corePlugins: {
				preflight: false,
			}
		}
	</script>
	<style>[x-cloak]{display:none !important;}</style>
	<div class="wrap">
		<div class="max-w-7xl mx-auto mt-5" x-data="{ step: 1, maxStep: 4 }">
			<div class="rounded-3xl overflow-hidden border border-slate-200 bg-white shadow-sm">
				<div class="bg-slate-950 text-white px-8 py-8 md:px-10">
					<div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
						<div class="max-w-3xl">
							<p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-300"><?php esc_html_e( 'RECI Theme Setup', 'reci-media-hub' ); ?></p>
							<h1 class="mt-3 text-3xl md:text-4xl font-semibold tracking-tight"><?php esc_html_e( 'Launch the site with a guided setup flow', 'reci-media-hub' ); ?></h1>
							<p class="mt-4 text-base leading-7 text-slate-300"><?php esc_html_e( 'Install the critical plugins, confirm core routes, and import remote-backed starter content from one place.', 'reci-media-hub' ); ?></p>
						</div>
						<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-[280px]">
							<div class="rounded-2xl bg-white/10 px-4 py-4 backdrop-blur">
								<p class="text-xs uppercase tracking-[0.18em] text-slate-300"><?php esc_html_e( 'Required Plugins', 'reci-media-hub' ); ?></p>
								<p class="mt-2 text-2xl font-semibold"><?php echo esc_html( $required_active . '/' . $required_total ); ?></p>
							</div>
							<div class="rounded-2xl bg-white/10 px-4 py-4 backdrop-blur">
								<p class="text-xs uppercase tracking-[0.18em] text-slate-300"><?php esc_html_e( 'Core Pages', 'reci-media-hub' ); ?></p>
								<p class="mt-2 text-2xl font-semibold"><?php echo esc_html( $page_ready . '/' . $page_total ); ?></p>
							</div>
							<div class="rounded-2xl bg-white/10 px-4 py-4 backdrop-blur">
								<p class="text-xs uppercase tracking-[0.18em] text-slate-300"><?php esc_html_e( 'Remote Manifest', 'reci-media-hub' ); ?></p>
								<p class="mt-2 text-lg font-semibold <?php echo $manifest_detected ? 'text-emerald-300' : 'text-rose-300'; ?>"><?php echo $manifest_detected ? esc_html__( 'Detected', 'reci-media-hub' ) : esc_html__( 'Missing', 'reci-media-hub' ); ?></p>
							</div>
						</div>
					</div>
				</div>

				<div class="flex flex-col xl:flex-row min-h-[820px]">
					<aside class="xl:w-80 border-b xl:border-b-0 xl:border-r border-slate-200 bg-slate-50 px-5 py-6 md:px-6 md:py-8">
						<p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"><?php esc_html_e( 'Setup Progress', 'reci-media-hub' ); ?></p>
						<nav class="mt-6 space-y-3">
							<button type="button" @click="step = 1" :class="step === 1 ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-800'" class="w-full rounded-2xl border p-4 text-left transition">
								<div class="flex items-start gap-3">
									<span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold" :class="step === 1 ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700'">1</span>
									<span>
										<span class="block text-sm font-semibold"><?php esc_html_e( 'Plugins', 'reci-media-hub' ); ?></span>
										<span class="mt-1 block text-xs leading-5" :class="step === 1 ? 'text-slate-300' : 'text-slate-500'"><?php esc_html_e( 'Install and activate the tools needed for editing and launch readiness.', 'reci-media-hub' ); ?></span>
									</span>
								</div>
							</button>
							<button type="button" @click="step = 2" :class="step === 2 ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-800'" class="w-full rounded-2xl border p-4 text-left transition">
								<div class="flex items-start gap-3">
									<span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold" :class="step === 2 ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700'">2</span>
									<span>
										<span class="block text-sm font-semibold"><?php esc_html_e( 'Pages', 'reci-media-hub' ); ?></span>
										<span class="mt-1 block text-xs leading-5" :class="step === 2 ? 'text-slate-300' : 'text-slate-500'"><?php esc_html_e( 'Confirm the main site shell, auth routes, and permalink-sensitive pages.', 'reci-media-hub' ); ?></span>
									</span>
								</div>
							</button>
							<button type="button" @click="step = 3" :class="step === 3 ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-800'" class="w-full rounded-2xl border p-4 text-left transition">
								<div class="flex items-start gap-3">
									<span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold" :class="step === 3 ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700'">3</span>
									<span>
										<span class="block text-sm font-semibold"><?php esc_html_e( 'Demo Import', 'reci-media-hub' ); ?></span>
										<span class="mt-1 block text-xs leading-5" :class="step === 3 ? 'text-slate-300' : 'text-slate-500'"><?php esc_html_e( 'Pull in the remote-backed starter content and media bundles.', 'reci-media-hub' ); ?></span>
									</span>
								</div>
							</button>
							<button type="button" @click="step = 4" :class="step === 4 ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-800'" class="w-full rounded-2xl border p-4 text-left transition">
								<div class="flex items-start gap-3">
									<span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold" :class="step === 4 ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-700'">4</span>
									<span>
										<span class="block text-sm font-semibold"><?php esc_html_e( 'Finish', 'reci-media-hub' ); ?></span>
										<span class="mt-1 block text-xs leading-5" :class="step === 4 ? 'text-slate-300' : 'text-slate-500'"><?php esc_html_e( 'Validate the install and hand off into settings and content editing.', 'reci-media-hub' ); ?></span>
									</span>
								</div>
							</button>
						</nav>
					</aside>

					<div class="flex-1 flex flex-col">
						<div class="p-6 md:p-8 lg:p-10 space-y-8 flex-1">
					<section x-show="step === 1" x-cloak>
						<div class="flex items-start justify-between gap-6 flex-wrap">
							<div class="max-w-2xl">
								<h2 class="text-2xl font-semibold text-slate-950"><?php esc_html_e( 'Install the essentials first', 'reci-media-hub' ); ?></h2>
								<p class="mt-2 text-sm leading-6 text-slate-600"><?php esc_html_e( 'Required plugins support the editorial and media experience. Recommended plugins improve launch readiness but may depend on hosting or security policy.', 'reci-media-hub' ); ?></p>
							</div>
							<div class="w-full sm:w-72">
								<div class="flex items-center justify-between text-sm text-slate-600"><span><?php esc_html_e( 'Required plugin completion', 'reci-media-hub' ); ?></span><span><?php echo esc_html( $progress_percent ); ?>%</span></div>
								<div class="mt-2 h-2 rounded-full bg-slate-200 overflow-hidden"><div class="h-full bg-emerald-500" style="width: <?php echo esc_attr( (string) $progress_percent ); ?>%"></div></div>
							</div>
						</div>
						<div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-6">
							<?php foreach ( $plugin_statuses as $slug => $status ) : ?>
								<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
									<div class="flex items-start justify-between gap-4">
										<div>
											<div class="flex items-center gap-2 flex-wrap">
												<h3 class="text-lg font-semibold text-slate-950"><?php echo esc_html( $status['label'] ); ?></h3>
												<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo ( $status['tier'] ?? '' ) === 'required' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700'; ?>"><?php echo esc_html( ucfirst( $status['tier'] ) ); ?></span>
											</div>
											<p class="mt-2 text-sm leading-6 text-slate-600"><?php echo esc_html( $status['description'] ); ?></p>
										</div>
										<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo ! empty( $status['active'] ) ? 'bg-emerald-100 text-emerald-800' : ( ! empty( $status['installed'] ) ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800' ); ?>"><?php echo ! empty( $status['active'] ) ? esc_html__( 'Active', 'reci-media-hub' ) : ( ! empty( $status['installed'] ) ? esc_html__( 'Ready to activate', 'reci-media-hub' ) : esc_html__( 'Needs install', 'reci-media-hub' ) ); ?></span>
									</div>
									<div class="mt-5">
										<?php if ( ! $status['installed'] ) : ?>
											<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
												<?php wp_nonce_field( 'reci_setup_plugin_action' ); ?>
												<input type="hidden" name="action" value="reci_setup_plugin_action">
												<input type="hidden" name="plugin" value="<?php echo esc_attr( $slug ); ?>">
												<input type="hidden" name="plugin_action" value="install">
												<button type="submit" class="button button-primary"><?php esc_html_e( 'Install plugin', 'reci-media-hub' ); ?></button>
											</form>
										<?php elseif ( ! $status['active'] ) : ?>
											<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
												<?php wp_nonce_field( 'reci_setup_plugin_action' ); ?>
												<input type="hidden" name="action" value="reci_setup_plugin_action">
												<input type="hidden" name="plugin" value="<?php echo esc_attr( $slug ); ?>">
												<input type="hidden" name="plugin_action" value="activate">
												<button type="submit" class="button button-primary"><?php esc_html_e( 'Activate plugin', 'reci-media-hub' ); ?></button>
											</form>
										<?php else : ?>
											<p class="text-sm font-medium text-emerald-700"><?php esc_html_e( 'Installed and active.', 'reci-media-hub' ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</section>

					<section x-show="step === 2" x-cloak>
						<div class="max-w-2xl">
							<h2 class="text-2xl font-semibold text-slate-950"><?php esc_html_e( 'Confirm the shell pages and routes', 'reci-media-hub' ); ?></h2>
							<p class="mt-2 text-sm leading-6 text-slate-600"><?php esc_html_e( 'These routes power the auth, community, and submission flow. They should exist before launch.', 'reci-media-hub' ); ?></p>
						</div>
						<div class="mt-8 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
							<?php foreach ( $page_statuses as $status ) : ?>
								<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
									<div class="flex items-start justify-between gap-4">
										<div>
											<h3 class="text-base font-semibold text-slate-950"><?php echo esc_html( $status['label'] ); ?></h3>
											<p class="mt-2 text-sm text-slate-600"><?php echo ! empty( $status['exists'] ) ? esc_html__( 'Page exists and is ready to use.', 'reci-media-hub' ) : esc_html__( 'This page is missing and must be created before launch.', 'reci-media-hub' ); ?></p>
										</div>
										<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo ! empty( $status['exists'] ) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'; ?>"><?php echo ! empty( $status['exists'] ) ? esc_html__( 'Ready', 'reci-media-hub' ) : esc_html__( 'Missing', 'reci-media-hub' ); ?></span>
									</div>
									<?php if ( ! empty( $status['url'] ) ) : ?>
										<p class="mt-4"><a class="text-sm font-medium text-blue-700 hover:text-blue-900" href="<?php echo esc_url( $status['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open page', 'reci-media-hub' ); ?></a></p>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
							<h3 class="text-base font-semibold text-amber-900"><?php esc_html_e( 'Permalinks', 'reci-media-hub' ); ?></h3>
							<p class="mt-2 text-sm leading-6 text-amber-800"><?php esc_html_e( 'After activation or major route changes, go to Settings -> Permalinks and click Save Changes once if dashboard, collaborator, auth, or community routes do not resolve correctly.', 'reci-media-hub' ); ?></p>
						</div>
					</section>

					<section x-show="step === 3" x-cloak>
						<div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
							<div class="max-w-2xl">
								<h2 class="text-2xl font-semibold text-slate-950"><?php esc_html_e( 'Import remote-backed starter content', 'reci-media-hub' ); ?></h2>
								<p class="mt-2 text-sm leading-6 text-slate-600"><?php esc_html_e( 'This pulls in the packaged RECI content structure and the demo media declared in the selected manifest set.', 'reci-media-hub' ); ?></p>
							</div>
							<div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
								<p class="text-xs uppercase tracking-[0.18em] text-slate-500"><?php esc_html_e( 'Manifest Status', 'reci-media-hub' ); ?></p>
								<p class="mt-2 text-base font-semibold <?php echo $manifest_detected ? 'text-emerald-700' : 'text-rose-700'; ?>"><?php echo $manifest_detected ? esc_html__( 'Remote manifest detected', 'reci-media-hub' ) : esc_html__( 'Remote manifest missing', 'reci-media-hub' ); ?></p>
							</div>
						</div>
						<?php if ( ! empty( $remote_manifest ) && ! empty( $content_sets ) ) : ?>
							<form id="reci-client-setup-import-form" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
								<div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
									<div>
										<label for="reci-client-content-set" class="block text-sm font-medium text-slate-700"><?php esc_html_e( 'Starter content set', 'reci-media-hub' ); ?></label>
										<select id="reci-client-content-set" name="content_set" class="mt-2 w-full max-w-xl rounded-xl border-slate-300 px-4 py-3">
											<?php foreach ( $content_sets as $content_set ) : ?>
												<option value="<?php echo esc_attr( $content_set['id'] ?? '' ); ?>"><?php echo esc_html( $content_set['label'] ?? '' ); ?></option>
											<?php endforeach; ?>
										</select>
										<p class="mt-3 text-sm leading-6 text-slate-600"><?php esc_html_e( 'The importer will use the groups declared by the selected remote manifest set, including remote image bundles where available.', 'reci-media-hub' ); ?></p>
									</div>
									<div class="rounded-2xl bg-slate-50 p-5 border border-slate-200">
										<p class="text-xs uppercase tracking-[0.18em] text-slate-500"><?php esc_html_e( 'Before importing', 'reci-media-hub' ); ?></p>
										<ul class="mt-3 space-y-2 text-sm leading-6 text-slate-700 list-disc pl-5">
											<li><?php esc_html_e( 'Activate required plugins first.', 'reci-media-hub' ); ?></li>
											<li><?php esc_html_e( 'Keep the browser tab open until the import completes.', 'reci-media-hub' ); ?></li>
											<li><?php esc_html_e( 'This process is idempotent and will skip already imported items.', 'reci-media-hub' ); ?></li>
										</ul>
									</div>
								</div>
								<div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
									<table class="min-w-full divide-y divide-slate-200">
										<thead class="bg-slate-50">
											<tr>
												<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500"><input type="checkbox" id="reci-demo-select-all" checked></th>
												<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500"><?php esc_html_e( 'Content Type', 'reci-media-hub' ); ?></th>
												<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500"><?php esc_html_e( 'Status', 'reci-media-hub' ); ?></th>
												<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500"><?php esc_html_e( 'Imported', 'reci-media-hub' ); ?></th>
												<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500"><?php esc_html_e( 'Remaining', 'reci-media-hub' ); ?></th>
											</tr>
										</thead>
										<tbody class="divide-y divide-slate-200 bg-white">
											<?php foreach ( $group_statuses as $group => $status ) : ?>
												<tr>
													<td class="px-4 py-3"><input type="checkbox" name="selected_groups[]" value="<?php echo esc_attr( $group ); ?>" class="reci-demo-group-checkbox" <?php checked( $status['remaining'] > 0 || $status['status'] === 'failed' || $status['status'] === 'partial' || $status['status'] === 'not_started' ); ?>></td>
													<td class="px-4 py-3 text-sm font-medium text-slate-900"><?php echo esc_html( $status['label'] ); ?></td>
													<td class="px-4 py-3 text-sm">
														<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $status['status'] === 'completed' ? 'bg-emerald-100 text-emerald-800' : ( $status['status'] === 'partial' || $status['status'] === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' ); ?>"><?php echo esc_html( ucwords( str_replace( '_', ' ', $status['status'] ) ) ); ?></span>
													</td>
													<td class="px-4 py-3 text-sm text-slate-700"><?php echo esc_html( (string) $status['imported'] ); ?> / <?php echo esc_html( (string) $status['expected'] ); ?></td>
													<td class="px-4 py-3 text-sm text-slate-700"><?php echo esc_html( (string) $status['remaining'] ); ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<div class="mt-6 flex items-center gap-3">
									<button type="submit" class="button button-primary" id="reci-client-start-import"><?php esc_html_e( 'Import Starter Content', 'reci-media-hub' ); ?></button>
								</div>
							</form>

							<div id="reci-client-import-progress" class="mt-6 rounded-2xl border border-slate-200 bg-slate-950 text-white p-6 shadow-sm <?php echo empty( $job_state ) ? 'hidden' : ''; ?>">
								<div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
									<div class="lg:w-[40%]">
										<p class="text-xs uppercase tracking-[0.18em] text-slate-400"><?php esc_html_e( 'Import Status', 'reci-media-hub' ); ?></p>
										<p class="mt-2 text-xl font-semibold" id="reci-client-progress-label"><?php echo esc_html( $job_state['current_label'] ?? 'Idle' ); ?></p>
										<progress id="reci-client-progress-bar" value="<?php echo esc_attr( (string) ( $job_state['percent'] ?? 0 ) ); ?>" max="100" class="mt-4 w-full h-2"></progress>
										<p id="reci-client-progress-meta" class="mt-3 text-sm text-slate-300"><?php echo esc_html( $job_state['progress_text'] ?? '' ); ?></p>
									</div>
									<div class="grid gap-6 lg:grid-cols-2 lg:w-[55%]">
										<div>
											<h4 class="text-sm font-semibold text-white"><?php esc_html_e( 'Activity', 'reci-media-hub' ); ?></h4>
											<ul id="reci-client-activity-log" class="mt-3 max-h-60 overflow-auto space-y-2 text-sm text-slate-300"></ul>
										</div>
										<div>
											<h4 class="text-sm font-semibold text-white"><?php esc_html_e( 'Results', 'reci-media-hub' ); ?></h4>
											<div id="reci-client-completed" class="mt-3 text-sm"></div>
											<div id="reci-client-failed" class="mt-3 text-sm"></div>
											<div id="reci-client-skipped" class="mt-3 text-sm"></div>
										</div>
									</div>
								</div>
							</div>
						<?php else : ?>
							<div class="mt-8 rounded-2xl border border-rose-200 bg-rose-50 p-6">
								<h3 class="text-base font-semibold text-rose-900"><?php esc_html_e( 'Remote demo content is not available yet', 'reci-media-hub' ); ?></h3>
								<p class="mt-2 text-sm leading-6 text-rose-800"><?php esc_html_e( 'The setup screen could not find a valid remote manifest. The theme shell can still be configured, but the starter content import is not ready.', 'reci-media-hub' ); ?></p>
							</div>
						<?php endif; ?>
					</section>

					<section x-show="step === 4" x-cloak>
						<div class="max-w-2xl">
							<h2 class="text-2xl font-semibold text-slate-950"><?php esc_html_e( 'Finish and hand off to content editing', 'reci-media-hub' ); ?></h2>
							<p class="mt-2 text-sm leading-6 text-slate-600"><?php esc_html_e( 'Once plugins are active, pages exist, and the starter content import has completed, the rest of the visual/content tuning can happen in RECI Settings.', 'reci-media-hub' ); ?></p>
						</div>
						<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-5">
							<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
								<h3 class="text-base font-semibold text-slate-950"><?php esc_html_e( 'Next admin actions', 'reci-media-hub' ); ?></h3>
								<ul class="mt-4 list-disc pl-5 space-y-2 text-sm leading-6 text-slate-700">
									<li><?php esc_html_e( 'Visit Settings -> Permalinks and save once after major route changes.', 'reci-media-hub' ); ?></li>
									<li><?php esc_html_e( 'Review RECI Settings for branding, footer, analytics, and content defaults.', 'reci-media-hub' ); ?></li>
									<li><?php esc_html_e( 'Open the live site and validate homepage, auth, and dashboard routes.', 'reci-media-hub' ); ?></li>
								</ul>
							</div>
							<div class="rounded-2xl border border-slate-200 bg-slate-950 text-white p-6 shadow-sm">
								<h3 class="text-base font-semibold"><?php esc_html_e( 'Continue setup', 'reci-media-hub' ); ?></h3>
								<div class="mt-5 flex flex-wrap gap-3">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=reci-settings' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Open RECI Settings', 'reci-media-hub' ); ?></a>
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary"><?php esc_html_e( 'View live site', 'reci-media-hub' ); ?></a>
								</div>
							</div>
						</div>
					</section>
						</div>
						<div class="border-t border-slate-200 bg-slate-50 px-6 py-4 md:px-8 lg:px-10 flex items-center justify-between gap-4">
							<button type="button" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition disabled:opacity-40" @click="step = Math.max(1, step - 1)" :disabled="step === 1"><?php esc_html_e( 'Back', 'reci-media-hub' ); ?></button>
							<div class="flex items-center gap-2 text-sm text-slate-500">
								<span x-text="step"></span>
								<span>/</span>
								<span x-text="maxStep"></span>
							</div>
							<button type="button" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-medium text-white transition disabled:opacity-40" @click="step = Math.min(maxStep, step + 1)" :disabled="step === maxStep"><?php esc_html_e( 'Continue', 'reci-media-hub' ); ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
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
		const selectAll = document.getElementById('reci-demo-select-all');
		const groupCheckboxes = Array.from(form.querySelectorAll('.reci-demo-group-checkbox'));

		if (selectAll) {
			selectAll.addEventListener('change', function() {
				groupCheckboxes.forEach(function(checkbox) {
					checkbox.checked = selectAll.checked;
				});
			});
		}

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
			const selectedGroups = groupCheckboxes.filter(function(checkbox) {
				return checkbox.checked;
			}).map(function(checkbox) {
				return checkbox.value;
			});
			if (!selectedGroups.length) {
				meta.textContent = 'Select at least one content group to import.';
				progressWrap.style.display = 'block';
				label.textContent = 'Nothing selected';
				return;
			}
			startBtn.disabled = true;
			try {
				const payload = await postAction('reci_client_setup_start_import', { content_set: contentSet, selected_groups: JSON.stringify(selectedGroups) });
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

	if ( isset( $_GET['page'] ) && sanitize_key( wp_unslash( $_GET['page'] ) ) === 'reci-client-setup' ) {
		return;
	}

	if ( reci_is_setup_notice_dismissed() || ! reci_theme_setup_needs_attention() ) {
		return;
	}

	$setup_url   = admin_url( 'admin.php?page=reci-client-setup' );
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

	$selected_groups = [];
	if ( isset( $_POST['selected_groups'] ) ) {
		$decoded = json_decode( wp_unslash( $_POST['selected_groups'] ), true );
		if ( is_array( $decoded ) ) {
			$selected_groups = array_values( array_unique( array_map( 'sanitize_key', $decoded ) ) );
		}
	}
	if ( ! empty( $selected_groups ) ) {
		$groups = array_values( array_intersect( $groups, $selected_groups ) );
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
