<?php
/**
 * Archive horizontal row item (category/topic style).
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
$excerpt         = $args['excerpt'] ?? '';
$author_name     = $args['author_name'] ?? '';
$author_url      = $args['author_url'] ?? '';
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$sphere_terms    = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$sdg_terms       = is_array($args['sdg_terms'] ?? null) ? $args['sdg_terms'] : [];
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';
$title_classes   = $args['title_classes'] ?? "self-stretch reci-card-title";
$excerpt_classes = $args['excerpt_classes'] ?? "self-stretch justify-start text-neutral-600 text-base lg:text-lg font-normal leading-7 ";
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

<div data-layer="Content" class="Content self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-14">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="no-underline reci-listing-media reci-listing-media--archive-row">
                <img data-layer="Image" class="Image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </a>
        <?php else : ?>
            <div class="reci-listing-media reci-listing-media--archive-row">
                <img data-layer="Image" class="Image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div data-layer="Content" class="Content flex-1 lg:h-60 inline-flex flex-col justify-between items-start gap-4">
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-2">
            <div data-layer="Content" class="Content inline-flex justify-start items-center gap-2.5 flex-wrap">
                <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
                    <div data-layer="<?php echo esc_attr($type_label); ?>" class="justify-start <?php echo esc_attr($type_text); ?> text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></div>
                </a>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Date" class="Date justify-start text-neutral-600 text-base font-normal leading-4"><?php echo esc_html($date); ?></div>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                    <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div data-layer="Meta" class="Meta justify-start text-neutral-600 text-base font-normal"><?php echo esc_html($meta_value); ?></div>
                </div>
                <?php if ($category !== '') : ?>
                    <div data-layer="Tag" class="Tag tag-dot"></div>
                    <a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="justify-start text-neutral-600 text-base font-normal leading-4 hover:underline no-underline"><?php echo esc_html($category); ?></a>
                <?php endif; ?>
                <?php if ($author_name !== '') : ?>
                    <div data-layer="Tag" class="Tag tag-dot"></div>
                    <div data-layer="Author" class="Author flex justify-start items-center gap-1">
                        <span class="text-neutral-600 text-base font-normal leading-4"><?php esc_html_e('by', 'reci-media-hub'); ?></span>
                        <?php if ($author_url !== '') : ?>
                            <a href="<?php echo esc_url($author_url); ?>" class="justify-start text-neutral-600 text-base font-normal leading-4 hover:underline no-underline"><?php echo esc_html($author_name); ?></a>
                        <?php else : ?>
                            <div class="justify-start text-neutral-600 text-base font-normal leading-4"><?php echo esc_html($author_name); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
                <?php if ($link_url) : ?>
                    <a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="<?php echo esc_attr($title_classes); ?> no-underline"><?php echo esc_html($title); ?></a>
                <?php else : ?>
                    <div data-layer="Title" class="<?php echo esc_attr($title_classes); ?>"><?php echo esc_html($title); ?></div>
                <?php endif; ?>
                <?php if ($excerpt !== '') : ?>
                    <div data-layer="Excerpt" class="<?php echo esc_attr($excerpt_classes); ?>"><?php echo esc_html($excerpt); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($sdg_terms) || !empty($sphere_terms)) : ?>
            <div class="self-stretch inline-flex justify-start items-center gap-2 flex-nowrap overflow-hidden">
                <?php if (!empty($sdg_terms)) :
                    $sdg = $sdg_terms[0]; ?>
                    <a href="<?php echo esc_url($sdg['url']); ?>" class="tag" style="background-color: <?php echo esc_attr($sdg['color']); ?>1a;">
                        <span class="rounded-full w-2 h-2" style="background-color: <?php echo esc_attr($sdg['color']); ?>;"></span>
                        <span class="font-medium" style="color: <?php echo esc_attr($sdg['color']); ?>;"><?php echo esc_html($sdg['name']); ?></span>
                    </a>
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
