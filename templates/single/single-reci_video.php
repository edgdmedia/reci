<?php

/**
 * Single Video template (reci_video post type).
 *
 * Wired to real WordPress data.
 */

if (! defined('ABSPATH')) {
	exit;
}

the_post();

$post_id  = get_the_ID();
$display_author = reci_media_hub_get_display_author($post_id);
$shared_fallback_image = function_exists('reci_get_fallback_thumbnail_url') ? reci_get_fallback_thumbnail_url('large', 'https://placehold.co/400x225') : 'https://placehold.co/400x225';

// Meta.
$video_url      = (string) get_post_meta($post_id, '_reci_video_url', true);
$platform       = (string) get_post_meta($post_id, '_reci_video_platform', true); // youtube|vimeo|self_hosted|other
$external_id    = (string) get_post_meta($post_id, '_reci_video_external_id', true);
$duration_label = (string) get_post_meta($post_id, '_reci_video_duration_label', true);

$thumb_id  = get_post_thumbnail_id($post_id);
$image_url = get_the_post_thumbnail_url($post_id, 'large') ?: $shared_fallback_image;
$image_alt = $thumb_id ? ((string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title()) : get_the_title();

$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '...');

$raw_tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
$tags     = is_wp_error($raw_tags) ? [] : array_slice($raw_tags, 0, 5);
if (empty($tags)) {
	$raw_cats = wp_get_post_terms($post_id, 'category', ['fields' => 'names']);
	$tags     = is_wp_error($raw_cats) ? [] : array_slice($raw_cats, 0, 3);
}
$video_spheres = reci_get_post_spheres($post_id);

$category = '';
$cats = wp_get_post_categories($post_id, ['fields' => 'names']);
if (!empty($cats) && !is_wp_error($cats)) {
    $category = (string) $cats[0];
}

// Build embed URL.
$embed_url = '';
if ('youtube' === $platform) {
	$yt_id = $external_id;
	if (! $yt_id && $video_url) {
		// Parse YouTube ID from URL.
		if (preg_match('/(?:v=|youtu\.be\/|\/embed\/)([A-Za-z0-9_\-]{11})/', $video_url, $m)) {
			$yt_id = $m[1];
		}
	}
	if ($yt_id) {
		$embed_url = 'https://www.youtube.com/embed/' . $yt_id . '?rel=0';
	}
} elseif ('vimeo' === $platform) {
	$v_id = $external_id ?: (preg_match('/vimeo\.com\/(\d+)/', $video_url, $m) ? $m[1] : '');
	if ($v_id) {
		$embed_url = 'https://player.vimeo.com/video/' . $v_id;
	}
} elseif ('self_hosted' === $platform && $video_url) {
	$embed_url = $video_url; // handled separately below.
}

