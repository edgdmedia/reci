<?php
/**
 * Search results.
 *
 * Without this template WordPress falls back to index.php, which renders bare
 * headings and excerpts instead of the theme's listing cards.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$search_query = get_search_query();
$result_count = (int) ($GLOBALS['wp_query']->found_posts ?? 0);

$listing_config = [
	'post_type'           => [
		'post',
		'reci_podcast',
		'reci_video',
		'reci_event',
		'reci_reflection',
		'reci_course',
		'reci_document',
		'reci_assessment',
	],
	'posts_per_page'      => 12,
	'orderby'             => 'relevance',
	'listing_style'       => 'archive_grid_card',
	'wrapper_class'       => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
	'item_overrides'      => [
		'title_classes'   => 'self-stretch justify-start text-neutral-800 text-2xl font-bold font-serif leading-7 line-clamp-3',
		'excerpt_classes' => 'self-stretch justify-start text-neutral-500 text-sm font-normal leading-5',
	],
	'enable_pagination'   => true,
	'pagination_param'    => 'paged',
	'filter_search_param' => 's',
	'empty_message'       => __('No results found. Try a different search term.', 'reci-media-hub'),
];

get_header();
?>

<main class="layout-page">
	<?php
	get_template_part(
		'template-parts/common/page-title-card',
		null,
		[
			'title'    => '' !== $search_query
				? sprintf( /* translators: %s: search term. */ __('Search: %s', 'reci-media-hub'), $search_query)
				: __('Search', 'reci-media-hub'),
			'subtitle' => '' !== $search_query
				? sprintf(
					/* translators: %d: number of results. */
					_n('%d result across articles, media, courses, and resources.', '%d results across articles, media, courses, and resources.', $result_count, 'reci-media-hub'),
					$result_count
				)
				: __('Search articles, media, courses, and resources across the RECI Media Hub.', 'reci-media-hub'),
		]
	);
	?>

	<section class="reci-container py-12 lg:py-14 flex flex-col justify-start items-start gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex flex-col sm:flex-row justify-between items-center gap-5" role="search">
				<div class="w-full sm:max-w-md">
					<div class="archive-filter-search-wrap">
						<svg class="archive-filter-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="search-results-input" class="sr-only"><?php esc_html_e('Search', 'reci-media-hub'); ?></label>
						<input
							id="search-results-input"
							type="search"
							name="s"
							value="<?php echo esc_attr($search_query); ?>"
							placeholder="<?php esc_attr_e('Search Articles, Videos, Podcasts…', 'reci-media-hub'); ?>"
							class="archive-filter-search-input" />
					</div>
				</div>
				<button type="submit" class="btn btn-primary btn-md w-full sm:w-auto"><?php esc_html_e('Search', 'reci-media-hub'); ?></button>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>

<?php get_footer(); ?>
