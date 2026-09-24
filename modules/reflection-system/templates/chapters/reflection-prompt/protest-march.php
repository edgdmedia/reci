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
				<?php
		get_template_part( 'template-parts/reflection/prompt-intro', null, [
			'eyebrow'          => $args['eyebrow'] ?? '',
			'title'            => $args['title'] ?? '',
			'intro'            => $args['intro'] ?? '',
			'cards'            => $args['cards'] ?? [],
			'eyebrow_class'    => 'mb-2 font-[\'Oswald\'] text-sm uppercase tracking-[0.14em] reci-reflection-accent',
			'title_class'      => 'mb-[1rem] font-[\'Oswald\'] text-[clamp(1.8rem,3vw,2.6rem)] uppercase tracking-[0.04em] text-white',
			'intro_class'      => 'mx-auto mb-[1.5rem] max-w-[38rem] text-[clamp(1rem,1.6vw,1.2rem)] leading-[1.7] text-[rgba(255,255,255,0.8)]',
			'cards_class'      => 'mx-auto mb-[2rem] grid max-w-[38rem] gap-4 md:grid-cols-2',
			'card_class'       => 'border border-white/15 bg-white/5 p-5 text-left',
			'card_title_class' => 'mb-2 font-[\'Oswald\'] text-lg uppercase tracking-[0.04em] text-white',
			'card_body_class'  => 'text-sm leading-7 text-white/70',
		] );
		?>
		<h2 class="mb-[1.75rem] font-['Oswald'] text-[clamp(2.8rem,5vw,4.2rem)] uppercase tracking-[0.04em] reci-reflection-accent">Your Reflection</h2>
		<p class="mx-auto mb-[2rem] max-w-[38rem] text-[clamp(1.15rem,2vw,1.6rem)] leading-[1.65] text-[rgba(255,255,255,0.92)]"><?php echo reci_reflection_format_text($args['prompt']); ?></p>
				<?php
		get_template_part( 'template-parts/reflection/prompt-form', null, [
			'prompt'              => $args['prompt'],
			'textarea_tone'       => 'border-[rgba(255,255,255,0.12)] bg-[rgba(255,255,255,0.08)] text-white placeholder:text-white/40',
			'button_class'        => 'inline-flex items-center justify-center border border-white/60 bg-transparent px-8 py-4 font-[\'Oswald\'] text-base uppercase tracking-[2px] text-white no-underline transition-colors hover:bg-white hover:text-black',
			'tone_class'          => 'text-white/70',
			'success_title_class' => 'mb-3 font-[\'Oswald\'] text-3xl uppercase tracking-[0.04em] text-white',
			'success_body_class'  => 'mb-2 text-base text-[rgba(255,255,255,0.92)]',
		] );
		?>
	</div>
</section>
