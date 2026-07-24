<?php
/**
 * Template Name: Reflection Archive
 *
 * Reflection archive using the original gallery Figma layout with live reflection posts.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

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

$connect_image = trailingslashit(get_template_directory_uri()) . 'assets/images/connect-now3.png';

$community_post = get_posts(
	[
		'post_type'      => 'reci_quote',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);

$community_quote       = $community_post ? $community_post[0] : null;
$community_quote_id    = $community_quote instanceof WP_Post ? (int) $community_quote->ID : 0;
$community_quote_text  = $community_quote_id ? (string) get_post_meta($community_quote_id, '_reci_quote_text', true) : '';
$community_author_name = $community_quote_id ? (string) get_post_meta($community_quote_id, '_reci_quote_author_name', true) : '';
$community_author_role = $community_quote_id ? (string) get_post_meta($community_quote_id, '_reci_quote_author_title', true) : '';
$community_author_img  = $community_quote_id ? (string) get_post_meta($community_quote_id, '_reci_quote_author_image_url', true) : '';

if ($community_quote instanceof WP_Post) {
	$community_quote_text  = $community_quote_text !== '' ? $community_quote_text : wp_trim_words(wp_strip_all_tags((string) $community_quote->post_content), 28, '...');
	$community_author_name = $community_author_name !== '' ? $community_author_name : get_the_title($community_quote_id);
}

$community_quote_text  = $community_quote_text !== '' ? $community_quote_text : 'RECI has truly transformed how we approach market analysis. The insights are unparalleled!.';
$community_author_name = $community_author_name !== '' ? $community_author_name : 'Jane Doe';
$community_author_role = $community_author_role !== '' ? $community_author_role : 'Financial Analyst';
$community_author_img  = $community_author_img !== '' ? $community_author_img : 'https://placehold.co/60x60';

get_header();
?>

<div class="bg-slate-100 min-h-screen overflow-hidden">
	<section class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 xl:px-24 py-14 border-b border-zinc-400">
		<div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 xl:gap-10">
			<div class="flex justify-start items-center gap-3">
				<div class="w-3 h-3 bg-amber-400 rounded-sm"></div>
				<h1 class="justify-start text-neutral-800 text-5xl font-medium font-['EB_Garamond'] leading-[50.40px]">
					RECI Reflection gallery
				</h1>
			</div>
			<div class="xl:pl-10 xl:border-l border-zinc-400 flex justify-center items-center gap-2.5">
				<div class="max-w-[556px] justify-start text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight">
					We're eager to see how these reflections can fuel conversations and positive change! We hope you enjoy!
				</div>
			</div>
		</div>
	</section>

	<section class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex flex-wrap justify-center items-center gap-6 xl:gap-10">
		<?php foreach (array_slice($reflection_posts, 0, count($collage_size_classes)) as $index => $reflection_post) : ?>
			<?php
			$collage_id    = (int) $reflection_post->ID;
			$collage_image = get_the_post_thumbnail_url($collage_id, 'medium_large') ?: 'https://placehold.co/250x250';
			$collage_alt   = get_post_meta((int) get_post_thumbnail_id($collage_id), '_wp_attachment_image_alt', true);
			$collage_alt   = $collage_alt !== '' ? $collage_alt : get_the_title($collage_id);
			?>
			<img
				class="<?php echo esc_attr($collage_size_classes[$index]); ?> rounded-lg object-cover"
				src="<?php echo esc_url($collage_image); ?>"
				alt="<?php echo esc_attr($collage_alt); ?>"
			/>
		<?php endforeach; ?>
	</section>

	<section class="max-w-[1440px] w-full mx-auto bg-neutral-800">
		<div class="px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex justify-start items-center gap-10">
			<div class="flex-1 inline-flex flex-col justify-start items-start gap-10">
				<div class="self-stretch justify-start text-white text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
					Dive into the Racial Equity Consciousness Institute's Virtual Reflection Gallery - a unique space we've created for you to connect with the stories and perspectives of civil rights leaders and activists. This gallery, born as an extension of the RECI modules, is all about sparking deep reflection and inspiring action in our community. As you explore the artwork, reflect and record what resonates with you (you can journal, take notes in your phone etc!)
					<br /><br />
					As you navigate the gallery below, consider leveraging the resources linked below to support building your consciousness toward racial equity:
				</div>
				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<?php foreach ($resource_links as $resource_link) : ?>
						<div class="self-stretch inline-flex justify-start items-center gap-3">
							<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
							<a href="<?php echo esc_url($resource_link['url']); ?>" class="justify-start text-white text-base font-normal font-['SF_Pro_Display'] underline leading-6 tracking-tight">
								<?php echo esc_html($resource_link['label']); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<section class="max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex flex-col justify-start items-start gap-10">
		<?php if (! empty($reflection_posts)) : ?>
			<?php foreach (array_chunk($reflection_posts, 3) as $row_posts) : ?>
				<div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-10">
					<?php foreach ($row_posts as $reflection_post) : ?>
						<?php
						$post_id      = (int) $reflection_post->ID;
						$image_url    = get_the_post_thumbnail_url($post_id, 'large') ?: 'https://placehold.co/387x300';
						$image_alt    = get_post_meta((int) get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
						$image_alt    = $image_alt !== '' ? $image_alt : get_the_title($post_id);
						$quote        = (string) get_post_meta($post_id, '_reci_reflection_quote', true);
						$description  = $quote !== '' ? $quote : (has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags((string) $reflection_post->post_content), 24, '...'));
						$description  = wp_trim_words($description, 24, '...');
						?>
						<article class="flex-1 self-stretch inline-flex flex-col justify-start items-start gap-5">
							<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="w-full inline-flex flex-col justify-start items-start gap-5 no-underline">
								<img class="self-stretch h-72 rounded-lg object-cover" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
								<div class="self-stretch flex flex-col justify-start items-start gap-3">
									<div class="self-stretch justify-start text-neutral-800 text-xl font-bold font-['EB_Garamond'] leading-6">
										<?php echo esc_html(get_the_title($post_id)); ?>
									</div>
									<div class="self-stretch justify-start text-neutral-800 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight line-clamp-3">
										<?php echo esc_html($description); ?>
									</div>
								</div>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="self-stretch justify-start text-neutral-800 text-xl font-normal font-['SF_Pro_Display'] leading-8 tracking-tight">
				No reflections published yet.
			</div>
		<?php endif; ?>
	</section>

	<section class="max-w-[1440px] w-full mx-auto bg-neutral-800 px-4 sm:px-6 lg:px-12 xl:px-24 py-12 xl:py-24 flex flex-col justify-start items-start gap-16 xl:gap-24">
		<div class="self-stretch flex flex-col xl:flex-row justify-start items-start gap-10">
			<img class="flex-1 self-stretch rounded-lg border-b-[11px] border-amber-400 object-cover" src="<?php echo esc_url($connect_image); ?>" alt="" aria-hidden="true" />
			<div class="inline-flex flex-col justify-start items-start gap-10">
				<div class="flex flex-col justify-start items-start gap-2.5">
					<div class="px-2 py-1 bg-amber-400 rounded inline-flex justify-center items-center gap-2.5">
						<div class="justify-start text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4">Connect Elements</div>
					</div>
					<div class="max-w-[785px] justify-start text-white text-4xl xl:text-6xl font-semibold font-['EB_Garamond'] leading-tight xl:leading-[74.40px]">
						Connect your interests to improve your feed and discover more relevant content
					</div>
				</div>
				<a href="<?php echo esc_url(is_user_logged_in() ? home_url('/my-account/') : home_url('/sign-up/')); ?>" class="min-w-28 px-7 py-3.5 bg-amber-400 rounded-lg inline-flex justify-center items-center gap-2 overflow-hidden text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6 no-underline">
					Connect Now
				</a>
			</div>
		</div>

		<div class="self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>

		<div class="self-stretch p-6 lg:p-10 xl:p-14 relative bg-neutral-600 rounded-lg flex flex-col xl:flex-row justify-start items-start gap-10 xl:gap-28">
			<div class="inline-flex flex-col justify-start items-start gap-2.5">
				<div class="w-24 h-6"></div>
				<div class="w-56 justify-start text-white text-5xl font-medium font-['EB_Garamond'] leading-[50.40px]">Community Pulse</div>
			</div>
			<div class="hidden xl:block w-20 h-20 left-[307px] top-[87px] absolute overflow-hidden" aria-hidden="true">
				<div class="w-12 h-8 left-[16.67px] top-[23.33px] absolute bg-amber-400"></div>
			</div>
			<div class="flex-1 py-4 xl:py-10 inline-flex flex-col justify-start items-start gap-10">
				<div class="self-stretch justify-start text-white text-2xl font-normal font-['EB_Garamond'] leading-10 tracking-tight">
					<?php echo esc_html($community_quote_text); ?>
				</div>
				<div class="self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
				<div class="self-stretch inline-flex justify-between items-center gap-6">
					<div class="flex justify-start items-center gap-2.5">
						<img class="w-14 h-14 rounded-full outline outline-4 outline-white object-cover" src="<?php echo esc_url($community_author_img); ?>" alt="<?php echo esc_attr($community_author_name); ?>" />
						<div class="inline-flex flex-col justify-start items-start gap-1">
							<div class="w-48 justify-start text-white text-xl font-bold font-['SF_Pro_Display'] leading-8 tracking-tight">
								<?php echo esc_html($community_author_name); ?>
							</div>
							<div class="w-48 justify-start text-zinc-400 text-base font-medium font-['SF_Pro_Display'] leading-6 tracking-tight">
								<?php echo esc_html($community_author_role); ?>
							</div>
						</div>
					</div>
					<div class="hidden md:flex justify-start items-center gap-3">
						<div class="p-4 rounded-lg outline outline-[0.50px] outline-offset-[-0.50px] outline-zinc-400 flex justify-center items-center gap-2 overflow-hidden text-white text-sm font-medium">
							Prev
						</div>
						<div class="p-4 rounded-lg outline outline-[0.50px] outline-offset-[-0.50px] outline-zinc-400 flex justify-center items-center gap-2 overflow-hidden text-white text-sm font-medium">
							Next
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php get_footer(); ?>
