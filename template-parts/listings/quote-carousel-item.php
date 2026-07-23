<?php

/**
 * Quotes carousel item content.
 *
 * @var array $args
 */

$quote        = $args['quote'] ?? '';
$author       = $args['author'] ?? '';
$author_role  = $args['author_role'] ?? '';
$author_image = $args['author_image'] ?? '';
?>

<div class="reci-container max-w-[1060px] px-4 sm:px-10 lg:px-20 py-8 lg:py-10 rounded-lg flex flex-col justify-center items-center gap-8 lg:gap-10">
    <div class="self-stretch text-center justify-center text-white text-2xl font-normal leading-10"><?php echo esc_html($quote); ?></div>
    <div class="flex flex-col items-center gap-3">
        <?php if ($author_image): ?>
            <img src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($author); ?>" class="w-12 h-12 rounded-full object-cover" />
        <?php endif; ?>
        <div class="flex flex-col items-center gap-1">
            <div class="text-center justify-start text-amber-400 text-base font-semibold leading-6"><?php echo esc_html($author); ?></div>
            <?php if ($author_role !== ''): ?>
                <div class="text-center justify-start text-amber-300/70 text-sm leading-5"><?php echo esc_html($author_role); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
