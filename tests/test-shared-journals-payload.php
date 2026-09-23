<?php
/**
 * This endpoint is a PUBLIC reading surface. It must never carry identity for
 * an anonymous entry — not even for a moderator, who has the admin queue for
 * that. Shaping is pure so the emitted field list is explicit.
 */

require_once __DIR__ . '/../inc/features/journal-sharing.php';

$row = (object) [
	'id'            => 3,
	'user_id'       => 7,
	'response'      => 'I froze when they said it.',
	'prompt'        => 'What did you notice?',
	'is_anonymous'  => 0,
	'created_at'    => '2026-09-01 10:00:00',
	'reflection_id' => 99,
	'flagged_terms' => 'badword',
];

$named = reci_shape_shared_journal( $row, 'Ada Obi' );

reci_assert_same( 3, $named['id'], 'payload: carries the entry id' );
reci_assert_same( 'Ada Obi', $named['author_name'], 'payload: named entry shows the name' );
reci_assert_same( 'I froze when they said it.', $named['response'], 'payload: carries the writing' );
reci_assert_same( 'What did you notice?', $named['prompt'], 'payload: carries the prompt' );
reci_assert_same( false, isset( $named['user_id'] ), 'payload: never carries a user id, even when named' );
reci_assert_same( false, isset( $named['flagged_terms'] ), 'payload: never carries moderation metadata' );

$row->is_anonymous = 1;
$anon = reci_shape_shared_journal( $row, 'Ada Obi' );

reci_assert_same( 'Anonymous', $anon['author_name'], 'payload: anonymous entry is masked' );
reci_assert_same( true, $anon['is_anonymous'], 'payload: flags itself anonymous so the UI can label it' );
reci_assert_same(
	false,
	str_contains( wp_json_encode( $anon ), 'Ada Obi' ),
	'payload: THE REAL NAME APPEARS NOWHERE IN AN ANONYMOUS PAYLOAD'
);
reci_assert_same( 'I froze when they said it.', $anon['response'], 'payload: anonymous entry still carries the writing' );
