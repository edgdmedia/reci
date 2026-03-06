<?php
/**
 * Community Pulse slide body (from herov3.php lines 1005-1031).
 *
 * @var array $args
 */

$quote          = $args['quote'] ?? '';
$author_name    = $args['author_name'] ?? '';
$author_role    = $args['author_role'] ?? '';
$author_image   = $args['author_image'] ?? '';
$author_alt     = $args['author_alt'] ?? '';
?>

<div data-layer="Content" class="Content flex-1 py-10 inline-flex flex-col justify-start items-start gap-10">
    <div data-layer="Quote" class="ReciHasTrulyTransformedHowWeApproachMarketAnalysisTheInsightsAreUnparalleled self-stretch justify-start text-white text-2xl font-normal font-['EB_Garamond'] leading-10 tracking-tight"><?php echo esc_html($quote); ?></div>
    <div data-layer="Vector 4" class="Vector4 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
    <div data-layer="Wrapper" class="Wrapper self-stretch inline-flex justify-between items-center">
        <div data-layer="Author" class="Author flex justify-start items-center gap-2.5">
            <?php if ($author_image) : ?>
                <img data-layer="Image" class="Image w-14 h-14 p-2.5 rounded-full outline outline-4 outline-white" src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($author_alt); ?>" />
            <?php endif; ?>
            <div data-layer="Author" class="Author inline-flex flex-col justify-start items-start gap-1">
                <div data-layer="Jane Doe" class="JaneDoe w-48 justify-start text-white text-xl font-bold font-['SF_Pro_Display'] leading-8 tracking-tight"><?php echo esc_html($author_name); ?></div>
                <div data-layer="Financial Analyst" class="FinancialAnalyst w-48 justify-start text-zinc-400 text-base font-medium font-['SF_Pro_Display'] leading-6 tracking-tight"><?php echo esc_html($author_role); ?></div>
            </div>
        </div>
        <div data-layer="CTA" class="Cta flex justify-start items-center gap-3">
            <div data-layer="Button" data-carousel-prev role="button" aria-label="<?php esc_attr_e('Previous', 'reci-media-hub'); ?>" class="Button p-4 rounded-lg outline outline-[0.50px] outline-offset-[-0.50px] outline-zinc-400 flex justify-center items-center gap-2 overflow-hidden cursor-pointer hover:outline-white transition-colors">
                <div data-layer="arrow-left" class="ArrowLeft w-5 h-5 relative overflow-hidden">
                    <div data-layer="Vector" class="Vector w-3.5 h-3.5 left-[3.47px] top-[3.40px] absolute bg-white"></div>
                </div>
            </div>
            <div data-layer="Button" data-carousel-next role="button" aria-label="<?php esc_attr_e('Next', 'reci-media-hub'); ?>" class="Button p-4 rounded-lg outline outline-[0.50px] outline-offset-[-0.50px] outline-zinc-400 flex justify-center items-center gap-2 overflow-hidden cursor-pointer hover:outline-white transition-colors">
                <div data-layer="arrow-right" class="ArrowRight w-5 h-5 relative overflow-hidden">
                    <div data-layer="Vector" class="Vector w-3.5 h-3.5 left-[3.33px] top-[3.40px] absolute bg-white"></div>
                </div>
            </div>
        </div>
    </div>
</div>
