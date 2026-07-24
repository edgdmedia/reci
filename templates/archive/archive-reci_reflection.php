<?php

/**
 * Native archive route for reci_reflection.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$page_title    = 'Reflection Gallery';
$page_subtitle = "We're eager to see how these reflections can fuel conversations and positive change! We hope you enjoy!";

$placeholder_avatar = 'https://placehold.co/60x60';

$get_post_image = static function (int $post_id, string $size = 'large', string $fallback = ''): string {
	$image = get_the_post_thumbnail_url($post_id, $size);
	if (! $image) {
		return $fallback;
	}
	return $image;
};

$reflection_posts = get_posts(
	[
		'post_type'      => 'reci_reflection',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);

$resource_links = [
	[
		'label' => 'Cognitive-Behavioral Techniques for Racial Equity Consciousness Development',
		'url'   => '#',
	],
	[
		'label' => 'Strategies For Developing Racial Equity Consciousness',
		'url'   => '#',
	],
	[
		'label' => 'Racial Equity Areas of Opportunity',
		'url'   => '#',
	],
];

$collage_size_classes = [
	'w-36 h-36',
	'w-64 h-64',
	'w-36 h-36',
	'w-64 h-64',
	'w-36 h-36',
	'w-64 h-64',
	'w-36 h-36',
];


// Quotes of the day — all quotes for carousel.
$quote_items = [
	[
		'quote'  => 'Racial equity work is built through consistent reflection, learning, and action.',
		'author' => '– By RECI',
	],
];
$community_slides = [
	[
		'quote'        => 'RECI has truly transformed how we approach racial equity learning.',
		'author_name'  => 'Community Member',
		'author_role'  => 'RECI Contributor',
		'author_image' => $placeholder_avatar,
		'author_alt'   => 'Community Member',
	],
];

$quote_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_quote',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'rand',
	]
);
if (! empty($quote_posts)) {
	$quote_items = [];
	foreach ($quote_posts as $post) {
		$post_id    = (int) $post->ID;
		$quote_text = (string) get_post_meta($post_id, '_reci_quote_text', true);
		$author     = reci_get_quote_author_data($post_id);
		$text       = $quote_text ?: wp_trim_words(wp_strip_all_tags($post->post_content), 38, '...');
		$name       = $author['name'] ?: get_the_title($post_id);

		if (count($quote_items) < 4) {
			$quote_items[] = [
				'quote'        => $text,
				'author'       => '– By ' . $name,
				'author_role'  => $author['title'] ?: '',
				'author_image' => $author['image_url'],
			];
		}
	}
	if (empty($quote_items)) {
		$quote_items = [['quote' => 'Racial equity work is built through consistent reflection, learning, and action.', 'author' => '– By RECI', 'author_role' => '']];
	}
}

$testimonial_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'rand',
	]
);
$community_slides = [];
if (! empty($testimonial_posts)) {
	foreach ($testimonial_posts as $post) {
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
if (empty($community_slides)) {
	$community_slides = [['quote' => 'RECI has truly transformed how we approach racial equity learning.', 'author_name' => 'Community Member', 'author_role' => 'RECI Contributor', 'author_image' => $placeholder_avatar, 'author_alt' => 'Community Member']];
}
$quote_count     = count($quote_items);
$community_count = count($community_slides);

get_header();
?>

<div class="layout-page">

	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $page_subtitle,
	]); ?>

	<section class="hidden reci-container  w-full mx-auto px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex flex-wrap justify-center items-center gap-6 xl:gap-10">
		<?php foreach (array_slice($reflection_posts, 1, count($collage_size_classes)) as $index => $reflection_post) : ?>
			<?php
			$collage_id    = (int) $reflection_post->ID;
			$collage_image = get_the_post_thumbnail_url($collage_id, 'medium_large') ?: 'https://placehold.co/250x250';
			$collage_alt   = get_post_meta((int) get_post_thumbnail_id($collage_id), '_wp_attachment_image_alt', true);
			$collage_alt   = $collage_alt !== '' ? $collage_alt : get_the_title($collage_id);
			?>
			<img
				class="<?php echo esc_attr($collage_size_classes[$index]); ?> rounded-lg object-cover"
				src="<?php echo esc_url($collage_image); ?>"
				alt="<?php echo esc_attr($collage_alt); ?>" />
		<?php endforeach; ?>
	</section>

	<section class="w-full mx-auto bg-neutral-800">
		<div class="px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex justify-start items-center gap-10">
			<div class="flex-1 inline-flex flex-col justify-start items-start gap-10">
				<div class="self-stretch justify-start text-white text-base font-normal leading-6 ">
					Dive into the Racial Equity Consciousness Institute's Virtual Reflection Gallery - a unique space we've created for you to connect with the stories and perspectives of civil rights leaders and activists. This gallery, born as an extension of the RECI modules, is all about sparking deep reflection and inspiring action in our community. As you explore the artwork, reflect and record what resonates with you (you can journal, take notes in your phone etc!)
					<br /><br />
					As you navigate the gallery below, consider leveraging the resources linked below to support building your consciousness toward racial equity:
				</div>
				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<?php foreach ($resource_links as $resource_link) : ?>
						<div class="self-stretch inline-flex justify-start items-center gap-3">
							<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
							<a href="<?php echo esc_url($resource_link['url']); ?>" class="justify-start text-white text-base font-normal underline leading-6 ">
								<?php echo esc_html($resource_link['label']); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="self-stretch justify-start text-white text-base font-normal leading-6">
					We also encourage you to take photos of the reflection gallery and share on social media! You can tag us on Instagram <a href="https://www.instagram.com/PITTCRSP" class="text-white underline">@PITTCRSP</a> and use our event hashtag: <span class="font-semibold">#TheRECIMovie</span>
				</div>
			</div>
		</div>
	</section>

	<section class=" reci-container w-full mx-auto px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex flex-col justify-start items-start gap-10">
		<?php if (! empty($reflection_posts)) : ?>
			<?php foreach (array_chunk($reflection_posts, 3) as $row_posts) : ?>
				<div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-10">
					<?php foreach ($row_posts as $reflection_post) : ?>
						<?php
						$post_id      = (int) $reflection_post->ID;
						$image_url    = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/387x300';
						$image_alt    = get_post_meta((int) get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
						$image_alt    = $image_alt !== '' ? $image_alt : get_the_title($post_id);
						$description  = has_excerpt($post_id) ? get_the_excerpt($post_id) : get_the_title($post_id);
						$description  = wp_trim_words($description, 24, '...');
						?>
						<article class="flex-1 self-stretch inline-flex flex-col justify-start items-start gap-5">
							<a href="<?php echo esc_url(get_permalink($post_id)); ?>" target="_blank" rel="noopener noreferrer" class="w-full inline-flex flex-col justify-start items-start gap-5 no-underline">
								<img class="self-stretch h-72 rounded-lg object-cover" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
								<div class="self-stretch flex flex-col justify-start items-start gap-3">
									<div class="self-stretch reci-side-listing-title">
										<?php echo esc_html(get_the_title($post_id)); ?>
									</div>
									<div class="self-stretch justify-start text-neutral-800 text-base font-normal leading-6  line-clamp-3">
										<?php echo esc_html($description); ?>
									</div>
								</div>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="self-stretch justify-start text-neutral-800 text-xl font-normal leading-8 ">
				No reflections published yet.
			</div>
		<?php endif; ?>
	</section>

	<?php get_template_part('template-parts/common/community-engagement-section', null, [
		'enabled'      => true,
		'show_join'    => true,
		'show_pulse'   => false,
		'pulse_slides' => $community_slides,
	]); ?>
</div>

<?php get_footer(); ?>
