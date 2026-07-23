<?php
/**
 * Podcast sidebar card for single podcast related episodes.
 *
 * @var array $args
 */

$type_label   = $args['type_label'] ?? 'Podcast';
$date         = $args['date'] ?? '';
$duration     = $args['duration'] ?? '';
$title        = $args['title'] ?? '';
$link_url     = $args['link_url'] ?? '';
$audio_url    = $args['audio_url'] ?? '';
$post_id      = (int) ($args['post_id'] ?? $args['id'] ?? 0);
$category     = $args['category'] ?? '';
$sphere_terms = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
?>

<div class="self-stretch flex flex-col justify-start items-start gap-5">
    <div class="self-stretch flex flex-col justify-start items-start gap-2">
        <div class="self-stretch inline-flex justify-start items-center gap-2.5">
            <div class="px-2 py-1 bg-neutral-500 rounded flex justify-center items-center gap-2.5">
                <span class="text-white text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></span>
            </div>
            <div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
            <span class="text-zinc-400 text-sm font-normal leading-4"><?php echo esc_html($date); ?></span>
            <?php if ($duration !== '') : ?>
                <div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
                <div class="flex justify-start items-center gap-0.5">
                    <?php echo reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-40 invert', ['aria-hidden' => 'true']); ?>
                    <span class="text-zinc-400 text-sm font-normal"><?php echo esc_html($duration); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="self-stretch reci-side-listing-title-dark line-clamp-2 hover:underline">
                <?php echo esc_html($title); ?>
            </a>
        <?php else : ?>
            <h3 class="self-stretch reci-side-listing-title-dark line-clamp-2">
                <?php echo esc_html($title); ?>
            </h3>
        <?php endif; ?>
    </div>

    <?php if ($audio_url) : ?>
        <div class="self-stretch inline-flex justify-start items-center gap-2.5" data-audio-player>
            <button type="button"
                data-audio-toggle
                data-audio-target="podcast-sidebar-audio-<?php echo esc_attr((string) $post_id); ?>"
                class="p-1 bg-amber-400 rounded-3xl flex justify-start items-center gap-2.5 focus:outline-none focus:ring-2 focus:ring-amber-300"
                aria-label="<?php esc_attr_e('Play episode', 'reci-media-hub'); ?>">
                <svg data-audio-play-icon class="w-4 h-4 flex-shrink-0 brightness-0 invert" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M8 5v14l11-7z" />
                </svg>
                <svg data-audio-pause-icon class="w-4 h-4 flex-shrink-0 brightness-0 invert hidden" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                </svg>
            </button>
            <div class="flex-1 bg-neutral-500 rounded h-0.5 overflow-hidden cursor-pointer"
                role="progressbar"
                aria-valuenow="0"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-label="<?php esc_attr_e('Playback progress', 'reci-media-hub'); ?>"
                data-audio-progress>
                <div class="h-full bg-neutral-800 rounded-full" style="width: 0%;" data-audio-progress-bar></div>
            </div>
            <span class="text-zinc-400 text-xs font-normal tabular-nums" data-audio-time><?php echo esc_html($duration); ?></span>
            <audio id="podcast-sidebar-audio-<?php echo esc_attr((string) $post_id); ?>" src="<?php echo esc_url($audio_url); ?>" preload="none" class="sr-only"></audio>
        </div>
    <?php endif; ?>

    <?php if ($category !== '' || ! empty($sphere_terms)) : ?>
        <div class="inline-flex justify-start items-center gap-2 flex-wrap">
            <?php if ($category !== '') : ?>
                <a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="tag"><?php echo esc_html($category); ?></a>
            <?php endif; ?>
            <?php if (! empty($sphere_terms)) :
                $s = $sphere_terms[0]; ?>
                <a href="<?php echo esc_url($s['url']); ?>" class="sphere" style="background-color: <?php echo esc_attr($s['color']); ?>1a;">
                    <span class="rounded-full w-2 h-2" style="background-color: <?php echo esc_attr($s['color']); ?>;"></span>
                    <span class="font-medium" style="color: <?php echo esc_attr($s['color']); ?>;"><?php echo esc_html($s['name']); ?></span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
