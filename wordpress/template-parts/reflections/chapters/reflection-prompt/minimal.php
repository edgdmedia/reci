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
	'button_label' => 'Complete Journey',
	'button_href' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="flex min-h-screen w-full flex-col items-center justify-center bg-[#0a0a0a] px-5 py-16 text-center text-white">
		<h2 class="max-w-[820px] font-['Playfair_Display'] text-4xl leading-tight text-white sm:text-5xl lg:text-[3rem]"><?php echo esc_html($args['prompt'] ?: $args['title']); ?></h2>
		<textarea class="mt-8 h-[150px] w-full max-w-[600px] rounded-none border border-white/20 bg-[#222] px-5 py-4 text-lg text-white outline-none" placeholder="Share your thoughts..."></textarea>
		<a class="mt-8 inline-flex items-center justify-center border border-white/60 px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-white no-underline" href="<?php echo esc_url($args['button_href']); ?>"><?php echo esc_html($args['button_label']); ?></a>
	</div>
</section>
