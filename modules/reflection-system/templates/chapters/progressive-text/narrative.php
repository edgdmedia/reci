<?php
/**
 * Reflection chapter progressive text variant: narrative.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'hold',
	'title' => '',
	'paragraphs' => [],
	'prompt' => 'Click to Reveal',
	'button_label' => '▼',
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="flex min-h-screen w-full flex-col items-center justify-center bg-[#1a1a1a] px-5 py-16 text-center sm:px-6 lg:px-12">
		<?php if ($args['title']) : ?>
			<h2 class="font-['Playfair_Display'] text-4xl font-semibold reci-reflection-accent sm:text-5xl lg:text-[3.25rem]"><?php echo esc_html($args['title']); ?></h2>
		<?php endif; ?>
		<div class="mt-10 min-h-[200px] max-w-[700px] font-['Playfair_Display'] text-2xl leading-10 text-white/90">
			<?php foreach ((array) $args['paragraphs'] as $paragraph) : 
				$text = is_array($paragraph) ? ($paragraph['text'] ?? implode(' ', $paragraph)) : (string) $paragraph;
			?>
				<p class="reci-progressive-paragraph mb-5 hidden translate-y-5 opacity-0 transition"><?php echo esc_html($text); ?></p>
			<?php endforeach; ?>
		</div>
		<div class="mt-10 text-center flex flex-col items-center">
			<div class="mb-3 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white/70 w-auto px-4"><?php echo esc_html($args['prompt']); ?></div>
			<button type="button" class="reci-progressive-reveal flex h-20 w-20 items-center justify-center rounded-full border-2 border-white/35 bg-transparent text-3xl text-white transition active:scale-90"><?php echo esc_html($args['button_label']); ?></button>
		</div>
		<?php if (($args['transition_mode'] ?? 'button') === 'button') : ?>
		<button type="button" class="reci-progressive-continue mt-12 hidden border border-white/40 px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
		<?php endif; ?>
	</div>
</section>
