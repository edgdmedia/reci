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
				<div class="rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6 lg:sticky lg:top-24 lg:h-fit">
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
					<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl"><?php echo esc_html($args['title']); ?></h2>
					<p class="mt-5 text-base leading-8 text-[var(--reflection-soft-text)]"><?php echo esc_html($args['intro']); ?></p>
					<div class="mt-8 flex flex-wrap gap-4">
						<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
					</div>
				</div>
				<div class="reci-panel-scroll">
					<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
						<?php foreach ((array) $args['items'] as $item) : ?>
							<article class="overflow-hidden rounded-[20px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)]">
								<img class="panel-image block h-[360px] w-full cursor-zoom-in object-cover" src="<?php echo esc_url($item['src']); ?>" alt="<?php echo esc_attr($item['alt']); ?>">
								<div class="p-4">
									<h3 class="mb-1 font-['Playfair_Display'] text-xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($item['title']); ?></h3>
									<p class="text-sm leading-7 text-[var(--reflection-soft-text)]"><?php echo esc_html($item['description']); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
