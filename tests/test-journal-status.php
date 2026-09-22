<?php
/**
 * Status transitions decide whether private writing becomes public, so the
 * rules are a pure function with an explicit table rather than scattered ifs.
 */

require_once __DIR__ . '/../inc/features/journal-status.php';

// Legacy mapping. Rows already flagged shared were publicly visible before the
// migration, so they must land on 'approved' — migrating must never silently
// unpublish someone's entry, nor silently publish a private one.
reci_assert_same( 'approved', reci_journal_status_from_legacy( 1 ), 'legacy: is_shared 1 becomes approved' );
reci_assert_same( 'private',  reci_journal_status_from_legacy( 0 ), 'legacy: is_shared 0 becomes private' );

reci_assert_same( true,  reci_journal_is_valid_status( 'private' ),  'valid: private' );
reci_assert_same( true,  reci_journal_is_valid_status( 'pending' ),  'valid: pending' );
reci_assert_same( true,  reci_journal_is_valid_status( 'approved' ), 'valid: approved' );
reci_assert_same( true,  reci_journal_is_valid_status( 'rejected' ), 'valid: rejected' );
reci_assert_same( false, reci_journal_is_valid_status( 'shared' ),   'valid: rejects unknown status' );
reci_assert_same( false, reci_journal_is_valid_status( '' ),         'valid: rejects empty status' );

// Sharing sends an entry for review. It must never jump straight to approved.
reci_assert_same( true,  reci_journal_can_transition( 'private', 'pending' ),   'transition: share sends for review' );
reci_assert_same( false, reci_journal_can_transition( 'private', 'approved' ), 'transition: sharing cannot self-approve' );

// Moderation outcomes.
reci_assert_same( true, reci_journal_can_transition( 'pending', 'approved' ), 'transition: moderator approves' );
reci_assert_same( true, reci_journal_can_transition( 'pending', 'rejected' ), 'transition: moderator rejects' );

// The author can withdraw from any state back to private.
reci_assert_same( true, reci_journal_can_transition( 'pending', 'private' ),  'transition: withdraw from pending' );
reci_assert_same( true, reci_journal_can_transition( 'approved', 'private' ), 'transition: withdraw from approved' );
reci_assert_same( true, reci_journal_can_transition( 'rejected', 'private' ), 'transition: withdraw from rejected' );

// A rejected entry must be re-reviewed, not quietly restored.
reci_assert_same( false, reci_journal_can_transition( 'rejected', 'approved' ), 'transition: rejected cannot skip review' );

// Re-sharing a rejected entry goes back through the queue.
reci_assert_same( true, reci_journal_can_transition( 'rejected', 'pending' ), 'transition: rejected can be resubmitted' );

// A no-op is not an error, but it is not a transition either.
reci_assert_same( false, reci_journal_can_transition( 'private', 'private' ), 'transition: same-state is not a transition' );

// Unknown states never transition.
reci_assert_same( false, reci_journal_can_transition( 'bogus', 'pending' ), 'transition: unknown source rejected' );
reci_assert_same( false, reci_journal_can_transition( 'pending', 'bogus' ), 'transition: unknown target rejected' );

reci_assert_same( 'Private', reci_journal_status_label( 'private' ), 'label: private' );
reci_assert_same( 'Pending review', reci_journal_status_label( 'pending' ), 'label: pending is distinct from private' );
reci_assert_same( 'Shared', reci_journal_status_label( 'approved' ), 'label: approved reads as shared' );
reci_assert_same( 'Not published', reci_journal_status_label( 'rejected' ), 'label: rejected' );
reci_assert_same( 'Private', reci_journal_status_label( 'nonsense' ), 'label: unknown status falls back to private' );

// --- Row action keys must not collide with wp-admin's global CSS ---------
//
// wp-admin/css/common.css carries an UNSCOPED rule:
//     .approve, .unapproved .unapprove { display: none; }
// WP_List_Table::row_actions() renders each key as <span class="{key}">, so a
// row action keyed 'approve' is present in the HTML and invisible on screen.
// This bit us once; the guard stops it coming back silently.
$reserved = reci_wp_admin_reserved_action_keys();

reci_assert_same( true, in_array( 'approve', $reserved, true ), 'reserved: approve is known to collide' );
reci_assert_same( true, in_array( 'unapprove', $reserved, true ), 'reserved: unapprove is known to collide' );

foreach ( reci_journal_row_action_keys() as $key ) {
	reci_assert_same(
		false,
		in_array( $key, $reserved, true ),
		'row action key "' . $key . '" must not collide with wp-admin CSS'
	);
}
