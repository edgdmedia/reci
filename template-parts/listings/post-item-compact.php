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
$title_text      = $args['title_text_class'] ?? 'text-neutral-800';
$author_name     = $args['author_name'] ?? '';
$author_url      = $args['author_url'] ?? '';
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$sphere_terms    = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';
$meta_value_size = $args['meta_value_size'] ?? 'text-sm';
$link_url        = $args['link_url'] ?? '';
$category        = $args['category'] ?? '';

$type_key    = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => (get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/')),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
$icon_markup = '';
if ($meta_icon === 'audio') {
    $icon_markup = reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-60', ['aria-hidden' => 'true']);
} elseif ($meta_icon === 'video') {
    $icon_markup = reci_inline_svg('assets/icons/play.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-60', ['aria-hidden' => 'true']);
} else {
    $icon_markup = reci_inline_svg('assets/icons/timer-outline.svg', 'w-4 h-4 flex-shrink-0 opacity-60', ['aria-hidden' => 'true']);
}
?>

<div data-layer="Content" class="Content self-stretch flex flex-row max-[450px]:flex-col justify-start items-start gap-4 sm:gap-5">
    <?php if ($link_url) : ?>
        <a href="<?php echo esc_url($link_url); ?>" class="no-underline block w-28 h-28 md:w-32 md:h-32 flex-shrink-0 max-[450px]:w-full max-[450px]:h-auto">
            <img data-layer="Image" class="Image w-full h-28 md:h-32 max-[450px]:h-[220px] flex-shrink-0 object-cover rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
        </a>
    <?php else : ?>
        <div class="block w-28 h-28 md:w-32 md:h-32 flex-shrink-0 max-[450px]:w-full max-[450px]:h-auto">
            <img data-layer="Image" class="Image w-full h-28 md:h-32 max-[450px]:h-[220px] flex-shrink-0 object-cover rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
        </div>
    <?php endif; ?>
    <div data-layer="Content" class="Content flex-1 self-stretch inline-flex flex-col justify-between items-start">
        <div data-layer="Content" class="Content self-stretch reci-meta-row">
            <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
                <div data-layer="<?php echo esc_attr($type_label); ?>" class="<?php echo esc_attr($type_label); ?> justify-start <?php echo esc_attr($type_text); ?> text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></div>
            </a>
            <div data-layer="Tag" class="Tag tag-dot"></div>
            <div data-layer="08 Jan 2026" class="Jan2026 justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($date); ?></div>
            <div data-layer="Tag" class="Tag tag-dot"></div>
            <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
                <div data-layer="meta" class="27 justify-start text-neutral-600 <?php echo esc_attr($meta_value_size); ?> font-normal"><?php echo esc_html($meta_value); ?></div>
            </div>
        </div>
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-2 mt-2">
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="title" class="self-stretch <?php echo esc_attr($title_text); ?> reci-side-listing-title no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="title" class="self-stretch <?php echo esc_attr($title_text); ?> reci-side-listing-title"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
            <?php if ($author_name !== '') : ?>
                <p class="text-neutral-600 text-sm font-normal">By
                    <?php if ($author_url !== '') : ?>
                        <a href="<?php echo esc_url($author_url); ?>" class="text-neutral-600 hover:underline"><?php echo esc_html($author_name); ?></a>
                    <?php else : ?>
                        <?php echo esc_html($author_name); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php if ($category !== '' || !empty($sphere_terms)) : ?>
                <div class="self-stretch inline-flex justify-start items-center gap-2 flex-nowrap overflow-hidden">
                    <?php if ($category !== '') : ?>
                        <a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="tag"><?php echo esc_html($category); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($sphere_terms)) :
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
</div>
