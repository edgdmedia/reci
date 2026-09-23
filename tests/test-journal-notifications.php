<?php
/**
 * Notification rows are STORED text, and the reflection owner reads those
 * rows. Masking at render time would be too late — the name must never be
 * written in the first place.
 */

require_once __DIR__ . '/../inc/features/journal-moderation.php';

// Named entry: the owner may see who wrote it.
$named = reci_journal_owner_notification_text( false, 'Ada Obi', 'We Humans' );
reci_assert_same( true, str_contains( $named['message'], 'Ada Obi' ), 'notify: named entry names the author' );
reci_assert_same( true, str_contains( $named['message'], 'We Humans' ), 'notify: names the reflection' );

// Anonymous entry: the owner must NOT see who wrote it. This is the assertion
// that enforces "hidden from the reflection owner".
$anon = reci_journal_owner_notification_text( true, 'Ada Obi', 'We Humans' );
reci_assert_same( false, str_contains( $anon['message'], 'Ada Obi' ), 'notify: ANONYMOUS ENTRY NEVER STORES THE NAME' );
reci_assert_same( false, str_contains( $anon['title'], 'Ada Obi' ), 'notify: the title never stores the name either' );
reci_assert_same( true, str_contains( $anon['message'], 'Anonymous' ), 'notify: anonymous entry says so' );
reci_assert_same( true, str_contains( $anon['message'], 'We Humans' ), 'notify: still names the reflection' );

// A deleted author must not produce an empty byline mid-sentence.
$gone = reci_journal_owner_notification_text( false, '', 'We Humans' );
reci_assert_same( true, str_contains( $gone['message'], 'Anonymous' ), 'notify: missing name falls back to the placeholder' );

// Titles stay short enough for the notification list.
reci_assert_same( true, strlen( $anon['title'] ) <= 255, 'notify: title fits the column' );
reci_assert_same( true, strlen( $named['title'] ) <= 255, 'notify: named title fits the column' );
