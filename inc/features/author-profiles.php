<?php
/**
 * Shared author profile helpers for frontend attribution.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_display_author_post_types')) {
	/**
	 * Post types that support shared display authors.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_display_author_post_types(): array {
		return [
			'post',
			'reci_podcast',
			'reci_video',
			'reci_event',
			'reci_reflection',
			'reci_course',
			'reci_document',
			'reci_assessment',
			'reci_quote',
			'reci_testimonial',
		];
	}
}

if (! function_exists('reci_media_hub_get_author_profile_linked_user_id')) {
	/**
	 * Resolve a linked WordPress user for an author profile.
	 */
	function reci_media_hub_get_author_profile_linked_user_id(int $profile_id): int {
		if ($profile_id <= 0 || get_post_type($profile_id) !== 'reci_author') {
			return 0;
		}

		return max(0, (int) get_post_meta($profile_id, '_reci_author_profile_user_id', true));
	}
}

if (! function_exists('reci_media_hub_get_author_profile_by_user_id')) {
	/**
	 * Find an author profile linked to a WordPress user.
	 */
	function reci_media_hub_get_author_profile_by_user_id(int $user_id): int {
		if ($user_id <= 0) {
			return 0;
		}

		$profile_ids = get_posts([
			'post_type'      => 'reci_author',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_key'       => '_reci_author_profile_user_id',
			'meta_value'     => $user_id,
			'no_found_rows'  => true,
		]);

		return ! empty($profile_ids) ? (int) $profile_ids[0] : 0;
	}
}

if (! function_exists('reci_media_hub_get_author_profile_data')) {
	/**
	 * Build frontend-safe author profile data.
	 *
	 * @return array<string,mixed>
	 */
	function reci_media_hub_get_author_profile_data(int $profile_id): array {
		$profile = get_post($profile_id);
		if (! $profile instanceof WP_Post || $profile->post_type !== 'reci_author' || $profile->post_status !== 'publish') {
			return [];
		}

		$image_id  = get_post_thumbnail_id($profile_id);
		$image_alt = $image_id ? (string) get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
		if ($image_alt === '') {
			$image_alt = get_the_title($profile_id);
		}

		$bio = has_excerpt($profile_id)
			? get_the_excerpt($profile_id)
			: wp_trim_words(wp_strip_all_tags((string) $profile->post_content), 45, '...');

		$bio = str_replace('Contributor to the RECI Media Hub demo library.', '', $bio);
		$bio = trim($bio);

		return [
			'id'         => $profile_id,
			'source'     => 'profile',
			'profile_id' => $profile_id,
			'user_id'    => reci_media_hub_get_author_profile_linked_user_id($profile_id),
			'name'       => get_the_title($profile_id),
			'title'      => (string) get_post_meta($profile_id, '_reci_author_profile_title', true),
			'bio'        => (string) $bio,
			'image_url'  => get_the_post_thumbnail_url($profile_id, 'medium') ?: '',
			'image_alt'  => $image_alt,
			'permalink'  => get_permalink($profile_id),
		];
	}
}

if (! function_exists('reci_media_hub_get_user_author_data')) {
	/**
	 * Build frontend-safe native user author data.
	 *
	 * @return array<string,mixed>
	 */
	function reci_media_hub_get_user_author_data(int $user_id): array {
		if ($user_id <= 0) {
			return [];
		}

		$user = get_user_by('id', $user_id);
		if (! $user instanceof WP_User) {
			return [];
		}

		return [
			'id'         => $user_id,
			'source'     => 'user',
			'profile_id' => 0,
			'user_id'    => $user_id,
			'name'       => $user->display_name,
			'title'      => (string) get_user_meta($user_id, 'user_title', true),
			'bio'        => (string) get_user_meta($user_id, 'description', true),
			'image_url'  => get_avatar_url($user_id, ['size' => 160]) ?: '',
			'image_alt'  => $user->display_name,
			'permalink'  => get_author_posts_url($user_id),
		];
	}
}

