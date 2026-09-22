<?php
/**
 * Moderating shared journal entries.
 *
 * Approving the mirror comment is what publishes an entry. The reflection's
 * owner is notified then, and not on share: the moderation queue absorbs the
 * unreviewed text and the noise of entries that are later rejected.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Build the owner's notification copy.
 *
 * Pure, and deliberately so: reci_create_notification() writes this text into
 * wp_reci_notifications, where the reflection owner reads it. For an anonymous
 * entry the author's name must never enter that row at all — masking it at
 * render time would already be too late.
 *
 * @return array{title:string,message:string}
 */
function reci_journal_owner_notification_text( bool $is_anonymous, string $author_name, string $reflection_title ): array {
	$identity = reci_journal_display_identity( $is_anonymous, false, $author_name );
	$who      = $identity['name'];

	return [
		'title'   => __( 'New shared reflection', 'reci-media-hub' ),
		'message' => sprintf(
			/* translators: 1: author name or "Anonymous", 2: reflection title */
			__( '%1$s shared a reflection on "%2$s".', 'reci-media-hub' ),
			$who,
			$reflection_title
		),
	];
}

/**
 * Publish an entry and tell the reflection's owner.
 */
function reci_approve_journal( int $journal_id ): bool {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal || ! reci_journal_can_transition( (string) $journal['status'], 'approved' ) ) {
		return false;
	}

	$wpdb->update(
		$table,
		[
			'status'    => 'approved',
			'is_shared' => 1,
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d' ],
		[ '%d' ]
	);

	reci_clear_shared_journal_count( (int) $journal['reflection_id'] );

	$reflection = get_post( (int) $journal['reflection_id'] );

	if ( ! $reflection ) {
		return true;
	}

	$author      = get_userdata( (int) $journal['user_id'] );
	$author_name = $author ? (string) $author->display_name : '';

	$copy = reci_journal_owner_notification_text(
		(bool) (int) $journal['is_anonymous'],
		$author_name,
		(string) $reflection->post_title
	);

	reci_create_notification(
		(int) $reflection->post_author,
		'shared_journal_approved',
		$copy['title'],
		$copy['message'],
		get_permalink( $reflection ),
		(int) $reflection->ID
	);

	return true;
}

/**
 * Reject an entry and tell its author. The reflection owner is not told.
 */
function reci_reject_journal( int $journal_id ): bool {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal || ! reci_journal_can_transition( (string) $journal['status'], 'rejected' ) ) {
		return false;
	}

	$wpdb->update(
		$table,
		[
			'status'    => 'rejected',
			'is_shared' => 0,
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d' ],
		[ '%d' ]
	);

	reci_clear_shared_journal_count( (int) $journal['reflection_id'] );

	reci_create_notification(
		(int) $journal['user_id'],
		'shared_journal_rejected',
		__( 'Your shared reflection was not published', 'reci-media-hub' ),
		__( 'A moderator reviewed your shared reflection and did not publish it. It is still in your journal, and still yours.', 'reci-media-hub' ),
		home_url( '/dashboard/journal/' ),
		0
	);

	return true;
}

/**
 * The cached count of approved entries for one reflection.
 */
function reci_get_shared_journal_count( int $reflection_id ): int {
	$cache_key = 'reci_shared_journals_' . $reflection_id;
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return (int) $cached;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'reci_journals';

	$count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(id) FROM {$table} WHERE reflection_id = %d AND status = 'approved'",
			$reflection_id
		)
	);

	set_transient( $cache_key, $count, HOUR_IN_SECONDS );

	return $count;
}

/**
 * Bust the count cache. Called on every status change.
 */
function reci_clear_shared_journal_count( int $reflection_id ): void {
	delete_transient( 'reci_shared_journals_' . $reflection_id );
}

/**
 * Approving the mirror comment is what publishes the entry.
 *
 * Moderators work in the native comment queue, so the journal row follows the
 * comment rather than the other way around.
 */
add_action( 'transition_comment_status', 'reci_journal_follow_comment_status', 10, 3 );
function reci_journal_follow_comment_status( $new_status, $old_status, $comment ): void {
	if ( 'reci_journal' !== $comment->comment_type ) {
		return;
	}

	$journal_id = (int) get_comment_meta( $comment->comment_ID, '_reci_journal_id', true );

	if ( ! $journal_id ) {
		return;
	}

	if ( 'approved' === $new_status ) {
		reci_approve_journal( $journal_id );
		return;
	}

	// Spam, trash and unapproved all mean "not published".
	if ( 'approved' === $old_status ) {
		reci_reject_journal( $journal_id );
	}
}

/**
 * Approve an entry from the journals list table.
 *
 * The mirror comment follows so both moderation surfaces report the same state.
 */
add_action( 'admin_post_reci_journal_approve', 'reci_handle_journal_approve' );
function reci_handle_journal_approve(): void {
	$journal_id = isset( $_GET['journal_id'] ) ? absint( wp_unslash( $_GET['journal_id'] ) ) : 0;

	if ( ! current_user_can( 'reci_moderate_journals' )
		|| ! $journal_id
		|| ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'reci_journal_moderate_' . $journal_id )
	) {
		wp_die( esc_html__( 'You are not allowed to moderate journal entries.', 'reci-media-hub' ) );
	}

	if ( reci_approve_journal( $journal_id ) ) {
		$journal = reci_get_journal_row( $journal_id );
		if ( $journal && (int) $journal['comment_id'] ) {
			wp_set_comment_status( (int) $journal['comment_id'], 'approve' );
		}
	}

	wp_safe_redirect( admin_url( 'admin.php?page=reci-journals&moderated=approved' ) );
	exit;
}

/**
 * Reject an entry from the journals list table.
 */
add_action( 'admin_post_reci_journal_reject', 'reci_handle_journal_reject' );
function reci_handle_journal_reject(): void {
	$journal_id = isset( $_GET['journal_id'] ) ? absint( wp_unslash( $_GET['journal_id'] ) ) : 0;

	if ( ! current_user_can( 'reci_moderate_journals' )
		|| ! $journal_id
		|| ! isset( $_GET['_wpnonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'reci_journal_moderate_' . $journal_id )
	) {
		wp_die( esc_html__( 'You are not allowed to moderate journal entries.', 'reci-media-hub' ) );
	}

	if ( reci_reject_journal( $journal_id ) ) {
		$journal = reci_get_journal_row( $journal_id );
		if ( $journal && (int) $journal['comment_id'] ) {
			// Trash, never spam. A moderator declining to publish testimony is
			// not reporting junk, and the distinction has teeth: Akismet hooks
			// transitions to spam and submits the comment body to its own
			// service, which would send a person's reflection off-site.
			wp_set_comment_status( (int) $journal['comment_id'], 'trash' );
		}
	}

	wp_safe_redirect( admin_url( 'admin.php?page=reci-journals&moderated=rejected' ) );
	exit;
}
