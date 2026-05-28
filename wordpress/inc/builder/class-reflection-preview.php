<?php
/**
 * Reflection Preview REST endpoint.
 *
 * POST /wp-json/reci/v1/reflection-preview
 * Body: { post_id: int, blueprint: object, selected_chapter_id?: string|null }
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
                    'selected_chapter_id' => [
                        'required' => false,
                        'type'     => 'string',
                    ],
                ],
            ]);
        }

        public static function handle(WP_REST_Request $request): WP_REST_Response|WP_Error
        {
            $post_id   = (int) $request->get_param('post_id');
            $blueprint = $request->get_param('blueprint');
            $selected  = $request->get_param('selected_chapter_id');

            if (! get_post($post_id)) {
                return new WP_Error('not_found', __('Post not found.', 'reci-media-hub'), ['status' => 404]);
            }

            if (! current_user_can('edit_post', $post_id)) {
                return new WP_Error('forbidden', __('Cannot edit this post.', 'reci-media-hub'), ['status' => 403]);
            }

            $key = 'reci_preview_' . wp_generate_password(16, false);
            set_transient($key, wp_json_encode($blueprint), 15 * MINUTE_IN_SECONDS);

            $preview_url = add_query_arg('reci_preview', $key, get_permalink($post_id));

            // Resolve the selected chapter's html id from the blueprint chapters.
            if (is_string($selected) && $selected !== '') {
                $chapters = is_array($blueprint['chapters'] ?? null) ? $blueprint['chapters'] : [];
                foreach ($chapters as $chapter) {
                    if (is_array($chapter) && ($chapter['id'] ?? '') === $selected) {
                        $chapter_id = (string) ($chapter['props']['id'] ?? '');
                        if ($chapter_id !== '') {
                            $preview_url .= '#' . sanitize_title($chapter_id);
                        }
                        break;
                    }
                }
            }

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

    $post_id = get_queried_object_id();
    if (! $post_id) {
        return;
    }

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

/**
 * Inject postMessage listener for live preview updates.
 * Allows the builder to surgically update individual chapter DOM nodes
 * without reloading the entire iframe.
 */
add_action('wp_footer', static function (): void {
    if (! is_singular('reci_reflection') || empty($_GET['reci_preview'])) {
        return;
    }
    ?>
    <script>
    (function() {
        'use strict';
        function sendToParent(msg) {
            if (window.parent !== window) {
                window.parent.postMessage(msg, '*');
            }
        }
        function handleMessage(event) {
            var data = event.data;
            if (!data || typeof data !== 'object') return;
            console.log('[preview] received:', data.type, data.chapterId);
            switch (data.type) {
                case 'scroll-to-chapter': {
                    var el = document.getElementById(data.chapterId) || document.querySelector('[data-chapter-id="' + data.chapterId + '"]');
                    if (el) {
                        console.log('[preview] scrolling to:', data.chapterId);
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        console.warn('[preview] scroll target not found:', data.chapterId);
                    }
                    break;
                }
                case 'update-chapter': {
                    var target = document.getElementById(data.chapterId) || document.querySelector('[data-chapter-id="' + data.chapterId + '"]');
                    if (target && data.html) {
                        console.log('[preview] replacing chapter:', data.chapterId);
                        var temp = document.createElement('div');
                        temp.innerHTML = data.html;
                        var newEl = temp.firstElementChild;
                        if (newEl) {
                            target.replaceWith(newEl);
                            console.log('[preview] replaced successfully');
                        } else {
                            console.warn('[preview] no firstElementChild in rendered HTML');
                        }
                    } else {
                        console.warn('[preview] update target not found:', data.chapterId);
                    }
                    break;
                }
            }
        }
        window.addEventListener('message', handleMessage);
        if (document.readyState === 'complete') {
            sendToParent({ type: 'preview-ready' });
            console.log('[preview] ready signal sent');
        } else {
            window.addEventListener('load', function() {
                sendToParent({ type: 'preview-ready' });
                console.log('[preview] ready signal sent (on load)');
            });
        }
    })();
    </script>
    <?php
}, 100);
