<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$items = is_array($scene['items'] ?? null) ? $scene['items'] : [];
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$heading_class   = reci_reflection_heading_class($scene);
$muted_class     = reci_reflection_muted_class($scene);
$surface_class   = reci_reflection_surface_class($scene);
$gallery_columns = reci_reflection_columns_class(reci_reflection_scene_option($scene, 'columns', 'auto'), 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3');
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-gallery')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?><h2 class="mb-8 text-2xl sm:text-3xl font-bold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2><?php endif; ?>
        <div class="grid gap-6 <?php echo esc_attr($gallery_columns); ?>">
            <?php foreach ($items as $item) : if (! is_array($item)) { continue; } ?>
                <figure class="overflow-hidden rounded-lg border <?php echo esc_attr($surface_class); ?>">
                    <?php if (! empty($item['image_url'])) : ?><img src="<?php echo esc_url((string) $item['image_url']); ?>" alt="<?php echo esc_attr((string) ($item['alt'] ?? '')); ?>" class="h-72 w-full object-cover" /><?php endif; ?>
                    <?php if (! empty($item['caption'])) : ?><figcaption class="p-4 text-sm <?php echo esc_attr($muted_class); ?>"><?php echo esc_html((string) $item['caption']); ?></figcaption><?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
