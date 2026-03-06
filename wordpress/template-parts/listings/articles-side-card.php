<?php
/**
 * Articles section right-rail card (from herov3.php lines 325-439 item pattern).
 *
 * @var array $args
 */

$type_label      = $args['type_label'] ?? 'Article';
$type_badge      = $args['type_badge_class'] ?? 'bg-amber-400';
$type_text       = $args['type_text_class'] ?? 'text-neutral-800';
$date            = $args['date'] ?? '';
$meta_value      = $args['meta_value'] ?? '';
$title           = $args['title'] ?? '';
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';
$link_url        = $args['link_url'] ?? '';

$type_key    = strtolower(trim((string) $type_label));
$type_archives = [
    'article' => get_post_type_archive_link('reci_article') ?: home_url('/articles/'),
    'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
    'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
?>

<div data-layer="Video card" class="VideoCard self-stretch h-96 flex flex-col justify-start items-start gap-5">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="self-stretch no-underline">
                <img data-layer="Image" class="Image self-stretch h-56 p-2.5 rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </a>
        <?php else : ?>
            <img data-layer="Image" class="Image self-stretch h-56 p-2.5 rounded-lg" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
        <?php endif; ?>
    <?php endif; ?>
    <div data-layer="Content" class="Content self-stretch flex-1 flex flex-col justify-between items-start">
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-3">
            <div data-layer="Content" class="Content self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap content-center">
                <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
                    <div data-layer="<?php echo esc_attr($type_label); ?>" class="justify-start <?php echo esc_attr($type_text); ?> text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($type_label); ?></div>
                </a>
                <div data-layer="Date" class="Date flex justify-start items-center gap-2.5">
                    <div data-layer="Tag" class="Tag tag-dot"></div>
                    <div data-layer="Date" class="Jan2026 justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($date); ?></div>
                </div>
                <div data-layer="Content" class="Content flex justify-start items-center gap-2.5">
                    <div data-layer="Tag" class="Tag tag-dot"></div>
                    <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/figma/assets/timer-outline.svg'); ?>" alt="" class="w-3.5 h-3.5 flex-shrink-0 opacity-60" aria-hidden="true" />
                        <div data-layer="Mins" class="Mins justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html($meta_value); ?></div>
                    </div>
                </div>
            </div>
            <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" data-layer="The Power of Collective Action" class="ThePowerOfCollectiveAction self-stretch justify-start text-neutral-800 text-xl font-bold font-['EB_Garamond'] leading-6 no-underline"><?php echo esc_html($title); ?></a>
            <?php else : ?>
                <div data-layer="The Power of Collective Action" class="ThePowerOfCollectiveAction self-stretch justify-start text-neutral-800 text-xl font-bold font-['EB_Garamond'] leading-6"><?php echo esc_html($title); ?></div>
            <?php endif; ?>
        </div>
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
