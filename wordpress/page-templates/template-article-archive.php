<?php
/**
 * Template Name: Article Archive
 *
 * Real-data archive template using the reusable listing builder.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$page_title    = 'Articles';
$page_subtitle = 'Explore ideas, insights, and practical equity learning stories from the RECI community.';

$topic_terms = get_terms(
	[
		'taxonomy'   => 'category',
		'hide_empty' => true,
	]
);
if (is_wp_error($topic_terms)) {
	$topic_terms = [];
}

$author_options = get_users(
	[
		'fields'              => ['ID', 'display_name'],
		'has_published_posts' => ['reci_article'],
		'orderby'             => 'display_name',
		'order'               => 'ASC',
	]
);

$current_topic  = isset($_GET['topic']) ? sanitize_title((string) wp_unslash($_GET['topic'])) : '';
$current_author = isset($_GET['author']) ? max(0, (int) wp_unslash($_GET['author'])) : 0;
$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';

$base_url        = is_post_type_archive('reci_article') ? get_post_type_archive_link('reci_article') : get_permalink();
$base_url        = $base_url ?: home_url('/articles/');
$all_filters_url = remove_query_arg(['topic', 'author', 'search', 'paged'], $base_url);
$has_filters     = ($current_topic !== '') || ($current_author > 0) || ($current_search !== '');

$query_args = [
	'post_type'      => 'reci_article',
	'post_status'    => 'publish',
	'posts_per_page' => 9,
	'paged'          => max(1, (int) get_query_var('paged'), (int) get_query_var('page')),
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ($current_search !== '') {
	$query_args['s'] = $current_search;
}
if ($current_author > 0) {
	$query_args['author'] = $current_author;
}
if ($current_topic !== '') {
	$query_args['tax_query'] = [[
		'taxonomy' => 'category',
		'field'    => 'slug',
		'terms'    => [$current_topic],
	]];
}

$articles_listing = RECI_Post_Query_Service::get_formatted_items($query_args, [
	'image_size'    => 'large',
	'tag_limit'     => 3,
	'excerpt_words' => 20,
]);
$article_items = $articles_listing['items'];
$article_query = $articles_listing['query'];

get_header();
?>

<div class="bg-slate-100 min-h-screen">
	<section class="w-full bg-white border-b border-zinc-400">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14">
			<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
				<div class="flex items-center gap-3">
					<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
					<h1 class="text-neutral-800 text-5xl font-medium font-['EB_Garamond'] leading-[1.05]"><?php echo esc_html($page_title); ?></h1>
				</div>
				<div class="lg:pl-10 lg:border-l lg:border-zinc-400 max-w-xl">
					<p class="text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight"><?php echo esc_html($page_subtitle); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 pt-5 pb-14 flex flex-col gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold font-['SF_Pro_Display']">Filter by:</span>
					<div class="relative">
						<label for="articles-topic-filter" class="sr-only">Filter by topic</label>
						<select id="articles-topic-filter" name="topic" class="appearance-none px-4 py-2 pr-8 text-neutral-800 text-base font-normal font-['SF_Pro_Display'] bg-transparent border-none cursor-pointer focus:outline-none" aria-label="Filter by topic">
							<option value="">All Topics</option>
							<?php foreach ($topic_terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_topic, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1">
							<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
						</span>
					</div>
					<div class="relative">
						<label for="articles-author-filter" class="sr-only">Filter by author</label>
						<select id="articles-author-filter" name="author" class="appearance-none px-4 py-2 pr-8 text-neutral-800 text-base font-normal font-['SF_Pro_Display'] bg-transparent border-none cursor-pointer focus:outline-none" aria-label="Filter by author">
							<option value="">All Authors</option>
							<?php foreach ($author_options as $author) : ?>
								<option value="<?php echo esc_attr((string) $author->ID); ?>" <?php selected($current_author, (int) $author->ID); ?>><?php echo esc_html($author->display_name); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1">
							<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
						</span>
					</div>
				</div>

				<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="w-full sm:w-80 px-5 py-4 bg-white rounded-lg flex justify-start items-center gap-2.5" role="search">
						<svg class="w-4 h-4 flex-shrink-0 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
						<label for="articles-search" class="sr-only">Search articles</label>
						<input id="articles-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search Articles" class="flex-1 bg-transparent text-neutral-500 text-base font-light font-['SF_Pro_Display'] placeholder-neutral-500 focus:outline-none" />
					</div>
					<?php if ($has_filters) : ?>
						<a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<?php if (! empty($article_items)) : ?>
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
				<?php foreach ($article_items as $item) : ?>
					<?php get_template_part('template-parts/listings/archive-content-row', null, $item); ?>
				<?php endforeach; ?>
			</div>
			<?php echo RECI_Post_Query_Service::render_pagination($article_query, [
				'base_url'      => $base_url,
				'param_name'    => 'paged',
				'wrapper_class' => 'mt-8 flex items-center justify-center gap-2',
				'item_class'    => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg border border-zinc-300 text-sm font-medium font-['SF_Pro_Display'] text-neutral-800 hover:bg-zinc-100",
				'current_class' => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg bg-[#003594] text-sm font-medium font-['SF_Pro_Display'] text-white",
			]); ?>
		<?php else : ?>
			<p class="text-neutral-500 text-base">No articles found for this filter combination.</p>
		<?php endif; ?>
	</section>
</div>

<?php get_footer(); ?>
