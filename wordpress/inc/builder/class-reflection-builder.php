<?php
/**
 * Reflection Builder — WP admin metabox + blueprint save hook.
 *
 * Registers a "Reflection Builder" metabox on reci_reflection posts.
 * Enqueues assets/js/builder.js as an ES module on the edit screen.
 * Saves _reci_reflection_blueprint from POST on save_post.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('RECI_Reflection_Builder')) {
    class RECI_Reflection_Builder
    {
        public static function register(): void
        {
            add_action('add_meta_boxes', [self::class, 'add_metabox']);
            add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
            add_action('save_post_reci_reflection', [self::class, 'save_blueprint'], 10, 1);
        }

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
            wp_nonce_field('reci_builder_save', 'reci_builder_nonce');

            $raw       = (string) get_post_meta($post->ID, '_reci_reflection_blueprint', true);
            $blueprint = ($raw !== '') ? $raw : '{"mode":"standard","appearance":{},"scenes":[],"chapters":[]}';
            $preview_nonce = wp_create_nonce('wp_rest');
            ?>
            <div
                id="reci-builder-root"
                data-post-id="<?php echo esc_attr((string) $post->ID); ?>"
                data-preview-nonce="<?php echo esc_attr($preview_nonce); ?>"
            ></div>
            <input
                type="hidden"
                id="reci-builder-blueprint"
                name="_reci_reflection_blueprint"
                value="<?php echo esc_attr($blueprint); ?>"
            />
            <?php
        }

        public static function enqueue_assets(string $hook): void
        {
            if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
                return;
            }

            $screen = get_current_screen();
            if (! $screen || $screen->post_type !== 'reci_reflection') {
                return;
            }

            $path    = get_template_directory() . '/assets/js/builder.js';
            $version = file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version');

            wp_enqueue_media();
            wp_enqueue_script(
                'reci-reflection-builder',
                get_template_directory_uri() . '/assets/js/builder.js',
                [],
                $version,
                true
            );
            wp_script_add_data('reci-reflection-builder', 'type', 'module');
        }

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

            // Validate it is JSON before saving
            $decoded = json_decode(wp_unslash($raw), true);
            if (! is_array($decoded)) {
                return;
            }

            update_post_meta($post_id, '_reci_reflection_blueprint', wp_unslash($raw));
        }
    }
}

RECI_Reflection_Builder::register();
