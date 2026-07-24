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
		'classic-editor'                    => 'Classic Editor',
		'ewww-image-optimizer'             => 'EWWW Image Optimizer',
		'really-simple-ssl'                => 'Really Simple Security',
		'all-in-one-wp-security-and-firewall' => 'All-in-One WP Security',
		'wordpress-seo'                    => 'Yoast SEO',
		'wp-super-cache'                   => 'WP Super Cache',
	];
}

function reci_plugin_status_map(): array {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	$statuses = [];
	foreach ( reci_required_plugins() as $slug => $label ) {
		$plugin_file = null;
		foreach ( array_keys( get_plugins() ) as $file ) {
			if ( 0 === strpos( $file, $slug . '/' ) ) {
				$plugin_file = $file;
				break;
			}
		}

		$statuses[ $slug ] = [
			'label'     => $label,
			'installed' => null !== $plugin_file,
			'active'    => $plugin_file ? is_plugin_active( $plugin_file ) : false,
			'file'      => $plugin_file,
		];
	}

	return $statuses;
}

function reci_render_client_setup_page(): void {
	$plugin_statuses = reci_plugin_status_map();
	$remote_manifest = function_exists( 'reci_fetch_remote_demo_manifest' ) ? reci_fetch_remote_demo_manifest() : [];
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'RECI Theme Setup', 'reci-media-hub' ); ?></h1>
		<p><?php esc_html_e( 'Use this guided setup to install required plugins, import demo content, and configure the theme.', 'reci-media-hub' ); ?></p>

		<h2><?php esc_html_e( 'Required Plugins', 'reci-media-hub' ); ?></h2>
		<ul>
			<?php foreach ( $plugin_statuses as $status ) : ?>
				<li>
					<strong><?php echo esc_html( $status['label'] ); ?></strong>
					- <?php echo $status['active'] ? esc_html__( 'Active', 'reci-media-hub' ) : ( $status['installed'] ? esc_html__( 'Installed, inactive', 'reci-media-hub' ) : esc_html__( 'Not installed', 'reci-media-hub' ) ); ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<h2><?php esc_html_e( 'Demo Content', 'reci-media-hub' ); ?></h2>
		<?php if ( ! empty( $remote_manifest ) ) : ?>
			<p><?php esc_html_e( 'Remote demo content manifest detected.', 'reci-media-hub' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'Remote demo content manifest not detected. Local bundled demo content remains available.', 'reci-media-hub' ); ?></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Branding', 'reci-media-hub' ); ?></h2>
		<p><?php esc_html_e( 'Core branding remains configurable through RECI Settings after setup.', 'reci-media-hub' ); ?></p>
	</div>
	<?php
}
