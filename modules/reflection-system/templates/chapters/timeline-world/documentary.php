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
	'title' => '',
	'items' => [],
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body justify-center">
			<div class="mx-auto flex h-full w-full max-w-[1180px] flex-col gap-6 py-12">
				<div class="flex items-end justify-between w-full">
					<div class="max-w-[54rem]">
						<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
						<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl lg:text-5xl"><?php echo esc_html($args['title'] ?: 'Timeline of circulation'); ?></h2>
					</div>
					
					<?php if (($args['transition_mode'] ?? 'button') === 'button' && !empty($args['continue_target']) && $args['continue_target'] !== '#') : ?>
					<div class="mb-2">
						<button class="reci-continue inline-flex items-center justify-center border border-[var(--reflection-border-soft)] px-8 py-3 font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-heading)] transition hover:bg-[var(--reflection-heading)] hover:text-[var(--reflection-bg)]" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
					</div>
					<?php endif; ?>
				</div>
				<div class="reci-scroll-panel !justify-start flex-1 overflow-y-auto rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6 md:p-10 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-[color:var(--reflection-border)]">
					<div class="relative border-l border-[color:var(--reflection-border)] pl-8">
						<?php foreach ((array) $args['items'] as $item) : ?>
							<article class="relative mb-12 last:mb-0">
								<span class="absolute -left-[2.2rem] top-3 h-4 w-4 rounded-full border-4 border-[var(--reflection-hotspot-ring)] bg-[var(--reflection-accent)]"></span>
								<?php if (!empty($item['date'])) : ?><div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent mb-2 font-bold"><?php echo esc_html($item['date']); ?></div><?php endif; ?>
								<h3 class="font-['Playfair_Display'] text-3xl font-semibold reci-reflection-text"><?php echo esc_html($item['title'] ?? ''); ?></h3>
								<p class="mt-4 text-base leading-8 reci-reflection-soft-text"><?php echo esc_html($item['body'] ?? ''); ?></p>
								<?php if (! empty($item['media'])) : ?>
									<div class="mt-5 grid gap-4 <?php echo count($item['media']) > 1 ? 'lg:grid-cols-2' : ''; ?>">
										<?php foreach ($item['media'] as $media) : ?>
											<figure class="overflow-hidden rounded-[18px] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)]">
												<button type="button" class="group relative block w-full cursor-zoom-in text-left" data-lightbox-image data-lightbox-src="<?php echo esc_url($media['src']); ?>" data-lightbox-alt="<?php echo esc_attr($media['alt']); ?>" data-lightbox-caption="<?php echo esc_attr($media['caption']); ?>" aria-label="<?php echo esc_attr(sprintf('Enlarge image: %s', $media['caption'])); ?>">
													<img class="block max-h-[380px] w-full object-contain" src="<?php echo esc_url($media['src']); ?>" alt="<?php echo esc_attr($media['alt']); ?>">
													<span class="pointer-events-none absolute right-3 top-3 z-[1] flex h-10 w-10 items-center justify-center rounded-full bg-[var(--reflection-overlay)] text-[var(--reflection-text)] opacity-80 shadow-[0_4px_14px_rgba(0,0,0,0.35)] transition group-hover:scale-105 group-hover:opacity-100" aria-hidden="true">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
													</span>
												</button>
												<figcaption class="px-4 pb-4 pt-3 text-sm reci-reflection-muted"><?php echo esc_html($media['caption']); ?></figcaption>
											</figure>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								<?php if (! empty($item['link'])) : ?>
									<p class="mt-4"><a class="font-semibold reci-reflection-accent no-underline hover:underline" href="<?php echo esc_url($item['link']['href']); ?>" target="_blank" rel="noopener"><?php echo esc_html($item['link']['label']); ?></a></p>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
