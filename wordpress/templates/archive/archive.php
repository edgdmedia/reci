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
			'title_classes'   => "self-stretch justify-start text-neutral-800 text-2xl font-bold font-['EB_Garamond'] leading-7 line-clamp-3",
			'excerpt_classes' => "self-stretch justify-start text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-5 tracking-tight",
		],
		'enable_pagination'        => true,
		'pagination_param'         => 'paged',
		'filter_search_param'      => 'search',
		'pagination_wrapper_class' => 'mt-10 flex items-center justify-center gap-2',
		'pagination_item_class'    => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg border border-zinc-300 text-sm font-medium font-['SF_Pro_Display'] text-neutral-800 hover:bg-zinc-100",
		'pagination_current_class' => "inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-lg bg-[#003594] text-sm font-medium font-['SF_Pro_Display'] text-white",
		'empty_message'            => 'No posts found.',
	];

	get_header();
	?>
	<main class="bg-slate-100 min-h-screen">
		<section class="w-full bg-white border-b border-zinc-400">
			<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 flex flex-col lg:flex-row justify-between gap-6">
				<div class="flex items-center gap-3">
					<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
					<h1 class="text-neutral-800 text-5xl font-medium font-['EB_Garamond']"><?php echo esc_html($title); ?></h1>
				</div>
				<?php if ($description !== '') : ?>
					<p class="max-w-xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display']"><?php echo esc_html($description); ?></p>
				<?php endif; ?>
			</div>
		</section>

		<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-6 border-b border-zinc-300">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="flex items-center justify-between gap-4 flex-wrap" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="text-neutral-700 text-sm font-medium font-['SF_Pro_Display']">Browse <?php echo esc_html(strtolower($title)); ?></div>
				<div class="flex items-center gap-2 w-full sm:w-auto">
					<label for="archive-search-runtime" class="sr-only">Search archive</label>
					<input id="archive-search-runtime" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search" class="w-full sm:w-80 px-4 py-3 rounded-lg border border-zinc-300 bg-white text-neutral-700 text-base font-['SF_Pro_Display'] focus:outline-none" />
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
	<?php
	get_footer();
	return;
}

get_header();
?>
<main class="reci-wrap" style="padding-top:40px;padding-bottom:40px;">
	<header style="margin-bottom:32px;">
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description('<div>', '</div>'); ?>
	</header>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom:32px;">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e('No posts found.', 'reci-media-hub'); ?></p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
