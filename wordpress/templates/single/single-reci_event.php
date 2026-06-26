<?php
/**
 * Single Event template (reci_event post type).
 *
 * Wired to real WordPress event meta fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$post_id = get_the_ID();
$display_author = reci_media_hub_get_display_author($post_id);

// Meta.
$start_date_raw   = (string) get_post_meta( $post_id, '_reci_event_start_date', true );
$end_date_raw     = (string) get_post_meta( $post_id, '_reci_event_end_date', true );
$start_time_raw   = (string) get_post_meta( $post_id, '_reci_event_start_time', true );
$end_time_raw     = (string) get_post_meta( $post_id, '_reci_event_end_time', true );
$timezone_raw     = (string) get_post_meta( $post_id, '_reci_event_timezone', true );
$location_name    = (string) get_post_meta( $post_id, '_reci_event_location_name', true );
$location_address = (string) get_post_meta( $post_id, '_reci_event_location_address', true );
$location         = trim( $location_name . ( $location_address ? ' - ' . $location_address : '' ) );
$cta_label        = (string) get_post_meta( $post_id, '_reci_event_cta_label', true ) ?: __( 'Register', 'reci-media-hub' );
$registration_url = (string) get_post_meta( $post_id, '_reci_event_registration_url', true );

$start_ts       = $start_date_raw ? strtotime( $start_date_raw ) : false;
$end_ts         = $end_date_raw ? strtotime( $end_date_raw ) : $start_ts;
$formatted_date = $start_ts ? wp_date( 'l, F j, Y', $start_ts ) : get_the_date( 'l, F j, Y' );
$formatted_time = trim( $start_time_raw . ( $timezone_raw ? ' ' . $timezone_raw : '' ) );
if ( $end_time_raw ) {
	$formatted_time = trim( $start_time_raw . ' - ' . $end_time_raw . ( $timezone_raw ? ' ' . $timezone_raw : '' ) );
}

$status         = 'upcoming';
$today_midnight = strtotime( 'today midnight' );
if ( $start_ts && $end_ts && $start_ts <= $today_midnight && $today_midnight <= $end_ts ) {
	$status = 'live';
} elseif ( $end_ts && $end_ts < $today_midnight ) {
	$status = 'past';
}

$status_label = ucfirst( $status );
$status_class = 'bg-amber-400 text-neutral-800';
if ( 'past' === $status ) {
	$status_class = 'bg-zinc-400 text-white';
} elseif ( 'live' === $status ) {
	$status_class = 'bg-green-500 text-white';
}

// Featured image.
$thumb_id             = get_post_thumbnail_id( $post_id );
$image_url            = get_the_post_thumbnail_url( $post_id, 'large' ) ?: 'https://placehold.co/1200x500';
$image_alt            = $thumb_id ? ( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title() ) : get_the_title();
$author_card_heading  = __( 'Event Organizer', 'reci-media-hub' );

// Tags / topics.
$raw_tags = wp_get_post_terms( $post_id, 'reci_topic', [ 'fields' => 'names' ] );
if ( is_wp_error( $raw_tags ) || empty( $raw_tags ) ) {
	$raw_tags = wp_get_post_terms( $post_id, 'post_tag', [ 'fields' => 'names' ] );
}
$tags = is_wp_error( $raw_tags ) ? [] : array_slice( $raw_tags, 0, 4 );

// Related events.
$related_events = [];
$related_query  = new WP_Query(
	[
		'post_type'      => 'reci_event',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => [ $post_id ],
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
foreach ( $related_query->posts as $rel_post ) {
	$rel_id          = (int) $rel_post->ID;
	$rel_date_raw    = (string) get_post_meta( $rel_id, '_reci_event_start_date', true );
	$rel_end_date_raw = (string) get_post_meta( $rel_id, '_reci_event_end_date', true );
	$rel_ts          = $rel_date_raw ? strtotime( $rel_date_raw ) : false;
	$rel_end_ts      = $rel_end_date_raw ? strtotime( $rel_end_date_raw ) : $rel_ts;
	$rel_time_raw    = (string) get_post_meta( $rel_id, '_reci_event_start_time', true );
	$rel_tz          = (string) get_post_meta( $rel_id, '_reci_event_timezone', true );
	$rel_registration = (string) get_post_meta( $rel_id, '_reci_event_registration_url', true );

	$rel_status = 'upcoming';
	$rel_today_midnight = strtotime( 'today midnight' );
	if ( $rel_ts && $rel_end_ts && $rel_ts <= $rel_today_midnight && $rel_today_midnight <= $rel_end_ts ) {
		$rel_status = 'live';
	} elseif ( $rel_end_ts && $rel_end_ts < $rel_today_midnight ) {
		$rel_status = 'past';
	}

	$related_events[] = [
		'status'       => $rel_status ? ucfirst( $rel_status ) : 'Upcoming',
		'date'         => $rel_ts ? wp_date( 'M j, Y', $rel_ts ) : get_the_date( 'M j, Y', $rel_id ),
		'time'         => trim( $rel_time_raw . ( $rel_tz ? ' ' . $rel_tz : '' ) ),
		'datetime_iso' => $rel_ts ? date( DATE_ATOM, $rel_ts ) : '',
		'title'        => get_the_title( $rel_id ),
		'excerpt'      => wp_trim_words( has_excerpt( $rel_id ) ? get_the_excerpt( $rel_id ) : wp_strip_all_tags( $rel_post->post_content ), 14, '...' ),
		'button_label' => 'View Event',
		'link_url'     => get_permalink( $rel_id ),
		'image_url'    => get_the_post_thumbnail_url( $rel_id, 'medium' ) ?: 'https://placehold.co/400x225',
		'image_alt'    => get_the_title( $rel_id ),
	];
}

get_header();
?>

<main class="layout">
	<section class="reci-container">
		<div class="border-l-0 lg:border-l-[0.50px] border-b-[0.50px] border-zinc-300 flex flex-col lg:flex-row justify-start items-start gap-10">

			<!-- ── LEFT: Event content ───────────────────────────────────── -->
			<div class="w-full pl-0 lg:px-10 self-stretch py-14 flex flex-col justify-start items-start gap-10 lg:border-r-[0.50px] border-zinc-300">

				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<div class="self-stretch flex flex-col justify-start items-start gap-2">
						<div class="self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap">
							<span class="px-2 py-1 rounded text-xs font-medium <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status_label ); ?></span>
							<div class="tag-dot"></div>
							<span class="text-neutral-500 text-sm font-normal leading-4"><?php echo esc_html( $formatted_date ); ?></span>
						<?php if ( $formatted_time ) : ?>
							<div class="tag-dot"></div>
							<span class="text-neutral-500 text-sm font-normal leading-4"><?php echo esc_html( $formatted_time ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $display_author['name'] ) ) : ?>
							<div class="tag-dot"></div>
							<?php if ( ! empty( $display_author['permalink'] ) ) : ?>
								<a href="<?php echo esc_url( (string) $display_author['permalink'] ); ?>" class="text-neutral-500 text-sm font-medium leading-4 no-underline hover:underline">
									<?php echo esc_html( (string) $display_author['name'] ); ?>
								</a>
							<?php else : ?>
								<span class="text-neutral-500 text-sm font-medium leading-4"><?php echo esc_html( (string) $display_author['name'] ); ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</div>
						<div class="self-stretch flex flex-col justify-start items-start gap-5">
							<h1 class="self-stretch reci-single-title"><?php the_title(); ?></h1>
						</div>
					</div>
					<?php if ( ! empty( $tags ) ) : ?>
						<div class="inline-flex justify-start items-center gap-2 flex-wrap">
							<?php foreach ( $tags as $tag ) : ?>
								<div class="px-2 py-1 bg-gray-200 rounded flex justify-center items-center gap-2.5">
									<span class="text-neutral-500 text-sm font-normal leading-4"><?php echo esc_html( $tag ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<img class="self-stretch rounded-lg w-full h-[350px] object-cover object-center" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />

				<!-- Event description -->
				<article class="w-full reci-post-content py-2 flex flex-col gap-6">
					<?php the_content(); ?>
				</article>

			</div>

			<!-- ── RIGHT: Event details sidebar ─────────────────────────── -->
			<aside class="w-full lg:w-1/3 lg:py-14 lg:self-start lg:sticky lg:top-10">
				<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-zinc-200 sticky top-6">

					<!-- CTA header -->
					<div class="bg-neutral-800 px-6 py-5">
						<h2 class="text-white text-2xl font-bold font-serif leading-7"><?php esc_html_e( 'Event Details', 'reci-media-hub' ); ?></h2>
					</div>

					<div class="px-6 py-6 flex flex-col gap-5">

						<!-- Date -->
						<div class="flex items-start gap-3">
							<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
							<div>
								<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Date', 'reci-media-hub' ); ?></p>
								<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $formatted_date ); ?></p>
							</div>
						</div>

						<!-- Time -->
						<?php if ( $formatted_time ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Time', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $formatted_time ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<!-- Location -->
						<?php if ( $location ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Location', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $location ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<div class="h-px bg-zinc-200"></div>

						<!-- CTA button -->
						<?php if ( $registration_url ) : ?>
							<a
								href="<?php echo esc_url( $registration_url ); ?>"
								target="_blank"
								rel="noopener noreferrer"
								class="w-full px-7 py-3.5 bg-amber-400 rounded-lg flex justify-center items-center gap-2 hover:bg-amber-500 transition-colors text-neutral-800 text-base font-medium leading-6"
							>
								<?php echo esc_html( $cta_label ); ?>
								<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
							</a>
						<?php elseif ( 'past' !== strtolower( $status ) ) : ?>
							<p class="text-neutral-500 text-sm font-normal text-center"><?php esc_html_e( 'Registration details coming soon.', 'reci-media-hub' ); ?></p>
						<?php else : ?>
							<p class="text-neutral-500 text-sm font-normal text-center"><?php esc_html_e( 'This event has ended.', 'reci-media-hub' ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $display_author['name'] ) ) : ?>
							<div class="h-px bg-zinc-200"></div>
							<div class="flex flex-col gap-4">
								<h3 class="text-neutral-800 text-xl font-bold font-serif leading-6"><?php echo esc_html( $author_card_heading ); ?></h3>
								<div class="flex items-start gap-3">
									<?php if ( ! empty( $display_author['image_url'] ) ) : ?>
										<?php if ( ! empty( $display_author['permalink'] ) ) : ?>
											<a href="<?php echo esc_url( (string) $display_author['permalink'] ); ?>" class="flex-shrink-0">
												<img src="<?php echo esc_url( (string) $display_author['image_url'] ); ?>" alt="<?php echo esc_attr( (string) ( $display_author['image_alt'] ?? $display_author['name'] ) ); ?>" class="w-14 h-14 rounded-full object-cover flex-shrink-0" />
											</a>
										<?php else : ?>
											<img src="<?php echo esc_url( (string) $display_author['image_url'] ); ?>" alt="<?php echo esc_attr( (string) ( $display_author['image_alt'] ?? $display_author['name'] ) ); ?>" class="w-14 h-14 rounded-full object-cover flex-shrink-0" />
										<?php endif; ?>
									<?php endif; ?>
									<div class="flex flex-col gap-1">
										<?php if ( ! empty( $display_author['permalink'] ) ) : ?>
											<a href="<?php echo esc_url( (string) $display_author['permalink'] ); ?>" class="text-neutral-800 text-base font-bold hover:underline">
												<?php echo esc_html( (string) $display_author['name'] ); ?>
											</a>
										<?php else : ?>
											<p class="text-neutral-800 text-base font-bold"><?php echo esc_html( (string) $display_author['name'] ); ?></p>
										<?php endif; ?>
										<?php if ( ! empty( $display_author['title'] ) ) : ?>
											<p class="text-neutral-500 text-sm font-medium"><?php echo esc_html( (string) $display_author['title'] ); ?></p>
										<?php endif; ?>
										<?php if ( ! empty( $display_author['bio'] ) ) : ?>
											<p class="text-neutral-500 text-sm font-normal leading-6"><?php echo esc_html( (string) $display_author['bio'] ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="h-px bg-zinc-200"></div>
						<?php endif; ?>

						<!-- Share -->
						<div class="flex items-center justify-center gap-3 pt-1">
							<button type="button" onclick="navigator.clipboard.writeText(window.location.href)" class="w-9 h-9 bg-gray-100 rounded-full flex justify-center items-center hover:bg-gray-200 transition-colors" aria-label="<?php esc_attr_e( 'Copy link', 'reci-media-hub' ); ?>">
								<svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
							</button>
							<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-gray-100 rounded-full flex justify-center items-center hover:bg-gray-200 transition-colors" aria-label="<?php esc_attr_e( 'Share on Facebook', 'reci-media-hub' ); ?>">
								<svg class="w-4 h-4 text-neutral-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
							</a>
							<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . rawurlencode( get_permalink() ) . '&text=' . rawurlencode( get_the_title() ) ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-gray-100 rounded-full flex justify-center items-center hover:bg-gray-200 transition-colors" aria-label="<?php esc_attr_e( 'Share on X', 'reci-media-hub' ); ?>">
								<svg class="w-4 h-4 text-neutral-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
							</a>
						</div>

					</div>
				</div>
			</aside>

		</div>

		<!-- Related Events -->
		<?php if ( ! empty( $related_events ) ) : ?>
			<div class="py-10 border-t border-zinc-300">
				<div class="flex items-center gap-3 mb-8">
					<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
					<h2 class="text-neutral-800 text-3xl font-bold font-serif leading-10"><?php esc_html_e( 'More Events', 'reci-media-hub' ); ?></h2>
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
					<?php foreach ( $related_events as $rel ) : ?>
						<?php get_template_part( 'template-parts/listings/event-archive-card', null, $rel ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</section><!-- /reci-container -->
</main>

<?php get_footer(); ?>
