<?php
/**
 * Single Course template (reci_course post type).
 *
 * Redirects to the external enrollment URL (_reci_course_enrollment_url).
 * Falls back to a basic info page if the URL is not set.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$post_id        = get_the_ID();
$enrollment_url = (string) get_post_meta( $post_id, '_reci_course_enrollment_url', true );

if ( $enrollment_url ) {
	wp_redirect( esc_url_raw( $enrollment_url ), 302 );
	exit;
}

// ─── Fallback info page ───────────────────────────────────────────────────────
// Only reached when _reci_course_enrollment_url is empty.

$level          = (string) get_post_meta( $post_id, '_reci_course_level', true );
$duration_weeks = (string) get_post_meta( $post_id, '_reci_course_duration_weeks', true );
$format         = (string) get_post_meta( $post_id, '_reci_course_format', true );
$start_date_raw = (string) get_post_meta( $post_id, '_reci_course_start_date', true );
$fee_label      = (string) get_post_meta( $post_id, '_reci_course_fee_label', true );

$start_ts       = $start_date_raw ? strtotime( $start_date_raw ) : false;
$formatted_date = $start_ts ? wp_date( 'F j, Y', $start_ts ) : '';

$thumb_id  = get_post_thumbnail_id( $post_id );
$image_url = get_the_post_thumbnail_url( $post_id, 'large' ) ?: 'https://placehold.co/1200x500';
$image_alt = $thumb_id ? ( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title() ) : get_the_title();

$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '...' );

$raw_tags = wp_get_post_terms( $post_id, 'reci_topic', [ 'fields' => 'names' ] );
$tags     = ( is_wp_error( $raw_tags ) || empty( $raw_tags ) ) ? [] : array_slice( $raw_tags, 0, 4 );

get_header();
?>

<div class="bg-slate-100 min-h-screen font-['SF_Pro_Display']">

	<!-- Hero image -->
	<div class="relative w-full h-64 sm:h-80 lg:h-[360px] bg-neutral-800 overflow-hidden">
		<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="absolute inset-0 w-full h-full object-cover opacity-50" />
		<div class="absolute inset-0 bg-gradient-to-t from-neutral-900/80 to-transparent"></div>
	</div>

	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">

		<!-- Breadcrumb -->
		<nav class="flex items-center gap-2 text-neutral-500 text-sm font-normal pt-6 pb-4" aria-label="<?php esc_attr_e( 'Breadcrumb', 'reci-media-hub' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-neutral-800 transition-colors"><?php esc_html_e( 'Home', 'reci-media-hub' ); ?></a>
			<span aria-hidden="true">/</span>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'reci_course' ) ?: home_url( '/courses/' ) ); ?>" class="hover:text-neutral-800 transition-colors"><?php esc_html_e( 'Courses', 'reci-media-hub' ); ?></a>
			<span aria-hidden="true">/</span>
			<span class="text-neutral-800"><?php the_title(); ?></span>
		</nav>

		<div class="flex flex-col lg:flex-row gap-10 pb-16">

			<!-- ── LEFT: Course details ──────────────────────────────────── -->
			<div class="flex-1 flex flex-col gap-6">

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

				<?php if ( $excerpt ) : ?>
					<p class="text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<div class="h-px bg-zinc-300"></div>

				<div class="text-neutral-700 text-base font-normal font-['SF_Pro_Display'] leading-7 reci-post-content">
					<?php the_content(); ?>
				</div>

			</div>

			<!-- ── RIGHT: Info sidebar ──────────────────────────────────── -->
			<div class="w-full lg:w-80 xl:w-96 flex-shrink-0">
				<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-zinc-200 sticky top-6">

					<div class="bg-neutral-800 px-6 py-5">
						<h2 class="text-white text-xl font-bold font-['EB_Garamond'] leading-6"><?php esc_html_e( 'Course Info', 'reci-media-hub' ); ?></h2>
					</div>

					<div class="px-6 py-6 flex flex-col gap-5">

						<?php if ( $level ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Level', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $level ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $duration_weeks ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Duration', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5">
										<?php
										printf(
											/* translators: %s: number of weeks */
											esc_html( _n( '%s week', '%s weeks', (int) $duration_weeks, 'reci-media-hub' ) ),
											esc_html( $duration_weeks )
										);
										?>
									</p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $format ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Format', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $format ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $formatted_date ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Start Date', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $formatted_date ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $fee_label ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium font-['SF_Pro_Display'] uppercase tracking-wider"><?php esc_html_e( 'Fee', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-sm font-medium font-['SF_Pro_Display'] mt-0.5"><?php echo esc_html( $fee_label ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<div class="h-px bg-zinc-200"></div>

						<p class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] text-center">
							<?php esc_html_e( 'Enrollment link coming soon.', 'reci-media-hub' ); ?>
						</p>

					</div>
				</div>
			</div>

		</div>
	</div><!-- /max-w container -->
</div>

<?php get_footer(); ?>
