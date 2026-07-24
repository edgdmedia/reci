<?php
/**
 * Articles listing card.
 *
 * @var array $args
 */

$type_label      = $args['type_label'] ?? 'Article';
$type_badge      = $args['type_badge_class'] ?? 'bg-amber-400';
$type_text       = $args['type_text_class'] ?? 'text-neutral-800';
$date            = $args['date'] ?? '';
$meta_value      = $args['meta_value'] ?? '';
$meta_icon       = $args['meta_icon'] ?? 'timer';
$title           = $args['title'] ?? '';
$author_name     = $args['author_name'] ?? '';
$author_url      = $args['author_url'] ?? '';
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$sphere_terms    = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$sdg_terms       = is_array($args['sdg_terms'] ?? null) ? $args['sdg_terms'] : [];
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';
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
    $icon_markup = reci_inline_svg('assets/icons/timer-outline.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-60', ['aria-hidden' => 'true']);
}
?>

<div class="VideoCard self-stretch flex flex-col justify-start items-start gap-5">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="self-stretch no-underline">
                <img class="w-full aspect-video object-cover rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy" />
            </a>
        <?php else : ?>
            <img class="w-full aspect-video object-cover rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy" />
        <?php endif; ?>
    <?php endif; ?>
    <div class="Content self-stretch flex-1 flex flex-col justify-between items-start gap-4">
        <div class="Content self-stretch flex flex-col justify-start items-start gap-4">
            <div class="self-stretch overflow-x-auto whitespace-nowrap no-scrollbar">
                <div class="inline-flex justify-start items-center gap-2.5">
                    <a href="<?php echo esc_url($type_archive_url); ?>" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
                        <div class="justify-start <?php echo esc_attr($type_text); ?> text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></div>
                    </a>
                    <div class="Tag tag-dot"></div>
                    <div class="justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($date); ?></div>
                    <?php if ($meta_value !== '') : ?>
                    <div class="Tag tag-dot"></div>
                    <div class="NavText inline-flex justify-start items-center gap-0.5">
                        <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <div class="Mins justify-start text-neutral-600 text-sm font-normal"><?php echo esc_html($meta_value); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if ($category !== '') : ?>
                    <div class="Tag tag-dot"></div>
                    <a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="hover:underline no-underline text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($category); ?></a>
                    <?php endif; ?>
                    <?php if ($author_name !== '') : ?>
                    <div class="Tag tag-dot"></div>
                    <div class="justify-start text-neutral-600 text-sm font-normal leading-4">
                        <?php esc_html_e('by', 'reci-media-hub'); ?>
                        <?php if ($author_url !== '') : ?>
                            <a href="<?php echo esc_url($author_url); ?>" class="hover:underline no-underline text-neutral-600"><?php echo esc_html($author_name); ?></a>
                        <?php else : ?>
                            <?php echo esc_html($author_name); ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" class="self-stretch reci-card-title"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div class="self-stretch reci-card-title"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
            <div class="self-stretch inline-flex justify-start items-center gap-2 flex-nowrap overflow-hidden">
                <?php if (!empty($sdg_terms)) :
                        $sdg = $sdg_terms[0]; ?>
                    <a href="<?php echo esc_url($sdg['url']); ?>" class="tag" style="background-color: <?php echo esc_attr($sdg['color']); ?>1a; color: <?php echo esc_attr($sdg['color']); ?>; border-color: <?php echo esc_attr($sdg['color']); ?>4d;">
                        <?php echo esc_html($sdg['name']); ?>
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
        </div>
    </div>
</div>
