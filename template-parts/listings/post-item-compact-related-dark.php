<?php

/**
 * Compact no-image related article item for dark sidebars.
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
$link_url        = $args['link_url'] ?? '';
$category        = $args['category'] ?? '';

$type_key = strtolower(trim((string) $type_label));
$type_archives = [
	'article' => (get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/')),
	'podcast' => get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/'),
	'video'   => get_post_type_archive_link('reci_video') ?: home_url('/videos/'),
];
$type_archive_url = $type_archives[$type_key] ?? '#';
$icon_markup = '';
if ($meta_icon === 'audio') {
	$icon_markup = reci_inline_svg('assets/icons/headphones.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-40 invert', ['aria-hidden' => 'true']);
} elseif ($meta_icon === 'video') {
	$icon_markup = reci_inline_svg('assets/icons/play.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-40 invert', ['aria-hidden' => 'true']);
} else {
	$icon_markup = reci_inline_svg('assets/icons/timer-outline.svg', 'w-3.5 h-3.5 flex-shrink-0 opacity-40 invert', ['aria-hidden' => 'true']);
}
?>

<div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
	<div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
		<div data-layer="content" class="Content self-stretch flex flex-col justify-start items-start gap-2">
			<div data-layer="Content" class="Content self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap content-center">
				<a href="<?php echo esc_url($type_archive_url); ?>" data-layer="Tag" class="Tag px-2 py-1 <?php echo esc_attr($type_badge); ?> rounded flex justify-center items-center gap-2.5 no-underline">
					<div data-layer="<?php echo esc_attr($type_label); ?>" class="justify-start <?php echo esc_attr($type_text); ?> text-xs font-normal leading-4"><?php echo esc_html($type_label); ?></div>
				</a>
				<div data-layer="Date" class="Date flex justify-start items-center gap-2.5">
					<div data-layer="Tag" class="Tag tag-dot"></div>
					<div data-layer="Date" class="justify-start text-zinc-300 text-xs font-normal leading-4"><?php echo esc_html($date); ?></div>
				</div>
				<div data-layer="Content" class="Content flex justify-start items-center gap-2.5">
					<div data-layer="Tag" class="Tag tag-dot"></div>
					<div data-layer="Nav Text" class="NavText flex justify-start items-center gap-0.5">
						<?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div data-layer="meta" class="justify-start text-zinc-300 text-xs font-normal"><?php echo esc_html($meta_value); ?></div>
					</div>
				</div>
			</div>
			<?php if ($link_url) : ?>
				<a href="<?php echo esc_url($link_url); ?>" data-layer="Title" class="self-stretch reci-side-listing-title-dark line-clamp-2 no-underline"><?php echo esc_html($title); ?></a>
			<?php else : ?>
				<div data-layer="Title" class="self-stretch reci-side-listing-title-dark line-clamp-2"><?php echo esc_html($title); ?></div>
			<?php endif; ?>
			<?php if ($author_name !== '') : ?>
				<p class="text-zinc-300 text-xs font-normal">By
					<?php if ($author_url !== '') : ?>
						<a href="<?php echo esc_url($author_url); ?>" class="text-white hover:underline"><?php echo esc_html($author_name); ?></a>
					<?php else : ?>
						<span class="text-white"><?php echo esc_html($author_name); ?></span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
	<?php if ($category !== '' || !empty($sphere_terms)) : ?>
		<div class="self-stretch inline-flex justify-start items-center gap-2 flex-wrap">
			<?php if ($category !== '') : ?>
				<a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="px-2 py-1 bg-white/10 text-white rounded text-xs font-medium no-underline hover:bg-white/20 transition-colors"><?php echo esc_html($category); ?></a>
			<?php endif; ?>
			<?php if (!empty($sphere_terms)) :
				$s = $sphere_terms[0]; ?>
				<a href="<?php echo esc_url($s['url']); ?>" class="px-2 py-1 bg-white/10 text-white rounded text-xs font-medium inline-flex items-center gap-1.5 no-underline hover:bg-white/20 transition-colors">
					<span class="rounded-full w-2 h-2 flex-shrink-0" style="background-color: <?php echo esc_attr($s['color']); ?>;"></span>
					<span><?php echo esc_html($s['name']); ?></span>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
