<?php
/**
 * Like and bookmark counters.
 *
 * Likes and bookmarks are stored per user, in user meta. Counting them per
 * post therefore means reading every user's meta, which is far too expensive
 * to do on a page render. These denormalised post-meta counters are the cache;
 * the user meta remains the source of truth.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'RECI_TESTS_BOOTSTRAPPED' ) ) {
	// Allow the test harness to require this file directly.
	if ( ! function_exists( 'reci_assert' ) ) {
		exit;
	}
}

const RECI_LIKE_COUNT_META     = '_reci_like_count';
const RECI_BOOKMARK_COUNT_META = '_reci_bookmark_count';

/**
 * Tally likes and bookmarks per post.
 *
 * Pure: it takes the two meta shapes as arguments and touches no globals, so
 * it can be tested without WordPress.
 *
 * @param array<int,array<int,mixed>> $likes_by_user     user_id => list of post IDs.
 * @param array<int,array<int,mixed>> $bookmarks_by_user user_id => list of [ 'post_id' => int, ... ].
 *
 * @return array<int,array{likes:int,bookmarks:int}> post_id => counts.
 */
function reci_tally_engagement( array $likes_by_user, array $bookmarks_by_user ): array {
	$tally = [];

	$touch = static function ( int $post_id ) use ( &$tally ): void {
		if ( ! isset( $tally[ $post_id ] ) ) {
			$tally[ $post_id ] = [ 'likes' => 0, 'bookmarks' => 0 ];
		}
	};

	foreach ( $likes_by_user as $likes ) {
		foreach ( (array) $likes as $post_id ) {
			$post_id = (int) $post_id;
			if ( $post_id <= 0 ) {
				continue;
			}
			$touch( $post_id );
			$tally[ $post_id ]['likes']++;
		}
	}

	foreach ( $bookmarks_by_user as $bookmarks ) {
		foreach ( (array) $bookmarks as $bookmark ) {
			if ( ! is_array( $bookmark ) || ! isset( $bookmark['post_id'] ) ) {
				continue;
			}
			$post_id = (int) $bookmark['post_id'];
			if ( $post_id <= 0 ) {
				continue;
			}
			$touch( $post_id );
			$tally[ $post_id ]['bookmarks']++;
		}
	}

	return $tally;
}

/**
 * Read a post's like count.
 */
function reci_get_like_count( int $post_id ): int {
	return (int) get_post_meta( $post_id, RECI_LIKE_COUNT_META, true );
}

/**
 * Read a post's bookmark count.
 */
function reci_get_bookmark_count( int $post_id ): int {
	return (int) get_post_meta( $post_id, RECI_BOOKMARK_COUNT_META, true );
}

/**
 * Write a post's count for one kind.
 *
 * @param string $kind 'like' or 'bookmark'.
 */
function reci_set_engagement_count( int $post_id, string $kind, int $count ): void {
	$meta_key = ( 'like' === $kind ) ? RECI_LIKE_COUNT_META : RECI_BOOKMARK_COUNT_META;
	update_post_meta( $post_id, $meta_key, max( 0, $count ) );
}

/**
 * Nudge a counter by one in either direction.
 *
 * Clamped at zero: a counter that has drifted below the real figure should
 * recover on the next recount rather than render as a negative.
 *
 * @param string $kind  'like' or 'bookmark'.
 * @param int    $delta Normally 1 or -1.
 */
function reci_bump_engagement_count( int $post_id, string $kind, int $delta ): void {
	$current = ( 'like' === $kind )
		? reci_get_like_count( $post_id )
		: reci_get_bookmark_count( $post_id );

	reci_set_engagement_count( $post_id, $kind, $current + $delta );
}

/**
 * Recompute every counter from user meta.
 *
 * Called once by the 1.6.0 migration, and available afterwards for repair if
 * the cached counts ever drift from the user meta.
 *
 * @return int Number of posts written.
 */
function reci_backfill_engagement_counts(): int {
	global $wpdb;

	$likes_by_user     = [];
	$bookmarks_by_user = [];

	$rows = $wpdb->get_results(
		"SELECT user_id, meta_key, meta_value
		   FROM {$wpdb->usermeta}
		  WHERE meta_key IN ( 'reci_likes', 'reci_bookmarks' )"
	);

	foreach ( $rows as $row ) {
		$value = maybe_unserialize( $row->meta_value );
		if ( ! is_array( $value ) ) {
			continue;
		}

		if ( 'reci_likes' === $row->meta_key ) {
			$likes_by_user[ (int) $row->user_id ] = $value;
		} else {
			$bookmarks_by_user[ (int) $row->user_id ] = $value;
		}
	}

	$tally = reci_tally_engagement( $likes_by_user, $bookmarks_by_user );

	foreach ( $tally as $post_id => $counts ) {
		reci_set_engagement_count( $post_id, 'like', $counts['likes'] );
		reci_set_engagement_count( $post_id, 'bookmark', $counts['bookmarks'] );
	}

	return count( $tally );
}
