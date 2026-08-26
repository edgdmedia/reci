<?php
/**
 * Front-end content submissions for RECI.
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action( 'transition_post_status', 'reci_maybe_notify_staff_about_submission', 10, 3 );
function reci_maybe_notify_staff_about_submission( string $new_status, string $old_status, WP_Post $post ): void {
	if ( 'pending' !== $new_status || 'pending' === $old_status ) {
		return;
	}

	$allowed_types = reci_media_hub_submission_supported_post_types();
	if ( ! in_array( $post->post_type, $allowed_types, true ) ) {
		return;
	}

	if ( function_exists( 'reci_send_staff_submission_notification' ) ) {
		reci_send_staff_submission_notification( (int) $post->ID );
	}
}

if (! function_exists('reci_media_hub_submission_type_map')) {
	/**
	 * Front-end submission content type map.
	 *
	 * @return array<string,string>
	 */
	function reci_media_hub_submission_type_map(): array {
		return [
			'blog'       => 'post',
			'article'    => 'post',
			'podcast'    => 'reci_podcast',
			'video'      => 'reci_video',
			'document'   => 'reci_document',
			'assessment' => 'reci_assessment',
			'exhibit'    => 'post',
			'other'      => 'post',
		];
	}
}

if (! function_exists('reci_media_hub_submission_type_labels')) {
	/**
	 * Display labels for front-end content types.
	 *
	 * @return array<string,string>
	 */
	function reci_media_hub_submission_type_labels(): array {
		return [
			'blog'       => __('Blog Post', 'reci-media-hub'),
			'article'    => __('Magazine / Newspaper Article', 'reci-media-hub'),
			'podcast'    => __('Podcast / Audio', 'reci-media-hub'),
			'video'      => __('Video Content', 'reci-media-hub'),
			'document'   => __('Resource', 'reci-media-hub'),
			'assessment' => __('Quiz / Tool', 'reci-media-hub'),
			'exhibit'    => __('Virtual Exhibit', 'reci-media-hub'),
			'other'      => __('Other Content', 'reci-media-hub'),
		];
	}
}

if (! function_exists('reci_media_hub_submission_type_definitions')) {
	/**
	 * Front-end submission type definitions (source of truth).
	 *
	 * @return array<int,array<string,string>>
	 */
	function reci_media_hub_submission_type_definitions(): array {
		$labels = reci_media_hub_submission_type_labels();

		return [
			[
				'id'        => 'article',
				'label'     => $labels['article'] ?? __('Magazine / Newspaper Article', 'reci-media-hub'),
				'icon'      => '✦',
				'desc'      => 'Long-form or short-form written pieces exploring racial equity practices, policies, or frameworks. Feature articles, op-eds, research summaries, and investigative pieces.',
				'examples'  => 'Feature stories, policy analyses, research-based essays, case studies',
				'wordRange' => '800 – 3,000 words',
			],
			[
				'id'        => 'blog',
				'label'     => $labels['blog'] ?? __('Blog Post', 'reci-media-hub'),
				'icon'      => '◈',
				'desc'      => 'Accessible, conversational pieces that share insights, reflections, or practical guidance on advancing racial equity. Can be personal narrative or analytical.',
				'examples'  => 'Personal reflections, how-to guides, commentary, book reviews',
				'wordRange' => '400 – 1,200 words',
			],
			[
				'id'        => 'video',
				'label'     => $labels['video'] ?? __('Video Content', 'reci-media-hub'),
				'icon'      => '▶',
				'desc'      => 'Explainer videos, mini-documentaries, recorded presentations, interviews, or visual storytelling that advances racial equity consciousness.',
				'examples'  => 'Explainer videos, documentary shorts, panel recordings, testimonials',
				'wordRange' => '3 – 30 minutes',
			],
			[
				'id'        => 'podcast',
				'label'     => $labels['podcast'] ?? __('Podcast / Audio', 'reci-media-hub'),
				'icon'      => '◉',
				'desc'      => 'Audio content including interviews, discussions, storytelling, or educational episodes centered on racial equity themes.',
				'examples'  => 'Interview episodes, roundtable discussions, narrative audio, lecture recordings',
				'wordRange' => '15 – 60 minutes',
			],
			[
				'id'        => 'exhibit',
				'label'     => $labels['exhibit'] ?? __('Virtual Exhibit', 'reci-media-hub'),
				'icon'      => '◇',
				'desc'      => 'Digital exhibitions, curated visual collections, interactive timelines, or multimedia presentations that illuminate racial equity topics.',
				'examples'  => 'Photo essays, digital archives, interactive timelines, curated collections',
				'wordRange' => 'Varies by format',
			],
			[
				'id'        => 'document',
				'label'     => $labels['document'] ?? __('Resource', 'reci-media-hub'),
				'icon'      => '▣',
				'desc'      => 'Reports, PDFs, curricula, policy briefs, toolkits, and linked materials that contributors want to share as practical standalone resources.',
				'examples'  => 'Research papers, reports, curricula, policy briefs, toolkits',
				'wordRange' => 'Varies by format',
			],
			[
				'id'        => 'assessment',
				'label'     => $labels['assessment'] ?? __('Quiz / Tool', 'reci-media-hub'),
				'icon'      => '⬡',
				'desc'      => 'Self-assessments, surveys, diagnostic instruments, or interactive tools that help users evaluate their racial equity consciousness.',
				'examples'  => 'Self-reflection instruments, organizational audits, learning diagnostics, checklists',
				'wordRange' => 'Varies by format',
			],
			[
				'id'        => 'other',
				'label'     => $labels['other'] ?? __('Other Content', 'reci-media-hub'),
				'icon'      => '✧',
				'desc'      => "Infographics, curricula, toolkits, creative works, or other formats that don't fit neatly into the above categories but advance racial equity.",
				'examples'  => 'Infographics, curricula, training materials, creative works, data visualizations',
				'wordRange' => 'Varies by format',
			],
		];
	}
}

