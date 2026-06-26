<?php
/**
 * Show taxonomy archive template.
 *
 * Displays the show landing page with banner, about section,
 * and filterable podcast listing.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$term = get_queried_object();
if (! $term instanceof WP_Term) {
	wp_safe_redirect(home_url('/'));
	exit;
}

$page_title  = $term->name;
$description = term_description($term->term_id, 'reci_show');
$base_url    = get_term_link($term);
if (is_wp_error($base_url)) {
	$base_url = home_url('/show/' . $term->slug . '/');
}

$owner_id = (int) get_term_meta($term->term_id, 'reci_show_owner', true);
$owner_name  = '';
$owner_title = '';
if ($owner_id > 0 && function_exists('reci_media_hub_get_author_profile_data')) {
	$author_data = reci_media_hub_get_author_profile_data($owner_id);
	$owner_name  = (string) ($author_data['name'] ?? get_the_title($owner_id));
	$owner_title = (string) ($author_data['title'] ?? '');
}
$image_id  = (int) get_term_meta($term->term_id, 'reci_show_image_id', true);
$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';

/* ── Category (topic) and sphere terms for filter dropdowns ── */
$topic_terms = get_terms([
	'taxonomy'   => 'category',
	'hide_empty' => true,
]);
if (is_wp_error($topic_terms)) {
	$topic_terms = [];
}

$sphere_terms_filter = get_terms([
	'taxonomy'   => 'reci_sphere',
	'hide_empty' => true,
]);
if (is_wp_error($sphere_terms_filter)) {
	$sphere_terms_filter = [];
}

$current_topic  = isset($_GET['topic']) ? sanitize_title((string) wp_unslash($_GET['topic'])) : '';
$current_sphere = isset($_GET['sphere']) ? sanitize_title((string) wp_unslash($_GET['sphere'])) : '';

/* ── Listing config ── */
$listing_config = [
	'post_type'                => ['reci_podcast'],
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
	'filter_taxonomies'        => [
		'category' => [
			'param' => 'topic',
			'field' => 'slug',
		],
		'reci_sphere' => [
			'param' => 'sphere',
			'field' => 'slug',
		],
	],
	'tax_query'                => [
		[
			'taxonomy' => 'reci_show',
			'field'    => 'term_id',
			'terms'    => [(int) $term->term_id],
		],
	],
	'pagination_wrapper_class' => 'mt-10 flex items-center justify-center gap-2',
	'pagination_item_class'    => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100",
	'pagination_current_class' => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg bg-[#003594] text-sm font-medium text-white",
	'empty_message'            => 'No episodes found for this show.',
];

get_header();
?>
<main class="layout-page">
	<div class="reci-container-full border-b border-zinc-400">
		<div class="reci-container py-14">
			<div class="flex flex-col md:flex-row justify-start md:justify-between items-center gap-6">
				<div class="flex items-center gap-3 w-full md:w-2/5 lg:w-1/2">
					<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
					<h1 class="text-neutral-800 text-5xl font-bold font-heading leading-[1.05]"><?php echo esc_html($page_title); ?></h1>
				</div>
				<?php if ($owner_name !== '') : ?>
					<div class="lg:pl-10 lg:border-l lg:border-zinc-400 w-full md:w-3/5 lg:w-1/2 flex flex-col gap-2">
						<p class="text-neutral-800 text-xl font-bold leading-7"><?php echo esc_html($owner_name); ?></p>
						<?php if ($owner_title !== '') : ?>
							<p class="text-neutral-600 text-lg font-normal leading-7"><?php echo esc_html($owner_title); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ($image_url) : ?>
		<div class="reci-container-full">
			<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($page_title); ?>" class="w-full h-96 object-cover" />
		</div>
	<?php endif; ?>

	<?php if ($description !== '') : ?>
		<div class="reci-container-full border-b border-zinc-400">
			<div class="reci-container py-14">
				<div class="flex flex-col gap-10">
					<div class="inline-flex items-center gap-2">
						<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
						<h2 class="text-neutral-700 text-2xl font-bold font-subhead"><?php esc_html_e('About', 'reci-media-hub'); ?></h2>
					</div>
					<div class="w-full h-px bg-zinc-300"></div>
					<div class="text-neutral-600 text-lg font-normal leading-7 max-w-prose">
						<?php echo wp_kses_post($description); ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold"><?php esc_html_e('Filter by:', 'reci-media-hub'); ?></span>
					<div class="archive-filter-select-wrap">
						<label for="show-topic-filter" class="sr-only"><?php esc_html_e('Filter by topic', 'reci-media-hub'); ?></label>
						<select id="show-topic-filter" name="topic" class="archive-filter-select" aria-label="<?php esc_attr_e('Filter by topic', 'reci-media-hub'); ?>">
							<option value=""><?php esc_html_e('All Topics', 'reci-media-hub'); ?></option>
							<?php foreach ($topic_terms as $term_item) : ?>
								<option value="<?php echo esc_attr($term_item->slug); ?>" <?php selected($current_topic, $term_item->slug); ?>><?php echo esc_html($term_item->name); ?></option>
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
							<label for="show-sphere-filter" class="sr-only"><?php esc_html_e('Filter by sphere', 'reci-media-hub'); ?></label>
							<select id="show-sphere-filter" name="sphere" class="archive-filter-select" aria-label="<?php esc_attr_e('Filter by sphere', 'reci-media-hub'); ?>">
								<option value=""><?php esc_html_e('All Spheres', 'reci-media-hub'); ?></option>
								<?php foreach ($sphere_terms_filter as $st) : ?>
									<option value="<?php echo esc_attr($st->slug); ?>" <?php selected($current_sphere, $st->slug); ?>><?php echo esc_html($st->name); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="archive-filter-chevron">
								<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
								</svg>
							</span>
						</div>
					<?php endif; ?>
				</div>
				<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="archive-filter-search-wrap" role="search">
						<svg class="archive-filter-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="show-search" class="sr-only"><?php esc_html_e('Search', 'reci-media-hub'); ?></label>
						<input id="show-search" type="search" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : ''); ?>" placeholder="<?php esc_attr_e('Search', 'reci-media-hub'); ?>" class="archive-filter-search-input" />
					</div>
				</div>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>
<?php get_footer(); ?>
