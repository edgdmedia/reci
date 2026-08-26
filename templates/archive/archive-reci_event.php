<?php
/**
 * Native archive route for reci_event.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$page_title    = 'Events';
$page_subtitle = 'From immersive learning cohorts to film screenings, symposia, and days of reflection — RECI events turn ideas about racial equity into shared experience and collective action.';

$location_terms = get_terms(
	[
		'taxonomy'   => 'reci_location',
		'hide_empty' => true,
	]
);
if (is_wp_error($location_terms)) {
	$location_terms = [];
}

$current_location = isset($_GET['location']) ? sanitize_title((string) wp_unslash($_GET['location'])) : '';
$current_search   = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';

$base_url        = is_post_type_archive('reci_event') ? get_post_type_archive_link('reci_event') : get_permalink();
$base_url        = $base_url ?: home_url('/events/');
$all_filters_url = remove_query_arg(['location', 'search', 'paged'], $base_url);
$has_filters     = ($current_location !== '') || ($current_search !== '');

$placeholder_image = function_exists('reci_get_fallback_thumbnail_url') ? reci_get_fallback_thumbnail_url('large', 'https://placehold.co/800x500/EEE/999?text=Event') : 'https://placehold.co/800x500/EEE/999?text=Event';

$listing_config = [
	'post_type'                => 'reci_event',
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'event_card',
	'fallback_image'           => $placeholder_image,
	'wrapper_class'            => 'self-stretch grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'filter_taxonomies'        => [
		'reci_location' => [
			'param' => 'location',
			'field' => 'slug',
		],
	],
	'empty_message'            => 'No events found for this filter combination.',
];

get_header();
?>

<main class="layout-page">
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $page_subtitle,
	]); ?>

	<section class="reci-container pt-12 pb-8 flex flex-col gap-14">
		<!-- Intro paragraph -->
		<div class="max-w-4xl">
			<p class="text-neutral-600 text-xl font-normal leading-8">
				<?php echo esc_html('Change rarely happens alone. Our events bring together practitioners, educators, students, and community members to learn the frameworks, sit with hard questions, and build the relationships that sustain equity work over the long haul. What begins in a single room in Pittsburgh now reaches a global community — our cohorts and conversations have traveled from South Africa to Switzerland and back.'); ?>
			</p>
		</div>
		
		<!-- Event Types Grid -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php
			$event_types = [
				[
					'title' => 'Learning Cohorts',
					'desc'  => 'Our signature Fall and Spring cohorts take a critical systems-thinking approach to understanding how racism operates and how to cultivate equity. Immersive, discussion-driven, and grounded in the racial equity consciousness framework.',
					'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
				],
				[
					'title' => 'Film & Screenings',
					'desc'  => 'Public screenings and conversations, including Illuminating the Vaccine for Racism, paired with panels, reflection galleries, and community dialogue.',
					'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>',
				],
				[
					'title' => 'Symposia & Speaker Series',
					'desc'  => 'Convenings like the Human Flourishing Symposium that connect research, practice, and community voices on the questions that matter most.',
					'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>',
				],
				[
					'title' => 'Days of Reflection',
					'desc'  => 'Gatherings around moments like the National Day of Racial Healing, designed for honest reflection and renewal.',
					'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
				],
				[
					'title' => 'Workshops',
					'desc'  => 'Focused, practical sessions — such as a restorative approach to racial equity consciousness — that you can bring back to your own organization.',
					'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
				]
			];
			
			foreach ($event_types as $type) :
			?>
				<div class="p-6 bg-white rounded-lg border border-zinc-300 flex flex-col gap-3 shadow-sm hover:border-amber-400 transition-colors">
					<?php echo $type['icon']; ?>
					<h3 class="text-neutral-800 text-2xl font-bold font-heading"><?php echo esc_html($type['title']); ?></h3>
					<p class="text-neutral-600 text-base font-normal leading-relaxed"><?php echo esc_html($type['desc']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		
		<!-- View Upcoming Events CTA -->
		<div class="p-8 bg-neutral-800 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
			<div class="flex flex-col gap-2 max-w-2xl">
				<h3 class="text-white text-3xl font-bold font-heading"><?php echo esc_html('Upcoming Events'); ?></h3>
				<p class="text-gray-200 text-lg font-normal leading-7"><?php echo esc_html("Browse what's next and save your seat."); ?></p>
			</div>
			<a href="https://calendar.pitt.edu/department/center_on_race_and_social_problems" target="_blank" rel="noopener noreferrer" class="btn btn-primary bg-amber-400 text-zinc-800 hover:bg-amber-500 border-0 flex-shrink-0 px-6 py-3">
				<?php echo esc_html('View Upcoming Events'); ?>
			</a>
		</div>
	</section>

	<section class="reci-container  pt-5 pb-14 flex flex-col gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold">Filter by:</span>
					<?php if (! empty($location_terms)) : ?>
						<div class="archive-filter-select-wrap">
							<label for="event-location-filter" class="sr-only">Filter by location</label>
							<select id="event-location-filter" name="location" class="archive-filter-select" aria-label="Filter by location">
								<option value="">All Locations</option>
								<?php foreach ($location_terms as $term) : ?>
									<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_location, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="archive-filter-chevron">
								<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
								</svg>
							</span>
						</div>
					<?php endif; ?>
				</div>

				<div class="w-full sm:w-auto flex items-center gap-2.5">
					<div class="archive-filter-search-wrap" role="search">
						<svg class="w-4 h-4 flex-shrink-0 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="event-search" class="sr-only">Search events</label>
						<input id="event-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="Search Events" class="archive-filter-search-input" />
					</div>
					<?php if ($has_filters) : ?>
						<a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900">Reset</a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>

<?php get_footer(); ?>
