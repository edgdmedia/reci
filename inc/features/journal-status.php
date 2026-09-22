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
		'approved' => [ 'rejected', 'private' ],
		'rejected' => [ 'pending', 'private' ],
	];

	return in_array( $to, $allowed[ $from ], true );
}
