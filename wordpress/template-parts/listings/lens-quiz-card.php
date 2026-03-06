<?php
/**
 * Lens quiz card (from herov3.php lines 907-976 card pattern).
 *
 * @var array $args
 */

$title        = $args['title'] ?? '';
$description  = $args['description'] ?? '';
$button_label = $args['button_label'] ?? 'Start quiz';
?>

<div data-layer="Wrapper" class="Wrapper w-full lg:w-80 self-stretch py-5 inline-flex flex-col justify-start items-start gap-2.5">
    <div data-layer="Quiz card" class="QuizCard self-stretch px-6 lg:px-10 pt-16 lg:pt-20 pb-8 lg:pb-10 bg-neutral-800 rounded-lg flex flex-col justify-end items-start gap-8 lg:gap-10 overflow-hidden">
        <div data-layer="content" class="Content self-stretch flex flex-col justify-start items-start gap-5">
            <div data-layer="Content" class="Content p-2.5 bg-neutral-600 rounded-lg flex flex-col justify-start items-start gap-2.5 overflow-hidden">
                <div data-layer="head-question" class="HeadQuestion w-6 h-6 relative overflow-hidden">
                    <div data-layer="Vector" class="Vector w-4 h-4 left-[3.98px] top-[3px] absolute bg-amber-400"></div>
                </div>
            </div>
            <div data-layer="Content" class="Content self-stretch flex flex-col justify-start items-start gap-2.5">
                <div data-layer="QuizTitle" class="self-stretch justify-start text-white text-2xl font-semibold font-['EB_Garamond'] leading-7"><?php echo esc_html($title); ?></div>
                <div data-layer="QuizDescription" class="self-stretch justify-start text-gray-200 text-sm font-normal font-['SF_Pro_Display'] leading-6"><?php echo esc_html($description); ?></div>
            </div>
        </div>
        <div data-layer="Button" data-property-1="Default" class="btn btn-primary btn-md">
            <div data-layer="Text Top" class="TextTop flex justify-start items-center gap-2">
                <div data-layer="Button Text Here" class="ButtonTextHere text-center justify-start"><?php echo esc_html($button_label); ?></div>
            </div>
        </div>
    </div>
</div>
