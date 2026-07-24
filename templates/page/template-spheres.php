<?php

/**
 * Template Name: Six Spheres Framework
 *
 * Overview page for the RECI Six Spheres framework.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
    exit;
}

$spheres = reci_get_all_spheres();

get_header();
?>

<main class="layout-page">
    <?php get_template_part('template-parts/common/page-title-card', null, [
        'title'    => __('The Six Spheres of RECI', 'reci-media-hub'),
        'subtitle' => __('The RECI Six Spheres framework provides a comprehensive lens for understanding and advancing racial equity. Each sphere represents a dimension of learning and growth — from recognizing oppression to championing justice.', 'reci-media-hub'),
    ]); ?>

    <section class="reci-container py-14 lg:py-20 flex flex-col gap-14 lg:gap-20">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php foreach ($spheres as $sphere):
                $color = $sphere['color'] ?? '#9B4D3A';
                $num   = $sphere['num'] ?? '';
                $name  = $sphere['awareness'] ?? '';
                $action = $sphere['action'] ?? '';
                $desc  = $sphere['desc'] ?? '';
                $slug  = $sphere['termSlug'] ?? '';
                $url   = ! empty($slug) ? home_url('/reci-sphere/' . $slug . '/') : '#';
            ?>
                <a href="<?php echo esc_url($url); ?>" class="group flex flex-col gap-5 p-6 lg:p-8 rounded-lg border border-zinc-200 hover:shadow-lg transition-all no-underline" style="border-left: 4px solid <?php echo esc_attr($color); ?>;">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl lg:text-4xl font-bold font-heading leading-none" style="color: <?php echo esc_attr($color); ?>;"><?php echo esc_html($num); ?></span>
                        <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?php echo esc_attr($color); ?>;"></span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h2 class="text-neutral-800 text-xl lg:text-2xl font-bold font-subhead leading-tight group-hover:text-amber-400 transition-colors">
                            <?php echo esc_html($name); ?>
                        </h2>
                        <span class="text-neutral-500 text-base font-normal"><?php echo esc_html($action); ?></span>
                    </div>
                    <p class="text-neutral-600 text-base font-normal leading-relaxed"><?php echo esc_html($desc); ?></p>
                    <span class="inline-flex items-center gap-1 text-sm font-medium" style="color: <?php echo esc_attr($color); ?>;">
                        <?php esc_html_e('Explore content', 'reci-media-hub'); ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="max-w-3xl border-t border-zinc-200 pt-10 flex flex-col gap-5">
            <h2 class="text-neutral-800 text-2xl lg:text-3xl font-bold font-heading leading-tight">
                <?php esc_html_e('How It Works', 'reci-media-hub'); ?>
            </h2>
            <p class="text-neutral-600 text-lg font-normal leading-relaxed">
                <?php esc_html_e('Every piece of content on the RECI Media Hub is aligned with one or more spheres. Each sphere pairs an awareness dimension (what to understand) with an action dimension (what to do). Together, they form a pathway from recognition to transformation.', 'reci-media-hub'); ?>
            </p>
            <p class="text-neutral-600 text-lg font-normal leading-relaxed">
                <?php esc_html_e('Browse any sphere to discover articles, videos, podcasts, and events that explore its themes. The more spheres you engage with, the deeper your racial equity journey becomes.', 'reci-media-hub'); ?>
            </p>
        </div>

        <!-- SDGs -->
        <div class="border-t border-zinc-200 pt-10 flex flex-col gap-8">
            <div class="flex flex-col gap-3">
                <h2 class="text-neutral-800 text-2xl lg:text-3xl font-bold font-heading leading-tight">
                    <?php esc_html_e('Sustainable Development Goals', 'reci-media-hub'); ?>
                </h2>
                <p class="text-neutral-600 text-lg font-normal leading-relaxed max-w-3xl">
                    <?php esc_html_e('RECI content is aligned with the United Nations Sustainable Development Goals — a shared blueprint for peace, prosperity, and people on planet Earth.', 'reci-media-hub'); ?>
                </p>
            </div>

            <?php $sdgs = reci_media_hub_default_sdgs(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($sdgs as $sdg): ?>
                    <a href="<?php echo esc_url(home_url('/sdg/' . $sdg['slug'] . '/')); ?>"
                       class="group flex flex-col gap-4 p-6 rounded-lg no-underline transition-all hover:shadow-lg"
                       style="border-left: 4px solid <?php echo esc_attr($sdg['color']); ?>; background-color: <?php echo esc_attr($sdg['color']); ?>0d;">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl lg:text-4xl font-bold font-heading leading-none" style="color: <?php echo esc_attr($sdg['color']); ?>;"><?php echo esc_html(str_replace('sdg-', '', $sdg['slug'])); ?></span>
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?php echo esc_attr($sdg['color']); ?>;"></span>
                        </div>
                        <h3 class="text-neutral-800 text-lg font-bold font-subhead leading-tight group-hover:text-amber-400 transition-colors">
                            <?php echo esc_html($sdg['name']); ?>
                        </h3>
                        <?php if (! empty($sdg['desc'])): ?>
                            <p class="text-neutral-600 text-sm font-normal leading-relaxed"><?php echo esc_html($sdg['desc']); ?></p>
                        <?php endif; ?>
                        <span class="inline-flex items-center gap-1 text-sm font-medium" style="color: <?php echo esc_attr($sdg['color']); ?>;">
                            <?php esc_html_e('Explore content', 'reci-media-hub'); ?>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </section>
</main>

<?php get_footer(); ?>
