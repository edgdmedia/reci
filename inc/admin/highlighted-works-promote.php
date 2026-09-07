<?php
/**
 * Promote a highlighted work into a Resource.
 *
 * Deliberately one at a time. Of the 101 imported links only 20 carry a real
 * title, so a bulk conversion would have filled an empty Resources archive with
 * items called "contemporaneity.pitt.edu". This makes promotion an editorial
 * act: staff pick the work, give it a title, and publish it themselves.
 *
 * The created Resource is a draft, and the promote action opens it for editing —
 * that edit is the curation, not an extra step around it.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reci_promote_titled_works' ) ) {
	/**
	 * Publish every highlighted work that arrived with a real title.
	 *
	 * Only these. A link whose anchor text was its own URL has no title a reader
	 * could use, and 81 resources called "contemporaneity.pitt.edu" would be
	 * worse than none — those stay on their profiles until someone names them.
	 *
	 * Idempotent: an entry already carrying a resource_id is skipped.
	 *
	 * @param bool $dry_run Report what would happen without writing.
	 * @return array<string,mixed>
	 */
	function reci_promote_titled_works( bool $dry_run = false ): array {
		$result = [ 'created' => 0, 'skipped' => 0, 'titles' => [] ];

		$profiles = get_posts(
			[
				'post_type'      => 'reci_author',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_reci_author_highlighted_works',
			]
		);

		foreach ( $profiles as $profile_id ) {
			$entries = get_post_meta( $profile_id, '_reci_author_highlighted_works', true );

			if ( ! is_array( $entries ) ) {
				continue;
			}

			$changed = false;

			foreach ( $entries as $index => $entry ) {
				$url   = (string) ( $entry['url'] ?? '' );
				$title = trim( (string) ( $entry['title'] ?? '' ) );

				if ( '' === $url || '' === $title ) {
					continue;
				}

				if ( ! empty( $entry['resource_id'] ) && get_post( (int) $entry['resource_id'] ) ) {
					++$result['skipped'];
					continue;
				}

				$result['titles'][] = $title;

				if ( $dry_run ) {
					++$result['created'];
					continue;
				}

				$resource_id = wp_insert_post(
					[
						'post_type'    => 'reci_document',
						// Draft, not published. These are curated candidates, and a
						// human should see each one before it is public.
						'post_status'  => 'draft',
						'post_title'   => $title,
						'post_excerpt' => sanitize_textarea_field( (string) ( $entry['note'] ?? '' ) ),
					],
					true
				);

				if ( is_wp_error( $resource_id ) || ! $resource_id ) {
					continue;
				}

				update_post_meta( $resource_id, '_reci_submission_content_link', esc_url_raw( $url ) );
				update_post_meta( $resource_id, '_reci_submission_content_type', 'document' );
				update_post_meta( $resource_id, '_reci_submission_source_type', 'link' );
				update_post_meta( $resource_id, '_reci_display_author_profile_id', (int) $profile_id );
				update_post_meta( $resource_id, '_reci_resource_from_profile', (int) $profile_id );

				$entries[ $index ]['resource_id'] = (int) $resource_id;
				$changed = true;
				++$result['created'];
			}

			if ( $changed ) {
				update_post_meta( $profile_id, '_reci_author_highlighted_works', $entries );
			}
		}

		return $result;
	}
}

add_action( 'add_meta_boxes', 'reci_add_highlighted_works_metabox' );
function reci_add_highlighted_works_metabox(): void {
	add_meta_box(
		'reci-highlighted-works',
		__( 'Highlighted Work', 'reci-media-hub' ),
		'reci_render_highlighted_works_metabox',
		'reci_author',
		'normal',
		'default'
	);
}

