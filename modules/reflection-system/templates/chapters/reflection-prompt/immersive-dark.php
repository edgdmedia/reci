<?php
/**
 * Voices of Resistance reflection stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-reflect',
	'prompt' => '',
	'button_label' => 'Submit Reflection',
	'button_href' => '#',
]);
?>
<section class="reci-stage chapter-reflection" id="<?php echo esc_attr($args['id']); ?>" data-reflection-id="<?php echo esc_attr(get_the_ID()); ?>" data-prompt="<?php echo esc_attr($args['prompt']); ?>">
	<div class="reci-reflection-form flex flex-col items-center justify-center w-full max-w-[800px] mx-auto text-center">
		<h2 class="reflect-prompt"><?php echo esc_html($args['prompt']); ?></h2>
		<textarea class="reflect-input reci-reflection-prompt__input" placeholder="Share your thoughts..."></textarea>
		<?php if ( ! is_user_logged_in() ) : ?>
			<p class="mt-4 text-sm text-white/60">Log in or create a free account to record your reflections in your private journal.</p>
		<?php endif; ?>
		<button class="enter-btn reci-complete-btn" style="margin-top:30px;" type="button" data-complete-href="<?php echo esc_url($args['button_href']); ?>"><?php echo esc_html($args['button_label']); ?></button>
	</div>
	
	<div class="reci-reflection-success hidden flex-col items-center justify-center w-full max-w-[800px] mx-auto text-center opacity-0 transition-opacity duration-700">
		<svg class="w-16 h-16 text-amber-500 mb-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
		<h3 class="text-3xl font-['Playfair_Display'] text-white mb-4">Reflection Saved</h3>
		<p class="text-white/80 text-lg mb-10 max-w-lg mx-auto">Your thoughts have been securely recorded in your private journal.</p>
		<div class="flex gap-6 justify-center">
			<a href="/reflections" class="inline-flex items-center justify-center border border-white/60 px-8 py-3 font-['Oswald'] text-xs uppercase tracking-[0.14em] text-white no-underline hover:bg-white hover:text-black transition-colors">Return to Gallery</a>
			<button class="reci-restart-btn inline-flex items-center justify-center bg-white/10 px-8 py-3 font-['Oswald'] text-xs uppercase tracking-[0.14em] text-white no-underline hover:bg-white/20 transition-colors" type="button">Start Over</button>
		</div>
	</div>
</section>
