<?php
/**
 * Shared Community Pulse block.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$placeholder_avatar = 'https://placehold.co/60x60';
$community_slides   = [[
	'quote'        => 'RECI has truly transformed how we approach racial equity learning.',
	'author_name'  => 'Community Member',
	'author_role'  => 'RECI Contributor',
	'author_image' => $placeholder_avatar,
	'author_alt'   => 'Community Member',
]];

$quotes_query = RECI_Post_Query_Service::query([
	'post_type'      => 'reci_quote',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
]);

if (! empty($quotes_query->posts)) {
	$community_slides = [];
	foreach ($quotes_query->posts as $post) {
		$post_id     = (int) $post->ID;
		$quote_text  = (string) get_post_meta($post_id, '_reci_quote_text', true);
		$author_name = (string) get_post_meta($post_id, '_reci_quote_author_name', true);
		$author_role = (string) get_post_meta($post_id, '_reci_quote_author_title', true);
		$author_img  = (string) get_post_meta($post_id, '_reci_quote_author_image_url', true);
		$text        = $quote_text ?: wp_trim_words(wp_strip_all_tags($post->post_content), 28, '...');
		$name        = $author_name ?: get_the_title($post_id);
		$community_slides[] = [
			'quote'        => $text,
			'author_name'  => $name,
			'author_role'  => $author_role ?: 'RECI Contributor',
			'author_image' => $author_img ?: $placeholder_avatar,
			'author_alt'   => $name,
		];
	}
}
?>
<section class="w-full bg-neutral-800">
	<div class="reci-container py-12 lg:py-24">
		<div class="self-stretch rounded-lg bg-neutral-600 p-6 lg:p-14 relative flex flex-col xl:flex-row justify-start items-start gap-8 xl:gap-28" data-carousel="community">
			<div class="inline-flex flex-col justify-start items-start gap-2.5">
				<div class="w-24 h-6"></div>
				<div class="CommunityPulse w-56 justify-start text-white text-3xl sm:text-4xl lg:text-5xl font-medium font-['EB_Garamond'] leading-tight lg:leading-[50.40px]">Community Pulse</div>
			</div>
			<div class="hidden lg:block w-20 h-20 absolute left-[307px] top-[87px] overflow-hidden" aria-hidden="true">
				<svg class="w-full h-full" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M16 42.667C16 30.885 23.611 21.333 35.2 21.333v12.8c-4.441 0-8 3.858-8 8.534V56h-11.2V42.667Zm28.8 0c0-11.782 7.611-21.334 19.2-21.334v12.8c-4.441 0-8 3.858-8 8.534V56H44.8V42.667Z" fill="#FFB81C"/>
				</svg>
			</div>
			<?php foreach ($community_slides as $i => $slide) : ?>
				<div data-carousel-item <?php if ($i > 0) echo 'class="hidden"'; ?>>
					<?php get_template_part('template-parts/listings/community-pulse-slide', null, $slide); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
