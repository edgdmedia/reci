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
			<h2 class="max-w-[820px] font-['Playfair_Display'] text-3xl leading-tight text-white sm:text-4xl lg:text-5xl"><?php echo reci_reflection_format_text($args['prompt'] ?: $args['title']); ?></h2>
			<?php
			get_template_part( 'template-parts/reflection/prompt-form', null, [
				'prompt'              => $args['prompt'],
				'textarea_tone'       => 'border-white/20 bg-[#222] text-white placeholder:text-white/40',
				'button_class'        => 'inline-flex items-center justify-center border border-white/60 px-8 py-3 font-[\'Oswald\'] text-xs uppercase tracking-[0.14em] text-white no-underline hover:bg-white hover:text-black transition-colors',
				'tone_class'          => 'text-white/70',
				'success_title_class' => 'text-2xl font-[\'Playfair_Display\'] text-white mb-3',
				'success_body_class'  => 'text-white/60 text-base mb-2',
			] );
			?>
		</div>
	</div>
</section>
