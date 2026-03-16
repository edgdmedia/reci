<?php
/**
 * Reflection chapter timeline-world variant: documentary.
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
	<div class="reci-stage-shell">
		<div class="reci-stage-body justify-center">
			<div class="mx-auto flex w-full max-w-[1180px] flex-col gap-6">
				<div class="max-w-[54rem]">
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
					<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4.5rem]">Timeline of circulation</h2>
				</div>
				<div class="reci-scroll-panel rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
					<div class="relative border-l border-[color:var(--reflection-border)] pl-8">
						<?php foreach ((array) $args['items'] as $item) : ?>
							<article class="relative mb-8 last:mb-0">
								<span class="absolute -left-[2.2rem] top-3 h-4 w-4 rounded-full border-4 border-[var(--reflection-hotspot-ring)] bg-[var(--reflection-accent)]"></span>
								<h3 class="font-['Playfair_Display'] text-3xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($item['title'] ?? ''); ?></h3>
								<p class="mt-4 text-base leading-8 text-[var(--reflection-soft-text)]"><?php echo esc_html($item['body'] ?? ''); ?></p>
								<?php if (! empty($item['media'])) : ?>
									<div class="mt-5 grid gap-4 <?php echo count($item['media']) > 1 ? 'lg:grid-cols-2' : ''; ?>">
										<?php foreach ($item['media'] as $media) : ?>
											<figure class="overflow-hidden rounded-[18px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)]">
												<img class="block h-[280px] w-full object-cover" src="<?php echo esc_url($media['src']); ?>" alt="<?php echo esc_attr($media['alt']); ?>">
												<figcaption class="px-4 pb-4 pt-3 text-sm text-[var(--reflection-muted)]"><?php echo esc_html($media['caption']); ?></figcaption>
											</figure>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								<?php if (! empty($item['link'])) : ?>
									<p class="mt-4"><a class="font-semibold text-[var(--reflection-accent)] no-underline" href="<?php echo esc_url($item['link']['href']); ?>" target="_blank" rel="noopener"><?php echo esc_html($item['link']['label']); ?></a></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="flex flex-wrap gap-4">
					<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
				</div>
			</div>
		</div>
	</div>
</section>
