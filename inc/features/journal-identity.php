<?php
/**
 * The journal anonymity gate.
 *
 * Anonymous means hidden from readers AND from the reflection's own author —
 * who is normally staff, and who normally holds moderate_comments through the
 * editor role. So the gate is a capability, never a "is this person staff"
 * check.
 *
 * The mirror comment for an anonymous entry stores no identity at all (see
 * inc/features/journal-sharing.php). Identity is added back here, for holders
 * of reci_view_journal_identity only. A display path that forgets to call this
 * function therefore shows nothing — which is the safe direction to fail in.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Decide the byline for one entry.
 *
 * Pure: every input is an argument, so the whole decision table is testable.
 *
 * @param bool   $is_anonymous            Did the author ask for anonymity?
 * @param bool   $viewer_can_see_identity Does the viewer hold reci_view_journal_identity?
 * @param string $real_name               The author's display name, '' if unknown.
 *
 * @return array{name:string,is_masked:bool}
 */
function reci_journal_display_identity( bool $is_anonymous, bool $viewer_can_see_identity, string $real_name ): array {
	$placeholder = __( 'Anonymous', 'reci-media-hub' );
	$real_name   = trim( $real_name );

	// No name to show — a deleted account, or meta that never existed. Fall
	// back rather than rendering an empty byline.
	if ( '' === $real_name ) {
		return [
			'name'      => $placeholder,
			'is_masked' => true,
		];
	}

	if ( $is_anonymous && ! $viewer_can_see_identity ) {
		return [
			'name'      => $placeholder,
			'is_masked' => true,
		];
	}

	return [
		'name'      => $real_name,
		'is_masked' => false,
	];
}

/**
 * May this user see through anonymity?
 *
 * @param int|null $user_id Defaults to the current user.
 */
function reci_can_see_journal_identity( ?int $user_id = null ): bool {
	if ( null === $user_id ) {
		return current_user_can( 'reci_view_journal_identity' );
	}

	return user_can( $user_id, 'reci_view_journal_identity' );
}

/**
 * Resolve the byline for a journal row, for the current viewer.
 *
 * `user_id` in the return is 0 whenever the byline is masked, so a caller that
 * passes the array straight into a response cannot leak the author by
 * accident.
 *
 * @return array{name:string,is_masked:bool,user_id:int}
 */
function reci_journal_author_for_display( int $journal_id ): array {
	global $wpdb;

	$table = $wpdb->prefix . 'reci_journals';

	$row = $wpdb->get_row(
		$wpdb->prepare( "SELECT user_id, is_anonymous FROM {$table} WHERE id = %d", $journal_id )
	);

	if ( ! $row ) {
		return [
			'name'      => __( 'Anonymous', 'reci-media-hub' ),
			'is_masked' => true,
			'user_id'   => 0,
		];
	}

	$author    = get_userdata( (int) $row->user_id );
	$real_name = $author ? (string) $author->display_name : '';

	$identity = reci_journal_display_identity(
		(bool) (int) $row->is_anonymous,
		reci_can_see_journal_identity(),
		$real_name
	);

	return [
		'name'      => $identity['name'],
		'is_masked' => $identity['is_masked'],
		'user_id'   => $identity['is_masked'] ? 0 : (int) $row->user_id,
	];
}

/**
 * Remove every author-identifying field from a prepared comment payload.
 *
 * Pure, so the field list is explicit and can be reviewed against what the
 * REST controller actually emits.
 */
function reci_strip_comment_identity( array $data ): array {
	$data['author']             = 0;
	$data['author_name']        = __( 'Anonymous', 'reci-media-hub' );
	$data['author_url']         = '';
	$data['author_avatar_urls'] = [];

	// These two have no anonymised form — an email or an IP address is
	// identifying however it is rendered — so they are dropped outright.
	unset( $data['author_email'], $data['author_ip'] );

	return $data;
}

/**
 * Close the /wp/v2/comments side door.
 *
 * Without this, core would serve a mirror comment's author fields directly,
 * bypassing every masking decision made elsewhere.
 */
add_filter( 'rest_prepare_comment', 'reci_rest_mask_journal_comment', 10, 3 );
function reci_rest_mask_journal_comment( $response, $comment, $request ) {
	if ( 'reci_journal' !== $comment->comment_type ) {
		return $response;
	}

	if ( (int) get_comment_meta( $comment->comment_ID, '_reci_anonymous', true ) !== 1 ) {
		return $response;
	}

	if ( reci_can_see_journal_identity() ) {
		return $response;
	}

	$response->set_data( reci_strip_comment_identity( (array) $response->get_data() ) );

	return $response;
}

/**
 * Mask the byline wherever WordPress renders a comment author.
 *
 * The mirror stores no identity for an anonymous entry, so this is a second
 * line of defence rather than the primary one — it matters for the cases where
 * something has reconstructed a name from elsewhere.
 */
add_filter( 'get_comment_author', 'reci_mask_journal_comment_author', 10, 3 );
function reci_mask_journal_comment_author( $author, $comment_id, $comment ) {
	if ( ! $comment || 'reci_journal' !== $comment->comment_type ) {
		return $author;
	}

	if ( (int) get_comment_meta( $comment_id, '_reci_anonymous', true ) !== 1 ) {
		return $author;
	}

	if ( reci_can_see_journal_identity() ) {
		return $author;
	}

	return __( 'Anonymous', 'reci-media-hub' );
}

/**
 * An anonymous entry must not carry a profile link.
 */
add_filter( 'get_comment_author_url', 'reci_mask_journal_comment_author_url', 10, 3 );
function reci_mask_journal_comment_author_url( $url, $comment_id, $comment ) {
	if ( ! $comment || 'reci_journal' !== $comment->comment_type ) {
		return $url;
	}

	if ( (int) get_comment_meta( $comment_id, '_reci_anonymous', true ) !== 1 ) {
		return $url;
	}

	return reci_can_see_journal_identity() ? $url : '';
}
