<?php
/**
 * Render Chapter REST endpoint.
 *
 * POST /wp-json/reci/v1/render-chapter
 * Body: { chapter: { family, variant, props }, post_id: int }
 *
 * Renders a single reflection chapter to HTML string and returns it.
 * Used by the builder preview for surgical DOM updates.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Render_Chapter')) {
    class RECI_Reflection_Render_Chapter
    {
        public static function register(): void
        {
            add_action('rest_api_init', [self::class, 'register_route']);
        }

        public static function register_route(): void
        {
            register_rest_route('reci/v1', '/render-chapter', [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [self::class, 'handle'],
                'permission_callback' => static fn() => current_user_can('edit_posts'),
                'args'                => [
                    'chapter' => [
                        'required' => true,
                        'type'     => 'object',
                    ],
                    'post_id' => [
                        'required'          => true,
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                    ],
                ],
            ]);
        }

        public static function handle(WP_REST_Request $request): WP_REST_Response|WP_Error
        {
            $chapter = $request->get_param('chapter');
            $post_id = (int) $request->get_param('post_id');

            if (! is_array($chapter)) {
                return new WP_Error('invalid_chapter', __('Chapter must be an object.', 'reci-media-hub'), ['status' => 400]);
            }

            if (! current_user_can('edit_post', $post_id)) {
                return new WP_Error('forbidden', __('Cannot edit this post.', 'reci-media-hub'), ['status' => 403]);
            }

            $GLOBALS['reci_reflection_preview_post_id'] = $post_id;
            $GLOBALS['reci_reflection_builder_preview'] = true;

            ob_start();
            RECI_Reflection_System_Render_Service::render_component($chapter);
            $html = ob_get_clean();

            return new WP_REST_Response(['html' => $html], 200);
        }
    }
}

RECI_Reflection_Render_Chapter::register();
