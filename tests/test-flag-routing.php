<?php
/**
 * Where a match routes is the privacy-critical half of the policy feature.
 * A private journal entry is the writer's own space: it may warn them, but it
 * must never put their writing in front of a moderator.
 */

require_once __DIR__ . '/../inc/features/community-policy.php';

$matches = [ 'badword' ];

// The rule that matters most.
reci_assert_same( false, reci_should_flag_for_review( 'private_journal', $matches ), 'routing: A PRIVATE JOURNAL IS NEVER FLAGGED' );

// Surfaces that publish to other people do route for review.
reci_assert_same( true, reci_should_flag_for_review( 'shared_journal', $matches ), 'routing: a shared journal is flagged' );
reci_assert_same( true, reci_should_flag_for_review( 'comment', $matches ), 'routing: a comment is flagged' );

// No match, nothing to route, on any surface.
reci_assert_same( false, reci_should_flag_for_review( 'shared_journal', [] ), 'routing: no match means no flag' );
reci_assert_same( false, reci_should_flag_for_review( 'comment', [] ), 'routing: no match means no flag on comments' );
reci_assert_same( false, reci_should_flag_for_review( 'private_journal', [] ), 'routing: no match means no flag when private' );

// An unrecognised surface must fail closed — do not flag writing we cannot
// classify, because flagging is what exposes it to a stranger.
reci_assert_same( false, reci_should_flag_for_review( 'something_new', $matches ), 'routing: unknown surface fails closed' );
reci_assert_same( false, reci_should_flag_for_review( '', $matches ), 'routing: empty surface fails closed' );
