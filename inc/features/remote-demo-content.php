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

function reci_remote_demo_content_sets(): array {
	$manifest = reci_fetch_remote_demo_manifest();
	$content_sets = $manifest['content_sets'] ?? [];

	if ( ! is_array( $content_sets ) || empty( $content_sets ) ) {
		$content_sets = [ reci_fallback_content_set() ];
	}

	return array_values( $content_sets );
}

function reci_remote_demo_content_set( string $content_set_id ): array {
	foreach ( reci_remote_demo_content_sets() as $content_set ) {
		if ( $content_set_id === (string) ( $content_set['id'] ?? '' ) ) {
			return is_array( $content_set ) ? $content_set : [];
		}
	}

	return [];
}

function reci_fallback_content_set(): array {
	return [
		'id'          => 'starter',
		'label'       => 'Starter Demo Content',
		'description' => 'Bundled starter content using the packaged RECI importer.',
		'groups'      => [
			'reci_demo_taxonomies',
			'reci_demo_images_reflections',
			'reci_demo_images_articles',
			'reci_demo_images_events',
			'reci_demo_images_courses',
			'reci_demo_images_podcasts',
			'reci_demo_images_videos',
			'reci_demo_images_quizzes',
			'reci_demo_images_partners',
			'post',
			'reci_podcast',
			'reci_video',
			'reci_event',
			'reci_reflection',
			'reci_quote',
			'reci_assessment',
			'reci_course',
			'reci_team',
			'reci_testimonial',
			'reci_glossary_term',
			'reci_author',
			'reci_partner',
			'reci_page',
		],
	];
}

function reci_remote_demo_content_groups( string $content_set_id ): array {
	$content_set = reci_remote_demo_content_set( $content_set_id );
	$groups = $content_set['groups'] ?? [];

	if ( ! is_array( $groups ) ) {
		return [];
	}

	return array_values( array_unique( array_filter( array_map( 'sanitize_key', $groups ) ) ) );
}