if (! function_exists('reci_media_hub_submission_supported_post_types')) {
	/**
	 * Post types that support submission source metadata.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_submission_supported_post_types(): array {
		$post_types = array_values(array_unique(array_values(reci_media_hub_submission_type_map())));
		sort($post_types);
		return $post_types;
	}
}

if (! function_exists('reci_media_hub_submission_meta_post_types')) {
	/**
	 * Post types where submission source metadata should be registered.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_submission_meta_post_types(): array {
		$post_types = reci_media_hub_submission_supported_post_types();
		$post_types[] = 'reci_author';
		$post_types[] = 'reci_document';
		$post_types = array_values(array_unique($post_types));
		sort($post_types);
		return $post_types;
	}
}

if (! function_exists('reci_media_hub_submission_taxonomies')) {
	/**
	 * Submission alignment taxonomies.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_submission_taxonomies(): array {
		return [
			'reci_sphere',
			'reci_practice_focus',
			'reci_target_audience',
		];
	}
}

if (! function_exists('reci_media_hub_submission_registered_meta_keys')) {
	/**
	 * Canonical submission meta key schema.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function reci_media_hub_submission_registered_meta_keys(): array {
		return [
			'_reci_submission_content_link' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return esc_url_raw((string) $value);
				},
			],
			'_reci_submission_file_id' => [
				'type'     => 'integer',
				'default'  => 0,
				'sanitize' => static function ($value): int {
					return absint($value);
				},
			],
			'_reci_submission_file_url' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return esc_url_raw((string) $value);
				},
			],
			'_reci_submission_source_type' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_key((string) $value);
				},
			],
			'_reci_submission_content_type' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_key((string) $value);
				},
			],
			'_reci_submission_first_name' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_last_name' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_email' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_email((string) $value);
				},
			],
			'_reci_submission_organization' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_role' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_bio' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_textarea_field((string) $value);
				},
			],
			'_reci_submission_website' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return esc_url_raw((string) $value);
				},
			],
			'_reci_submission_file_description' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_textarea_field((string) $value);
				},
			],
			'_reci_submission_submitter_user_id' => [
				'type'     => 'integer',
				'default'  => 0,
				'sanitize' => static function ($value): int {
					return absint($value);
				},
			],
			'_reci_submission_submitted_at' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_submitted_at_gmt' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_author_opt_in' => [
				'type'     => 'boolean',
				'default'  => false,
				'sanitize' => static function ($value): bool {
					return ! empty($value);
				},
			],
			'_reci_submission_location' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_text_field((string) $value);
				},
			],
			'_reci_submission_term_summary' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return sanitize_textarea_field((string) $value);
				},
			],
			'_reci_submission_payload_json' => [
				'type'     => 'string',
				'default'  => '',
				'sanitize' => static function ($value): string {
					return (string) $value;
				},
			],
		];
	}
}

if (! function_exists('reci_media_hub_submission_query_args')) {
	/**
	 * Build normalized query args for submissions.
	 *
	 * @param array<string,mixed> $filters Query filters.
	 *
	 * @return array<string,mixed>
	 */
	function reci_media_hub_submission_query_args(array $filters = []): array {
		$post_types = reci_media_hub_submission_supported_post_types();

		$args = [
			'post_type'      => $post_types,
			'post_status'    => $filters['post_status'] ?? ['pending', 'draft', 'publish'],
			'posts_per_page' => isset($filters['posts_per_page']) ? (int) $filters['posts_per_page'] : 50,
			'paged'          => isset($filters['paged']) ? max(1, (int) $filters['paged']) : 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => false,
		];

		if (! empty($filters['post_type']) && is_string($filters['post_type'])) {
			$requested_post_type = sanitize_key($filters['post_type']);
			if (in_array($requested_post_type, $post_types, true)) {
				$args['post_type'] = [$requested_post_type];
			}
		}

		if (! empty($filters['search']) && is_string($filters['search'])) {
			$args['s'] = sanitize_text_field($filters['search']);
		}

		if (! empty($filters['author_id'])) {
			$args['author'] = absint($filters['author_id']);
		}

		$meta_query = [];
		if (! empty($filters['content_type']) && is_string($filters['content_type'])) {
			$meta_query[] = [
				'key'   => '_reci_submission_content_type',
				'value' => sanitize_key($filters['content_type']),
			];
		}
		if (! empty($meta_query)) {
			$args['meta_query'] = $meta_query;
		}

		$tax_query = [];
		$tax_filter_map = [
			'reci_sphere'          => $filters['sphere_term_ids'] ?? [],
			'reci_practice_focus'  => $filters['practice_term_ids'] ?? [],
			'reci_target_audience' => $filters['audience_term_ids'] ?? [],
		];
		foreach ($tax_filter_map as $taxonomy => $terms) {
			if (! is_array($terms)) {
				continue;
			}
			$term_ids = array_values(array_filter(array_map('absint', $terms)));
			if (empty($term_ids)) {
				continue;
			}
			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $term_ids,
			];
		}
		if (! empty($tax_query)) {
			$args['tax_query'] = $tax_query;
		}

		$date_query = [];
		if (! empty($filters['date_from']) && is_string($filters['date_from'])) {
			$date_query['after'] = sanitize_text_field($filters['date_from']);
		}
		if (! empty($filters['date_to']) && is_string($filters['date_to'])) {
			$date_query['before'] = sanitize_text_field($filters['date_to']);
		}
		if (! empty($date_query)) {
			$date_query['inclusive'] = true;
			$args['date_query'] = [$date_query];
		}

		return $args;
	}
}

if (! function_exists('reci_media_hub_get_submission_posts')) {
	/**
	 * Fetch submissions with standardized filters.
	 *
	 * @param array<string,mixed> $filters Query filters.
	 *
	 * @return array<int,WP_Post>
	 */
	function reci_media_hub_get_submission_posts(array $filters = []): array {
		$query = new WP_Query(reci_media_hub_submission_query_args($filters));
		return $query->posts;
	}
}

