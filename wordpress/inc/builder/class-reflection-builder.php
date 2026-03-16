<?php
/**
 * Reflection Builder — metabox launcher + full-page builder admin page.
 *
 * The standard post-edit screen shows a single "Edit with Reflection Builder"
 * button. Clicking it opens admin.php?page=reci-reflection-builder&post_id=X,
 * a full-page shell that mounts the React builder with no WP chrome.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Builder')) {
    class RECI_Reflection_Builder
    {
        public static function register(): void
        {
            add_action('add_meta_boxes',        [self::class, 'add_metabox']);
            add_action('admin_menu',             [self::class, 'register_page']);
            add_action('admin_enqueue_scripts',  [self::class, 'enqueue_assets']);
            add_action('save_post_reci_reflection', [self::class, 'save_blueprint'], 10, 1);
        }

        // ── Metabox: shows the launch button on the standard edit screen ──────

        public static function add_metabox(): void
        {
            add_meta_box(
                'reci-reflection-builder',
                __('Reflection Builder', 'reci-media-hub'),
                [self::class, 'render_metabox'],
                'reci_reflection',
                'normal',
                'high'
            );
        }

        public static function render_metabox(WP_Post $post): void
        {
            $url = add_query_arg([
                'page'    => 'reci-reflection-builder',
                'post_id' => $post->ID,
            ], admin_url('admin.php'));
            ?>
            <div style="padding:12px 0;">
                <a
                    href="<?php echo esc_url($url); ?>"
                    class="button button-primary button-large"
                    style="display:inline-flex;align-items:center;gap:8px;"
                >
                    <span dashicons="dashicons-edit"></span>
                    <?php esc_html_e('Edit with Reflection Builder', 'reci-media-hub'); ?>
                </a>
                <p class="description" style="margin-top:8px;">
                    <?php esc_html_e('Opens the full-page visual builder.', 'reci-media-hub'); ?>
                </p>
            </div>
            <?php
        }

        // ── Full-page builder admin page ──────────────────────────────────────

        public static function register_page(): void
        {
            add_menu_page(
                __('Reflection Builder', 'reci-media-hub'),
                'Reflection Builder',
                'edit_posts',
                'reci-reflection-builder',
                [self::class, 'render_page'],
                '',
                null
            );

            // Remove from admin sidebar so it's only reachable via the button.
            remove_menu_page('reci-reflection-builder');
        }

        public static function render_page(): void
        {
            $post_id = absint($_GET['post_id'] ?? 0);
            $post    = $post_id ? get_post($post_id) : null;

            if (! $post || $post->post_type !== 'reci_reflection' || ! current_user_can('edit_post', $post_id)) {
                wp_die(esc_html__('Invalid post or insufficient permissions.', 'reci-media-hub'));
            }

            $raw           = (string) get_post_meta($post_id, '_reci_reflection_blueprint', true);
            $decoded       = $raw !== '' ? json_decode($raw, true) : [];
            $normalized    = is_array($decoded) && function_exists('reci_reflection_system_normalize_blueprint')
                ? reci_reflection_system_normalize_blueprint($decoded)
                : reci_reflection_system_default_blueprint();
            $blueprint     = wp_json_encode($normalized);
            $preview_nonce = wp_create_nonce('wp_rest');
            $save_nonce    = wp_create_nonce('reci_builder_save');
            $back_url      = get_edit_post_link($post_id, 'url');
            $post_title    = get_the_title($post_id);
            $builder_config = [
                'registry' => reci_reflection_system_registry(),
                'defaultBlueprint' => reci_reflection_system_default_blueprint(),
                'currentBlueprint' => $normalized,
                'postId' => $post_id,
                'previewNonce' => $preview_nonce,
                'previewEndpoint' => rest_url('reci/v1/reflection-preview'),
            ];

            // Enqueue assets for this page
            self::enqueue_builder_assets($post_id);

            // Output the full-page shell (replaces normal WP admin chrome)
            ?>
            <!doctype html>
            <html <?php language_attributes(); ?>>
            <head>
                <meta charset="<?php bloginfo('charset'); ?>" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <title><?php echo esc_html($post_title); ?> — <?php esc_html_e('Reflection Builder', 'reci-media-hub'); ?></title>
                <?php wp_head(); ?>
                <style>
                    html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; background: #f0f0f1; }
                    #reci-builder-shell { display: flex; flex-direction: column; height: 100vh; }
                    #reci-builder-topbar {
                        display: flex; align-items: center; gap: 12px;
                        padding: 0 16px; height: 48px; background: #1e1e1e; color: #fff;
                        flex-shrink: 0; z-index: 100;
                    }
                    #reci-builder-topbar a { color: #ccc; text-decoration: none; font-size: 13px; }
                    #reci-builder-topbar a:hover { color: #fff; }
                    #reci-builder-topbar .sep { color: #555; }
                    #reci-builder-topbar .post-title { font-size: 13px; font-weight: 600; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
                    #reci-builder-topbar .save-btn {
                        background: #2271b1; color: #fff; border: none; border-radius: 3px;
                        padding: 6px 14px; font-size: 13px; cursor: pointer;
                    }
                    #reci-builder-topbar .save-btn:hover { background: #135e96; }
                    #reci-builder-root { flex: 1; overflow: hidden; }
                </style>
            </head>
            <body>
            <div id="reci-builder-shell">
                <div id="reci-builder-topbar">
                    <a href="<?php echo esc_url($back_url); ?>">&#8592; <?php esc_html_e('Exit Builder', 'reci-media-hub'); ?></a>
                    <span class="sep">|</span>
                    <span class="post-title"><?php echo esc_html($post_title); ?></span>
                    <button type="button" class="save-btn" id="reci-builder-save-btn">
                        <?php esc_html_e('Save', 'reci-media-hub'); ?>
                    </button>
                </div>
                <div
                    id="reci-builder-root"
                    data-post-id="<?php echo esc_attr((string) $post_id); ?>"
                    data-preview-nonce="<?php echo esc_attr($preview_nonce); ?>"
                ></div>
            </div>

            <input type="hidden" id="reci-builder-blueprint" value="<?php echo esc_attr($blueprint); ?>" />
            <input type="hidden" id="reci-builder-nonce"     value="<?php echo esc_attr($save_nonce); ?>" />
            <input type="hidden" id="reci-builder-post-id"   value="<?php echo esc_attr((string) $post_id); ?>" />

            <script>window.RECIReflectionBuilderConfig = <?php echo wp_json_encode($builder_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;</script>

            <script>
            document.getElementById('reci-builder-save-btn').addEventListener('click', async function() {
                const btn       = this;
                const blueprint = document.getElementById('reci-builder-blueprint').value;
                const nonce     = document.getElementById('reci-builder-nonce').value;
                const postId    = document.getElementById('reci-builder-post-id').value;

                btn.disabled    = true;
                btn.textContent = '<?php echo esc_js(__('Saving…', 'reci-media-hub')); ?>';

                const res = await fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action:                       'reci_save_blueprint',
                        post_id:                      postId,
                        _reci_reflection_blueprint:   blueprint,
                        reci_builder_nonce:           nonce,
                    }),
                });

                const data = await res.json();
                btn.disabled    = false;
                btn.textContent = data.success
                    ? '<?php echo esc_js(__('Saved ✓', 'reci-media-hub')); ?>'
                    : '<?php echo esc_js(__('Error — retry', 'reci-media-hub')); ?>';

                setTimeout(() => { btn.textContent = '<?php echo esc_js(__('Save', 'reci-media-hub')); ?>'; }, 2000);
            });
            </script>

            <?php wp_footer(); ?>
            </body>
            </html>
            <?php
            exit; // Prevent WP from appending its own admin page chrome.
        }

        // ── Asset helpers ─────────────────────────────────────────────────────

        public static function enqueue_assets(string $hook): void
        {
            // Standard edit screen: no assets needed (metabox is just a button).
            // Full-page builder page: handled inside render_page() via enqueue_builder_assets().
            if ($hook !== 'toplevel_page_reci-reflection-builder') {
                return;
            }
        }

        private static function enqueue_builder_assets(int $post_id): void
        {
            $css_path = get_template_directory() . '/assets/css/tailwind.css';
            $js_path  = get_template_directory() . '/assets/js/builder.js';
            $version  = file_exists($js_path) ? (string) filemtime($js_path) : wp_get_theme()->get('Version');

            wp_enqueue_media();

            wp_enqueue_style(
                'reci-tailwind',
                get_template_directory_uri() . '/assets/css/tailwind.css',
                [],
                file_exists($css_path) ? (string) filemtime($css_path) : wp_get_theme()->get('Version')
            );

            wp_enqueue_script(
                'reci-reflection-builder',
                get_template_directory_uri() . '/assets/js/builder.js',
                [],
                $version,
                true
            );

            add_filter('script_loader_tag', static function (string $tag, string $handle): string {
                if ($handle === 'reci-reflection-builder') {
                    return str_replace(' src=', ' type="module" src=', $tag);
                }
                return $tag;
            }, 10, 2);
        }

        // ── AJAX save (used by the full-page builder's Save button) ───────────

        public static function register_ajax(): void
        {
            add_action('wp_ajax_reci_save_blueprint', [self::class, 'ajax_save_blueprint']);
        }

        public static function ajax_save_blueprint(): void
        {
            $post_id   = absint($_POST['post_id'] ?? 0);
            $nonce_raw = sanitize_text_field(wp_unslash($_POST['reci_builder_nonce'] ?? ''));

            if (! wp_verify_nonce($nonce_raw, 'reci_builder_save') || ! current_user_can('edit_post', $post_id)) {
                wp_send_json_error(['message' => 'Unauthorized.']);
            }

            $raw = $_POST['_reci_reflection_blueprint'] ?? '';
            if (! is_string($raw) || $raw === '') {
                wp_send_json_error(['message' => 'Empty blueprint.']);
            }

            $decoded = json_decode(wp_unslash($raw), true);
            if (! is_array($decoded)) {
                wp_send_json_error(['message' => 'Invalid JSON.']);
            }

            if (! function_exists('reci_reflection_system_normalize_blueprint')) {
                wp_send_json_error(['message' => 'Reflection system unavailable.']);
            }

            $normalized = reci_reflection_system_normalize_blueprint($decoded);
            update_post_meta($post_id, '_reci_reflection_blueprint', wp_json_encode($normalized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            wp_send_json_success(['message' => 'Saved.']);
        }

        // ── Save via standard post save (fallback) ────────────────────────────

        public static function save_blueprint(int $post_id): void
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            $nonce_raw = $_POST['reci_builder_nonce'] ?? '';
            $nonce     = is_string($nonce_raw) ? sanitize_text_field(wp_unslash($nonce_raw)) : '';
            if (! wp_verify_nonce($nonce, 'reci_builder_save')) {
                return;
            }

            if (! current_user_can('edit_post', $post_id)) {
                return;
            }

            $raw = $_POST['_reci_reflection_blueprint'] ?? '';
            if (! is_string($raw) || $raw === '') {
                return;
            }

            $decoded = json_decode(wp_unslash($raw), true);
            if (! is_array($decoded)) {
                return;
            }

            if (! function_exists('reci_reflection_system_normalize_blueprint')) {
                return;
            }

            $normalized = reci_reflection_system_normalize_blueprint($decoded);
            update_post_meta($post_id, '_reci_reflection_blueprint', wp_json_encode($normalized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }
}

RECI_Reflection_Builder::register();
RECI_Reflection_Builder::register_ajax();
