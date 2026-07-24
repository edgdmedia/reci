<?php
if (! defined('ABSPATH')) { exit; }
$args = wp_parse_args($args ?? [], [
	'id' => 'chain-break',
	'text' => '',
	'instruction' => 'Drag Down to Break',
]);
?>
<section class="reci-stage min-h-screen px-5 py-16 text-center reci-reflection-text" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	<div class="mx-auto flex min-h-[70vh] max-w-[720px] flex-col items-center justify-center">
		<p class="max-w-[620px] text-2xl leading-10"><?php echo esc_html($args['text']); ?></p>
		<div class="relative mt-12 h-[400px] w-[100px]" id="chainInteract">
			<div class="absolute left-[10px] top-0 h-[120px] w-[80px] rounded-[40px] border-[15px] border-[var(--reflection-metal)] shadow-[inset_0_0_20px_black,0_10px_20px_rgba(0,0,0,0.5)]"></div>
			<div class="absolute left-[10px] top-[80px] h-[120px] w-[80px] rounded-[40px] border-[15px] border-[var(--reflection-metal)] shadow-[inset_0_0_20px_black,0_10px_20px_rgba(0,0,0,0.5)] transition-transform" id="breakableLink"></div>
		</div>
		<p id="chainInstruction" class="mt-5 font-['Oswald'] text-sm uppercase tracking-[0.18em] reci-reflection-accent"><?php echo esc_html($args['instruction']); ?></p>
		<?php if (! empty($args['continue_target']) && ! empty($args['continue_label']) && ($args['transition_mode'] ?? 'button') === 'button') : ?>
			<div class="mt-12 transition-opacity duration-700 opacity-0 pointer-events-none" id="dragRevealContinueWrapper">
				<button class="inline-flex items-center justify-center border border-[var(--reflection-accent)] px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.18em] text-[var(--reflection-accent)] no-underline transition-colors hover:bg-[var(--reflection-accent)] hover:text-black" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>">
					<?php echo esc_html($args['continue_label']); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
</section>
