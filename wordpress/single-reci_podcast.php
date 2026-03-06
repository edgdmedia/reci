<?php

/**
 * Single Podcast Episode template (reci_podcast post type).
 *
 * Wired to real WordPress data.
 */

if (! defined('ABSPATH')) {
	exit;
}

$icons_url = get_template_directory_uri() . '/figma/assets/';

if (! have_posts()) {
	wp_safe_redirect(home_url('/'));
	exit;
}

the_post();

$post_id    = get_the_ID();
$author_id  = (int) get_the_author_meta('ID');

$format_duration = static function (int $seconds): string {
	if ($seconds <= 0) {
		return '';
	}

	$hours = (int) floor($seconds / 3600);
	$mins  = (int) floor(($seconds % 3600) / 60);
	$secs  = (int) ($seconds % 60);

	if ($hours > 0) {
		return sprintf('%d:%02d:%02d', $hours, $mins, $secs);
	}

	return sprintf('%02d:%02d', $mins, $secs);
};

$single_payload = RECI_Single_Post_Service::get_post_payload($post_id, [
	'image_size' => 'large',
	'tag_limit'  => 5,
]);

$duration_label = (string) get_post_meta($post_id, '_reci_podcast_duration_label', true);
$duration_secs  = (int) get_post_meta($post_id, '_reci_podcast_duration_secs', true);
$duration       = $duration_label !== '' ? $duration_label : $format_duration($duration_secs);
if ($duration === '') {
	$duration = (string) ($single_payload['meta_value'] ?? '07:27');
}

$audio_url   = (string) get_post_meta($post_id, '_reci_podcast_audio_url', true);
$video_url   = (string) get_post_meta($post_id, '_reci_podcast_video_url', true);
$ep_num      = (string) get_post_meta($post_id, '_reci_podcast_episode_number', true);
$season_num  = (string) get_post_meta($post_id, '_reci_podcast_season_number', true);
$spotify     = (string) get_post_meta($post_id, '_reci_podcast_spotify_url', true);
$apple       = (string) get_post_meta($post_id, '_reci_podcast_apple_url', true);
$transcript  = (string) get_post_meta($post_id, '_reci_podcast_transcript_url', true);

$image_url = (string) ($single_payload['featured_image_url'] ?? 'https://placehold.co/600x600');
$image_alt = (string) ($single_payload['featured_image_alt'] ?? get_the_title());
$excerpt   = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '...');

$raw_tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
$tags     = is_wp_error($raw_tags) ? [] : array_slice($raw_tags, 0, 5);
if (empty($tags)) {
	$raw_cats = wp_get_post_terms($post_id, 'category', ['fields' => 'names']);
	$tags     = is_wp_error($raw_cats) ? [] : array_slice($raw_cats, 0, 3);
}

// Build embed URL for video podcasts.
$embed_url = '';
if ($video_url) {
	if (preg_match('/youtu(?:be\.com\/watch\?v=|\.be\/)([a-zA-Z0-9_-]{11})/', $video_url, $yt_m)) {
		$embed_url = 'https://www.youtube.com/embed/' . $yt_m[1] . '?rel=0';
	} elseif (preg_match('/vimeo\.com\/(\d+)/', $video_url, $vi_m)) {
		$embed_url = 'https://player.vimeo.com/video/' . $vi_m[1];
	}
}

$host = [
	'name'   => get_the_author_meta('display_name'),
	'title'  => get_the_author_meta('user_title') ?: __('RECI Media Hub', 'reci-media-hub'),
	'bio'    => get_the_author_meta('description'),
	'avatar' => get_avatar_url($author_id, ['size' => 80]),
];

$related_episodes = RECI_Related_Posts_Service::get_related($post_id, [
	'post_type'   => 'reci_podcast',
	'limit'       => 3,
	'taxonomy'    => 'post_tag',
	'format_args' => [
		'image_size'    => 'medium',
		'tag_limit'     => 3,
		'excerpt_words' => 12,
		'overrides'     => [
			'progress_percent' => 0,
		],
	],
]);

get_header();
?>

