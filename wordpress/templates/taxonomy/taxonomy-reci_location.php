<?php

/**
 * Location taxonomy archive (runtime route).
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$term = get_queried_object();
if (! $term instanceof WP_Term) {
	$term = null;
}

$page_title  = $term ? $term->name : 'Location';
$description = $term ? term_description($term->term_id, 'reci_location') : '';

$base_url = $term ? get_term_link($term) : home_url('/location/');
if (is_wp_error($base_url)) {
	$base_url = home_url('/location/');
}

$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';
$clear_url      = remove_query_arg(['search', 'paged'], $base_url);

$listing_config = [
	'post_type'                => ['post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection'],
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'archive_grid_card',
	'wrapper_class'            => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
	'item_overrides'           => [
		'title_classes'   => "self-stretch justify-start text-neutral-800 text-2xl font-bold font-serif leading-7 line-clamp-3",
		'excerpt_classes' => "self-stretch justify-start text-neutral-500 text-sm font-normal leading-5 ",
	],
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'pagination_wrapper_class' => 'mt-10 flex items-center justify-center gap-2',
	'pagination_item_class'    => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100",
	'pagination_current_class' => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg bg-[#003594] text-sm font-medium text-white",
	'empty_message'            => 'No content found for this location.',
];

if ($term) {
	$listing_config['tax_query'] = [
		[
			'taxonomy' => 'reci_location',
			'field'    => 'term_id',
			'terms'    => [(int) $term->term_id],
		],
	];
}

get_header();
?>
<main class="layout-page">
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $description,
	]); ?>

	<section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold">Filter by:</span>
				</div>
				<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="archive-filter-search-wrap" role="search">
						<svg class="archive-filter-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="location-search" class="sr-only">Search</label>
						<input id="location-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search" class="archive-filter-search-input" />
					</div>
					<?php if ($current_search !== '') : ?>
						<a href="<?php echo esc_url($clear_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>
<?php get_footer(); ?>
