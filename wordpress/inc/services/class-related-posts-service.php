<?php
/**
 * Related posts service.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Related_Posts_Service')) {
    class RECI_Related_Posts_Service {
        /**
         * Get related posts for a post.
         *
         * @param int                 $post_id Post ID.
         * @param array<string,mixed> $args    Args.
         * @return array<int,array<string,mixed>>
         */
        public static function get_related(int $post_id, array $args = []): array {
            if ($post_id <= 0) {
                return [];
            }

            $post_type  = (string) ($args['post_type'] ?? get_post_type($post_id));
            $limit      = max(1, (int) ($args['limit'] ?? 4));
            $taxonomy   = (string) ($args['taxonomy'] ?? self::resolve_primary_taxonomy($post_type));
            $format_args = is_array($args['format_args'] ?? null) ? $args['format_args'] : [];

            $base_query = [
                'post_type'      => $post_type,
                'posts_per_page' => $limit,
                'post__not_in'   => [$post_id],
                'orderby'        => 'date',
                'order'          => 'DESC',
                'no_found_rows'  => true,
            ];

            $term_ids = [];
            if ($taxonomy !== '') {
                $terms = get_the_terms($post_id, $taxonomy);
                if ($terms && ! is_wp_error($terms)) {
                    $term_ids = array_map('intval', wp_list_pluck($terms, 'term_id'));
                }
            }

            if (! empty($term_ids) && $taxonomy !== '') {
                $base_query['tax_query'] = [[
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                ]];
            }

            $result = RECI_Post_Query_Service::get_formatted_items($base_query, $format_args);
            $items  = $result['items'];

            if (! empty($items)) {
                return $items;
            }

            $fallback = RECI_Post_Query_Service::get_formatted_items([
                'post_type'      => $post_type,
                'posts_per_page' => $limit,
                'post__not_in'   => [$post_id],
                'orderby'        => 'date',
                'order'          => 'DESC',
                'no_found_rows'  => true,
            ], $format_args);

            return $fallback['items'];
        }

        /**
         * Resolve default taxonomy for related posts.
         */
        protected static function resolve_primary_taxonomy(string $post_type): string {
            if ($post_type === 'reci_article') {
                return 'category';
            }

            if (taxonomy_exists('reci_topic')) {
                return 'reci_topic';
            }

            return 'post_tag';
        }
    }
}