if (! function_exists('reci_media_hub_get_submission_export_url')) {
	/**
	 * Build a signed export URL for submissions.
	 *
	 * @param array<string,mixed> $filters Export filters.
	 * @param string              $format  Export format: csv|json.
	 */
	function reci_media_hub_get_submission_export_url(array $filters = [], string $format = 'csv'): string {
		$args = [
			'action' => 'reci_export_submissions',
			'format' => in_array($format, ['csv', 'json'], true) ? $format : 'csv',
			'reci_export_submissions_nonce' => wp_create_nonce('reci_export_submissions'),
		];

		foreach (['content_type', 'post_type', 'search', 'date_from', 'date_to'] as $key) {
			if (! empty($filters[$key]) && is_string($filters[$key])) {
				$args[$key] = sanitize_text_field($filters[$key]);
			}
		}

		foreach (['sphere_term_ids', 'practice_term_ids', 'audience_term_ids'] as $key) {
			if (! empty($filters[$key]) && is_array($filters[$key])) {
				$ids = array_values(array_filter(array_map('absint', $filters[$key])));
				if (! empty($ids)) {
					$args[$key] = implode(',', $ids);
				}
			}
		}

		return add_query_arg($args, admin_url('admin-post.php'));
	}
}

if (! function_exists('reci_media_hub_register_submission_meta')) {
	/**
	 * Register submission source meta keys.
	 */
	function reci_media_hub_register_submission_meta(): void {
		$keys = reci_media_hub_submission_registered_meta_keys();

		foreach (reci_media_hub_submission_meta_post_types() as $post_type) {
			foreach ($keys as $key => $config) {
				register_post_meta(
					$post_type,
					$key,
					[
						'type'              => $config['type'],
						'single'            => true,
						'default'           => $config['default'],
						'show_in_rest'      => true,
						'sanitize_callback' => $config['sanitize'],
						'auth_callback'     => static function (): bool {
							return current_user_can('edit_posts');
						},
					]
				);
			}
		}
	}
}

if (! function_exists('reci_media_hub_get_submission_page_url')) {
	/**
	 * Resolve submission page URL.
	 */
	function reci_media_hub_get_submission_page_url(): string {
		$pages = (array) get_option('reci_pages', []);
		$page_id = isset($pages['submit']) ? absint($pages['submit']) : 0;
		if ($page_id) return get_permalink($page_id);

		$page = get_page_by_path('submit');
		if ($page instanceof WP_Post) {
			$url = get_permalink($page->ID);
			if (is_string($url) && $url !== '') {
				return $url;
			}
		}

		return home_url('/');
	}
}

