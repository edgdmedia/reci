<?php

/**
 * Native archive route for reci_course.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$page_title    = 'Courses';
$page_subtitle = 'Self-paced learning journeys exploring racial equity, digital access, and the systems that shape our communities.';

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

$base_url        = is_post_type_archive('reci_course') ? get_post_type_archive_link('reci_course') : get_permalink();
$base_url        = $base_url ?: home_url('/learn/');
$all_filters_url = remove_query_arg(['topic', 'search', 'paged'], $base_url);
$has_filters     = ($current_topic !== '') || ($current_search !== '');

$listing_config = [
	'post_type'                => 'reci_course',
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'archive_grid_card',
	'wrapper_class'            => 'self-stretch grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10',
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'filter_taxonomies'        => [
		'category' => [
			'param' => 'topic',
			'field' => 'slug',
		],
	],
	'empty_message'            => 'No courses found for this filter combination.',
];

get_header();
?>

<main class="layout-page">
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $page_subtitle,
	]); ?>

	<section class="reci-container pt-5 pb-14 flex flex-col gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold">Filter by:</span>
					<div class="archive-filter-select-wrap">
						<label for="course-topic-filter" class="sr-only">Filter by topic</label>
						<select id="course-topic-filter" name="topic" class="archive-filter-select" aria-label="Filter by topic">
							<option value="">All Topics</option>
							<?php foreach ($topic_terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_topic, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
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
						<svg class="w-4 h-4 flex-shrink-0 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="course-search" class="sr-only">Search courses</label>
						<input id="course-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search Courses" class="archive-filter-search-input" />
					</div>
					<?php if ($has_filters) : ?>
						<a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>

<?php get_footer(); ?>
