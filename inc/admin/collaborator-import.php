<?php
/**
 * Collaborator directory import.
 *
 * Imports the scraped CRSP collaboratory directory into `reci_author` profiles.
 *
 * The records are scraped, not signups — they get no user accounts. Each profile
 * stores its contact email so that when the person later registers with the same
 * address, the approval sync claims the existing profile instead of duplicating it.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reci_collaborator_import_dataset_path' ) ) {
	function reci_collaborator_import_dataset_path(): string {
		return get_template_directory() . '/demo-content/imported-collaborators.json';
	}
}

if ( ! function_exists( 'reci_collaborator_import_dataset' ) ) {
	/**
	 * Load the scraped directory.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	function reci_collaborator_import_dataset(): array {
		$path = reci_collaborator_import_dataset_path();
		if ( ! file_exists( $path ) ) {
			return [];
		}

		$decoded = json_decode( (string) file_get_contents( $path ), true );
		if ( ! is_array( $decoded ) || empty( $decoded['profiles'] ) || ! is_array( $decoded['profiles'] ) ) {
			return [];
		}

		return $decoded['profiles'];
	}
}

if ( ! function_exists( 'reci_collaborator_import_split_name' ) ) {
	/**
	 * Split a single display name into first and last parts.
	 *
	 * The source has one `name` string. Most are two clean words, but some carry
	 * honorifics ("Dr. Alexus Brown") or post-nominals ("Aliya Durham, PhD, MSW").
	 * Strip both, then split on the last space. `needs_review` flags anything the
	 * heuristic had to guess at, so it can be checked by hand rather than trusted.
	 *
	 * @return array{first:string,last:string,display:string,needs_review:bool}
	 */
	function reci_collaborator_import_split_name( string $name ): array {
		$display = trim( preg_replace( '/\s+/', ' ', $name ) );

		// Drop post-nominals: everything after the first comma.
		$core = trim( (string) preg_replace( '/,.*$/', '', $display ) );

		// Drop leading honorifics.
		$core = trim( (string) preg_replace( '/^(Dr|Prof|Professor|Mr|Mrs|Ms|Mx|Rev)\.?\s+/i', '', $core ) );

		// Drop trailing post-nominals that were not comma separated.
		$core = trim( (string) preg_replace( '/\s+(PhD|Ph\.?D\.?|MD|MSW|MPH|MPIA|EdD|JD|RN|MBA|II|III|IV|Jr\.?|Sr\.?)$/i', '', $core ) );

		$parts = array_values( array_filter( explode( ' ', $core ) ) );

		if ( count( $parts ) < 2 ) {
			return [
				'first'        => $core,
				'last'         => '',
				'display'      => $display,
				'needs_review' => true,
			];
		}

		$last  = (string) array_pop( $parts );
		$first = implode( ' ', $parts );

		return [
			'first'        => $first,
			'last'         => $last,
			'display'      => $display,
			// Anything that was not a clean two-word name deserves a human look.
			'needs_review' => count( $parts ) > 1 || $core !== $display,
		];
	}
}

