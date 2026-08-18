<?php
/**
 * Reflection chapter panel-explorer variant: documentary.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'panels',
	'eyebrow' => '',
	'title' => '',
	'intro' => '',
	'items' => [],
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body">
			<div class="reci-stage-panels">
				<div class="flex flex-row justify-between rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6 lg:sticky  lg:h-fit">
					<div>
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
					<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl"><?php echo esc_html($args['title']); ?></h2>
					<p class="mt-5 text-base leading-8 reci-reflection-soft-text"><?php echo esc_html($args['intro']); ?></p>
					</div>
					<div class="mt-8 flex flex-wrap gap-4">
						<?php if (($args['transition_mode'] ?? 'button') === 'button' && !empty($args['continue_target']) && $args['continue_target'] !== '#') : ?>
						<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
						<?php endif; ?>
					</div>
				</div>
				<div class="reci-panel-scroll">
					<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
						<?php foreach ((array) $args['items'] as $item) : ?>
							<article class="overflow-hidden rounded-[20px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)]">
								<img class="panel-image block max-h-[480px] w-full cursor-zoom-in object-contain" src="<?php echo esc_url($item['src']); ?>" alt="<?php echo esc_attr($item['alt']); ?>" data-annotations="<?php echo esc_attr(wp_json_encode($item['annotations'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>">
								<div class="p-4">
									<h3 class="mb-1 font-['Playfair_Display'] text-xl font-semibold reci-reflection-text"><?php echo esc_html($item['title']); ?></h3>
									<p class="text-sm leading-7 reci-reflection-soft-text"><?php echo esc_html($item['description']); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