function reci_render_highlighted_works_metabox( WP_Post $post ): void {
	$entries = get_post_meta( $post->ID, '_reci_author_highlighted_works', true );
	$entries = is_array( $entries ) ? array_values( $entries ) : [];

	wp_nonce_field( 'reci_save_highlighted_works', 'reci_highlighted_works_nonce' );

	echo '<p class="description">' . esc_html__( 'An entry with a link can be published as a Resource. An entry without one is a reference and stays on the profile. Clear a row to delete it.', 'reci-media-hub' ) . '</p>';

	echo '<table class="widefat striped"><thead><tr>';
	printf( '<th style="width:34%%;">%s</th>', esc_html__( 'Title', 'reci-media-hub' ) );
	printf( '<th style="width:34%%;">%s</th>', esc_html__( 'Link', 'reci-media-hub' ) );
	printf( '<th>%s</th>', esc_html__( 'Note or reference', 'reci-media-hub' ) );
	printf( '<th style="width:150px;">%s</th>', esc_html__( 'Resource', 'reci-media-hub' ) );
	echo '</tr></thead><tbody>';

	// Existing rows, then three blank ones. Adding entries without JavaScript
	// keeps this working in any admin, and three at a time is enough for the
	// occasional edit this field gets.
	$rows = array_merge( $entries, array_fill( 0, 3, [] ) );

	foreach ( $rows as $index => $entry ) {
		$url         = (string) ( $entry['url'] ?? '' );
		$title       = (string) ( $entry['title'] ?? '' );
		$note        = (string) ( $entry['note'] ?? '' );
		$resource_id = (int) ( $entry['resource_id'] ?? 0 );

		echo '<tr><td>';
		printf(
			'<input type="text" name="reci_hw[%1$d][title]" value="%2$s" class="widefat" placeholder="%3$s" />',
			(int) $index,
			esc_attr( $title ),
			esc_attr__( 'Optional', 'reci-media-hub' )
		);
		printf( '<input type="hidden" name="reci_hw[%1$d][resource_id]" value="%2$d" />', (int) $index, $resource_id );
		echo '</td><td>';
		printf(
			'<input type="url" name="reci_hw[%1$d][url]" value="%2$s" class="widefat" placeholder="https://" />',
			(int) $index,
			esc_attr( $url )
		);
		echo '</td><td>';
		printf(
			'<textarea name="reci_hw[%1$d][note]" rows="2" class="widefat">%2$s</textarea>',
			(int) $index,
			esc_textarea( $note )
		);
		echo '</td><td style="vertical-align:middle;">';

		if ( '' === $url ) {
			echo '<span class="description">&mdash;</span>';
		} elseif ( $resource_id > 0 && get_post( $resource_id ) ) {
			printf(
				'<a href="%s" class="button button-small">%s</a>',
				esc_url( (string) get_edit_post_link( $resource_id ) ),
				esc_html__( 'Open Resource', 'reci-media-hub' )
			);
		} else {
			printf(
				'<a href="%s" class="button button-small button-primary">%s</a><br /><span class="description" style="font-size:11px;">%s</span>',
				esc_url( reci_promote_work_url( (int) $post->ID, (int) $index ) ),
				esc_html__( 'Publish as Resource', 'reci-media-hub' ),
				esc_html__( 'Save changes first', 'reci-media-hub' )
			);
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

add_action( 'save_post_reci_author', 'reci_save_highlighted_works', 10, 2 );

/**
 * Persist edited highlighted works.
 *
 * Rows are rebuilt from the POST rather than merged, so clearing a row's fields
 * deletes it — which is how the metabox says removal works.
 */
function reci_save_highlighted_works( int $post_id, WP_Post $post ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$nonce = isset( $_POST['reci_highlighted_works_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['reci_highlighted_works_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'reci_save_highlighted_works' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$submitted = isset( $_POST['reci_hw'] ) && is_array( $_POST['reci_hw'] ) ? wp_unslash( $_POST['reci_hw'] ) : [];
	$clean     = [];

	foreach ( $submitted as $row ) {
		$url   = esc_url_raw( (string) ( $row['url'] ?? '' ) );
		$title = sanitize_text_field( (string) ( $row['title'] ?? '' ) );
		$note  = sanitize_textarea_field( (string) ( $row['note'] ?? '' ) );

		// A row with neither a link nor text is an empty slot, not an entry.
		if ( '' === $url && '' === $note ) {
			continue;
		}

		$entry = [ 'url' => $url, 'title' => $title, 'note' => $note ];

		$resource_id = (int) ( $row['resource_id'] ?? 0 );
		if ( $resource_id > 0 && get_post( $resource_id ) ) {
			$entry['resource_id'] = $resource_id;
		}

		$clean[] = $entry;
	}

	update_post_meta( $post_id, '_reci_author_highlighted_works', $clean );
}

if ( ! function_exists( 'reci_promote_work_url' ) ) {
	/**
	 * Nonced URL that promotes one entry.
	 */
	function reci_promote_work_url( int $profile_id, int $index ): string {
		return wp_nonce_url(
			add_query_arg(
				[ 'action' => 'reci_promote_work', 'profile' => $profile_id, 'index' => $index ],
				admin_url( 'admin-post.php' )
			),
			'reci_promote_work_' . $profile_id . '_' . $index
		);
	}
}

add_action( 'admin_post_reci_promote_work', 'reci_handle_promote_work' );

function reci_handle_promote_work(): void {
	$profile_id = isset( $_GET['profile'] ) ? absint( wp_unslash( $_GET['profile'] ) ) : 0;
	$index      = isset( $_GET['index'] ) ? absint( wp_unslash( $_GET['index'] ) ) : 0;
	$back       = (string) get_edit_post_link( $profile_id, '' );

	check_admin_referer( 'reci_promote_work_' . $profile_id . '_' . $index );

	if ( ! current_user_can( 'publish_posts' ) || ! current_user_can( 'edit_post', $profile_id ) ) {
		wp_die( esc_html__( 'You are not allowed to publish resources.', 'reci-media-hub' ) );
	}

	$entries = get_post_meta( $profile_id, '_reci_author_highlighted_works', true );

	if ( ! is_array( $entries ) || ! isset( $entries[ $index ] ) ) {
		wp_safe_redirect( add_query_arg( 'reci_promoted', 'missing', $back ) );
		exit;
	}

	$entry = $entries[ $index ];
	$url   = esc_url_raw( (string) ( $entry['url'] ?? '' ) );

	if ( '' === $url ) {
		wp_safe_redirect( add_query_arg( 'reci_promoted', 'nolink', $back ) );
		exit;
	}

	// Already promoted — send staff to the Resource rather than making a second.
	$existing = (int) ( $entry['resource_id'] ?? 0 );
	if ( $existing > 0 && get_post( $existing ) ) {
		wp_safe_redirect( (string) get_edit_post_link( $existing, '' ) );
		exit;
	}

	$title = sanitize_text_field( (string) ( $entry['title'] ?? '' ) );
	if ( '' === $title ) {
		// No title in the source. Name it after the destination so the draft is
		// identifiable in a list; staff replace it on the screen this opens.
		$title = sprintf(
			/* translators: %s: domain name. */
			__( 'Work at %s', 'reci-media-hub' ),
			function_exists( 'reci_link_domain' ) ? reci_link_domain( $url ) : $url
		);
	}

	$resource_id = wp_insert_post(
		[
			'post_type'    => 'reci_document',
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_excerpt' => sanitize_textarea_field( (string) ( $entry['note'] ?? '' ) ),
			'post_author'  => get_current_user_id(),
		],
		true
	);

	if ( is_wp_error( $resource_id ) || ! $resource_id ) {
		wp_safe_redirect( add_query_arg( 'reci_promoted', 'failed', $back ) );
		exit;
	}

	update_post_meta( $resource_id, '_reci_submission_content_link', $url );
	update_post_meta( $resource_id, '_reci_submission_content_type', 'document' );
	update_post_meta( $resource_id, '_reci_submission_source_type', 'link' );
	// Credit the collaborator whose profile this came from.
	update_post_meta( $resource_id, '_reci_display_author_profile_id', $profile_id );
	update_post_meta( $resource_id, '_reci_resource_from_profile', $profile_id );

	// Record it on the entry so the profile links to the Resource instead of
	// offering to create another.
	$entries[ $index ]['resource_id'] = (int) $resource_id;
	update_post_meta( $profile_id, '_reci_author_highlighted_works', $entries );

	wp_safe_redirect( (string) get_edit_post_link( (int) $resource_id, '' ) );
	exit;
}

add_action( 'admin_notices', 'reci_promote_work_notice' );
function reci_promote_work_notice(): void {
	if ( ! isset( $_GET['reci_promoted'] ) ) {
		return;
	}

	$messages = [
		'missing' => __( 'That highlighted work no longer exists.', 'reci-media-hub' ),
		'nolink'  => __( 'That entry is a reference with no link, so it cannot become a Resource.', 'reci-media-hub' ),
		'failed'  => __( 'The Resource could not be created.', 'reci-media-hub' ),
	];

	$code = sanitize_key( wp_unslash( $_GET['reci_promoted'] ) );

	if ( isset( $messages[ $code ] ) ) {
		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $messages[ $code ] ) );
	}
}