// Related videos.
$related_videos = [];
$related_query  = new WP_Query(
	[
		'post_type'      => 'reci_video',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post__not_in'   => [$post_id],
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
foreach ($related_query->posts as $rel_post) {
	$rel_id       = (int) $rel_post->ID;
	$rel_thumb_id = get_post_thumbnail_id($rel_id);
	$rel_tags_raw = wp_get_post_terms($rel_id, 'post_tag', ['fields' => 'names']);
	$rel_tags     = is_wp_error($rel_tags_raw) ? [] : array_slice($rel_tags_raw, 0, 3);
	if (empty($rel_tags)) {
		$rel_cats_raw = wp_get_post_terms($rel_id, 'category', ['fields' => 'names']);
		$rel_tags     = is_wp_error($rel_cats_raw) ? [] : array_slice($rel_cats_raw, 0, 3);
	}
	$related_videos[] = [
		'type_label' => 'Video',
		'date'       => get_the_date('d M Y', $rel_id),
		'duration'   => (string) get_post_meta($rel_id, '_reci_video_duration_label', true) ?: '',
		'title'      => get_the_title($rel_id),
		'link_url'   => get_permalink($rel_id),
		'tags'       => $rel_tags,
		'image_url'  => get_the_post_thumbnail_url($rel_id, 'medium') ?: $shared_fallback_image,
		'image_alt'  => $rel_thumb_id ? ((string) get_post_meta($rel_thumb_id, '_wp_attachment_image_alt', true) ?: get_the_title($rel_id)) : get_the_title($rel_id),
	];
}

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

<div class="layout-page">
	<div class="reci-container">

		<!-- ── VIDEO PLAYER + SIDEBAR LAYOUT ─────────────────────────────── -->
		<div class="flex flex-col lg:flex-row justify-start items-start gap-0 border-l border-b border-zinc-300 gap-10">

			<!-- ── LEFT: Primary video content ──────────────────────────── -->
			<div class="w-full  py-10 px-10 flex flex-col justify-start items-start gap-8 border-r border-zinc-300">

				<!-- Video metadata: type + date + duration + tags -->
				<div class="self-stretch flex flex-col justify-start items-start gap-4">

					<!-- Meta row -->
					<div class="flex items-center justify-between w-full flex-wrap gap-4">
						<div class="inline-flex justify-start items-center gap-2.5 flex-wrap">
							<a href="<?php echo esc_url(get_post_type_archive_link('reci_video')); ?>" class="no-underline">
								<div class="px-2 py-1 bg-[#003594] rounded flex justify-center items-center gap-2.5">
									<span class="text-white text-sm font-normal leading-4"><?php esc_html_e('Video', 'reci-media-hub'); ?></span>
								</div>
							</a>
							<?php if ($category !== '') : ?>
								<div class="tag-dot"></div>
								<a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="text-neutral-500 text-sm font-normal leading-4 no-underline hover:underline"><?php echo esc_html($category); ?></a>
							<?php endif; ?>
							<?php if (! empty($display_author['name'])) : ?>
								<div class="tag-dot"></div>
								<p class="font-bold font-subhead text-neutral-600">By
									<a href="<?php echo esc_url(! empty($display_author['permalink']) ? $display_author['permalink'] : get_author_posts_url((int) get_post_field('post_author', $post_id))); ?>" class="text-neutral-600 hover:underline"><?php echo esc_html((string) $display_author['name']); ?></a>
								</p>
							<?php endif; ?>
							<div class="tag-dot"></div>
							<span class="text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html(get_the_date('d M Y')); ?></span>
							<?php if ($duration_label) : ?>
								<div class="tag-dot"></div>
								<div class="flex justify-start items-center gap-1">
									<svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60 text-neutral-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
										<path d="M8 5v14l11-7z" />
									</svg>
									<span class="text-neutral-600 text-sm font-normal"><?php echo esc_html($duration_label); ?></span>
								</div>
							<?php endif; ?>
						</div>
						<?php echo reci_render_post_actions(); ?>
					</div>
					<!-- Video title -->
					<h1 class="self-stretch reci-single-title">
						<?php the_title(); ?>
					</h1>

					<!-- Excerpt / subtitle -->
					<?php if ($excerpt) : ?>
						<p class="self-stretch text-neutral-600 text-lg font-normal leading-7 ">
							<?php echo esc_html($excerpt); ?>
						</p>
					<?php endif; ?>
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
				</div>

				<!-- 16:9 video embed area -->
				<div class="self-stretch w-full" aria-label="<?php esc_attr_e('Video player', 'reci-media-hub'); ?>">
					<?php if ($embed_url && 'self_hosted' !== $platform) : ?>
						<div class="relative w-full bg-neutral-900 rounded-lg overflow-hidden" style="padding-top: 56.25%;">
							<iframe
								class="absolute inset-0 w-full h-full"
								src="<?php echo esc_url($embed_url); ?>"
								title="<?php echo esc_attr(get_the_title()); ?>"
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								allowfullscreen
								loading="lazy"></iframe>
						</div>
					<?php elseif ('self_hosted' === $platform && $embed_url) : ?>
						<div class="relative w-full bg-neutral-900 rounded-lg overflow-hidden">
							<video
								class="w-full rounded-lg"
								controls
								preload="metadata"
								<?php if ($image_url) : ?>poster="<?php echo esc_url($image_url); ?>" <?php endif; ?>>
								<source src="<?php echo esc_url($embed_url); ?>" />
								<?php esc_html_e('Your browser does not support the video tag.', 'reci-media-hub'); ?>
							</video>
						</div>
					<?php else : ?>
						<!-- Fallback: poster image with play overlay -->
						<div class="relative w-full bg-neutral-900 rounded-lg overflow-hidden" style="padding-top: 56.25%;">
							<?php if ($image_url) : ?>
								<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" class="absolute inset-0 w-full h-full object-cover opacity-70" />
							<?php endif; ?>
							<div class="absolute inset-0 flex flex-col justify-center items-center gap-4">
								<?php if ($video_url) : ?>
									<a href="<?php echo esc_url($video_url); ?>" target="_blank" rel="noopener noreferrer" class="w-20 h-20 bg-white/20 hover:bg-white/30 transition-colors rounded-full flex justify-center items-center focus:outline-none focus:ring-4 focus:ring-white/40" aria-label="<?php echo esc_attr(sprintf(__('Watch %s', 'reci-media-hub'), get_the_title())); ?>">
										<svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
											<path d="M8 5v14l11-7z" />
										</svg>
									</a>
									<span class="text-white/70 text-sm font-normal"><?php esc_html_e('Watch video', 'reci-media-hub'); ?></span>
								<?php else : ?>
									<div class="w-20 h-20 bg-white/20 rounded-full flex justify-center items-center" aria-hidden="true">
										<svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
											<path d="M8 5v14l11-7z" />
										</svg>
									</div>
									<span class="text-white/70 text-sm font-normal"><?php esc_html_e('Video unavailable', 'reci-media-hub'); ?></span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<!-- Description body -->
				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<h2 class="self-stretch text-neutral-800 text-2xl font-bold font-subhead leading-7">
						<?php esc_html_e('About this video', 'reci-media-hub'); ?>
					</h2>
					<div class="self-stretch reci-post-content">
						<?php the_content(); ?>
					</div>
					</div>

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

					<div class="self-stretch border-t border-zinc-300 pt-10 flex flex-col justify-start items-start gap-5">
						<div class="inline-flex items-center gap-2">
							<div class="tag-dot"></div>
							<h2 class="text-neutral-700 text-2xl font-bold font-subhead leading-7"><?php esc_html_e('About the author', 'reci-media-hub'); ?></h2>
						</div>
						<div class="flex flex-row items-center gap-4">
							<?php if (! empty($display_author['image_url'])) : ?>
							<?php if (! empty($display_author['permalink'])) : ?>
								<a href="<?php echo esc_url((string) $display_author['permalink']); ?>" class="flex-shrink-0">
									<img src="<?php echo esc_url((string) $display_author['image_url']); ?>" alt="<?php echo esc_attr((string) ($display_author['image_alt'] ?? $display_author['name'])); ?>" class="w-10 h-10 flex-shrink-0 rounded-full object-cover" />
								</a>
							<?php else : ?>
								<img src="<?php echo esc_url((string) $display_author['image_url']); ?>" alt="<?php echo esc_attr((string) ($display_author['image_alt'] ?? $display_author['name'])); ?>" class="w-10 h-10 flex-shrink-0 rounded-full object-cover" />
							<?php endif; ?>
						<?php endif; ?>
							<div class="flex flex-col gap-2">
								<?php if (! empty($display_author['permalink'])) : ?>
									<a href="<?php echo esc_url((string) $display_author['permalink']); ?>" class="text-neutral-800 text-2xl font-bold font-subhead leading-7 hover:underline">
										<?php echo esc_html((string) $display_author['name']); ?>
									</a>
								<?php else : ?>
									<h3 class="text-neutral-800 text-2xl font-bold font-subhead leading-7"><?php echo esc_html((string) $display_author['name']); ?></h3>
								<?php endif; ?>
								<?php if (! empty($display_author['title'])) : ?>
									<p class="text-neutral-600 text-sm font-medium leading-5"><?php echo esc_html((string) $display_author['title']); ?></p>
								<?php endif; ?>
								<?php if (! empty($display_author['bio'])) : ?>
									<p class="max-w-3xl text-neutral-600 text-base font-normal leading-6"><?php echo esc_html((string) $display_author['bio']); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>


			</div><!-- /left primary -->

			<!-- ── RIGHT: Related videos sidebar ────────────────────────── -->

			<aside class="w-full lg:w-1/3 lg:py-14 lg:self-start lg:sticky lg:top-10">
				<div class="flex flex-col gap-10">
					<?php if (! empty($related_videos)) : ?>
						<div class="bg-neutral-800 p-5 flex flex-col gap-10 overflow-hidden">
							<div class="self-stretch flex flex-col gap-5">
								<div class="inline-flex items-center gap-2">
									<div class="tag-dot"></div>
									<div class="text-white text-2xl font-heading leading-7"><?php esc_html_e('Related Videos', 'reci-media-hub'); ?></div>
								</div>
								<div class="self-stretch h-0 border-t-2 border-white"></div>
							</div>
							<div class="self-stretch flex flex-col gap-10 overflow-hidden">
								<?php foreach ($related_videos as $index => $item) : ?>
									<?php if ($index > 0) : ?><div class="self-stretch h-0 border-t border-zinc-400"></div><?php endif; ?>
									<?php
									get_template_part('template-parts/listings/post-item-compact-related-dark', null, $item);
									?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="flex flex-col py-4 gap-5">
						<div class="flex items-center gap-2">
							<div class="tag-dot"></div>
							<div class="text-neutral-800 text-xl font-bold font-subhead leading-6"><?php esc_html_e('Share this article', 'reci-media-hub'); ?></div>
						</div>
						<div class="self-stretch h-px bg-zinc-300"></div>
						<div class="inline-flex items-center gap-2">
							<button type="button" onclick="navigator.clipboard.writeText(window.location.href)" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e('Copy link', 'reci-media-hub'); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
								</svg>
							</button>
							<a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . $share_url); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e('Share on Facebook', 'reci-media-hub'); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
								</svg>
							</a>
							<a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e('Share on X (Twitter)', 'reci-media-hub'); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
								</svg>
							</a>
						</div>
					</div>

				</div>
			</aside>

		</div><!-- /main layout row -->

		<!-- ── "SEE ALSO" — more content ──────────────────────────────────── -->
		<?php if (! empty($read_also_items)) : ?>
			<div class=" pb-10">
				<div class=" pt-10 flex flex-col justify-start items-start gap-10">

					<div class="self-stretch inline-flex justify-start items-center gap-5 pb-5 border-b border-zinc-300">
						<div class="flex justify-start items-center gap-2">
							<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
							<h2 class="text-neutral-800 text-5xl font-heading leading-tight"><?php esc_html_e('See also', 'reci-media-hub'); ?></h2>
						</div>
					</div>

					<div class="self-stretch grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
						<?php foreach ($read_also_items as $item) : ?>
							<?php get_template_part("template-parts/listings/see-also-card", null, $item); ?>
						<?php endforeach; ?>
					</div>

				</div>
			</div>
		<?php endif; ?>

	</div><!-- /max-w container -->
</div><!-- /bg-slate-100 -->

<?php get_footer(); ?>
