<?php
/**
 * Reflection chapter prompt variant: exit stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'reflection',
	'eyebrow' => '',
	'title' => '',
	'intro' => '',
	'cards' => [],
	'prompt' => '',
	'continue_label' => 'Return',
	'continue_target' => 'top',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body justify-center">
			<div class="mx-auto flex w-full max-w-[1040px] flex-col items-center rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] px-6 py-10 text-center sm:px-8 lg:px-12 lg:py-14">
				<?php
				get_template_part( 'template-parts/reflection/prompt-intro', null, [
					'eyebrow'          => $args['eyebrow'] ?? '',
					'title'            => $args['title'] ?? '',
					'intro'            => $args['intro'] ?? '',
					'cards'            => $args['cards'] ?? [],
					'eyebrow_class'    => 'font-[\'Oswald\'] text-sm uppercase tracking-[0.14em] reci-reflection-accent',
					'title_class'      => 'mt-4 max-w-[16ch] font-[\'Playfair_Display\'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl lg:text-[4.5rem]',
					'intro_class'      => 'mt-5 max-w-[44rem] text-base leading-8 reci-reflection-soft-text sm:text-lg sm:leading-9',
					'cards_class'      => 'mt-8 grid w-full gap-4 md:grid-cols-2',
					'card_class'       => 'rounded-3xl border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)] p-5 text-left',
					'card_title_class' => 'mb-3 font-[\'Playfair_Display\'] text-2xl font-semibold reci-reflection-text',
					'card_body_class'  => 'text-base leading-8 reci-reflection-soft-text',
				] );
				?>
				<div class="mt-8 w-full max-w-[42rem] rounded-[24px] border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] p-6 text-left">
					<label class="mb-3 block font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo reci_reflection_format_text($args['prompt'] ?: 'Your reflection'); ?></label>
					<?php
					get_template_part( 'template-parts/reflection/prompt-form', null, [
						'prompt'              => $args['prompt'],
						'continue_label'      => $args['continue_label'] ?? '',
						'continue_target'     => ( ( $args['transition_mode'] ?? 'button' ) === 'button' ) ? ( $args['continue_target'] ?? '' ) : '',
						'textarea_tone'       => 'border-[color:var(--reflection-border)] bg-transparent reci-reflection-text',
						'button_class'        => 'inline-flex items-center justify-center rounded-full border border-[color:var(--reflection-border)] bg-transparent px-6 py-3 font-[\'Oswald\'] text-sm uppercase tracking-[0.1em] reci-reflection-text no-underline hover:bg-[var(--reflection-card-strong)]',
						'tone_class'          => 'reci-reflection-soft-text',
						'success_title_class' => 'mb-3 font-[\'Playfair_Display\'] text-3xl font-semibold reci-reflection-text',
						'success_body_class'  => 'mb-2 text-base leading-8 reci-reflection-soft-text',
					] );
					?>
				</div>
			</div>
		</div>
	</div>
</section>
