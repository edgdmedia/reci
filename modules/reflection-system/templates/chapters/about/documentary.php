<?php
/**
 * Reflection chapter about variant: documentary.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'about-exhibit',
	'eyebrow' => '',
	'title' => '',
	'intro' => '',
	'items' => [],
	'continue_label' => 'Return',
	'continue_target' => 'top',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body">
			<div class="grid gap-4">
				<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
				<h2 class="font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl lg:text-[4rem]"><?php echo esc_html($args['title']); ?></h2>
				<p class="max-w-[74rem] text-base leading-8 reci-reflection-soft-text sm:text-[1.05rem] sm:leading-9"><?php echo esc_html($args['intro']); ?></p>
			</div>
			<div class="reci-scroll-panel mt-8">
				<div class="grid gap-4 lg:grid-cols-2">
					<?php foreach ((array) $args['items'] as $item) : ?>
						<article class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
							<h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold reci-reflection-text"><?php echo esc_html($item['title']); ?></h3>
							<?php foreach ((array) ($item['paragraphs'] ?? []) as $paragraph) : ?>
								<p class="<?php echo $paragraph !== (($item['paragraphs'] ?? [])[0] ?? null) ? 'mt-4 ' : ''; ?>text-base leading-8 reci-reflection-soft-text"><?php echo wp_kses_post($paragraph); ?></p>
							<?php endforeach; ?>
						</article>
					<?php endforeach; ?>
				</div>
				<div class="mt-8 flex flex-wrap gap-4">
					<?php if (($args['transition_mode'] ?? 'button') === 'button') : ?>
					<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
