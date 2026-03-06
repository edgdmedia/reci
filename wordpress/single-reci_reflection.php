<?php
/**
 * Single template for the reci_reflection post type.
 *
 * Wired to real WordPress data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$post_id = get_the_ID();

// Meta.
$speaker     = (string) get_post_meta( $post_id, '_reci_reflection_speaker', true );
$role        = (string) get_post_meta( $post_id, '_reci_reflection_role', true );
$quote_text  = (string) get_post_meta( $post_id, '_reci_reflection_quote', true );

// Fallbacks.
$speaker    = $speaker ?: get_the_title();
$quote_text = $quote_text ?: ( has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 40, '...' ) );

// Category / topic badge.
$category   = '';
$topic_terms = wp_get_post_terms( $post_id, 'reci_topic', [ 'fields' => 'names' ] );
if ( ! is_wp_error( $topic_terms ) && ! empty( $topic_terms ) ) {
	$category = $topic_terms[0];
} else {
	$cat_terms = wp_get_post_terms( $post_id, 'category', [ 'fields' => 'names' ] );
	if ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) ) {
		$category = $cat_terms[0];
	}
}

// Related reflections.
$related_reflections = [];
$related_query = new WP_Query(
	[
		'post_type'      => 'reci_reflection',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => [ $post_id ],
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
foreach ( $related_query->posts as $rel_post ) {
	$rel_id       = (int) $rel_post->ID;
	$rel_speaker  = (string) get_post_meta( $rel_id, '_reci_reflection_speaker', true ) ?: get_the_title( $rel_id );
	$rel_quote    = (string) get_post_meta( $rel_id, '_reci_reflection_quote', true )
		?: wp_trim_words( has_excerpt( $rel_id ) ? get_the_excerpt( $rel_id ) : wp_strip_all_tags( $rel_post->post_content ), 18, '...' );
	$rel_cat      = '';
	$rel_topics   = wp_get_post_terms( $rel_id, 'reci_topic', [ 'fields' => 'names' ] );
	if ( ! is_wp_error( $rel_topics ) && ! empty( $rel_topics ) ) {
		$rel_cat = $rel_topics[0];
	} else {
		$rel_cats = wp_get_post_terms( $rel_id, 'category', [ 'fields' => 'names' ] );
		if ( ! is_wp_error( $rel_cats ) && ! empty( $rel_cats ) ) {
			$rel_cat = $rel_cats[0];
		}
	}
	$related_reflections[] = [
		'name'      => $rel_speaker,
		'quote'     => $rel_quote,
		'category'  => $rel_cat,
		'image_url' => get_the_post_thumbnail_url( $rel_id, 'medium' ) ?: 'https://placehold.co/387x300',
		'image_alt' => $rel_speaker . ' reflection',
		'link_url'  => get_permalink( $rel_id ),
	];
}

// Archive link for breadcrumb.
$archive_url   = get_post_type_archive_link( 'reci_reflection' ) ?: home_url( '/reflection-gallery/' );
$archive_label = __( 'Reflection Gallery', 'reci-media-hub' );

get_header();
?>

<div class="bg-slate-100 min-h-screen font-['SF_Pro_Display']">

	<!-- Hero: Large Quote Display -->
	<div class="relative bg-neutral-800/90 min-h-[750px] flex flex-col justify-end px-4 sm:px-6 lg:px-12 xl:px-20 py-14">
		<div class="max-w-[1440px] mx-auto w-full flex flex-col gap-8">

			<!-- Breadcrumb -->
			<nav class="flex items-center gap-2 text-zinc-400 text-sm font-normal" aria-label="<?php esc_attr_e( 'Breadcrumb', 'reci-media-hub' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-white transition-colors">
					<?php esc_html_e( 'Home', 'reci-media-hub' ); ?>
				</a>
				<span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="hover:text-white transition-colors">
					<?php echo esc_html( $archive_label ); ?>
				</a>
				<span aria-hidden="true">/</span>
				<span class="text-white"><?php echo esc_html( $speaker ); ?></span>
			</nav>

			<!-- Quote block -->
			<div class="flex flex-col gap-4 max-w-3xl">
				<div class="w-20 h-20 bg-white/10 rounded-lg flex items-center justify-center">
					<svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M0 32V19.2C0 14.933 1.2 11 3.6 7.4C6 3.8 9.6 1.2 14.4 0L16.8 3.6C12.8 4.933 10 6.867 8.4 9.4C6.8 11.933 6.133 14.4 6.4 16.8H12.8V32H0ZM23.2 32V19.2C23.2 14.933 24.4 11 26.8 7.4C29.2 3.8 32.8 1.2 37.6 0L40 3.6C36 4.933 33.2 6.867 31.6 9.4C30 11.933 29.333 14.4 29.6 16.8H36V32H23.2Z" fill="#F59E0B"/>
					</svg>
				</div>
				<blockquote class="text-white text-4xl lg:text-5xl font-medium font-['EB_Garamond'] leading-tight tracking-wide">
					<?php echo esc_html( $quote_text ); ?>
				</blockquote>
			</div>

			<!-- Author attribution -->
			<div class="border-t border-white/30 pt-6 max-w-3xl">
				<div class="flex items-center gap-4 flex-wrap">
					<p class="text-white text-2xl font-normal font-['EB_Garamond'] leading-10 tracking-tight">
						<?php echo esc_html( $speaker ); ?>
					</p>
					<?php if ( $role ) : ?>
						<span class="text-zinc-300 text-base font-normal font-['SF_Pro_Display']"><?php echo esc_html( $role ); ?></span>
					<?php endif; ?>
					<?php if ( $category ) : ?>
						<span class="px-2 py-1 bg-amber-400 rounded text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4">
							<?php echo esc_html( $category ); ?>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Context / Commentary Section -->
	<div class="bg-neutral-800">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">

			<!-- Section heading -->
			<div class="border-t border-zinc-600 pt-10 mb-10">
				<div class="flex items-center justify-center gap-3">
					<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
					<h2 class="text-white text-3xl font-bold font-['EB_Garamond'] leading-10">
						<?php the_title(); ?>
					</h2>
				</div>
			</div>
			<div class="border-t border-zinc-600 mb-16"></div>

			<!-- Post content -->
			<div class="text-white font-['EB_Garamond'] reci-reflection-content max-w-3xl mx-auto">
				<?php the_content(); ?>
			</div>
		</div>
	</div>

	<!-- Related Reflections -->
	<?php if ( ! empty( $related_reflections ) ) : ?>
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">
			<div class="flex items-center gap-3 mb-10">
				<div class="w-3 h-3 bg-amber-400 rounded-sm"></div>
				<h2 class="text-neutral-800 text-3xl font-bold font-['EB_Garamond'] leading-10">
					<?php esc_html_e( 'Related Reflections', 'reci-media-hub' ); ?>
				</h2>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
				<?php foreach ( $related_reflections as $related ) : ?>
					<?php $card_tag = $related['link_url'] ? 'a' : 'div'; ?>
					<<?php echo esc_attr( $card_tag ); ?>
						<?php if ( $related['link_url'] ) : ?>href="<?php echo esc_url( $related['link_url'] ); ?>"<?php endif; ?>
						class="bg-white rounded-lg overflow-hidden flex flex-col shadow-sm<?php echo $related['link_url'] ? ' hover:shadow-md transition-shadow' : ''; ?>"
					>
						<img
							src="<?php echo esc_url( $related['image_url'] ); ?>"
							alt="<?php echo esc_attr( $related['image_alt'] ); ?>"
							class="w-full h-48 object-cover"
						/>
						<div class="flex flex-col flex-1 p-6 gap-4">
							<div class="w-8 h-8 bg-amber-400 rounded-sm flex items-center justify-center flex-shrink-0">
								<svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M0 11V6.6C0 5.133 0.35 3.817 1.05 2.65C1.75 1.483 2.8 0.617 4.2 0.05L4.9 1.3C3.733 1.717 2.917 2.35 2.45 3.2C1.983 4.05 1.783 4.867 1.85 5.65H3.5V11H0ZM6.65 11V6.6C6.65 5.133 7 3.817 7.7 2.65C8.4 1.483 9.45 0.617 10.85 0.05L11.55 1.3C10.383 1.717 9.567 2.35 9.1 3.2C8.633 4.05 8.433 4.867 8.5 5.65H10.15V11H6.65Z" fill="#1F2937"/>
								</svg>
							</div>
							<blockquote class="text-neutral-800 text-base font-medium font-['EB_Garamond'] leading-6 flex-1 line-clamp-3">
								<?php echo esc_html( $related['quote'] ); ?>
							</blockquote>
							<div class="border-t border-zinc-200 pt-4 flex items-center justify-between">
								<span class="text-neutral-800 text-sm font-semibold font-['SF_Pro_Display']">
									<?php echo esc_html( $related['name'] ); ?>
								</span>
								<?php if ( $related['category'] ) : ?>
									<span class="px-2 py-1 bg-slate-100 rounded text-neutral-500 text-xs font-normal font-['SF_Pro_Display']">
										<?php echo esc_html( $related['category'] ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>
					</<?php echo esc_attr( $card_tag ); ?>>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
