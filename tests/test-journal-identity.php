<?php
/**
 * The masking decision is the highest-consequence pure function in this
 * feature: getting it wrong exposes a real person who was promised anonymity.
 * It takes its inputs as arguments precisely so it can be exhaustively tested.
 */

require_once __DIR__ . '/../inc/features/journal-identity.php';

// Not anonymous: everyone sees the real name, whatever their capability.
$r = reci_journal_display_identity( false, false, 'Ada Obi' );
reci_assert_same( 'Ada Obi', $r['name'], 'identity: named entry shows the name to an ordinary viewer' );
reci_assert_same( false, $r['is_masked'], 'identity: named entry is not masked' );

$r = reci_journal_display_identity( false, true, 'Ada Obi' );
reci_assert_same( 'Ada Obi', $r['name'], 'identity: named entry shows the name to a privileged viewer' );
reci_assert_same( false, $r['is_masked'], 'identity: named entry is not masked for a privileged viewer' );

// Anonymous, ordinary viewer: masked. This covers the public, the reflection
// owner, editors and site managers alike — none of them hold the capability.
$r = reci_journal_display_identity( true, false, 'Ada Obi' );
reci_assert_same( 'Anonymous', $r['name'], 'identity: anonymous entry masks the name' );
reci_assert_same( true, $r['is_masked'], 'identity: anonymous entry reports itself masked' );

// Anonymous, privileged viewer: revealed.
$r = reci_journal_display_identity( true, true, 'Ada Obi' );
reci_assert_same( 'Ada Obi', $r['name'], 'identity: privileged viewer sees through anonymity' );
reci_assert_same( false, $r['is_masked'], 'identity: privileged view is not masked' );

// A missing real name must never leak an empty string into the UI, and must
// never be mistaken for "not anonymous".
$r = reci_journal_display_identity( false, false, '' );
reci_assert_same( 'Anonymous', $r['name'], 'identity: empty real name falls back to the placeholder' );
reci_assert_same( true, $r['is_masked'], 'identity: empty real name counts as masked' );

// Deleted user: same fallback, not a fatal and not a blank byline.
$r = reci_journal_display_identity( true, true, '' );
reci_assert_same( 'Anonymous', $r['name'], 'identity: privileged view of a deleted user still has a name' );
reci_assert_same( true, $r['is_masked'], 'identity: deleted user counts as masked' );
