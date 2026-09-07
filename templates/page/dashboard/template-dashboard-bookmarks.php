<?php
/**
 * Template Name: Dashboard — Bookmarks
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user  = wp_get_current_user();
$bookmark_data = reci_get_user_bookmarks( $current_user->ID );
$post_ids      = ! empty( $bookmark_data ) ? array_column( $bookmark_data, 'post_id' ) : [];

$bookmarked_posts = ! empty( $post_ids )
	? get_posts( [
		'post__in'       => $post_ids,
		'post_type'      => 'any',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'orderby'        => 'post__in',
	] )
	: [];

get_header('dashboard');
?>
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>
		<div class="flex-1 p-6 lg:p-10">
			<?php
			get_template_part(
				'template-parts/dashboard/page-header',
				null,
				[ 'title' => 'Bookmarks', 'subtitle' => 'Everything you have saved, in one place.' ]
			);
			?>

			<?php if ( empty( $bookmarked_posts ) ) : ?>
			<p class="text-zinc-500">You haven't bookmarked any content yet. Browse the site and click the bookmark icon to save posts.</p>
			<?php else : ?>
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
				<?php foreach ( $bookmarked_posts as $post ) : ?>
				<?php $thumb = get_the_post_thumbnail_url( $post, 'thumbnail' ); ?>
				<div class="bg-white border border-zinc-200 rounded-xl overflow-hidden hover:shadow-sm transition-shadow">
					<?php if ( $thumb ) : ?>
					<div class="aspect-[16/9] bg-zinc-100 overflow-hidden">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="w-full h-full object-cover" loading="lazy">
					</div>
					<?php endif; ?>
					<div class="p-4">
						<p class="text-xs text-zinc-500 uppercase tracking-wider mb-1"><?php echo esc_html( get_post_type_object( get_post_type( $post ) )->labels->singular_name ?? get_post_type( $post ) ); ?></p>
						<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="text-sm font-semibold text-zinc-800 hover:text-amber-700 transition-colors line-clamp-2"><?php echo esc_html( get_the_title( $post ) ); ?></a>
						<button class="reci-bookmark-btn text-xs text-amber-600 hover:text-amber-700 mt-2 inline-flex items-center gap-1"
								data-post-id="<?php echo esc_attr( $post->ID ); ?>"
								data-bookmarked="1">
							<span class="bookmark-icon">★</span>
							<span class="bookmark-label">Saved</span>
						</button>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
