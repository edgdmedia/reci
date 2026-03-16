<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$items = array_values(array_filter(is_array($scene['items'] ?? null) ? $scene['items'] : [], 'is_array'));
$first = $items[0] ?? [];
$image = (string) ($scene['image_url'] ?? $scene['background_image_url'] ?? 'https://placehold.co/1200x800');
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$heading_class   = reci_reflection_heading_class($scene);
$muted_class     = reci_reflection_muted_class($scene);
$surface_class   = reci_reflection_surface_class($scene);
$accent_class    = reci_reflection_accent_class($scene);
$grid_classes    = reci_reflection_layout_grid_class(reci_reflection_scene_option($scene, 'layout', 'sidebar-right'));
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-hotspots')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>" data-reflection-hotspots>
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?><h2 class="mb-8 text-2xl sm:text-3xl font-bold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2><?php endif; ?>
        <div class="<?php echo esc_attr($grid_classes); ?>">
            <div class="relative overflow-hidden rounded-lg border <?php echo esc_attr($surface_class); ?>">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr((string) ($scene['image_alt'] ?? '')); ?>" class="w-full object-cover" />
                <?php foreach ($items as $index => $item) : 
                    $x = isset($item['x']) ? (float) $item['x'] : 50;
                    $y = isset($item['y']) ? (float) $item['y'] : 50;
                ?>
                    <button type="button" class="absolute flex h-8 w-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 text-sm font-semibold shadow <?php echo esc_attr($accent_class); ?> <?php echo 0 === $index ? 'ring-2 ring-white border-white' : 'border-white/70'; ?>" style="left: <?php echo esc_attr((string) $x); ?>%; top: <?php echo esc_attr((string) $y); ?>%;" data-hotspot-trigger data-hotspot-index="<?php echo esc_attr((string) $index); ?>" data-hotspot-title="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" data-hotspot-content="<?php echo esc_attr(wp_strip_all_tags((string) ($item['content'] ?? ''))); ?>" data-hotspot-label="<?php echo esc_attr((string) ($item['label'] ?? ($index + 1))); ?>">
                        <?php echo esc_html((string) ($item['label'] ?? ($index + 1))); ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="rounded-lg border p-6 shadow-sm <?php echo esc_attr($surface_class); ?>">
                <span data-hotspot-label class="inline-flex rounded px-2 py-1 text-sm <?php echo esc_attr($accent_class); ?>"><?php echo esc_html((string) ($first['label'] ?? '1')); ?></span>
                <h3 data-hotspot-title class="mt-4 text-xl font-semibold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) ($first['title'] ?? '')); ?></h3>
                <p data-hotspot-content class="mt-3 <?php echo esc_attr($muted_class); ?> leading-7"><?php echo esc_html((string) ($first['content'] ?? '')); ?></p>
            </div>
        </div>
    </div>
</section>
