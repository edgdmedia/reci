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
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-timeline')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?><h2 class="mb-8 text-2xl sm:text-3xl font-bold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2><?php endif; ?>
        <div class="grid gap-6">
            <?php foreach ($items as $item) : if (! is_array($item)) { continue; } ?>
                <div class="grid gap-2 rounded-lg border p-6 md:grid-cols-[140px_1fr] <?php echo esc_attr($surface_class); ?>">
                    <div class="text-sm font-semibold uppercase tracking-wide <?php echo esc_attr($muted_class); ?>"><?php echo esc_html((string) ($item['label'] ?? $item['date'] ?? '')); ?></div>
                    <div>
                        <?php if (! empty($item['title'])) : ?><h3 class="text-xl font-semibold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $item['title']); ?></h3><?php endif; ?>
                        <?php if (! empty($item['content'])) : ?><div class="mt-2 <?php echo esc_attr($muted_class); ?> leading-7"><?php echo wp_kses_post((string) $item['content']); ?></div><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
