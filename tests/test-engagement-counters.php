<?php
/**
 * Counting is the part that can be wrong silently, so it is a pure function
 * over the two user-meta shapes the dashboard already stores.
 *
 * Likes are stored as a flat list of post IDs.
 * Bookmarks are stored as a list of [ 'post_id' => int, 'bookmarked_at' => int ].
 */

require_once __DIR__ . '/../inc/features/engagement-counters.php';

$likes_by_user = [
	7  => [ 10, 11 ],
	8  => [ 11 ],
	9  => [],
];

$bookmarks_by_user = [
	7 => [ [ 'post_id' => 11, 'bookmarked_at' => 1 ] ],
	8 => [ [ 'post_id' => 11, 'bookmarked_at' => 2 ], [ 'post_id' => 12, 'bookmarked_at' => 3 ] ],
];

$tally = reci_tally_engagement( $likes_by_user, $bookmarks_by_user );

reci_assert_same( 1, $tally[10]['likes'], 'tally: post 10 has one like' );
reci_assert_same( 0, $tally[10]['bookmarks'], 'tally: post 10 has no bookmarks' );
reci_assert_same( 2, $tally[11]['likes'], 'tally: post 11 has two likes' );
reci_assert_same( 2, $tally[11]['bookmarks'], 'tally: post 11 has two bookmarks' );
reci_assert_same( 0, $tally[12]['likes'], 'tally: post 12 has no likes' );
reci_assert_same( 1, $tally[12]['bookmarks'], 'tally: post 12 has one bookmark' );

// A user with an empty list must not create phantom entries.
reci_assert_same( false, isset( $tally[0] ), 'tally: no phantom post 0' );

// Malformed rows must be skipped, not fatal. Real user meta accumulates junk.
$messy = reci_tally_engagement(
	[ 7 => [ 10, 'not-an-id', 0, null ] ],
	[ 7 => [ [ 'no_post_id' => 1 ], 'garbage', [ 'post_id' => 13 ] ] ]
);
reci_assert_same( 1, $messy[10]['likes'], 'tally: valid like survives junk' );
reci_assert_same( 1, $messy[13]['bookmarks'], 'tally: valid bookmark survives junk' );
reci_assert_same( false, isset( $messy[0] ), 'tally: junk does not become post 0' );
