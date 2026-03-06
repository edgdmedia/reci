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

// Meta.
$status           = (string) get_post_meta( $post_id, '_reci_event_status', true );
$start_date_raw   = (string) get_post_meta( $post_id, '_reci_event_start_date', true );
$start_time_raw   = (string) get_post_meta( $post_id, '_reci_event_start_time', true );
$end_date_raw     = (string) get_post_meta( $post_id, '_reci_event_end_date', true );
$end_time_raw     = (string) get_post_meta( $post_id, '_reci_event_end_time', true );
$timezone_raw     = (string) get_post_meta( $post_id, '_reci_event_timezone', true );
$location         = (string) get_post_meta( $post_id, '_reci_event_location', true );
$cta_label        = (string) get_post_meta( $post_id, '_reci_event_cta_label', true ) ?: __( 'Register', 'reci-media-hub' );
$registration_url = (string) get_post_meta( $post_id, '_reci_event_registration_url', true );

// Format dates.
$start_ts      = $start_date_raw ? strtotime( $start_date_raw ) : false;
$end_ts        = $end_date_raw ? strtotime( $end_date_raw ) : false;
$formatted_date = $start_ts ? wp_date( 'l, F j, Y', $start_ts ) : get_the_date( 'l, F j, Y' );
$formatted_time = trim( $start_time_raw . ( $timezone_raw ? ' ' . $timezone_raw : '' ) );
if ( $end_time_raw ) {
	$formatted_time = trim( $start_time_raw . ' – ' . $end_time_raw . ( $timezone_raw ? ' ' . $timezone_raw : '' ) );
}

// Featured image.
$thumb_id  = get_post_thumbnail_id( $post_id );
$image_url = get_the_post_thumbnail_url( $post_id, 'large' ) ?: 'https://placehold.co/1200x500';
$image_alt = $thumb_id ? ( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title() ) : get_the_title();

// Status badge colour.
$status_label = $status ? ucfirst( $status ) : 'Upcoming';
$status_class = 'bg-amber-400 text-neutral-800';
if ( 'past' === strtolower( $status ) || 'ended' === strtolower( $status ) ) {
	$status_class = 'bg-zinc-400 text-white';
} elseif ( 'live' === strtolower( $status ) || 'ongoing' === strtolower( $status ) ) {
	$status_class = 'bg-green-500 text-white';
}

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
		'meta_key'       => '_reci_event_start_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
	]
);
foreach ( $related_query->posts as $rel_post ) {
	$rel_id          = (int) $rel_post->ID;
	$rel_date_raw    = (string) get_post_meta( $rel_id, '_reci_event_start_date', true );
	$rel_ts          = $rel_date_raw ? strtotime( $rel_date_raw ) : false;
	$rel_status      = (string) get_post_meta( $rel_id, '_reci_event_status', true );
	$rel_time_raw    = (string) get_post_meta( $rel_id, '_reci_event_start_time', true );
	$rel_tz          = (string) get_post_meta( $rel_id, '_reci_event_timezone', true );
	$related_events[] = [
		'title'    => get_the_title( $rel_id ),
		'link_url' => get_permalink( $rel_id ),
		'status'   => $rel_status ? ucfirst( $rel_status ) : 'Upcoming',
		'date'     => $rel_ts ? wp_date( 'M j, Y', $rel_ts ) : get_the_date( 'M j, Y', $rel_id ),
		'time'     => trim( $rel_time_raw . ( $rel_tz ? ' ' . $rel_tz : '' ) ),
		'image_url'=> get_the_post_thumbnail_url( $rel_id, 'medium' ) ?: 'https://placehold.co/400x225',
		'image_alt'=> get_the_title( $rel_id ),
	];
}

get_header();
?>

