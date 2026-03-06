<?php
/**
 * Topic taxonomy archive (runtime route).
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

$taxonomy    = $term ? $term->taxonomy : 'reci_topic';
$topic_title = $term ? $term->name : 'Topic';
$description = $term ? term_description($term->term_id, $taxonomy) : '';

$base_url = $term ? get_term_link($term) : home_url('/topic/');
if (is_wp_error($base_url)) {
	$base_url = home_url('/topic/');
}

$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';
$clear_url      = remove_query_arg(['search', 'paged'], $base_url);

$listing_config = [
	'post_type'                => ['reci_article', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection'],
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'topic_archive_row',
	'wrapper_class'            => 'flex flex-col gap-10',
	'divider_class'            => 'self-stretch h-0 border-t border-zinc-400',
	'item_overrides'           => [
		'title_classes'   => "self-stretch justify-start text-neutral-800 text-3xl lg:text-5xl font-bold font-['EB_Garamond'] leading-tight lg:leading-10 line-clamp-2",
		'excerpt_classes' => "self-stretch justify-start text-neutral-500 text-base lg:text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight line-clamp-2",
	],
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'pagination_wrapper_class' => 'mt-10 flex items-center justify-center gap-2',
	'pagination_item_class'    => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg border border-zinc-300 text-sm font-medium font-['SF_Pro_Display'] text-neutral-800 hover:bg-zinc-100",
	'pagination_current_class' => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg bg-[#003594] text-sm font-medium font-['SF_Pro_Display'] text-white",
	'empty_message'            => 'No content found for this topic.',
];

if ($term) {
	$listing_config['tax_query'] = [
		[
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => [(int) $term->term_id],
		],
	];
}

get_header();
?>
<main class="bg-slate-100 min-h-screen">
	<section class="w-full bg-white border-b border-zinc-400">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 flex flex-col lg:flex-row justify-between gap-6">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
				<h1 class="text-neutral-800 text-5xl font-medium font-['EB_Garamond']"><?php echo esc_html($topic_title); ?></h1>
			</div>
			<?php if ($description !== '') : ?>
				<p class="max-w-xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display']"><?php echo wp_kses_post(wp_strip_all_tags($description)); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-6 border-b border-zinc-300">
		<form method="get" action="<?php echo esc_url($base_url); ?>" class="flex items-center justify-between gap-4 flex-wrap" data-archive-filter-form data-search-min="3" data-search-debounce="350">
			<div class="text-neutral-700 text-sm font-medium font-['SF_Pro_Display']">Filtering this topic</div>
			<div class="flex items-center gap-2 w-full sm:w-auto">
				<label for="topic-search-runtime" class="sr-only">Search in this topic</label>
				<input id="topic-search-runtime" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search this topic" class="w-full sm:w-80 px-4 py-3 rounded-lg border border-zinc-300 bg-white text-neutral-700 text-base font-['SF_Pro_Display'] focus:outline-none" />
				<?php if ($current_search !== '') : ?>
					<a href="<?php echo esc_url($clear_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
				<?php endif; ?>
			</div>
		</form>
	</section>

	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10">
		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>
<?php get_footer(); ?>
