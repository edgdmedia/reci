<?php

/**
 * Generic taxonomy archive.
 *
 * The router looks for templates/taxonomy/taxonomy-{taxonomy}.php first and
 * falls back here. Only four taxonomies had a template of their own, so every
 * other term archive — subject areas, affiliations, categories, tags, practice
 * focus, SDGs, target audiences — dropped through to WordPress's default and
 * looked nothing like the rest of the site.
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

$taxonomy        = $term ? $term->taxonomy : (string) get_query_var('taxonomy');
$taxonomy_object = $taxonomy ? get_taxonomy($taxonomy) : null;

// Post types come from the taxonomy's own registration rather than a fixed
// list. Subject Areas and Affiliations belong to collaborators, so a hardcoded
// content list would have returned nothing for them.
$post_types = $taxonomy_object && ! empty($taxonomy_object->object_type)
	? (array) $taxonomy_object->object_type
	: ['post'];

$page_title  = $term ? $term->name : ($taxonomy_object->labels->name ?? __('Archive', 'reci-media-hub'));
$description = $term ? term_description($term->term_id, $taxonomy) : '';

$base_url = $term ? get_term_link($term) : home_url('/');
if (is_wp_error($base_url)) {
	$base_url = home_url('/');
}

$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';

// Post type filter. Only types that actually have something under this term are
// offered, so the dropdown never leads to an empty page.
$type_options = [];

if ($term) {
	foreach ($post_types as $candidate) {
		$object = get_post_type_object($candidate);

		if (! $object) {
			continue;
		}

		$probe = new WP_Query([
			'post_type'              => $candidate,
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => [[
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => [(int) $term->term_id],
			]],
		]);

		if ($probe->found_posts > 0) {
			$type_options[$candidate] = [
				'label' => (string) ($object->labels->name ?? $candidate),
				'count' => (int) $probe->found_posts,
			];
		}
	}
}

$current_type = isset($_GET['type']) ? sanitize_key((string) wp_unslash($_GET['type'])) : '';
if ('' !== $current_type && ! isset($type_options[$current_type])) {
	$current_type = '';
}

$clear_url = remove_query_arg(['search', 'type', 'paged'], $base_url);
$search_id      = 'taxonomy-search-' . sanitize_html_class($taxonomy ?: 'term');

$singular = $taxonomy_object->labels->singular_name ?? __('term', 'reci-media-hub');

$listing_config = [
	'post_type'           => '' !== $current_type ? [$current_type] : $post_types,
	'posts_per_page'      => 9,
	'orderby'             => 'date',
	'order'               => 'DESC',
	'listing_style'       => 'archive_grid_card',
	'wrapper_class'       => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
	'item_overrides'      => [
		'title_classes'   => 'self-stretch justify-start text-neutral-800 text-2xl font-bold font-serif leading-7 line-clamp-3',
		'excerpt_classes' => 'self-stretch justify-start text-neutral-500 text-sm font-normal leading-5 ',
	],
	'enable_pagination'   => true,
	'pagination_param'    => 'paged',
	'filter_search_param' => 'search',
	'empty_message'       => sprintf(
		/* translators: %s: taxonomy singular name, lowercased. */
		__('No content found for this %s.', 'reci-media-hub'),
		strtolower((string) $singular)
	),
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
<main class="layout-page">
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $description,
		'eyebrow'  => $taxonomy_object->labels->singular_name ?? '',
	]); ?>

	<section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold"><?php esc_html_e('Filter by:', 'reci-media-hub'); ?></span>

					<?php if (count($type_options) > 1) : ?>
						<?php // Only worth showing when the term spans more than one type. ?>
						<label for="taxonomy-type" class="sr-only"><?php esc_html_e('Content type', 'reci-media-hub'); ?></label>
						<select id="taxonomy-type" name="type" class="archive-filter-select">
							<option value=""><?php esc_html_e('All types', 'reci-media-hub'); ?></option>
							<?php foreach ($type_options as $type_slug => $type_data) : ?>
								<option value="<?php echo esc_attr($type_slug); ?>" <?php selected($current_type, $type_slug); ?>>
									<?php echo esc_html(sprintf('%s (%d)', $type_data['label'], $type_data['count'])); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</div>
				<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="archive-filter-search-wrap" role="search">
						<svg class="archive-filter-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="<?php echo esc_attr($search_id); ?>" class="sr-only"><?php esc_html_e('Search', 'reci-media-hub'); ?></label>
						<input id="<?php echo esc_attr($search_id); ?>" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="<?php esc_attr_e('Search', 'reci-media-hub'); ?>" class="archive-filter-search-input" />
					</div>
					<?php if ($current_search !== '') : ?>
						<a href="<?php echo esc_url($clear_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900"><?php esc_html_e('Reset', 'reci-media-hub'); ?></a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>
<?php get_footer(); ?>
