<?php
/**
 * The mirror comment is where anonymity is actually enforced. For an
 * anonymous entry it must carry no identity at all — not the user id, not the
 * email — because everything downstream (the admin queue, /wp/v2/comments,
 * exports, other plugins) reads those fields directly.
 */

require_once __DIR__ . '/../inc/features/journal-sharing.php';

$journal = [
	'id'           => 42,
	'user_id'      => 7,
	'response'     => 'I froze when they said it.',
	'prompt'       => 'What did you notice?',
	'is_anonymous' => 0,
];

$args = reci_journal_mirror_comment_args( $journal, 'Ada Obi', 99 );

reci_assert_same( 'reci_journal', $args['comment_type'], 'mirror: uses the dedicated comment type' );
reci_assert_same( 99, $args['comment_post_ID'], 'mirror: attaches to the reflection' );
reci_assert_same( 0, $args['comment_approved'], 'mirror: starts unapproved, never self-publishes' );
reci_assert_same( 7, $args['user_id'], 'mirror: named entry keeps the author id' );
reci_assert_same( 'Ada Obi', $args['comment_author'], 'mirror: named entry keeps the display name' );
reci_assert_same( 'I froze when they said it.', $args['comment_content'], 'mirror: carries the response' );
reci_assert_same( 42, $args['comment_meta']['_reci_journal_id'], 'mirror: links back to the journal row' );
reci_assert_same( 0, $args['comment_meta']['_reci_anonymous'], 'mirror: records not-anonymous' );

// The anonymous case. These four assertions are the whole privacy guarantee.
$journal['is_anonymous'] = 1;
$anon = reci_journal_mirror_comment_args( $journal, 'Ada Obi', 99 );

reci_assert_same( 0, $anon['user_id'], 'mirror: ANONYMOUS ENTRY STORES NO USER ID' );
reci_assert_same( 'Anonymous', $anon['comment_author'], 'mirror: anonymous entry stores the placeholder name' );
reci_assert_same( '', $anon['comment_author_email'], 'mirror: anonymous entry stores no email' );
reci_assert_same( '', $anon['comment_author_url'], 'mirror: anonymous entry stores no url' );
reci_assert_same( 1, $anon['comment_meta']['_reci_anonymous'], 'mirror: records anonymous' );

// The real name must not survive anywhere in the argument array, including
// places a future edit might add it without thinking.
reci_assert_same(
	false,
	str_contains( wp_json_encode( $anon ), 'Ada Obi' ),
	'mirror: the real name appears nowhere in an anonymous mirror'
);

// The prompt travels so a moderator can read the entry in context.
reci_assert_same( 'What did you notice?', $anon['comment_meta']['_reci_prompt'], 'mirror: carries the prompt for context' );
