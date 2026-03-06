<?php

/**
 * Compact listing item for sidebar/repeated rows.
 *
 * @var array $args
 */

$type_label      = $args['type_label'] ?? 'Article';
$type_badge      = $args['type_badge_class'] ?? 'bg-amber-400';
$type_text       = $args['type_text_class'] ?? 'text-neutral-800';
$date            = $args['date'] ?? '';
$meta_value      = $args['meta_value'] ?? '';
$meta_icon       = $args['meta_icon'] ?? 'timer'; // timer|audio|video
$title           = $args['title'] ?? '';
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';
$meta_value_size = $args['meta_value_size'] ?? 'text-sm';
$link_url        = $args['link_url'] ?? '';

$icons_url   = get_template_directory_uri() . '/figma/assets/';
$type_key    = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => get_post_type_archive_link('reci_article') ?: home_url('/articles/'),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
$icon_markup = '';
if ($meta_icon === 'audio') {
    $icon_markup = '<img src="' . esc_url($icons_url . 'headphones.svg') . '" alt="" class="w-3.5 h-3.5 flex-shrink-0 opacity-60" aria-hidden="true" />';
} elseif ($meta_icon === 'video') {
    $icon_markup = '<img src="' . esc_url($icons_url . 'play.svg') . '" alt="" class="w-3.5 h-3.5 flex-shrink-0 opacity-60" aria-hidden="true" />';
} else {
    $icon_markup = '<img src="' . esc_url($icons_url . 'timer-outline.svg') . '" alt="" class="w-4 h-4 flex-shrink-0 opacity-60" aria-hidden="true" />';
}
?>

<div data-layer="Content" class="Content self-stretch inline-flex justify-start items-start gap-5">
    <?php if ($link_url) : ?>
        <a href="<?php echo esc_url($link_url); ?>" class="no-underline">
            <img data-layer="Image" class="Image w-36 md:w-44 h-36 p-2.5 rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
        </a>
    <?php else : ?>
        <img data-layer="Image" class="Image w-36 md:w-44 h-36 p-2.5 rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
    <?php endif; ?>
    <div data-layer="Content" class="Content flex-1 self-stretch inline-flex flex-col justify-between items-start">
        <div data-layer="Content" class="Content self-stretch inline-flex justify-start items-center gap-2.5">
            <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
                <div data-layer="<?php echo esc_attr($type_label); ?>" class="<?php echo esc_attr($type_label); ?> justify-start <?php echo esc_attr($type_text); ?> text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($type_label); ?></div>
            </a>
            <div data-layer="Tag" class="Tag tag-dot"></div>
            <div data-layer="08 Jan 2026" class="Jan2026 justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($date); ?></div>
            <div data-layer="Tag" class="Tag tag-dot"></div>
            <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div data-layer="meta" class="27 justify-start text-neutral-500 <?php echo esc_attr($meta_value_size); ?> font-normal font-['SF_Pro_Display']"><?php echo esc_html($meta_value); ?></div>
            </div>
        </div>
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="title" class="self-stretch justify-start text-neutral-800 text-2xl font-semibold font-['EB_Garamond'] leading-7 line-clamp-2 no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="title" class="self-stretch justify-start text-neutral-800 text-2xl font-semibold font-['EB_Garamond'] leading-7 line-clamp-2"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
            <?php if (!empty($tags)) : ?>
                <div data-layer="Tags" class="Tags self-stretch inline-flex justify-start items-center gap-2 flex-wrap content-center">
                    <?php foreach ($tags as $tag) : ?>
                        <?php
                        $tag_term = get_term_by('name', (string) $tag, 'post_tag');
                        $tag_link = ($tag_term && !is_wp_error($tag_term)) ? get_term_link($tag_term) : '';
                        if (is_wp_error($tag_link) || !$tag_link) {
                            $tag_link = home_url('/tag/' . sanitize_title((string) $tag) . '/');
                        }
                        ?>
                        <a href="<?php echo esc_url($tag_link); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-gray-200 rounded flex justify-center items-center gap-2.5 no-underline">
                            <div data-layer="<?php echo esc_attr($tag); ?>" class="justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($tag); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
