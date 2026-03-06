<?php

/**
 * Podcast archive card.
 *
 * @var array $args
 */

$topic_tags       = is_array($args['topic_tags'] ?? null) ? $args['topic_tags'] : [];
$type_label       = $args['type_label'] ?? 'Podcast';
$date             = $args['date'] ?? '';
$duration         = $args['duration'] ?? '';
$title            = $args['title'] ?? '';
$image_url        = $args['image_url'] ?? '';
$image_alt        = $args['image_alt'] ?? '';
$progress_percent = (int) ($args['progress_percent'] ?? 35);

if ($progress_percent < 0) {
    $progress_percent = 0;
}
if ($progress_percent > 100) {
    $progress_percent = 100;
}

$link_url  = $args['link_url'] ?? '';
$icons_url = get_template_directory_uri() . '/figma/assets/';
$type_key  = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => get_post_type_archive_link('reci_article') ?: home_url('/articles/'),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
?>

<div data-layer="Content" class="Content flex-1 self-stretch inline-flex flex-col justify-start items-start gap-5">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="self-stretch no-underline">
                <img data-layer="Image" class="Image self-stretch h-48 p-2.5 rounded-lg object-cover" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </a>
        <?php else : ?>
            <img data-layer="Image" class="Image self-stretch h-48 p-2.5 rounded-lg object-cover" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
        <?php endif; ?>
    <?php endif; ?>

    <div data-layer="Content" class="Content self-stretch flex-1 flex flex-col justify-between items-start">
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-3">
            <?php if (! empty($topic_tags)) : ?>
                <div data-layer="Tags" class="Tags inline-flex justify-start items-center gap-2 flex-wrap">
                    <?php foreach ($topic_tags as $tag) : ?>
                        <?php
                        $tag_term = get_term_by('name', (string) $tag, 'post_tag');
                        $tag_link = ($tag_term && !is_wp_error($tag_term)) ? get_term_link($tag_term) : '';
                        if (is_wp_error($tag_link) || !$tag_link) {
                            $tag_link = home_url('/tag/' . sanitize_title((string) $tag) . '/');
                        }
                        ?>
                        <a href="<?php echo esc_url($tag_link); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-gray-200 rounded flex justify-center items-center gap-2.5 no-underline">
                            <div data-layer="TagName" class="justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($tag); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div data-layer="Content" class="Content self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap">
                <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-neutral-800 rounded flex justify-center items-center gap-2.5 no-underline">
                    <div data-layer="Podcast" class="Podcast justify-start text-white text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($type_label); ?></div>
                </a>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Date" class="Date justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($date); ?></div>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                    <img src="<?php echo esc_url($icons_url . 'headphones.svg'); ?>" alt="" class="w-3.5 h-3.5 flex-shrink-0 opacity-60" aria-hidden="true" />
                    <div data-layer="Duration" class="Duration justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html($duration); ?></div>
                </div>
            </div>

            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="Title self-stretch justify-start text-neutral-800 text-xl font-bold font-['EB_Garamond'] leading-6 line-clamp-2 no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="Title" class="Title self-stretch justify-start text-neutral-800 text-xl font-bold font-['EB_Garamond'] leading-6 line-clamp-2"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
        </div>

        <div data-layer="Play" class="Play self-stretch inline-flex justify-start items-center gap-2.5 pt-2">
            <div data-layer="play bg" class="PlayBg p-1 bg-amber-400 rounded-3xl flex justify-start items-center gap-2.5">
                <img src="<?php echo esc_url($icons_url . 'play.svg'); ?>" alt="" class="w-4 h-4 flex-shrink-0 brightness-0 invert" aria-hidden="true" />
            </div>
            <div data-layer="Tags" class="Tags flex-1 bg-zinc-400 flex justify-start items-center gap-2">
                <div data-layer="Tag" class="Tag h-0.5 bg-neutral-800 rounded" style="width: <?php echo esc_attr((string) $progress_percent); ?>%;"></div>
            </div>
            <div data-layer="Duration" class="Duration justify-start text-neutral-500 text-xs font-normal font-['SF_Pro_Display']"><?php echo esc_html($duration); ?></div>
        </div>
    </div>
</div>
