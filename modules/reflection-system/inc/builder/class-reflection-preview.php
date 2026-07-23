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
            $payload = [
                'post_id' => $post_id,
                'blueprint' => $blueprint,
            ];
            set_transient($key, wp_json_encode($payload), 15 * MINUTE_IN_SECONDS);

            $preview_url = add_query_arg('reci_preview', $key, home_url('/'));

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

add_action('template_redirect', static function (): void {
    $key = sanitize_text_field(wp_unslash($_GET['reci_preview'] ?? ''));
    if ($key === '') {
        return;
    }

    $json = get_transient($key);
    if (! is_string($json) || $json === '') {
        status_header(404);
        exit('Preview expired. Refresh builder preview.');
    }

    $decoded = json_decode($json, true);
    if (! is_array($decoded)) {
        status_header(400);
        exit('Invalid preview payload.');
    }

    $post_id = (int) ($decoded['post_id'] ?? 0);
    $blueprint = $decoded['blueprint'] ?? $decoded;
    if (! is_array($blueprint)) {
        status_header(400);
        exit('Invalid preview blueprint.');
    }

    if ($post_id > 0 && ! current_user_can('edit_post', $post_id)) {
        status_header(403);
        exit('Unauthorized preview access.');
    }

    if (function_exists('reci_reflection_system_normalize_blueprint')) {
        $blueprint = reci_reflection_system_normalize_blueprint($blueprint);
    }

    $title = $post_id > 0 ? get_the_title($post_id) : 'Reflection Preview';
    $title = $title !== '' ? $title : 'Reflection Preview';

    nocache_headers();
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html($title); ?> - Preview</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Roboto:wght@400;700&family=Instrument+Serif&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&family=Oswald:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&family=Roboto+Mono:wght@300;400;500&family=Space+Grotesk:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo esc_url(get_stylesheet_uri()); ?>?ver=<?php echo esc_attr((string) filemtime(get_stylesheet_directory() . '/style.css')); ?>">
        <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/tailwind.css'); ?>?ver=<?php echo esc_attr((string) filemtime(get_template_directory() . '/assets/css/tailwind.css')); ?>">
        <?php 
        $styles = [
            'reflection-system-runtime',
            'reflection-immersive',
            'reflection-immersive-dark',
            'reflection-breaking-chains',
        ];
        foreach ($styles as $filename) {
            $css_path = get_template_directory() . '/modules/reflection-system/assets/css/' . $filename . '.css';
            if (file_exists($css_path)) {
                ?>
                <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/modules/reflection-system/assets/css/' . $filename . '.css'); ?>?ver=<?php echo esc_attr((string) filemtime($css_path)); ?>">
                <?php
            }
        }
        ?>
        <?php 
            $global_style = sanitize_key((string) ($blueprint['settings']['global_style'] ?? 'immersive-dark'));
            if (file_exists(get_template_directory() . '/modules/reflection-system/assets/css/reflection-' . $global_style . '.css')): 
        ?>
        <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/modules/reflection-system/assets/css/reflection-' . $global_style . '.css'); ?>?ver=<?php echo esc_attr((string) filemtime(get_template_directory() . '/modules/reflection-system/assets/css/reflection-' . $global_style . '.css')); ?>">
        <?php endif; ?>
        
        <?php 
            $settings = is_array($blueprint['settings'] ?? null) ? $blueprint['settings'] : [];
            $c_bg = esc_attr((string) ($settings['color_bg'] ?? ''));
            $c_heading = esc_attr((string) ($settings['color_heading'] ?? ''));
            $c_body = esc_attr((string) ($settings['color_body'] ?? ''));
            $c_primary = esc_attr((string) ($settings['color_primary'] ?? ''));
            $c_accent = esc_attr((string) ($settings['color_accent'] ?? ''));
            
            if ($c_bg || $c_heading || $c_body || $c_primary || $c_accent) {
                echo '<style>:root {';
                if ($c_bg) echo '--reflection-bg: ' . $c_bg . '; ';
                if ($c_heading) echo '--reflection-heading: ' . $c_heading . '; ';
                if ($c_body) echo '--reflection-body: ' . $c_body . '; ';
                if ($c_primary) echo '--reflection-primary: ' . $c_primary . '; ';
                if ($c_accent) echo '--reflection-accent: ' . $c_accent . '; ';
                echo '}</style>';
            }
        ?>
        <style>html,body{margin:0;padding:0;background:#fff;}
        .reci-stage{position:fixed;inset:0;display:none;opacity:0;transition:opacity 0.7s ease;background:var(--reflection-bg);overflow-y:auto;overflow-x:hidden;}
        .reci-stage-shell{width:100%;min-height:100vh;display:flex;align-items:stretch;}
        .reci-stage-body{width:100%;max-width:1440px;margin:0 auto;padding:6rem 1.25rem 4.75rem;display:flex;flex-direction:column;justify-content:center;}
        .reci-continue{display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.95rem 1.35rem;border-radius:999px;border:1px solid var(--reflection-border);background:var(--reflection-card);color:var(--reflection-text);font-family:'Oswald', sans-serif;text-transform:uppercase;letter-spacing:0.1em;cursor:pointer;}
        .reci-stage-grid{display:grid;gap:1.5rem;}
        .reci-scroll-panel{max-height:min(72vh, 820px);overflow-y:auto;padding-right:0.5rem;}
        .reci-scroll-panel::-webkit-scrollbar{width:8px;}
        .reci-scroll-panel::-webkit-scrollbar-thumb{background:var(--reflection-border);border-radius:999px;}
        .reci-timeline-world{height:100vh;width:300vw;display:flex;transform:translateX(0);transition:transform 0.85s cubic-bezier(0.23, 1, 0.32, 1);will-change:transform;}
        .reci-timeline-panel{width:100vw;height:100vh;flex-shrink:0;display:flex;align-items:center;justify-content:center;padding:6rem 1.25rem 4.75rem;position:relative;border-right:1px solid var(--reflection-border-soft);}
        .reci-timeline-card{width:min(1120px, 100%);background:linear-gradient(180deg, var(--reflection-card-strong), var(--reflection-card));border:1px solid var(--reflection-border-soft);border-radius:2rem;padding:2rem;box-shadow:0 24px 60px rgba(0,0,0,0.18);}
        .reci-timeline-controls{position:absolute;left:50%;bottom:3.25rem;transform:translateX(-50%);display:flex;gap:0.85rem;z-index:10;}
        .reci-icon-btn{width:3.1rem;height:3.1rem;border-radius:999px;border:1px solid var(--reflection-border);background:var(--reflection-card);color:var(--reflection-text);cursor:pointer;}
        .reci-stage-panels{display:grid;grid-template-columns:minmax(280px, 420px) minmax(0, 1fr);gap:1.5rem;align-items:stretch;}
        .reci-panel-scroll{max-height:min(72vh, 820px);overflow-y:auto;padding-right:0.5rem;}
        .reci-panel-scroll::-webkit-scrollbar{width:8px;}
        .reci-panel-scroll::-webkit-scrollbar-thumb{background:var(--reflection-border);border-radius:999px;}
        .reci-stage-menu{display:grid;gap:1rem;}
        .reci-stage-menu button.active{background:var(--reflection-accent);color:var(--reflection-accent-contrast);border-color:transparent;}
        .panel-hotspot{position:absolute;width:22px;height:22px;border-radius:999px;border:2px solid rgba(255,255,255,0.9);background:rgba(167, 199, 150, 0.95);color:#1a1713;font-size:0.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;transform:translate(-50%, -50%);cursor:pointer;pointer-events:auto;box-shadow:0 0 0 8px var(--reflection-hotspot-ring);}
        .panel-hotspot.active{background:white;box-shadow:0 0 0 10px rgba(255,255,255,0.08);}
        .annotation-chip.active{border-color:var(--reflection-accent) !important;background:rgba(167, 199, 150, 0.18) !important;}
        @media (max-width: 1024px){.reci-stage-body,.reci-timeline-panel{padding-top:5.5rem;}.reci-stage-panels,.reci-stage-grid{grid-template-columns:1fr;}.reci-timeline-card{padding:1.5rem;}}
        </style>
    </head>
    <body>
        <main id="reci-builder-preview-root">
            <?php RECI_Reflection_System_Render_Service::render_blueprint($blueprint); ?>
        </main>
        <script>window.RECIReflectionConfig = { reflectionId: <?php echo (int) $post_id; ?> };</script>
        <script src="<?php echo esc_url(get_template_directory_uri() . '/modules/reflection-system/assets/js/reflection-stage-controller.js'); ?>?ver=<?php echo esc_attr((string) filemtime(get_template_directory() . '/modules/reflection-system/assets/js/reflection-stage-controller.js')); ?>"></script>
        <script src="<?php echo esc_url(get_template_directory_uri() . '/modules/reflection-system/assets/js/reflection-system-runtime.js'); ?>?ver=<?php echo esc_attr((string) filemtime(get_template_directory() . '/modules/reflection-system/assets/js/reflection-system-runtime.js')); ?>"></script>
        <?php 
        $interactions = [
            'reflection-immersive-dark',
            'reflection-breaking-chains',
            'reflection-march-toward-justice',
            'reflection-racial-disparities',
        ];
        foreach ($interactions as $filename) {
            $js_path = get_template_directory() . '/modules/reflection-system/assets/js/' . $filename . '.js';
            if (file_exists($js_path)) {
                ?>
                <script src="<?php echo esc_url(get_template_directory_uri() . '/modules/reflection-system/assets/js/' . $filename . '.js'); ?>?ver=<?php echo esc_attr((string) filemtime($js_path)); ?>"></script>
                <?php
            }
        }
        ?>
        <script>
        (function() {
            'use strict';
            function sendToParent(msg) {
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage(msg, '*');
                }
            }
            function handleMessage(event) {
                var data = event.data;
                if (!data || !data.type) return;
                console.log('[preview] received:', data.type, data.chapterId);
                switch (data.type) {
                    case 'scroll-to-chapter': {
                        var el = document.querySelector('[data-chapter-id="' + data.chapterId + '"]');
                        if (window.RECIReflectionController && typeof window.RECIReflectionController.goTo === 'function') {
                            var targetId = el ? el.id : data.chapterId;
                            console.log('[preview] controller.goTo:', targetId);
                            window.RECIReflectionController.goTo(targetId);
                            // Fallback: If Tailwind classes override fixed positioning, ensure we still scroll down to the stage after transition
                            setTimeout(function() {
                                var scrollEl = document.getElementById(targetId);
                                if (scrollEl) scrollEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }, 500); // 500ms to allow the 450ms hide transition to complete
                        } else {
                            if (!el) el = document.getElementById(data.chapterId);
                            if (el) {
                                console.log('[preview] scrolling to:', data.chapterId);
                                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            } else {
                                console.warn('[preview] scroll target not found:', data.chapterId);
                            }
                        }
                        break;
                    }
                    case 'update-chapter': {
                        var target = document.querySelector('[data-chapter-id="' + data.chapterId + '"]') || document.getElementById(data.chapterId);
                        if (target && data.html) {
                            console.log('[preview] replacing chapter:', data.chapterId);
                            var temp = document.createElement('div');
                            temp.innerHTML = data.html;
                            var newEl = temp.firstElementChild;
                            if (newEl) {
                                var isCurrent = false;
                                if (window.RECIReflectionController && typeof window.RECIReflectionController.current === 'function') {
                                    isCurrent = (window.RECIReflectionController.current() === target.id);
                                }
                                target.replaceWith(newEl);
                                if (isCurrent) {
                                    var isFlex = newEl.classList.contains('flex');
                                    for (var i = 0; i < newEl.classList.length; i++) {
                                        if (newEl.classList[i].indexOf('chapter-') === 0) {
                                            isFlex = true;
                                            break;
                                        }
                                    }
                                    newEl.style.display = isFlex ? 'flex' : 'block';
                                    newEl.style.opacity = '1';
                                    newEl.hidden = false;
                                    newEl.removeAttribute('hidden');
                                    newEl.removeAttribute('aria-hidden');
                                }
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
    </body>
    </html>
    <?php
    exit;
});
