<?php
/**
 * AJAX Live Search functionality.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('wp_ajax_reci_live_search', 'reci_ajax_live_search');
add_action('wp_ajax_nopriv_reci_live_search', 'reci_ajax_live_search');

function reci_ajax_live_search(): void {
	header('Content-Type: application/json');

	$query = isset($_GET['q']) ? sanitize_text_field((string) wp_unslash($_GET['q'])) : '';

	if (strlen($query) < 3) {
		wp_send_json_success(['results' => []]);
		return;
	}

	$post_types = ['post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection'];

	$args = [
		'post_type'              => $post_types,
		'posts_per_page'        => 8,
		's'                     => $query,
		'no_found_rows'         => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	];

	$search = new WP_Query($args);
	$results = [];

	if ($search->have_posts()) {
		while ($search->have_posts()) {
			$search->the_post();
			$post_id   = get_the_ID();
			$post_type = get_post_type();

			$type_labels = [
				'post'    => 'Article',
				'reci_podcast'    => 'Podcast',
				'reci_video'      => 'Video',
				'reci_event'      => 'Event',
				'reci_reflection' => 'Reflection',
			];

			$results[] = [
				'id'      => $post_id,
				'title'   => get_the_title(),
				'url'     => get_permalink(),
				'type'    => $type_labels[$post_type] ?? ucfirst($post_type),
				'date'    => get_the_date('M j, Y'),
				'excerpt' => wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 12, '...'),
			];
		}
		wp_reset_postdata();
	}

	wp_send_json_success(['results' => $results]);
}
