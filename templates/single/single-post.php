<?php

/**
 * Single template for post.
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

if (!have_posts()) {
    wp_safe_redirect(home_url("/"));
    exit();
}

the_post();

$post_id = get_the_ID();
$single_payload = RECI_Single_Post_Service::get_post_payload($post_id, [
    "subtitle_meta_key" => "_post_subtitle",
    "image_size" => "large",
    "tag_limit" => 3,
]);

$article_title = (string) ($single_payload["title"] ?? get_the_title($post_id));
$article_date =
    (string) ($single_payload["date"] ?? get_the_date("d M Y", $post_id));
$article_read_time = (string) ($single_payload["meta_value"] ?? "3 mins");
$article_subtitle = (string) ($single_payload["subtitle"] ?? "");
$article_tags = is_array($single_payload["tags"] ?? null)
    ? $single_payload["tags"]
    : [];
$article_spheres = is_array($single_payload["sphere_terms"] ?? null)
    ? $single_payload["sphere_terms"]
    : [];
$article_author = is_array($single_payload["author"] ?? null)
    ? $single_payload["author"]
    : reci_media_hub_get_display_author($post_id);
$featured_image_url =
    (string) ($single_payload["featured_image_url"] ??
        (function_exists('reci_get_fallback_thumbnail_url') ? reci_get_fallback_thumbnail_url('large', 'https://placehold.co/800x446') : 'https://placehold.co/800x446'));
$featured_image_alt =
    (string) ($single_payload["featured_image_alt"] ?? get_the_title($post_id));
$share_url = rawurlencode(get_permalink($post_id));
$share_title = rawurlencode($article_title);
$article_comments_enabled = post_type_supports(
    (string) get_post_type($post_id),
    "comments",
);

$category = '';
$cats = wp_get_post_categories($post_id, ['fields' => 'names']);
if (!empty($cats) && !is_wp_error($cats)) {
    $category = (string) $cats[0];
}

$related_articles = RECI_Related_Posts_Service::get_related($post_id, [
    "post_type" => "post",
    "limit" => 4,
    "taxonomy" => "category",
    "format_args" => [
        "image_size" => "medium",
        "tag_limit" => 3,
        "excerpt_words" => 16,
    ],
]);

$read_also_items = RECI_Related_Posts_Service::get_cross_type_related($post_id, [
    "limit" => 3,
    "format_args" => [
        "image_size" => "large",
        "tag_limit" => 3,
        "excerpt_words" => 16,
    ],
]);

$article_comments = get_comments([
    "post_id" => $post_id,
    "status" => "approve",
    "order" => "ASC",
]);

get_header();
?>

<main class="layout">
	<section class="reci-container">
		<!-- ── Article + SIDEBAR LAYOUT ─────────────────────────────── -->
		<div class=" border-l-0 lg:border-l-[0.50px] border-b-[0.50px] border-zinc-300 flex flex-col lg:flex-row justify-start items-start gap-10">
			<!-- ── LEFT: Primary article content ──────────────────────────── -->
			<div class="w-full pl-0 lg:px-10 self-stretch py-14 flex flex-col justify-start items-start gap-10  lg:border-r-[0.50px] border-zinc-300">
				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<div class="self-stretch flex flex-col justify-start items-start gap-5">
						<div class="flex items-center justify-between w-full flex-wrap gap-4">
							<div class="inline-flex justify-start items-center gap-2.5 flex-wrap">
								<a href="<?php echo esc_url((get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/'))); ?>" class="no-underline">
									<div class="px-2 py-1 bg-amber-400 rounded flex justify-center items-center gap-2.5">
										<span class="text-neutral-800 text-sm font-normal leading-4"><?php esc_html_e("Article", "reci-media-hub"); ?></span>
									</div>
								</a>
								<?php if ($category !== '') : ?>
									<div class="tag-dot"></div>
									<a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="text-neutral-500 text-sm font-normal leading-4 no-underline hover:underline"><?php echo esc_html($category); ?></a>
								<?php endif; ?>
								<?php if (!empty($article_author["name"])): ?>
									<div class="tag-dot"></div>
									<p class="font-bold font-subhead text-neutral-600">By
										<a href="<?php echo esc_url(! empty($article_author["permalink"]) ? $article_author["permalink"] : get_author_posts_url((int) get_post_field('post_author', $post_id))); ?>" class="text-neutral-600 hover:underline"><?php echo esc_html((string) $article_author["name"]); ?></a>
									</p>
								<?php endif; ?>
								<div class="tag-dot"></div>
								<span class="text-neutral-600 text-sm font-normal"><?php echo esc_html($article_date); ?></span>
								<div class="tag-dot"></div>
								<div class="flex items-center gap-1">
									<?php echo reci_inline_svg("assets/icons/timer-outline.svg", "w-3.5 h-3.5 opacity-60 text-neutral-600", ["aria-hidden" => "true"]); ?>
									<span class="text-neutral-600 text-sm font-normal"><?php echo esc_html($article_read_time); ?></span>
								</div>
							</div>
							<?php echo reci_render_post_actions(); ?>
						</div>
						<div class="self-stretch flex flex-col gap-5">
							<h1 class="self-stretch reci-single-title capitalize"><?php echo esc_html(
            $article_title,
        ); ?></h1>
							<?php if ($article_subtitle !== ""): ?>
								<p class="self-stretch text-neutral-700 text-2xl font-normal leading-7 "><?php echo esc_html(
            $article_subtitle,
        ); ?></p>
							<?php endif; ?>
							<?php
$all_spheres = reci_get_post_spheres(get_the_ID());
if (!empty($all_spheres)) : ?>
    <div class="self-stretch overflow-hidden">
        <div class="inline-flex items-center gap-1.5 flex-nowrap">
            <?php foreach ($all_spheres as $s) : ?>
								<a href="<?php echo esc_url($s['url']); ?>" class="sphere" style="background-color: <?php echo esc_attr($s['color']); ?>1a;">
                    <span class="rounded-full w-2 h-2" style="background-color: <?php echo esc_attr($s['color']); ?>;"></span>
                    <span class="font-medium" style="color: <?php echo esc_attr($s['color']); ?>;"><?php echo esc_html($s['name']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
						</div>
					</div>
				</div>

				<img class="self-stretch rounded-lg w-full h-[350px] object-cover object-center" src="<?php echo esc_url(
        $featured_image_url,
    ); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" />



				<article class="w-full reci-post-content pt-10 flex flex-col gap-6 ">
					<?php the_content(); ?>
				</article>
                	<?php
$post_tags = wp_get_post_tags(get_the_ID(), ['fields' => 'all']);
if (!empty($post_tags) && !is_wp_error($post_tags)) : ?>
    <div class="self-stretch mt-4 flex flex-wrap items-center gap-2">
        <?php foreach ($post_tags as $tag) : ?>
							<a href="<?php echo esc_url(get_tag_link((int) $tag->term_id)); ?>" class="tag"><?php echo esc_html($tag->name); ?></a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

					<?php
					$sdgs = wp_get_post_terms(get_the_ID(), 'sdgs');
					if (!empty($sdgs) && !is_wp_error($sdgs)) : ?>
						<div class="self-stretch mt-4 flex flex-wrap items-center gap-2">
							<?php foreach ($sdgs as $sdg) : 
								$sdg_color = get_term_meta($sdg->term_id, 'sdg_color', true) ?: '#ccc';
							?>
								<a href="<?php echo esc_url(get_term_link($sdg)); ?>" class="tag" style="background-color: <?php echo esc_attr($sdg_color); ?>; color: #fff;">
									<?php echo esc_html($sdg->name); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<section class="w-full border-t border-zinc-300 pt-10">
						<div class="flex flex-col gap-5">
							<div class="inline-flex items-center gap-2">
								<div class="tag-dot"></div>
								<h2 class="text-neutral-800 text-2xl font-bold font-subhead leading-7"><?php esc_html_e(
            "About the author",
            "reci-media-hub",
        ); ?></h2>
							</div>
							<div class="flex flex-col sm:flex-row items-start gap-4">
							<?php if (!empty($article_author["image_url"])): ?>
								<?php if (!empty($article_author["permalink"])): ?>
									<a href="<?php echo esc_url(
             (string) $article_author["permalink"],
         ); ?>" class="flex-shrink-0">
										<img src="<?php echo esc_url(
              (string) $article_author["image_url"],
          ); ?>" alt="<?php echo esc_attr(
    (string) ($article_author["image_alt"] ?? $article_author["name"]),
); ?>" class="w-16 h-16 flex-shrink-0 rounded-full object-cover" />
									</a>
								<?php else: ?>
									<img src="<?php echo esc_url(
             (string) $article_author["image_url"],
         ); ?>" alt="<?php echo esc_attr(
    (string) ($article_author["image_alt"] ?? $article_author["name"]),
); ?>" class="w-16 h-16 flex-shrink-0 rounded-full object-cover" />
								<?php endif; ?>
							<?php endif; ?>
								<div class="flex flex-col gap-2">
									<?php if (!empty($article_author["permalink"])): ?>
										<a href="<?php echo esc_url(
                                              (string) $article_author["permalink"],
                                          ); ?>" class="text-neutral-700 text-xl font-normal leading-7 hover:underline">
                                            <?php echo esc_html((string) $article_author["name"]); ?>
                                        </a>
									<?php else: ?>
										<h3 class="text-neutral-700 text-xl font-normal leading-7"><?php echo esc_html(
                                              (string) $article_author["name"],
                                          ); ?></h3>
									<?php endif; ?>
									<?php if (!empty($article_author["title"])): ?>
										<p class="text-neutral-600 text-sm font-medium leading-5"><?php echo esc_html(
                                              (string) $article_author["title"],
                                          ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</section>

				<section class="w-full border-t border-zinc-300 pt-10 flex flex-col gap-8">
					<div class="self-stretch flex flex-col gap-5">
						<div class="inline-flex items-center gap-2">
							<div class="tag-dot"></div>
							<h2 class="text-neutral-800 text-2xl font-bold font-subhead leading-7"><?php esc_html_e(
           "Readers response",
           "reci-media-hub",
       ); ?></h2>
						</div>
						<div class="self-stretch h-px bg-zinc-300"></div>
					</div>

					<?php if ($article_comments_enabled): ?>
						<div class="flex flex-col gap-4">
							<?php comment_form([
           "title_reply" => "",
           "title_reply_before" => "",
           "title_reply_after" => "",
           "comment_notes_before" => "",
           "comment_notes_after" => "",
           "label_submit" => __("Publish", "reci-media-hub"),
           "class_submit" => "btn btn-primary btn-md",
           "class_form" => "flex flex-col gap-4",
           "class_submit" => "btn btn-primary btn-md self-end",
           "comment_field" =>
               '<div class="flex flex-col gap-3"><textarea id="comment" name="comment" cols="45" rows="6" required class="w-full p-3 bg-white rounded-lg border border-zinc-300 text-neutral-800 text-base font-normal leading-6 outline-none" placeholder="' .
               esc_attr__("Share your thoughts…", "reci-media-hub") .
               '"></textarea></div>',
           "fields" => [
               "author" =>
                   '<input id="author" name="author" type="text" required class="w-full p-3 bg-white rounded-lg border border-zinc-300 text-neutral-800 text-base font-normal leading-6 outline-none" placeholder="' .
                   esc_attr__("Your name", "reci-media-hub") .
                   '"/>',
               "email" =>
                   '<input id="email" name="email" type="email" required class="w-full p-3 bg-white rounded-lg border border-zinc-300 text-neutral-800 text-base font-normal leading-6 outline-none" placeholder="' .
                   esc_attr__("Your email", "reci-media-hub") .
                   '"/>',
           ],
       ]); ?>
						</div>
					<?php endif; ?>

					<?php if (!empty($article_comments)): ?>
						<div class="flex flex-col gap-6">
							<?php foreach ($article_comments as $comment): ?>
								<div class="flex flex-col gap-2">
									<div class="inline-flex items-center gap-3 flex-wrap">
										<div class="text-neutral-800 text-lg font-bold leading-7 "><?php echo esc_html(
              get_comment_author($comment),
          ); ?></div>
										<div class="tag-dot"></div>
										<div class="text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html(
              get_comment_date("d M Y", $comment),
          ); ?></div>
									</div>
									<div class="text-neutral-600 text-base font-normal leading-6 "><?php echo esc_html(
             get_comment_text($comment),
         ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</section>
			</div>
			<!-- ── RIGHT: Related videos sidebar ────────────────────────── -->
			<aside class="w-full lg:w-1/3 lg:py-14 lg:self-start lg:sticky lg:top-10">
				<div class="flex flex-col gap-10">
					<?php if (!empty($related_articles)): ?>
						<div class="bg-neutral-800 p-5 flex flex-col gap-10 overflow-hidden">
							<div class="self-stretch flex flex-col gap-5">
								<div class="inline-flex items-center gap-2">
									<div class="tag-dot"></div>
									<div class="text-white text-2xl tracking-wider font-heading leading-7"><?php esc_html_e(
             "Related Article",
             "reci-media-hub",
         ); ?></div>
								</div>
								<div class="self-stretch h-0 border-t-2 border-white"></div>
							</div>
							<div class="self-stretch flex flex-col gap-10 overflow-hidden">
								<?php foreach ($related_articles as $index => $item): ?>
									<?php if (
             $index > 0
         ): ?><div class="self-stretch h-0 border-t border-zinc-400"></div><?php endif; ?>
									<?php get_template_part(
             "template-parts/listings/post-item-compact-related-dark",
             null,
             $item,
         ); ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="flex flex-col py-4 gap-5">
						<div class="flex items-center gap-2">
							<div class="tag-dot"></div>
							<div class="text-neutral-800 text-xl font-bold font-subhead leading-6"><?php esc_html_e(
           "Share this article",
           "reci-media-hub",
       ); ?></div>
						</div>
						<div class="self-stretch h-px bg-zinc-300"></div>
						<div class="inline-flex items-center gap-2">
							<button type="button" onclick="navigator.clipboard.writeText(window.location.href)" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e(
           "Copy link",
           "reci-media-hub",
       ); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
								</svg>
							</button>
							<a href="<?php echo esc_url(
           "https://www.facebook.com/sharer/sharer.php?u=" . $share_url,
       ); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e(
    "Share on Facebook",
    "reci-media-hub",
); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
								</svg>
							</a>
							<a href="<?php echo esc_url(
           "https://twitter.com/intent/tweet?url=" .
               $share_url .
               "&text=" .
               $share_title,
       ); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e(
    "Share on X (Twitter)",
    "reci-media-hub",
); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
								</svg>
							</a>
						</div>
					</div>

				</div>
			</aside>
		</div>
		<!-- ── "SEE ALSO" — more videos ──────────────────────────────────── -->
		<?php if (!empty($read_also_items)): ?>
			<div class="pt-10 pb-10 flex flex-col gap-10">
				<div class="inline-flex items-center gap-2 pb-5 border-b border-zinc-300">
					<div class="tag-dot"></div>
					<h2 class="text-neutral-800 text-2xl md:text-3xl font-bold font-subhead leading-snug"><?php esc_html_e(
         "Read also",
         "reci-media-hub",
     ); ?></h2>
				</div>
				<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
					<?php foreach ($read_also_items as $item): ?>
						<?php get_template_part("template-parts/listings/see-also-card", null, $item); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php get_footer(); ?>