if (! function_exists('reci_media_hub_handle_submission')) {
	/**
	 * Handle front-end content submissions.
	 */
	function reci_media_hub_handle_submission(): void {
		$redirect_url = reci_media_hub_get_submission_page_url();

		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( reci_get_auth_page_url( 'sign-in' ) ?: wp_login_url( $redirect_url ) );
			exit;
		}

		if ( ! function_exists( 'reci_user_is_collaborator' ) || ! reci_user_is_collaborator( get_current_user_id() ) ) {
			wp_safe_redirect( add_query_arg( 'submit_error', 'collaborator_required', $redirect_url ) );
			exit;
		}

		$nonce_raw = $_POST['reci_submit_content_nonce'] ?? '';
		$nonce = is_string($nonce_raw) ? sanitize_text_field(wp_unslash($nonce_raw)) : '';
		if (! wp_verify_nonce($nonce, 'reci_submit_content')) {
			wp_safe_redirect(add_query_arg('submit_error', 'invalid_nonce', $redirect_url));
			exit;
		}

		$raw_type = $_POST['submission_content_type'] ?? '';
		$content_type = sanitize_key(is_string($raw_type) ? wp_unslash($raw_type) : '');
		$type_map = reci_media_hub_submission_type_map();
		if (! isset($type_map[$content_type])) {
			wp_safe_redirect(add_query_arg('submit_error', 'invalid_type', $redirect_url));
			exit;
		}

		$title_raw = $_POST['submission_title'] ?? '';
		$title = sanitize_text_field(is_string($title_raw) ? wp_unslash($title_raw) : '');

		$summary_raw = $_POST['submission_summary'] ?? '';
		$summary = sanitize_textarea_field(is_string($summary_raw) ? wp_unslash($summary_raw) : '');

		$details_raw = $_POST['submission_details'] ?? '';
		$details = wp_kses_post(is_string($details_raw) ? wp_unslash($details_raw) : '');

		$link_raw = $_POST['submission_content_link'] ?? '';
		$content_link = esc_url_raw(is_string($link_raw) ? wp_unslash($link_raw) : '');

		if ($title === '' || $summary === '') {
			wp_safe_redirect(add_query_arg('submit_error', 'missing_fields', $redirect_url));
			exit;
		}

		$post_type = $type_map[$content_type];
		$post_data = [
			'post_type'    => $post_type,
			'post_status'  => 'pending',
			'post_title'   => $title,
			'post_excerpt' => $summary,
			'post_content' => $details,
		];

		$current_user_id = get_current_user_id();
		if ($current_user_id > 0) {
			$post_data['post_author'] = $current_user_id;
		}

		$post_id = wp_insert_post($post_data, true);
		if (is_wp_error($post_id) || ! $post_id) {
			wp_safe_redirect(add_query_arg('submit_error', 'save_failed', $redirect_url));
			exit;
		}

		update_post_meta($post_id, '_reci_submission_submitted_at', current_time('mysql'));
		update_post_meta($post_id, '_reci_submission_submitted_at_gmt', current_time('mysql', true));
		if ($current_user_id > 0) {
			update_post_meta($post_id, '_reci_submission_submitter_user_id', $current_user_id);
		}

		$term_assignments = [];
		foreach (reci_media_hub_submission_taxonomies() as $taxonomy) {
			$key = $taxonomy . '_terms';
			$raw_terms = $_POST[$key] ?? [];
			$term_ids = [];
			if (is_array($raw_terms)) {
				$term_ids = array_values(array_filter(array_map('absint', wp_unslash($raw_terms))));
			}

			$name_key = $taxonomy . '_term_names';
			$raw_term_names = $_POST[$name_key] ?? [];
			if (is_string($raw_term_names)) {
				$raw_term_names = [$raw_term_names];
			}

			if (is_array($raw_term_names)) {
				foreach (wp_unslash($raw_term_names) as $raw_term_name) {
					if (! is_string($raw_term_name)) {
						continue;
					}
					$term_name = sanitize_text_field($raw_term_name);
					if ($term_name === '') {
						continue;
					}

					$existing = term_exists($term_name, $taxonomy);
					if (is_array($existing) && isset($existing['term_id'])) {
						$term_ids[] = (int) $existing['term_id'];
						continue;
					}
					if (is_int($existing) && $existing > 0) {
						$term_ids[] = $existing;
						continue;
					}

					$created = wp_insert_term($term_name, $taxonomy);
					if (! is_wp_error($created) && isset($created['term_id'])) {
						$term_ids[] = (int) $created['term_id'];
					}
				}
			}

			$term_ids = array_values(array_unique(array_filter(array_map('absint', $term_ids))));
			if (! empty($term_ids)) {
				wp_set_object_terms($post_id, $term_ids, $taxonomy, false);
				$term_assignments[$taxonomy] = $term_ids;
			}
		}

		if ($content_link !== '') {
			update_post_meta($post_id, '_reci_submission_content_link', $content_link);
		}

		update_post_meta($post_id, '_reci_submission_content_type', $content_type);

		$contributor_fields = [
			'_reci_submission_first_name'   => 'submission_first_name',
			'_reci_submission_last_name'    => 'submission_last_name',
			'_reci_submission_email'        => 'submission_email',
			'_reci_submission_organization' => 'submission_organization',
			'_reci_submission_role'         => 'submission_role',
			'_reci_submission_bio'          => 'submission_bio',
			'_reci_submission_website'      => 'submission_website',
			'_reci_submission_file_description' => 'submission_file_description',
		];
		$contributor_meta_values = [];

		foreach ($contributor_fields as $meta_key => $field_name) {
			$value_raw = $_POST[$field_name] ?? '';
			$value = is_string($value_raw) ? wp_unslash($value_raw) : '';

			if ($meta_key === '_reci_submission_email') {
				$sanitized = sanitize_email($value);
			} elseif ($meta_key === '_reci_submission_website') {
				$sanitized = esc_url_raw($value);
			} elseif ($meta_key === '_reci_submission_bio' || $meta_key === '_reci_submission_file_description') {
				$sanitized = sanitize_textarea_field($value);
			} else {
				$sanitized = sanitize_text_field($value);
			}

			if ($sanitized !== '') {
				update_post_meta($post_id, $meta_key, $sanitized);
				$contributor_meta_values[$meta_key] = $sanitized;
			}
		}

		$author_opt_in_raw = $_POST['submission_author_opt_in'] ?? '';
		$author_opt_in = is_string($author_opt_in_raw) ? rest_sanitize_boolean(wp_unslash($author_opt_in_raw)) : false;
		if ($author_opt_in) {
			update_post_meta($post_id, '_reci_submission_author_opt_in', true);

			$author_name = trim(
				($contributor_meta_values['_reci_submission_first_name'] ?? '') . ' ' .
				($contributor_meta_values['_reci_submission_last_name'] ?? '')
			);
			$author_bio = $contributor_meta_values['_reci_submission_bio'] ?? '';

			if ($author_name !== '' && function_exists('reci_media_hub_create_or_get_author_profile')) {
				$title = $contributor_meta_values['_reci_submission_role'] ?? '';
				$profile_id = reci_media_hub_create_or_get_author_profile($author_name, $title, $author_bio);
				if ($profile_id > 0) {
					update_post_meta($post_id, '_reci_display_author_profile_id', $profile_id);
				}
			}
		}

		$location_raw = $_POST['submission_location'] ?? '';
		$location = is_string($location_raw) ? sanitize_text_field(wp_unslash($location_raw)) : '';
		if ($location !== '') {
			update_post_meta($post_id, '_reci_submission_location', $location);
		}

		$file_upload_failed = false;
		$file_id = 0;
		$file_url = '';
		if (
			isset($_FILES['submission_file']) &&
			is_array($_FILES['submission_file']) &&
			! empty($_FILES['submission_file']['name']) &&
			((int) ($_FILES['submission_file']['error'] ?? 0)) === UPLOAD_ERR_OK
		) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			$attachment_id = media_handle_upload('submission_file', $post_id);
			if (! is_wp_error($attachment_id) && $attachment_id > 0) {
				$file_id  = (int) $attachment_id;
				$file_url = (string) wp_get_attachment_url($attachment_id);
			} else {
				$file_upload_failed = true;
			}
		}

		if ($file_id > 0) {
			update_post_meta($post_id, '_reci_submission_file_id', $file_id);
		}
		if ($file_url !== '') {
			update_post_meta($post_id, '_reci_submission_file_url', esc_url_raw($file_url));
		}

		$source_type = '';
		if ($content_link !== '' && $file_id > 0) {
			$source_type = 'link_file';
		} elseif ($content_link !== '') {
			$source_type = 'link';
		} elseif ($file_id > 0) {
			$source_type = 'file';
		}
		if ($source_type !== '') {
			update_post_meta($post_id, '_reci_submission_source_type', $source_type);
		}

		$term_summary = [];
		foreach (reci_media_hub_submission_taxonomies() as $taxonomy) {
			$term_names = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'names']);
			if (is_wp_error($term_names) || ! is_array($term_names) || empty($term_names)) {
				continue;
			}
			$term_summary[$taxonomy] = array_values(array_filter(array_map(static function ($name): string {
				return is_string($name) ? trim($name) : '';
			}, $term_names)));
		}
		if (! empty($term_summary)) {
			update_post_meta($post_id, '_reci_submission_term_summary', wp_json_encode($term_summary));
		}

		$payload_snapshot = [
			'content_type'   => $content_type,
			'post_type'      => $post_type,
			'title'          => $title,
			'summary'        => $summary,
			'content_link'   => $content_link,
			'source_type'    => $source_type,
			'submitter_user' => $current_user_id > 0 ? $current_user_id : null,
			'contributor'    => [
				'first_name'   => $contributor_meta_values['_reci_submission_first_name'] ?? '',
				'last_name'    => $contributor_meta_values['_reci_submission_last_name'] ?? '',
				'email'        => $contributor_meta_values['_reci_submission_email'] ?? '',
				'organization' => $contributor_meta_values['_reci_submission_organization'] ?? '',
				'role'         => $contributor_meta_values['_reci_submission_role'] ?? '',
				'website'      => $contributor_meta_values['_reci_submission_website'] ?? '',
			],
			'taxonomies'     => $term_summary,
			'file'           => [
				'id'  => $file_id,
				'url' => $file_url,
			],
		];
		update_post_meta($post_id, '_reci_submission_payload_json', wp_json_encode($payload_snapshot));
		reci_media_hub_send_submission_notifications($post_id, $payload_snapshot, $contributor_meta_values);

		if (
			$current_user_id > 0 &&
			! empty($term_assignments) &&
			function_exists('reci_media_hub_get_author_profile_by_user_id')
		) {
			$profile_id = reci_media_hub_get_author_profile_by_user_id($current_user_id);
			if ($profile_id > 0) {
				foreach ($term_assignments as $taxonomy => $term_ids) {
					wp_set_object_terms($profile_id, $term_ids, $taxonomy, false);
				}
			}
		}

		$redirect_args = [
			'submit_success' => '1',
			'submission_id'  => (string) $post_id,
		];
		if ($file_upload_failed) {
			$redirect_args['submit_warning'] = 'file_upload_failed';
		}

		wp_safe_redirect(add_query_arg($redirect_args, $redirect_url));
		exit;
	}
}

