<?php
/**
 * Custom theme update checks via GitHub Releases API.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GitHub repo slug for update checks.
 */
function reci_theme_update_repo(): string {
	return (string) apply_filters( 'reci_theme_update_repo', 'edgdmedia/reci' );
}

/**
 * Get the installed theme version.
 */
function reci_get_installed_theme_version(): string {
	return (string) wp_get_theme()->get( 'Version' );
}

/**
 * Fetch the latest release data from GitHub Releases API.
 */
function reci_fetch_latest_github_release(): array {
	$repo = reci_theme_update_repo();
	$url  = 'https://api.github.com/repos/' . $repo . '/releases/latest';

	$response = wp_remote_get( $url, [
		'timeout' => 15,
		'headers' => [
			'Accept' => 'application/vnd.github.v3+json',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return [];
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $code ) {
		return [];
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	return is_array( $data ) ? $data : [];
}

/**
 * Parse version number from a GitHub release tag (e.g. "v0.4.5" -> "0.4.5").
 */
function reci_parse_version_from_tag( string $tag ): string {
	return preg_replace( '/^v/', '', trim( $tag ) );
}

/**
 * Find the zip download URL from release assets.
 */
function reci_find_zip_asset_url( array $release ): string {
	$assets = $release['assets'] ?? [];
	foreach ( $assets as $asset ) {
		$name = $asset['name'] ?? '';
		if ( preg_match( '/\.zip$/i', $name ) ) {
			return (string) ( $asset['browser_download_url'] ?? '' );
		}
	}

	return '';
}

/**
 * Check for theme updates by comparing installed version to latest GitHub release.
 */
function reci_check_for_theme_update( $transient ) {
	if ( ! is_object( $transient ) ) {
		$transient = new stdClass();
	}

	if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
		$transient->response = [];
	}

	$release = reci_fetch_latest_github_release();
	if ( empty( $release['tag_name'] ) ) {
		return $transient;
	}

	$remote_version = reci_parse_version_from_tag( $release['tag_name'] );
	$installed      = reci_get_installed_theme_version();

	if ( version_compare( $remote_version, $installed, '>' ) ) {
		$download_url = reci_find_zip_asset_url( $release );
		$release_url  = $release['html_url'] ?? '';

		$transient->response[ get_template() ] = (object) [
			'theme'        => get_template(),
			'new_version'  => $remote_version,
			'url'          => $release_url,
			'package'      => $download_url,
			'update'       => true,
		];
	}

	return $transient;
}

add_filter( 'pre_set_site_transient_update_themes', 'reci_check_for_theme_update' );

/**
 * Provide theme information for the "View version details" link.
 */
function reci_theme_update_info( $response, $action, $args ) {
	if ( 'theme_information' !== $action || empty( $args->stylesheet ) || get_template() !== $args->stylesheet ) {
		return $response;
	}

	$release = reci_fetch_latest_github_release();
	if ( empty( $release['tag_name'] ) ) {
		return $response;
	}

	$remote_version = reci_parse_version_from_tag( $release['tag_name'] );
	$release_url    = $release['html_url'] ?? '';
	$download_url   = reci_find_zip_asset_url( $release );
	$changelog      = $release['body'] ?? '';

	return (object) [
		'name'          => wp_get_theme()->get( 'Name' ),
		'slug'          => get_template(),
		'version'       => $remote_version,
		'author'        => 'RECI',
		'homepage'      => $release_url,
		'download_link' => $download_url,
		'sections'      => [
			'changelog' => wp_kses_post( $changelog ),
		],
	];
}

add_filter( 'themes_api', 'reci_theme_update_info', 10, 3 );

/**
 * Show a admin notice when a theme update is available.
 */
function reci_theme_update_admin_notice() {
	$release = reci_fetch_latest_github_release();
	if ( empty( $release['tag_name'] ) ) {
		return;
	}

	$remote_version = reci_parse_version_from_tag( $release['tag_name'] );
	$installed      = reci_get_installed_theme_version();

	if ( version_compare( $remote_version, $installed, '<=' ) ) {
		return;
	}

	$release_url = $release['html_url'] ?? '#';
	$theme_name  = wp_get_theme()->get( 'Name' );

	printf(
		'<div class="notice notice-info is-dismissible"><p><strong>%s</strong> — A new version (%s) is available. <a href="%s" target="_blank" rel="noopener">Download from GitHub</a>.</p></div>',
		esc_html( $theme_name ),
		esc_html( $remote_version ),
		esc_url( $release_url )
	);
}

add_action( 'admin_notices', 'reci_theme_update_admin_notice' );
