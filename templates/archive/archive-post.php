<?php

/**
 * Native archive route for post.
 *
 * Canonical article archive implementation. The page template wrapper
 * should include this file so both entry points stay in sync.
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$page_title = "Articles";
$page_subtitle =
    "Explore ideas, insights, and practical equity learning stories from the RECI community.";

$topic_terms = get_terms([
    "taxonomy" => "category",
    "hide_empty" => true,
]);
if (is_wp_error($topic_terms)) {
    $topic_terms = [];
}

$placeholder_avatar = "https://placehold.co/60x60";

$get_post_image = static function (
    int $post_id,
    string $size = "large",
    string $fallback = "",
): string {
    $image = get_the_post_thumbnail_url($post_id, $size);
    if (!$image) {
        return $fallback;
    }
    return $image;
};

$author_options = reci_media_hub_get_author_profile_options(true, [
    "post",
]);

$sphere_terms_filter = get_terms([
    'taxonomy'   => 'reci_sphere',
    'hide_empty' => true,
]);
if (is_wp_error($sphere_terms_filter)) {
    $sphere_terms_filter = [];
}

$current_topic = isset($_GET["topic"])
    ? sanitize_title((string) wp_unslash($_GET["topic"]))
    : "";
$current_sphere = isset($_GET["sphere"])
    ? sanitize_title((string) wp_unslash($_GET["sphere"]))
    : "";
$current_author = isset($_GET["author"])
    ? max(0, (int) wp_unslash($_GET["author"]))
    : 0;
$current_search = isset($_GET["search"])
    ? sanitize_text_field((string) wp_unslash($_GET["search"]))
    : "";

$base_url = (is_post_type_archive("post") && get_option('page_for_posts'))
    ? get_post_type_archive_link("post")
    : get_permalink();
$base_url = (empty($base_url) || $base_url === home_url() || $base_url === home_url('/')) ? home_url("/articles/") : $base_url;
$all_filters_url = remove_query_arg(
    ["topic", "sphere", "author", "search", "paged"],
    $base_url,
);
$has_filters =
    $current_topic !== "" || $current_sphere !== "" || $current_author > 0 || $current_search !== "";

$query_args = [
    "post_type" => "post",
    "post_status" => "publish",
    "posts_per_page" => 12,
    "paged" => max(
        1,
        (int) get_query_var("paged"),
        (int) get_query_var("page"),
    ),
    "orderby" => "date",
    "order" => "DESC",
];

if ($current_search !== "") {
    $query_args["s"] = $current_search;
}
if ($current_author > 0) {
    $author_post_ids = reci_media_hub_get_authored_content_ids(
        $current_author,
        ["post"],
    );
    $query_args["post__in"] = !empty($author_post_ids) ? $author_post_ids : [0];
}
$query_args["tax_query"] = ["relation" => "AND"];
if ($current_topic !== "") {
    $query_args["tax_query"][] = [
        "taxonomy" => "category",
        "field" => "slug",
        "terms" => [$current_topic],
    ];
}
if ($current_sphere !== "") {
    $query_args["tax_query"][] = [
        "taxonomy" => "reci_sphere",
        "field" => "slug",
        "terms" => [$current_sphere],
    ];
}

$articles_listing = RECI_Post_Query_Service::get_formatted_items($query_args, [
    "image_size" => "large",
    "tag_limit" => 3,
    "excerpt_words" => 20,
]);
$article_items = $articles_listing["items"];
$article_query = $articles_listing["query"];

// Quotes of the day — all quotes for carousel.
$quote_items = [
    [
        "quote" =>
            "Racial equity work is built through consistent reflection, learning, and action.",
        "author" => "– By RECI",
    ],
];
$community_slides = [
    [
        "quote" =>
            "RECI has truly transformed how we approach racial equity learning.",
        "author_name" => "Community Member",
        "author_role" => "RECI Contributor",
        "author_image" => $placeholder_avatar,
        "author_alt" => "Community Member",
    ],
];

$quote_posts = RECI_Post_Query_Service::get_posts([
    "post_type" => "reci_quote",
    "post_status" => "publish",
    "posts_per_page" => 4,
    "orderby" => "rand",
]);
if (!empty($quote_posts)) {
    $quote_items = [];
    foreach ($quote_posts as $post) {
        $post_id    = (int) $post->ID;
        $quote_text = (string) get_post_meta($post_id, "_reci_quote_text", true);
        $author     = reci_get_quote_author_data($post_id);
        $text =
            $quote_text ?:
            wp_trim_words(wp_strip_all_tags($post->post_content), 38, "...");
        $name = $author["name"] ?: get_the_title($post_id);

        if (count($quote_items) < 4) {
            $quote_items[] = [
                "quote"        => $text,
                "author"       => "– By " . $name,
                "author_role"  => $author["title"] ?: "",
                "author_image" => $author["image_url"],
            ];
        }
    }
    if (empty($quote_items)) {
        $quote_items = [
            [
                "quote" =>
                    "Racial equity work is built through consistent reflection, learning, and action.",
                "author" => "– By RECI",
                "author_role" => "",
            ],
        ];
    }
}

$testimonial_posts = RECI_Post_Query_Service::get_posts([
    "post_type" => "reci_testimonial",
    "post_status" => "publish",
    "posts_per_page" => 4,
    "orderby" => "rand",
]);
$community_slides = [];
if (!empty($testimonial_posts)) {
    foreach ($testimonial_posts as $post) {
        $post_id = (int) $post->ID;
        $text    = (string) get_post_meta($post_id, "_reci_testimonial_text", true);
        $name    = (string) get_post_meta($post_id, "_reci_testimonial_full_name", true);
        $role    = (string) get_post_meta($post_id, "_reci_testimonial_role", true);
        $org     = (string) get_post_meta($post_id, "_reci_testimonial_organization", true);
        $image   = get_the_post_thumbnail_url($post_id, "thumbnail") ?: "";

        if (empty($text)) continue;

        $community_slides[] = [
            "quote"        => $text,
            "author_name"  => $name ?: get_the_title($post_id),
            "author_role"  => $role,
            "author_org"   => $org,
            "author_image" => $image ?: $placeholder_avatar,
            "author_alt"   => $name ?: "Testimonial",
        ];
    }
}
if (empty($community_slides)) {
    $community_slides = [
        [
            "quote" =>
                "RECI has truly transformed how we approach racial equity learning.",
            "author_name" => "Community Member",
            "author_role" => "RECI Contributor",
            "author_image" => $placeholder_avatar,
            "author_alt" => "Community Member",
        ],
    ];
}
$quote_count = count($quote_items);
$community_count = count($community_slides);

get_header();
?>

<main class="layout-page">
	<?php get_template_part("template-parts/common/page-title-card", null, [
     "title" => $page_title,
     "subtitle" => $page_subtitle,
 ]); ?>

	<section class="reci-container  mx-auto  pt-5 pb-14 flex flex-col gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url(
       $base_url,
   ); ?>" class="self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold">Filter by:</span>
					<div class="archive-filter-select-wrap">
						<label for="articles-topic-filter" class="sr-only">Filter by topic</label>
						<select id="articles-topic-filter" name="topic" class="archive-filter-select" aria-label="Filter by topic">
							<option value="">All Topics</option>
							<?php foreach ($topic_terms as $term): ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php selected(
    $current_topic,
    $term->slug,
); ?>><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="archive-filter-chevron">
							<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
							</svg>
						</span>
					</div>
					<?php if (! empty($sphere_terms_filter)) : ?>
						<div class="archive-filter-select-wrap">
							<label for="articles-sphere-filter" class="sr-only">Filter by sphere</label>
							<select id="articles-sphere-filter" name="sphere" class="archive-filter-select" aria-label="Filter by sphere">
								<option value="">All Spheres</option>
                                <?php foreach ($sphere_terms_filter as $st):
                $st_name = $st->name;
                ?>
									<option value="<?php echo esc_attr($st->slug); ?>" <?php selected($current_sphere, $st->slug); ?>><?php echo esc_html($st_name); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="archive-filter-chevron">
								<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
								</svg>
							</span>
						</div>
					<?php endif; ?>
					<div class="archive-filter-select-wrap">
						<label for="articles-author-filter" class="sr-only">Filter by author</label>
						<select id="articles-author-filter" name="author" class="archive-filter-select" aria-label="Filter by author">
							<option value="">All Authors</option>
							<?php foreach ($author_options as $author): ?>
								<option value="<?php echo esc_attr((string) $author["ID"]); ?>" <?php selected(
    $current_author,
    (int) $author["ID"],
); ?>><?php echo esc_html((string) $author["display_name"]); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="archive-filter-chevron">
							<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
							</svg>
						</span>
					</div>
				</div>

				<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="archive-filter-search-wrap" role="search">
						<svg class="w-4 h-4 flex-shrink-0 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="articles-search" class="sr-only">Search articles</label>
						<input id="articles-search" type="search" name="search" value="<?php echo esc_attr(
          $current_search,
      ); ?>" placeholder="Search Articles" class="archive-filter-search-input" />
					</div>
					<?php if ($has_filters): ?>
						<a href="<?php echo esc_url(
          $all_filters_url,
      ); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<?php if (!empty($article_items)): ?>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
				<?php foreach ($article_items as $item): ?>
					<?php get_template_part(
         "template-parts/listings/articles-side-card",
         null,
         $item,
     ); ?>
				<?php endforeach; ?>
			</div>
			<?php echo RECI_Post_Query_Service::render_pagination($article_query, [
       "base_url" => $base_url,
       "param_name" => "paged",
       "wrapper_class" => "mt-8 flex w-full items-center justify-center gap-2",
       "item_class" =>
           "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100",
       "current_class" =>
           "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg bg-[#003594] text-sm font-medium text-white",
   ]); ?>
		<?php else: ?>
			<p class="text-neutral-500 text-base">No articles found for this filter combination.</p>
		<?php endif; ?>
	</section>

		
</main>

<?php get_footer(); ?>
