<?php
/**
 * Single Course template (reci_course post type).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$post_id = get_the_ID();
$display_author = reci_media_hub_get_display_author( $post_id );
$shared_fallback_image = function_exists('reci_get_fallback_thumbnail_url') ? reci_get_fallback_thumbnail_url('large', 'https://placehold.co/1200x500') : 'https://placehold.co/1200x500';

$category = '';
$cats = wp_get_post_categories( $post_id, ['fields' => 'names'] );
if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
    $category = (string) $cats[0];
}

// Meta.
$level        = (string) get_post_meta( $post_id, '_reci_course_level', true ) ?: 'Beginner';
$duration     = (int) get_post_meta( $post_id, '_reci_course_duration_weeks', true );
$format       = (string) get_post_meta( $post_id, '_reci_course_format', true ) ?: 'self_paced';
$start_date   = (string) get_post_meta( $post_id, '_reci_course_start_date', true );
$fee_label    = (string) get_post_meta( $post_id, '_reci_course_fee_label', true ) ?: 'Free';
$enroll_url   = (string) get_post_meta( $post_id, '_reci_course_enrollment_url', true );
$lessons      = json_decode((string) get_post_meta($post_id, '_reci_course_lessons', true), true) ?: [];

$format_label = 'self_paced' === $format ? 'Self-Paced' : ( 'cohort' === $format ? 'Cohort' : 'Live' );
$start_label  = $start_date ? wp_date( 'F j, Y', strtotime( $start_date ) ) : '';
$duration_label = $duration > 0
	? sprintf( _n( '%d week', '%d weeks', $duration, 'reci-media-hub' ), $duration )
	: __( 'Self-Paced', 'reci-media-hub' );

// Featured image.
$thumb_id  = get_post_thumbnail_id( $post_id );
$image_url = get_the_post_thumbnail_url( $post_id, 'large' ) ?: $shared_fallback_image;
$image_alt = $thumb_id
	? ( (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title() )
	: get_the_title();

// Tags / topics.
$raw_tags = wp_get_post_terms( $post_id, 'reci_topic', [ 'fields' => 'names' ] );
if ( is_wp_error( $raw_tags ) || empty( $raw_tags ) ) {
	$raw_tags = wp_get_post_terms( $post_id, 'post_tag', [ 'fields' => 'names' ] );
}
$tags = is_wp_error( $raw_tags ) ? [] : array_slice( $raw_tags, 0, 4 );

// Related courses.
$related_config = [
	'post_type'      => 'reci_course',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'post__not_in'   => [ $post_id ],
	'orderby'        => 'date',
	'order'          => 'DESC',
	'listing_style'  => 'archive_grid_card',
	'wrapper_class'  => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6',
	'empty_message'  => '',
];

$read_also_items = RECI_Related_Posts_Service::get_cross_type_related($post_id, [
    "limit" => 3,
    "format_args" => [
        "image_size" => "medium",
        "tag_limit" => 3,
        "excerpt_words" => 12,
    ],
]);

get_header();
?>

<main class="layout">
	<section class="reci-container">
		<div class="border-l-0 lg:border-l-[0.50px] border-b-[0.50px] border-zinc-300 flex flex-col lg:flex-row justify-start items-start gap-10">

			<!-- ── LEFT: Course content ──────────────────────────────────── -->
			<div class="w-full lg:flex-1 pl-0 lg:px-10 self-stretch py-14 flex flex-col justify-start items-start gap-10 lg:border-r-[0.50px] border-zinc-300">

				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<div class="self-stretch flex flex-col justify-start items-start gap-2">
							<div class="self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap">
							<a href="<?php echo esc_url(get_post_type_archive_link('reci_course')); ?>" class="no-underline">
								<div class="px-2 py-1 bg-amber-400 rounded flex justify-center items-center gap-2.5">
									<span class="text-neutral-800 text-sm font-normal leading-4"><?php esc_html_e('Course', 'reci-media-hub'); ?></span>
								</div>
							</a>
							<div class="tag-dot"></div>
							<span class="px-2 py-1 rounded text-xs font-medium bg-neutral-800 text-white"><?php echo esc_html( ucfirst( $level ) ); ?></span>
						<?php if ($category !== '') : ?>
							<div class="tag-dot"></div>
							<a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="text-neutral-500 text-sm font-normal leading-4 no-underline hover:underline"><?php echo esc_html($category); ?></a>
						<?php endif; ?>
						</div>
						<h1 class="self-stretch reci-single-title"><?php the_title(); ?></h1>
						<?php
$all_spheres = reci_get_post_spheres(get_the_ID());
if (!empty($all_spheres)) : ?>
    <div class="self-stretch overflow-hidden">
        <div class="inline-flex items-center gap-1.5 flex-nowrap">
            <?php foreach ($all_spheres as $s) : ?>
								<a href="<?php echo esc_url($s['url']); ?>" class="sphere" style="background-color: <?php echo esc_attr($s['color']); ?>1a;">
                    <span class="rounded-full w-2 h-2" style="background-color: <?php echo esc_attr($s['color']); ?>;"></span>
                    <span class="font-medium" style="color: <?php echo esc_attr($s['color']); ?>;"><?php echo esc_html($s['name']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
						<div class="flex items-center justify-between w-full border-t border-zinc-200 pt-5 mt-2">
							<div class="flex flex-wrap items-center gap-3">
								<span class="text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html($duration_label); ?></span>
							</div>
							<?php echo reci_render_post_actions(); ?>
						</div>
					</div>
				</div>

				<img class="self-stretch rounded-lg w-full h-[350px] object-cover object-center" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />



				<article class="w-full reci-post-content py-2 flex flex-col gap-6">
					<?php the_content(); ?>
				</article>

				<?php if ( ! empty( $lessons ) ) : ?>
					<section class="w-full pt-10">
						<div class="flex flex-col gap-5">
							<div class="inline-flex items-center gap-2">
								<div class="tag-dot"></div>
								<h2 class="text-neutral-800 text-2xl font-bold font-subhead leading-7"><?php esc_html_e( 'Course Lessons', 'reci-media-hub' ); ?></h2>
							</div>
							<ul class="flex flex-col gap-3 list-none p-0">
								<?php foreach ( $lessons as $lesson ) : ?>
									<li class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg border border-slate-100">
										<svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
										<span class="text-neutral-700 font-medium"><?php echo esc_html( $lesson ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</section>
				<?php endif; ?>

				<?php
$post_tags = wp_get_post_tags(get_the_ID(), ['fields' => 'all']);
if (!empty($post_tags) && !is_wp_error($post_tags)) : ?>
    <div class="self-stretch mt-4 flex flex-wrap items-center gap-2">
        <?php foreach ($post_tags as $tag) : ?>
            <a href="<?php echo esc_url(get_tag_link((int) $tag->term_id)); ?>" class="px-2 py-1 bg-gray-200 rounded no-underline text-xs text-neutral-600 font-normal leading-4 uppercase"><?php echo esc_html($tag->name); ?></a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$sdgs = wp_get_post_terms(get_the_ID(), 'sdgs');
if (!empty($sdgs) && !is_wp_error($sdgs)) : ?>
    <div class="self-stretch mt-4 flex flex-wrap items-center gap-2">
        <?php foreach ($sdgs as $sdg) : 
            $sdg_color = get_term_meta($sdg->term_id, 'sdg_color', true) ?: '#ccc';
        ?>
            <a href="<?php echo esc_url(get_term_link($sdg)); ?>" class="px-2 py-1 bg-gray-200 rounded no-underline text-xs text-neutral-600 font-normal leading-4 uppercase" style="background-color: <?php echo esc_attr($sdg_color); ?>; color: #fff;">
                <?php echo esc_html($sdg->name); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


			</div><!-- /LEFT: Course content -->

			<!-- ── RIGHT: Course details sidebar ─────────────────────────── -->
			<aside class="w-full lg:w-1/3 lg:py-14 lg:self-start lg:sticky lg:top-10">
				<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-zinc-200">

					<div class="bg-neutral-800 px-6 py-5">
						<h2 class="text-white text-2xl font-bold font-serif leading-7"><?php esc_html_e( 'Course Details', 'reci-media-hub' ); ?></h2>
					</div>

					<div class="px-6 py-6 flex flex-col gap-5">

						<!-- Level -->
						<?php if ( $level ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Level', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( ucfirst( $level ) ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<!-- Duration -->
						<?php if ( $duration > 0 ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Duration', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $duration_label ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<!-- Format -->
						<?php if ( $format ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Format', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $format_label ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<!-- Start date -->
						<?php if ( $start_label ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Start Date', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $start_label ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<!-- Fee -->
						<?php if ( $fee_label ) : ?>
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
								<div>
									<p class="text-neutral-500 text-xs font-medium uppercase tracking-wider"><?php esc_html_e( 'Fee', 'reci-media-hub' ); ?></p>
									<p class="text-neutral-800 text-base font-medium mt-0.5"><?php echo esc_html( $fee_label ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<div class="h-px bg-zinc-200"></div>

						<!-- CTA button -->
						<?php if ( $enroll_url ) : ?>
							<a
								href="<?php echo esc_url( $enroll_url ); ?>"
								target="_blank"
								rel="noopener noreferrer"
								class="w-full px-7 py-3.5 bg-amber-400 rounded-lg flex justify-center items-center gap-2 hover:bg-amber-500 transition-colors text-neutral-800 text-base font-medium leading-6"
							>
								<?php esc_html_e( 'Go to Course', 'reci-media-hub' ); ?>
								<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
							</a>
						<?php endif; ?>

						<?php if ( ! empty( $display_author['name'] ) ) : ?>
							<div class="h-px bg-zinc-200"></div>
							<div class="flex flex-col gap-4">
								<h3 class="text-neutral-800 text-xl font-bold font-serif leading-6"><?php esc_html_e( 'Course Instructor', 'reci-media-hub' ); ?></h3>
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

		<!-- See Also -->
		<?php if (! empty($read_also_items)) : ?>
			<div class="py-10 border-t border-zinc-300">
				<div class="flex items-center gap-3 mb-8">
					<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
					<h2 class="text-neutral-800 text-3xl font-bold font-serif leading-10"><?php esc_html_e('See also', 'reci-media-hub'); ?></h2>
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
					<?php foreach ($read_also_items as $item) : ?>
						<?php get_template_part('template-parts/listings/see-also-card', null, $item); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

	</section><!-- /reci-container -->
</main>

<?php get_footer(); ?>
