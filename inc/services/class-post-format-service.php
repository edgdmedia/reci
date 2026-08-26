<?php
/**
 * Formats posts into reusable listing payloads.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Post_Format_Service')) {
    class RECI_Post_Format_Service {
        /**
         * Format many posts.
         *
         * @param array<int,WP_Post|int> $posts Posts.
         * @param array<string,mixed>    $args  Format args.
         * @return array<int,array<string,mixed>>
         */
        public static function format_posts(array $posts, array $args = []): array {
            $items = [];
            foreach ($posts as $post) {
                $items[] = self::format_post($post, $args);
            }
            return array_values(array_filter($items));
        }

        /**
         * Format one post.
         *
         * @param WP_Post|int            $post Post object or ID.
         * @param array<string,mixed>    $args Format args.
         * @return array<string,mixed>
         */
        public static function format_post($post, array $args = []): array {
            $post_obj = is_numeric($post) ? get_post((int) $post) : $post;
            if (! $post_obj instanceof WP_Post) {
                return [];
            }

            $post_id   = (int) $post_obj->ID;
            $post_type = (string) get_post_type($post_id);
            $type_cfg  = self::type_config($post_type);

            $image_size       = (string) ($args['image_size'] ?? 'medium');
            $fallback_image   = (string) ($args['fallback_image'] ?? (function_exists('reci_get_fallback_thumbnail_url') ? reci_get_fallback_thumbnail_url('medium', 'https://placehold.co/460x232') : 'https://placehold.co/460x232'));
            $excerpt_words    = (int) ($args['excerpt_words'] ?? 20);
            $tag_limit        = (int) ($args['tag_limit'] ?? 3);
            $date_format      = (string) ($args['date_format'] ?? 'd M Y');
            $use_cat_fallback = ! array_key_exists('include_category_fallback', $args) || ! empty($args['include_category_fallback']);
            $overrides       = is_array($args['overrides'] ?? null) ? $args['overrides'] : [];

			$thumb_id  = get_post_thumbnail_id($post_id);
			$image_alt = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
			if ($image_alt === '') {
				$image_alt = get_the_title($post_id);
			}

			$image_url = get_the_post_thumbnail_url($post_id, $image_size) ?: '';
			if ($image_url === '') {
				$image_url = $fallback_image;
			}

            $meta_value = self::meta_value($post_id, $post_type, $type_cfg);
            $author     = reci_media_hub_get_display_author($post_id);

            $category = '';
            $cats = wp_get_post_categories($post_id, ['fields' => 'names']);
            if (! empty($cats) && ! is_wp_error($cats)) {
                $category = (string) $cats[0];
            }

            $tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
            if (is_wp_error($tags)) {
                $tags = [];
            }
            $tags = array_values(array_unique(array_filter(array_map('trim', $tags))));
            if ($use_cat_fallback && count($tags) < $tag_limit) {
                $cats = wp_get_post_terms($post_id, 'category', ['fields' => 'names']);
                if (! is_wp_error($cats)) {
                    foreach ($cats as $cat_name) {
                        if (count($tags) >= $tag_limit) {
                            break;
                        }
                        $cat_name = trim((string) $cat_name);
                        if ($cat_name !== '' && ! in_array($cat_name, $tags, true)) {
                            $tags[] = $cat_name;
                        }
                    }
                }
            }
            $tags = array_slice($tags, 0, max(1, $tag_limit));

            $sdg_terms_data = [];
            $sdgs = wp_get_post_terms($post_id, 'sdgs');
            if (! is_wp_error($sdgs) && ! empty($sdgs)) {
                foreach ($sdgs as $sdg_term) {
                    $s_color = (string) get_term_meta($sdg_term->term_id, 'sdg_color', true);
                    $s_link  = get_term_link($sdg_term);
                    $sdg_terms_data[] = [
                        'term_id' => $sdg_term->term_id,
                        'slug'    => $sdg_term->slug,
                        'name'    => $sdg_term->name,
                        'color'   => $s_color,
                        'url'     => is_string($s_link) ? $s_link : '',
                    ];
                }
            }

            $sphere_terms_data = [];
            $sphere_terms = wp_get_post_terms($post_id, 'reci_sphere');
            if (! is_wp_error($sphere_terms) && ! empty($sphere_terms)) {
                foreach ($sphere_terms as $sphere_term) {
                    $default      = reci_media_hub_get_sphere_default_by_slug($sphere_term->slug) ?? [];
                    $s_color      = (string) get_term_meta($sphere_term->term_id, 'reci_sphere_color', true);
                    $s_num        = (string) get_term_meta($sphere_term->term_id, 'reci_sphere_num', true);
                    $s_action     = (string) get_term_meta($sphere_term->term_id, 'reci_sphere_action', true);
                    $s_link       = get_term_link($sphere_term);

                    if ($s_color === '') {
                        $s_color = (string) ($default['color'] ?? '#9B4D3A');
                    }
                    if ($s_num === '') {
                        $s_num = (string) ($default['num'] ?? '');
                    }
                    if ($s_action === '') {
                        $s_action = (string) ($default['action'] ?? '');
                    }

                    $sphere_terms_data[] = [
                        'term_id' => $sphere_term->term_id,
                        'slug'    => $sphere_term->slug,
                        'name'    => $sphere_term->name,
                        'num'     => $s_num,
                        'action'  => $s_action,
                        'color'   => $s_color,
                        'url'     => is_string($s_link) ? $s_link : '',
                    ];
                }
            }

            $show_name = '';
            $show_url  = '';
            $shows = wp_get_post_terms($post_id, 'reci_show');
            if (! is_wp_error($shows) && ! empty($shows)) {
                $show_name = $shows[0]->name;
                $s_link    = get_term_link($shows[0]);
                $show_url  = is_string($s_link) ? $s_link : '';
            }

            $excerpt = has_excerpt($post_id)
                ? get_the_excerpt($post_id)
                : wp_trim_words(wp_strip_all_tags((string) $post_obj->post_content), max(5, $excerpt_words), '...');

            $item = [
                'id'               => $post_id,
                'post_type'        => $post_type,
                'permalink'        => get_permalink($post_id),
                'link_url'         => get_permalink($post_id),
                'title'            => get_the_title($post_id),
                'excerpt'          => $excerpt,
                'date'             => get_the_date($date_format, $post_id),
				'image_url'        => $image_url,
                'image_alt'        => $image_alt,
                'tags'             => $tags,
                'topic_tags'       => $tags,
                'type_label'       => $type_cfg['label'],
                'type_badge_class' => $type_cfg['badge'],
                'type_text_class'  => $type_cfg['text'],
                'meta_value'       => $meta_value,
                'meta_icon'        => $type_cfg['icon'],
                'meta_value_size'  => 'text-sm',
                'duration'         => $meta_value,
                'audio_url'        => (string) get_post_meta($post_id, '_reci_podcast_audio_url', true),
                'video_url'        => (string) get_post_meta($post_id, '_reci_podcast_video_url', true),
                'episode_number'   => (string) get_post_meta($post_id, '_reci_podcast_episode_number', true),
                'season_number'    => (string) get_post_meta($post_id, '_reci_podcast_season_number', true),
                'author_name'      => (string) ($author['name'] ?? ''),
                'author_title'     => (string) ($author['title'] ?? ''),
                'author_url'       => (string) ($author['permalink'] ?? ''),
                'author_image_url' => (string) ($author['image_url'] ?? ''),
                'sphere_terms'     => $sphere_terms_data,
                'sdg_terms'        => $sdg_terms_data,
                'show_name'        => $show_name,
                'show_url'         => $show_url,
                'category'         => $category,
            ];

            if (! empty($overrides)) {
                $item = array_merge($item, $overrides);
            }

            return $item;
        }

        /**
         * @param string $post_type Post type.
         * @return array<string,string>
         */
        protected static function type_config(string $post_type): array {
            $map = [
                'post' => [
                    'label'        => 'Article',
                    'badge'        => 'bg-reci-blue',
                    'text'         => 'text-white',
                    'icon'         => 'timer',
                    'meta_key'     => '_post_read_time_label',
                    'meta_default' => '3 mins',
                ],
                'reci_article' => [
                    'label'        => 'Article',
                    'badge'        => 'bg-reci-blue',
                    'text'         => 'text-white',
                    'icon'         => 'timer',
                    'meta_key'     => '_reci_article_read_time_label',
                    'meta_default' => '3 mins',
                ],
                'reci_podcast' => [
                    'label'        => 'Podcast',
                    'badge'        => 'bg-purple-600',
                    'text'         => 'text-white',
                    'icon'         => 'audio',
                    'meta_key'     => '_reci_podcast_duration_label',
                    'meta_default' => '07:27',
                ],
                'reci_video' => [
                    'label'        => 'Video',
                    'badge'        => 'bg-red-600',
                    'text'         => 'text-white',
                    'icon'         => 'video',
                    'meta_key'     => '_reci_video_duration_label',
                    'meta_default' => '12:45',
                ],
                'reci_reflection' => [
                    'label'        => 'Reflection',
                    'badge'        => 'bg-teal-600',
                    'text'         => 'text-white',
                    'icon'         => 'timer',
                    'meta_key'     => '_post_read_time_label',
                    'meta_default' => '5 mins',
                ],
                'reci_quote' => [
                    'label'        => 'Quote',
                    'badge'        => 'bg-rose-600',
                    'text'         => 'text-white',
                    'icon'         => 'timer',
                    'meta_key'     => '_post_read_time_label',
                    'meta_default' => '1 min',
                ],
                'reci_assessment' => [
                    'label'        => 'Quiz',
                    'badge'        => 'bg-indigo-600',
                    'text'         => 'text-white',
                    'icon'         => 'timer',
                    'meta_key'     => '_post_read_time_label',
                    'meta_default' => '10 mins',
                ],
                'reci_course' => [
                    'label'        => 'Course',
                    'badge'        => 'bg-emerald-600',
                    'text'         => 'text-white',
                    'icon'         => 'timer',
                    'meta_key'     => '_post_read_time_label',
                    'meta_default' => '2 hrs',
                ],
                'reci_event' => [
                    'label'        => 'Event',
                    'badge'        => 'bg-amber-400',
                    'text'         => 'text-neutral-800',
                    'icon'         => 'timer',
                    'meta_key'     => '_reci_event_time_label',
                    'meta_default' => '3PM EST',
                ],
            ];

            if (isset($map[$post_type])) {
                return $map[$post_type];
            }

            $fallback_label = ucwords(str_replace('_', ' ', str_replace('reci_', '', $post_type)));
            return [
                'label'        => $fallback_label !== '' ? $fallback_label : 'Post',
                'badge'        => 'bg-reci-blue',
                'text'         => 'text-white',
                'icon'         => 'timer',
                'meta_key'     => '_post_read_time_label',
                'meta_default' => '3 mins',
            ];
        }

        /**
         * @param int                  $post_id   Post id.
         * @param string               $post_type Post type.
         * @param array<string,string> $type_cfg  Type config.
         */
        protected static function meta_value(int $post_id, string $post_type, array $type_cfg): string {
            $meta_value = (string) get_post_meta($post_id, $type_cfg['meta_key'], true);
            if ($meta_value !== '') {
                return $meta_value;
            }

            if ('reci_podcast' === $post_type) {
                return self::duration_from_seconds((int) get_post_meta($post_id, '_reci_podcast_duration_secs', true), $type_cfg['meta_default']);
            }

            if ('reci_video' === $post_type) {
                return self::duration_from_seconds((int) get_post_meta($post_id, '_reci_video_duration_secs', true), $type_cfg['meta_default']);
            }

            return $type_cfg['meta_default'];
        }

        protected static function duration_from_seconds(int $seconds, string $fallback): string {
            if ($seconds <= 0) {
                return $fallback;
            }

            $hours = (int) floor($seconds / 3600);
            $mins  = (int) floor(($seconds % 3600) / 60);
            $secs  = (int) ($seconds % 60);

            if ($hours > 0) {
                return sprintf('%d:%02d:%02d', $hours, $mins, $secs);
            }

            return sprintf('%02d:%02d', $mins, $secs);
        }
    }
}
