<?php
/**
 * Journal (reflection response) storage for immersive galleries.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

// ---------------------------------------------------------------------------
// REST API routes
// ---------------------------------------------------------------------------

if (! function_exists('reci_register_journal_routes')) {
	function reci_register_journal_routes(): void {
		register_rest_route(
			'reci/v1',
			'/journals',
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => 'reci_get_journals',
					'permission_callback' => static function (): bool {
						return is_user_logged_in();
					},
				],
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => 'reci_create_journal',
					'permission_callback' => static function (): bool {
						return is_user_logged_in();
					},
				],
			]
		);

		register_rest_route('reci/v1', '/journals/(?P<id>\d+)/share', [
			'methods'             => 'PATCH',
			'callback'            => 'reci_update_journal_share',
			'permission_callback' => static function (WP_REST_Request $request): bool {
				global $wpdb;
				$id = (int) $request->get_param('id');
				$table = $wpdb->prefix . 'reci_journals';
				$post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
				return $post && (int) $post->user_id === get_current_user_id();
			},
		]);
	}
}
add_action('rest_api_init', 'reci_register_journal_routes');

if (! function_exists('reci_get_journals')) {
	function reci_get_journals(WP_REST_Request $request): WP_REST_Response {
		global $wpdb;
		$user_id       = get_current_user_id();
		$reflection_id = absint((string) $request->get_param('reflection_id'));
		$page          = max(1, absint((string) $request->get_param('page') ?: '1'));
		$per_page      = min(100, max(1, absint((string) $request->get_param('per_page') ?: '20')));
		$offset        = ($page - 1) * $per_page;
		
		$table = $wpdb->prefix . 'reci_journals';
		$where = $wpdb->prepare("user_id = %d", $user_id);
		
		if ($reflection_id) {
			$where .= $wpdb->prepare(" AND reflection_id = %d", $reflection_id);
		}
		
		$total_query = "SELECT COUNT(id) FROM $table WHERE $where";
		$total       = (int) $wpdb->get_var($total_query);
		
		$data_query = "SELECT * FROM $table WHERE $where ORDER BY created_at DESC LIMIT %d, %d";
		$results    = $wpdb->get_results($wpdb->prepare($data_query, $offset, $per_page));

		$items = [];
		foreach ($results as $row) {
			$items[] = [
				'id'            => (int) $row->id,
				'title'         => wp_trim_words($row->prompt, 8, '...') . ' - ' . wp_date('Y-m-d H:i', strtotime($row->created_at)),
				'raw_response'  => $row->response,
				'prompt'        => $row->prompt,
				'reflection_id' => (int) $row->reflection_id,
				'created_at'    => gmdate('Y-m-d\TH:i:sP', strtotime($row->created_at)),
				'status'        => (string) $row->status,
				'is_anonymous'  => (bool) (int) $row->is_anonymous,
				'flagged_terms' => array_values( array_filter( explode( "\n", (string) $row->flagged_terms ) ) ),
			];
		}
		
		$max_pages = ceil($total / $per_page);
		
		return new WP_REST_Response([
			'items'       => $items,
			'total'       => $total,
			'page'        => $page,
			'per_page'    => $per_page,
			'total_pages' => (int) $max_pages,
		], 200);
	}
}

if (! function_exists('reci_create_journal')) {
	function reci_create_journal(WP_REST_Request $request) {
		global $wpdb;
		$user_id       = get_current_user_id();
		$reflection_id = absint((string) $request->get_param('reflection_id'));
		$prompt        = sanitize_text_field((string) $request->get_param('prompt'));
		$response      = wp_kses_post((string) $request->get_param('response'));
		
		if (! $reflection_id || get_post_type($reflection_id) !== 'reci_reflection') {
			return new WP_Error('invalid_reflection', __('Invalid reflection selected.', 'reci-media-hub'), ['status' => 400]);
		}
		if ($prompt === '' || trim(wp_strip_all_tags($response)) === '') {
			return new WP_Error('missing_response', __('Prompt and response are required.', 'reci-media-hub'), ['status' => 400]);
		}
		
		// A user whose default privacy is "public" previously had every entry
		// published outright, with no review at all. Entries are now always
		// created private, and a public default routes through the same share
		// path as an explicit share — landing in the moderation queue.
		$default_privacy = get_user_meta($user_id, 'reci_journal_default_privacy', true);
		$share_by_default = ('public' === $default_privacy);
		$is_shared       = 0;
		
		$table = $wpdb->prefix . 'reci_journals';
		$inserted = $wpdb->insert(
			$table,
			[
				'user_id'       => $user_id,
				'reflection_id' => $reflection_id,
				'prompt'        => $prompt,
				'response'      => $response,
				'is_shared'     => 0,
				'status'        => 'private',
				'created_at'    => current_time('mysql', true),
			],
			['%d', '%d', '%s', '%s', '%d', '%s', '%s']
		);
		
		if (! $inserted) {
			return new WP_Error('insert_failed', __('Failed to save response.', 'reci-media-hub'), ['status' => 500]);
		}
		
		$journal_id = (int) $wpdb->insert_id;

		if ($share_by_default) {
			reci_share_journal($journal_id, false);
		}

		return new WP_REST_Response(
			[
				'id'      => $journal_id,
				'status'  => $share_by_default ? 'pending' : 'private',
				'message' => $share_by_default
					? __('Response saved and sent for review.', 'reci-media-hub')
					: __('Response saved.', 'reci-media-hub'),
			],
			201
		);
	}
}

if (! function_exists('reci_update_journal_share')) {
	function reci_update_journal_share(WP_REST_Request $request) {
		$journal_id = (int) $request->get_param('id');
		$shared     = (bool) $request->get_param('shared');
		$anonymous  = (bool) $request->get_param('anonymous');

		if (! $shared) {
			$ok = reci_unshare_journal($journal_id);

			return $ok
				? new WP_REST_Response(['shared' => false, 'status' => 'private'], 200)
				: new WP_Error('unshare_failed', __('Could not withdraw that entry.', 'reci-media-hub'), ['status' => 409]);
		}

		$result = reci_share_journal($journal_id, $anonymous);

		if (is_wp_error($result)) {
			return $result;
		}

		return new WP_REST_Response(
			[
				'shared'    => true,
				'status'    => $result['status'],
				'anonymous' => $anonymous,
				'flagged'   => $result['flagged'],
			],
			200
		);
	}
}
