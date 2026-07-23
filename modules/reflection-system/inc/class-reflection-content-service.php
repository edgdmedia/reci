<?php
/**
 * Reflection content service.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Content_Service')) {
    class RECI_Reflection_Content_Service {
	/**
	 * Build a reflection payload with normalized scenes.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string,mixed>
	 */
	public static function get_payload(int $post_id): array {
		if ($post_id <= 0) {
			return [];
		}

		$cache_key = 'reci_reflection_payload_' . $post_id;
		$cached = get_transient($cache_key);
		if (is_array($cached)) {
			return $cached;
		}

		$post = get_post($post_id);
		if (! $post instanceof WP_Post) {
			return [];
		}

		$body_content = has_excerpt($post_id) ? (string) get_the_excerpt($post_id) : get_the_title($post_id);
			$quote                = $body_content;
			$speaker              = '';
			$role                 = '';
            $background_image_id  = (int) get_post_meta($post_id, '_reci_reflection_background_image_id', true);
            $background_image_url = (string) get_post_meta($post_id, '_reci_reflection_background_image_url', true);
			$video_url            = '';
			$audio_url            = '';
            $mode                 = (string) get_post_meta($post_id, '_reci_reflection_mode', true);
            $template             = (string) get_post_meta($post_id, '_reci_reflection_template', true);
            $theme                = (string) get_post_meta($post_id, '_reci_reflection_theme', true);
            $accent               = (string) get_post_meta($post_id, '_reci_reflection_accent', true);
            $default_columns      = (string) get_post_meta($post_id, '_reci_reflection_default_columns', true);
            $default_spacing      = (string) get_post_meta($post_id, '_reci_reflection_default_spacing', true);
            $default_text_width   = (string) get_post_meta($post_id, '_reci_reflection_default_text_width', true);
            $raw                  = (string) get_post_meta($post_id, '_reci_reflection_blueprint', true);

            if ($background_image_url === '' && $background_image_id > 0) {
                $background_image_url = wp_get_attachment_url($background_image_id) ?: '';
            }

            $thumb_id  = $background_image_id > 0 ? $background_image_id : get_post_thumbnail_id($post_id);
            $image_alt = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
            if ($image_alt === '') {
                $image_alt = get_the_title($post_id);
            }

            $topic_terms = wp_get_post_terms($post_id, 'reci_topic');
            if (is_wp_error($topic_terms)) {
                $topic_terms = [];
            }

            $topics = [];
            foreach ($topic_terms as $term) {
                if (! $term instanceof WP_Term) {
                    continue;
                }

                $term_link = get_term_link($term);
                $topics[]  = [
                    'name' => $term->name,
                    'url'  => is_wp_error($term_link) ? '' : $term_link,
                ];
            }

            $blueprint = self::parse_blueprint($raw);

			$payload = [
				'id'                 => $post_id,
				'title'              => get_the_title($post_id),
				'permalink'          => get_permalink($post_id),
				'content'            => wpautop(wp_kses_post($body_content)),
				'content_raw'        => wp_strip_all_tags($body_content),
				'quote'              => $quote,
                'speaker'            => $speaker,
                'role'               => $role,
                'video_url'          => $video_url,
                'audio_url'          => $audio_url,
                'mode'               => $mode !== '' ? $mode : 'immersive',
                'template'           => $template !== '' ? $template : 'narrative',
                'appearance'         => array_merge(
                    [
                        'theme'      => $theme !== '' ? $theme : 'immersive-dark',
                        'accent'     => $accent !== '' ? $accent : 'amber',
                        'columns'    => $default_columns !== '' ? $default_columns : 'auto',
                        'spacing'    => $default_spacing !== '' ? $default_spacing : 'comfortable',
                        'text_width' => $default_text_width !== '' ? $default_text_width : 'default',
                    ],
                    is_array($blueprint['appearance'] ?? null) ? $blueprint['appearance'] : [],
                ),
                'featured_image_url' => $background_image_url !== '' ? $background_image_url : (get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/1440x750'),
                'featured_image_alt' => $image_alt,
                'topics'             => $topics,
                'topic_badge'        => isset($topics[0]['name']) ? (string) $topics[0]['name'] : '',
                'blueprint'          => $blueprint,
            ];

            $payload['scenes'] = ! empty($blueprint['scenes'])
                ? self::normalize_scenes($blueprint['scenes'], $payload)
			: self::build_fallback_scenes($payload);

		set_transient($cache_key, $payload, HOUR_IN_SECONDS);
		return $payload;
	}

	/**
	 * Invalidate the payload cache for a reflection post.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function invalidate_cache(int $post_id): void {
		delete_transient('reci_reflection_payload_' . $post_id);
	}

        /**
         * @param string $raw Raw JSON.
         * @return array<string,mixed>
         */
        protected static function parse_blueprint(string $raw): array {
            if ($raw === '') {
                return [];
            }

            $decoded = json_decode($raw, true);
            if (! is_array($decoded)) {
                return [];
            }

            if ((isset($decoded['scenes']) && is_array($decoded['scenes'])) || (isset($decoded['chapters']) && is_array($decoded['chapters']))) {
                return $decoded;
            }

            if (array_is_list($decoded)) {
                return [
                    'template' => 'custom',
                    'scenes'   => $decoded,
                ];
            }

            return [];
        }

        /**
         * @param array<int,mixed>    $scenes  Raw scenes.
         * @param array<string,mixed> $payload Payload.
         * @return array<int,array<string,mixed>>
         */
        protected static function normalize_scenes(array $scenes, array $payload): array {
            $items = [];

            foreach ($scenes as $index => $scene) {
                if (! is_array($scene)) {
                    continue;
                }

                $type = sanitize_key((string) ($scene['type'] ?? 'rich_text'));
                $appearance = is_array($payload['appearance'] ?? null) ? $payload['appearance'] : [];
                $item = [
                    'type'       => $type,
                    'title'      => (string) ($scene['title'] ?? ''),
                    'content'    => (string) ($scene['content'] ?? ''),
                    'layout'     => (string) ($scene['layout'] ?? ($appearance['layout'] ?? 'inherit')),
                    'columns'    => (string) ($scene['columns'] ?? ($appearance['columns'] ?? 'auto')),
                    'spacing'    => (string) ($scene['spacing'] ?? ($appearance['spacing'] ?? 'comfortable')),
                    'text_width' => (string) ($scene['text_width'] ?? ($appearance['text_width'] ?? 'default')),
                    'accent'     => (string) ($scene['accent'] ?? ($appearance['accent'] ?? 'amber')),
                    'items'      => is_array($scene['items'] ?? null) ? $scene['items'] : [],
                    'media'      => is_array($scene['media'] ?? null) ? $scene['media'] : [],
                    'quote'      => (string) ($scene['quote'] ?? ''),
                    'speaker'    => (string) ($scene['speaker'] ?? ''),
                    'role'       => (string) ($scene['role'] ?? ''),
                    'theme'      => (string) ($scene['theme'] ?? ($appearance['theme'] ?? 'immersive-dark')),
                    'template'   => (string) ($scene['template'] ?? ($payload['template'] ?? 'narrative')),
                    'id'         => sanitize_html_class((string) ($scene['id'] ?? ('scene-' . ($index + 1)))),
                ];

                if (in_array($type, ['gallery', 'timeline', 'documents', 'prompt_list', 'compare_panels', 'hotspots'], true)) {
                    if (empty($item['items']) && is_array($scene['panels'] ?? null)) {
                        $item['items'] = $scene['panels'];
                    }

                    if ($type === 'hotspots') {
                        $item['image_url'] = (string) ($scene['image_url'] ?? ($scene['background_image_url'] ?? $payload['featured_image_url']));
                        $item['image_alt'] = (string) ($scene['image_alt'] ?? $payload['featured_image_alt']);
                    }
                }

                if ($type === 'hero') {
                    $item['title']                = $item['title'] !== '' ? $item['title'] : (string) $payload['title'];
                    $item['quote']                = $item['quote'] !== '' ? $item['quote'] : (string) $payload['quote'];
                    $item['speaker']              = $item['speaker'] !== '' ? $item['speaker'] : (string) $payload['speaker'];
                    $item['role']                 = $item['role'] !== '' ? $item['role'] : (string) $payload['role'];
                    $item['badge']                = (string) ($scene['badge'] ?? ($payload['topic_badge'] ?? ''));
                    $item['background_image_url'] = (string) ($scene['background_image_url'] ?? $payload['featured_image_url']);
                }

                if ($type === 'rich_text' && $item['content'] === '') {
                    $item['content'] = (string) $payload['content'];
                }

                if ($type === 'quote') {
                    $item['quote']   = $item['quote'] !== '' ? $item['quote'] : (string) $payload['quote'];
                    $item['speaker'] = $item['speaker'] !== '' ? $item['speaker'] : (string) $payload['speaker'];
                    $item['role']    = $item['role'] !== '' ? $item['role'] : (string) $payload['role'];
                }

                if ($type === 'media_embed') {
                    $item['video_url'] = (string) ($scene['video_url'] ?? ($payload['video_url'] ?? ''));
                    $item['audio_url'] = (string) ($scene['audio_url'] ?? ($payload['audio_url'] ?? ''));
                }

                $items[] = $item;
            }

            return $items;
        }

        /**
         * @param array<string,mixed> $payload Payload.
         * @return array<int,array<string,mixed>>
         */
        protected static function build_fallback_scenes(array $payload): array {
            $scenes = [
                [
                    'type'                 => 'hero',
                    'template'             => (string) ($payload['template'] ?? 'narrative'),
                    'theme'                => (string) (($payload['appearance']['theme'] ?? 'immersive-dark')),
                    'accent'               => (string) (($payload['appearance']['accent'] ?? 'amber')),
                    'spacing'              => (string) (($payload['appearance']['spacing'] ?? 'comfortable')),
                    'text_width'           => (string) (($payload['appearance']['text_width'] ?? 'default')),
                    'columns'              => (string) (($payload['appearance']['columns'] ?? 'auto')),
                    'layout'               => 'inherit',
                    'id'                   => 'scene-hero',
                    'title'                => (string) $payload['title'],
                    'quote'                => (string) $payload['quote'],
                    'speaker'              => (string) $payload['speaker'],
                    'role'                 => (string) $payload['role'],
                    'badge'                => (string) ($payload['topic_badge'] ?? ''),
                    'background_image_url' => (string) $payload['featured_image_url'],
                ],
                [
                    'type'     => 'rich_text',
                    'template' => (string) ($payload['template'] ?? 'narrative'),
                    'theme'    => (string) (($payload['appearance']['theme'] ?? 'immersive-dark')),
                    'accent'   => (string) (($payload['appearance']['accent'] ?? 'amber')),
                    'spacing'  => (string) (($payload['appearance']['spacing'] ?? 'comfortable')),
                    'text_width' => (string) (($payload['appearance']['text_width'] ?? 'default')),
                    'columns'  => (string) (($payload['appearance']['columns'] ?? 'auto')),
                    'layout'   => 'inherit',
                    'id'       => 'scene-body',
                    'title'    => (string) $payload['title'],
                    'content'  => (string) $payload['content'],
                ],
            ];

            if (! empty($payload['video_url']) || ! empty($payload['audio_url'])) {
                $scenes[] = [
                    'type'      => 'media_embed',
                    'template'  => (string) ($payload['template'] ?? 'narrative'),
                    'theme'     => (string) (($payload['appearance']['theme'] ?? 'immersive-dark')),
                    'accent'    => (string) (($payload['appearance']['accent'] ?? 'amber')),
                    'spacing'   => (string) (($payload['appearance']['spacing'] ?? 'comfortable')),
                    'text_width'=> (string) (($payload['appearance']['text_width'] ?? 'default')),
                    'columns'   => (string) (($payload['appearance']['columns'] ?? 'auto')),
                    'layout'    => 'inherit',
                    'id'        => 'scene-media',
                    'title'     => 'Media',
                    'video_url' => (string) ($payload['video_url'] ?? ''),
                    'audio_url' => (string) ($payload['audio_url'] ?? ''),
                ];
            }

            return $scenes;
        }
    }
}

add_action('save_post_reci_reflection', [RECI_Reflection_Content_Service::class, 'invalidate_cache']);
add_action('delete_post_reci_reflection', [RECI_Reflection_Content_Service::class, 'invalidate_cache']);
