<?php
/**
 * Remote demo content manifest helpers.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reci_remote_demo_manifest_url(): string {
	$default_url = 'https://raw.githubusercontent.com/edgdmedia/reci/main/docs/demo-content-manifest.json';

	return (string) apply_filters( 'reci_remote_demo_manifest_url', $default_url );
}

function reci_fetch_remote_demo_manifest(): array {
	$url = reci_remote_demo_manifest_url();
	if ( '' === $url ) {
		return [];
	}

	$response = wp_remote_get( $url, [ 'timeout' => 20 ] );
	if ( is_wp_error( $response ) ) {
		return [];
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	return is_array( $data ) ? $data : [];
}
