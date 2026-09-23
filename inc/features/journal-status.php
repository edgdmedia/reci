<?php
/**
 * Journal status rules.
 *
 * A journal entry's status decides whether private writing is visible to other
 * people, so the transition table is explicit and lives in one place. Nothing
 * outside this file should compare status strings directly.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * The only four statuses a journal entry may hold.
 */
const RECI_JOURNAL_STATUSES = [ 'private', 'pending', 'approved', 'rejected' ];

/**
 * Map the pre-1.6.0 `is_shared` flag onto a status.
 *
 * Rows already flagged shared were publicly visible, so they become approved.
 * Migrating must not change what anyone can see.
 */
function reci_journal_status_from_legacy( int $is_shared ): string {
	return 1 === $is_shared ? 'approved' : 'private';
}

/**
 * Is this one of the four known statuses?
 */
function reci_journal_is_valid_status( string $status ): bool {
	return in_array( $status, RECI_JOURNAL_STATUSES, true );
}

/**
 * May an entry move from one status to another?
 *
 * The table is deliberately restrictive. Two rules matter most: sharing can
 * only reach `pending`, never `approved` — nothing may self-publish; and a
 * rejected entry cannot become approved without going back through the queue.
 */
function reci_journal_can_transition( string $from, string $to ): bool {
	if ( ! reci_journal_is_valid_status( $from ) || ! reci_journal_is_valid_status( $to ) ) {
		return false;
	}

	if ( $from === $to ) {
		return false;
	}

	$allowed = [
		'private'  => [ 'pending' ],
		'pending'  => [ 'approved', 'rejected', 'private' ],
		// A moderator may change their mind in either direction. Only the
		// author can return an entry to private.
		'approved' => [ 'rejected', 'private' ],
		'rejected' => [ 'approved', 'pending', 'private' ],
	];

	return in_array( $to, $allowed[ $from ], true );
}

/**
 * The user-facing label for a status.
 *
 * The dashboard previously showed a two-state Shared/Private pill driven by
 * `is_shared`, which would render a pending entry as "Private" and leave the
 * author wondering whether their share went through.
 */
function reci_journal_status_label( string $status ): string {
	$labels = [
		'private'  => __( 'Private', 'reci-media-hub' ),
		'pending'  => __( 'Pending review', 'reci-media-hub' ),
		'approved' => __( 'Shared', 'reci-media-hub' ),
		'rejected' => __( 'Not published', 'reci-media-hub' ),
	];

	return $labels[ $status ] ?? $labels['private'];
}

/**
 * Row-action keys wp-admin hides with global CSS.
 *
 * `wp-admin/css/common.css` carries an unscoped `.approve { display: none; }`
 * (plus `.unapproved .unapprove`), for the comments screen's approve/unapprove
 * toggle. WP_List_Table::row_actions() renders every key as
 * `<span class="{key}">`, so any custom table using these keys renders a link
 * that is in the HTML and invisible on screen.
 *
 * @return array<int,string>
 */
function reci_wp_admin_reserved_action_keys(): array {
	return [ 'approve', 'unapprove', 'spam', 'unspam', 'trash', 'untrash' ];
}

/**
 * The row-action keys the journals list table uses.
 *
 * Kept here, beside the reserved list, so the test can hold them against each
 * other.
 *
 * @return array<int,string>
 */
function reci_journal_row_action_keys(): array {
	return [ 'reci-approve', 'reci-reject' ];
}