<div class="bg-slate-100 min-h-screen font-['SF_Pro_Display']">

	<!-- Hero image -->
	<div class="relative w-full h-64 sm:h-80 lg:h-[420px] bg-neutral-800 overflow-hidden">
		<img
			src="<?php echo esc_url( $image_url ); ?>"
			alt="<?php echo esc_attr( $image_alt ); ?>"
			class="absolute inset-0 w-full h-full object-cover opacity-60"
		/>
		<div class="absolute inset-0 bg-gradient-to-t from-neutral-900/80 to-transparent"></div>
		<!-- Status badge -->
		<div class="absolute bottom-6 left-6 lg:left-20">
			<span class="px-3 py-1.5 rounded text-sm font-medium font-['SF_Pro_Display'] <?php echo esc_attr( $status_class ); ?>">
				<?php echo esc_html( $status_label ); ?>
			</span>
		</div>
	</div>

	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">

		<!-- Breadcrumb -->
		<nav class="flex items-center gap-2 text-neutral-500 text-sm font-normal pt-6 pb-4" aria-label="<?php esc_attr_e( 'Breadcrumb', 'reci-media-hub' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-neutral-800 transition-colors"><?php esc_html_e( 'Home', 'reci-media-hub' ); ?></a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'reci_event' ) ?: home_url( '/events/' ) ); ?>" class="hover:text-neutral-800 transition-colors"><?php esc_html_e( 'Events', 'reci-media-hub' ); ?></a>
			<span aria-hidden="true">/</span>
			<span class="text-neutral-800"><?php the_title(); ?></span>
		</nav>

		<div class="flex flex-col lg:flex-row gap-10 pb-16">

			<!-- ── LEFT: Event content ───────────────────────────────────── -->
			<div class="flex-1 flex flex-col gap-8">

				<!-- Title + tags -->
				<div class="flex flex-col gap-4">
					<h1 class="text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-tight">
						<?php the_title(); ?>
					</h1>
					<?php if ( ! empty( $tags ) ) : ?>
						<div class="inline-flex gap-2 flex-wrap">
							<?php foreach ( $tags as $tag ) : ?>
								<span class="px-2 py-1 bg-gray-200 rounded text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html( $tag ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="h-px bg-zinc-300"></div>

				<!-- Event description -->
				<div class="text-neutral-700 text-base font-normal font-['SF_Pro_Display'] leading-7 reci-post-content">
					<?php the_content(); ?>
				</div>

			</div>

			<!-- ── RIGHT: Event details sidebar ─────────────────────────── -->
			<div class="w-full lg:w-80 xl:w-96 flex-shrink-0">
				<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-zinc-200 sticky top-6">

					<!-- CTA header -->
					<div class="bg-neutral-800 px-6 py-5">
						<h2 class="text-white text-xl font-bold font-['EB_Garamond'] leading-6"><?php esc_html_e( 'Event Details', 'reci-media-hub' ); ?></h2>
					</div>

					<div class="px-6 py-6 flex flex-col gap-5">

						<!-- Date -->
						<div class="flex items-start gap-3">
							<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
							<div>
								<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Date', 'reci-media-hub' ); ?></p>
								<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $formatted_date ); ?></p>
							</div>
						</div>

						<!-- Time -->
						<?php if ( $formatted_time ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Time', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $formatted_time ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<!-- Location -->
						<?php if ( $location ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Location', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $location ); ?></p>
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
								class="w-full px-7 py-3.5 bg-amber-400 rounded-lg flex justify-center items-center gap-2 hover:bg-amber-500 transition-colors text-neutral-800 text-base font-medium font-['SF_Pro_Display'] leading-6"
							>
								<?php echo esc_html( $cta_label ); ?>
								<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
							</a>
						<?php elseif ( 'past' !== strtolower( $status ) ) : ?>
							<p class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] text-center"><?php esc_html_e( 'Registration details coming soon.', 'reci-media-hub' ); ?></p>
						<?php else : ?>
							<p class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] text-center"><?php esc_html_e( 'This event has ended.', 'reci-media-hub' ); ?></p>
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
			</div>

		</div>

		<!-- Related Events -->
		<?php if ( ! empty( $related_events ) ) : ?>
			<div class="py-10 border-t border-zinc-300">
				<div class="flex items-center gap-3 mb-8">
					<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
					<h2 class="text-neutral-800 text-3xl font-bold font-['EB_Garamond'] leading-10"><?php esc_html_e( 'More Events', 'reci-media-hub' ); ?></h2>
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
					<?php foreach ( $related_events as $rel ) : ?>
						<a href="<?php echo esc_url( $rel['link_url'] ); ?>" class="bg-white rounded-lg shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow">
							<div class="relative h-40 bg-neutral-200 overflow-hidden">
								<img src="<?php echo esc_url( $rel['image_url'] ); ?>" alt="<?php echo esc_attr( $rel['image_alt'] ); ?>" class="w-full h-full object-cover" />
								<span class="absolute top-3 left-3 px-2 py-1 bg-amber-400 rounded text-neutral-800 text-xs font-medium font-['SF_Pro_Display']"><?php echo esc_html( $rel['status'] ); ?></span>
							</div>
							<div class="p-4 flex flex-col gap-2 flex-1">
								<div class="flex items-center gap-1.5 text-neutral-500 text-xs font-normal font-['SF_Pro_Display']">
									<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
									<?php echo esc_html( $rel['date'] ); ?>
									<?php if ( $rel['time'] ) : ?> · <?php echo esc_html( $rel['time'] ); ?><?php endif; ?>
								</div>
								<h3 class="text-neutral-800 text-base font-bold font-['EB_Garamond'] leading-5 line-clamp-2"><?php echo esc_html( $rel['title'] ); ?></h3>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</div><!-- /max-w container -->
</div>

<?php get_footer(); ?>
