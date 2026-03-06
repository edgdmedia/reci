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
$tags            = is_array($args['tags'] ?? null) ? $args['tags'] : [];
$image_url       = $args['image_url'] ?? '';
$image_alt       = $args['image_alt'] ?? '';
$title_classes   = $args['title_classes'] ?? "self-stretch justify-start text-neutral-800 text-3xl lg:text-5xl font-bold font-['EB_Garamond'] leading-tight lg:leading-10";
$excerpt_classes = $args['excerpt_classes'] ?? "self-stretch justify-start text-neutral-500 text-base lg:text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight";
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

<div data-layer="Content" class="Content self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-14">
    <?php if ($image_url) : ?>
        <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="no-underline">
                <img data-layer="Image" class="Image w-full lg:w-96 h-60 p-2.5 rounded-lg object-cover flex-shrink-0" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
            </a>
        <?php else : ?>
            <img data-layer="Image" class="Image w-full lg:w-96 h-60 p-2.5 rounded-lg object-cover flex-shrink-0" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
        <?php endif; ?>
    <?php endif; ?>

    <div data-layer="Content" class="Content flex-1 lg:h-60 inline-flex flex-col justify-between items-start gap-4">
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-2">
            <div data-layer="Content" class="Content inline-flex justify-start items-center gap-2.5 flex-wrap">
                <a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
                    <div data-layer="<?php echo esc_attr($type_label); ?>" class="justify-start <?php echo esc_attr($type_text); ?> text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($type_label); ?></div>
                </a>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Date" class="Date justify-start text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($date); ?></div>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
                    <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div data-layer="Meta" class="Meta justify-start text-neutral-500 text-base font-normal font-['SF_Pro_Display']"><?php echo esc_html($meta_value); ?></div>
                </div>
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

        <?php if (! empty($tags)) : ?>
            <div data-layer="Tags" class="Tags inline-flex justify-start items-center gap-2 flex-wrap">
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