<div class="bg-slate-100 min-h-screen">
	<div class="reci-container mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">

		<!-- Main content + sidebar layout -->
		<div class="flex flex-col lg:flex-row justify-start items-start gap-0 ">

			<!-- ── LEFT: Primary episode content ───────────────────────────── -->
			<div class="w-full md:w-2/3 flex-shrink-0 py-14 px-10 flex flex-col justify-start items-start gap-10 border-x border-zinc-300">

				<!-- Episode metadata + title -->
				<div class="self-stretch flex flex-col justify-start items-start gap-5">

					<!-- Meta row -->
					<div class="self-stretch flex flex-col justify-start items-start gap-2">
						<div class="self-stretch inline-flex justify-start items-center gap-2.5">
							<div class="px-2 py-1 bg-neutral-800 rounded flex justify-center items-center gap-2.5">
								<span class="text-white text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php esc_html_e('Podcast', 'reci-media-hub'); ?></span>
							</div>
							<?php if ($ep_num || $season_num) : ?>
								<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
								<span class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4">
									<?php
									if ($season_num && $ep_num) {
										echo esc_html('S' . $season_num . ' E' . $ep_num);
									} elseif ($ep_num) {
										echo esc_html('Ep. ' . $ep_num);
									}
									?>
								</span>
							<?php endif; ?>
							<div class="tag-dot"></div>
							<span class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html(get_the_date('d M Y')); ?></span>
							<?php if ($duration) : ?>
								<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
								<div class="flex justify-start items-center gap-0.5">
									<img src="<?php echo esc_url($icons_url . 'headphones.svg'); ?>" alt="" class="w-3.5 h-3.5 flex-shrink-0 opacity-60" aria-hidden="true" />
									<span class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html($duration); ?></span>
								</div>
							<?php endif; ?>
						</div>

						<!-- Title + excerpt -->
						<div class="self-stretch flex flex-col justify-start items-start gap-5">
							<h1 class="self-stretch text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-tight">
								<?php the_title(); ?>
							</h1>
							<?php if ($excerpt) : ?>
								<p class="self-stretch text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight">
									<?php echo esc_html($excerpt); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>

					<!-- Topic tags -->
					<?php if (! empty($tags)) : ?>
						<div class="inline-flex justify-start items-center gap-2 flex-wrap">
							<?php foreach ($tags as $tag) : ?>
								<div class="px-2 py-1 bg-gray-200 rounded flex justify-center items-center gap-2.5">
									<span class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($tag); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Audio player bar -->
				<?php if ($audio_url) : ?>

					<div class="self-stretch flex flex-col gap-2" data-audio-player>
						<div class="self-stretch inline-flex justify-start items-center gap-2.5">

							<button
								type="button"
								data-audio-toggle
								data-audio-target="main-audio"
								class="p-1.5 bg-amber-400 rounded-full flex justify-center items-center flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-amber-500"
								aria-label="<?php esc_attr_e('Play episode', 'reci-media-hub'); ?>">
								<!-- Play icon – shown when paused -->
								<svg data-audio-play-icon class="w-4 h-4 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M8 5v14l11-7z" />
								</svg>
								<!-- Pause icon – shown when playing -->
								<svg data-audio-pause-icon class="w-4 h-4 text-neutral-800 hidden" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
								</svg>
							</button>

							<div
								class="flex-1 h-1.5 bg-zinc-300 rounded-full overflow-hidden cursor-pointer"
								role="progressbar"
								aria-valuenow="0"
								aria-valuemin="0"
								aria-valuemax="100"
								aria-label="<?php esc_attr_e('Playback progress', 'reci-media-hub'); ?>"
								data-audio-progress>
								<div class="h-full bg-neutral-800 rounded-full" style="width: 0%;" data-audio-progress-bar></div>
							</div>

							<span class="text-neutral-500 text-xs font-normal font-['SF_Pro_Display'] tabular-nums flex-shrink-0" data-audio-time>
								<?php echo esc_html($duration ?: '0:00'); ?>
							</span>

						</div>
						<audio id="main-audio" src="<?php echo esc_url($audio_url); ?>" preload="none" class="sr-only"></audio>
					</div>

				<?php endif; ?>

				<!-- Episode artwork or video player -->
				<div class="self-stretch">
					<?php if ($video_url) : ?>

						<?php if ($embed_url) : ?>
							<div class="relative w-full aspect-video rounded-lg overflow-hidden bg-neutral-900">
								<iframe
									src="<?php echo esc_url($embed_url); ?>"
									class="absolute inset-0 w-full h-full"
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
									allowfullscreen
									title="<?php the_title_attribute(); ?>"
									loading="lazy"></iframe>
							</div>
						<?php else : ?>
							<video
								class="w-full rounded-lg bg-neutral-900"
								controls
								preload="metadata"
								poster="<?php echo esc_url($image_url); ?>">
								<source src="<?php echo esc_url($video_url); ?>" />
								<?php esc_html_e('Your browser does not support video playback.', 'reci-media-hub'); ?>
							</video>
						<?php endif; ?>

					<?php else : ?>
						<img
							src="<?php echo esc_url($image_url); ?>"
							alt="<?php echo esc_attr($image_alt); ?>"
							class="w-full object-cover rounded-lg" />
					<?php endif; ?>
				</div>

				<!-- Platform links + transcript (shown when defined, for any podcast type) -->
				<?php if ($spotify || $apple || $transcript) : ?>
					<div class="inline-flex flex-wrap items-center gap-x-4 gap-y-1">
						<?php if ($spotify) : ?>
							<a href="<?php echo esc_url($spotify); ?>" target="_blank" rel="noopener noreferrer" class="text-neutral-500 text-xs font-normal font-['SF_Pro_Display'] hover:text-neutral-800 transition-colors inline-flex items-center gap-1">
								<svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
									<path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z" />
								</svg>
								<?php esc_html_e('Listen on Spotify', 'reci-media-hub'); ?>
							</a>
						<?php endif; ?>
						<?php if ($apple) : ?>
							<a href="<?php echo esc_url($apple); ?>" target="_blank" rel="noopener noreferrer" class="text-neutral-500 text-xs font-normal font-['SF_Pro_Display'] hover:text-neutral-800 transition-colors inline-flex items-center gap-1">
								<svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
									<path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11" />
								</svg>
								<?php esc_html_e('Listen on Apple Podcasts', 'reci-media-hub'); ?>
							</a>
						<?php endif; ?>
						<?php if ($transcript) : ?>
							<a href="<?php echo esc_url($transcript); ?>" target="_blank" rel="noopener noreferrer" class="text-neutral-500 text-xs font-normal font-['SF_Pro_Display'] hover:text-neutral-800 transition-colors inline-flex items-center gap-1">
								<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
								</svg>
								<?php esc_html_e('Read transcript', 'reci-media-hub'); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Description body -->
				<div class="w-full flex-shrink-0 pt-10 pb-14 flex flex-col justify-start items-start gap-10">
					<div class="self-stretch flex flex-col justify-start items-start gap-5">
						<h2 class="self-stretch text-neutral-800 text-2xl font-bold font-['EB_Garamond'] leading-7">
							<?php esc_html_e('About this episode', 'reci-media-hub'); ?>
						</h2>
						<div class="self-stretch text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight reci-post-content">
							<?php the_content(); ?>
						</div>
					</div>
				</div>

			</div><!-- /left primary content -->

			<!-- ── RIGHT: Related episodes sidebar ─────────────────────────── -->
			<div class="w-full md:w-1/3 flex-1 pl-5 pt-14 pr-4 pb-10 flex flex-col justify-start items-start gap-2.5 ">
				<div class="flex-1 p-5 bg-neutral-800 flex flex-col justify-start items-start gap-10 overflow-hidden rounded-lg">

					<!-- Sidebar heading -->
					<div class="self-stretch flex flex-col justify-start items-start gap-5">
						<div class="self-stretch inline-flex justify-start items-center gap-2">
							<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
							<h2 class="text-white text-2xl font-bold font-['EB_Garamond'] leading-7"><?php esc_html_e('Related Podcast', 'reci-media-hub'); ?></h2>
						</div>
						<div class="self-stretch h-px bg-white opacity-40"></div>
					</div>

					<!-- Related episode cards -->
					<?php if (! empty($related_episodes)) : ?>
						<div class="self-stretch flex flex-col justify-start items-start gap-10 overflow-hidden">
							<?php foreach ($related_episodes as $idx => $rel) : ?>
								<?php if ($idx > 0) : ?>
									<div class="self-stretch h-px bg-zinc-600"></div>
								<?php endif; ?>

								<div class="self-stretch flex flex-col justify-start items-start gap-5">
									<!-- Meta row -->
									<div class="self-stretch flex flex-col justify-start items-start gap-2">
										<div class="self-stretch inline-flex justify-start items-center gap-2.5">
											<div class="px-2 py-1 bg-neutral-500 rounded flex justify-center items-center gap-2.5">
												<span class="text-white text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($rel['type_label']); ?></span>
											</div>
											<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
											<span class="text-zinc-400 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($rel['date']); ?></span>
											<?php if ($rel['duration']) : ?>
												<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
												<div class="flex justify-start items-center gap-0.5">
													<img src="<?php echo esc_url($icons_url . 'headphones.svg'); ?>" alt="" class="w-3.5 h-3.5 flex-shrink-0 opacity-40 invert" aria-hidden="true" />
													<span class="text-zinc-400 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html($rel['duration']); ?></span>
												</div>
											<?php endif; ?>
										</div>

										<?php if ($rel['link_url']) : ?>
											<a href="<?php echo esc_url($rel['link_url']); ?>" class="self-stretch text-white text-xl font-bold font-['EB_Garamond'] leading-6 line-clamp-2 hover:underline">
												<?php echo esc_html($rel['title']); ?>
											</a>
										<?php else : ?>
											<h3 class="self-stretch text-white text-xl font-bold font-['EB_Garamond'] leading-6 line-clamp-2">
												<?php echo esc_html($rel['title']); ?>
											</h3>
										<?php endif; ?>
									</div>

									<!-- Mini player -->
									<?php if ($rel['audio_url']) : ?>
										<div class="self-stretch inline-flex justify-start items-center gap-2.5">
											<button type="button" class="p-1 bg-amber-400 rounded-3xl flex justify-start items-center gap-2.5 focus:outline-none focus:ring-2 focus:ring-amber-300" aria-label="<?php echo esc_attr(sprintf(__('Play %s', 'reci-media-hub'), $rel['title'])); ?>">
												<img src="<?php echo esc_url($icons_url . 'play.svg'); ?>" alt="" class="w-4 h-4 flex-shrink-0 brightness-0 invert" aria-hidden="true" />
											</button>
											<div class="flex-1 bg-neutral-500 rounded h-0.5" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
											<span class="text-zinc-400 text-xs font-normal font-['SF_Pro_Display']"><?php echo esc_html($rel['duration']); ?></span>
										</div>
									<?php endif; ?>

									<!-- Topic tags -->
									<?php if (! empty($rel['topic_tags'])) : ?>
										<div class="inline-flex justify-start items-center gap-2 flex-wrap">
											<?php foreach ($rel['topic_tags'] as $tag) : ?>
												<div class="px-2 py-1 bg-gray-700 rounded flex justify-center items-center gap-2.5">
													<span class="text-neutral-400 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($tag); ?></span>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="text-zinc-400 text-sm font-normal font-['SF_Pro_Display']"><?php esc_html_e('No related episodes yet.', 'reci-media-hub'); ?></p>
					<?php endif; ?>

				</div>
				<!-- Share sidebar (right of description) -->
				<div class="flex-1 w-full px-4 pt-10 flex justify-start items-start gap-2.5">
					<div class="flex-1 flex flex-col justify-start items-start gap-5">

						<!-- Share heading -->
						<div class="self-stretch flex flex-col justify-start items-start gap-5">
							<div class="inline-flex justify-start items-center gap-2">
								<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
								<h2 class="text-neutral-800 text-xl font-medium font-['EB_Garamond'] leading-6"><?php esc_html_e('Share this podcast', 'reci-media-hub'); ?></h2>
							</div>
							<div class="self-stretch h-px bg-zinc-300"></div>
						</div>

						<!-- Social icons -->
						<div class="inline-flex justify-start items-center gap-2">
							<button type="button" onclick="navigator.clipboard.writeText(window.location.href)" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e('Copy link', 'reci-media-hub'); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
								</svg>
							</button>
							<a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode(get_permalink())); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e('Share on Facebook', 'reci-media-hub'); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
								</svg>
							</a>
							<a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . rawurlencode(get_permalink()) . '&text=' . rawurlencode(get_the_title())); ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-gray-200 rounded-full flex justify-center items-center hover:bg-gray-300 transition-colors" aria-label="<?php esc_attr_e('Share on X (Twitter)', 'reci-media-hub'); ?>">
								<svg class="w-5 h-5 text-neutral-800" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
								</svg>
							</a>
						</div>

					</div>
				</div>

				<!-- ── HOST INFO SECTION ─────────────────────────────────────────── -->
				<?php if ($host['name']) : ?>


					<div class="w-full px-4 pt-10 flex flex-col justify-start items-start gap-6">

						<!-- Host heading -->
						<div class="self-stretch flex flex-col justify-start items-start gap-5">
							<div class="inline-flex justify-start items-center gap-2">
								<div class="w-2 h-2 bg-amber-400 rounded-sm"></div>
								<h2 class="text-neutral-800 text-xl font-medium font-['EB_Garamond'] leading-6"><?php esc_html_e('Hosted by', 'reci-media-hub'); ?></h2>
							</div>
							<div class="self-stretch h-px bg-zinc-300"></div>
						</div>

						<!-- Host details -->
						<div class="flex flex-col md:flex-row justify-start items-start gap-4">
							<!-- Avatar -->
							<?php if ($host['avatar']) : ?>
								<img src="<?php echo esc_url($host['avatar']); ?>" alt="<?php echo esc_attr($host['name']); ?>" class="w-16 h-16 flex-shrink-0 rounded-full object-cover" />
							<?php else : ?>
								<div class="w-16 h-16 flex-shrink-0 rounded-full bg-neutral-300 flex items-center justify-center overflow-hidden" aria-hidden="true">
									<svg class="w-10 h-10 text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
										<path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
									</svg>
								</div>
							<?php endif; ?>

							<div>
								<h2 class="text-neutral-800 text-2xl font-bold font-['EB_Garamond'] leading-7"><?php echo esc_html($host['name']); ?></h2>
								<?php if ($host['title']) : ?>
									<p class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-5"><?php echo esc_html($host['title']); ?></p>
								<?php endif; ?>
								<?php if ($host['bio']) : ?>
									<p class="text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight max-w-prose"><?php echo esc_html($host['bio']); ?></p>
								<?php endif; ?>
							</div>
						</div>

					</div>

				<?php endif; ?>
			</div>
		</div><!-- /main layout row -->

		<!-- ── "SEE ALSO" — more from this podcast ───────────────────────── -->
		<?php if (! empty($related_episodes)) : ?>
			<div class=" py-10 flex flex-col justify-start items-start gap-10 border-t border-zinc-300">
				<div class="self-stretch inline-flex justify-start items-center gap-5 border-b pb-10 border-zinc-300">
					<div class="flex justify-start items-center gap-2">
						<div class="tag-dot"></div>
						<h2 class="text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-tight"><?php esc_html_e('See also', 'reci-media-hub'); ?></h2>
					</div>
				</div>

				<div class="self-stretch grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
					<?php foreach ($related_episodes as $related_episode) : ?>
						<?php get_template_part('template-parts/listings/podcast-archive-card', null, $related_episode); ?>
					<?php endforeach; ?>
				</div>

			</div>
		<?php endif; ?>

	</div><!-- /max-w container -->
</div><!-- /bg-slate-100 -->

<?php get_footer(); ?>