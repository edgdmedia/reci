<?php
/**
 * Community policy: the guideline text and the abuse term list.
 *
 * The term list never blocks a save. It warns the writer and, on the surfaces
 * that are published to other people, flags the entry for a human to read.
 * This site's subject matter means people will legitimately quote the language
 * used against them, and that testimony must survive.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Parse the raw textarea value into a clean term list.
 *
 * Pure. One term per line; blank lines and lines starting with `#` are
 * dropped; everything is lowercased and deduplicated so matching can be a
 * straight comparison later.
 *
 * @return array<int,string>
 */
function reci_parse_abuse_terms( string $raw ): array {
	$raw   = str_replace( "\r\n", "\n", $raw );
	$raw   = str_replace( "\r", "\n", $raw );
	$lines = explode( "\n", $raw );

	$terms = [];

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line || str_starts_with( $line, '#' ) ) {
			continue;
		}

		$terms[] = mb_strtolower( $line, 'UTF-8' );
	}

	return array_values( array_unique( $terms ) );
}

/**
 * Read one key out of the theme settings array.
 */
function reci_policy_setting( string $key ): string {
	$settings = get_option( 'reci_theme_settings', [] );

	if ( ! is_array( $settings ) || ! isset( $settings[ $key ] ) ) {
		return '';
	}

	return (string) $settings[ $key ];
}

/**
 * The configured abuse term list.
 *
 * @return array<int,string>
 */
function reci_get_abuse_terms(): array {
	return reci_parse_abuse_terms( reci_policy_setting( 'abuse_terms' ) );
}

/**
 * The community/abuse guideline body.
 */
function reci_get_community_policy(): string {
	return reci_policy_setting( 'community_policy' );
}

/**
 * The submission guideline body.
 */
function reci_get_submission_guidelines(): string {
	return reci_policy_setting( 'submission_guidelines' );
}

/**
 * Mirror the term list into WordPress's own moderation keyword option.
 *
 * `moderation_keys` holds a comment for review. `disallowed_keys` would reject
 * it outright, which this feature must never do, so that option is left alone.
 *
 * @param array<int,string> $terms
 */
function reci_sync_moderation_keys( array $terms ): void {
	update_option( 'moderation_keys', implode( "\n", $terms ) );
}
