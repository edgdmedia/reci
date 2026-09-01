<?php
/**
 * Template Name: Dashboard — Feed
 *
 * The signed-in content home. Dashboard overview stays a summary; this is where
 * the full personalized feed lives.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user       = wp_get_current_user();
$personalized_posts = function_exists( 'reci_get_personalized_dashboard_posts' )
	? reci_get_personalized_dashboard_posts( $current_user->ID, 24 )
	: [];

$lead_post  = ! empty( $personalized_posts ) ? array_shift( $personalized_posts ) : null;
$settings_url = home_url( '/dashboard/settings/' );

/**
 * Post type label for a feed item.
 */
$feed_type_label = static function ( $post ): string {
	$object = get_post_type_object( get_post_type( $post ) );
	return $object && isset( $object->labels->singular_name )
		? (string) $object->labels->singular_name
		: (string) get_post_type( $post );
};

/**
 * Trimmed summary for a feed item.
 */
$feed_excerpt = static function ( $post, int $words ): string {
	$source = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( $post->post_content );
	return wp_trim_words( $source, $words, '…' );
};

get_header( 'dashboard' );
?>
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>

		<div class="flex-1 p-6 lg:p-10">
			<div class="max-w-6xl">
				<?php
				get_template_part(
					'template-parts/dashboard/page-header',
					null,
					[
						'title'    => 'Made for you',
						'subtitle' => 'Shaped by the collaborators and topics you follow. Adjust what shows up here any time in Settings.',
						'action'   => sprintf(
							'<a href="%s" class="btn btn-outline-primary btn-md">%s</a>',
							esc_url( $settings_url ),
							esc_html__( 'Manage interests', 'reci-media-hub' )
						),
					]
				);
				?>

				<?php if ( $lead_post ) : ?>

					<article class="mb-6 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
						<?php if ( has_post_thumbnail( $lead_post ) ) : ?>
							<a href="<?php echo esc_url( get_permalink( $lead_post ) ); ?>" class="block">
								<?php echo get_the_post_thumbnail( $lead_post, 'large', [ 'class' => 'h-64 w-full object-cover' ] ); ?>
							</a>
						<?php endif; ?>
						<div class="p-6 sm:p-8">
							<p class="text-xs font-semibold uppercase tracking-[0.14em] text-amber-700"><?php echo esc_html( $feed_type_label( $lead_post ) ); ?></p>
							<h2 class="mt-3 text-neutral-800 text-2xl font-bold font-serif leading-7">
								<a href="<?php echo esc_url( get_permalink( $lead_post ) ); ?>" class="transition-colors hover:text-amber-700"><?php echo esc_html( get_the_title( $lead_post ) ); ?></a>
							</h2>
							<p class="mt-3 max-w-3xl text-neutral-700 text-base leading-7"><?php echo esc_html( $feed_excerpt( $lead_post, 45 ) ); ?></p>
							<p class="mt-4 text-xs text-zinc-500"><?php echo esc_html( get_the_date( '', $lead_post ) ); ?></p>
						</div>
					</article>

					<?php if ( ! empty( $personalized_posts ) ) : ?>
						<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
							<?php foreach ( $personalized_posts as $feed_post ) : ?>
								<article class="flex flex-col rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
									<p class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php echo esc_html( $feed_type_label( $feed_post ) ); ?></p>
									<h2 class="mt-3 text-neutral-800 text-xl font-bold font-subhead leading-7">
										<a href="<?php echo esc_url( get_permalink( $feed_post ) ); ?>" class="transition-colors hover:text-amber-700"><?php echo esc_html( get_the_title( $feed_post ) ); ?></a>
									</h2>
									<p class="mt-3 flex-1 text-neutral-700 text-sm leading-6"><?php echo esc_html( $feed_excerpt( $feed_post, 26 ) ); ?></p>
									<p class="mt-4 text-xs text-zinc-500"><?php echo esc_html( get_the_date( '', $feed_post ) ); ?></p>
								</article>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				<?php else : ?>

					<div class="max-w-3xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8">
						<h2 class="text-neutral-800 text-2xl font-bold font-serif leading-7">Your feed is empty</h2>
						<p class="mt-3 text-neutral-700 text-base leading-7">Follow collaborators and pick the topics you care about, and new RECI content will collect here automatically.</p>
						<div class="mt-6 flex flex-wrap gap-3">
							<a href="<?php echo esc_url( $settings_url ); ?>" class="btn btn-primary btn-md">Choose your interests</a>
							<a href="<?php echo esc_url( home_url( '/community/' ) ); ?>" class="btn btn-outline-primary btn-md">Browse collaborators</a>
						</div>
					</div>

				<?php endif; ?>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
