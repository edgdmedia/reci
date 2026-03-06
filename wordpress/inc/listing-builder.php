<?php
/**
 * Reusable listing builder for RECI templates and shortcodes.
 *
 * Usage in PHP templates:
 *   echo reci_media_hub_render_listing([
 *     'post_type'      => 'reci_article',
 *     'posts_per_page' => 4,
 *     'orderby'        => 'date',
 *     'order'          => 'DESC',
 *     'listing_style'  => 'content_compact',
 *   ]);
 *
 * Usage with pagination + filtering:
 *   echo reci_media_hub_render_listing([
 *     'post_type'         => 'reci_article',
 *     'posts_per_page'    => 9,
 *     'listing_style'     => 'archive_grid_card',
 *     'enable_pagination' => true,
 *     'filter_search_param' => 'search',
 *     'filter_taxonomies' => [
 *       'category' => [ 'param' => 'topic', 'field' => 'slug' ],
 *       'reci_location' => [ 'param' => 'location', 'field' => 'slug' ],
 *     ],
 *   ]);
 *
 * Usage via shortcode:
 *   [reci_post_listing post_type="reci_article" posts_per_page="4" orderby="date" order="DESC" listing_style="content_compact"]
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('RECI_Media_Hub_Listing_Builder')) {
	class RECI_Media_Hub_Listing_Builder {
		/**
		 * Register shortcode.
		 */
		public static function init(): void {
			add_shortcode('reci_post_listing', [__CLASS__, 'shortcode']);
		}

		/**
		 * Render listing with query and mapped item payloads for listing components.
		 *
		 * @param array<string,mixed> $config Listing configuration.
		 */
		public static function render(array $config = []): string {
			$config         = self::normalize_config($config);
			$component_info = self::resolve_component(self::resolve_component_key($config));
			$query          = self::query($config);
			$posts          = $query->posts;
			$wrapper_tag    = self::sanitize_wrapper_tag($config['wrapper_tag']);
			$wrapper_class  = trim((string) $config['wrapper_class']);
			$item_class     = trim((string) $config['item_wrapper_class']);
			$divider_class  = trim((string) $config['divider_class']);
			$empty_message  = (string) $config['empty_message'];
			$item_overrides = is_array($config['item_overrides']) ? $config['item_overrides'] : [];

			ob_start();

			printf(
				'<%1$s%2$s>',
				esc_attr($wrapper_tag),
				$wrapper_class ? ' class="' . esc_attr($wrapper_class) . '"' : ''
			);

			if (empty($posts)) {
				if ($empty_message !== '') {
					printf('<p class="reci-listing-empty">%s</p>', esc_html($empty_message));
				}
			} else {
				foreach ($posts as $index => $post) {
					if (! $post instanceof WP_Post) {
						continue;
					}

					$item_args = self::map_post_to_item($post, $component_info['variant'], $config);
					if (! empty($item_overrides)) {
						$item_args = array_merge($item_args, $item_overrides);
					}

					if ($item_class !== '') {
						printf('<div class="%s">', esc_attr($item_class));
					}

					get_template_part($component_info['template_part'], null, $item_args);

					if ($item_class !== '') {
						echo '</div>';
					}

					if ($divider_class !== '' && $index < (count($posts) - 1)) {
						printf('<div class="%s"></div>', esc_attr($divider_class));
					}
				}
			}

			printf('</%s>', esc_attr($wrapper_tag));
			echo self::render_pagination($query, $config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			return (string) ob_get_clean();
		}

		/**
		 * Return listing query only.
		 *
		 * @param array<string,mixed> $config Listing configuration.
		 */
		public static function get_query(array $config = []): WP_Query {
			return self::query(self::normalize_config($config));
		}

		/**
		 * Render pagination markup from a query and listing config.
		 *
		 * @param array<string,mixed> $config Listing configuration.
		 */
		public static function pagination(WP_Query $query, array $config = []): string {
			return self::render_pagination($query, self::normalize_config($config));
		}

		/**
		 * Echo listing directly.
		 *
		 * @param array<string,mixed> $config Listing configuration.
		 */
		public static function output(array $config = []): void {
			echo self::render($config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Shortcode callback.
		 *
		 * @param array<string,string>|string $atts Shortcode attributes.
		 */
		public static function shortcode($atts = []): string {
			$atts = shortcode_atts(
				[
					'post_type'              => 'post',
					'posts_per_page'         => '3',
					'order'                  => 'DESC',
					'orderby'                => 'date',
					'meta_key'               => '',
					'meta_type'              => '',
					'offset'                 => '0',
					'listing_style'          => '',
					'component'              => 'post-item-compact',
					'wrapper_tag'            => 'div',
					'wrapper_class'          => '',
					'item_wrapper_class'     => '',
					'divider_class'          => '',
					'empty_message'          => '',
					'fallback_image'         => 'https://placehold.co/460x232',
					'button_label'           => 'Start quiz',
					'enable_pagination'      => '0',
					'pagination_param'       => 'paged',
					'pagination_wrapper_class' => 'mt-8 flex items-center justify-center gap-2',
					'pagination_item_class'  => '',
					'pagination_current_class' => '',
					'filter_search_param'    => '',
					'filter_author_param'    => '',
				],
				is_array($atts) ? $atts : [],
				'reci_post_listing'
			);

			return self::render(
				[
					'post_type'                => (string) $atts['post_type'],
					'posts_per_page'           => (int) $atts['posts_per_page'],
					'order'                    => (string) $atts['order'],
					'orderby'                  => (string) $atts['orderby'],
					'meta_key'                 => (string) $atts['meta_key'],
					'meta_type'                => (string) $atts['meta_type'],
					'offset'                   => (int) $atts['offset'],
					'listing_style'            => (string) $atts['listing_style'],
					'component'                => (string) $atts['component'],
					'wrapper_tag'              => (string) $atts['wrapper_tag'],
					'wrapper_class'            => (string) $atts['wrapper_class'],
					'item_wrapper_class'       => (string) $atts['item_wrapper_class'],
					'divider_class'            => (string) $atts['divider_class'],
					'empty_message'            => (string) $atts['empty_message'],
					'fallback_image'           => (string) $atts['fallback_image'],
					'button_label'             => (string) $atts['button_label'],
					'enable_pagination'        => in_array((string) $atts['enable_pagination'], ['1', 'true', 'yes'], true),
					'pagination_param'         => (string) $atts['pagination_param'],
					'pagination_wrapper_class' => (string) $atts['pagination_wrapper_class'],
					'pagination_item_class'    => (string) $atts['pagination_item_class'],
					'pagination_current_class' => (string) $atts['pagination_current_class'],
					'filter_search_param'      => (string) $atts['filter_search_param'],
					'filter_author_param'      => (string) $atts['filter_author_param'],
				]
			);
		}

		/**
		 * Default + sanitized listing config.
		 *
		 * @param array<string,mixed> $config Raw config.
		 * @return array<string,mixed>
		 */
		protected static function normalize_config(array $config): array {
			$defaults = [
				'post_type'                => 'post',
				'posts_per_page'           => 3,
				'order'                    => 'DESC',
				'orderby'                  => 'date',
				'meta_key'                 => '',
				'meta_type'                => '',
				'offset'                   => 0,
				'post_status'              => 'publish',
				'post__in'                 => [],
				'post__not_in'             => [],
				'tax_query'                => [],
				'meta_query'               => [],
				'author'                   => 0,
				'search'                   => '',
				'enable_pagination'        => false,
				'paged'                    => 0,
				'pagination_param'         => 'paged',
				'pagination_mid_size'      => 1,
				'pagination_end_size'      => 1,
				'pagination_prev_label'    => 'Prev',
				'pagination_next_label'    => 'Next',
				'pagination_wrapper_class' => 'mt-8 flex items-center justify-center gap-2',
				'pagination_item_class'    => 'inline-flex',
				'pagination_current_class' => 'inline-flex',
				'preserve_query_args'      => true,
				'filter_search_param'      => '',
				'filter_author_param'      => '',
				'filter_taxonomies'        => [],
				'filter_meta'              => [],
				'listing_style'            => '',
				'component'                => 'post-item-compact',
				'wrapper_tag'              => 'div',
				'wrapper_class'            => '',
				'item_wrapper_class'       => '',
				'divider_class'            => '',
				'empty_message'            => '',
				'item_overrides'           => [],
				'fallback_image'           => 'https://placehold.co/460x232',
				'button_label'             => 'Start quiz',
			];

			$config = wp_parse_args($config, $defaults);

			$config['post_type']           = self::normalize_post_types($config['post_type']);
			$config['posts_per_page']      = max(1, min(50, (int) $config['posts_per_page']));
			$config['offset']              = max(0, (int) $config['offset']);
			$config['order']               = in_array(strtoupper((string) $config['order']), ['ASC', 'DESC'], true) ? strtoupper((string) $config['order']) : 'DESC';
			$config['orderby']             = self::normalize_orderby((string) $config['orderby']);
			$config['meta_key']            = sanitize_key((string) $config['meta_key']);
			$config['meta_type']           = strtoupper((string) $config['meta_type']);
			$config['wrapper_tag']         = self::sanitize_wrapper_tag((string) $config['wrapper_tag']);
			$config['post_status']         = sanitize_key((string) $config['post_status']);
			$config['author']              = max(0, (int) $config['author']);
			$config['search']              = sanitize_text_field((string) $config['search']);
			$config['enable_pagination']   = (bool) $config['enable_pagination'];
			$config['pagination_param']    = sanitize_key((string) $config['pagination_param']) ?: 'paged';
			$config['pagination_mid_size'] = max(0, (int) $config['pagination_mid_size']);
			$config['pagination_end_size'] = max(1, (int) $config['pagination_end_size']);
			$config['preserve_query_args'] = (bool) $config['preserve_query_args'];
			$config['filter_search_param'] = sanitize_key((string) $config['filter_search_param']);
			$config['filter_author_param'] = sanitize_key((string) $config['filter_author_param']);
			$config['filter_taxonomies']   = self::normalize_filter_taxonomies($config['filter_taxonomies']);
			$config['filter_meta']         = self::normalize_filter_meta($config['filter_meta']);
			$config['listing_style']       = self::sanitize_listing_style((string) $config['listing_style']);
			$config['paged']               = self::resolve_current_page($config);

			return $config;
		}

		/**
		 * Build WP_Query from config.
		 *
		 * @param array<string,mixed> $config Normalized config.
		 */
		protected static function query(array $config): WP_Query {
			$args = [
				'post_type'      => $config['post_type'],
				'post_status'    => $config['post_status'] ?: 'publish',
				'posts_per_page' => (int) $config['posts_per_page'],
				'orderby'        => $config['orderby'],
				'order'          => $config['order'],
				'ignore_sticky_posts' => true,
				'no_found_rows'       => ! $config['enable_pagination'],
			];

			if ($config['enable_pagination']) {
				$args['paged'] = (int) $config['paged'];
			} elseif ((int) $config['offset'] > 0) {
				$args['offset'] = (int) $config['offset'];
			}

			if (! empty($config['meta_key']) && in_array($config['orderby'], ['meta_value', 'meta_value_num'], true)) {
				$args['meta_key'] = $config['meta_key'];
			}

			if (! empty($config['meta_type']) && in_array($config['orderby'], ['meta_value', 'meta_value_num'], true)) {
				$args['meta_type'] = $config['meta_type'];
			}

			if (! empty($config['post__in']) && is_array($config['post__in'])) {
				$args['post__in'] = array_map('intval', $config['post__in']);
			}

			if (! empty($config['post__not_in']) && is_array($config['post__not_in'])) {
				$args['post__not_in'] = array_map('intval', $config['post__not_in']);
			}

			if ((int) $config['author'] > 0) {
				$args['author'] = (int) $config['author'];
			}

			if ($config['search'] !== '') {
				$args['s'] = $config['search'];
			}

			if ($config['filter_search_param'] !== '') {
				$request_search = self::request_string($config['filter_search_param']);
				if ($request_search !== '') {
					$args['s'] = $request_search;
				}
			}

			if ($config['filter_author_param'] !== '') {
				$request_author = self::request_int($config['filter_author_param']);
				if ($request_author > 0) {
					$args['author'] = $request_author;
				}
			}

			$tax_query_clauses = [];
			if (! empty($config['tax_query']) && is_array($config['tax_query'])) {
				$tax_query_clauses[] = $config['tax_query'];
			}

			$filter_tax_clauses = self::build_tax_filters_from_request($config);
			if (! empty($filter_tax_clauses)) {
				$tax_query_clauses = array_merge($tax_query_clauses, $filter_tax_clauses);
			}

			if (! empty($tax_query_clauses)) {
				$args['tax_query'] = self::build_compound_query($tax_query_clauses);
			}

			$meta_query_clauses = [];
			if (! empty($config['meta_query']) && is_array($config['meta_query'])) {
				$meta_query_clauses[] = $config['meta_query'];
			}

			$filter_meta_clauses = self::build_meta_filters_from_request($config);
			if (! empty($filter_meta_clauses)) {
				$meta_query_clauses = array_merge($meta_query_clauses, $filter_meta_clauses);
			}

			if (! empty($meta_query_clauses)) {
				$args['meta_query'] = self::build_compound_query($meta_query_clauses);
			}

			return new WP_Query($args);
		}

		/**
		 * Render pagination links for a listing query.
		 *
		 * @param array<string,mixed> $config Normalized config.
		 */
		protected static function render_pagination(WP_Query $query, array $config): string {
			if (! $config['enable_pagination'] || (int) $query->max_num_pages < 2) {
				return '';
			}

			$current_page     = max(1, (int) $config['paged']);
			$pagination_param = (string) $config['pagination_param'];
			$wrapper_class    = trim((string) $config['pagination_wrapper_class']);
			$item_class       = trim((string) $config['pagination_item_class']);
			$current_class    = trim((string) $config['pagination_current_class']);
			$add_args         = [];

			if ($config['preserve_query_args'] && isset($_GET) && is_array($_GET)) {
				foreach ($_GET as $key => $value) {
					$key = sanitize_key((string) $key);
					if ($key === '' || $key === $pagination_param) {
						continue;
					}

					if (is_array($value)) {
						$add_args[$key] = array_map(
							static function ($item): string {
								return sanitize_text_field((string) wp_unslash($item));
							},
							$value
						);
					} else {
						$add_args[$key] = sanitize_text_field((string) wp_unslash($value));
					}
				}
			}

			if ('paged' === $pagination_param) {
				$big  = 999999999;
				$base = str_replace((string) $big, '%#%', esc_url_raw(get_pagenum_link($big)));
			} else {
				$base = (string) add_query_arg(
					$pagination_param,
					'%#%',
					(string) remove_query_arg($pagination_param)
				);
			}

			$links = paginate_links(
				[
					'base'      => $base,
					'format'    => '',
					'current'   => $current_page,
					'total'     => max(1, (int) $query->max_num_pages),
					'mid_size'  => (int) $config['pagination_mid_size'],
					'end_size'  => (int) $config['pagination_end_size'],
					'prev_text' => esc_html((string) $config['pagination_prev_label']),
					'next_text' => esc_html((string) $config['pagination_next_label']),
					'type'      => 'array',
					'add_args'  => $add_args,
				]
			);

			if (empty($links) || ! is_array($links)) {
				return '';
			}

			ob_start();
			printf(
				'<nav aria-label="%1$s"%2$s>',
				esc_attr__('Pagination', 'reci-media-hub'),
				$wrapper_class !== '' ? ' class="' . esc_attr($wrapper_class) . '"' : ''
			);

			foreach ($links as $link) {
				$classes = str_contains($link, 'current') ? $current_class : $item_class;
				if ($classes !== '') {
					printf('<span class="%1$s">%2$s</span>', esc_attr($classes), wp_kses_post($link));
				} else {
					echo wp_kses_post($link);
				}
			}

			echo '</nav>';
			return (string) ob_get_clean();
		}

		/**
		 * Resolve component key from listing style / component config.
		 *
		 * @param array<string,mixed> $config Normalized config.
		 */
		protected static function resolve_component_key(array $config): string {
			$style     = self::sanitize_listing_style((string) ($config['listing_style'] ?? ''));
			$component = trim((string) ($config['component'] ?? ''));

			if ($style !== '') {
				$style_map = self::listing_style_map();
				if (isset($style_map[$style])) {
					return $style_map[$style];
				}

				// Allow listing_style to directly pass a component alias/path.
				if ($component !== '' && $component === $style) {
					return $component;
				}
				if (isset(self::component_map()[$style])) {
					return $style;
				}

				foreach (self::component_map() as $resolved) {
					if (isset($resolved['template_part']) && $resolved['template_part'] === $style) {
						return $style;
					}
				}
			}

			return $component !== '' ? $component : 'post-item-compact';
		}

		/**
		 * Resolve component alias to template part and mapping variant.
		 *
		 * @param string $component Requested component key/path.
		 * @return array{template_part:string,variant:string}
		 */
		protected static function resolve_component(string $component): array {
			$component = trim($component);
			$map       = self::component_map();

			if (isset($map[$component])) {
				return $map[$component];
			}

			foreach ($map as $resolved) {
				if ($resolved['template_part'] === $component) {
					return $resolved;
				}
			}

			return $map['post-item-compact'];
		}

		/**
		 * Listing style aliases (semantic names) to concrete components.
		 *
		 * @return array<string,string>
		 */
		protected static function listing_style_map(): array {
			return [
				'content_feature'      => 'post-item-half-row',
				'content_compact'      => 'post-item-compact',
				'archive_grid_card'    => 'articles-side-card',
				'archive_list_row'     => 'archive-content-row',
				'category_archive_row' => 'archive-content-row',
				'topic_archive_row'    => 'archive-content-row',
				'article_archive_card' => 'articles-side-card',
				'podcast_archive_card' => 'podcast-archive-card',
				'podcast_feature'      => 'podcast-feature-card',
				'video_feature'        => 'videos-featured-overlay-card',
				'event_card'           => 'event-carousel-card',
				'lens_card'            => 'lens-quiz-card',
				'quote_card'           => 'quote-carousel-item',
				'community_quote'      => 'community-pulse-slide',
			];
		}

		/**
		 * Component alias map.
		 *
		 * @return array<string,array{template_part:string,variant:string}>
		 */
		protected static function component_map(): array {
			return [
				'post-item-half-row'         => ['template_part' => 'template-parts/listings/post-item-half-row', 'variant' => 'content'],
				'post-item-compact'          => ['template_part' => 'template-parts/listings/post-item-compact', 'variant' => 'content'],
				'articles-side-card'         => ['template_part' => 'template-parts/listings/articles-side-card', 'variant' => 'content'],
				'archive-content-row'        => ['template_part' => 'template-parts/listings/archive-content-row', 'variant' => 'content'],
				'videos-featured-overlay-card' => ['template_part' => 'template-parts/listings/videos-featured-overlay-card', 'variant' => 'video_feature'],
				'podcast-feature-card'       => ['template_part' => 'template-parts/listings/podcast-feature-card', 'variant' => 'podcast_feature'],
				'podcast-compact-card'       => ['template_part' => 'template-parts/listings/podcast-compact-card', 'variant' => 'podcast_compact'],
				'podcast-archive-card'       => ['template_part' => 'template-parts/listings/podcast-archive-card', 'variant' => 'podcast_compact'],
				'event-carousel-card'        => ['template_part' => 'template-parts/listings/event-carousel-card', 'variant' => 'event'],
				'lens-quiz-card'             => ['template_part' => 'template-parts/listings/lens-quiz-card', 'variant' => 'assessment'],
				'quote-carousel-item'        => ['template_part' => 'template-parts/listings/quote-carousel-item', 'variant' => 'quote'],
				'community-pulse-slide'      => ['template_part' => 'template-parts/listings/community-pulse-slide', 'variant' => 'community_quote'],
			];
		}

		/**
		 * Convert post to item args expected by target listing component.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_post_to_item(WP_Post $post, string $variant, array $config): array {
			switch ($variant) {
				case 'video_feature':
					return self::map_video_feature_item($post, $config);
				case 'podcast_feature':
					return self::map_podcast_feature_item($post, $config);
				case 'podcast_compact':
					return self::map_podcast_compact_item($post, $config);
				case 'event':
					return self::map_event_item($post, $config);
				case 'assessment':
					return self::map_assessment_item($post, $config);
				case 'quote':
					return self::map_quote_item($post);
				case 'community_quote':
					return self::map_community_quote_item($post, $config);
				case 'content':
				default:
					return self::map_content_item($post, $config);
			}
		}

		/**
		 * Generic content card mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_content_item(WP_Post $post, array $config): array {
			$post_id   = (int) $post->ID;
			$post_type = get_post_type($post_id);
			$ui        = self::post_type_ui($post_type);
			$meta      = self::meta_value_for_type($post_id, $post_type);

			return [
				'type_label'       => $ui['label'],
				'type_badge_class' => $ui['badge'],
				'type_text_class'  => $ui['text'],
				'date'             => get_the_date('d M Y', $post_id),
				'meta_icon'        => $meta['icon'],
				'meta_value'       => $meta['value'],
				'meta_value_size'  => 'text-sm',
				'title'            => get_the_title($post_id),
				'excerpt'          => self::excerpt($post),
				'tags'             => self::tags($post_id, 3),
				'image_url'        => self::image($post_id, 'large', (string) $config['fallback_image']),
				'image_alt'        => self::image_alt($post_id),
				'link_url'         => get_permalink($post_id),
			];
		}

		/**
		 * Video featured card mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_video_feature_item(WP_Post $post, array $config): array {
			$post_id = (int) $post->ID;
			$meta    = self::meta_value_for_type($post_id, 'reci_video');

			return [
				'type_label'   => 'Video',
				'date'         => get_the_date('d M Y', $post_id),
				'meta_value'   => $meta['value'],
				'title'        => get_the_title($post_id),
				'excerpt'      => self::excerpt($post, 24),
				'tags'         => self::tags($post_id, 3),
				'bg_image_url' => self::image($post_id, 'large', (string) $config['fallback_image']),
				'link_url'     => get_permalink($post_id),
			];
		}

		/**
		 * Podcast feature card mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_podcast_feature_item(WP_Post $post, array $config): array {
			$post_id = (int) $post->ID;
			$meta    = self::meta_value_for_type($post_id, 'reci_podcast');

			return [
				'type_label'       => 'Podcast',
				'date'             => get_the_date('d M Y', $post_id),
				'duration'         => $meta['value'],
				'title'            => get_the_title($post_id),
				'tags'             => self::tags($post_id, 3),
				'excerpt'          => self::excerpt($post, 26),
				'image_url'        => self::image($post_id, 'large', (string) $config['fallback_image']),
				'image_alt'        => self::image_alt($post_id),
				'progress_percent' => 35,
				'link_url'         => get_permalink($post_id),
			];
		}

		/**
		 * Podcast compact card mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_podcast_compact_item(WP_Post $post, array $config): array {
			$post_id = (int) $post->ID;
			$meta    = self::meta_value_for_type($post_id, 'reci_podcast');

			return [
				'topic_tags'       => self::tags($post_id, 3),
				'type_label'       => 'Podcast',
				'date'             => get_the_date('d M Y', $post_id),
				'duration'         => $meta['value'],
				'title'            => get_the_title($post_id),
				'image_url'        => self::image($post_id, 'medium', (string) $config['fallback_image']),
				'image_alt'        => self::image_alt($post_id),
				'progress_percent' => 35,
				'link_url'         => get_permalink($post_id),
			];
		}

		/**
		 * Event card mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_event_item(WP_Post $post, array $config): array {
			$post_id          = (int) $post->ID;
			$status           = (string) get_post_meta($post_id, '_reci_event_status', true);
			$start_date_raw   = (string) get_post_meta($post_id, '_reci_event_start_date', true);
			$start_time_raw   = (string) get_post_meta($post_id, '_reci_event_start_time', true);
			$timezone_raw     = (string) get_post_meta($post_id, '_reci_event_timezone', true);
			$registration_url = (string) get_post_meta($post_id, '_reci_event_registration_url', true);
			$cta_label        = (string) get_post_meta($post_id, '_reci_event_cta_label', true);

			$timestamp = $start_date_raw ? strtotime($start_date_raw) : false;
			$date      = $timestamp ? wp_date('j F, Y', $timestamp) : get_the_date('j F, Y', $post_id);
			$time      = trim($start_time_raw . ($timezone_raw ? ' ' . $timezone_raw : ''));

			return [
				'status'       => $status ? ucfirst($status) : 'Upcoming',
				'date'         => $date,
				'time'         => $time,
				'title'        => get_the_title($post_id),
				'excerpt'      => self::excerpt($post, 18),
				'button_label' => $cta_label ?: 'Register',
				'link_url'     => $registration_url ?: get_permalink($post_id),
				'image_url'    => self::image($post_id, 'large', (string) $config['fallback_image']),
				'image_alt'    => self::image_alt($post_id),
			];
		}

		/**
		 * Assessment card mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_assessment_item(WP_Post $post, array $config): array {
			$post_id   = (int) $post->ID;
			$intro     = (string) get_post_meta($post_id, '_reci_assessment_intro', true);
			$estimated = (string) get_post_meta($post_id, '_reci_assessment_estimated_time', true);
			$desc      = $intro ?: self::excerpt($post, 14);

			if ($estimated) {
				$desc = trim($desc . ' (' . $estimated . ')');
			}

			return [
				'title'        => get_the_title($post_id),
				'description'  => $desc,
				'button_label' => (string) $config['button_label'],
				'link_url'     => get_permalink($post_id),
			];
		}

		/**
		 * Quote carousel mapper.
		 *
		 * @return array<string,mixed>
		 */
		protected static function map_quote_item(WP_Post $post): array {
			$post_id     = (int) $post->ID;
			$quote_text  = (string) get_post_meta($post_id, '_reci_quote_text', true);
			$author_name = (string) get_post_meta($post_id, '_reci_quote_author_name', true);

			return [
				'quote'  => $quote_text ?: self::excerpt($post, 38),
				'author' => '– By ' . ($author_name ?: get_the_title($post_id)),
			];
		}

		/**
		 * Community quote mapper.
		 *
		 * @param array<string,mixed> $config
		 * @return array<string,mixed>
		 */
		protected static function map_community_quote_item(WP_Post $post, array $config): array {
			$post_id      = (int) $post->ID;
			$quote_text   = (string) get_post_meta($post_id, '_reci_quote_text', true);
			$author_name  = (string) get_post_meta($post_id, '_reci_quote_author_name', true);
			$author_title = (string) get_post_meta($post_id, '_reci_quote_author_title', true);
			$author_img   = (string) get_post_meta($post_id, '_reci_quote_author_image_url', true);

			return [
				'quote'        => $quote_text ?: self::excerpt($post, 28),
				'author_name'  => $author_name ?: get_the_title($post_id),
				'author_role'  => $author_title ?: 'RECI Contributor',
				'author_image' => $author_img ?: self::image($post_id, 'thumbnail', (string) $config['fallback_image']),
				'author_alt'   => $author_name ?: get_the_title($post_id),
			];
		}

		/**
		 * Convert mixed post_type input to valid array.
		 *
		 * @param mixed $post_type Raw value.
		 * @return array<int,string>
		 */
		protected static function normalize_post_types($post_type): array {
			$list = [];

			if (is_string($post_type)) {
				$list = array_map('trim', explode(',', $post_type));
			} elseif (is_array($post_type)) {
				$list = array_map(
					static function ($value): string {
						return trim((string) $value);
					},
					$post_type
				);
			}

			$list = array_filter($list, static function ($type): bool {
				return $type !== '' && post_type_exists($type);
			});

			if (empty($list)) {
				$list = ['post'];
			}

			return array_values($list);
		}

		/**
		 * Normalize request-driven taxonomy filters.
		 *
		 * @param mixed $filters Raw taxonomy filter config.
		 * @return array<string,array{param:string,field:string,operator:string}>
		 */
		protected static function normalize_filter_taxonomies($filters): array {
			if (! is_array($filters)) {
				return [];
			}

			$normalized = [];
			foreach ($filters as $taxonomy => $definition) {
				if (is_int($taxonomy)) {
					$taxonomy   = sanitize_key((string) $definition);
					$definition = [];
				} else {
					$taxonomy = sanitize_key((string) $taxonomy);
				}

				if ($taxonomy === '' || ! taxonomy_exists($taxonomy)) {
					continue;
				}

				$param    = $taxonomy;
				$field    = 'slug';
				$operator = 'IN';

				if (is_string($definition) && $definition !== '') {
					$param = sanitize_key($definition);
				} elseif (is_array($definition)) {
					if (! empty($definition['param'])) {
						$param = sanitize_key((string) $definition['param']);
					}
					if (! empty($definition['field'])) {
						$field = (string) $definition['field'];
					}
					if (! empty($definition['operator'])) {
						$operator = strtoupper((string) $definition['operator']);
					}
				}

				if (! in_array($field, ['slug', 'name', 'term_id'], true)) {
					$field = 'slug';
				}
				if (! in_array($operator, ['IN', 'NOT IN', 'AND'], true)) {
					$operator = 'IN';
				}

				$normalized[$taxonomy] = [
					'param'    => $param ?: $taxonomy,
					'field'    => $field,
					'operator' => $operator,
				];
			}

			return $normalized;
		}

		/**
		 * Normalize request-driven meta filters.
		 *
		 * @param mixed $filters Raw meta filter config.
		 * @return array<int,array{meta_key:string,param:string,compare:string,type:string,sanitize:string,allow_multiple:bool}>
		 */
		protected static function normalize_filter_meta($filters): array {
			if (! is_array($filters)) {
				return [];
			}

			$allowed_compares = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS'];
			$allowed_types    = ['NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'];
			$allowed_sanitize = ['text', 'key', 'int', 'float', 'bool', 'raw'];
			$normalized       = [];

			foreach ($filters as $filter) {
				if (! is_array($filter) || empty($filter['meta_key']) || empty($filter['param'])) {
					continue;
				}

				$meta_key       = sanitize_key((string) $filter['meta_key']);
				$param          = sanitize_key((string) $filter['param']);
				$compare        = strtoupper((string) ($filter['compare'] ?? '='));
				$type           = strtoupper((string) ($filter['type'] ?? 'CHAR'));
				$sanitize       = strtolower((string) ($filter['sanitize'] ?? 'text'));
				$allow_multiple = (bool) ($filter['allow_multiple'] ?? false);

				if (! in_array($compare, $allowed_compares, true)) {
					$compare = '=';
				}
				if (! in_array($type, $allowed_types, true)) {
					$type = 'CHAR';
				}
				if (! in_array($sanitize, $allowed_sanitize, true)) {
					$sanitize = 'text';
				}
				if (in_array($compare, ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'], true)) {
					$allow_multiple = true;
				}

				$normalized[] = [
					'meta_key'       => $meta_key,
					'param'          => $param,
					'compare'        => $compare,
					'type'           => $type,
					'sanitize'       => $sanitize,
					'allow_multiple' => $allow_multiple,
				];
			}

			return $normalized;
		}

		/**
		 * Resolve current page for pagination.
		 *
		 * @param array<string,mixed> $config Normalized config.
		 */
		protected static function resolve_current_page(array $config): int {
			$config_page = max(0, (int) $config['paged']);
			if ($config_page > 0) {
				return $config_page;
			}

			$page = 1;
			if (! empty($config['pagination_param'])) {
				$page = max($page, self::request_int((string) $config['pagination_param']));
			}

			$page = max($page, (int) get_query_var('paged'));
			$page = max($page, (int) get_query_var('page'));
			return max(1, $page);
		}

		/**
		 * Build tax_query clauses from request params.
		 *
		 * @param array<string,mixed> $config Normalized config.
		 * @return array<int,array<string,mixed>>
		 */
		protected static function build_tax_filters_from_request(array $config): array {
			if (empty($config['filter_taxonomies']) || ! is_array($config['filter_taxonomies'])) {
				return [];
			}

			$clauses = [];
			foreach ($config['filter_taxonomies'] as $taxonomy => $definition) {
				if (! taxonomy_exists($taxonomy) || ! is_array($definition)) {
					continue;
				}

				$param    = sanitize_key((string) ($definition['param'] ?? ''));
				$field    = (string) ($definition['field'] ?? 'slug');
				$operator = strtoupper((string) ($definition['operator'] ?? 'IN'));

				if ($param === '') {
					continue;
				}

				$raw = self::request_value($param);
				if ($raw === null || $raw === '' || $raw === []) {
					continue;
				}

				$terms = self::sanitize_term_filter_value($raw, $field);
				if (empty($terms)) {
					continue;
				}

				$clauses[] = [
					'taxonomy' => $taxonomy,
					'field'    => $field,
					'terms'    => $terms,
					'operator' => $operator,
				];
			}

			return $clauses;
		}

		/**
		 * Build meta_query clauses from request params.
		 *
		 * @param array<string,mixed> $config Normalized config.
		 * @return array<int,array<string,mixed>>
		 */
		protected static function build_meta_filters_from_request(array $config): array {
			if (empty($config['filter_meta']) || ! is_array($config['filter_meta'])) {
				return [];
			}

			$clauses = [];
			foreach ($config['filter_meta'] as $filter) {
				if (! is_array($filter) || empty($filter['meta_key']) || empty($filter['param'])) {
					continue;
				}

				$param   = sanitize_key((string) $filter['param']);
				$raw     = self::request_value($param);
				$compare = strtoupper((string) ($filter['compare'] ?? '='));

				if ($raw === null || $raw === '' || $raw === []) {
					continue;
				}

				$value = self::sanitize_meta_filter_value(
					$raw,
					(string) ($filter['sanitize'] ?? 'text'),
					(bool) ($filter['allow_multiple'] ?? false)
				);
				if ($value === '' || $value === [] || $value === null) {
					continue;
				}

				$clause = [
					'key'     => sanitize_key((string) $filter['meta_key']),
					'compare' => $compare,
					'type'    => strtoupper((string) ($filter['type'] ?? 'CHAR')),
				];

				if (! in_array($compare, ['EXISTS', 'NOT EXISTS'], true)) {
					$clause['value'] = $value;
				}

				$clauses[] = $clause;
			}

			return $clauses;
		}

		/**
		 * Normalize tax filter value from request.
		 *
		 * @param mixed $raw Raw request value.
		 * @return array<int,int|string>
		 */
		protected static function sanitize_term_filter_value($raw, string $field): array {
			$values = self::normalize_request_list($raw);
			if (empty($values)) {
				return [];
			}

			if ('term_id' === $field) {
				return array_values(
					array_filter(
						array_map('intval', $values),
						static function (int $item): bool {
							return $item > 0;
						}
					)
				);
			}

			if ('name' === $field) {
				return array_values(
					array_filter(
						array_map(
							static function (string $item): string {
								return sanitize_text_field($item);
							},
							$values
						),
						static function (string $item): bool {
							return $item !== '';
						}
					)
				);
			}

			return array_values(
				array_filter(
					array_map(
						static function (string $item): string {
							return sanitize_title($item);
						},
						$values
					),
					static function (string $item): bool {
						return $item !== '';
					}
				)
			);
		}

		/**
		 * Normalize meta filter value from request.
		 *
		 * @param mixed  $raw Raw request value.
		 * @param string $sanitize_type Value sanitizer.
		 * @return mixed
		 */
		protected static function sanitize_meta_filter_value($raw, string $sanitize_type, bool $allow_multiple) {
			$values = $allow_multiple ? self::normalize_request_list($raw) : [is_array($raw) ? reset($raw) : $raw];
			$values = array_map(
				static function ($item): string {
					return (string) $item;
				},
				$values
			);

			$sanitized = [];
			foreach ($values as $value) {
				if ($sanitize_type === 'int') {
					$sanitized[] = (int) $value;
				} elseif ($sanitize_type === 'float') {
					$sanitized[] = (float) $value;
				} elseif ($sanitize_type === 'bool') {
					$sanitized[] = in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
				} elseif ($sanitize_type === 'key') {
					$sanitized[] = sanitize_key($value);
				} elseif ($sanitize_type === 'raw') {
					$sanitized[] = wp_kses_post($value);
				} else {
					$sanitized[] = sanitize_text_field($value);
				}
			}

			$sanitized = array_values(
				array_filter(
					$sanitized,
					static function ($item): bool {
						return ! ($item === '' || $item === null);
					}
				)
			);

			if ($allow_multiple) {
				return $sanitized;
			}

			return $sanitized[0] ?? '';
		}

		/**
		 * Combine query clauses under an AND relation when needed.
		 *
		 * @param array<int,mixed> $clauses Query clauses.
		 * @return array<string,mixed>
		 */
		protected static function build_compound_query(array $clauses): array {
			$normalized = array_values(
				array_filter(
					$clauses,
					static function ($clause): bool {
						return is_array($clause) && ! empty($clause);
					}
				)
			);

			if (empty($normalized)) {
				return [];
			}

			if (count($normalized) === 1) {
				return $normalized[0];
			}

			return array_merge(['relation' => 'AND'], $normalized);
		}

		/**
		 * Get unslashed request value from query string or query vars.
		 *
		 * @return mixed|null
		 */
		protected static function request_value(string $key) {
			if ($key === '') {
				return null;
			}

			if (isset($_GET[$key])) {
				return wp_unslash($_GET[$key]);
			}

			$query_var = get_query_var($key, null);
			if ($query_var !== null && $query_var !== '') {
				return $query_var;
			}

			return null;
		}

		/**
		 * Resolve request value as a sanitized string.
		 */
		protected static function request_string(string $key): string {
			$value = self::request_value($key);
			if ($value === null) {
				return '';
			}

			if (is_array($value)) {
				$value = reset($value);
			}

			return sanitize_text_field((string) $value);
		}

		/**
		 * Resolve request value as integer.
		 */
		protected static function request_int(string $key): int {
			$value = self::request_value($key);
			if ($value === null) {
				return 0;
			}

			if (is_array($value)) {
				$value = reset($value);
			}

			return max(0, (int) $value);
		}

		/**
		 * Convert comma-delimited strings or arrays to a flat list.
		 *
		 * @param mixed $raw Raw value from query string.
		 * @return array<int,string>
		 */
		protected static function normalize_request_list($raw): array {
			if (is_array($raw)) {
				$values = $raw;
			} else {
				$values = preg_split('/\s*,\s*/', (string) $raw) ?: [];
			}

			return array_values(
				array_filter(
					array_map(
						static function ($item): string {
							return trim((string) $item);
						},
						$values
					),
					static function (string $item): bool {
						return $item !== '';
					}
				)
			);
		}

		/**
		 * Normalize orderby to safe values.
		 */
		protected static function normalize_orderby(string $orderby): string {
			$allowed = [
				'date',
				'title',
				'name',
				'modified',
				'menu_order',
				'rand',
				'comment_count',
				'meta_value',
				'meta_value_num',
				'post__in',
			];

			return in_array($orderby, $allowed, true) ? $orderby : 'date';
		}

		/**
		 * Sanitize wrapper HTML tag.
		 */
		protected static function sanitize_wrapper_tag(string $tag): string {
			$tag     = strtolower(trim($tag));
			$allowed = ['div', 'section', 'article', 'ul', 'ol'];
			return in_array($tag, $allowed, true) ? $tag : 'div';
		}

		/**
		 * Sanitize listing style key.
		 */
		protected static function sanitize_listing_style(string $style): string {
			$style = strtolower(trim($style));
			$style = preg_replace('/[^a-z0-9_\\/-]/', '', $style);
			return is_string($style) ? $style : '';
		}

		/**
		 * Get UI style metadata per post type.
		 *
		 * @return array{label:string,badge:string,text:string}
		 */
		protected static function post_type_ui(string $post_type): array {
			$map = [
				'reci_article'   => ['label' => 'Article',    'badge' => 'bg-amber-400', 'text' => 'text-neutral-800'],
				'reci_podcast'   => ['label' => 'Podcast',    'badge' => 'bg-neutral-800', 'text' => 'text-white'],
				'reci_video'     => ['label' => 'Video',      'badge' => 'bg-blue-900', 'text' => 'text-white'],
				'reci_event'     => ['label' => 'Event',      'badge' => 'bg-amber-400', 'text' => 'text-neutral-800'],
				'reci_reflection'=> ['label' => 'Reflection', 'badge' => 'bg-neutral-800', 'text' => 'text-white'],
				'reci_quote'     => ['label' => 'Quote',      'badge' => 'bg-neutral-800', 'text' => 'text-white'],
				'reci_assessment'=> ['label' => 'Assessment', 'badge' => 'bg-neutral-800', 'text' => 'text-white'],
				'reci_course'    => ['label' => 'Course',     'badge' => 'bg-neutral-800', 'text' => 'text-white'],
				'post'           => ['label' => 'Article',    'badge' => 'bg-amber-400', 'text' => 'text-neutral-800'],
			];

			return $map[$post_type] ?? $map['post'];
		}

		/**
		 * Return meta icon/value based on post type.
		 *
		 * @return array{icon:string,value:string}
		 */
			protected static function meta_value_for_type(int $post_id, string $post_type): array {
				if ('reci_podcast' === $post_type) {
					return [
						'icon'  => 'audio',
						'value' => self::duration_label_from_meta($post_id, '_reci_podcast_duration_label', '_reci_podcast_duration_secs'),
					];
				}

				if ('reci_video' === $post_type) {
					return [
						'icon'  => 'video',
						'value' => self::duration_label_from_meta($post_id, '_reci_video_duration_label', '_reci_video_duration_secs'),
					];
				}

			if ('reci_event' === $post_type) {
				return [
					'icon'  => 'timer',
					'value' => (string) get_post_meta($post_id, '_reci_event_start_time', true) ?: '',
				];
			}

				return [
					'icon'  => 'timer',
					'value' => (string) get_post_meta($post_id, '_reci_article_read_time_label', true) ?: '3 mins',
				];
			}

			/**
			 * Resolve duration label with seconds fallback.
			 */
			protected static function duration_label_from_meta(int $post_id, string $label_key, string $secs_key): string {
				$label = (string) get_post_meta($post_id, $label_key, true);
				if ($label !== '') {
					return $label;
				}

				$seconds = (int) get_post_meta($post_id, $secs_key, true);
				if ($seconds <= 0) {
					return '00:00';
				}

				$hours = (int) floor($seconds / 3600);
				$mins  = (int) floor(($seconds % 3600) / 60);
				$secs  = (int) ($seconds % 60);

				if ($hours > 0) {
					return sprintf('%d:%02d:%02d', $hours, $mins, $secs);
				}

				return sprintf('%02d:%02d', $mins, $secs);
			}

		/**
		 * Get normalized excerpt.
		 */
		protected static function excerpt(WP_Post $post, int $words = 22): string {
			$post_id = (int) $post->ID;
			if (has_excerpt($post_id)) {
				$text = get_the_excerpt($post_id);
			} else {
				$text = wp_strip_all_tags($post->post_content);
			}
			return wp_trim_words($text, $words, '...');
		}

		/**
		 * Combined tags/categories list.
		 *
		 * @return array<int,string>
		 */
		protected static function tags(int $post_id, int $limit = 3): array {
			$tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
			if (is_wp_error($tags)) {
				$tags = [];
			}

			$tags = array_values(array_unique(array_filter(array_map('trim', $tags))));
			if (count($tags) >= $limit) {
				return array_slice($tags, 0, $limit);
			}

			$cats = wp_get_post_terms($post_id, 'category', ['fields' => 'names']);
			if (! is_wp_error($cats)) {
				foreach ($cats as $cat) {
					if (count($tags) >= $limit) {
						break;
					}
					if (! in_array($cat, $tags, true)) {
						$tags[] = $cat;
					}
				}
			}

			return array_slice($tags, 0, $limit);
		}

		/**
		 * Featured image with fallback.
		 */
		protected static function image(int $post_id, string $size, string $fallback): string {
			$image = get_the_post_thumbnail_url($post_id, $size);
			return $image ?: $fallback;
		}

		/**
		 * Resolve featured image alt.
		 */
		protected static function image_alt(int $post_id): string {
			$thumb_id = get_post_thumbnail_id($post_id);
			if (! $thumb_id) {
				return get_the_title($post_id);
			}
			$alt = (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
			return $alt !== '' ? $alt : get_the_title($post_id);
		}
	}
}

if (! function_exists('reci_media_hub_render_listing')) {
	/**
	 * Render reusable listing and return HTML.
	 *
	 * @param array<string,mixed> $config Listing config.
	 */
	function reci_media_hub_render_listing(array $config = []): string {
		return RECI_Media_Hub_Listing_Builder::render($config);
	}
}

if (! function_exists('reci_media_hub_the_listing')) {
	/**
	 * Echo reusable listing output.
	 *
	 * @param array<string,mixed> $config Listing config.
	 */
	function reci_media_hub_the_listing(array $config = []): void {
		RECI_Media_Hub_Listing_Builder::output($config);
	}
}

if (! function_exists('reci_media_hub_listing_query')) {
	/**
	 * Get a listing query using the reusable listing config API.
	 *
	 * @param array<string,mixed> $config Listing config.
	 */
	function reci_media_hub_listing_query(array $config = []): WP_Query {
		return RECI_Media_Hub_Listing_Builder::get_query($config);
	}
}

if (! function_exists('reci_media_hub_listing_pagination')) {
	/**
	 * Render pagination HTML from a query and listing config.
	 *
	 * @param array<string,mixed> $config Listing config.
	 */
	function reci_media_hub_listing_pagination(WP_Query $query, array $config = []): string {
		return RECI_Media_Hub_Listing_Builder::pagination($query, $config);
	}
}

add_action('init', ['RECI_Media_Hub_Listing_Builder', 'init']);
