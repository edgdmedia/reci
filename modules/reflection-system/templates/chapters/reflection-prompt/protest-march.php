<?php
/**
 * Reflection prompt variant: Protest March.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'id' => 's-reflect',
		'prompt' => '',
		'button_label' => 'Submit Reflection',
		'button_href' => '#',
	]
);

$section_attributes = '';
if (! empty($args['section_attributes']) && is_array($args['section_attributes'])) {
	foreach ($args['section_attributes'] as $attr_key => $attr_value) {
		$section_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
	}
}
?>
<section class="reci-stage flex flex-col items-center justify-center bg-[#111] p-8 text-white <?php echo esc_attr($args['section_class'] ?? ''); ?>" id="<?php echo esc_attr($args['id']); ?>" data-reflection-id="<?php echo esc_attr(get_the_ID()); ?>" data-prompt="<?php echo esc_attr($args['prompt']); ?>"<?php echo $section_attributes; ?>>
	<div class="reci-reflection-form w-full max-w-[840px] border border-[rgba(255,255,255,0.12)] bg-gradient-to-b from-[rgba(255,255,255,0.06)] to-[rgba(255,255,255,0.03)] p-[clamp(2rem,4vw,3.25rem)] text-center shadow-[0_24px_60px_rgba(0,0,0,0.2)]">
		<h2 class="mb-[1.75rem] font-['Oswald'] text-[clamp(2.8rem,5vw,4.2rem)] uppercase tracking-[0.04em] reci-reflection-accent">Your Reflection</h2>
		<p class="mx-auto mb-[2rem] max-w-[38rem] text-[clamp(1.15rem,2vw,1.6rem)] leading-[1.65] text-[rgba(255,255,255,0.92)]"><?php echo esc_html($args['prompt']); ?></p>
		<textarea class="reflect-input reci-reflection-prompt__input mb-[2rem] min-h-[160px] w-full rounded-[10px] border border-[rgba(255,255,255,0.12)] bg-[rgba(255,255,255,0.08)] p-[18px_20px] font-['Merriweather'] text-white" placeholder="Share your thoughts..."></textarea>
		<?php if ( ! is_user_logged_in() ) : ?>
			<p class="mb-6 text-sm text-white/60">Log in or create a free account to record your reflections in your private journal.</p>
		<?php endif; ?>
		<button class="reci-complete-btn inline-flex items-center justify-center bg-[var(--reflection-accent)] px-[50px] py-[20px] font-['Oswald'] text-[1.2rem] uppercase tracking-[2px] text-white transition-transform hover:scale-105" type="button" data-complete-href="<?php echo esc_url($args['button_href']); ?>"><?php echo esc_html($args['button_label']); ?></button>
	</div>
	
	<div class="reci-reflection-success hidden flex-col items-center justify-center w-full max-w-[840px] border border-[rgba(255,255,255,0.12)] bg-gradient-to-b from-[rgba(255,255,255,0.06)] to-[rgba(255,255,255,0.03)] p-[clamp(2rem,4vw,3.25rem)] text-center shadow-[0_24px_60px_rgba(0,0,0,0.2)] opacity-0 transition-opacity duration-700">
		<svg class="w-20 h-20 text-[var(--reflection-accent)] mb-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
		<h3 class="mb-[1.5rem] font-['Oswald'] text-[clamp(2rem,4vw,3rem)] uppercase tracking-[0.04em] text-white">Reflection Saved</h3>
		<p class="mx-auto mb-[3rem] max-w-[38rem] text-[clamp(1.15rem,2vw,1.4rem)] leading-[1.65] text-[rgba(255,255,255,0.92)]">Your thoughts have been securely recorded in your private journal.</p>
		<div class="flex flex-col sm:flex-row gap-6 justify-center">
			<a href="/reflections" class="inline-flex items-center justify-center border border-white/60 px-[40px] py-[16px] font-['Oswald'] text-[1.1rem] uppercase tracking-[2px] text-white no-underline hover:bg-white hover:text-black transition-colors">Return to Gallery</a>
			<button class="reci-restart-btn inline-flex items-center justify-center bg-[var(--reflection-accent)] px-[40px] py-[16px] font-['Oswald'] text-[1.1rem] uppercase tracking-[2px] text-white transition-transform hover:scale-105" type="button">Start Over</button>
		</div>
	</div>
</section>
