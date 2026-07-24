<?php
/**
 * Generic archive fallback.
 *
 * Uses the reusable listing builder for post type archives,
 * and a safe default loop for all other archive types.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (is_post_type_archive()) {
	$post_type = get_query_var('post_type');
	if (is_array($post_type)) {
		$post_type = reset($post_type);
	}
	$post_type = is_string($post_type) ? $post_type : 'post';
	if (! post_type_exists($post_type)) {
		$post_type = 'post';
	}

	$post_type_object = get_post_type_object($post_type);
	$title            = $post_type_object && isset($post_type_object->labels->name) ? (string) $post_type_object->labels->name : ucfirst(str_replace(['reci_', '_'], ['', ' '], $post_type));
	$description      = $post_type_object && isset($post_type_object->description) ? (string) $post_type_object->description : '';

	$base_url = get_post_type_archive_link($post_type) ?: home_url('/');

	$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';
	$clear_url      = remove_query_arg(['search', 'paged'], $base_url);

	$listing_config = [
		'post_type'                => $post_type,
		'posts_per_page'           => 9,
		'orderby'                  => 'date',
		'order'                    => 'DESC',
		'listing_style'            => 'archive_grid_card',
		'wrapper_class'            => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
		'item_overrides'           => [
			'title_classes'   => "self-stretch justify-start text-neutral-800 text-2xl font-bold font-heading leading-7 line-clamp-3",
			'excerpt_classes' => "self-stretch justify-start text-neutral-500 text-sm font-normal leading-5 ",
		],
		'enable_pagination'        => true,
		'pagination_param'         => 'paged',
		'filter_search_param'      => 'search',
		'empty_message'            => 'No posts found.',
	];

	get_header();
	?>
	<main class="layout-page">
		<?php get_template_part('template-parts/common/page-title-card', null, [
			'title'    => $title,
			'subtitle' => $description,
		]); ?>

		<section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">
			<div class="self-stretch pb-5 border-b border-zinc-400">
				<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
					<div class="flex justify-start items-center gap-5 flex-wrap">
						<span class="text-neutral-800 text-base font-bold">Browse <?php echo esc_html(strtolower($title)); ?></span>
					</div>
					<div class="w-full sm:w-auto flex items-center gap-2.5">
						<div class="archive-filter-search-wrap" role="search">
							<svg class="w-4 h-4 flex-shrink-0 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
							<label for="archive-search" class="sr-only">Search archive</label>
							<input id="archive-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search" class="archive-filter-search-input" />
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
	<?php
	get_footer();
	return;
}

get_header();
?>
<main class="layout-page">
	<div class="reci-container py-14">
		<header class="mb-8">
			<h1 class="text-neutral-800 text-5xl font-bold font-heading"><?php the_archive_title(); ?></h1>
			<?php the_archive_description('<div class="text-neutral-500 text-lg mt-2">', '</div>'); ?>
		</header>

		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('mb-8 pb-8 border-b border-zinc-200'); ?>>
					<h2 class="text-2xl font-bold font-heading mb-2"><a href="<?php the_permalink(); ?>" class="text-neutral-800 hover:underline"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>
		<?php else : ?>
			<p class="text-neutral-500"><?php esc_html_e('No posts found.', 'reci-media-hub'); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
