<?php
/**
 * Unified content feed service for formatted homepage and listing feeds.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Content_Feed')) {
    class RECI_Content_Feed {
        /**
         * Query and format a content feed into reusable item arrays.
         *
         * @param array<string,mixed> $query_args  Query args.
         * @param array<string,mixed> $format_args Format args.
         * @return array<int,array<string,mixed>>
         */
        public static function query(array $query_args = [], array $format_args = []): array {
            $defaults = [
                'image_size'                => 'large',
                'fallback_image'            => 'https://placehold.co/460x232',
                'excerpt_words'             => 22,
                'tag_limit'                 => 3,
                'date_format'               => 'd M Y',
                'include_category_fallback' => true,
            ];

            $items = RECI_Post_Query_Service::get_formatted_items(
                $query_args,
                wp_parse_args($format_args, $defaults)
            );

            return $items['items'];
        }

        /**
         * Get the first item from a feed.
         *
         * @param array<int,array<string,mixed>> $items Feed items.
         * @return array<string,mixed>|null
         */
        public static function first(array $items): ?array {
            return $items[0] ?? null;
        }

        /**
         * Slice feed items for section composition.
         *
         * @param array<int,array<string,mixed>> $items  Feed items.
         * @param int                            $offset Offset.
         * @param int|null                       $length Length.
         * @return array<int,array<string,mixed>>
         */
        public static function slice(array $items, int $offset, ?int $length = null): array {
            return array_values(array_slice($items, $offset, $length));
        }

        /**
         * Extract item ids from a feed.
         *
         * @param array<int,array<string,mixed>> $items Feed items.
         * @return array<int,int>
         */
        public static function ids(array $items): array {
            return array_values(array_filter(array_map(static function (array $item): int {
                return (int) ($item['id'] ?? 0);
            }, $items)));
        }
    }
}
