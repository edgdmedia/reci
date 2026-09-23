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

	// Without this the reflection keeps advertising a count that includes the
	// entry just withdrawn, for as long as the transient lives. This is the
	// take-down path, so a stale count here fails in the wrong direction.
	reci_clear_shared_journal_count( (int) $journal['reflection_id'] );

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

	// Read the reflection before the update, so the cached count for it can be
	// cleared afterwards.
	$reflection_id = (int) $wpdb->get_var(
		$wpdb->prepare( "SELECT reflection_id FROM {$table} WHERE comment_id = %d", (int) $comment_id )
	);

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

	if ( $reflection_id ) {
		reci_clear_shared_journal_count( $reflection_id );
	}
}

/**
 * Shape one approved entry for the public reading surface.
 *
 * Pure. Note what is absent: no user id and no moderation metadata, for any
 * caller at any capability. A moderator who needs identity uses the admin
 * queue; this endpoint is what unauthenticated readers receive, and it should
 * not vary by who is asking.
 */
function reci_shape_shared_journal( object $row, string $author_name ): array {
	$is_anonymous = (bool) (int) $row->is_anonymous;

	$identity = reci_journal_display_identity( $is_anonymous, false, $author_name );

	return [
		'id'           => (int) $row->id,
		'author_name'  => $identity['name'],
		'is_anonymous' => $is_anonymous,
		'prompt'       => (string) $row->prompt,
		'response'     => (string) $row->response,
		'created_at'   => gmdate( 'Y-m-d\TH:i:sP', strtotime( (string) $row->created_at ) ),
	];
}

add_action( 'rest_api_init', 'reci_register_shared_journal_routes' );
function reci_register_shared_journal_routes(): void {
	register_rest_route(
		'reci/v1',
		'/reflections/(?P<id>\d+)/shared-journals',
		[
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'reci_get_shared_journals',
			// Approved entries are published writing, readable by anyone who
			// can read the reflection itself.
			'permission_callback' => '__return_true',
		]
	);
}

/**
 * List the approved shared entries for one reflection.
 */
function reci_get_shared_journals( WP_REST_Request $request ): WP_REST_Response {
	global $wpdb;

	$reflection_id = absint( (string) $request->get_param( 'id' ) );
	$page          = max( 1, absint( (string) $request->get_param( 'page' ) ?: '1' ) );
	$per_page      = min( 50, max( 1, absint( (string) $request->get_param( 'per_page' ) ?: '20' ) ) );
	$offset        = ( $page - 1 ) * $per_page;

	$table = $wpdb->prefix . 'reci_journals';

	$total = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(id) FROM {$table} WHERE reflection_id = %d AND status = 'approved'",
			$reflection_id
		)
	);

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table}
			  WHERE reflection_id = %d AND status = 'approved'
			  ORDER BY shared_at DESC, id DESC
			  LIMIT %d OFFSET %d",
			$reflection_id,
			$per_page,
			$offset
		)
	);

	$items = [];

	foreach ( $rows as $row ) {
		$author      = get_userdata( (int) $row->user_id );
		$author_name = $author ? (string) $author->display_name : '';

		$items[] = reci_shape_shared_journal( $row, $author_name );
	}

	return new WP_REST_Response(
		[
			'items'       => $items,
			'total'       => $total,
			'page'        => $page,
			'per_page'    => $per_page,
			'total_pages' => (int) ceil( $total / $per_page ),
		],
		200
	);
}
