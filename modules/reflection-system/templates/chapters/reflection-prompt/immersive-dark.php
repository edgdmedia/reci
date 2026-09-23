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
		<h2 class="reflect-prompt"><?php echo reci_reflection_format_text($args['prompt']); ?></h2>
				<?php
		get_template_part( 'template-parts/reflection/prompt-form', null, [
			'prompt'              => $args['prompt'],
			'textarea_tone'       => 'border-white/20 bg-white/5 text-white placeholder:text-white/40',
			'button_class'        => 'enter-btn',
			'tone_class'          => 'text-white/70',
			'success_title_class' => 'mb-3 font-[\'Playfair_Display\'] text-2xl text-white',
			'success_body_class'  => 'mb-2 text-base text-white/60',
		] );
		?>
	</div>
</section>
