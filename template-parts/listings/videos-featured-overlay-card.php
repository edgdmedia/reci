<?php

/**
 * Videos section featured overlay card.
 *
 * @var array $args
 */

$type_label   = $args['type_label'] ?? 'Video';
$date         = $args['date'] ?? '';
$meta_value   = $args['meta_value'] ?? '';
$title        = $args['title'] ?? '';
$excerpt      = $args['excerpt'] ?? '';
$tags         = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$sphere_terms = is_array($args['sphere_terms'] ?? null) ? $args['sphere_terms'] : [];
$sdg_terms    = is_array($args['sdg_terms'] ?? null) ? $args['sdg_terms'] : [];
$bg_image_url = $args['bg_image_url'] ?? '';
$link_url     = $args['link_url'] ?? '';
$category     = $args['category'] ?? '';

$type_key    = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => (get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/')),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
?>

<div data-layer="Video" class="Video w-full lg:max-w-[710px] min-h-[420px] lg:min-h-[700px] p-6 lg:p-10 relative rounded-lg inline-flex flex-col justify-start items-start gap-2.5 overflow-hidden" <?php if ($bg_image_url) : ?> style="background-image: url('<?php echo esc_url($bg_image_url); ?>'); background-size: cover; background-position: center;" <?php endif; ?>>

    <div data-layer="Overlay" class="Overlay absolute z-0 inset-0 bg-gradient-to-b from-black/5 via-black/80 to-black/90"></div>

    <div data-layer="Content" class="Content relative z-10 self-stretch flex-1 flex flex-col justify-end items-start gap-5">

        <div data-layer="Content" class="Content inline-flex justify-start items-center gap-2.5">
            <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-blue-900 rounded flex justify-center items-center gap-2.5 no-underline">
                <div data-layer="Video" class="Video justify-start text-white text-sm font-normal leading-4"><?php echo esc_html($type_label); ?></div>
            </a>
            <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
            <div data-layer="Date" class="Jan2026 justify-start text-white text-base font-normal leading-4"><?php echo esc_html($date); ?></div>
            <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
            <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                <div data-layer="play" class="Play w-3.5 h-3.5 relative overflow-hidden">
                    <div data-layer="Vector" class="Vector w-1.5 h-2 left-[4.67px] top-[3px] absolute bg-white"></div>
                </div>
                <div data-layer="Duration" class="27 justify-start text-white text-sm font-normal"><?php echo esc_html($meta_value); ?></div>
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
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-10">
                <div data-layer="Excerpt" class="ExploringTheTransformativePotentialOfIntergroupDialogueInFosteringUnderstandingAndResolvingConflicts self-stretch justify-start text-white text-lg font-normal leading-7 "><?php echo esc_html($excerpt); ?></div>
                <?php if (!empty($sdg_terms) || !empty($sphere_terms)) : ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div data-layer="play" class="Play hidden lg:block w-20 h-20 left-[315px] top-[310px] absolute z-10 opacity-0 overflow-hidden">
        <div data-layer="Vector" class="Vector w-9 h-12 left-[26.67px] top-[17.13px] absolute bg-white"></div>
    </div>

</div>
