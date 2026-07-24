<?php
/**
 * Single post data service.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Single_Post_Service')) {
    class RECI_Single_Post_Service {
        /**
         * Build payload for a single post page.
         *
         * @param int                 $post_id Post ID.
         * @param array<string,mixed> $args    Args.
         * @return array<string,mixed>
         */
        public static function get_post_payload(int $post_id, array $args = []): array {
            if ($post_id <= 0) {
                return [];
            }

            $post_type         = (string) get_post_type($post_id);
            $subtitle_meta_key = (string) ($args['subtitle_meta_key'] ?? '');
            $subtitle          = '';

            if ($subtitle_meta_key !== '') {
                $subtitle = (string) get_post_meta($post_id, $subtitle_meta_key, true);
            }
            if ($subtitle === '') {
                $subtitle = has_excerpt($post_id) ? get_the_excerpt($post_id) : '';
            }

            $featured_image_url = get_the_post_thumbnail_url($post_id, (string) ($args['image_size'] ?? 'large')) ?: (string) ($args['fallback_image'] ?? 'https://placehold.co/800x446');
            $thumb_id           = get_post_thumbnail_id($post_id);
            $featured_image_alt = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
            if ($featured_image_alt === '') {
                $featured_image_alt = get_the_title($post_id);
            }

            $item = RECI_Post_Format_Service::format_post($post_id, [
                'image_size' => (string) ($args['image_size'] ?? 'large'),
                'tag_limit'  => (int) ($args['tag_limit'] ?? 3),
            ]);
            $author = reci_media_hub_get_display_author($post_id);

            return [
                'id'                 => $post_id,
                'post_type'          => $post_type,
                'title'              => get_the_title($post_id),
                'subtitle'           => $subtitle,
                'date'               => get_the_date((string) ($args['date_format'] ?? 'd M Y'), $post_id),
                'meta_value'         => (string) ($item['meta_value'] ?? ''),
                'meta_icon'          => (string) ($item['meta_icon'] ?? 'timer'),
                'tags'               => is_array($item['tags'] ?? null) ? $item['tags'] : [],
                'type_label'         => (string) ($item['type_label'] ?? 'Post'),
                'type_badge_class'   => (string) ($item['type_badge_class'] ?? 'bg-amber-400'),
                'type_text_class'    => (string) ($item['type_text_class'] ?? 'text-neutral-800'),
                'featured_image_url' => $featured_image_url,
                'featured_image_alt' => $featured_image_alt,
                'permalink'          => get_permalink($post_id),
                'author_id'          => (int) get_post_field('post_author', $post_id),
                'author_name'        => (string) ($author['name'] ?? ''),
                'author'             => $author,
                'sphere_terms'       => is_array($item['sphere_terms'] ?? null) ? $item['sphere_terms'] : [],
            ];
        }
    }
}
