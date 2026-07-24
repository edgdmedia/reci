<?php

/**
 * Podcast archive card.
 *
 * @var array $args
 */

$topic_tags       = is_array($args['topic_tags'] ?? null) ? $args['topic_tags'] : [];
$tags             = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$type_label       = $args['type_label'] ?? 'Podcast';
$date             = $args['date'] ?? '';
$duration         = $args['duration'] ?? '';
$title            = $args['title'] ?? '';
$author_name      = $args['author_name'] ?? '';
$author_url       = $args['author_url'] ?? '';
$sphere_terms     = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$image_url        = $args['image_url'] ?? '';
$image_alt        = $args['image_alt'] ?? '';
$progress_percent = (int) ($args['progress_percent'] ?? 35);
$audio_url        = (string) ($args['audio_url'] ?? '');
$video_url        = (string) ($args['video_url'] ?? '');
$post_id          = (int) ($args['post_id'] ?? $args['id'] ?? 0);
$has_audio        = $audio_url !== '';
$has_video        = $video_url !== '';

if ($progress_percent < 0) {
    $progress_percent = 0;
}
if ($progress_percent > 100) {
    $progress_percent = 100;
}

$link_url  = $args['link_url'] ?? '';
$category  = $args['category'] ?? '';
$type_key  = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => (get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/')),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
?>

<div data-layer="Content" class="Content flex-1 self-stretch inline-flex flex-col justify-start items-start gap-5">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="self-stretch no-underline reci-listing-media reci-listing-media--podcast-archive">
                <img data-layer="Image" class="Image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </a>
        <?php else : ?>
            <div class="self-stretch reci-listing-media reci-listing-media--podcast-archive">
                <img data-layer="Image" class="Image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div data-layer="Content" class="Content self-stretch flex-1 flex flex-col justify-between items-start">
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-3">
            <div data-layer="Content" class="Content self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap">
                <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-neutral-800 rounded flex justify-center items-center gap-2.5 no-underline">
                    <div data-layer="Podcast" class="Podcast justify-start text-white text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></div>
                </a>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Date" class="Date justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($date); ?></div>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                    <?php echo reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-60', ['aria-hidden' => 'true']); ?>
                    <div data-layer="Duration" class="Duration justify-start text-neutral-600 text-sm font-normal"><?php echo esc_html($duration); ?></div>
                </div>
                <?php if ($author_name !== '') : ?>
                    <div data-layer="Tag" class="Tag tag-dot"></div>
                    <div data-layer="Author" class="Author flex justify-start items-center gap-1">
                        <?php if ($author_url !== '') : ?>
                            <a href="<?php echo esc_url($author_url); ?>" class="justify-start text-neutral-600 text-sm font-normal leading-4 hover:underline no-underline"><?php echo esc_html($author_name); ?></a>
                        <?php else : ?>
                            <div class="justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($author_name); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="Title self-stretch reci-side-listing-title line-clamp-2 no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="Title" class="Title self-stretch reci-side-listing-title line-clamp-2"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
        </div>

        <?php if ($has_audio) : ?>
            <div data-layer="Play" class="Play self-stretch inline-flex justify-start items-center gap-2.5 pt-1" data-audio-player>
                <button type="button"
                    data-audio-toggle
                    data-audio-target="podcast-audio-<?php echo esc_attr((string) $post_id); ?>"
                    class="p-1 bg-amber-400 rounded-3xl flex justify-start items-center gap-2.5 focus:outline-none focus:ring-2 focus:ring-amber-300"
                    aria-label="<?php esc_attr_e('Play episode', 'reci-media-hub'); ?>">
                    <svg data-audio-play-icon class="w-4 h-4 flex-shrink-0 brightness-0 invert" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                    <svg data-audio-pause-icon class="w-4 h-4 flex-shrink-0 brightness-0 invert hidden" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                    </svg>
                </button>
                <div class="Tags flex-1 h-1 bg-zinc-400 rounded-full overflow-hidden cursor-pointer"
                    role="progressbar"
                    aria-valuenow="0"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="<?php esc_attr_e('Playback progress', 'reci-media-hub'); ?>"
                    data-audio-progress>
                    <div class="h-full bg-neutral-800 rounded-full" style="width: 0%;" data-audio-progress-bar></div>
                </div>
                <span class="Duration justify-start text-neutral-600 text-xs font-normal tabular-nums" data-audio-time><?php echo esc_html($duration); ?></span>
                <audio id="podcast-audio-<?php echo esc_attr((string) $post_id); ?>" src="<?php echo esc_url($audio_url); ?>" preload="none" class="sr-only"></audio>
            </div>
        <?php endif; ?>
        <?php if ($category !== '' || ! empty($sphere_terms)) : ?>
            <div class="self-stretch inline-flex justify-start items-center gap-2 flex-nowrap overflow-hidden">
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
</div>
