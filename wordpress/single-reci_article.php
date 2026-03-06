<?php

/**
 * Single template for reci_article.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! have_posts()) {
	wp_safe_redirect(home_url('/'));
	exit;
}

the_post();

$post_id   = get_the_ID();
$icons_url = get_template_directory_uri() . '/figma/assets/';

$single_payload = RECI_Single_Post_Service::get_post_payload($post_id, [
	'subtitle_meta_key' => '_reci_article_subtitle',
	'image_size'        => 'large',
	'tag_limit'         => 3,
]);

$article_title       = (string) ($single_payload['title'] ?? get_the_title($post_id));
$article_date        = (string) ($single_payload['date'] ?? get_the_date('d M Y', $post_id));
$article_read_time   = (string) ($single_payload['meta_value'] ?? '3 mins');
$article_subtitle    = (string) ($single_payload['subtitle'] ?? '');
$article_tags        = is_array($single_payload['tags'] ?? null) ? $single_payload['tags'] : [];
$featured_image_url  = (string) ($single_payload['featured_image_url'] ?? 'https://placehold.co/800x446');
$featured_image_alt  = (string) ($single_payload['featured_image_alt'] ?? get_the_title($post_id));

$related_articles = RECI_Related_Posts_Service::get_related($post_id, [
	'post_type'   => 'reci_article',
	'limit'       => 4,
	'taxonomy'    => 'category',
	'format_args' => [
		'image_size'  => 'medium',
		'tag_limit'   => 3,
		'excerpt_words' => 16,
	],
]);

$read_also_result = RECI_Post_Query_Service::get_formatted_items([
	'post_type'      => ['reci_article', 'reci_video', 'reci_podcast'],
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'post__not_in'   => [$post_id],
	'orderby'        => 'date',
	'order'          => 'DESC',
	'no_found_rows'  => true,
], [
	'image_size'    => 'large',
	'tag_limit'     => 3,
	'excerpt_words' => 16,
	'overrides'     => [
		'title_classes' => "self-stretch justify-start text-neutral-800 text-[42px] font-bold font-['EB_Garamond'] leading-[46.20px]",
	],
]);
$read_also_items = $read_also_result['items'];

get_header();
?>

<main class="bg-slate-100 min-h-screen">
	<section class="reci-container mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">
		<!-- Main content + sidebar layout -->
		<div class="flex flex-col lg:flex-row justify-start items-start gap-0">
			<!-- ── LEFT: Primary episode content ───────────────────────────── -->
			<div class="w-full md:w-2/3 flex-shrink-0 py-14 px-10 flex flex-col justify-start items-start gap-10 border-x border-zinc-300">
				<!-- Meta + title -->
				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<!-- Meta row -->
					<div class="self-stretch flex flex-col justify-start items-start gap-2">
						<div class="self-stretch inline-flex justify-start items-center gap-2.5">
							<div class="px-2 py-1 bg-amber-400 rounded flex justify-center items-center gap-2.5">
								<span class="text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php esc_html_e('Article', 'reci-media-hub'); ?></span>
							</div>
							<div class="flex items-center gap-2.5">
								<span class="tag-dot"></span>
								<span class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($article_date); ?></span>
							</div>
							<div class="flex items-center gap-2.5">
								<div class="tag-dot"></div>
								<div class="flex items-center gap-0.5">
									<img src="<?php echo esc_url($icons_url . 'timer-outline.svg'); ?>" alt="" class="w-3.5 h-3.5 opacity-60" aria-hidden="true" />
									<div class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html($article_read_time); ?></div>
								</div>
							</div>
						</div>
						<div class="self-stretch flex flex-col gap-5">
							<h1 class="self-stretch text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-10"><?php echo esc_html($article_title); ?></h1>
							<?php if ($article_subtitle !== '') : ?>
								<p class="self-stretch text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight"><?php echo esc_html($article_subtitle); ?></p>
							<?php endif; ?>
						</div>
					</div>
					<?php if (! empty($article_tags)) : ?>
						<div class="inline-flex items-center gap-2 flex-wrap">
							<?php foreach ($article_tags as $tag) : ?>
								<div class="px-2 py-1 bg-gray-200 rounded flex items-center gap-2.5">
									<div class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($tag); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<img class="self-stretch p-2.5 rounded-lg" src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" />
			</div>
			<!-- /left primary content -->

			<!-- ── RIGHT: Related episodes sidebar ─────────────────────────── -->
			<?php if (! empty($related_articles)) : ?>
				<aside class="flex-1 lg:pl-5 lg:pt-14 border-l-[0.50px] border-zinc-400">
					<div class="bg-neutral-800 p-5 flex flex-col gap-10 overflow-hidden">
						<div class="self-stretch flex flex-col gap-5">
							<div class="inline-flex items-center gap-2">
								<div class="tag-dot"></div>
								<div class="text-white text-2xl font-bold font-['EB_Garamond'] leading-7">Related Article</div>
							</div>
							<div class="self-stretch h-0 border-t-2 border-white"></div>
						</div>
						<div class="self-stretch flex flex-col gap-10 overflow-hidden">
							<?php foreach ($related_articles as $index => $item) : ?>
								<?php if ($index > 0) : ?><div class="self-stretch h-0 border-t border-zinc-400"></div><?php endif; ?>
								<?php get_template_part('template-parts/listings/post-item-compact', null, $item); ?>
							<?php endforeach; ?>
						</div>
					</div>
				</aside>
			<?php endif; ?>
		</div>

		<div class="self-stretch lg:pl-10 border-l-[0.50px] border-zinc-400 border-t-[0.50px] border-zinc-400 flex flex-col lg:flex-row gap-10">
			<article class="w-full lg:w-[800px] py-10 flex flex-col gap-6 text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
				<?php the_content(); ?>
			</article>

			<aside class="flex-1 lg:pl-5 py-10 border-l-[0.50px] border-zinc-400">
				<div class="flex flex-col gap-5">
					<div class="flex items-center gap-2">
						<div class="tag-dot"></div>
						<div class="text-neutral-800 text-xl font-medium font-['EB_Garamond'] leading-6">Share this article</div>
					</div>
					<div class="self-stretch h-0 border-t border-zinc-400"></div>
					<div class="inline-flex items-center gap-2">
						<a href="#" aria-label="Copy link" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">🔗</a>
						<a href="#" aria-label="Facebook" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">f</a>
						<a href="#" aria-label="X" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">x</a>
						<a href="#" aria-label="Instagram" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">◎</a>
					</div>
				</div>
			</aside>
		</div>

		<?php if (! empty($read_also_items)) : ?>
			<div class="self-stretch lg:pl-10 border-l-[0.50px] border-zinc-400 border-t-[0.50px] border-zinc-400 py-10 flex flex-col gap-10">
				<div class="inline-flex items-center gap-2">
					<div class="tag-dot"></div>
					<h2 class="text-neutral-800 text-[42px] font-bold font-['EB_Garamond'] leading-[50.40px]">Read also</h2>
				</div>
				<div class="self-stretch h-0 border-t border-zinc-400"></div>
				<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
					<?php foreach ($read_also_items as $item) : ?>
						<?php get_template_part('template-parts/listings/post-item-half-row', null, $item); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</section>
</main>

<?php get_footer(); ?>