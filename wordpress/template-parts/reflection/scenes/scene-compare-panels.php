<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$items = array_values(array_filter(is_array($scene['items'] ?? null) ? $scene['items'] : [], 'is_array'));
$first = $items[0] ?? [];
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$heading_class   = reci_reflection_heading_class($scene);
$muted_class     = reci_reflection_muted_class($scene);
$surface_class   = reci_reflection_surface_class($scene);
$accent_class    = reci_reflection_accent_class($scene);
$grid_classes    = reci_reflection_layout_grid_class(reci_reflection_scene_option($scene, 'layout', 'sidebar-right'));
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-compare-panels')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>" data-reflection-compare>
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?><h2 class="mb-8 text-2xl sm:text-3xl font-bold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2><?php endif; ?>
        <div class="<?php echo esc_attr($grid_classes); ?>">
            <div class="overflow-hidden rounded-lg border <?php echo esc_attr($surface_class); ?>">
                <img data-compare-image src="<?php echo esc_url((string) ($first['image_url'] ?? 'https://placehold.co/1200x800')); ?>" alt="<?php echo esc_attr((string) ($first['alt'] ?? '')); ?>" class="h-full w-full object-cover" />
            </div>
            <div class="flex flex-col gap-4">
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($items as $index => $item) : ?>
                        <button type="button" class="rounded border px-4 py-2 text-sm transition hover:border-neutral-700 <?php echo 0 === $index ? esc_attr($accent_class) . ' border-transparent' : esc_attr($muted_class) . ' border-stone-300'; ?>" data-compare-trigger data-panel-index="<?php echo esc_attr((string) $index); ?>" data-panel-image="<?php echo esc_url((string) ($item['image_url'] ?? '')); ?>" data-panel-alt="<?php echo esc_attr((string) ($item['alt'] ?? '')); ?>" data-panel-title="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" data-panel-content="<?php echo esc_attr(wp_strip_all_tags((string) ($item['content'] ?? ''))); ?>">
                            <?php echo esc_html((string) ($item['label'] ?? ('Panel ' . ($index + 1)))); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="rounded-lg border p-6 <?php echo esc_attr($surface_class); ?>">
                    <h3 data-compare-title class="text-xl font-semibold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) ($first['title'] ?? '')); ?></h3>
                    <p data-compare-content class="mt-3 <?php echo esc_attr($muted_class); ?> leading-7"><?php echo esc_html((string) ($first['content'] ?? '')); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
