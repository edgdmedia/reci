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
		<p class="mx-auto mb-[2rem] max-w-[38rem] text-[clamp(1.15rem,2vw,1.6rem)] leading-[1.65] text-[rgba(255,255,255,0.92)]"><?php echo reci_reflection_format_text($args['prompt']); ?></p>
				<?php
		get_template_part( 'template-parts/reflection/prompt-form', null, [
			'style'          => 'stage',
			'prompt'         => $args['prompt'],
			'textarea_class' => 'reflect-input reci-reflection-prompt__input mb-[2rem] min-h-[160px] w-full rounded-[10px] border border-[rgba(255,255,255,0.12)] bg-[rgba(255,255,255,0.08)] p-[18px_20px] font-[\'Merriweather\'] text-white',
			'placeholder'    => 'Share your thoughts...',
			'note_class'     => 'mb-6 text-sm text-white/60',
			'row_class'      => 'mt-8 flex flex-wrap items-center justify-center gap-4',
			'status_class'   => 'mt-4 text-sm text-white/70',
			'button_class'   => 'inline-flex items-center justify-center bg-[var(--reflection-accent)] px-[50px] py-[20px] font-[\'Oswald\'] text-[1.2rem] uppercase tracking-[2px] text-white transition-transform hover:scale-105',
						'success_class'       => 'flex-col items-center justify-center w-full text-center',
			'success_title_class' => 'mb-[1.5rem] font-[\'Oswald\'] text-[clamp(2rem,4vw,3rem)] uppercase tracking-[0.04em] text-white',
			'success_body_class'  => 'mx-auto mb-[3rem] max-w-[38rem] text-[clamp(1.15rem,2vw,1.4rem)] leading-[1.65] text-[rgba(255,255,255,0.92)]',
			'success_row_class'   => 'flex flex-col sm:flex-row gap-6 justify-center',
			'restart_class'       => 'inline-flex items-center justify-center border border-white/40 px-[40px] py-[16px] font-[\'Oswald\'] text-[1.1rem] uppercase tracking-[2px] text-white/70',
			'continue_label' => $args['button_label'],
			'continue_href'  => $args['button_href'],
		] );
		?>
	</div>
</section>
