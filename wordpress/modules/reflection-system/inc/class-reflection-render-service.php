<?php
/**
 * Reflection render service.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Render_Service')) {
    class RECI_Reflection_Render_Service {
        /**
         * @param array<int,array<string,mixed>> $scenes Scenes.
         */
        public static function render_scenes(array $scenes): void {
            _deprecated_function(__METHOD__, '2.0', 'RECI_Reflection_System_Render_Service::render_components');
            foreach ($scenes as $scene) {
                self::render_scene($scene);
            }
        }

        /**
         * @param array<int,array<string,mixed>> $chapters Chapters.
         */
        public static function render_chapters(array $chapters): void {
            _deprecated_function(__METHOD__, '2.0', 'RECI_Reflection_System_Render_Service::render_components');
            foreach ($chapters as $chapter) {
                self::render_chapter($chapter);
            }
        }

        /**
         * @param array<string,mixed> $scene Scene config.
         */
        public static function render_scene(array $scene): void {
            $type = sanitize_key((string) ($scene['type'] ?? 'rich_text'));
            $slug = 'template-parts/reflection/scenes/scene-' . str_replace('_', '-', $type);

            if (locate_template($slug . '.php', false, false)) {
                get_template_part($slug, null, ['scene' => $scene]);
                return;
            }

            get_template_part('template-parts/reflection/scenes/scene-rich-text', null, ['scene' => $scene]);
        }

        /**
         * @param array<string,mixed> $chapter Chapter config.
         */
        public static function render_chapter(array $chapter): void {
            $type = sanitize_key((string) ($chapter['type'] ?? 'content_stage'));
            $slug = 'template-parts/reflection/chapters/chapter-' . str_replace('_', '-', $type);

            if (locate_template($slug . '.php', false, false)) {
                get_template_part($slug, null, ['chapter' => $chapter]);
                return;
            }

            get_template_part('template-parts/reflection/chapters/chapter-content-stage', null, ['chapter' => $chapter]);
        }
    }
}
