<?php
/**
 * Reflection chapter timeline-world variant: horizontal.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'timeline',
	'eyebrow' => '',
	'items' => [],
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell overflow-hidden">
		<div class="reci-timeline-world" id="timelineWorld">
			<?php foreach ((array) $args['items'] as $index => $item) : ?>
				<div class="reci-timeline-panel" data-timeline-index="<?php echo esc_attr((string) $index); ?>">
					<article class="reci-timeline-card max-h-[76vh] overflow-y-auto">
						<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent flex items-center gap-2">
							<?php if (!empty($item['date'])) : ?><span class="font-bold border border-current px-2 py-1 rounded text-xs"><?php echo esc_html($item['date']); ?></span><?php endif; ?>
							<span><?php echo esc_html($args['eyebrow']); ?></span>
						</div>
						<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl"><?php echo esc_html($item['title'] ?? ''); ?></h2>
						<p class="mt-5 max-w-[56rem] text-lg leading-8 reci-reflection-soft-text"><?php echo esc_html($item['body'] ?? ''); ?></p>
						<?php if (! empty($item['media'])) : ?>
							<div class="mt-6 grid gap-4 <?php echo count($item['media']) > 1 ? 'xl:grid-cols-2' : 'lg:grid-cols-1'; ?>">
								<?php foreach ($item['media'] as $media) : ?>
									<figure class="overflow-hidden rounded-[18px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)]">
										<img class="block h-[280px] w-full object-cover xl:h-[320px]" src="<?php echo esc_url($media['src']); ?>" alt="<?php echo esc_attr($media['alt']); ?>">
										<figcaption class="px-4 pb-4 pt-3 text-sm reci-reflection-muted"><?php echo esc_html($media['caption']); ?></figcaption>
									</figure>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if (! empty($item['link'])) : ?>
							<p class="mt-5"><a class="font-semibold reci-reflection-accent no-underline" href="<?php echo esc_url($item['link']['href']); ?>" target="_blank" rel="noopener"><?php echo esc_html($item['link']['label']); ?></a></p>
						<?php endif; ?>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="reci-timeline-controls">
			<button class="reci-icon-btn" type="button" id="timelinePrev">←</button>
			<button class="reci-icon-btn" type="button" id="timelineNext">→</button>
			<?php if (($args['transition_mode'] ?? 'button') === 'button') : ?>
			<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
			<?php endif; ?>
		</div>
	</div>
</section>