if (! function_exists('reci_media_hub_add_submission_source_metaboxes')) {
	/**
	 * Add source metabox to supported content post types.
	 */
	function reci_media_hub_add_submission_source_metaboxes(): void {
		foreach (reci_media_hub_submission_supported_post_types() as $post_type) {
			add_meta_box(
				'reci-submission-source',
				__('Submission Source', 'reci-media-hub'),
				'reci_media_hub_render_submission_source_metabox',
				$post_type,
				'side',
				'high'
			);
		}
	}
}

if (! function_exists('reci_media_hub_render_submission_source_metabox')) {
	/**
	 * Render submission source metabox.
	 */
	function reci_media_hub_render_submission_source_metabox(WP_Post $post): void {
		wp_nonce_field('reci_save_submission_source', 'reci_submission_source_nonce');

		$content_link = (string) get_post_meta($post->ID, '_reci_submission_content_link', true);
		$file_url     = (string) get_post_meta($post->ID, '_reci_submission_file_url', true);
		$file_id      = (int) get_post_meta($post->ID, '_reci_submission_file_id', true);
		$content_type = (string) get_post_meta($post->ID, '_reci_submission_content_type', true);
		?>
		<p>
			<label for="reci_submission_content_link"><strong><?php esc_html_e('Content Link', 'reci-media-hub'); ?></strong></label>
			<input
				type="url"
				id="reci_submission_content_link"
				name="reci_submission_content_link"
				value="<?php echo esc_attr($content_link); ?>"
				style="width:100%;margin-top:6px;"
			/>
		</p>
		<p>
			<label for="reci_submission_file_url"><strong><?php esc_html_e('File URL', 'reci-media-hub'); ?></strong></label>
			<input
				type="url"
				id="reci_submission_file_url"
				name="reci_submission_file_url"
				value="<?php echo esc_attr($file_url); ?>"
				style="width:100%;margin-top:6px;"
			/>
		</p>
		<p>
			<label for="reci_submission_file_id"><strong><?php esc_html_e('File Attachment ID', 'reci-media-hub'); ?></strong></label>
			<input
				type="number"
				min="0"
				id="reci_submission_file_id"
				name="reci_submission_file_id"
				value="<?php echo esc_attr((string) $file_id); ?>"
				style="width:100%;margin-top:6px;"
			/>
		</p>
		<hr />
		<p><strong><?php esc_html_e('Submission Content Type:', 'reci-media-hub'); ?></strong> <?php echo esc_html($content_type !== '' ? $content_type : '-'); ?></p>
		<?php
	}
}

if (! function_exists('reci_media_hub_save_submission_source_meta')) {
	/**
	 * Save source metabox fields.
	 */
	function reci_media_hub_save_submission_source_meta(int $post_id): void {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
			return;
		}

		$nonce_raw = $_POST['reci_submission_source_nonce'] ?? '';
		$nonce = is_string($nonce_raw) ? sanitize_text_field(wp_unslash($nonce_raw)) : '';
		if (! wp_verify_nonce($nonce, 'reci_save_submission_source')) {
			return;
		}

		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		$content_link_raw = $_POST['reci_submission_content_link'] ?? '';
		$content_link = esc_url_raw(is_string($content_link_raw) ? wp_unslash($content_link_raw) : '');

		$file_url_raw = $_POST['reci_submission_file_url'] ?? '';
		$file_url = esc_url_raw(is_string($file_url_raw) ? wp_unslash($file_url_raw) : '');

		$file_id_raw = $_POST['reci_submission_file_id'] ?? 0;
		$file_id = absint($file_id_raw);

		if ($content_link !== '') {
			update_post_meta($post_id, '_reci_submission_content_link', $content_link);
		} else {
			delete_post_meta($post_id, '_reci_submission_content_link');
		}

		if ($file_url !== '') {
			update_post_meta($post_id, '_reci_submission_file_url', $file_url);
		} else {
			delete_post_meta($post_id, '_reci_submission_file_url');
		}

		if ($file_id > 0) {
			update_post_meta($post_id, '_reci_submission_file_id', $file_id);
		} else {
			delete_post_meta($post_id, '_reci_submission_file_id');
		}

		$source_type = '';
		if ($content_link !== '' && $file_id > 0) {
			$source_type = 'link_file';
		} elseif ($content_link !== '') {
			$source_type = 'link';
		} elseif ($file_id > 0 || $file_url !== '') {
			$source_type = 'file';
		}

		if ($source_type !== '') {
			update_post_meta($post_id, '_reci_submission_source_type', $source_type);
		} else {
			delete_post_meta($post_id, '_reci_submission_source_type');
		}
	}
}

