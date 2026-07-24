<?php
/**
 * Notifications helpers and workflow hooks.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reci_notifications_table_name(): string {
	global $wpdb;

	return $wpdb->prefix . 'reci_notifications';
}

function reci_create_notification( int $user_id, string $type, string $title, string $message, string $target_url = '', int $related_post_id = 0 ): int {
	global $wpdb;

	$inserted = $wpdb->insert(
		reci_notifications_table_name(),
		[
			'user_id'         => $user_id,
			'type'            => sanitize_key( $type ),
			'title'           => sanitize_text_field( $title ),
			'message'         => sanitize_textarea_field( $message ),
			'target_url'      => esc_url_raw( $target_url ),
			'related_post_id' => $related_post_id,
			'is_read'         => 0,
			'email_sent'      => 0,
			'created_at'      => current_time( 'mysql' ),
		],
		[ '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s' ]
	);

	return $inserted ? (int) $wpdb->insert_id : 0;
}

function reci_get_user_notifications( int $user_id, int $limit = 20, bool $unread_only = false ): array {
	global $wpdb;

	$table = reci_notifications_table_name();
	$sql   = "SELECT * FROM {$table} WHERE user_id = %d";
	$args  = [ $user_id ];

	if ( $unread_only ) {
		$sql .= ' AND is_read = 0';
	}

	$sql .= ' ORDER BY created_at DESC LIMIT %d';
	$args[] = $limit;

	return $wpdb->get_results( $wpdb->prepare( $sql, ...$args ), ARRAY_A ) ?: [];
}

function reci_mark_notification_read( int $notification_id, int $user_id ): bool {
	global $wpdb;

	$updated = $wpdb->update(
		reci_notifications_table_name(),
		[ 'is_read' => 1 ],
		[ 'id' => $notification_id, 'user_id' => $user_id ],
		[ '%d' ],
		[ '%d', '%d' ]
	);

	return false !== $updated;
}

function reci_get_staff_notification_recipients(): array {
	$users = get_users(
		[
			'role__in' => [ 'administrator', 'editor' ],
			'fields'   => [ 'ID', 'user_email', 'display_name' ],
		]
	);

	return is_array( $users ) ? $users : [];
}

function reci_send_staff_submission_notification( int $post_id ): void {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$edit_link = get_edit_post_link( $post_id ) ?: admin_url( 'post.php?post=' . $post_id . '&action=edit' );
	$title     = get_the_title( $post ) ?: '(untitled)';
	$subject   = sprintf( 'New content submission: %s', $title );
	$message   = sprintf(
		"A new content submission is ready for review.\n\nTitle: %s\nType: %s\nStatus: %s\n\nReview: %s",
		$title,
		$post->post_type,
		$post->post_status,
		$edit_link
	);

	foreach ( reci_get_staff_notification_recipients() as $user ) {
		if ( ! empty( $user->user_email ) ) {
			wp_mail( $user->user_email, $subject, $message );
		}

		if ( ! empty( $user->ID ) ) {
			reci_create_notification(
				(int) $user->ID,
				'staff_submission',
				'New content submission',
				sprintf( '"%s" is ready for review.', $title ),
				$edit_link,
				$post_id
			);
		}
	}
}

function reci_get_interested_user_ids_for_post( int $post_id ): array {
	$taxonomy_map = [
		'reci_topic'           => 'reci_followed_topics',
		'reci_sphere'          => 'reci_followed_spheres',
		'reci_practice_focus'  => 'reci_followed_practice_focus',
		'reci_target_audience' => 'reci_followed_target_audience',
	];

	$matching_user_ids = [];

	foreach ( $taxonomy_map as $taxonomy => $meta_key ) {
		$term_ids = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
		if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) {
			continue;
		}

		$users = get_users(
			[
				'meta_key' => $meta_key,
				'fields'   => 'ID',
			]
		);

		foreach ( $users as $user_id ) {
			if ( ! function_exists( 'reci_get_user_followed_term_ids' ) ) {
				continue;
			}

			$saved = reci_get_user_followed_term_ids( (int) $user_id, $meta_key );
			if ( array_intersect( $saved, array_map( 'absint', $term_ids ) ) ) {
				$matching_user_ids[] = (int) $user_id;
			}
		}
	}

	return array_values( array_unique( $matching_user_ids ) );
}

function reci_notify_interested_users_about_post( int $post_id ): void {
	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return;
	}

	$user_ids = reci_get_interested_user_ids_for_post( $post_id );
	if ( empty( $user_ids ) ) {
		return;
	}

	$title = get_the_title( $post ) ?: '(untitled)';
	$url   = get_permalink( $post ) ?: '';

	foreach ( $user_ids as $user_id ) {
		reci_create_notification(
			$user_id,
			'personalized_content',
			'New content for you',
			sprintf( '"%s" matches your saved interests.', $title ),
			$url,
			$post_id
		);

		if ( '1' === get_user_meta( $user_id, 'reci_notify_personalized_content', true ) ) {
			$user = get_user_by( 'id', $user_id );
			if ( $user && ! empty( $user->user_email ) ) {
				wp_mail(
					$user->user_email,
					'New RECI content matching your interests',
					sprintf( "A new piece of content matches your interests:\n\n%s\n\nView it here: %s", $title, $url )
				);
			}
		}
	}
}

function reci_get_submission_submitter_user_id( int $post_id ): int {
	return absint( get_post_meta( $post_id, '_reci_submission_submitter_user_id', true ) );
}

function reci_notify_submitter_about_approval( int $post_id ): void {
	$user_id = reci_get_submission_submitter_user_id( $post_id );
	if ( ! $user_id ) {
		return;
	}

	$user = get_user_by( 'id', $user_id );
	$post = get_post( $post_id );
	if ( ! $user || ! $post ) {
		return;
	}

	$title = get_the_title( $post ) ?: '(untitled)';
	$url   = get_permalink( $post ) ?: '';
	$message = sprintf( 'Your submission "%s" has been published.', $title );

	reci_create_notification( $user_id, 'submission_approved', 'Submission approved', $message, $url, $post_id );

	if ( '1' === get_user_meta( $user_id, 'reci_notify_submission_approved', true ) && ! empty( $user->user_email ) ) {
		wp_mail( $user->user_email, 'RECI submission update', $message . "\n\n" . $url );
	}
}

add_action( 'transition_post_status', 'reci_maybe_notify_users_about_published_content', 10, 3 );
function reci_maybe_notify_users_about_published_content( string $new_status, string $old_status, WP_Post $post ): void {
	if ( 'publish' !== $new_status || 'publish' === $old_status ) {
		return;
	}

	$allowed_post_types = [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_course', 'reci_reflection' ];
	if ( ! in_array( $post->post_type, $allowed_post_types, true ) ) {
		return;
	}

	reci_notify_interested_users_about_post( (int) $post->ID );
}

add_action( 'transition_post_status', 'reci_maybe_notify_submitter_about_approval', 10, 3 );
function reci_maybe_notify_submitter_about_approval( string $new_status, string $old_status, WP_Post $post ): void {
	if ( $new_status === $old_status || 'publish' !== $new_status ) {
		return;
	}

	if ( ! function_exists( 'reci_media_hub_submission_supported_post_types' ) ) {
		return;
	}

	$allowed_types = reci_media_hub_submission_supported_post_types();
	if ( ! in_array( $post->post_type, $allowed_types, true ) ) {
		return;
	}

	reci_notify_submitter_about_approval( (int) $post->ID );
}
