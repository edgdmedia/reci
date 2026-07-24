<?php
/**
 * Reflection chapter prompt variant: minimal.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'reflection',
	'title' => '',
	'prompt' => '',
	'button_label' => 'Submit Reflection',
	'button_href' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>" data-reflection-id="<?php echo esc_attr(get_the_ID()); ?>" data-prompt="<?php echo esc_attr($args['prompt'] ?: $args['title']); ?>">
	<div class="flex min-h-screen w-full flex-col items-center justify-center bg-[#0a0a0a] px-5 py-16 text-center text-white">
		<div class="reci-reflection-form flex flex-col items-center justify-center w-full">
			<h2 class="max-w-[820px] font-['Playfair_Display'] text-3xl leading-tight text-white sm:text-4xl lg:text-5xl"><?php echo esc_html($args['prompt'] ?: $args['title']); ?></h2>
			<textarea class="reflect-input reci-reflection-prompt__input mt-8 h-[150px] w-full max-w-[600px] rounded-none border border-white/20 bg-[#222] px-5 py-4 text-base text-white outline-none" placeholder="Share your thoughts..."></textarea>
			<?php if ( ! is_user_logged_in() ) : ?>
				<p class="mt-4 text-sm text-white/60">Log in or create a free account to record your reflections in your private journal.</p>
			<?php endif; ?>
			<button class="reci-complete-btn mt-8 inline-flex items-center justify-center border border-white/60 px-8 py-3 font-['Oswald'] text-xs uppercase tracking-[0.14em] text-white no-underline hover:bg-white hover:text-black transition-colors" type="button" data-complete-href="<?php echo esc_url($args['button_href']); ?>"><?php echo esc_html($args['button_label']); ?></button>
		</div>
		
		<div class="reci-reflection-success hidden flex-col items-center justify-center w-full opacity-0 transition-opacity duration-700">
			<svg class="w-16 h-16 text-white mb-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
			<h3 class="text-3xl font-['Playfair_Display'] text-white mb-4">Reflection Saved</h3>
			<p class="text-white/60 text-base mb-10 max-w-md mx-auto">Your thoughts have been securely recorded in your private journal.</p>
			<div class="flex flex-col sm:flex-row gap-4 justify-center">
				<a href="/reflections" class="inline-flex items-center justify-center border border-white/60 px-8 py-3 font-['Oswald'] text-xs uppercase tracking-[0.14em] text-white no-underline hover:bg-white hover:text-black transition-colors">Return to Gallery</a>
				<button class="reci-restart-btn inline-flex items-center justify-center border border-white/20 px-8 py-3 font-['Oswald'] text-xs uppercase tracking-[0.14em] text-white/60 no-underline hover:border-white/60 hover:text-white transition-colors" type="button">Start Over</button>
			</div>
		</div>
	</div>
</section>
