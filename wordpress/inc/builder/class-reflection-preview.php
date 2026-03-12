<?php
/**
 * Reflection Preview REST endpoint.
 *
 * POST /wp-json/reci/v1/reflection-preview
 * Body: { post_id: int, blueprint: object }
 *
 * Temporarily stores the blueprint in a transient and returns a
 * preview URL for the post with ?reci_preview=<key>.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Preview')) {
    class RECI_Reflection_Preview
    {
        public static function register(): void
        {
            add_action('rest_api_init', [self::class, 'register_route']);
        }

        public static function register_route(): void
        {
            register_rest_route('reci/v1', '/reflection-preview', [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [self::class, 'handle'],
                'permission_callback' => static fn() => current_user_can('edit_posts'),
                'args'                => [
                    'post_id'   => [
                        'required'          => true,
                        'type'              => 'integer',
                        'sanitize_callback' => 'absint',
                    ],
                    'blueprint' => [
                        'required' => true,
                        'type'     => 'object',
                    ],
                ],
            ]);
        }

        public static function handle(WP_REST_Request $request): WP_REST_Response|WP_Error
        {
            $post_id   = (int) $request->get_param('post_id');
            $blueprint = $request->get_param('blueprint');

            if (! get_post($post_id)) {
                return new WP_Error('not_found', __('Post not found.', 'reci-media-hub'), ['status' => 404]);
            }

            if (! current_user_can('edit_post', $post_id)) {
                return new WP_Error('forbidden', __('Cannot edit this post.', 'reci-media-hub'), ['status' => 403]);
            }

            $key = 'reci_preview_' . wp_generate_password(16, false);
            set_transient($key, wp_json_encode($blueprint), 15 * MINUTE_IN_SECONDS);

            $preview_url = add_query_arg('reci_preview', $key, get_permalink($post_id));

            return new WP_REST_Response(['preview_url' => $preview_url], 200);
        }
    }
}

RECI_Reflection_Preview::register();

/**
 * If ?reci_preview=<key> is present, override the stored blueprint
 * with the transient saved by the preview endpoint.
 */
add_action('template_redirect', static function (): void {
    $key = sanitize_text_field(wp_unslash($_GET['reci_preview'] ?? ''));
    if ($key === '' || ! is_singular('reci_reflection')) {
        return;
    }

    $json = get_transient($key);
    if (! is_string($json) || $json === '') {
        return;
    }

    $post_id = get_the_ID();

    add_filter(
        'get_post_metadata',
        static function ($value, $object_id, $meta_key) use ($post_id, $json) {
            if ($meta_key === '_reci_reflection_blueprint' && (int) $object_id === $post_id) {
                return [$json];
            }
            return $value;
        },
        10,
        3
    );
});
