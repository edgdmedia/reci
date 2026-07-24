<?php
/**
 * Lens quiz card.
 *
 * @var array $args
 */

$title        = $args['title'] ?? '';
$description  = $args['description'] ?? '';
$button_label = $args['button_label'] ?? 'Start quiz';
$link_url     = $args['link_url'] ?? '#';
?>

<div data-layer="Wrapper" class="Wrapper w-full lg:w-96 self-stretch py-5 inline-flex flex-col justify-start items-start gap-2.5">
    <div data-layer="Quiz card" class="QuizCard self-stretch px-8 lg:px-12 pt-20 lg:pt-24 pb-10 lg:pb-12 bg-neutral-800 rounded-lg flex flex-col justify-end items-start gap-8 lg:gap-10 overflow-hidden">
        <div data-layer="content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
            <div data-layer="Content" class="Content p-3 bg-neutral-600 rounded-lg flex flex-col justify-start items-start gap-2.5 overflow-hidden">
                <div data-layer="head-question" class="HeadQuestion w-8 h-8 relative overflow-hidden">
                    <div data-layer="Vector" class="Vector w-5 h-5 left-[5px] top-[4px] absolute bg-amber-400"></div>
                </div>
            </div>
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-3">
                <div data-layer="QuizTitle" class="self-stretch justify-start text-white text-3xl font-bold font-heading leading-9"><?php echo esc_html($title); ?></div>
                <div data-layer="QuizDescription" class="self-stretch justify-start text-gray-200 text-base font-normal leading-7"><?php echo esc_html($description); ?></div>
            </div>
        </div>
        <a href="<?php echo esc_url($link_url); ?>" data-layer="Button" data-property-1="Default" class="btn btn-primary btn-md no-underline">
            <div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
                <div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start"><?php echo esc_html($button_label); ?></div>
            </div>
        </a>
    </div>
</div>
