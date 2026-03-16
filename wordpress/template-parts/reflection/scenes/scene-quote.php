<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$surface_class   = reci_reflection_surface_class($scene);
$heading_class   = reci_reflection_heading_class($scene);
$muted_class     = reci_reflection_muted_class($scene);
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-quote')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <div class="mx-auto max-w-4xl rounded-lg border p-8 shadow-sm sm:p-12 <?php echo esc_attr($surface_class); ?>">
            <blockquote class="text-2xl sm:text-3xl font-medium font-['EB_Garamond'] leading-tight <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) ($scene['quote'] ?? '')); ?></blockquote>
            <div class="mt-6 flex flex-wrap items-center gap-3 <?php echo esc_attr($muted_class); ?>">
                <?php if (! empty($scene['speaker'])) : ?><span class="text-base font-semibold <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['speaker']); ?></span><?php endif; ?>
                <?php if (! empty($scene['role'])) : ?><span class="text-sm"><?php echo esc_html((string) $scene['role']); ?></span><?php endif; ?>
            </div>
        </div>
    </div>
</section>
