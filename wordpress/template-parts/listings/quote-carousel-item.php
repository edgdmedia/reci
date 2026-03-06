<?php

/**
 * Quotes carousel item content (from herov3.php lines 634-637).
 *
 * @var array $args
 */

$quote  = $args['quote'] ?? '';
$author = $args['author'] ?? '';
?>

<div class="reci-container max-w-[1060px]  px-4 sm:px-10 lg:px-20 py-8 lg:py-10 rounded-lg flex flex-col justify-center items-center gap-8 lg:gap-10">
    <div data-layer="Quote" class="LoremIpsumDolorSitAmetConsecteturAdipiscingElitSedDoEiusmodTemporIncididuntUtLaboreEtDoloreMagnaAliquaUtEnimAdMinimVeniamQuisNostrudExercitationUllamcoLaborisNisiUtAliquipExEaCommodoConsequatDuisAuteIrureDolorIn self-stretch text-center justify-start text-white text-2xl font-normal font-['EB_Garamond'] leading-10 tracking-tight"><?php echo esc_html($quote); ?></div>
    <div data-layer="Author" class="ByJohnDoe w-48 text-center justify-start text-amber-400 text-base font-semibold font-['SF_Pro_Display'] leading-6 tracking-tight"><?php echo esc_html($author); ?></div>
</div>