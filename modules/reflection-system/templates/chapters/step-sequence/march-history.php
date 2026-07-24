<?php
if (! defined('ABSPATH')) { exit; }
$args = wp_parse_args($args ?? [], [
	'id' => 'history-stage',
	'backdrop' => '',
	'title' => '',
	'body' => '',
	'button_label' => 'Forward',
	'continue_target' => '#',
	'dark' => false,
]);
?>
<section id="<?php echo esc_attr($args['id']); ?>" class="reci-stage section history-section relative flex min-h-screen w-screen flex-shrink-0 items-center justify-center overflow-hidden" data-march-panel data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	<div class="year-huge text-[var(--reflection-heading)]"><?php echo esc_html($args['backdrop']); ?></div>
	<div class="march-interaction-layer" data-march-step-layer>
		<div class="history-footprints">
			<span class="mini-fp" data-footprint>👣</span>
			<span class="mini-fp" data-footprint>👣</span>
			<span class="mini-fp" data-footprint>👣</span>
		</div>
		<div class="mb-3 font-['Oswald'] text-sm uppercase tracking-[0.14em] text-[var(--reflection-muted)]">Join the March to Witness</div>
		<button type="button" class="mini-step-btn" data-march-step>Step</button>
	</div>
	<article class="history-card" data-march-card>
		<h3 class="font-['Oswald'] text-2xl uppercase reci-reflection-accent"><?php echo esc_html($args['title']); ?></h3>
		<p class="mt-4 text-base leading-8"><?php echo esc_html($args['body']); ?></p>
		<?php if (($args['transition_mode'] ?? 'button') === 'button') : ?>
		<button type="button" class="mt-6 inline-flex items-center justify-center bg-[var(--reflection-accent)] px-6 py-3 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-white" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['button_label']); ?></button>
		<?php endif; ?>
	</article>
</section>
