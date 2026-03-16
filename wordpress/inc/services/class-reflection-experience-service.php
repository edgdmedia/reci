<?php
/**
 * Reflection immersive experience service.
 *
 * Normalizes reflection blueprints into a chapter/progression contract
 * that a dedicated frontend runtime can execute.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Experience_Service')) {
    class RECI_Reflection_Experience_Service {
        /**
         * Build immersive experience payload from reflection payload.
         *
         * @param array<string,mixed> $payload Reflection payload.
         * @return array<string,mixed>
         */
        public static function get_payload(array $payload): array {
            $blueprint   = is_array($payload['blueprint'] ?? null) ? $payload['blueprint'] : [];
            $appearance  = is_array($payload['appearance'] ?? null) ? $payload['appearance'] : [];
            $template    = (string) ($payload['template'] ?? 'narrative');
            $mode        = (string) ($blueprint['mode'] ?? ($payload['mode'] ?? 'standard'));
            $chapters    = self::normalize_chapters($blueprint, $payload);
            $progression = self::build_progression($chapters);

            return [
                'mode'       => $mode,
                'template'   => $template,
                'appearance' => [
                    'theme'      => (string) ($appearance['theme'] ?? 'immersive-dark'),
                    'accent'     => (string) ($appearance['accent'] ?? 'amber'),
                    'columns'    => (string) ($appearance['columns'] ?? 'auto'),
                    'spacing'    => (string) ($appearance['spacing'] ?? 'comfortable'),
                    'text_width' => (string) ($appearance['text_width'] ?? 'default'),
                ],
                'chapters'   => $chapters,
                'progression'=> $progression,
            ];
        }

        /**
         * @param array<string,mixed> $blueprint
         * @param array<string,mixed> $payload
         * @return array<int,array<string,mixed>>
         */
        protected static function normalize_chapters(array $blueprint, array $payload): array {
            $raw_chapters = is_array($blueprint['chapters'] ?? null) ? $blueprint['chapters'] : [];

            if (empty($raw_chapters)) {
                return self::fallback_chapters($payload);
            }

            $chapters = [];
            $count    = count($raw_chapters);

            foreach ($raw_chapters as $index => $chapter) {
                if (! is_array($chapter)) {
                    continue;
                }

                $id           = sanitize_html_class((string) ($chapter['id'] ?? ('chapter-' . ($index + 1))));
                $type         = sanitize_key((string) ($chapter['type'] ?? 'content_stage'));
                $label        = (string) ($chapter['label'] ?? $chapter['title'] ?? ('Chapter ' . ($index + 1)));
                $presentation = is_array($chapter['presentation'] ?? null) ? $chapter['presentation'] : [];
                $state        = is_array($chapter['state'] ?? null) ? $chapter['state'] : [];
                $content      = is_array($chapter['content'] ?? null) ? $chapter['content'] : [];
                $interaction  = is_array($chapter['interaction'] ?? null) ? $chapter['interaction'] : [];
                $completion   = is_array($state['completion'] ?? null) ? $state['completion'] : [];
                $next_id      = '';

                if ($index < ($count - 1) && isset($raw_chapters[$index + 1]) && is_array($raw_chapters[$index + 1])) {
                    $next_id = sanitize_html_class((string) ($raw_chapters[$index + 1]['id'] ?? ('chapter-' . ($index + 2))));
                }

                $chapters[] = [
                    'id' => $id,
                    'type' => $type,
                    'label' => $label,
                    'title' => (string) ($chapter['title'] ?? ''),
                    'template' => (string) ($chapter['template'] ?? ($payload['template'] ?? 'narrative')),
                    'presentation' => [
                        'fullscreen' => array_key_exists('fullscreen', $presentation) ? (bool) $presentation['fullscreen'] : true,
                        'theme' => (string) ($presentation['theme'] ?? ($payload['appearance']['theme'] ?? 'immersive-dark')),
                        'layout' => (string) ($presentation['layout'] ?? 'full'),
                        'accent' => (string) ($presentation['accent'] ?? ($payload['appearance']['accent'] ?? 'amber')),
                    ],
                    'state' => [
                        'initial' => (string) ($state['initial'] ?? ($index === 0 ? 'active' : 'locked')),
                        'completion' => [
                            'trigger' => (string) ($completion['trigger'] ?? self::default_trigger_for_type($type)),
                            'target'  => (string) ($completion['target'] ?? $next_id),
                            'min_required' => isset($completion['min_required']) ? (int) $completion['min_required'] : null,
                        ],
                    ],
                    'content' => self::normalize_content($type, $content, $payload),
                    'interaction' => $interaction,
                ];
            }

            return $chapters;
        }

        /**
         * @param string $type
         * @param array<string,mixed> $content
         * @param array<string,mixed> $payload
         * @return array<string,mixed>
         */
        protected static function normalize_content(string $type, array $content, array $payload): array {
            // Only inject a featured-image fallback for types that display a full-bleed background.
            $types_with_background = ['threshold_intro', 'content_stage', 'hotspot_stage', 'parallax_stage'];
            if (in_array($type, $types_with_background, true) && empty($content['background_image_url'])) {
                $featured = (string) ($payload['featured_image_url'] ?? '');
                // Only use the featured image — never inject a placeholder URL.
                if ($featured !== '' && strpos($featured, 'placehold.co') === false) {
                    $content['background_image_url'] = $featured;
                }
            }

            if ($type === 'threshold_intro') {
                $content['title']    = (string) ($content['title'] ?? ($payload['title'] ?? ''));
                $content['subtitle'] = (string) ($content['subtitle'] ?? ($payload['quote'] ?? ''));
            }

            if ($type === 'hotspot_stage') {
                if (empty($content['items'])) {
                    $content['items'] = [];
                }
                if (empty($content['continue_label']) && ! empty($content['button_label'])) {
                    $content['continue_label'] = (string) $content['button_label'];
                }
            }

            if ($type === 'progressive_text') {
                if (empty($content['paragraphs']) && ! empty($content['items']) && is_array($content['items'])) {
                    $content['paragraphs'] = $content['items'];
                }
                if (empty($content['prompt']) && ! empty($content['placeholder'])) {
                    $content['prompt'] = (string) $content['placeholder'];
                }
                if (empty($content['continue_label']) && ! empty($content['secondary_button_label'])) {
                    $content['continue_label'] = (string) $content['secondary_button_label'];
                }
            }

            if ($type === 'horizontal_panels') {
                if (empty($content['items'])) {
                    $content['items'] = [];
                }
                if (empty($content['continue_label']) && ! empty($content['button_label'])) {
                    $content['continue_label'] = (string) $content['button_label'];
                }
            }

            if ($type === 'step_sequence') {
                if (empty($content['continue_label']) && ! empty($content['placeholder'])) {
                    $content['continue_label'] = (string) $content['placeholder'];
                }
            }

            if ($type === 'data_cards') {
                if (empty($content['continue_label']) && ! empty($content['button_label'])) {
                    $content['continue_label'] = (string) $content['button_label'];
                }
            }

            if ($type === 'drag_reveal') {
                if (empty($content['instruction']) && ! empty($content['placeholder'])) {
                    $content['instruction'] = (string) $content['placeholder'];
                }
                if (empty($content['continue_label']) && ! empty($content['button_label'])) {
                    $content['continue_label'] = (string) $content['button_label'];
                }
            }

            if ($type === 'word_shift') {
                if (empty($content['continue_label']) && ! empty($content['button_label'])) {
                    $content['continue_label'] = (string) $content['button_label'];
                }
            }

            if ($type === 'parallax_stage') {
                if (empty($content['items'])) {
                    $content['items'] = [];
                }
            }

            return $content;
        }

        /**
         * @param string $type
         * @return string
         */
        protected static function default_trigger_for_type(string $type): string {
            $map = [
                'threshold_intro'   => 'button',
                'hotspot_stage'     => 'all_hotspots',
                'progressive_text'  => 'manual_continue',
                'threshold_message' => 'button',
                'horizontal_panels' => 'last_panel',
                'reflection_prompt' => 'manual_continue',
                'content_stage'     => 'manual_continue',
                'step_sequence'     => 'all_steps',
                'data_cards'        => 'manual_continue',
                'drag_reveal'       => 'drag_break',
                'word_shift'        => 'manual_continue',
                'parallax_stage'    => 'manual_continue',
            ];

            return $map[$type] ?? 'manual_continue';
        }

        /**
         * @param array<int,array<string,mixed>> $chapters
         * @return array<string,mixed>
         */
        protected static function build_progression(array $chapters): array {
            $order        = [];
            $initial      = '';
            $transitions  = [];

            foreach ($chapters as $chapter) {
                $id = (string) ($chapter['id'] ?? '');
                if ($id === '') {
                    continue;
                }

                if ($initial === '' && (string) ($chapter['state']['initial'] ?? '') === 'active') {
                    $initial = $id;
                }

                $order[] = $id;
                $transitions[$id] = [
                    'trigger' => (string) ($chapter['state']['completion']['trigger'] ?? 'manual_continue'),
                    'target'  => (string) ($chapter['state']['completion']['target'] ?? ''),
                    'min_required' => $chapter['state']['completion']['min_required'] ?? null,
                ];
            }

            if ($initial === '' && ! empty($order)) {
                $initial = $order[0];
            }

            return [
                'initial' => $initial,
                'order' => $order,
                'transitions' => $transitions,
                'navigation' => [
                    'type' => 'hidden',
                    'show_labels' => false,
                ],
            ];
        }

        /**
         * @param array<string,mixed> $payload
         * @return array<int,array<string,mixed>>
         */
        protected static function fallback_chapters(array $payload): array {
            return [
                [
                    'id' => 'chapter-intro',
                    'type' => 'content_stage',
                    'label' => 'Intro',
                    'title' => (string) ($payload['title'] ?? ''),
                    'template' => (string) ($payload['template'] ?? 'narrative'),
                    'presentation' => [
                        'fullscreen' => true,
                        'theme' => (string) ($payload['appearance']['theme'] ?? 'immersive-dark'),
                        'layout' => 'full',
                        'accent' => (string) ($payload['appearance']['accent'] ?? 'amber'),
                    ],
                    'state' => [
                        'initial' => 'active',
                        'completion' => [
                            'trigger' => 'manual_continue',
                            'target' => '',
                            'min_required' => null,
                        ],
                    ],
                    'content' => [
                        'title' => (string) ($payload['title'] ?? ''),
                        'content' => wp_strip_all_tags((string) ($payload['content_raw'] ?? '')),
                        'button_label' => __('Continue', 'reci-media-hub'),
                    ],
                    'interaction' => [],
                ],
            ];
        }
    }
}
