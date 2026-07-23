<?php

/**
 * Template Name: Homepage (Wired)
 * Description: Homepage wired with reusable listing components.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$placeholder_image_large  = 'https://placehold.co/1200x700';
$placeholder_image_medium = 'https://placehold.co/710x700';
$placeholder_image_card   = 'https://placehold.co/460x232';
$placeholder_avatar       = 'https://placehold.co/60x60';
$today_background_image   = 'https://placehold.co/1440x750';
$icons_url                = get_template_directory_uri() . '/assets/icons/';
$reflection_background_images = [
	get_template_directory_uri() . '/demo-content/images/site/reflections/we-humans/students-1959.webp',
];

$get_post_image = static function (int $post_id, string $size = 'large', string $fallback = ''): string {
	$image = get_the_post_thumbnail_url($post_id, $size);
	if (! $image) {
		return $fallback;
	}
	return $image;
};

$get_post_image_alt = static function (int $post_id): string {
	$thumb_id = get_post_thumbnail_id($post_id);
	if (! $thumb_id) {
		return get_the_title($post_id);
	}
	$alt = (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
	return $alt !== '' ? $alt : get_the_title($post_id);
};

$default_article_item = [
	'type_label'       => 'Article',
	'type_badge_class' => 'bg-amber-400',
	'type_text_class'  => 'text-neutral-800',
	'date'             => '08 Jan 2026',
	'meta_icon'        => 'timer',
	'meta_value'       => '3 mins',
	'title'            => "Tracing Redlining: Pittsburgh's Hidden Borders",
	'excerpt'          => 'Explore the historical practices of redlining in Pittsburgh and their lasting impact on communities.',
	'tags'             => ['Identity', 'Inclusion', 'Language'],
	'image_url'        => $placeholder_image_large,
	'image_alt'        => 'Featured article image',
	'link_url'         => '#',
];

$hero_feature_item = $default_article_item;
$hero_feature_id   = 0;
$hero_sidebar_ids  = [];
$featured_method = (string) reci_setting('hp_featured_method', 'latest');
$hero_feed_items = RECI_Content_Feed::query(
	[
		'post_type'      => ['post', 'reci_podcast', 'reci_video'],
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	],
	[
		'image_size'     => 'large',
		'fallback_image' => $placeholder_image_large,
	]
);

if ('sticky' === $featured_method) {
	$sticky_ids = array_values(array_filter(array_map('intval', (array) get_option('sticky_posts'))));
	if (! empty($sticky_ids)) {
		$sticky_items = RECI_Content_Feed::query(
			[
				'post_type'      => ['post', 'reci_podcast', 'reci_video'],
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'post__in'       => $sticky_ids,
		'orderby'        => 'rand',
			],
			[
				'image_size'     => 'large',
				'fallback_image' => $placeholder_image_large,
			]
		);
		$sticky_item = RECI_Content_Feed::first($sticky_items);
		if (is_array($sticky_item)) {
			$hero_feed_items = array_values(array_filter(array_merge(
				[$sticky_item],
				array_filter($hero_feed_items, static function (array $item) use ($sticky_item): bool {
					return (int) ($item['id'] ?? 0) !== (int) ($sticky_item['id'] ?? 0);
				})
			)));
		}
	}
}

$hero_feature_item = RECI_Content_Feed::first($hero_feed_items) ?: $hero_feature_item;
if (! empty($hero_feature_item['id'])) {
	$hero_feature_id = (int) $hero_feature_item['id'];
}

$hero_sidebar_items = array_map(static function (array $item): array {
	$item['meta_value_size'] = 'text-sm';
	return $item;
}, RECI_Content_Feed::slice($hero_feed_items, 1, 4));
$hero_sidebar_ids = RECI_Content_Feed::ids($hero_sidebar_items);

// Today at RECI — fetch up to 4 upcoming events for carousel.
$today_items = [
	[
		'status'       => 'Upcoming',
		'date'         => '4 Jun, 2026',
		'time'         => '11:30AM',
		'datetime_iso' => '2026-06-04T11:30:00-04:00',
		'title'        => 'RECI Spring Cohort — Session 14: Gauging Racial Inequities',
		'excerpt'      => 'Explore frameworks for measuring racial inequities and championing racial justice in this penultimate session.',
		'button_label' => 'Learn More',
		'link_url'     => home_url( '/events/' ),
		'image_url'    => $placeholder_image_card,
		'image_alt'    => 'RECI Spring Cohort',
	],
	[
		'status'       => 'Upcoming',
		'date'         => '11 Jun, 2026',
		'time'         => '11:30AM',
		'datetime_iso' => '2026-06-11T11:30:00-04:00',
		'title'        => 'RECI Spring Cohort — Closing Session',
		'excerpt'      => 'Join us for the final session as we reflect on our journey and celebrate participant achievements.',
		'button_label' => 'Learn More',
		'link_url'     => home_url( '/events/' ),
		'image_url'    => $placeholder_image_card,
		'image_alt'    => 'RECI Spring Cohort',
	],
];

$today_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_event',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'meta_key'       => '_reci_event_start_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => '_reci_event_end_date',
				'value'   => wp_date('Y-m-d'),
				'compare' => '>=',
				'type'    => 'DATE',
			],
			[
				'relation' => 'AND',
				[
					'key'     => '_reci_event_end_date',
					'compare' => 'NOT EXISTS',
				],
				[
					'key'     => '_reci_event_start_date',
					'value'   => wp_date('Y-m-d'),
					'compare' => '>=',
					'type'    => 'DATE',
				],
			],
		],
	]
);
if (! empty($today_posts)) {
	$today_items = [];
	foreach ($today_posts as $event_post) {
		$event_id        = (int) $event_post->ID;
		$start_date_raw  = (string) get_post_meta($event_id, '_reci_event_start_date', true);
		$end_date_raw    = (string) get_post_meta($event_id, '_reci_event_end_date', true);
		$start_time_raw  = (string) get_post_meta($event_id, '_reci_event_start_time', true);
		$timezone_raw    = (string) get_post_meta($event_id, '_reci_event_timezone', true);
		$cta_label       = (string) get_post_meta($event_id, '_reci_event_cta_label', true) ?: 'Register';
		$registration    = (string) get_post_meta($event_id, '_reci_event_registration_url', true);
		$status          = 'upcoming';
		$event_datetime  = null;
		$formatted_date  = get_the_date('j M, Y', $event_id);
		$formatted_time  = '';
		$datetime_iso    = '';

		if ($start_date_raw !== '') {
			try {
				$event_timezone = new DateTimeZone($timezone_raw !== '' ? $timezone_raw : 'UTC');
				$datetime_input = trim($start_date_raw . ' ' . ($start_time_raw !== '' ? $start_time_raw : '00:00'));
				$event_datetime = new DateTimeImmutable($datetime_input, $event_timezone);
				$today_midnight = new DateTimeImmutable('today midnight', $event_timezone);
				$end_date_ref = $end_date_raw !== '' ? $end_date_raw : $start_date_raw;
				$end_datetime = new DateTimeImmutable($end_date_ref . ' 23:59:59', $event_timezone);
				if ($event_datetime <= $today_midnight && $today_midnight <= $end_datetime) {
					$status = 'live';
				} elseif ($end_datetime < $today_midnight) {
					$status = 'past';
				}
			} catch (Exception $e) {
				$event_datetime = null;
			}
		}

		if ($event_datetime instanceof DateTimeImmutable) {
			$formatted_date = $event_datetime->format('j M, Y');
			$formatted_time = strtoupper($event_datetime->format('g:iA'));
			$datetime_iso   = $event_datetime->format(DATE_ATOM);
		}

		$today_items[] = [
			'status'       => $status ? ucfirst($status) : 'Upcoming',
			'date'         => $formatted_date,
			'time'         => $formatted_time ?: '',
			'datetime_iso' => $datetime_iso,
			'title'        => get_the_title($event_id),
			'excerpt'      => wp_trim_words(has_excerpt($event_id) ? get_the_excerpt($event_id) : wp_strip_all_tags($event_post->post_content), 18, '...'),
			'button_label' => $cta_label,
			'link_url'     => get_permalink($event_id),
			'image_url'    => $get_post_image($event_id, 'large', $placeholder_image_card),
			'image_alt'    => $get_post_image_alt($event_id),
		];
	}
	$today_background_image = $get_post_image((int) $today_posts[0]->ID, 'full', $today_background_image);
}
$today_count = count($today_items);

$reflection_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_reflection',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($reflection_posts)) {
	$reflection_background_images = [];
	foreach ($reflection_posts as $reflection_post) {
		$reflection_image = $get_post_image((int) $reflection_post->ID, 'full', '');
		if ($reflection_image !== '' && ! in_array($reflection_image, $reflection_background_images, true)) {
			$reflection_background_images[] = $reflection_image;
		}
	}
	if (empty($reflection_background_images)) {
		$reflection_background_images = [
			get_template_directory_uri() . '/demo-content/images/site/reflections/we-humans/students-1959.webp',
		];
	}
}

$article_exclusions = array_values(array_filter(array_merge([$hero_feature_id], $hero_sidebar_ids)));
$articles_pool      = RECI_Content_Feed::query(
	[
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'post__not_in'   => $article_exclusions,
		'orderby'        => 'date',
		'order'          => 'DESC',
	],
	[
		'image_size'     => 'large',
		'fallback_image' => $placeholder_image_card,
	]
);
if (count($articles_pool) < 3) {
	$fallback_article_items = RECI_Content_Feed::query(
		[
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'orderby'        => 'date',
			'order'          => 'DESC',
		],
		[
			'image_size'     => 'large',
			'fallback_image' => $placeholder_image_card,
		]
	);
	foreach ($fallback_article_items as $item) {
		if (count($articles_pool) >= 3) {
			break;
		}
		if (! in_array((int) ($item['id'] ?? 0), RECI_Content_Feed::ids($articles_pool), true)) {
			$articles_pool[] = $item;
		}
	}
}

$articles_feature_item = $default_article_item;
if (! empty($articles_pool)) {
	$articles_feature_item = $articles_pool[0];
}
$articles_feature_item['title_classes'] = 'self-stretch reci-card-title';
$articles_feature_item['fill_media']    = true;

$articles_side_items = RECI_Content_Feed::slice($articles_pool, 1, 2);

$videos_side_items    = [];
$videos_featured_item = [
	'type_label'   => 'Video',
	'date'         => '08 Jan 2026',
	'meta_value'   => '07:27',
	'title'        => 'The Power of Collective Action',
	'excerpt'      => 'Exploring the transformative potential of intergroup dialogue in fostering understanding and resolving conflicts.',
	'tags'         => ['Dialogue', 'Peace', 'Society'],
	'show_name'        => '',
	'show_url'         => '',
	'category'         => '',
	'sdg_terms'        => [],
	'sphere_terms'     => [],
	'bg_image_url' => $placeholder_image_medium,
	'link_url'    => '#',
];

$video_items = RECI_Content_Feed::query(
	[
		'post_type'      => 'reci_video',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	],
	[
		'image_size'     => 'large',
		'fallback_image' => $placeholder_image_medium,
	]
);
if (! empty($video_items)) {
	$featured_video_item = RECI_Content_Feed::first($video_items);
	if (is_array($featured_video_item)) {
		$videos_featured_item = [
			'type_label'   => 'Video',
			'date'         => $featured_video_item['date'],
			'meta_value'   => $featured_video_item['meta_value'],
			'title'        => $featured_video_item['title'],
			'excerpt'      => $featured_video_item['excerpt'],
			'tags'         => $featured_video_item['tags'],
			'sphere_terms' => $featured_video_item['sphere_terms'] ?? [],
			'sdg_terms'    => $featured_video_item['sdg_terms'] ?? [],
			'category'     => $featured_video_item['category'] ?? '',
			'bg_image_url' => $featured_video_item['image_url'],
			'link_url'     => $featured_video_item['link_url'],
		];
		$videos_side_items = RECI_Content_Feed::slice($video_items, 1, 2);
	}
}

// Quotes of the day — all quotes for carousel.
$quote_items = [
	[
		'quote'  => 'Racial equity work is built through consistent reflection, learning, and action.',
		'author' => '– By RECI',
	],
];

$quote_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_quote',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($quote_posts)) {
	$quote_items = [];
	foreach ($quote_posts as $post) {
		$post_id    = (int) $post->ID;
		$quote_text = (string) get_post_meta($post_id, '_reci_quote_text', true);
		$author     = reci_get_quote_author_data($post_id);
		$text       = $quote_text ?: wp_trim_words(wp_strip_all_tags($post->post_content), 38, '...');
		$name       = $author['name'] ?: get_the_title($post_id);

		if (count($quote_items) < 4) {
			$quote_items[] = [
				'quote'        => $text,
				'author'       => '– By ' . $name,
				'author_role'  => $author['title'] ?: '',
				'author_image' => $author['image_url'],
			];
		}
	}
	if (empty($quote_items)) {
		$quote_items = [['quote' => 'Racial equity work is built through consistent reflection, learning, and action.', 'author' => '– By RECI', 'author_role' => '']];
	}
}

$testimonial_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'rand',
	]
);
$community_slides = [];
if (! empty($testimonial_posts)) {
	foreach ($testimonial_posts as $post) {
		$post_id = (int) $post->ID;
		$text    = (string) get_post_meta($post_id, '_reci_testimonial_text', true);
		$name    = (string) get_post_meta($post_id, '_reci_testimonial_full_name', true);
		$role    = (string) get_post_meta($post_id, '_reci_testimonial_role', true);
		$org     = (string) get_post_meta($post_id, '_reci_testimonial_organization', true);
		$image   = get_the_post_thumbnail_url($post_id, 'thumbnail') ?: '';

		if (empty($text)) continue;

		$community_slides[] = [
			'quote'        => $text,
			'author_name'  => $name ?: get_the_title($post_id),
			'author_role'  => $role,
			'author_org'   => $org,
			'author_image' => $image ?: $placeholder_avatar,
			'author_alt'   => $name ?: 'Testimonial',
		];
	}
}
if (empty($community_slides)) {
	$community_slides = [['quote' => 'RECI has truly transformed how we approach racial equity learning.', 'author_name' => 'Community Member', 'author_role' => 'RECI Contributor', 'author_image' => $placeholder_avatar, 'author_alt' => 'Community Member']];
}
$quote_count     = count($quote_items);
$community_count = count($community_slides);

$podcast_feature_item = [
	'type_label'       => 'Podcast',
	'date'             => '08 Jan 2026',
	'duration'         => '07:27',
	'title'            => 'Decolonizing the Curriculum: A Conversation',
	'tags'             => ['Identity', 'Inclusion', 'Language'],
	'excerpt'          => 'Listen to educators and scholars discuss the vital process of decolonizing academic curricula to include diverse perspectives.',
	'image_url'        => $placeholder_image_large,
	'image_alt'        => 'Podcast feature image',
	'cta_label'        => 'Open episode',
	'cta_url'          => '#',
	'progress_percent' => 35,
];
$podcast_compact_items = [];

$podcast_items = RECI_Content_Feed::query(
	[
		'post_type'      => 'reci_podcast',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	],
	[
		'image_size'     => 'large',
		'fallback_image' => $placeholder_image_large,
	]
);
if (! empty($podcast_items)) {
	$feature_podcast = RECI_Content_Feed::first($podcast_items);
	if (is_array($feature_podcast)) {
		$podcast_feature_item = [
			'type_label'       => 'Podcast',
			'date'             => $feature_podcast['date'],
			'duration'         => $feature_podcast['duration'],
			'title'            => $feature_podcast['title'],
			'tags'             => $feature_podcast['tags'],
			'show_name'        => $feature_podcast['show_name'] ?? '',
			'show_url'         => $feature_podcast['show_url'] ?? '',
			'category'         => $feature_podcast['category'] ?? '',
			'sdg_terms'        => $feature_podcast['sdg_terms'] ?? [],
			'sphere_terms'     => $feature_podcast['sphere_terms'] ?? [],
			'excerpt'          => wp_trim_words((string) $feature_podcast['excerpt'], 24, '...'),
			'image_url'        => $feature_podcast['image_url'],
			'image_alt'        => $feature_podcast['image_alt'],
			'bg_image_url'     => $feature_podcast['image_url'],
			'cta_label'        => ! empty($feature_podcast['video_url']) ? 'Watch episode' : (! empty($feature_podcast['audio_url']) ? 'Listen episode' : 'Open episode'),
			'cta_url'          => ! empty($feature_podcast['video_url']) ? $feature_podcast['video_url'] : (! empty($feature_podcast['audio_url']) ? $feature_podcast['audio_url'] : $feature_podcast['link_url']),
			'progress_percent' => 35,
			'link_url'         => $feature_podcast['link_url'],
			'audio_url'        => $feature_podcast['audio_url'] ?? '',
			'video_url'        => $feature_podcast['video_url'] ?? '',
		];

		foreach (RECI_Content_Feed::slice($podcast_items, 1, 3) as $item) {
			$podcast_compact_items[] = [
				'topic_tags'       => $item['topic_tags'],
				'type_label'       => 'Podcast',
				'date'             => $item['date'],
				'duration'         => $item['duration'],
				'title'            => $item['title'],
				'image_url'        => $item['image_url'],
				'image_alt'        => $item['image_alt'],
				'link_url'         => $item['link_url'],
				'show_name'        => $item['show_name'] ?? '',
				'show_url'         => $item['show_url'] ?? '',
				'category'         => $item['category'] ?? '',
				'sdg_terms'        => $item['sdg_terms'] ?? [],
				'sphere_terms'     => $item['sphere_terms'] ?? [],
				'progress_percent' => 35,
				'audio_url'        => $item['audio_url'] ?? '',
				'video_url'        => $item['video_url'] ?? '',
			];
		}
	}
}

$lens_cards = [
	[
		'title'        => 'Racial anxiety quiz',
		'description'  => 'Assess your comfort levels in cross-racial interactions.',
		'button_label' => 'Start quiz',
	],
];

$assessment_posts = RECI_Post_Query_Service::get_posts(
	[
		'post_type'      => 'reci_assessment',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($assessment_posts)) {
	$lens_cards = [];
	foreach ($assessment_posts as $post) {
		$post_id     = (int) $post->ID;
		$intro       = (string) get_post_meta($post_id, '_reci_assessment_intro', true);
		$estimated   = (string) get_post_meta($post_id, '_reci_assessment_estimated_time', true);
		$description = $intro ?: wp_trim_words(wp_strip_all_tags($post->post_content), 14, '...');
		if ($estimated) {
			$description = trim($description . ' (' . $estimated . ')');
		}
		$lens_cards[] = [
			'title'        => get_the_title($post_id),
			'description'  => $description,
			'button_label' => 'Start quiz',
			'link_url'     => get_permalink($post_id),
		];
	}
}

get_header();
?>

<main>
	<h1 class="sr-only"><?php echo esc_html(get_bloginfo('name')); ?> — <?php echo esc_html(get_bloginfo('description')); ?></h1>
	<!-- Main  -->
	<div class="reci-container pt-8 lg:pt-10 mb-12 lg:mb-24 flex flex-col lg:flex-row justify-start items-stretch gap-6 lg:gap-8 xl:gap-10">
		<?php get_template_part('template-parts/listings/post-item-half-row', null, $hero_feature_item); ?>

		<div class="w-full lg:flex-[0.8] lg:min-h-full lg:pl-8 xl:pl-10 pb-5 border-t-[0.50px] lg:border-t-0 lg:border-l-[0.50px] border-zinc-400 pt-6 lg:pt-0 inline-flex flex-col justify-start items-start gap-5 overflow-hidden">
			<?php foreach ($hero_sidebar_items as $index => $item) : ?>
				<?php get_template_part('template-parts/listings/post-item-compact', null, $item); ?>
				<?php if ($index < count($hero_sidebar_items) - 1) : ?>
					<div data-layer="Vector" class="divider divider-stone"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

		<!-- Reflection Gallery -->
	<div class="reci-container-full flex flex-col min-h-[440px] lg:min-h-[650px] pt-12 lg:pt-24 pb-10 lg:pb-14 relative overflow-hidden" data-reflection-slideshow data-reflection-interval="6000">
		<?php foreach ($reflection_background_images as $index => $reflection_background_image) : ?>
			<div class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-reflection-slide aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>" style="background-image: url('<?php echo esc_url($reflection_background_image); ?>');"></div>
		<?php endforeach; ?>
		<div class="absolute inset-0 bg-neutral-900/70"></div>
		<div class="reci-container flex-1 flex flex-col justify-end items-start gap-8 relative z-10">
			<div data-layer="Frame 1" class="Frame1 self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
				<div data-layer="Reflection Gallery" class="ReflectionGallery text-white reci-section-title font-bold text-7xl">Reflection Gallery</div>
				<a href="<?php echo esc_url(get_post_type_archive_link('reci_reflection') ?: home_url('/reflections/')); ?>" class="btn btn-primary btn-md">Reflection gallery</a>
			</div>
			<div data-layer="Vector 3" class=" divider divider-white-50"></div>
			<div data-layer="Description" class="self-stretch justify-start text-white text-3xl font-normal font-accent leading-10 ">Dive into the Racial Equity Consciousness Institute's Virtual Reflection Gallery – a unique space we've created for you to connect with the stories and perspectives of civil rights leaders and activists.</div>
		</div>
	</div>


	<!-- Articles -->
	<div class="reci-container w-full py-10 flex flex-col justify-center items-start gap-8 lg:gap-10">
		<div class=" self-stretch flex flex-row justify-between items-start items-center gap-4 border-b border-zinc-400 pb-8">
			<div class=" flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Articles" class="Articles text-neutral-800 reci-section-title font-bold">Articles</div>
			</div>
			<a href="<?php echo esc_url((get_option('page_for_posts') ? get_post_type_archive_link('post') : home_url('/articles/'))); ?>" class="btn btn-outline-primary btn-sm md:btn-md">View all</a>
		</div>
		<div class=" self-stretch flex flex-col lg:flex-row justify-start items-stretch gap-6 lg:gap-10 border-b border-zinc-400 pb-8">
			<div class="w-full lg:flex-[1.2] self-stretch flex">
				<?php get_template_part('template-parts/listings/post-item-half-row', null, $articles_feature_item); ?>
			</div>
			<?php get_template_part('template-parts/listings/articles-side-rail', null, ['items' => $articles_side_items]); ?>
		</div>
	</div>


	<!-- Today at RECI carousel -->
	<div class="reci-container-full min-h-[540px] lg:min-h-[750px] py-12 lg:pt-24 lg:pb-14 relative overflow-hidden" data-carousel-background-wrapper style="background-image: url('<?php echo esc_url($today_background_image); ?>'); background-size: cover; background-position: center;">
		<div data-layer="Overlay" class="Overlay absolute inset-0 bg-gradient-to-l from-neutral-800 to-white/0"></div>
		<div class="reci-container flex flex-col justify-start items-start">
			<div class="w-auto mr-auto  z-50 p-6 sm:p-10 bg-white rounded-lg flex flex-col justify-start items-start gap-5" data-carousel="today">
				<div data-layer="Today at RECI" class="TodayAtReci self-stretch text-neutral-800 reci-section-title font-bold">Today at RECI</div>
				<div data-layer="Vector 3" class=" divider divider-zinc"></div>
				<?php foreach ($today_items as $i => $today_item) : ?>
					<div data-carousel-item data-carousel-background-image="<?php echo esc_url($today_item['image_url'] ?? $today_background_image); ?>" <?php if ($i > 0) echo 'class="hidden"'; ?>>
						<?php get_template_part('template-parts/listings/event-carousel-card', null, $today_item); ?>
					</div>
				<?php endforeach; ?>
				<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
					<?php for ($i = 0; $i < $today_count; $i++) : ?>
						<button data-carousel-dot class="Pagination flex-1 h-2 rounded-full cursor-pointer transition-colors <?php echo $i === 0 ? 'bg-amber-400' : 'bg-zinc-400 hover:bg-zinc-300'; ?>" aria-label="<?php echo esc_attr('Go to event ' . ($i + 1)); ?>"></button>
					<?php endfor; ?>
				</div>
			</div>
		</div>

	</div>


	<!-- Videos-->
	<div class="reci-container py-10 flex flex-col justify-start items-start gap-8 lg:gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch flex flex-row justify-between items-center gap-4 border-b border-zinc-400 pb-8">
			<div class="Content w-full lg:w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Videos" class="Videos text-neutral-800 reci-section-title font-bold">Videos</div>
			</div>
			<a href="<?php echo esc_url(get_post_type_archive_link('reci_video') ?: home_url('/videos/')); ?>" class="btn btn-outline-primary btn-md">View all</a>
		</div>
		<div class="Content self-stretch flex flex-col lg:flex-row justify-start items-stretch gap-6 lg:gap-10">
			<div class="Content w-full lg:flex-1 lg:min-h-[701px] lg:pr-10 overflow-hidden border-b-[0.50px] lg:border-b-0 lg:border-r-[0.50px] border-zinc-400 pb-6 lg:pb-0 inline-flex flex-col justify-start items-start gap-5">
				<?php foreach ($videos_side_items as $index => $item) : ?>
					<?php get_template_part('template-parts/listings/articles-side-card', null, $item); ?>
					<?php if ($index < count($videos_side_items) - 1) : ?>
						<div data-layer="Vector" class="divider divider-stone"></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php get_template_part('template-parts/listings/videos-featured-overlay-card', null, $videos_featured_item); ?>
		</div>
		<div data-layer="Vector 5" class="Vector5 divider divider-zinc"></div>
	</div>

	<!-- Quotes of the day carousel -->
	<div class="reci-container-full p-12 lg:p-24 bg-blue-900">
		<div class="reci-container self-stretch p-10 bg-blue-800 rounded-lg flex flex-col justify-start items-center gap-5" data-carousel="quotes">
			<div data-layer="Reflection of the Day" class="QuotesOfTheDay self-stretch text-center text-white reci-section-title font-bold">Reflection of the Day</div>
			<div data-layer="Vector 3" class=" divider divider-slate"></div>
			<?php foreach ($quote_items as $i => $quote_item) : ?>
				<div data-carousel-item <?php if ($i > 0) echo 'class="hidden"'; ?>>
					<?php get_template_part('template-parts/listings/quote-carousel-item', null, $quote_item); ?>
				</div>
			<?php endforeach; ?>
			<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
				<?php for ($i = 0; $i < $quote_count; $i++) : ?>
					<button data-carousel-dot class="Pagination flex-1 h-2 rounded-full cursor-pointer transition-colors <?php echo $i === 0 ? 'bg-amber-400' : 'bg-stone-300 hover:bg-stone-200'; ?>" aria-label="<?php echo esc_attr('Quote ' . ($i + 1)); ?>"></button>
				<?php endfor; ?>
			</div>
		</div>
	</div>
	<!-- podcast-->
	<div class=" reci-container py-10 flex flex-col justify-start items-center gap-8 lg:gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch flex flex-row justify-between items-center gap-4 border-b border-zinc-400 pb-8">
			<div class="Content w-full lg:w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Podcasts" class="Podcasts text-neutral-800 reci-section-title font-bold">Podcasts</div>
			</div>
			<a href="<?php echo esc_url(get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/')); ?>" class="btn btn-outline-primary btn-sm md:btn-md">View all</a>
		</div>
		<div class="Content self-stretch flex flex-col lg:flex-row justify-start items-stretch gap-6 lg:gap-10">
			<?php get_template_part('template-parts/listings/podcast-feature-card', null, $podcast_feature_item); ?>
			<div class="Content w-full lg:flex-1 lg:min-h-[701px] lg:pr-10 overflow-hidden border-b-[0.50px] lg:border-b-0 lg:border-r-[0.50px] border-zinc-400 pb-6 lg:pb-0 inline-flex flex-col justify-start items-start gap-5">
				<?php foreach ($podcast_compact_items as $index => $item) : ?>
					<?php get_template_part('template-parts/listings/podcast-compact-card', null, $item); ?>
					<?php if ($index < count($podcast_compact_items) - 1) : ?>
						<div data-layer="Vector" class="divider divider-stone"></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<!-- Check Lens -->
	<div class=" reci-container-full p-12 lg:p-24 bg-white ">
		<div class="reci-container  self-stretch py-14 lg:py-20 bg-slate-100 rounded-lg flex flex-col justify-start items-center gap-10 lg:gap-14">
			<div class="self-stretch flex flex-row justify-between items-center gap-4">
				<div data-layer="Check your lens" class="CheckYourLens text-neutral-800 reci-section-title font-bold">Check your lens</div>
				<a href="<?php echo esc_url(get_post_type_archive_link('reci_assessment') ?: home_url('/quizzes/')); ?>" class="btn btn-outline-primary btn-sm md:btn-md">View all</a>
			</div>
			<?php get_template_part('template-parts/listings/lens-quiz-row', null, ['cards' => $lens_cards]); ?>
		</div>
	</div>
	<?php get_template_part('template-parts/common/community-engagement-section', null, [
		'enabled'      => true,
		'show_join'    => true,
		'show_pulse'   => true,
		'pulse_slides' => $community_slides,
	]); ?>
</main>

<?php get_footer(); ?>