if (! function_exists('reci_media_hub_get_display_author')) {
	/**
	 * Resolve the public author identity for a content item.
	 *
	 * @return array<string,mixed>
	 */
	function reci_media_hub_get_display_author(int $post_id): array {
		if ($post_id <= 0) {
			return [
				'id'         => 0,
				'source'     => 'fallback',
				'profile_id' => 0,
				'user_id'    => 0,
				'name'       => __('RECI', 'reci-media-hub'),
				'title'      => '',
				'bio'        => '',
				'image_url'  => '',
				'image_alt'  => __('RECI', 'reci-media-hub'),
				'permalink'  => '',
			];
		}

		$explicit_profile_id = max(0, (int) get_post_meta($post_id, '_reci_display_author_profile_id', true));
		if ($explicit_profile_id > 0) {
			$profile_data = reci_media_hub_get_author_profile_data($explicit_profile_id);
			if (! empty($profile_data)) {
				return $profile_data;
			}
		}

		$user_id = (int) get_post_field('post_author', $post_id);
		if ($user_id > 0) {
			$linked_profile_id = reci_media_hub_get_author_profile_by_user_id($user_id);
			if ($linked_profile_id > 0) {
				$profile_data = reci_media_hub_get_author_profile_data($linked_profile_id);
				if (! empty($profile_data)) {
					return $profile_data;
				}
			}

			$user_data = reci_media_hub_get_user_author_data($user_id);
			if (! empty($user_data)) {
				return $user_data;
			}
		}

		return [
			'id'         => 0,
			'source'     => 'fallback',
			'profile_id' => 0,
			'user_id'    => 0,
			'name'       => __('RECI', 'reci-media-hub'),
			'title'      => '',
			'bio'        => '',
			'image_url'  => '',
			'image_alt'  => __('RECI', 'reci-media-hub'),
			'permalink'  => '',
		];
	}
}

if (! function_exists('reci_media_hub_get_authored_content_ids')) {
	/**
	 * Get content IDs attributed to an author profile.
	 *
	 * @param array<int,string> $post_types Post types to inspect.
	 * @return array<int,int>
	 */
	function reci_media_hub_get_authored_content_ids(int $profile_id, array $post_types = []): array {
		if ($profile_id <= 0) {
			return [];
		}

		$post_types = array_values(array_filter(array_map('sanitize_key', empty($post_types) ? reci_media_hub_display_author_post_types() : $post_types)));
		if (empty($post_types)) {
			return [];
		}

		$explicit_ids = get_posts([
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_key'       => '_reci_display_author_profile_id',
			'meta_value'     => $profile_id,
			'no_found_rows'  => true,
		]);

		$fallback_ids = [];
		$linked_user  = reci_media_hub_get_author_profile_linked_user_id($profile_id);
		if ($linked_user > 0) {
			$fallback_ids = get_posts([
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'author'         => $linked_user,
				'no_found_rows'  => true,
				'meta_query'     => [
					'relation' => 'OR',
					[
						'key'     => '_reci_display_author_profile_id',
						'compare' => 'NOT EXISTS',
					],
					[
						'key'   => '_reci_display_author_profile_id',
						'value' => '',
					],
					[
						'key'     => '_reci_display_author_profile_id',
						'value'   => 0,
						'compare' => '=',
						'type'    => 'NUMERIC',
					],
				],
			]);
		}

		return array_values(array_unique(array_map('intval', array_merge($explicit_ids, $fallback_ids))));
	}
}

if (! function_exists('reci_media_hub_get_author_profile_options')) {
	/**
	 * Build author profile select/navigation options.
	 *
	 * @param array<int,string> $post_types Optional content post types used to restrict profiles by usage.
	 * @return array<int,array<string,mixed>>
	 */
	function reci_media_hub_get_author_profile_options(bool $only_with_content = false, array $post_types = []): array {
		$profiles = get_posts([
			'post_type'      => 'reci_author',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		]);

		$options = [];
		foreach ($profiles as $profile) {
			if (! $profile instanceof WP_Post) {
				continue;
			}

			$authored_ids = reci_media_hub_get_authored_content_ids((int) $profile->ID, $post_types);
			if ($only_with_content && empty($authored_ids)) {
				continue;
			}

			$options[] = [
				'ID'             => (int) $profile->ID,
				'display_name'   => get_the_title($profile->ID),
				'permalink'      => get_permalink($profile->ID),
				'linked_user_id' => reci_media_hub_get_author_profile_linked_user_id((int) $profile->ID),
				'count'          => count($authored_ids),
			];
		}

		return $options;
	}
}
