<?php

/**
 * Template Name: Location Archive
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$location_terms = get_terms(
	[
		'taxonomy'   => 'reci_location',
		'hide_empty' => true,
	]
);
if (is_wp_error($location_terms)) {
	$location_terms = [];
}

$current_location = isset($_GET['location']) ? sanitize_title((string) wp_unslash($_GET['location'])) : '';
$current_search   = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';

$base_url        = get_permalink() ?: home_url('/location/');
$all_filters_url = remove_query_arg(['location', 'search', 'paged'], $base_url);
$has_filters     = ($current_location !== '') || ($current_search !== '');

$listing_config = [
	'post_type'                => ['post', 'reci_podcast', 'reci_video', 'reci_event'],
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'archive_grid_card',
	'wrapper_class'            => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
	'item_overrides'           => [
		'title_classes'   => "self-stretch justify-start text-neutral-800 text-2xl font-bold font-heading leading-7 line-clamp-3",
		'excerpt_classes' => "self-stretch justify-start text-neutral-600 text-sm font-normal leading-5 ",
	],
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'filter_taxonomies'        => [
		'reci_location' => [
			'param' => 'location',
			'field' => 'slug',
		],
	],
	'pagination_wrapper_class' => 'mt-10 flex items-center justify-center gap-2',
	'pagination_item_class'    => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100",
	'pagination_current_class' => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg bg-[#003594] text-sm font-medium text-white",
	'empty_message'            => 'No content found for this location filter.',
];

get_header();
?>

<main class="bg-slate-100 min-h-screen">
	<section class="w-full bg-white border-b border-zinc-400">
		<div class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 flex flex-col lg:flex-row justify-between gap-6">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
				<h1 class="text-neutral-800 text-5xl font-bold font-heading">By Location</h1>
			</div>
			<p class="max-w-xl text-neutral-600 text-lg font-normal">Discover content tied to specific communities and geographies.</p>
		</div>
	</section>

	<section class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-6 border-b border-zinc-300">
		<form method="get" action="<?php echo esc_url($base_url); ?>" class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4" data-archive-filter-form data-search-min="3" data-search-debounce="350">
			<div class="flex items-center gap-3 w-full lg:w-auto">
				<label for="location-filter" class="sr-only">Filter by location</label>
				<select id="location-filter" name="location" class="px-4 py-3 rounded-lg border border-zinc-300 bg-white text-neutral-800 text-base focus:outline-none">
					<option value="">All Locations</option>
					<?php foreach ($location_terms as $term) : ?>
						<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_location, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="flex items-center gap-2 w-full lg:w-auto">
				<label for="location-search" class="sr-only">Search content</label>
				<input id="location-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search" class="w-full lg:w-80 px-4 py-3 rounded-lg border border-zinc-300 bg-white text-neutral-700 text-base focus:outline-none" />
				<?php if ($has_filters) : ?>
					<a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
				<?php endif; ?>
			</div>
		</form>
	</section>

	<section class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10">
		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>

<?php get_footer(); ?>