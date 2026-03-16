<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$heading_class   = reci_reflection_heading_class($scene);
$muted_class     = reci_reflection_muted_class($scene);
$dot_class       = reci_reflection_dot_class($scene);
$content_width   = reci_reflection_width_class(reci_reflection_scene_option($scene, 'text_width', 'prose'));
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-rich-text')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?>
            <div class="mb-8 flex items-center gap-3">
                <div class="h-2 w-2 rounded-sm <?php echo esc_attr($dot_class); ?>"></div>
                <h2 class="text-2xl sm:text-3xl font-bold font-['EB_Garamond'] leading-tight <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2>
            </div>
        <?php endif; ?>
        <div class="reci-reflection-content mx-auto <?php echo esc_attr($content_width); ?> font-['EB_Garamond'] text-lg leading-8 [&_p]:mb-6 [&_h2]:mt-10 [&_h2]:mb-4 [&_ul]:mb-6 [&_li]:mb-2">
            <?php echo wp_kses_post((string) ($scene['content'] ?? '')); ?>
        </div>
    </div>
</section>
