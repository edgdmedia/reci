<?php
/**
 * Single template for the reci_reflection post type.
 */

if (! defined('ABSPATH')) {
    exit;
}

the_post();

$post_id     = get_the_ID();
$payload     = RECI_Reflection_Content_Service::get_payload($post_id);
$blueprint   = is_array($payload['blueprint'] ?? null) ? $payload['blueprint'] : [];
$uses_new_system = function_exists('reci_reflection_blueprint_uses_new_system') && reci_reflection_blueprint_uses_new_system($post_id);

if ($uses_new_system) {
    $normalized = reci_reflection_system_normalize_blueprint($blueprint);
    $response_rest_url = rest_url('reci/v1/journals');
    $reflection_config = [
        'isLoggedIn' => is_user_logged_in(),
        'restUrl' => $response_rest_url,
        'nonce' => wp_create_nonce('wp_rest'),
        'reflectionId' => $post_id,
        'currentUserId' => get_current_user_id(),
    ];
    ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class('single-reci-reflection immersive-reflection-page reci-reflection-system-page'); ?>>
    <?php wp_body_open(); ?>
    <script>window.RECIReflectionConfig = <?php echo wp_json_encode($reflection_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;</script>
    <?php
        $settings = $normalized['settings'] ?? [];
        $style = '';
        if (!empty($settings['color_bg'])) $style .= '--reflection-bg: ' . esc_attr($settings['color_bg']) . ';';
        if (!empty($settings['color_heading'])) $style .= '--reflection-heading: ' . esc_attr($settings['color_heading']) . ';';
        if (!empty($settings['color_body'])) $style .= '--reflection-body: ' . esc_attr($settings['color_body']) . ';';
        if (!empty($settings['color_primary'])) $style .= '--reflection-primary: ' . esc_attr($settings['color_primary']) . ';';
        if (!empty($settings['color_accent'])) $style .= '--reflection-accent: ' . esc_attr($settings['color_accent']) . ';';
    ?>
    <div class="reci-reflection-page" <?php if ($style) echo 'style="' . $style . '"'; ?>>
        <button id="reciSystemBack" type="button" class="fixed bottom-5 left-5 z-[60] hidden rounded-full border border-[color:var(--reflection-border-soft)] bg-black/45 px-4 py-3 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-white">← Back</button>
        <?php if (($settings['global_style'] ?? 'immersive-dark') === 'breaking-chains') : ?>
            <div id="bcProgressLine" class="fixed left-1/2 top-0 z-40 h-0 w-[2px] -translate-x-1/2 bg-[var(--reflection-accent)] shadow-[0_0_10px_var(--reflection-accent)]"></div>
            <div id="bcFreedomBg" class="pointer-events-none fixed inset-0 z-[-1] bg-[linear-gradient(to_top,#1a0b00,#000)] opacity-0 transition-opacity duration-[2000ms]"></div>
        <?php endif; ?>

        <main class="reci-reflection-shell">
            <?php RECI_Reflection_System_Render_Service::render_blueprint($normalized); ?>
        </main>
    </div>
    <?php wp_footer(); ?>
    </body>
    </html>
    <?php
    return;
}

$experience  = RECI_Reflection_Experience_Service::get_payload($payload);
$mode        = (string) ($experience['mode'] ?? 'standard');
$related     = RECI_Related_Posts_Service::get_related(
    $post_id,
    [
        'post_type'   => 'reci_reflection',
        'limit'       => 3,
        'taxonomy'    => taxonomy_exists('reci_topic') ? 'reci_topic' : 'post_tag',
        'format_args' => [
            'image_size' => 'medium',
            'tag_limit'  => 1,
        ],
    ]
);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class('single-reci-reflection immersive-reflection-page'); ?>>
<?php wp_body_open(); ?>
<script id="reci-reflection-experience-data" type="application/json"><?php echo wp_json_encode($experience, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<div class="reci-reflection-page">
    <main class="reci-reflection-shell">
        <div id="reci-reflection-root"></div>
    </main>

    <?php if ($mode !== 'immersive' && ! empty($related)) : ?>
        <section class="reci-reflection-related">
            <div class="mx-auto  px-4 py-16 sm:px-6 lg:px-12 xl:px-20">
                <div class="mb-10 flex items-center gap-3">
                    <div class="h-2 w-2 rounded-sm bg-amber-400"></div>
                    <h2 class="text-2xl font-bold font-serif text-neutral-800 sm:text-3xl"><?php esc_html_e('Related Reflections', 'reci-media-hub'); ?></h2>
                </div>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($related as $item) : ?>
                        <article class="overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <a href="<?php echo esc_url((string) ($item['link_url'] ?? '#')); ?>" class="block">
                                <img src="<?php echo esc_url((string) ($item['image_url'] ?? 'https://placehold.co/387x300')); ?>" alt="<?php echo esc_attr((string) ($item['image_alt'] ?? '')); ?>" class="h-56 w-full object-cover" />
                            </a>
                            <div class="flex flex-col gap-4 p-6">
                                <div class="flex items-center gap-3">
                                    <a href="<?php echo esc_url(get_post_type_archive_link('reci_reflection') ?: '#'); ?>" class="inline-flex rounded bg-neutral-800 px-2 py-1 text-sm text-white"><?php echo esc_html((string) ($item['type_label'] ?? 'Reflection')); ?></a>
                                    <?php foreach ((array) ($item['tags'] ?? []) as $tag_name) : $tag_obj = get_term_by('name', $tag_name, 'post_tag'); ?>
                                        <?php if ($tag_obj && ! is_wp_error($tag_obj)) : ?>
                                            <a href="<?php echo esc_url(get_term_link($tag_obj)); ?>" class="text-sm text-neutral-500 hover:text-neutral-800"><?php echo esc_html((string) $tag_name); ?></a>
                                            <?php break; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <h3 class="text-xl font-semibold font-serif text-neutral-800"><a href="<?php echo esc_url((string) ($item['link_url'] ?? '#')); ?>" class="hover:underline"><?php echo esc_html((string) ($item['title'] ?? '')); ?></a></h3>
                                <?php if (! empty($item['excerpt'])) : ?><p class="text-neutral-600 leading-7"><?php echo esc_html((string) $item['excerpt']); ?></p><?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
