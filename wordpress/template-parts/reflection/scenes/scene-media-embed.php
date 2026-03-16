<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$video_url = (string) ($scene['video_url'] ?? '');
$audio_url = (string) ($scene['audio_url'] ?? '');
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20');
$heading_class   = reci_reflection_heading_class($scene);
$surface_class   = reci_reflection_surface_class($scene);
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-media-embed')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <div class="<?php echo esc_attr($inner_classes); ?>">
        <?php if (! empty($scene['title'])) : ?><h2 class="mb-8 text-2xl sm:text-3xl font-bold font-['EB_Garamond'] <?php echo esc_attr($heading_class); ?>"><?php echo esc_html((string) $scene['title']); ?></h2><?php endif; ?>
        <div class="grid gap-8 lg:grid-cols-2">
            <?php if ($video_url !== '') : ?>
                <div class="rounded-lg border p-4 <?php echo esc_attr($surface_class); ?>">
                    <div class="aspect-video overflow-hidden rounded bg-black/70">
                        <iframe src="<?php echo esc_url($video_url); ?>" class="h-full w-full" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($audio_url !== '') : ?>
                <div class="rounded-lg border p-6 shadow-sm <?php echo esc_attr($surface_class); ?>">
                    <audio controls class="w-full" src="<?php echo esc_url($audio_url); ?>"></audio>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
