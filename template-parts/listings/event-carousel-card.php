<?php

/**
 * Event/Webinar carousel card item (from herov3.php lines 242-266).
 *
 * @var array $args
 */

$status        = $args['status'] ?? 'Upcoming';
$date          = $args['date'] ?? '';
$time          = $args['time'] ?? '';
$title         = $args['title'] ?? '';
$excerpt       = $args['excerpt'] ?? '';
$button_label  = $args['button_label'] ?? 'Register';
$link_url      = $args['link_url'] ?? '#';
$image_url     = $args['image_url'] ?? '';
$image_alt     = $args['image_alt'] ?? '';
?>

<div class="Container w-full max-w-[800px] bg-white rounded-lg flex flex-col md:flex-row justify-start items-start overflow-hidden">
    <div class="Container flex-1 p-6 lg:p-10 bg-slate-100 inline-flex flex-col justify-start items-start gap-8 lg:gap-10">
        <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-2.5">
            <div data-layer="Content" class="Content inline-flex justify-start items-center gap-2.5">
                <div data-layer="Tag" class="Tag px-2 py-1 bg-amber-400 rounded flex justify-center items-center gap-2.5">
                    <div data-layer="Upcoming" class="Upcoming justify-start text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($status); ?></div>
                </div>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Date" class="July2026 justify-start text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($date); ?></div>
                <div data-layer="Tag" class="Tag tag-dot"></div>
                <div data-layer="Time" class="PmEst justify-start text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-4"><?php echo esc_html($time); ?></div>
            </div>
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
                <div data-layer="Title" class="OnlineSeminarOnDecolonizationEducation self-stretch justify-start text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-10"><?php echo esc_html($title); ?></div>
                <div data-layer="Excerpt" class="NewPodcastSeriesIntersectionalFuturesEpisode1ReleasedAvailableNow self-stretch justify-start text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7"><?php echo esc_html($excerpt); ?></div>
            </div>
        </div>
        <a href="<?php echo esc_url($link_url); ?>" class="btn btn-primary btn-md"><?php echo esc_html($button_label); ?></a>
    </div>
    <?php if ($image_url) : ?>
        <img data-layer="Image" class="Image w-full md:w-96 object-cover self-stretch p-2.5" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" />
    <?php endif; ?>
</div>