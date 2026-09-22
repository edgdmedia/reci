<?php
/**
 * Sharing a journal entry.
 *
 * The journals table stays the source of truth. Sharing mirrors the entry into
 * wp_comments so WordPress's own moderation queue, threading and
 * moderate_comments capability do the work.
 *
 * For an anonymous entry the mirror carries no identity whatsoever. The link
 * back to the author exists only through the _reci_journal_id meta, and is
 * re-established for holders of reci_view_journal_identity. A display path we
 * failed to think of therefore shows "Anonymous" rather than a real person.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Build the argument array for the mirror comment.
 *
 * Pure, so the anonymity guarantee is testable without a database.
 *
 * @param array  $journal       Row as an array: id, user_id, response, prompt, is_anonymous.
 * @param string $author_name   The author's display name.
 * @param int    $reflection_id The reflection the entry belongs to.
 */
function reci_journal_mirror_comment_args( array $journal, string $author_name, int $reflection_id ): array {
	$is_anonymous = (bool) (int) ( $journal['is_anonymous'] ?? 0 );

	return [
		'comment_post_ID'      => $reflection_id,
		'comment_type'         => 'reci_journal',
		'comment_content'      => (string) ( $journal['response'] ?? '' ),
		// Never 1. Sharing sends an entry for review; only a moderator
		// publishes it.
		'comment_approved'     => 0,
		// The privacy guarantee. An anonymous mirror carries no identity, so
		// nothing downstream can render one by accident.
		'user_id'              => $is_anonymous ? 0 : (int) ( $journal['user_id'] ?? 0 ),
		'comment_author'       => $is_anonymous ? __( 'Anonymous', 'reci-media-hub' ) : $author_name,
		'comment_author_email' => '',
		'comment_author_url'   => '',
		'comment_meta'         => [
			'_reci_journal_id' => (int) ( $journal['id'] ?? 0 ),
			'_reci_anonymous'  => $is_anonymous ? 1 : 0,
			'_reci_prompt'     => (string) ( $journal['prompt'] ?? '' ),
		],
	];
}

/**
 * Fetch one journal row as an array.
 *
 * @return array<string,mixed>|null
 */
function reci_get_journal_row( int $journal_id ): ?array {
	global $wpdb;

	$table = $wpdb->prefix . 'reci_journals';
	$row   = $wpdb->get_row(
		$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $journal_id ),
		ARRAY_A
	);

	return $row ?: null;
}

/**
 * Share an entry: send it for review and create its mirror comment.
 *
 * @return array{status:string,comment_id:int,flagged:array}|WP_Error
 */
function reci_share_journal( int $journal_id, bool $anonymous ) {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal ) {
		return new WP_Error( 'journal_missing', __( 'That entry no longer exists.', 'reci-media-hub' ), [ 'status' => 404 ] );
	}

	$from = (string) $journal['status'];

	if ( ! reci_journal_can_transition( $from, 'pending' ) ) {
		return new WP_Error(
			'journal_bad_transition',
			__( 'That entry cannot be shared from its current state.', 'reci-media-hub' ),
			[ 'status' => 409 ]
		);
	}

	// Matching never blocks the save. It records what a moderator should look
	// at, and nothing more.
	$flagged = reci_match_flagged_terms( (string) $journal['response'], reci_get_abuse_terms() );

	$journal['is_anonymous'] = $anonymous ? 1 : 0;

	$author      = get_userdata( (int) $journal['user_id'] );
	$author_name = $author ? (string) $author->display_name : '';

	$args       = reci_journal_mirror_comment_args( $journal, $author_name, (int) $journal['reflection_id'] );
	$comment_id = wp_insert_comment( wp_slash( $args ) );

	if ( ! $comment_id ) {
		return new WP_Error( 'mirror_failed', __( 'Could not share that entry.', 'reci-media-hub' ), [ 'status' => 500 ] );
	}

	if ( [] !== $flagged ) {
		update_comment_meta( $comment_id, '_reci_flagged_terms', $flagged );
	}

	$wpdb->update(
		$table,
		[
			'status'        => 'pending',
			'is_shared'     => 0,
			'is_anonymous'  => $anonymous ? 1 : 0,
			'shared_at'     => current_time( 'mysql', true ),
			'comment_id'    => $comment_id,
			'flagged_terms' => implode( "\n", $flagged ),
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d', '%d', '%s', '%d', '%s' ],
		[ '%d' ]
	);

	return [
		'status'     => 'pending',
		'comment_id' => (int) $comment_id,
		'flagged'    => $flagged,
	];
}

/**
 * Withdraw an entry: return it to private and remove its mirror.
 */
function reci_unshare_journal( int $journal_id ): bool {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal ) {
		return false;
	}

	if ( ! reci_journal_can_transition( (string) $journal['status'], 'private' ) ) {
		return false;
	}

	$comment_id = (int) $journal['comment_id'];

	if ( $comment_id ) {
		// Force delete rather than trash: a trashed comment is still readable
		// in wp-admin, and the author has just asked for this to stop being
		// visible to other people.
		wp_delete_comment( $comment_id, true );
	}

	$wpdb->update(
		$table,
		[
			'status'     => 'private',
			'is_shared'  => 0,
			'shared_at'  => null,
			'comment_id' => 0,
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d', '%s', '%d' ],
		[ '%d' ]
	);

	return true;
}

/**
 * Keep the journal row consistent if its mirror is deleted from wp-admin.
 *
 * Without this, deleting the comment would leave the entry claiming to be
 * pending or approved forever, with a comment_id pointing at nothing.
 */
add_action( 'deleted_comment', 'reci_journal_handle_deleted_mirror' );
function reci_journal_handle_deleted_mirror( $comment_id ): void {
	global $wpdb;

	$table = $wpdb->prefix . 'reci_journals';

	$wpdb->update(
		$table,
		[
			'status'     => 'private',
			'is_shared'  => 0,
			'shared_at'  => null,
			'comment_id' => 0,
		],
		[ 'comment_id' => (int) $comment_id ],
		[ '%s', '%d', '%s', '%d' ],
		[ '%d' ]
	);
}