if (! function_exists('reci_media_hub_parse_id_filter_list')) {
	/**
	 * Parse a comma-separated or array value into integer IDs.
	 *
	 * @param mixed $value Raw request value.
	 *
	 * @return array<int,int>
	 */
	function reci_media_hub_parse_id_filter_list($value): array {
		if (is_string($value)) {
			$value = explode(',', $value);
		}
		if (! is_array($value)) {
			return [];
		}
		return array_values(array_filter(array_map('absint', $value)));
	}
}

if (! function_exists('reci_media_hub_submission_export_row')) {
	/**
	 * Build an export-ready row for a submission post.
	 *
	 * @param WP_Post $post Submission post.
	 *
	 * @return array<string,mixed>
	 */
	function reci_media_hub_submission_export_row(WP_Post $post): array {
		$taxonomy_names = [];
		foreach (reci_media_hub_submission_taxonomies() as $taxonomy) {
			$terms = wp_get_post_terms($post->ID, $taxonomy, ['fields' => 'names']);
			$taxonomy_names[$taxonomy] = (is_wp_error($terms) || ! is_array($terms))
				? ''
				: implode(' | ', array_filter(array_map(static function ($value): string {
					return is_string($value) ? trim($value) : '';
				}, $terms)));
		}

		return [
			'post_id'            => $post->ID,
			'post_type'          => $post->post_type,
			'post_status'        => $post->post_status,
			'submitted_at'       => (string) get_post_meta($post->ID, '_reci_submission_submitted_at', true),
			'title'              => $post->post_title,
			'abstract'           => $post->post_excerpt,
			'content_type'       => (string) get_post_meta($post->ID, '_reci_submission_content_type', true),
			'content_link'       => (string) get_post_meta($post->ID, '_reci_submission_content_link', true),
			'source_type'        => (string) get_post_meta($post->ID, '_reci_submission_source_type', true),
			'first_name'         => (string) get_post_meta($post->ID, '_reci_submission_first_name', true),
			'last_name'          => (string) get_post_meta($post->ID, '_reci_submission_last_name', true),
			'email'              => (string) get_post_meta($post->ID, '_reci_submission_email', true),
			'organization'       => (string) get_post_meta($post->ID, '_reci_submission_organization', true),
			'role'               => (string) get_post_meta($post->ID, '_reci_submission_role', true),
			'website'            => (string) get_post_meta($post->ID, '_reci_submission_website', true),
			'file_id'            => (int) get_post_meta($post->ID, '_reci_submission_file_id', true),
			'file_url'           => (string) get_post_meta($post->ID, '_reci_submission_file_url', true),
			'spheres'            => $taxonomy_names['reci_sphere'] ?? '',
			'practice_focus'     => $taxonomy_names['reci_practice_focus'] ?? '',
			'target_audience'    => $taxonomy_names['reci_target_audience'] ?? '',
			'location'           => (string) get_post_meta($post->ID, '_reci_submission_location', true),
			'author_opt_in'      => (int) get_post_meta($post->ID, '_reci_submission_author_opt_in', true),
		];
	}
}

