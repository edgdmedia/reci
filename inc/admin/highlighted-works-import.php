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

// ── Admin screen ─────────────────────────────────────────────────────────────
//
// Both of these were reachable only by calling the function directly, which is
// fine on a machine with shell access and useless on the live site. They need a
// button, like the collaborator profile import already has.

add_action( 'admin_menu', 'reci_register_collaborator_data_page', 12 );
function reci_register_collaborator_data_page(): void {
	add_submenu_page(
		'reci-settings',
		__( 'Collaborator Data', 'reci-media-hub' ),
		__( 'Collaborator Data', 'reci-media-hub' ),
		'manage_options',
		'reci-collaborator-data',
		'reci_render_collaborator_data_page'
	);
}

function reci_render_collaborator_data_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$dataset  = reci_highlighted_works_dataset();
	$profiles = wp_count_posts( 'reci_author' )->publish;

	$stored = 0;
	foreach ( get_posts( [ 'post_type' => 'reci_author', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_reci_author_highlighted_works' ] ) as $id ) {
		++$stored;
	}

	echo '<div class="wrap"><h1>' . esc_html__( 'Collaborator Data', 'reci-media-hub' ) . '</h1>';
	echo '<p class="description">' . esc_html__( 'Run these after importing collaborator profiles. Both are safe to run more than once.', 'reci-media-hub' ) . '</p>';

	echo '<table class="widefat striped" style="max-width:900px;margin-top:16px;"><tbody>';

	printf(
		'<tr><td style="width:60%%;"><strong>%s</strong><p class="description">%s</p></td><td>%s</td></tr>',
		esc_html__( 'Import highlighted works', 'reci-media-hub' ),
		esc_html(
			sprintf(
				/* translators: 1: profiles in the data file, 2: profiles that already have entries, 3: published profiles. */
				__( '%1$d profiles in the data file. %2$d of %3$d published profiles currently hold entries.', 'reci-media-hub' ),
				count( $dataset ),
				$stored,
				(int) $profiles
			)
		),
		sprintf(
			'<a href="%s" class="button button-primary">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( [ 'action' => 'reci_run_hw_import' ], admin_url( 'admin-post.php' ) ), 'reci_run_hw_import' ) ),
			esc_html__( 'Run import', 'reci-media-hub' )
		)
	);

	printf(
		'<tr><td><strong>%s</strong><p class="description">%s</p></td><td>%s</td></tr>',
		esc_html__( 'Publish titled works as Resources', 'reci-media-hub' ),
		esc_html__( 'Creates a draft Resource for every highlighted work that arrived with a usable title. Links without one stay on the profile until someone names them.', 'reci-media-hub' ),
		sprintf(
			'<a href="%s" class="button">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( [ 'action' => 'reci_run_hw_promote' ], admin_url( 'admin-post.php' ) ), 'reci_run_hw_promote' ) ),
			esc_html__( 'Create drafts', 'reci-media-hub' )
		)
	);

	echo '</tbody></table></div>';
}

add_action( 'admin_post_reci_run_hw_import', 'reci_handle_run_hw_import' );
function reci_handle_run_hw_import(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'reci-media-hub' ) );
	}

	check_admin_referer( 'reci_run_hw_import' );

	$result = reci_import_highlighted_works();

	set_transient(
		'reci_hw_notice',
		sprintf(
			/* translators: 1: profiles matched, 2: links, 3: citations, 4: unmatched. */
			__( 'Imported: %1$d profiles matched, %2$d links, %3$d references. %4$d slugs matched no profile.', 'reci-media-hub' ),
			$result['matched'],
			$result['links'],
			$result['citations'],
			count( $result['missing'] )
		),
		60
	);

	wp_safe_redirect( admin_url( 'admin.php?page=reci-collaborator-data' ) );
	exit;
}

add_action( 'admin_post_reci_run_hw_promote', 'reci_handle_run_hw_promote' );
function reci_handle_run_hw_promote(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'reci-media-hub' ) );
	}

	check_admin_referer( 'reci_run_hw_promote' );

	$result = reci_promote_titled_works();

	set_transient(
		'reci_hw_notice',
		sprintf(
			/* translators: 1: drafts created, 2: already promoted. */
			__( 'Created %1$d draft Resources. %2$d were already promoted.', 'reci-media-hub' ),
			$result['created'],
			$result['skipped']
		),
		60
	);

	wp_safe_redirect( admin_url( 'admin.php?page=reci-collaborator-data' ) );
	exit;
}

add_action( 'admin_notices', 'reci_hw_admin_notice' );
function reci_hw_admin_notice(): void {
	$message = get_transient( 'reci_hw_notice' );

	if ( ! $message ) {
		return;
	}

	delete_transient( 'reci_hw_notice' );

	printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( (string) $message ) );
}
