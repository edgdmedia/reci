<?php
/**
 * Voices of Resistance progressive text stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-hold',
	'title' => 'The Decision',
	'paragraphs' => [],
	'prompt' => 'Click to Reveal',
	'button_label' => '▼',
	'continue_label' => 'Face the Moment →',
]);
?>
<section class="reci-stage chapter-hold !justify-between !px-0" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	
	<!-- TOP: Title Area (approx 20%) -->
	<div class="flex-none w-full h-[20vh] min-h-[100px] flex items-end justify-center pb-4">
		<h2 class="hold-title !m-0"><?php echo esc_html($args['title']); ?></h2>
	</div>

	<!-- MIDDLE: Progressive Text Area (approx 60%) -->
	<div class="relative w-full max-w-[800px] mx-auto flex-1 flex flex-col min-h-0">
		<!-- Top fade overlay -->
		<div class="absolute top-0 left-0 right-0 h-16 bg-gradient-to-b from-[#111111] to-transparent z-10 pointer-events-none" style="background-image: linear-gradient(to bottom, var(--reflection-bg, #111) 0%, transparent 100%);"></div>
		
		<!-- The scrolling container needs flex-1 and overflow-y-auto to fill the middle space without expanding -->
		<div class="hold-text-container flex-1 overflow-y-auto w-full px-4 scroll-smooth flex flex-col" style="scrollbar-width: none;">
			<style>.hold-text-container::-webkit-scrollbar { display: none; }</style>
			<!-- m-auto vertically centers the text block if it's small, but allows it to scroll normally when it grows large -->
			<div class="m-auto w-full py-16">
				<?php foreach ((array) $args['paragraphs'] as $paragraph) : 
					$text = is_array($paragraph) ? ($paragraph['text'] ?? implode(' ', $paragraph)) : (string) $paragraph;
				?>
					<p class="reci-progressive-paragraph hidden opacity-0 translate-y-5 transition-all duration-1000 ease-in-out mb-6 text-xl text-white/40 leading-relaxed text-center [&.progressive-latest]:text-3xl [&.progressive-latest]:lg:text-4xl [&.progressive-latest]:text-white [&.progressive-latest]:leading-[1.4]"><?php echo esc_html($text); ?></p>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Bottom fade overlay -->
		<div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-[#111111] to-transparent z-10 pointer-events-none" style="background-image: linear-gradient(to top, var(--reflection-bg, #111) 0%, transparent 100%);"></div>
	</div>

	<!-- BOTTOM: Controls Area (approx 20%) -->
	<div class="flex-none w-full h-[20vh] min-h-[120px] flex flex-col items-center justify-start pt-4 relative">
		<div class="hold-controls flex flex-col items-center">
			<div class="hold-prompt mb-3 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white/70"><?php echo esc_html($args['prompt']); ?></div>
			<button class="reci-progressive-reveal flex h-20 w-20 items-center justify-center rounded-full border-2 border-white/35 bg-transparent text-3xl text-white transition active:scale-90" type="button"><?php echo esc_html($args['button_label']); ?></button>
		</div>
		<?php if (($args['transition_mode'] ?? 'button') === 'button') : ?>
		<button class="reci-progressive-continue absolute top-4 hidden border border-white/40 px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
		<?php endif; ?>
	</div>

</section>
