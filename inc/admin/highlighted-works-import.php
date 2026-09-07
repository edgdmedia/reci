<?php
/**
 * Import recovered Highlighted Contributions onto collaborator profiles.
 *
 * The source data is docs/content/collaborators/highlighted-works.json, produced
 * by scripts/extract-highlighted-works.py from the saved Pitt HTML. See that
 * script for why the HTML rather than the JSON export is the source of truth.
 *
 * Idempotent: matches on the import slug, and rewrites the structured field each
 * run rather than appending.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reci_highlighted_works_dataset' ) ) {
	/**
	 * Parsed entries, keyed by profile slug.
	 *
	 * @return array<string,array<int,array<string,mixed>>>
	 */
	function reci_highlighted_works_dataset(): array {
		$path = get_template_directory() . '/docs/content/collaborators/highlighted-works.json';

		if ( ! file_exists( $path ) ) {
			return [];
		}

		$decoded = json_decode( (string) file_get_contents( $path ), true );

		if ( ! is_array( $decoded ) || empty( $decoded['profiles'] ) ) {
			return [];
		}

		$by_slug = [];

		foreach ( $decoded['profiles'] as $profile ) {
			if ( ! empty( $profile['slug'] ) && ! empty( $profile['entries'] ) ) {
				$by_slug[ (string) $profile['slug'] ] = (array) $profile['entries'];
			}
		}

		return $by_slug;
	}
}

if ( ! function_exists( 'reci_find_profile_by_import_slug' ) ) {
	/**
	 * The profile a scraped page belongs to.
	 *
	 * Prefers the slug recorded at import time; falls back to the post slug,
	 * which is how profiles created before that field was added are matched.
	 */
	function reci_find_profile_by_import_slug( string $slug ): int {
		$by_meta = get_posts(
			[
				'post_type'      => 'reci_author',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_reci_author_import_slug',
				'meta_value'     => $slug,
			]
		);

		if ( ! empty( $by_meta ) ) {
			return (int) $by_meta[0];
		}

		$post = get_page_by_path( $slug, OBJECT, 'reci_author' );

		return $post instanceof WP_Post ? (int) $post->ID : 0;
	}
}

if ( ! function_exists( 'reci_import_highlighted_works' ) ) {
	/**
	 * Write the recovered entries onto their profiles.
	 *
	 * @return array<string,mixed> Counts, plus the slugs that matched nothing.
	 */
	function reci_import_highlighted_works(): array {
		$result = [ 'matched' => 0, 'missing' => [], 'links' => 0, 'citations' => 0 ];

		foreach ( reci_highlighted_works_dataset() as $slug => $entries ) {
			$profile_id = reci_find_profile_by_import_slug( $slug );

			if ( $profile_id <= 0 ) {
				$result['missing'][] = $slug;
				continue;
			}

			$clean = [];

			foreach ( $entries as $entry ) {
				$url   = esc_url_raw( (string) ( $entry['url'] ?? '' ) );
				$title = sanitize_text_field( (string) ( $entry['title'] ?? '' ) );
				$note  = sanitize_textarea_field( (string) ( $entry['note'] ?? '' ) );

				if ( '' === $url && '' === $note ) {
					continue;
				}

				$clean[] = [ 'url' => $url, 'title' => $title, 'note' => $note ];

				if ( '' !== $url ) {
					++$result['links'];
				} else {
					++$result['citations'];
				}
			}

			update_post_meta( $profile_id, '_reci_author_highlighted_works', $clean );
			++$result['matched'];
		}

		return $result;
	}
}
