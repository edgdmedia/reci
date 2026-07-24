<?php
/**
 * Custom theme update checks.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reci_theme_update_manifest_url(): string {
	$default_url = 'https://raw.githubusercontent.com/edgdmedia/reci/main/docs/theme-update-manifest.json';

	return (string) apply_filters( 'reci_theme_update_manifest_url', $default_url );
}

function reci_get_installed_theme_version(): string {
	return (string) wp_get_theme()->get( 'Version' );
}

function reci_fetch_theme_update_manifest(): array {
	$manifest_url = reci_theme_update_manifest_url();
	if ( '' === $manifest_url ) {
		return [];
	}

	$response = wp_remote_get( $manifest_url, [ 'timeout' => 15 ] );
	if ( is_wp_error( $response ) ) {
		return [];
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	return is_array( $data ) ? $data : [];
}

function reci_check_for_theme_update( $transient ) {
	if ( ! is_object( $transient ) ) {
		$transient = new stdClass();
	}

	if ( empty( $transient->response ) ) {
		$transient->response = [];
	}

	$data = reci_fetch_theme_update_manifest();
	if ( ! is_array( $data ) || empty( $data['version'] ) || empty( $data['download_url'] ) ) {
		return $transient;
	}

	$remote_version = $data['version'];
	$installed      = reci_get_installed_theme_version();

	if ( version_compare( $remote_version, $installed, '>' ) ) {
		$transient->response[ get_template() ] = (object) [
			'theme'        => get_template(),
			'new_version'  => $remote_version,
			'url'          => isset( $data['info_url'] ) ? $data['info_url'] : '',
			'package'      => $data['download_url'],
			'update'       => true,
		];
	}

	return $transient;
}

add_filter( 'pre_set_site_transient_update_themes', 'reci_check_for_theme_update' );

function reci_theme_update_info( $response, $action, $args ) {
	if ( 'theme_information' !== $action || empty( $args->stylesheet ) || get_template() !== $args->stylesheet ) {
		return $response;
	}

	$data = reci_fetch_theme_update_manifest();
	if ( ! is_array( $data ) || empty( $data['version'] ) ) {
		return $response;
	}

	return (object) [
		'name'          => wp_get_theme()->get( 'Name' ),
		'slug'          => get_template(),
		'version'       => $data['version'],
		'author'        => isset( $data['author'] ) ? $data['author'] : 'RECI',
		'homepage'      => isset( $data['info_url'] ) ? $data['info_url'] : '',
		'download_link' => isset( $data['download_url'] ) ? $data['download_url'] : '',
		'sections'      => isset( $data['sections'] ) ? (array) $data['sections'] : [],
	];
}

add_filter( 'themes_api', 'reci_theme_update_info', 10, 3 );
