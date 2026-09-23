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
			'style'          => 'stage',
			'prompt'         => $args['prompt'],
			'textarea_class' => 'reflect-input reci-reflection-prompt__input',
			'placeholder'    => 'Share your thoughts...',
			'note_class'     => 'mt-4 text-sm text-white/60',
			'row_class'      => 'mt-8 flex flex-wrap items-center justify-center gap-4',
			'status_class'   => 'mt-4 text-sm text-white/70',
			'button_class'   => 'enter-btn',
						'success_class'       => 'hidden flex-col items-center justify-center w-full text-center',
			'success_title_class' => 'mb-4 font-[\'Playfair_Display\'] text-3xl text-white',
			'success_body_class'  => 'mb-8 max-w-md text-base text-white/60',
			'success_row_class'   => 'flex flex-col sm:flex-row gap-4 justify-center',
			'restart_class'       => 'enter-btn',
			'continue_label' => $args['button_label'],
			'continue_href'  => $args['button_href'],
		] );
		?>
	</div>
</section>
