<?php
/**
 * Community Pulse slide body.
 *
 * @var array $args
 */

$quote          = $args['quote'] ?? '';
$author_name    = $args['author_name'] ?? '';
$author_role    = $args['author_role'] ?? '';
$author_org     = $args['author_org'] ?? '';
$author_image   = $args['author_image'] ?? '';
$author_alt     = $args['author_alt'] ?? '';

$role_display = $author_role;
if ($author_role && $author_org) {
	$role_display = $author_role . ', ' . $author_org;
} elseif ($author_org) {
	$role_display = $author_org;
}
?>

<div data-layer="Content" class="Content flex-1 py-10 inline-flex flex-col justify-start items-start gap-10">
    <div data-layer="Quote" class="ReciHasTrulyTransformedHowWeApproachMarketAnalysisTheInsightsAreUnparalleled self-stretch justify-start text-white text-2xl font-normal font-accent leading-10 "><?php echo esc_html($quote); ?></div>
    <div data-layer="Vector 4" class="Vector4 self-stretch h-0 outline outline-[0.50px] outline-offset-[-0.25px] outline-zinc-400"></div>
    <div data-layer="Wrapper" class="Wrapper self-stretch inline-flex justify-between items-center">
        <div data-layer="Author" class="Author flex justify-start items-center gap-2.5">
            <?php if ($author_image) : ?>
                <img data-layer="Image" class="Image w-14 h-14 p-2.5 rounded-full outline outline-4 outline-white" src="<?php echo esc_url($author_image); ?>" alt="<?php echo esc_attr($author_alt); ?>" />
            <?php endif; ?>
            <div data-layer="Author" class="Author inline-flex flex-col justify-start items-start gap-1">
                <div data-layer="Jane Doe" class="JaneDoe w-48 justify-start text-white text-xl font-bold leading-8 "><?php echo esc_html($author_name); ?></div>
                <div class="FinancialAnalyst w-48 justify-start text-zinc-300 text-base font-medium leading-6 "><?php echo esc_html($role_display); ?></div>
            </div>
        </div>
        <div data-layer="CTA" class="Cta flex justify-start items-center gap-3">
            <button type="button" data-layer="Button" data-carousel-prev aria-label="<?php esc_attr_e('Previous', 'reci-media-hub'); ?>" class="Button p-4 rounded-lg outline outline-[0.50px] outline-offset-[-0.50px] outline-zinc-400 flex justify-center items-center gap-2 overflow-hidden cursor-pointer hover:outline-white transition-colors">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button type="button" data-layer="Button" data-carousel-next aria-label="<?php esc_attr_e('Next', 'reci-media-hub'); ?>" class="Button p-4 rounded-lg outline outline-[0.50px] outline-offset-[-0.50px] outline-zinc-400 flex justify-center items-center gap-2 overflow-hidden cursor-pointer hover:outline-white transition-colors">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</div>