if ( ! function_exists( 'reci_collaborator_import_find_profile' ) ) {
	/**
	 * Locate an existing profile for a record, so re-running updates in place.
	 */
	function reci_collaborator_import_find_profile( string $import_slug, string $email ): int {
		foreach ( [ '_reci_author_import_slug' => $import_slug, '_reci_author_email' => $email ] as $meta_key => $meta_value ) {
			if ( '' === $meta_value ) {
				continue;
			}

			$ids = get_posts([
				'post_type'      => 'reci_author',
				'post_status'    => [ 'publish', 'draft', 'pending', 'private' ],
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'meta_key'       => $meta_key,
				'meta_value'     => $meta_value,
				'no_found_rows'  => true,
			]);

			if ( ! empty( $ids ) ) {
				return (int) $ids[0];
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'reci_collaborator_import_sideload' ) ) {
	/**
	 * Sideload a remote file onto a profile and return the attachment ID.
	 *
	 * Used for both headshots and CVs, so it cannot assume an image.
	 */
	function reci_collaborator_import_sideload( string $url, int $post_id, string $description ): int {
		if ( '' === $url ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$temp = download_url( $url, 60 );
		if ( is_wp_error( $temp ) ) {
			return 0;
		}

		$name = basename( (string) wp_parse_url( $url, PHP_URL_PATH ) );
		$name = sanitize_file_name( urldecode( $name ) );
		if ( '' === $name ) {
			$name = 'collaborator-asset';
		}

		$file = [
			'name'     => $name,
			'tmp_name' => $temp,
		];

		$attachment_id = media_handle_sideload( $file, $post_id, $description );

		if ( is_wp_error( $attachment_id ) ) {
			if ( file_exists( $temp ) ) {
				wp_delete_file( $temp );
			}
			return 0;
		}

		return (int) $attachment_id;
	}
}

if ( ! function_exists( 'reci_collaborator_import_assign_terms' ) ) {
	/**
	 * Assign term names to a taxonomy, creating any that do not exist yet.
	 *
	 * @param array<int,string> $names
	 */
	function reci_collaborator_import_assign_terms( int $post_id, string $taxonomy, array $names ): int {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return 0;
		}

		$term_ids = [];
		foreach ( $names as $name ) {
			$name = trim( (string) $name );
			if ( '' === $name ) {
				continue;
			}

			$existing = term_exists( $name, $taxonomy );
			if ( ! $existing ) {
				$existing = wp_insert_term( $name, $taxonomy );
			}

			if ( is_wp_error( $existing ) ) {
				continue;
			}

			$term_ids[] = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
		}

		if ( empty( $term_ids ) ) {
			return 0;
		}

		wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );

		return count( $term_ids );
	}
}

if ( ! function_exists( 'reci_collaborator_import_profile' ) ) {
	/**
	 * Import or update one collaborator profile.
	 *
	 * @param array<string,mixed> $record
	 * @return array<string,mixed>
	 */
	function reci_collaborator_import_profile( array $record ): array {
		$import_slug = sanitize_title( (string) ( $record['slug'] ?? '' ) );
		$email       = sanitize_email( (string) ( $record['email'] ?? '' ) );
		$name        = (string) ( $record['name'] ?? '' );

		if ( '' === $name ) {
			return [ 'status' => 'failed', 'name' => '(unnamed)', 'message' => 'Record has no name.' ];
		}

		$parts = reci_collaborator_import_split_name( $name );
		$bio   = trim( (string) ( $record['bio'] ?? '' ) );
		$role  = trim( (string) ( $record['role_title'] ?? '' ) );
		$site  = esc_url_raw( (string) ( $record['website'] ?? '' ) );

		$socials = array_values( array_filter( array_map( 'esc_url_raw', (array) ( $record['social_links'] ?? [] ) ) ) );
		// The scrape repeats the website as the sole social link on most records;
		// store it once rather than twice.
		$socials = array_values( array_filter( $socials, static fn( $url ) => $url !== $site ) );

		$existing_id = reci_collaborator_import_find_profile( $import_slug, $email );
		$is_update   = $existing_id > 0;

		$postarr = [
			'post_type'    => 'reci_author',
			'post_status'  => 'publish',
			'post_title'   => $parts['display'],
			'post_content' => $bio,
			'post_excerpt' => $bio,
		];

		if ( $is_update ) {
			$postarr['ID'] = $existing_id;
			$profile_id    = wp_update_post( $postarr, true );
		} else {
			$postarr['post_name'] = $import_slug;
			$profile_id           = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $profile_id ) || (int) $profile_id <= 0 ) {
			return [ 'status' => 'failed', 'name' => $name, 'message' => 'Could not save the profile post.' ];
		}

		$profile_id = (int) $profile_id;

		// Pitt affiliation is inferable from the address; the school or unit is not.
		$is_pitt = '' !== $email && (bool) preg_match( '/(^|\.)pitt\.edu$/i', (string) substr( strrchr( $email, '@' ) ?: '', 1 ) );

		$meta = [
			'_reci_author_profile_title'     => $role,
			'_reci_author_email'             => $email,
			'_reci_author_website'           => $site,
			'_reci_author_social_links'      => implode( "\n", $socials ),
			'_reci_author_highlighted_links' => trim( (string) ( $record['highlighted_contributions'] ?? '' ) ),
			'_reci_author_source_url'        => esc_url_raw( (string) ( $record['url'] ?? '' ) ),
			'_reci_author_import_slug'       => $import_slug,
			'_reci_author_import_name_parts' => wp_json_encode( [ 'first' => $parts['first'], 'last' => $parts['last'] ] ),
			'_reci_author_import_review'     => $parts['needs_review'] ? '1' : '0',
			'_reci_author_pitt_affiliated'   => $is_pitt ? 'Yes' : '',
		];

		foreach ( $meta as $key => $value ) {
			update_post_meta( $profile_id, $key, $value );
		}

		$affiliations = reci_collaborator_import_assign_terms( $profile_id, 'reci_affiliation', (array) ( $record['affiliation'] ?? [] ) );
		$expertise    = reci_collaborator_import_assign_terms( $profile_id, 'reci_expertise', (array) ( $record['practice_focus'] ?? [] ) );

		// Assets are the slow part; never re-download one we already hold.
		$image_id = 0;
		if ( ! has_post_thumbnail( $profile_id ) ) {
			$image_id = reci_collaborator_import_sideload( (string) ( $record['image_url'] ?? '' ), $profile_id, $parts['display'] );
			if ( $image_id > 0 ) {
				set_post_thumbnail( $profile_id, $image_id );
			}
		}

		// `cv_url` is only sometimes a CV. Where the source had no document it
		// repeats the website, so treat it as a CV only when it is a real file
		// link that differs from the website — otherwise every re-run would
		// re-download 51 home pages and store them as attachments.
		$cv_url       = (string) ( $record['cv_url'] ?? '' );
		$cv_extension = strtolower( (string) pathinfo( (string) wp_parse_url( urldecode( $cv_url ), PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$is_document  = in_array( $cv_extension, [ 'pdf', 'doc', 'docx', 'rtf', 'odt' ], true );

		$cv_id = absint( get_post_meta( $profile_id, '_reci_author_cv_id', true ) );
		if ( $cv_id <= 0 && $is_document && $cv_url !== $site ) {
			$cv_id = reci_collaborator_import_sideload(
				$cv_url,
				$profile_id,
				sprintf( 'CV — %s', $parts['display'] )
			);
			if ( $cv_id > 0 ) {
				update_post_meta( $profile_id, '_reci_author_cv_id', $cv_id );
			}
		}

		return [
			'status'       => $is_update ? 'updated' : 'created',
			'name'         => $parts['display'],
			'profile_id'   => $profile_id,
			'affiliations' => $affiliations,
			'expertise'    => $expertise,
			'image'        => $image_id > 0,
			'cv'           => $cv_id > 0,
			'needs_review' => $parts['needs_review'],
		];
	}
}

if ( ! function_exists( 'reci_collaborator_import_run' ) ) {
	/**
	 * Import a batch of collaborator profiles.
	 *
	 * 109 records with two sideloads each will not finish in one request, so the
	 * caller walks the dataset in batches and stops when `done` comes back true.
	 *
	 * @return array<string,mixed>
	 */
	function reci_collaborator_import_run( int $batch_size = 10, int $offset = 0 ): array {
		$profiles = reci_collaborator_import_dataset();
		$total    = count( $profiles );

		if ( 0 === $total ) {
			return [ 'done' => true, 'total' => 0, 'processed' => 0, 'results' => [], 'message' => 'No collaborator dataset found.' ];
		}

		if ( $batch_size <= 0 ) {
			$batch_size = $total;
		}

		$slice   = array_slice( $profiles, $offset, $batch_size );
		$results = [];

		foreach ( $slice as $record ) {
			$results[] = reci_collaborator_import_profile( (array) $record );
		}

		$next = $offset + count( $slice );

		return [
			'done'      => $next >= $total,
			'total'     => $total,
			'offset'    => $next,
			'processed' => count( $slice ),
			'results'   => $results,
		];
	}
}
