<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$items = is_array($scene['items'] ?? null) ? $scene['items'] : [];
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$heading_class   = reci_reflection_heading_class($scene);
$surface_class   = reci_reflection_surface_class($scene);
$prompt_columns = reci_reflection_columns_class(reci_reflection_scene_option($scene, 'columns', 'auto'), 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3');
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-prompt-list')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?><h2 class="mb-8 text-2xl sm:text-3xl font-bold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2><?php endif; ?>
        <div class="grid gap-4 <?php echo esc_attr($prompt_columns); ?>">
            <?php foreach ($items as $item) : ?>
                <div class="rounded-lg border p-6 <?php echo esc_attr($surface_class); ?>">
                    <p class="text-lg leading-8"><?php echo esc_html(is_array($item) ? (string) ($item['text'] ?? '') : (string) $item); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
