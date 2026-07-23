<?php

/**
 * Reusable community section.
 *
 * Args:
 * - title: string
 * - slides: array[]
 * - wrapper_class: string
 * - content_class: string
 * - quote_icon_class: string
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$placeholder_avatar = 'https://placehold.co/60x60';
$title              = isset($args['title']) ? (string) $args['title'] : 'Community Pulse';
$wrapper_class      = isset($args['wrapper_class']) ? (string) $args['wrapper_class'] : '';
$content_class      = isset($args['content_class']) ? (string) $args['content_class'] : '';
$quote_icon_class   = isset($args['quote_icon_class']) ? (string) $args['quote_icon_class'] : 'hidden lg:block pt-12 pr-10';
$community_slides   = isset($args['slides']) && is_array($args['slides']) ? $args['slides'] : [
	[
		'quote'        => 'RECI has truly transformed how we approach racial equity learning.',
		'author_name'  => 'Community Member',
		'author_role'  => 'RECI Contributor',
		'author_image' => $placeholder_avatar,
		'author_alt'   => 'Community Member',
	],
];

?>
<div class="self-stretch p-6 lg:p-14 relative bg-neutral-600 rounded-lg flex flex-col xl:flex-row justify-start items-start gap-8 xl:gap-28 <?php echo esc_attr($wrapper_class); ?>" data-carousel="community">
	<div class="inline-flex flex-col justify-start items-start gap-2.5 pt-6 <?php echo esc_attr($content_class); ?>">
		<div data-layer="Community Pulse" class="text-white reci-section-title font-medium"><?php echo esc_html($title); ?></div>
	</div>
	<div class="flex flex-1">
		<div data-layer="format-quote-open" class="<?php echo esc_attr($quote_icon_class); ?>">
			<?php echo reci_inline_svg('assets/icons/quote.svg', 'w-[47px] h-[34px]', ['aria-hidden' => 'true']); ?>
		</div>
		<div class="flex-1">
			<?php foreach ($community_slides as $i => $slide) : ?>
				<div data-carousel-item <?php if ($i > 0) echo 'class="hidden"'; ?>>
					<?php get_template_part('template-parts/listings/community-pulse-slide', null, $slide); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
