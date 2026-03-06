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

$get_post_tags = static function (int $post_id, int $limit = 3): array {
	$tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
	if (is_wp_error($tags)) {
		$tags = [];
	}
	$tags = array_values(array_unique(array_filter(array_map('trim', $tags))));
	if (count($tags) < $limit) {
		$cats = wp_get_post_terms($post_id, 'category', ['fields' => 'names']);
		if (! is_wp_error($cats)) {
			foreach ($cats as $cat_name) {
				if (count($tags) >= $limit) {
					break;
				}
				if (! in_array($cat_name, $tags, true)) {
					$tags[] = $cat_name;
				}
			}
		}
	}
	return array_slice($tags, 0, $limit);
};

$get_duration_label = static function (int $post_id, string $label_key, string $secs_key): string {
	$label = (string) get_post_meta($post_id, $label_key, true);
	if ($label !== '') {
		return $label;
	}

	$seconds = (int) get_post_meta($post_id, $secs_key, true);
	if ($seconds <= 0) {
		return '00:00';
	}

	$hours = (int) floor($seconds / 3600);
	$mins  = (int) floor(($seconds % 3600) / 60);
	$secs  = (int) ($seconds % 60);

	if ($hours > 0) {
		return sprintf('%d:%02d:%02d', $hours, $mins, $secs);
	}

	return sprintf('%02d:%02d', $mins, $secs);
};

$build_content_card = static function (WP_Post $post) use ($get_post_image, $get_post_image_alt, $get_post_tags, $get_duration_label, $placeholder_image_card): array {
	$post_id   = (int) $post->ID;
	$post_type = get_post_type($post_id);
	$date      = get_the_date('d M Y', $post_id);
	$title     = get_the_title($post_id);
	$excerpt   = wp_trim_words(has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_strip_all_tags($post->post_content), 22, '...');
	$item      = [
		'type_label'       => 'Article',
		'type_badge_class' => 'bg-amber-400',
		'type_text_class'  => 'text-neutral-800',
		'date'             => $date,
		'meta_icon'        => 'timer',
		'meta_value'       => '3 mins',
		'meta_value_size'  => 'text-sm',
		'title'            => $title,
		'excerpt'          => $excerpt,
		'tags'             => $get_post_tags($post_id, 3),
		'image_url'        => $get_post_image($post_id, 'large', $placeholder_image_card),
		'image_alt'        => $get_post_image_alt($post_id),
			'link_url'         => get_permalink($post_id),
	];

	if ('reci_podcast' === $post_type) {
		$item['type_label']       = 'Podcast';
		$item['type_badge_class'] = 'bg-neutral-800';
		$item['type_text_class']  = 'text-white';
		$item['meta_icon']        = 'audio';
		$item['meta_value']       = $get_duration_label($post_id, '_reci_podcast_duration_label', '_reci_podcast_duration_secs');
	} elseif ('reci_video' === $post_type) {
		$item['type_label']       = 'Video';
		$item['type_badge_class'] = 'bg-blue-900';
		$item['type_text_class']  = 'text-white';
		$item['meta_icon']        = 'video';
		$item['meta_value']       = $get_duration_label($post_id, '_reci_video_duration_label', '_reci_video_duration_secs');
	} else {
		$item['meta_value'] = (string) get_post_meta($post_id, '_reci_article_read_time_label', true) ?: '3 mins';
	}

	return $item;
};

