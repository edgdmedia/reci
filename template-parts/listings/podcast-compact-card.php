<?php

/**
 * Podcast compact card.
 *
 * @var array $args
 */

$topic_tags       = is_array($args['topic_tags'] ?? null) ? $args['topic_tags'] : [];
$type_label       = $args['type_label'] ?? 'Podcast';
$date             = $args['date'] ?? '';
$duration         = $args['duration'] ?? '';
$title            = $args['title'] ?? '';
$author_name      = $args['author_name'] ?? '';
$author_url       = $args['author_url'] ?? '';
$sphere_terms     = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$image_url        = $args['image_url'] ?? '';
$image_alt        = $args['image_alt'] ?? '';
$link_url         = $args['link_url'] ?? '';
$category         = $args['category'] ?? '';
$show_name        = $args['show_name'] ?? '';
$show_url         = $args['show_url'] ?? '';
$sdg_terms        = is_array($args['sdg_terms'] ?? null) ? $args['sdg_terms'] : [];
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

<div data-layer="Content" class="Content flex h-full flex-col xl:flex-row justify-start items-start gap-5 px-2 sm:px-4 xl:px-6 py-6">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="block w-full xl:w-40 xl:self-stretch xl:flex-shrink-0 overflow-hidden rounded-lg no-underline">
                <img data-layer="Image" class="block h-44 w-full object-cover md:h-48 xl:h-full xl:min-h-[176px]" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </a>
        <?php else : ?>
            <div class="block w-full xl:w-40 xl:self-stretch xl:flex-shrink-0 overflow-hidden rounded-lg">
                <img data-layer="Image" class="block h-44 w-full object-cover md:h-48 xl:h-full xl:min-h-[176px]" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div data-layer="Content" class="Content flex flex-1 flex-col justify-center items-start gap-5 self-stretch">


        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
            <div data-layer="content" class="Content self-stretch flex flex-col justify-start items-start gap-2">
                <div data-layer="Content" class="Content self-stretch flex flex-wrap justify-start items-center gap-2.5">
                    <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-neutral-800 rounded flex justify-center items-center gap-2.5 no-underline">
                        <div data-layer="Podcast" class="Podcast justify-start text-white text-xs font-normal leading-4"><?php echo esc_html($type_label); ?></div>
                    </a>

					<div data-layer="Tag" class="Tag tag-dot"></div>
					<div data-layer="Date" class="Jan2026 justify-start text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($date); ?></div>
                    <div data-layer="Tag" class="Tag tag-dot"></div>
                    <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                        <?php echo reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-60', ['aria-hidden' => 'true']); ?>
                        <div data-layer="Duration" class="27 justify-start text-neutral-600 text-xs font-normal"><?php echo esc_html($duration); ?></div>
                    </div>
                </div>
                <?php if ($link_url) : ?>
                    <a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="TheDigitalDivideAccessAndEquity self-stretch reci-side-listing-title line-clamp-3 no-underline"><?php echo esc_html($title); ?></a>
                <?php else : ?>
                    <div data-layer="Title" class="TheDigitalDivideAccessAndEquity self-stretch reci-side-listing-title line-clamp-3"><?php echo esc_html($title); ?></div>
                <?php endif; ?>
                <?php if ($author_name !== '') : ?>
                    <p class="text-neutral-600 text-xs font-normal">By
                        <?php if ($author_url !== '') : ?>
                            <a href="<?php echo esc_url($author_url); ?>" class="text-neutral-600 hover:underline"><?php echo esc_html($author_name); ?></a>
                        <?php else : ?>
                            <?php echo esc_html($author_name); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (!empty($sphere_terms) || $show_name !== '') : ?>
                <div class="self-stretch inline-flex flex-wrap justify-start items-center gap-2 overflow-hidden">
                    <?php if ($show_name !== '') : ?>
                        <a href="<?php echo esc_url($show_url); ?>" class="tag bg-neutral-100 text-neutral-800 border-neutral-300"><?php echo esc_html($show_name); ?></a>
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

            <?php if (!empty($args['audio_url'])) : ?>
                <div data-layer="Play" class="Play self-stretch inline-flex justify-start items-center gap-2.5">
                    <div data-layer="play bg" class="PlayBg p-1 bg-amber-400 rounded-3xl flex justify-start items-center gap-2.5">
                        <?php echo reci_inline_svg('assets/icons/play.svg', 'w-4 h-4 flex-shrink-0 brightness-0 invert', ['aria-hidden' => 'true']); ?>
                    </div>
                    <div data-layer="Tags" class="Tags flex-1 bg-zinc-400 flex justify-start items-center gap-2">
                        <div data-layer="Tag" class="Tag h-0.5 bg-neutral-800 rounded" style="width: <?php echo esc_attr((string) $progress_percent); ?>%;"></div>
                    </div>
                    <div data-layer="Duration" class="27 justify-start text-neutral-600 text-xs font-normal"><?php echo esc_html($duration); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
