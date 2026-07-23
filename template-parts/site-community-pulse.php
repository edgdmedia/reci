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

$testimonials_query = RECI_Post_Query_Service::query([
	'post_type'      => 'reci_testimonial',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'orderby'        => 'rand',
	'no_found_rows'  => true,
]);

if (! empty($testimonials_query->posts)) {
	$community_slides = [];
	foreach ($testimonials_query->posts as $post) {
		$post_id = (int) $post->ID;
		$text    = (string) get_post_meta($post_id, '_reci_testimonial_text', true);
		$name    = (string) get_post_meta($post_id, '_reci_testimonial_full_name', true);
		$role    = (string) get_post_meta($post_id, '_reci_testimonial_role', true);
		$org     = (string) get_post_meta($post_id, '_reci_testimonial_organization', true);
		$image   = get_the_post_thumbnail_url($post_id, 'thumbnail') ?: '';

		if (empty($text)) continue;

		$community_slides[] = [
			'quote'        => $text,
			'author_name'  => $name ?: get_the_title($post_id),
			'author_role'  => $role,
			'author_org'   => $org,
			'author_image' => $image ?: $placeholder_avatar,
			'author_alt'   => $name ?: 'Testimonial',
		];
	}
}
?>
<section class="w-full bg-neutral-800">
	<div class="reci-container py-12 lg:py-24">
		<?php get_template_part('template-parts/common/community-section', null, [
			'title' => 'Community Pulse',
			'slides' => $community_slides,
		]); ?>
	</div>
</section>
