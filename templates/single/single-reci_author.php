<?php
/**
 * Single Collaborator template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! have_posts()) {
	wp_safe_redirect(home_url('/'));
	exit;
}

the_post();

$profile_id      = get_the_ID();
$profile         = reci_media_hub_get_author_profile_data($profile_id);
$authored_post_ids = reci_media_hub_get_authored_content_ids($profile_id, ['post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection', 'reci_course', 'reci_document']);

$topic_terms = get_terms([
	'taxonomy'   => 'category',
	'hide_empty' => true,
]);
if (is_wp_error($topic_terms)) {
	$topic_terms = [];
}

$sphere_terms_filter = get_terms([
	'taxonomy'   => 'reci_sphere',
	'hide_empty' => true,
]);
if (is_wp_error($sphere_terms_filter)) {
	$sphere_terms_filter = [];
}

$current_topic = isset($_GET['topic']) ? sanitize_title((string) wp_unslash($_GET['topic'])) : '';
$current_sphere = isset($_GET['sphere']) ? sanitize_title((string) wp_unslash($_GET['sphere'])) : '';

$listing_config = [
	'post_type'                => ['post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection', 'reci_course'],
	'post__in'                 => ! empty($authored_post_ids) ? $authored_post_ids : [0],
	'posts_per_page'           => 9,
	'orderby'                  => 'date',
	'order'                    => 'DESC',
	'listing_style'            => 'archive_grid_card',
	'wrapper_class'            => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8',
	'item_overrides'           => [
		'title_classes'   => "self-stretch justify-start text-neutral-800 text-2xl font-bold font-serif leading-7 line-clamp-3",
		'excerpt_classes' => "self-stretch justify-start text-neutral-500 text-sm font-normal leading-5 ",
	],
	'enable_pagination'        => true,
	'pagination_param'         => 'paged',
	'filter_search_param'      => 'search',
	'filter_taxonomies'        => [
		'category' => [
			'param' => 'topic',
			'field' => 'slug',
		],
		'reci_sphere' => [
			'param' => 'sphere',
			'field' => 'slug',
		],
	],
	'empty_message'            => 'No content found for this collaborator yet.',
];

$is_logged_in = is_user_logged_in();
$followed_collaborators = $is_logged_in && function_exists( 'reci_get_user_followed_collaborator_ids' ) ? reci_get_user_followed_collaborator_ids( get_current_user_id() ) : [];
$is_following = in_array( $profile_id, $followed_collaborators, true );

get_header();
?>
<main class="layout-page">
	<div class="reci-container-full border-b border-zinc-400">
		<div class="reci-container py-14">
			<div class="flex flex-col md:flex-row justify-between items-start gap-6">
				<div class="flex items-center w-full md:flex-1 md:min-w-0">
					<div class="flex flex-col gap-3">
						<div class="flex items-center gap-3">
							<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
							<h1 class="text-neutral-800 text-5xl font-bold font-heading"><?php echo esc_html((string) ($profile['name'] ?? get_the_title())); ?></h1>
						</div>
						<?php if (! empty($profile['title'])) : ?>
							<p class="text-neutral-500 text-lg font-medium"><?php echo esc_html((string) $profile['title']); ?></p>
						<?php endif; ?>
						<?php
						$affiliation_chips = get_the_terms($profile_id, 'reci_affiliation');
						if (! is_wp_error($affiliation_chips) && ! empty($affiliation_chips)) :
						?>
							<div class="flex flex-wrap gap-2 pt-1">
								<?php foreach ($affiliation_chips as $chip) : ?>
									<a href="<?php echo esc_url((string) get_term_link($chip)); ?>" class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-800 transition-colors hover:bg-amber-200"><?php echo esc_html($chip->name); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $is_logged_in ) : ?>
					<div class="w-full md:w-72 md:flex-shrink-0 md:text-right">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pt-2 md:pt-0">
							<input type="hidden" name="action" value="reci_toggle_follow_collaborator" />
							<input type="hidden" name="collaborator_id" value="<?php echo esc_attr( (string) $profile_id ); ?>" />
							<input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink( $profile_id ) ); ?>" />
							<?php wp_nonce_field( 'reci_toggle_follow_collaborator_' . $profile_id, 'reci_follow_collaborator_nonce' ); ?>
							<button type="submit" class="btn btn-outline-primary btn-md"><?php echo esc_html( $is_following ? __( 'Following', 'reci-media-hub' ) : __( 'Follow Collaborator', 'reci-media-hub' ) ); ?></button>
						</form>
						<p class="pt-2 text-sm leading-6 text-zinc-600 md:ml-auto md:max-w-xs"><?php esc_html_e( 'Follow this collaborator to keep up with their published work in your dashboard feed.', 'reci-media-hub' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="reci-container-full border-b border-zinc-400">
		<div class="reci-container py-14">
			<div class="flex flex-col gap-10">
				<div class="inline-flex items-center gap-2">
					<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
					<h2 class="text-neutral-700 text-2xl font-bold font-subhead"><?php esc_html_e('About the Collaborator', 'reci-media-hub'); ?></h2>
				</div>
				<div class="w-full h-px bg-zinc-300"></div>
				<div class="flex flex-col md:flex-row items-start gap-6">
					<?php if (! empty($profile['image_url'])) : ?>
						<img src="<?php echo esc_url((string) $profile['image_url']); ?>" alt="<?php echo esc_attr((string) $profile['image_alt']); ?>" class="w-full md:w-72 md:h-72 object-cover rounded-xl flex-shrink-0" />
					<?php else : ?>
						<div class="w-full md:w-72 md:h-72 rounded-xl bg-zinc-200 flex items-center justify-center flex-shrink-0">
							<span class="text-zinc-400 text-5xl font-bold font-heading"><?php echo esc_html(substr($profile['name'] ?? get_the_title(), 0, 2)); ?></span>
						</div>
					<?php endif; ?>
					<div class="flex min-w-0 flex-1 flex-col gap-6">
						<div class="text-neutral-700 text-xl font-normal leading-7">
							<?php the_content(); ?>
						</div>

						<?php
						$detail_rows = array_filter([
							__('Organization', 'reci-media-hub')     => (string) ($profile['organization'] ?? ''),
							__('Department', 'reci-media-hub')       => (string) ($profile['department'] ?? ''),
							__('Pitt Affiliation', 'reci-media-hub') => (string) ($profile['pitt'] ?? ''),
						], static fn($value) => '' !== $value);

						if (! empty($detail_rows)) :
						?>
							<dl class="grid gap-x-8 gap-y-3 sm:grid-cols-2">
								<?php foreach ($detail_rows as $detail_label => $detail_value) : ?>
									<div>
										<dt class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php echo esc_html($detail_label); ?></dt>
										<dd class="mt-1 text-base text-neutral-700"><?php echo esc_html($detail_value); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>

						<?php
						$expertise_terms = get_the_terms($profile_id, 'reci_expertise');
						if (! is_wp_error($expertise_terms) && ! empty($expertise_terms)) :
						?>
							<div>
								<p class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e('Subject Areas', 'reci-media-hub'); ?></p>
								<div class="mt-2 flex flex-wrap gap-2">
									<?php foreach ($expertise_terms as $expertise_term) : ?>
										<a href="<?php echo esc_url((string) get_term_link($expertise_term)); ?>" class="inline-flex rounded-lg border border-zinc-300 px-3 py-1 text-sm text-neutral-700 transition-colors hover:border-amber-400 hover:text-amber-800"><?php echo esc_html($expertise_term->name); ?></a>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php
						$profile_links = [];
						if (! empty($profile['website'])) {
							$profile_links[] = ['url' => (string) $profile['website'], 'label' => __('Website', 'reci-media-hub')];
						}
						foreach ((array) ($profile['social_links'] ?? []) as $social_link) {
							$social_host = (string) wp_parse_url((string) $social_link, PHP_URL_HOST);
							$profile_links[] = [
								'url'   => (string) $social_link,
								'label' => '' !== $social_host ? (string) preg_replace('/^www\./', '', $social_host) : __('Profile', 'reci-media-hub'),
							];
						}
						if (! empty($profile['cv_url'])) {
							$profile_links[] = ['url' => (string) $profile['cv_url'], 'label' => __('Download CV', 'reci-media-hub')];
						}

						if (! empty($profile_links)) :
						?>
							<div class="flex flex-wrap gap-3">
								<?php foreach ($profile_links as $profile_link) : ?>
									<a href="<?php echo esc_url($profile_link['url']); ?>" class="btn btn-outline-primary btn-md" rel="noopener noreferrer" target="_blank"><?php echo esc_html($profile_link['label']); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if (! empty($profile['highlighted'])) : ?>
							<div>
								<p class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500"><?php esc_html_e('Highlighted Work', 'reci-media-hub'); ?></p>
								<ul class="mt-2 flex flex-col gap-1">
									<?php foreach ((array) $profile['highlighted'] as $highlighted_link) : ?>
										<li><a href="<?php echo esc_url((string) $highlighted_link); ?>" class="break-words text-amber-700 underline underline-offset-2 hover:text-amber-800" rel="noopener noreferrer" target="_blank"><?php echo esc_html((string) $highlighted_link); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">
		<div class="self-stretch pb-5 border-b border-zinc-400">
			<form method="get" action="<?php echo esc_url(get_permalink($profile_id)); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
				<div class="flex justify-start items-center gap-5 flex-wrap">
					<span class="text-neutral-800 text-base font-bold"><?php esc_html_e('Filter by:', 'reci-media-hub'); ?></span>
					<div class="archive-filter-select-wrap">
						<label for="author-topic-filter" class="sr-only"><?php esc_html_e('Filter by topic', 'reci-media-hub'); ?></label>
						<select id="author-topic-filter" name="topic" class="archive-filter-select" aria-label="<?php esc_attr_e('Filter by topic', 'reci-media-hub'); ?>">
							<option value=""><?php esc_html_e('All Topics', 'reci-media-hub'); ?></option>
							<?php foreach ($topic_terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_topic, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="archive-filter-chevron">
							<svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
							</svg>
						</span>
					</div>
					<?php if (! empty($sphere_terms_filter)) : ?>
						<div class="archive-filter-select-wrap">
							<label for="author-sphere-filter" class="sr-only"><?php esc_html_e('Filter by sphere', 'reci-media-hub'); ?></label>
							<select id="author-sphere-filter" name="sphere" class="archive-filter-select" aria-label="<?php esc_attr_e('Filter by sphere', 'reci-media-hub'); ?>">
								<option value=""><?php esc_html_e('All Spheres', 'reci-media-hub'); ?></option>
								<?php foreach ($sphere_terms_filter as $st) : ?>
									<option value="<?php echo esc_attr($st->slug); ?>" <?php selected($current_sphere, $st->slug); ?>><?php echo esc_html($st->name); ?></option>
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
						<svg class="archive-filter-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<label for="author-profile-search" class="sr-only"><?php esc_html_e('Search', 'reci-media-hub'); ?></label>
						<input id="author-profile-search" type="search" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : ''); ?>" placeholder="<?php esc_attr_e('Search', 'reci-media-hub'); ?>" class="archive-filter-search-input" />
					</div>
				</div>
			</form>
		</div>

		<?php echo reci_media_hub_render_listing($listing_config); ?>
	</section>
</main>
<?php get_footer(); ?>
