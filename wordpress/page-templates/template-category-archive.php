<?php
/**
 * Template Name: Category Archive
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$topic_terms = get_terms(
	[
		'taxonomy'   => 'category',
		'hide_empty' => true,
	]
);
if (is_wp_error($topic_terms)) {
	$topic_terms = [];
}

$current_topic  = isset($_GET['topic']) ? sanitize_title((string) wp_unslash($_GET['topic'])) : '';
$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';

$base_url        = get_permalink() ?: home_url('/category/');
$all_filters_url = remove_query_arg(['topic', 'search', 'paged'], $base_url);
$has_filters     = ($current_topic !== '') || ($current_search !== '');

$listing_config = [
	'post_type'                => ['reci_article', 'reci_podcast', 'reci_video'],
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'category_archive_row',
	'wrapper_class'            => 'flex flex-col gap-10',
	'divider_class'            => 'divider divider-zinc',
	'item_overrides'           => [
		'title_classes'   => "self-stretch justify-start text-neutral-800 text-3xl lg:text-5xl font-bold font-['EB_Garamond'] leading-tight lg:leading-10 line-clamp-2",
		'excerpt_classes' => "self-stretch justify-start text-neutral-500 text-base lg:text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight line-clamp-2",
	],
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'filter_taxonomies'        => [
		'category' => [
			'param' => 'topic',
			'field' => 'slug',
		],
	],
	'pagination_wrapper_class' => 'mt-10 flex items-center justify-center gap-2',
	'pagination_item_class'    => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg border border-zinc-300 text-sm font-medium font-['SF_Pro_Display'] text-neutral-800 hover:bg-zinc-100",
	'pagination_current_class' => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg bg-[#003594] text-sm font-medium font-['SF_Pro_Display'] text-white",
	'empty_message'            => 'No content found for this category filter.',
];

get_header();
?>

<main class="bg-slate-100 min-h-screen">
	<section class="w-full bg-white border-b border-zinc-400">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 flex flex-col lg:flex-row justify-between gap-6">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
				<h1 class="text-neutral-800 text-5xl font-medium font-['EB_Garamond']">By Category</h1>
			</div>
			<p class="max-w-xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display']">Browse articles, podcasts, and videos by topic.</p>
		</div>
	</section>

	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-6 border-b border-zinc-300">
		<form method="get" action="<?php echo esc_url($base_url); ?>" class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4" data-archive-filter-form data-search-min="3" data-search-debounce="350">
			<div class="flex items-center gap-3 w-full lg:w-auto">
				<label for="category-topic-filter" class="sr-only">Filter by category</label>
				<select id="category-topic-filter" name="topic" class="px-4 py-3 rounded-lg border border-zinc-300 bg-white text-neutral-800 text-base font-['SF_Pro_Display'] focus:outline-none">
					<option value="">All Categories</option>
					<?php foreach ($topic_terms as $term) : ?>
						<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_topic, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="flex items-center gap-2 w-full lg:w-auto">
				<label for="category-search" class="sr-only">Search content</label>
				<input id="category-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search" class="w-full lg:w-80 px-4 py-3 rounded-lg border border-zinc-300 bg-white text-neutral-700 text-base font-['SF_Pro_Display'] focus:outline-none" />
				<?php if ($has_filters) : ?>
					<a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
				<?php endif; ?>
			</div>
		</form>
	</section>

	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10">
		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>

<?php get_footer(); ?>
