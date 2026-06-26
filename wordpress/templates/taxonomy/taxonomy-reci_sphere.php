<?php

/**
 * Taxonomy archive for reci_sphere.
 *
 * Displays content tagged with the current sphere.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

$queried = get_queried_object();
if (! $queried instanceof WP_Term) {
    wp_safe_redirect(home_url('/'));
    exit;
}

$term_id    = (int) $queried->term_id;
$slug       = $queried->slug;
$default    = reci_media_hub_get_sphere_default_by_slug($slug) ?? [];

$sphere_name    = (string) get_term_meta($term_id, 'reci_sphere_awareness', true);
$sphere_action  = (string) get_term_meta($term_id, 'reci_sphere_action', true);
$sphere_color   = (string) get_term_meta($term_id, 'reci_sphere_color', true);
$sphere_num     = (string) get_term_meta($term_id, 'reci_sphere_num', true);
$sphere_desc    = (string) get_term_meta($term_id, 'reci_sphere_desc', true);
$sphere_gradient = (string) get_term_meta($term_id, 'reci_sphere_gradient', true);

if ($sphere_name === '')   { $sphere_name   = (string) ($default['awareness'] ?? $queried->name); }
if ($sphere_action === '') { $sphere_action  = (string) ($default['action'] ?? ''); }
if ($sphere_color === '')  { $sphere_color   = (string) ($default['color'] ?? '#9B4D3A'); }
if ($sphere_num === '')    { $sphere_num     = (string) ($default['num'] ?? ''); }
if ($sphere_desc === '')   { $sphere_desc    = (string) ($default['desc'] ?? $queried->description); }
if ($sphere_gradient === '') {
    $sphere_gradient = (string) ($default['gradient'] ?? 'linear-gradient(135deg, ' . $sphere_color . ', ' . $sphere_color . ')');
}

$image_id = (int) get_term_meta($term_id, 'reci_sphere_content_image_id', true);
$sphere_image_url = '';
if ($image_id > 0) {
    $sphere_image_url = wp_get_attachment_url($image_id);
} else {
    $filename = (string) ($default['image_file'] ?? '');
    if ($filename !== '') {
        $sphere_image_url = get_template_directory_uri() . '/assets/images/site/reci-spheres/' . $filename;
    }
}

$page_title   = $sphere_name;
$page_subtitle = $sphere_desc;

$current_topic  = isset($_GET['topic']) ? sanitize_title((string) wp_unslash($_GET['topic'])) : '';
$current_search = isset($_GET['search']) ? sanitize_text_field((string) wp_unslash($_GET['search'])) : '';

$base_url        = get_term_link($queried);
$base_url        = $base_url && is_string($base_url) ? $base_url : home_url('/reci-sphere/' . $slug . '/');
$all_filters_url = remove_query_arg(['topic', 'search', 'paged'], $base_url);
$has_filters     = ($current_topic !== '') || ($current_search !== '');

$query_args = [
    'post_type'      => ['post', 'reci_video', 'reci_podcast', 'reci_event', 'reci_reflection'],
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => max(1, (int) get_query_var('paged'), (int) get_query_var('page')),
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => [[
        'taxonomy' => 'reci_sphere',
        'field'    => 'term_id',
        'terms'    => [$term_id],
    ]],
];

if ($current_search !== '') {
    $query_args['s'] = $current_search;
}
if ($current_topic !== '') {
    $query_args['tax_query'][] = [
        'taxonomy' => 'category',
        'field'    => 'slug',
        'terms'    => [$current_topic],
    ];
}

$listing = RECI_Post_Query_Service::get_formatted_items($query_args, [
    'image_size'    => 'large',
    'tag_limit'     => 3,
    'excerpt_words' => 16,
]);
$items  = $listing['items'];
$query  = $listing['query'];

$topic_terms = get_terms([
    'taxonomy'   => 'category',
    'hide_empty' => true,
]);
if (is_wp_error($topic_terms)) {
    $topic_terms = [];
}

$all_spheres = reci_get_all_spheres();

get_header();
?>

<main class="layout-page">

    <section class="relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background: <?php echo esc_attr($sphere_gradient); ?>;"></div>
        <div class="reci-container relative py-14 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-10 items-center">
                <!-- Left: Content (60%) -->
                <div class="lg:col-span-6 flex flex-col gap-5">
                    <div class="flex items-center gap-3">
                        <?php if ($sphere_num): ?>
                            <span class="text-4xl lg:text-5xl font-bold font-heading leading-none" style="color: <?php echo esc_attr($sphere_color); ?>;">
                                <?php echo esc_html($sphere_num); ?>
                            </span>
                        <?php endif; ?>
                        <span class="w-4 h-4 rounded-full" style="background-color: <?php echo esc_attr($sphere_color); ?>;"></span>
                    </div>
                    <h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-heading leading-tight">
                        <?php echo esc_html($page_title); ?>
                    </h1>
                    <?php if ($sphere_action): ?>
                        <p class="text-neutral-500 text-xl lg:text-2xl font-normal"><?php echo esc_html($sphere_action); ?></p>
                    <?php endif; ?>
                    <?php if ($page_subtitle): ?>
                        <p class="max-w-3xl text-neutral-700 text-lg font-normal leading-relaxed"><?php echo esc_html($page_subtitle); ?></p>
                    <?php endif; ?>
                </div>
                <!-- Right: Image (40%) -->
                <div class="lg:col-span-4 flex justify-center">
                    <?php if ($sphere_image_url): ?>
                        <img src="<?php echo esc_url($sphere_image_url); ?>" alt="<?php echo esc_attr($page_title); ?>" class="w-full h-auto rounded-lg shadow-sm" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="reci-container pt-5 pb-14 flex flex-col justify-start items-start gap-10">

        <?php if (! empty($all_spheres)): ?>
            <div class="self-stretch flex flex-wrap items-center gap-2 pb-5 border-b border-zinc-200">
                <span class="text-neutral-800 text-sm font-bold mr-1"><?php esc_html_e('Spheres:', 'reci-media-hub'); ?></span>
                <?php foreach ($all_spheres as $s):
                    $s_color = $s['color'] ?? '#9B4D3A';
                    $s_url   = ! empty($s['termSlug']) ? home_url('/reci-sphere/' . $s['termSlug'] . '/') : '#';
                    $is_current = ($s['termSlug'] ?? '') === $slug;
                ?>
                    <a href="<?php echo esc_url($s_url); ?>" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium no-underline <?php echo $is_current ? 'text-white' : 'hover:opacity-80'; ?>" style="background-color: <?php echo $is_current ? esc_attr($s_color) : esc_attr($s_color) . '1a'; ?>; color: <?php echo $is_current ? '#fff' : esc_attr($s_color); ?>;">
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: <?php echo $is_current ? '#fff' : esc_attr($s_color); ?>;"></span>
                        <?php echo esc_html($s['name'] ?? ''); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="self-stretch pb-5 border-b border-zinc-400">
            <form method="get" action="<?php echo esc_url($base_url); ?>" class="self-stretch flex flex-col sm:flex-row justify-between items-center gap-5" data-archive-filter-form data-search-min="3" data-search-debounce="350">
                <div class="flex justify-start items-center gap-5 flex-wrap">
                    <span class="text-neutral-800 text-base font-bold"><?php esc_html_e('Filter by:', 'reci-media-hub'); ?></span>
                    <div class="relative">
                        <label for="sphere-topic-filter" class="sr-only"><?php esc_html_e('Filter by topic', 'reci-media-hub'); ?></label>
                        <select id="sphere-topic-filter" name="topic" class="appearance-none px-4 py-2 pr-8 text-neutral-800 text-base font-normal bg-transparent border-none cursor-pointer focus:outline-none" aria-label="<?php esc_attr_e('Filter by topic', 'reci-media-hub'); ?>">
                            <option value=""><?php esc_html_e('All Topics', 'reci-media-hub'); ?></option>
                            <?php foreach ($topic_terms as $term): ?>
                                <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($current_topic, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1">
                            <svg class="w-4 h-4 text-neutral-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="w-full sm:w-auto flex items-center gap-2.5">
                    <div class="archive-filter-search-wrap border-zinc-200" role="search">
                        <svg class="archive-filter-search-icon text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <label for="sphere-search" class="sr-only"><?php esc_html_e('Search', 'reci-media-hub'); ?></label>
                        <input id="sphere-search" type="search" name="search" value="<?php echo esc_attr($current_search); ?>" placeholder="<?php esc_attr_e('Search', 'reci-media-hub'); ?>" class="archive-filter-search-input" />
                    </div>
                    <?php if ($has_filters): ?>
                        <a href="<?php echo esc_url($all_filters_url); ?>" class="px-4 py-3 text-sm font-medium text-neutral-700 hover:text-neutral-900"><?php esc_html_e('Reset', 'reci-media-hub'); ?></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if (! empty($items)): ?>
            <div class="self-stretch grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php foreach ($items as $item):
                    $template = 'template-parts/listings/articles-side-card';
                    $pt = $item['post_type'] ?? '';
                    if ($pt === 'reci_podcast') {
                        $template = 'template-parts/listings/podcast-archive-card';
                    }
                    get_template_part($template, null, $item);
                endforeach; ?>
            </div>
            <?php echo RECI_Post_Query_Service::render_pagination($query, [
                'base_url'      => $base_url,
                'param_name'    => 'paged',
                'wrapper_class' => 'mt-8 flex items-center justify-center gap-2',
                'item_class'    => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg border border-zinc-300 text-sm font-medium text-neutral-800 hover:bg-zinc-100",
                'current_class' => "inline-flex items-center justify-center min-w-11 h-11 px-3 rounded-lg bg-[#003594] text-sm font-medium text-white",
            ]); ?>
        <?php else: ?>
            <div class="self-stretch py-20 flex flex-col items-center gap-5">
                <p class="text-neutral-500 text-lg"><?php esc_html_e('No content found for this sphere.', 'reci-media-hub'); ?></p>
                <a href="<?php echo esc_url(home_url('/framework/')); ?>" class="text-sm font-medium text-[#3366FF] hover:underline"><?php esc_html_e('Browse all spheres', 'reci-media-hub'); ?></a>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>
