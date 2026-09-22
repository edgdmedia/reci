<?php
/**
 * /wp/v2/comments is a side door: it would serve the mirror comments straight
 * out of core with their author fields attached. Stripping is a pure function
 * so the field list is explicit and reviewable.
 */

require_once __DIR__ . '/../inc/features/journal-identity.php';

$payload = [
	'id'           => 5,
	'post'         => 99,
	'content'      => [ 'rendered' => 'I froze.' ],
	'author'       => 7,
	'author_name'  => 'Ada Obi',
	'author_url'   => 'https://example.com',
	'author_email' => 'ada@example.com',
	'author_ip'    => '203.0.113.9',
	'author_avatar_urls' => [ '96' => 'https://example.com/a.png' ],
	'meta'         => [ 'anything' => 1 ],
];

$clean = reci_strip_comment_identity( $payload );

reci_assert_same( 0, $clean['author'], 'strip: author id zeroed' );
reci_assert_same( 'Anonymous', $clean['author_name'], 'strip: author name replaced' );
reci_assert_same( '', $clean['author_url'], 'strip: author url cleared' );
reci_assert_same( false, isset( $clean['author_email'] ), 'strip: author email removed entirely' );
reci_assert_same( false, isset( $clean['author_ip'] ), 'strip: author ip removed entirely' );
reci_assert_same( [], $clean['author_avatar_urls'], 'strip: avatars cleared, since an avatar identifies a person' );

// The entry itself must survive: this hides who wrote it, not what they wrote.
reci_assert_same( 'I froze.', $clean['content']['rendered'], 'strip: the writing is preserved' );
reci_assert_same( 5, $clean['id'], 'strip: the id is preserved' );
reci_assert_same( 99, $clean['post'], 'strip: the reflection link is preserved' );

// A payload missing some fields must not fatal.
$sparse = reci_strip_comment_identity( [ 'id' => 1 ] );
reci_assert_same( 1, $sparse['id'], 'strip: sparse payload survives' );
reci_assert_same( 0, $sparse['author'], 'strip: sparse payload still gets a zeroed author' );
