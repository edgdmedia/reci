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

	if ( ! is_array( $entries ) || empty( $entries ) ) {
		echo '<p class="description">' . esc_html__( 'Nothing recorded for this collaborator.', 'reci-media-hub' ) . '</p>';
		return;
	}

	echo '<p class="description">' . esc_html__( 'Links can be published as Resources. Entries without a link are references, and stay on the profile.', 'reci-media-hub' ) . '</p>';
	echo '<table class="widefat striped"><tbody>';

	foreach ( $entries as $index => $entry ) {
		$url       = (string) ( $entry['url'] ?? '' );
		$title     = (string) ( $entry['title'] ?? '' );
		$note      = (string) ( $entry['note'] ?? '' );
		$promoted  = (int) ( $entry['resource_id'] ?? 0 );

		echo '<tr><td>';

		if ( '' !== $url ) {
			printf(
				'<strong>%s</strong><br /><a href="%s" target="_blank" rel="noopener noreferrer" style="font-size:12px;">%s</a>',
				esc_html( '' !== $title ? $title : __( '(no title in the source)', 'reci-media-hub' ) ),
				esc_url( $url ),
				esc_html( $url )
			);
		} else {
			printf( '<em>%s</em>', esc_html__( 'Reference', 'reci-media-hub' ) );
		}

		if ( '' !== $note ) {
			printf( '<br /><span class="description">%s</span>', esc_html( wp_trim_words( $note, 24 ) ) );
		}

		echo '</td><td style="width:190px;vertical-align:middle;text-align:right;">';

		if ( '' === $url ) {
			echo '<span class="description">&mdash;</span>';
		} elseif ( $promoted > 0 && get_post( $promoted ) ) {
			printf(
				'<a href="%s" class="button button-small">%s</a>',
				esc_url( (string) get_edit_post_link( $promoted ) ),
				esc_html__( 'Open Resource', 'reci-media-hub' )
			);
		} else {
			printf(
				'<a href="%s" class="button button-small button-primary">%s</a>',
				esc_url( reci_promote_work_url( (int) $post->ID, (int) $index ) ),
				esc_html__( 'Publish as Resource', 'reci-media-hub' )
			);
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
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