if (! function_exists('reci_media_hub_export_submissions')) {
	/**
	 * Export submissions in CSV/JSON for reporting.
	 */
	function reci_media_hub_export_submissions(): void {
		if (! current_user_can('edit_others_posts')) {
			wp_die(esc_html__('You are not allowed to export submissions.', 'reci-media-hub'));
		}

		$nonce_raw = $_GET['reci_export_submissions_nonce'] ?? '';
		$nonce = is_string($nonce_raw) ? sanitize_text_field(wp_unslash($nonce_raw)) : '';
		if (! wp_verify_nonce($nonce, 'reci_export_submissions')) {
			wp_die(esc_html__('Invalid export nonce.', 'reci-media-hub'));
		}

		$format_raw = $_GET['format'] ?? 'csv';
		$format = is_string($format_raw) ? sanitize_key(wp_unslash($format_raw)) : 'csv';
		if (! in_array($format, ['csv', 'json'], true)) {
			$format = 'csv';
		}

		$filters = [
			'posts_per_page'    => -1,
			'post_status'       => ! empty($_GET['post_status']) ? sanitize_text_field((string) wp_unslash($_GET['post_status'])) : ['pending', 'draft', 'publish'],
			'content_type'      => ! empty($_GET['content_type']) ? sanitize_key((string) wp_unslash($_GET['content_type'])) : '',
			'post_type'         => ! empty($_GET['post_type']) ? sanitize_key((string) wp_unslash($_GET['post_type'])) : '',
			'search'            => ! empty($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '',
			'sphere_term_ids'   => reci_media_hub_parse_id_filter_list($_GET['sphere_term_ids'] ?? []),
			'practice_term_ids' => reci_media_hub_parse_id_filter_list($_GET['practice_term_ids'] ?? []),
			'audience_term_ids' => reci_media_hub_parse_id_filter_list($_GET['audience_term_ids'] ?? []),
			'date_from'         => ! empty($_GET['date_from']) ? sanitize_text_field((string) wp_unslash($_GET['date_from'])) : '',
			'date_to'           => ! empty($_GET['date_to']) ? sanitize_text_field((string) wp_unslash($_GET['date_to'])) : '',
		];

		$posts = reci_media_hub_get_submission_posts($filters);
		$rows = array_map('reci_media_hub_submission_export_row', $posts);
		$stamp = gmdate('Ymd-His');

		nocache_headers();
		if ($format === 'json') {
			header('Content-Type: application/json; charset=utf-8');
			header('Content-Disposition: attachment; filename="reci-submissions-' . $stamp . '.json"');
			echo wp_json_encode($rows, JSON_PRETTY_PRINT);
			exit;
		}

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="reci-submissions-' . $stamp . '.csv"');

		$output = fopen('php://output', 'w');
		if (! $output) {
			wp_die(esc_html__('Unable to stream export output.', 'reci-media-hub'));
		}

		$headers = [
			'post_id',
			'post_type',
			'post_status',
			'submitted_at',
			'title',
			'abstract',
			'content_type',
			'content_link',
			'source_type',
			'first_name',
			'last_name',
			'email',
			'organization',
			'role',
			'website',
			'file_id',
			'file_url',
			'spheres',
			'practice_focus',
			'target_audience',
			'location',
			'author_opt_in',
		];
		fputcsv($output, $headers);

		foreach ($rows as $row) {
			$values = [];
			foreach ($headers as $header_key) {
				$values[] = $row[$header_key] ?? '';
			}
			fputcsv($output, $values);
		}

		fclose($output);
		exit;
	}
}

if (! function_exists('reci_media_hub_send_submission_notifications')) {
	/**
	 * Send submitter and admin email notifications for a new submission.
	 *
	 * @param int                  $post_id         Submission post ID.
	 * @param array<string,mixed>  $payload_snapshot Normalized snapshot payload.
	 * @param array<string,string> $contributor_meta Contributor metadata map.
	 */
	function reci_media_hub_send_submission_notifications(int $post_id, array $payload_snapshot, array $contributor_meta): void {
		$post = get_post($post_id);
		if (! ($post instanceof WP_Post)) {
			return;
		}

		$site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
		$post_type_object = get_post_type_object($post->post_type);
		$post_type_label = $post_type_object && ! empty($post_type_object->labels->singular_name)
			? (string) $post_type_object->labels->singular_name
			: (string) $post->post_type;
		$edit_url = get_edit_post_link($post_id, '');
		$submission_url = reci_media_hub_get_submission_page_url();

		$first_name = (string) ($contributor_meta['_reci_submission_first_name'] ?? '');
		$last_name = (string) ($contributor_meta['_reci_submission_last_name'] ?? '');
		$full_name = trim($first_name . ' ' . $last_name);
		$display_name = $full_name !== '' ? $full_name : __('Contributor', 'reci-media-hub');

		$submitter_email = sanitize_email((string) ($contributor_meta['_reci_submission_email'] ?? ''));
		$admin_email = sanitize_email((string) get_option('admin_email'));
		$content_type = (string) ($payload_snapshot['content_type'] ?? '');
		$content_link = (string) ($payload_snapshot['content_link'] ?? '');

		$headers = ['Content-Type: text/plain; charset=UTF-8'];

		if (is_email($submitter_email)) {
			$submitter_subject = sprintf(
				/* translators: %s site name */
				__('[%s] Submission Received', 'reci-media-hub'),
				$site_name
			);
			$submitter_message = implode("\n", [
				sprintf(__('Hi %s,', 'reci-media-hub'), $display_name),
				'',
				__('Thank you for your submission. We have received it and queued it for editorial review.', 'reci-media-hub'),
				'',
				sprintf(__('Title: %s', 'reci-media-hub'), (string) $post->post_title),
				sprintf(__('Content Type: %s', 'reci-media-hub'), $content_type !== '' ? $content_type : $post_type_label),
				__('Status: Pending Review', 'reci-media-hub'),
				'',
				__('We will follow up after review.', 'reci-media-hub'),
				'',
				sprintf(__('Submission page: %s', 'reci-media-hub'), $submission_url),
			]);
			wp_mail($submitter_email, $submitter_subject, $submitter_message, $headers);
		}

		if (is_email($admin_email)) {
			$admin_subject = sprintf(
				/* translators: %s site name */
				__('[%s] New Content Submission', 'reci-media-hub'),
				$site_name
			);
			$admin_message_lines = [
				__('A new submission has been received.', 'reci-media-hub'),
				'',
				sprintf(__('Title: %s', 'reci-media-hub'), (string) $post->post_title),
				sprintf(__('Post Type: %s', 'reci-media-hub'), $post_type_label),
				sprintf(__('Submission Content Type: %s', 'reci-media-hub'), $content_type !== '' ? $content_type : '-'),
				sprintf(__('Status: %s', 'reci-media-hub'), (string) $post->post_status),
				sprintf(__('Contributor: %s', 'reci-media-hub'), $display_name),
				sprintf(__('Contributor Email: %s', 'reci-media-hub'), $submitter_email !== '' ? $submitter_email : '-'),
			];
			if ($content_link !== '') {
				$admin_message_lines[] = sprintf(__('Content Link: %s', 'reci-media-hub'), $content_link);
			}
			if (is_string($edit_url) && $edit_url !== '') {
				$admin_message_lines[] = '';
				$admin_message_lines[] = sprintf(__('Edit Submission: %s', 'reci-media-hub'), $edit_url);
			}
			wp_mail($admin_email, $admin_subject, implode("\n", $admin_message_lines), $headers);
		}
	}
}

if (! function_exists('reci_media_hub_register_submission_admin_page')) {
	/**
	 * Register a consolidated admin page for submissions.
	 */
	function reci_media_hub_register_submission_admin_page(): void {
		add_management_page(
			__('Submissions', 'reci-media-hub'),
			__('Submissions', 'reci-media-hub'),
			'edit_posts',
			'reci-submissions',
			'reci_media_hub_render_submission_admin_page'
		);
	}
}

if (! function_exists('reci_media_hub_render_submission_admin_page')) {
	/**
	 * Render the consolidated submissions admin page.
	 */
	function reci_media_hub_render_submission_admin_page(): void {
		if (! current_user_can('edit_posts')) {
			wp_die(esc_html__('You are not allowed to view submissions.', 'reci-media-hub'));
		}

		$post_status = isset($_GET['post_status']) && is_string($_GET['post_status'])
			? sanitize_key(wp_unslash($_GET['post_status']))
			: '';
		$post_type = isset($_GET['post_type']) && is_string($_GET['post_type'])
			? sanitize_key(wp_unslash($_GET['post_type']))
			: '';
		$search = isset($_GET['s']) && is_string($_GET['s'])
			? sanitize_text_field(wp_unslash($_GET['s']))
			: '';

		$filters = [
			'posts_per_page' => 100,
			'post_status'    => $post_status !== '' ? $post_status : ['pending', 'draft', 'publish'],
			'post_type'      => $post_type,
			'search'         => $search,
		];
		if (! current_user_can('edit_others_posts')) {
			$filters['author_id'] = get_current_user_id();
		}

		$posts = reci_media_hub_get_submission_posts($filters);
		$post_types = reci_media_hub_submission_supported_post_types();

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__('Submissions', 'reci-media-hub') . '</h1>';
		echo '<p>' . esc_html__('Unified queue for content submissions across mapped post types.', 'reci-media-hub') . '</p>';

		echo '<form method="get" style="margin: 12px 0 18px;">';
		echo '<input type="hidden" name="page" value="reci-submissions" />';
		echo '<input type="search" name="s" value="' . esc_attr($search) . '" placeholder="' . esc_attr__('Search title', 'reci-media-hub') . '" style="min-width:220px;" /> ';
		echo '<select name="post_status">';
		$status_options = [
			''        => __('All statuses', 'reci-media-hub'),
			'pending' => __('Pending', 'reci-media-hub'),
			'draft'   => __('Draft', 'reci-media-hub'),
			'publish' => __('Published', 'reci-media-hub'),
		];
		foreach ($status_options as $value => $label) {
			echo '<option value="' . esc_attr($value) . '"' . selected($post_status, $value, false) . '>' . esc_html($label) . '</option>';
		}
		echo '</select> ';
		echo '<select name="post_type">';
		echo '<option value="">' . esc_html__('All post types', 'reci-media-hub') . '</option>';
		foreach ($post_types as $type) {
			$type_object = get_post_type_object($type);
			$type_label = $type_object && ! empty($type_object->labels->singular_name) ? $type_object->labels->singular_name : $type;
			echo '<option value="' . esc_attr($type) . '"' . selected($post_type, $type, false) . '>' . esc_html((string) $type_label) . '</option>';
		}
		echo '</select> ';
		echo '<button type="submit" class="button button-primary">' . esc_html__('Filter', 'reci-media-hub') . '</button>';
		echo '</form>';

		$csv_url = reci_media_hub_get_submission_export_url($filters, 'csv');
		$json_url = reci_media_hub_get_submission_export_url($filters, 'json');
		echo '<p>';
		echo '<a class="button" href="' . esc_url($csv_url) . '">' . esc_html__('Export CSV', 'reci-media-hub') . '</a> ';
		echo '<a class="button" href="' . esc_url($json_url) . '">' . esc_html__('Export JSON', 'reci-media-hub') . '</a>';
		echo '</p>';

		if (empty($posts)) {
			echo '<p>' . esc_html__('No submissions found for the current filters.', 'reci-media-hub') . '</p>';
			echo '</div>';
			return;
		}

		echo '<table class="widefat striped">';
		echo '<thead><tr>';
		echo '<th>' . esc_html__('Date', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Title', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Status', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Post Type', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Content Type', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Contributor', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Email', 'reci-media-hub') . '</th>';
		echo '<th>' . esc_html__('Actions', 'reci-media-hub') . '</th>';
		echo '</tr></thead><tbody>';

		foreach ($posts as $post) {
			$post_type_object = get_post_type_object($post->post_type);
			$post_type_label = $post_type_object && ! empty($post_type_object->labels->singular_name)
				? (string) $post_type_object->labels->singular_name
				: (string) $post->post_type;

			$first_name = (string) get_post_meta($post->ID, '_reci_submission_first_name', true);
			$last_name = (string) get_post_meta($post->ID, '_reci_submission_last_name', true);
			$contributor = trim($first_name . ' ' . $last_name);
			$email = (string) get_post_meta($post->ID, '_reci_submission_email', true);
			$content_type = (string) get_post_meta($post->ID, '_reci_submission_content_type', true);
			$edit_url = get_edit_post_link($post->ID, '');

			echo '<tr>';
			echo '<td>' . esc_html(get_the_date('Y-m-d H:i', $post)) . '</td>';
			echo '<td><strong>' . esc_html($post->post_title) . '</strong></td>';
			echo '<td>' . esc_html($post->post_status) . '</td>';
			echo '<td>' . esc_html($post_type_label) . '</td>';
			echo '<td>' . esc_html($content_type !== '' ? $content_type : '-') . '</td>';
			echo '<td>' . esc_html($contributor !== '' ? $contributor : '-') . '</td>';
			echo '<td>' . esc_html($email !== '' ? $email : '-') . '</td>';
			echo '<td>';
			if (is_string($edit_url) && $edit_url !== '') {
				echo '<a class="button button-small" href="' . esc_url($edit_url) . '">' . esc_html__('Open', 'reci-media-hub') . '</a>';
			}
			echo '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
	}
}

add_action('init', 'reci_media_hub_register_submission_meta');
add_action('admin_post_reci_submit_content', 'reci_media_hub_handle_submission');
add_action('admin_post_nopriv_reci_submit_content', 'reci_media_hub_handle_submission');
add_action('admin_post_reci_export_submissions', 'reci_media_hub_export_submissions');
add_action('add_meta_boxes', 'reci_media_hub_add_submission_source_metaboxes');
add_action('save_post', 'reci_media_hub_save_submission_source_meta');
add_action('admin_menu', 'reci_media_hub_register_submission_admin_page');
