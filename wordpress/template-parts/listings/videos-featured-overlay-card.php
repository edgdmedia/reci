<?php
/**
 * Videos section featured overlay card (from herov3.php lines 589-626).
 *
 * @var array $args
 */

$type_label   = $args['type_label'] ?? 'Video';
$date         = $args['date'] ?? '';
$meta_value   = $args['meta_value'] ?? '';
$title        = $args['title'] ?? '';
$excerpt      = $args['excerpt'] ?? '';
$tags         = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$bg_image_url = $args['bg_image_url'] ?? '';
$link_url     = $args['link_url'] ?? '';

$type_key    = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => get_post_type_archive_link('reci_article') ?: home_url('/articles/'),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
?>

<div data-layer="Video" class="Video w-full lg:max-w-[710px] min-h-[420px] lg:min-h-[700px] p-6 lg:p-10 relative bg-black/40 rounded-lg inline-flex flex-col justify-start items-start gap-2.5 overflow-hidden"<?php if ($bg_image_url) : ?> style="background-image: url('<?php echo esc_url($bg_image_url); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
    <div data-layer="Content" class="Content self-stretch flex-1 flex flex-col justify-end items-start gap-5">
        <div data-layer="Content" class="Content inline-flex justify-start items-center gap-2.5">
            <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 bg-blue-900 rounded flex justify-center items-center gap-2.5 no-underline">
                <div data-layer="Video" class="Video justify-start text-white text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($type_label); ?></div>
            </a>
            <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
            <div data-layer="Date" class="Jan2026 justify-start text-white text-base font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($date); ?></div>
            <div data-layer="Tag" class="Tag w-2 h-2 px-1 py-1 bg-amber-400 rounded-sm"></div>
            <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                <div data-layer="play" class="Play w-3.5 h-3.5 relative overflow-hidden">
                    <div data-layer="Vector" class="Vector w-1.5 h-2 left-[4.67px] top-[3px] absolute bg-white"></div>
                </div>
                <div data-layer="Duration" class="27 justify-start text-white text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html($meta_value); ?></div>
            </div>
        </div>
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="ThePowerOfCollectiveAction self-stretch justify-start text-white text-5xl font-bold font-['EB_Garamond'] leading-[50.40px] no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="Title" class="ThePowerOfCollectiveAction self-stretch justify-start text-white text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-10">
                <div data-layer="Excerpt" class="ExploringTheTransformativePotentialOfIntergroupDialogueInFosteringUnderstandingAndResolvingConflicts self-stretch justify-start text-white text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight"><?php echo esc_html($excerpt); ?></div>
                <?php if (!empty($tags)) : ?>
                    <div data-layer="Tags" class="Tags inline-flex justify-start items-center gap-2">
                        <?php foreach ($tags as $tag) : ?>
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
            </div>
        </div>
    </div>
    <div data-layer="play" class="Play hidden lg:block w-20 h-20 left-[315px] top-[310px] absolute opacity-0 overflow-hidden">
        <div data-layer="Vector" class="Vector w-9 h-12 left-[26.67px] top-[17.13px] absolute bg-white"></div>
    </div>
</div>
