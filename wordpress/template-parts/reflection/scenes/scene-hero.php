<?php
if (! defined('ABSPATH')) {
    exit;
}
$scene = is_array($args['scene'] ?? null) ? $args['scene'] : [];
$bg    = (string) ($scene['background_image_url'] ?? '');
$section_classes = reci_reflection_section_classes($scene, 'reci-immersive-scene relative min-h-[680px] flex items-end overflow-hidden');
$inner_classes   = reci_reflection_inner_classes($scene, 'reci-scene-inner relative z-10 mx-auto flex w-full flex-col gap-6 px-4 sm:px-6 lg:px-12 xl:px-20');
$accent_class    = reci_reflection_accent_class($scene);
?>
<section id="<?php echo esc_attr((string) ($scene['id'] ?? 'scene-hero')); ?>" class="<?php echo esc_attr($section_classes); ?>" data-template="<?php echo esc_attr((string) ($scene['template'] ?? 'narrative')); ?>">
    <?php if ($bg !== '') : ?>
        <img src="<?php echo esc_url($bg); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" />
    <?php endif; ?>
    <div class="absolute inset-0 bg-gradient-to-t from-neutral-900 via-neutral-900/70 to-neutral-900/25"></div>
    <div class="reci-scene-inner relative z-10 mx-auto flex w-full max-w-[1440px] flex-col gap-6 px-4 py-14 sm:px-6 lg:px-12 xl:px-20">
        <?php if (! empty($scene['badge'])) : ?>
            <div class="inline-flex w-fit items-center rounded bg-amber-400 px-2 py-1 text-sm text-neutral-800"><?php echo esc_html((string) $scene['badge']); ?></div>
        <?php endif; ?>
        <?php if (! empty($scene['title'])) : ?>
            <h1 class="max-w-4xl text-white text-3xl sm:text-4xl lg:text-5xl font-bold font-['EB_Garamond'] leading-tight"><?php echo esc_html((string) $scene['title']); ?></h1>
        <?php endif; ?>
        <?php if (! empty($scene['quote'])) : ?>
            <blockquote class="max-w-4xl text-white text-2xl sm:text-3xl lg:text-4xl font-medium font-['EB_Garamond'] leading-tight"><?php echo esc_html((string) $scene['quote']); ?></blockquote>
        <?php endif; ?>
        <div class="flex flex-wrap items-center gap-3 text-white/85">
            <?php if (! empty($scene['speaker'])) : ?><span class="text-lg sm:text-xl font-semibold font-['SF_Pro_Display']"><?php echo esc_html((string) $scene['speaker']); ?></span><?php endif; ?>
            <?php if (! empty($scene['role'])) : ?><span class="text-sm sm:text-base"><?php echo esc_html((string) $scene['role']); ?></span><?php endif; ?>
        </div>
    </div>
</section>
