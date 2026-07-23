<?php
/**
 * Query service for reusable post fetching.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Post_Query_Service')) {
    class RECI_Post_Query_Service {
        /**
         * Build and run a query.
         *
         * @param array<string,mixed> $args Query args.
         */
        public static function query(array $args = []): WP_Query {
            $defaults = [
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 10,
                'paged'          => max(1, (int) get_query_var('paged'), (int) get_query_var('page')),
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post__in'       => [],
                'post__not_in'   => [],
                'tax_query'      => [],
                'meta_query'     => [],
                'author'         => 0,
                's'              => '',
                'no_found_rows'  => false,
            ];

            $query_args = wp_parse_args($args, $defaults);

            $query_args['posts_per_page'] = max(1, (int) $query_args['posts_per_page']);
            $query_args['paged']          = max(1, (int) $query_args['paged']);
            $query_args['order']          = strtoupper((string) $query_args['order']) === 'ASC' ? 'ASC' : 'DESC';

            return new WP_Query($query_args);
        }

        /**
         * Build and run a query, returning only posts.
         *
         * @param array<string,mixed> $args Query args.
         * @return array<int,WP_Post>
         */
        public static function get_posts(array $args = []): array {
            return self::query($args)->posts;
        }

        /**
         * Get the first post from a result set.
         *
         * @param array<int,WP_Post> $posts Posts.
         */
        public static function first(array $posts): ?WP_Post {
            return $posts[0] ?? null;
        }

        /**
         * Slice a result set for section composition.
         *
         * @param array<int,WP_Post> $posts  Posts.
         * @param int                $offset Offset.
         * @param int|null           $length Length.
         * @return array<int,WP_Post>
         */
        public static function slice(array $posts, int $offset, ?int $length = null): array {
            return array_values(array_slice($posts, $offset, $length));
        }

        /**
         * Extract post IDs from a result set.
         *
         * @param array<int,WP_Post|int> $posts Posts.
         * @return array<int,int>
         */
        public static function ids(array $posts): array {
            return array_values(array_filter(array_map(static function ($post): int {
                if ($post instanceof WP_Post) {
                    return (int) $post->ID;
                }

                return (int) $post;
            }, $posts)));
        }

        /**
         * Get formatted listing data from query args.
         *
         * @param array<string,mixed> $query_args  Query args.
         * @param array<string,mixed> $format_args Format args.
         * @return array{items:array<int,array<string,mixed>>,query:WP_Query}
         */
        public static function get_formatted_items(array $query_args = [], array $format_args = []): array {
            $query = self::query($query_args);
            $items = RECI_Post_Format_Service::format_posts($query->posts, $format_args);

            return [
                'items' => $items,
                'query' => $query,
            ];
        }

        /**
         * Render standard pagination markup.
         *
         * @param WP_Query            $query Query object.
         * @param array<string,mixed> $args  Pagination args.
         */
        public static function render_pagination(WP_Query $query, array $args = []): string {
            if ((int) $query->max_num_pages <= 1) {
                return '';
            }

            $paged         = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
            $base_url      = (string) ($args['base_url'] ?? get_pagenum_link(1));
            $param_name    = (string) ($args['param_name'] ?? 'paged');
            $wrapper_class = (string) ($args['wrapper_class'] ?? 'mt-8 flex items-center justify-center gap-2');
            $item_class    = (string) ($args['item_class'] ?? "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100");
            $current_class = (string) ($args['current_class'] ?? "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg bg-[#003594] text-sm font-medium text-white");

            $links = paginate_links([
                'base'      => add_query_arg($param_name, '%#%', $base_url),
                'format'    => '',
                'current'   => $paged,
                'total'     => (int) $query->max_num_pages,
                'type'      => 'array',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ]);

            if (empty($links) || ! is_array($links)) {
                return '';
            }

            $html = '<nav class="' . esc_attr($wrapper_class) . '" aria-label="Pagination">';
            foreach ($links as $link) {
                $is_current = strpos($link, 'current') !== false;
                $class      = $is_current ? $current_class : $item_class;
                $html      .= '<span class="' . esc_attr($class) . '">' . $link . '</span>';
            }
            $html .= '</nav>';

            return $html;
        }
    }
}
