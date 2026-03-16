<?php
/**
 * Reflection chapter threshold message variant: threshold.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'threshold',
	'title' => '',
	'button_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="relative flex min-h-screen w-full items-center justify-center bg-[#111] px-5 py-16 text-center">
		<div class="absolute inset-0 bg-white/10"></div>
		<div class="relative z-10 max-w-[800px]">
			<h2 id="vorThresholdText" class="font-['Oswald'] text-4xl uppercase tracking-[0.25em] text-[#333] transition-[color] duration-[2000ms] sm:text-5xl lg:text-[3rem]"><?php echo esc_html($args['title']); ?></h2>
			<button type="button" id="vorThresholdContinue" class="mt-8 border border-[#333] px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-[#333]" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['button_label']); ?></button>
		</div>
	</div>
</section>
