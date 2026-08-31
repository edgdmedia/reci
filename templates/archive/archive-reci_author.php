<?php
/**
 * Archive for Collaborators (reci_author).
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$page_title    = 'Collaborators';
$page_subtitle = 'Meet the voices behind RECI — researchers, practitioners, and community leaders advancing equity and justice.';

$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';
$current_focus  = isset($_GET['focus']) ? sanitize_title((string) wp_unslash($_GET['focus'])) : '';
$current_affiliation = isset($_GET['affiliation']) ? sanitize_title((string) wp_unslash($_GET['affiliation'])) : '';
$base_url       = get_post_type_archive_link('reci_author') ?: home_url('/collaborators/');
$all_filters_url = remove_query_arg(['search', 'focus', 'affiliation', 'paged'], $base_url);
$has_filters    = $current_search !== '' || $current_focus !== '' || $current_affiliation !== '';

// Ordered by how many collaborators hold each subject, so the dozen that
// actually describe the directory sit at the top of a 47-term vocabulary.
$focus_terms = get_terms([
	'taxonomy'   => 'reci_expertise',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
]);
if (is_wp_error($focus_terms)) {
	$focus_terms = [];
}

$affiliation_terms = get_terms([
	'taxonomy'   => 'reci_affiliation',
	'hide_empty' => true,
]);
if (is_wp_error($affiliation_terms)) {
	$affiliation_terms = [];
}

$paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

$query_args = [
	'post_type'      => 'reci_author',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => $paged,
	'orderby'        => 'title',
	'order'          => 'ASC',
];

if ($current_search !== '') {
	$query_args['s'] = $current_search;
}

$query_args['tax_query'] = ['relation' => 'AND'];
if ($current_focus !== '') {
	$query_args['tax_query'][] = [
		'taxonomy' => 'reci_expertise',
		'field'    => 'slug',
		'terms'    => [$current_focus],
	];
}
if ($current_affiliation !== '') {
	$query_args['tax_query'][] = [
		'taxonomy' => 'reci_affiliation',
		'field'    => 'slug',
		'terms'    => [$current_affiliation],
	];
}
if (count($query_args['tax_query']) === 1) {
	unset($query_args['tax_query']);
}

$author_query = new WP_Query($query_args);

get_header();
?>
<main class="layout-page">
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $page_subtitle,
	]); ?>

	<section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold">Filter by:</span>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
					<div>
						<label for="author-focus" class="mb-2 block text-sm font-medium text-neutral-700">Research or Work Focus</label>
						<select id="author-focus" name="focus" class="w-full rounded-lg border border-zinc-300 px-4 py-3">
							<option value="">- Any -</option>
							<?php foreach ($focus_terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_focus, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<label for="author-affiliation" class="mb-2 block text-sm font-medium text-neutral-700">Affiliation</label>
						<select id="author-affiliation" name="affiliation" class="w-full rounded-lg border border-zinc-300 px-4 py-3">
							<option value="">- Any -</option>
							<?php foreach ($affiliation_terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_affiliation, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="archive-filter-search-wrap" role="search">
						<svg class="w-4 h-4 flex-shrink-0 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="author-search" class="sr-only">Search collaborators</label>
						<input id="author-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search Collaborators" class="archive-filter-search-input" />
					</div>
					<?php if ($has_filters) : ?>
						<a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
					<?php endif; ?>
					</div>
				</div>
			</form>
		</div>

		<?php if ($author_query->have_posts()) : ?>
			<div class="self-stretch grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
				<?php while ($author_query->have_posts()) : $author_query->the_post();
					$profile = reci_media_hub_get_author_profile_data(get_the_ID());
				?>
					<a href="<?php the_permalink(); ?>" class="group flex flex-col items-center text-center gap-4 p-8 rounded-xl bg-white border border-zinc-200 hover:border-zinc-400 transition-colors no-underline">
						<?php if (! empty($profile['image_url'])) : ?>
							<img src="<?php echo esc_url($profile['image_url']); ?>" alt="<?php echo esc_attr($profile['image_alt']); ?>" class="w-28 h-28 rounded-full object-cover" />
						<?php else : ?>
							<div class="w-28 h-28 rounded-full bg-zinc-200 flex items-center justify-center">
								<span class="text-zinc-400 text-3xl font-bold font-heading"><?php echo esc_html(substr(get_the_title(), 0, 2)); ?></span>
							</div>
						<?php endif; ?>
						<div class="flex flex-col gap-1">
							<h2 class="text-neutral-800 text-xl font-bold font-heading group-hover:text-[#003594] transition-colors"><?php the_title(); ?></h2>
							<?php if (! empty($profile['title'])) : ?>
								<p class="text-neutral-500 text-sm font-medium"><?php echo esc_html($profile['title']); ?></p>
							<?php endif; ?>
						</div>
					</a>
				<?php endwhile; ?>
			</div>

			<div class="self-stretch mt-8 flex items-center justify-center gap-2">
				<?php
				echo paginate_links([
					'total'     => $author_query->max_num_pages,
					'current'   => $paged,
					'format'    => '?paged=%#%',
					'base'      => $current_search !== '' ? add_query_arg('search', $current_search, get_pagenum_link(1)) : get_pagenum_link(1),
					'prev_text' => '<span class="inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100">&laquo;</span>',
					'next_text' => '<span class="inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100">&raquo;</span>',
					'before_page_number' => '<span class="inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100">',
					'after_page_number'  => '</span>',
					'mid_size'  => 2,
				]);
				?>
			</div>
		<?php else : ?>
			<p class="self-stretch text-center text-neutral-500 text-lg py-20"><?php esc_html_e('No collaborators found.', 'reci-media-hub'); ?></p>
		<?php endif; ?>
	</section>
</main>
<?php
wp_reset_postdata();
get_footer();
