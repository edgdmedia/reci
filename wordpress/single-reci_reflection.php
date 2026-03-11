<?php
/**
 * Single template for the reci_reflection post type.
 *
 * Standalone reflection shell. Standard reflections render scene stacks.
 * Immersive reflections render staged fullscreen chapters.
 */

if (! defined('ABSPATH')) {
    exit;
}

the_post();

$post_id     = get_the_ID();
$payload     = RECI_Reflection_Content_Service::get_payload($post_id);
$experience  = RECI_Reflection_Experience_Service::get_payload($payload);
$mode        = (string) ($experience['mode'] ?? 'standard');
$template    = (string) ($payload['template'] ?? 'narrative');
$appearance  = is_array($payload['appearance'] ?? null) ? $payload['appearance'] : [];
$scenes      = array_values(array_filter(is_array($payload['scenes'] ?? null) ? $payload['scenes'] : [], 'is_array'));
$chapters    = array_values(array_filter(is_array($experience['chapters'] ?? null) ? $experience['chapters'] : [], 'is_array'));
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
<div class="reci-reflection-page" data-reflection-template="<?php echo esc_attr($template); ?>" data-reflection-theme="<?php echo esc_attr((string) ($appearance['theme'] ?? 'immersive-dark')); ?>" data-reflection-accent="<?php echo esc_attr((string) ($appearance['accent'] ?? 'amber')); ?>" data-reflection-mode="<?php echo esc_attr($mode); ?>">
    <main class="reci-reflection-shell" data-reflection-shell>
        <div class="reci-reflection-stage" data-reflection-stage>
            <?php if ($mode === 'immersive') : ?>
                <?php RECI_Reflection_Render_Service::render_chapters($chapters); ?>
            <?php else : ?>
                <?php RECI_Reflection_Render_Service::render_scenes($scenes); ?>
            <?php endif; ?>
        </div>
    </main>

    <?php if ($mode !== 'immersive' && ! empty($related)) : ?>
        <section class="reci-reflection-related">
            <div class="mx-auto max-w-[1440px] px-4 py-16 sm:px-6 lg:px-12 xl:px-20">
                <div class="mb-10 flex items-center gap-3">
                    <div class="h-2 w-2 rounded-sm bg-amber-400"></div>
                    <h2 class="text-2xl font-bold font-['EB_Garamond'] text-neutral-800 sm:text-3xl"><?php esc_html_e('Related Reflections', 'reci-media-hub'); ?></h2>
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
                                <h3 class="text-xl font-semibold font-['EB_Garamond'] text-neutral-800"><a href="<?php echo esc_url((string) ($item['link_url'] ?? '#')); ?>" class="hover:underline"><?php echo esc_html((string) ($item['title'] ?? '')); ?></a></h3>
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