$hero_feature_item = [
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
$hero_feature_id   = 0;
$hero_sidebar_ids  = [];

$hero_feature_query = new WP_Query(
	[
		'post_type'      => 'reci_article',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_reci_article_featured_rank',
		'orderby'        => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
	]
);
if (! empty($hero_feature_query->posts)) {
	$hero_feature_post = $hero_feature_query->posts[0];
	$hero_feature_item = $build_content_card($hero_feature_post);
	$hero_feature_id   = (int) $hero_feature_post->ID;
}

$hero_sidebar_items = [];
$hero_sidebar_query = new WP_Query(
	[
		'post_type'      => ['reci_article', 'reci_podcast', 'reci_video'],
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'post__not_in'   => $hero_feature_id ? [$hero_feature_id] : [],
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
foreach ($hero_sidebar_query->posts as $post) {
	$item                    = $build_content_card($post);
	$item['meta_value_size'] = 'text-sm';
	$hero_sidebar_items[]    = $item;
	$hero_sidebar_ids[]      = (int) $post->ID;
}

// Today at RECI — fetch up to 4 upcoming events for carousel.
$today_items = [
	[
		'status'       => 'Upcoming',
		'date'         => '20 July, 2026',
		'time'         => '3 PM EST',
		'title'        => 'Online Seminar on Decolonization Education',
		'excerpt'      => 'Join us for an upcoming RECI event focused on practical equity learning.',
		'button_label' => 'Register',
		'link_url'     => '#',
		'image_url'    => $placeholder_image_card,
		'image_alt'    => 'Event visual',
	],
];

$today_query = new WP_Query(
	[
		'post_type'      => 'reci_event',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'meta_key'       => '_reci_event_start_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => [
			[
				'key'     => '_reci_event_status',
				'value'   => ['upcoming', 'Upcoming'],
				'compare' => 'IN',
			],
		],
	]
);
if (! empty($today_query->posts)) {
	$today_items = [];
	foreach ($today_query->posts as $event_post) {
		$event_id        = (int) $event_post->ID;
		$status          = (string) get_post_meta($event_id, '_reci_event_status', true);
		$start_date_raw  = (string) get_post_meta($event_id, '_reci_event_start_date', true);
		$start_time_raw  = (string) get_post_meta($event_id, '_reci_event_start_time', true);
		$timezone_raw    = (string) get_post_meta($event_id, '_reci_event_timezone', true);
		$cta_label       = (string) get_post_meta($event_id, '_reci_event_cta_label', true) ?: 'Register';
		$registration    = (string) get_post_meta($event_id, '_reci_event_registration_url', true);
		$start_timestamp = $start_date_raw ? strtotime($start_date_raw) : false;
		$formatted_date  = $start_timestamp ? wp_date('j F, Y', $start_timestamp) : get_the_date('j F, Y', $event_id);
		$formatted_time  = trim($start_time_raw . ($timezone_raw ? ' ' . $timezone_raw : ''));
		$today_items[] = [
			'status'       => $status ? ucfirst($status) : 'Upcoming',
			'date'         => $formatted_date,
			'time'         => $formatted_time ?: '',
			'title'        => get_the_title($event_id),
			'excerpt'      => wp_trim_words(has_excerpt($event_id) ? get_the_excerpt($event_id) : wp_strip_all_tags($event_post->post_content), 18, '...'),
			'button_label' => $cta_label,
			'link_url'     => $registration ?: get_permalink($event_id),
			'image_url'    => $get_post_image($event_id, 'large', $placeholder_image_card),
			'image_alt'    => $get_post_image_alt($event_id),
		];
	}
	$today_background_image = $get_post_image((int) $today_query->posts[0]->ID, 'full', $today_background_image);
}
$today_count = count($today_items);

$article_exclusions = array_values(array_filter(array_merge([$hero_feature_id], $hero_sidebar_ids)));
$articles_pool      = [];
$articles_query     = new WP_Query(
	[
		'post_type'      => 'reci_article',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'post__not_in'   => $article_exclusions,
		'orderby'        => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
		'meta_key'       => '_reci_article_featured_rank',
	]
);
foreach ($articles_query->posts as $post) {
	$articles_pool[] = $post;
}
if (count($articles_pool) < 3) {
	$fallback_articles_query = new WP_Query(
		[
			'post_type'      => 'reci_article',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'orderby'        => ['meta_value_num' => 'ASC', 'date' => 'DESC'],
			'meta_key'       => '_reci_article_featured_rank',
		]
	);
	foreach ($fallback_articles_query->posts as $post) {
		if (count($articles_pool) >= 3) {
			break;
		}
		if (! in_array((int) $post->ID, wp_list_pluck($articles_pool, 'ID'), true)) {
			$articles_pool[] = $post;
		}
	}
}

$articles_feature_item = $hero_feature_item;
if (! empty($articles_pool)) {
	$articles_feature_item = $build_content_card($articles_pool[0]);
}
$articles_feature_item['title_classes'] = "self-stretch justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-10";

$articles_side_items = [];
foreach (array_slice($articles_pool, 1, 2) as $post) {
	$item                  = $build_content_card($post);
	$item['meta_icon']     = 'timer';
	$item['meta_value']    = (string) get_post_meta((int) $post->ID, '_reci_article_read_time_label', true) ?: '3 mins';
	$articles_side_items[] = $item;
}

$videos_side_items    = [];
$videos_featured_item = [
	'type_label'   => 'Video',
	'date'         => '08 Jan 2026',
	'meta_value'   => '07:27',
	'title'        => 'The Power of Collective Action',
	'excerpt'      => 'Exploring the transformative potential of intergroup dialogue in fostering understanding and resolving conflicts.',
	'tags'         => ['Dialogue', 'Peace', 'Society'],
	'bg_image_url' => $placeholder_image_medium,
	'link_url'    => '#',
];

$videos_query = new WP_Query(
	[
		'post_type'      => 'reci_video',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($videos_query->posts)) {
	$featured_video       = $videos_query->posts[0];
	$featured_video_item  = $build_content_card($featured_video);
	$videos_featured_item = [
		'type_label'   => 'Video',
		'date'         => $featured_video_item['date'],
		'meta_value'   => $featured_video_item['meta_value'],
		'title'        => $featured_video_item['title'],
		'excerpt'      => $featured_video_item['excerpt'],
		'tags'         => $featured_video_item['tags'],
		'bg_image_url' => $get_post_image((int) $featured_video->ID, 'large', $placeholder_image_medium),
			'link_url'    => get_permalink((int) $featured_video->ID),
	];
	foreach (array_slice($videos_query->posts, 1, 2) as $post) {
		$item               = $build_content_card($post);
		$item['meta_icon']  = 'video';
		$videos_side_items[] = $item;
	}
}

// Quotes of the day — all quotes for carousel.
$quote_items = [
	[
		'quote'  => 'Racial equity work is built through consistent reflection, learning, and action.',
		'author' => '– By RECI',
	],
];

$community_slides = [
	[
		'quote'        => 'RECI has truly transformed how we approach racial equity learning.',
		'author_name'  => 'Community Member',
		'author_role'  => 'RECI Contributor',
		'author_image' => $placeholder_avatar,
		'author_alt'   => 'Community Member',
	],
];

$quotes_query = new WP_Query(
	[
		'post_type'      => 'reci_quote',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($quotes_query->posts)) {
	$quote_items      = [];
	$community_slides = [];
	foreach ($quotes_query->posts as $post) {
		$post_id     = (int) $post->ID;
		$quote_text  = (string) get_post_meta($post_id, '_reci_quote_text', true);
		$author_name = (string) get_post_meta($post_id, '_reci_quote_author_name', true);
		$author_role = (string) get_post_meta($post_id, '_reci_quote_author_title', true);
		$author_img  = (string) get_post_meta($post_id, '_reci_quote_author_image_url', true);
		$text        = $quote_text ?: wp_trim_words(wp_strip_all_tags($post->post_content), 38, '...');
		$name        = $author_name ?: get_the_title($post_id);

		if (count($quote_items) < 4) {
			$quote_items[] = [
				'quote'  => $text,
				'author' => '– By ' . $name,
			];
		}
		if (count($community_slides) < 4) {
			$community_slides[] = [
				'quote'        => wp_trim_words($text, 28, '...'),
				'author_name'  => $name,
				'author_role'  => $author_role ?: 'RECI Contributor',
				'author_image' => $author_img ?: $get_post_image($post_id, 'thumbnail', $placeholder_avatar),
				'author_alt'   => $name,
			];
		}
	}
	if (empty($quote_items)) {
		$quote_items = [['quote' => 'Racial equity work is built through consistent reflection, learning, and action.', 'author' => '– By RECI']];
	}
	if (empty($community_slides)) {
		$community_slides = [['quote' => 'RECI has truly transformed how we approach racial equity learning.', 'author_name' => 'Community Member', 'author_role' => 'RECI Contributor', 'author_image' => $placeholder_avatar, 'author_alt' => 'Community Member']];
	}
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
	'progress_percent' => 35,
];
$podcast_compact_items = [];

$podcasts_query = new WP_Query(
	[
		'post_type'      => 'reci_podcast',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($podcasts_query->posts)) {
	$feature_podcast      = $podcasts_query->posts[0];
	$feature_id           = (int) $feature_podcast->ID;
	$feature_duration     = $get_duration_label($feature_id, '_reci_podcast_duration_label', '_reci_podcast_duration_secs');
	$podcast_feature_item = [
		'type_label'       => 'Podcast',
		'date'             => get_the_date('d M Y', $feature_id),
		'duration'         => $feature_duration,
		'title'            => get_the_title($feature_id),
		'tags'             => $get_post_tags($feature_id, 3),
		'excerpt'          => wp_trim_words(has_excerpt($feature_id) ? get_the_excerpt($feature_id) : wp_strip_all_tags($feature_podcast->post_content), 24, '...'),
		'image_url'        => $get_post_image($feature_id, 'large', $placeholder_image_large),
		'image_alt'        => $get_post_image_alt($feature_id),
		'progress_percent' => 35,
		'link_url'         => get_permalink($feature_id),
	];

	foreach (array_slice($podcasts_query->posts, 1, 3) as $post) {
		$post_id               = (int) $post->ID;
		$podcast_compact_items[] = [
			'topic_tags'       => $get_post_tags($post_id, 3),
			'type_label'       => 'Podcast',
			'date'             => get_the_date('d M Y', $post_id),
			'duration'         => $get_duration_label($post_id, '_reci_podcast_duration_label', '_reci_podcast_duration_secs'),
			'title'            => get_the_title($post_id),
			'image_url'        => $get_post_image($post_id, 'medium', $placeholder_image_card),
			'image_alt'        => $get_post_image_alt($post_id),
			'link_url'         => get_permalink($post_id),
			'progress_percent' => 35,
		];
	}
}

$lens_cards = [
	[
		'title'        => 'Racial anxiety quiz',
		'description'  => 'Assess your comfort levels in cross-racial interactions.',
		'button_label' => 'Start quiz',
	],
];

$assessments_query = new WP_Query(
	[
		'post_type'      => 'reci_assessment',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]
);
if (! empty($assessments_query->posts)) {
	$lens_cards = [];
	foreach ($assessments_query->posts as $post) {
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
		];
	}
}

get_header();
?>

<div class="bg-slate-100 flex flex-col justify-start items-center overflow-hidden">
	<div class="reci-container pt-8 lg:pt-10 pb-12 lg:pb-24 flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-10">
		<?php get_template_part('template-parts/listings/post-item-half-row', null, $hero_feature_item); ?>

		<div data-layer="Content" class="Content w-full lg:flex-1 lg:min-h-[700px] lg:pl-10 pb-5 border-t-[0.50px] lg:border-t-0 lg:border-l-[0.50px] border-zinc-400 pt-6 lg:pt-0 inline-flex flex-col justify-start items-start gap-5 overflow-hidden">
			<?php foreach ($hero_sidebar_items as $index => $item) : ?>
				<?php get_template_part('template-parts/listings/post-item-compact', null, $item); ?>
				<?php if ($index < count($hero_sidebar_items) - 1) : ?>
					<div data-layer="Vector" class="divider divider-stone"></div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- Today at RECI carousel -->
	<div class="reci-container-full min-h-[540px] lg:min-h-[750px] pt-12 lg:pt-24 pb-10 lg:pb-14 relative" style="background-image: url('<?php echo esc_url($today_background_image); ?>'); background-size: cover; background-position: center;">
		<div data-layer="Overlay" class="Overlay absolute inset-0 bg-gradient-to-l from-neutral-800 to-white/0"></div>
		<div class="reci-container flex flex-col justify-start items-start">
			<div class="w-auto mr-auto  z-50 p-10 bg-white rounded-lg flex flex-col justify-start items-start gap-5" data-carousel="today">
				<div data-layer="Today at RECI" class="TodayAtReci self-stretch justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Today at RECI</div>
				<div data-layer="Vector 3" class=" divider divider-zinc"></div>
				<?php foreach ($today_items as $i => $today_item) : ?>
					<div data-carousel-item <?php if ($i > 0) echo 'class="hidden"'; ?>>
						<?php get_template_part('template-parts/listings/event-carousel-card', null, $today_item); ?>
					</div>
				<?php endforeach; ?>
				<?php if ($today_count > 1) : ?>
					<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
						<?php for ($i = 0; $i < $today_count; $i++) : ?>
							<div data-carousel-dot class="Pagination flex-1 h-0.5 relative <?php echo $i === 0 ? 'bg-amber-400' : 'bg-zinc-400'; ?>" role="button" aria-label="<?php echo esc_attr('Go to event ' . ($i + 1)); ?>"></div>
						<?php endfor; ?>
					</div>
				<?php else : ?>
					<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
						<div class="Pagination flex-1 h-0.5 relative bg-amber-400"></div>
						<div class="Pagination flex-1 h-0.5 relative bg-zinc-400"></div>
						<div class="Pagination flex-1 h-0.5 relative bg-zinc-400"></div>
						<div class="Pagination flex-1 h-0.5 relative bg-zinc-400"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>

	<!-- Articles -->
	<div class="reci-container py-10 flex flex-col justify-center items-start gap-8 lg:gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch flex flex-col sm:flex-row justify-between items-start lg:items-center gap-4 border-b border-zinc-400 pb-8">
			<div data-layer="Content" class=" flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Articles" class="Articles justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Articles</div>
			</div>
			<a href="<?php echo esc_url(get_post_type_archive_link('reci_article') ?: home_url('/articles/')); ?>" class="btn btn-outline-primary btn-md">View all</a>
		</div>
		<div data-layer="Content" class="Content self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-10 border-b border-zinc-400 pb-8">
			<?php get_template_part('template-parts/listings/post-item-half-row', null, $articles_feature_item); ?>
			<?php get_template_part('template-parts/listings/articles-side-rail', null, ['items' => $articles_side_items]); ?>
		</div>
	</div>

	<!-- Reflection Gallery -->
	<div class="reci-container-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-12 lg:pt-24 pb-10 lg:pb-14 bg-neutral-800/60  lg:gap-10" style="background-image: url('/wp-content/uploads/2026/03/reflection2.png'); background-size: cover; background-position: center;">
		<div class="reci-container min-h-[100vh] flex flex-col justify-end items-start gap-8">
			<div data-layer="Frame 1" class="Frame1 self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
				<div data-layer="Reflection Gallery" class="ReflectionGallery justify-start text-white text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Reflection Gallery</div>
				<a href="<?php echo esc_url(home_url('/reflection-gallery/')); ?>" class="btn btn-primary btn-md">Reflection gallery</a>
			</div>
			<div data-layer="Vector 3" class=" divider divider-white-50"></div>
			<div data-layer="Description" class="self-stretch justify-start text-white text-2xl font-normal font-['EB_Garamond'] leading-10 tracking-tight">Dive into the Racial Equity Consciousness Institute's Virtual Reflection Gallery – a unique space we've created for you to connect with the stories and perspectives of civil rights leaders and activists.</div>
		</div>
	</div>
	<!-- Videos-->
	<div class="reci-container py-10 flex flex-col justify-start items-start gap-8 lg:gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
			<div data-layer="Content" class="Content w-full lg:w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Videos" class="Videos justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Videos</div>
			</div>
			<a href="<?php echo esc_url(get_post_type_archive_link('reci_video') ?: home_url('/videos/')); ?>" class="btn btn-outline-primary btn-md">View all</a>
		</div>
		<div data-layer="Vector 3" class=" divider divider-zinc"></div>
		<div data-layer="Content" class="Content self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-10">
			<div data-layer="Content" class="Content w-full lg:flex-1 lg:min-h-[701px] lg:pr-10 overflow-hidden border-b-[0.50px] lg:border-b-0 lg:border-r-[0.50px] border-zinc-400 pb-6 lg:pb-0 inline-flex flex-col justify-start items-start gap-5">
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
	<div class="reci-container-full px-4 sm:px-6 lg:px-20 xl:px-36 py-12 lg:py-24 bg-blue-900 ">
		<div class="reci-container self-stretch p-10 bg-blue-800 rounded-lg flex flex-col justify-start items-start gap-5" data-carousel="quotes">
			<div data-layer="Quotes of the day" class="QuotesOfTheDay self-stretch text-center justify-start text-white text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Quotes of the day</div>
			<div data-layer="Vector 3" class=" divider divider-slate"></div>
			<?php foreach ($quote_items as $i => $quote_item) : ?>
				<div data-carousel-item <?php if ($i > 0) echo 'class="hidden"'; ?>>
					<?php get_template_part('template-parts/listings/quote-carousel-item', null, $quote_item); ?>
				</div>
			<?php endforeach; ?>
			<?php if ($quote_count > 1) : ?>
				<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
					<?php for ($i = 0; $i < $quote_count; $i++) : ?>
						<div data-carousel-dot class="Pagination flex-1 h-0.5 relative <?php echo $i === 0 ? 'bg-amber-400' : 'bg-stone-300'; ?>" role="button" aria-label="<?php echo esc_attr('Quote ' . ($i + 1)); ?>"></div>
					<?php endfor; ?>
				</div>
			<?php else : ?>
				<div class="Container self-stretch inline-flex justify-start items-center gap-2.5">
					<div class="Pagination flex-1 h-0.5 relative bg-amber-400"></div>
					<div class="Pagination flex-1 h-0.5 relative bg-stone-300"></div>
					<div class="Pagination flex-1 h-0.5 relative bg-stone-300"></div>
					<div class="Pagination flex-1 h-0.5 relative bg-stone-300"></div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<!-- podcast-->
	<div class=" reci-container py-10 flex flex-col justify-start items-center gap-8 lg:gap-10">
		<div data-layer="Cotnent" class="Cotnent self-stretch flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
			<div data-layer="Content" class="Content w-full lg:w-[652.50px] flex justify-start items-center gap-2">
				<div data-layer="Tag" class="Tag tag-dot"></div>
				<div data-layer="Podcasts" class="Podcasts justify-start text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-[50.40px]">Podcasts</div>
			</div>
			<a href="<?php echo esc_url(get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/')); ?>" class="btn btn-outline-primary btn-md">View all</a>
		</div>
		<div data-layer="Vector 3" class=" divider divider-zinc"></div>
		<?php get_template_part('template-parts/listings/podcast-feature-card', null, $podcast_feature_item); ?>
		<div data-layer="Vector 5" class="Vector5 divider divider-zinc"></div>
		<div class="-mx-4 sm:-mx-6 lg:-mx-12 xl:-mx-20 self-stretch">
			<?php get_template_part('template-parts/listings/podcast-compact-row', null, ['items' => $podcast_compact_items]); ?>
		</div>
		<div data-layer="Vector 4" class="Vector4 divider divider-zinc"></div>
	</div>

	<div class=" reci-container-full py-12 lg:py-24 bg-white ">
		<div data-layer="Content" class="reci-container  self-stretch py-10 lg:py-14 bg-slate-100 rounded-lg flex flex-col justify-start items-start gap-8 lg:gap-10">
			<div data-layer="content" class="self-stretch px-4 sm:px-6 lg:px-14 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
				<div data-layer="Check your lens" class="CheckYourLens justify-start text-neutral-800 text-5xl font-medium font-['EB_Garamond'] leading-[50.40px]">Check your lens</div>
				<a href="<?php echo esc_url(get_post_type_archive_link('reci_assessment') ?: home_url('/assessments/')); ?>" class="btn btn-outline-primary btn-md">View all</a>
			</div>
			<?php get_template_part('template-parts/listings/lens-quiz-row', null, ['cards' => $lens_cards]); ?>
		</div>
	</div>

	<div class=" reci-container-full py-12 lg:py-24 bg-neutral-800 ">
		<div class="reci-container flex flex-col justify-start items-start gap-12 lg:gap-24">
			<div data-layer="Content" class="Content self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-10">
				<img data-layer="Image" class="Image flex-1 self-stretch p-2.5 rounded-lg border-b-[11px] border-amber-400" src="<?php echo get_template_directory_uri() . '/assets/images/connect-now3.png'; ?>" alt="Connect elements" />
				<div data-layer="Content" class="Content inline-flex flex-col justify-start items-start gap-10">
					<div data-layer="Tag" class="Tag flex flex-col justify-start items-start gap-2.5">
						<div data-layer="Tag" class="Tag px-2 py-1 bg-amber-400 rounded inline-flex justify-center items-center gap-2.5">
							<div data-layer="Connect Elements" class="ConnectElements justify-start text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4">Connect Elements</div>
						</div>
						<div data-layer="Title" class="w-full lg:max-w-[785px] justify-start text-white text-4xl lg:text-6xl font-semibold font-['EB_Garamond'] leading-tight lg:leading-[74.40px]">Connect your interests to improve your feed and discover more relevant content</div>
					</div>
					<a href="<?php echo esc_url(is_user_logged_in() ? home_url('/my-account/') : home_url('/sign-up/')); ?>" class="btn btn-primary btn-md">Connect Now</a>
				</div>
			</div>
			<div data-layer="Vector 4" class="Vector4 divider divider-zinc"></div>

			<!-- Community Pulse carousel -->
			<div data-layer="Content" class="Content self-stretch p-6 lg:p-14 relative bg-neutral-600 rounded-lg flex flex-col xl:flex-row justify-start items-start gap-8 xl:gap-28" data-carousel="community">
				<div data-layer="Content" class="Content inline-flex flex-col justify-start items-start gap-2.5">
					<div data-layer="Content" class="Content w-24 h-6"></div>
					<div data-layer="Community Pulse" class="CommunityPulse w-56 justify-start text-white text-5xl font-medium font-['EB_Garamond'] leading-[50.40px]">Community Pulse</div>
				</div>
				<div data-layer="format-quote-open" class="FormatQuoteOpen hidden lg:block w-20 h-20 left-[307px] top-[87px] absolute overflow-hidden">
					<div data-layer="Vector" class="Vector w-12 h-8 left-[16.67px] top-[23.33px] absolute bg-amber-400"></div>
				</div>
				<?php foreach ($community_slides as $i => $slide) : ?>
					<div data-carousel-item <?php if ($i > 0) echo 'class="hidden"'; ?>>
						<?php get_template_part('template-parts/listings/community-pulse-slide', null, $slide); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
