<?php

/**
 * Podcast feature card.
 *
 * @var array $args
 */

$type_label       = $args['type_label'] ?? 'Podcast';
$date             = $args['date'] ?? '';
$duration         = $args['duration'] ?? '';
$title            = $args['title'] ?? '';
$tags             = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$sphere_terms     = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$excerpt          = $args['excerpt'] ?? '';
$image_url        = $args['image_url'] ?? '';
$image_alt        = $args['image_alt'] ?? '';
$bg_image_url     = $args['bg_image_url'] ?? $image_url;
$link_url         = $args['link_url'] ?? '';
$category         = $args['category'] ?? '';
$show_name        = $args['show_name'] ?? '';
$show_url         = $args['show_url'] ?? '';
$sdg_terms        = is_array($args['sdg_terms'] ?? null) ? $args['sdg_terms'] : [];
$cta_label        = $args['cta_label'] ?? '';
$cta_url          = $args['cta_url'] ?? '';
$progress_percent = (int) ($args['progress_percent'] ?? 35);
if ($progress_percent < 0) {
    $progress_percent = 0;
}
if ($progress_percent > 100) {
    $progress_percent = 100;
}

$type_key    = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => (get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/')),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
?>

<div data-layer="Podcast" class="Podcast w-full lg:max-w-[710px] min-h-[420px] lg:min-h-[700px] p-6 lg:p-10 relative inline-flex flex-col justify-start items-start gap-2.5 overflow-hidden rounded-lg" <?php if ($bg_image_url) : ?> style="background-image: url('<?php echo esc_url($bg_image_url); ?>'); background-size: cover; background-position: center;" <?php endif; ?>>
    <div data-layer="Overlay" class="Overlay absolute z-0 inset-0 bg-gradient-to-b from-black/5 via-black/80 to-black/90"></div>

    <div data-layer="Content" class="Content relative z-10 self-stretch flex-1 flex flex-col justify-end items-start gap-5">
        <div data-layer="Content" class="Content inline-flex flex-wrap justify-start items-center gap-2.5">
                <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-neutral-800 rounded flex justify-center items-center gap-2.5 no-underline">
                    <div data-layer="Podcast" class="Podcast justify-start text-white text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></div>
                </a>
                <?php if ($show_name !== '') : ?>
                    <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
                    <a href="<?php echo esc_url($show_url); ?>" class="text-white hover:underline text-sm font-normal leading-4"><?php echo esc_html($show_name); ?></a>
                <?php endif; ?>
                <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
                <div data-layer="Date" class="Jan2026 justify-start text-white text-base font-normal leading-4"><?php echo esc_html($date); ?></div>
                <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
                <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                    <?php echo reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 brightness-0 invert opacity-90', ['aria-hidden' => 'true']); ?>
                    <div data-layer="Duration" class="27 justify-start text-white text-sm font-normal"><?php echo esc_html($duration); ?></div>
                </div>
                <?php if ($category !== '') : ?>
                    <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
                    <a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="text-white hover:underline text-base font-normal leading-4"><?php echo esc_html($category); ?></a>
                <?php endif; ?>
        </div>
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="self-stretch text-white reci-card-title-lg no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="Title" class="self-stretch text-white reci-card-title-lg"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-6">
                <div data-layer="Excerpt" class="self-stretch justify-start text-white text-lg font-normal leading-7"><?php echo esc_html($excerpt); ?></div>
                <?php if (!empty($sdg_terms) || !empty($sphere_terms) || $show_name !== '') : ?>
                    <div class="self-stretch inline-flex flex-wrap justify-start items-center gap-2 overflow-hidden">
                    <?php if ($show_name !== '') : ?>
                        <a href="<?php echo esc_url($show_url ?: $link_url); ?>" class="tag bg-white/10 text-white border-white/20"><?php echo esc_html($show_name); ?></a>
                    <?php endif; ?>
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
                <?php endif; ?>
            </div>
        </div>
        <div class="self-stretch flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pt-2">
            <div class="flex flex-wrap items-center gap-2 text-sm text-white/90">
                <?php if (!empty($args['video_url'])) : ?>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15">
                        <?php echo reci_inline_svg('assets/icons/play.svg', 'w-3.5 h-3.5 flex-shrink-0 brightness-0 invert opacity-90', ['aria-hidden' => 'true']); ?>
                        <span>Video episode</span>
                    </span>
                <?php elseif (!empty($args['audio_url'])) : ?>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15">
                        <?php echo reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 brightness-0 invert opacity-90', ['aria-hidden' => 'true']); ?>
                        <span>Audio episode</span>
                    </span>
                <?php endif; ?>
            </div>
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" class="btn btn-primary btn-sm md:btn-md no-underline"><?php echo esc_html($cta_label !== '' ? $cta_label : 'Open episode'); ?></a>
            <?php elseif ($cta_url !== '') : ?>
                <a href="<?php echo esc_url($cta_url); ?>"<?php echo !empty($args['video_url']) || !empty($args['audio_url']) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?> class="btn btn-primary btn-sm md:btn-md no-underline"><?php echo esc_html($cta_label !== '' ? $cta_label : 'Open episode'); ?></a>
            <?php elseif ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" class="btn btn-primary btn-sm md:btn-md no-underline">Open episode</a>
            <?php endif; ?>
        </div>
    </div>
</div>
