<?php
/**
 * Reflection response storage for hardcoded immersive galleries.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_register_reflection_response_post_type')) {
	function reci_register_reflection_response_post_type(): void {
		register_post_type(
			'reci_reflection_response',
			[
				'labels' => [
					'name' => __('Reflection Responses', 'reci-media-hub'),
					'singular_name' => __('Reflection Response', 'reci-media-hub'),
				],
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_rest' => false,
				'supports' => ['title', 'editor', 'author', 'revisions'],
				'menu_icon' => 'dashicons-feedback',
				'menu_position' => 29,
			]
		);
	}
}
add_action('init', 'reci_register_reflection_response_post_type');

if (! function_exists('reci_register_reflection_response_routes')) {
	function reci_register_reflection_response_routes(): void {
		register_rest_route(
			'reci/v1',
			'/reflection-responses',
			[
				[
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'reci_get_reflection_responses',
					'permission_callback' => static function (): bool {
						return is_user_logged_in();
					},
				],
				[
					'methods' => WP_REST_Server::CREATABLE,
					'callback' => 'reci_create_reflection_response',
					'permission_callback' => static function (): bool {
						return is_user_logged_in();
					},
				],
			]
		);
	}
}
add_action('rest_api_init', 'reci_register_reflection_response_routes');

if (! function_exists('reci_get_reflection_responses')) {
	function reci_get_reflection_responses(WP_REST_Request $request): WP_REST_Response {
		$user_id = get_current_user_id();
		$reflection_id = absint((string) $request->get_param('reflection_id'));
		$args = [
			'post_type' => 'reci_reflection_response',
			'post_status' => 'private',
			'posts_per_page' => 100,
			'author' => $user_id,
			'orderby' => 'date',
			'order' => 'DESC',
		];
		if ($reflection_id) {
			$args['meta_query'] = [[
				'key' => '_reci_reflection_id',
				'value' => $reflection_id,
				'compare' => '=',
				'type' => 'NUMERIC',
			]];
		}
		$query = new WP_Query($args);
		$items = [];
		foreach ($query->posts as $post) {
			$items[] = [
				'id' => $post->ID,
				'title' => get_the_title($post),
				'raw_response' => $post->post_content,
				'prompt' => (string) get_post_meta($post->ID, '_reci_reflection_prompt', true),
				'reflection_id' => (int) get_post_meta($post->ID, '_reci_reflection_id', true),
				'created_at' => get_the_date(DATE_ATOM, $post),
			];
		}
		wp_reset_postdata();
		return new WP_REST_Response(['items' => $items], 200);
	}
}

if (! function_exists('reci_create_reflection_response')) {
	function reci_create_reflection_response(WP_REST_Request $request) {
		$user_id = get_current_user_id();
		$reflection_id = absint((string) $request->get_param('reflection_id'));
		$prompt = sanitize_text_field((string) $request->get_param('prompt'));
		$response = wp_kses_post((string) $request->get_param('response'));
		if (! $reflection_id || get_post_type($reflection_id) !== 'reci_reflection') {
			return new WP_Error('invalid_reflection', __('Invalid reflection selected.', 'reci-media-hub'), ['status' => 400]);
		}
		if ($prompt === '' || trim(wp_strip_all_tags($response)) === '') {
			return new WP_Error('missing_response', __('Prompt and response are required.', 'reci-media-hub'), ['status' => 400]);
		}
		$post_id = wp_insert_post([
			'post_type' => 'reci_reflection_response',
			'post_status' => 'private',
			'post_author' => $user_id,
			'post_title' => wp_trim_words($prompt, 8, '...') . ' - ' . wp_date('Y-m-d H:i'),
			'post_content' => $response,
		], true);
		if (is_wp_error($post_id)) {
			return $post_id;
		}
		update_post_meta($post_id, '_reci_reflection_id', $reflection_id);
		update_post_meta($post_id, '_reci_reflection_prompt', $prompt);
		return new WP_REST_Response(['id' => $post_id, 'message' => __('Response saved.', 'reci-media-hub')], 201);
	}
}
