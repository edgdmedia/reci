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

            $image_size      = (string) ($args['image_size'] ?? 'medium');
            $fallback_image  = (string) ($args['fallback_image'] ?? 'https://placehold.co/460x232');
            $excerpt_words   = (int) ($args['excerpt_words'] ?? 20);
            $tag_limit       = (int) ($args['tag_limit'] ?? 3);
            $date_format     = (string) ($args['date_format'] ?? 'd M Y');
            $overrides       = is_array($args['overrides'] ?? null) ? $args['overrides'] : [];

            $thumb_id  = get_post_thumbnail_id($post_id);
            $image_alt = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
            if ($image_alt === '') {
                $image_alt = get_the_title($post_id);
            }

            $meta_value = (string) get_post_meta($post_id, $type_cfg['meta_key'], true);
            if ($meta_value === '') {
                $meta_value = $type_cfg['meta_default'];
            }

            $tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
            if (is_wp_error($tags)) {
                $tags = [];
            }
            $tags = array_slice(array_values(array_unique(array_filter(array_map('trim', $tags)))), 0, max(1, $tag_limit));

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
                'image_url'        => get_the_post_thumbnail_url($post_id, $image_size) ?: $fallback_image,
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
                'reci_article' => [
                    'label'        => 'Article',
                    'badge'        => 'bg-amber-400',
                    'text'         => 'text-neutral-800',
                    'icon'         => 'timer',
                    'meta_key'     => '_reci_article_read_time_label',
                    'meta_default' => '3 mins',
                ],
                'reci_podcast' => [
                    'label'        => 'Podcast',
                    'badge'        => 'bg-neutral-800',
                    'text'         => 'text-white',
                    'icon'         => 'audio',
                    'meta_key'     => '_reci_podcast_duration_label',
                    'meta_default' => '07:27',
                ],
                'reci_video' => [
                    'label'        => 'Video',
                    'badge'        => 'bg-blue-900',
                    'text'         => 'text-white',
                    'icon'         => 'video',
                    'meta_key'     => '_reci_video_duration_label',
                    'meta_default' => '07:27',
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
                'badge'        => 'bg-amber-400',
                'text'         => 'text-neutral-800',
                'icon'         => 'timer',
                'meta_key'     => '_reci_article_read_time_label',
                'meta_default' => '3 mins',
            ];
        }
    }
}
