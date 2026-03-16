<?php
if (! defined('ABSPATH')) {
	exit;
}
$args = wp_parse_args($args ?? [], [
	'id' => 'data-gap',
	'title' => '',
	'intro' => '',
	'cards' => [],
	'footer_text' => '',
	'footer_href' => '#',
	'footer_label' => 'Return',
]);
?>
<section class="py-16 sm:py-20 mx-auto" id="<?php echo esc_attr($args['id']); ?>">
	<div class="mx-auto w-full max-w-[1240px] px-5 sm:px-6 lg:px-12 xl:px-16">
		<div class="text-center">
			<h1 class="font-['Space_Grotesk'] text-5xl font-bold leading-none text-[var(--reflection-accent-contrast)] sm:text-6xl"><?php echo esc_html($args['title']); ?></h1>
			<p class="mx-auto mt-5 max-w-[600px] text-xl leading-8 text-[var(--reflection-muted)]"><?php echo esc_html($args['intro']); ?></p>
		</div>
		<div class="mt-10 grid gap-[2px] bg-black/10 p-[2px] md:grid-cols-2" id="rdDataGrid">
			<?php foreach ((array) $args['cards'] as $index => $card) : ?>
				<article class="rd-data-card cursor-pointer bg-white p-8 transition" data-data-card data-card-index="<?php echo esc_attr((string) $index); ?>">
					<div class="text-3xl"><?php echo esc_html($card['icon'] ?? ''); ?></div>
					<div class="mt-5 font-mono text-sm uppercase tracking-[0.08em] text-black/50"><?php echo esc_html($card['eyebrow'] ?? ''); ?></div>
					<div class="mt-3 flex items-baseline gap-3 text-5xl font-bold text-black"><span><?php echo esc_html($card['stat'] ?? ''); ?></span><span class="text-base font-normal text-black/50"><?php echo esc_html($card['unit'] ?? ''); ?></span></div>
					<p class="mt-4 text-base leading-7 text-black/75"><?php echo esc_html($card['summary'] ?? ''); ?></p>
					<div class="rd-card-detail mt-5 hidden border-t border-black/20 pt-5 text-sm leading-7 text-white/85">
						<?php if (! empty($card['toggle'])) : ?>
							<div class="mb-4 flex gap-3 font-mono text-xs uppercase tracking-[0.08em]">
								<button type="button" class="rd-toggle-btn bg-black/5 px-3 py-2 text-black/60" data-toggle-solution><?php echo esc_html($card['toggle']); ?></button>
							</div>
						<?php endif; ?>
						<div class="rd-view-problem">
							<p><?php echo esc_html($card['detail'] ?? ''); ?></p>
							<?php if (! empty($card['bars'])) : ?>
								<div class="mt-5 grid gap-3">
									<?php foreach ($card['bars'] as $bar) : ?>
										<div class="flex items-center gap-3 text-xs">
											<div class="w-20 shrink-0"><?php echo esc_html($bar['label']); ?></div>
											<div class="h-2 flex-1 rounded-full bg-white/10">
												<div class="h-2 rounded-full <?php echo ! empty($bar['alert']) ? 'bg-[var(--reflection-alert)]' : 'bg-white/60'; ?>" style="width: <?php echo esc_attr($bar['width']); ?>;"></div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
						<?php if (! empty($card['solution'])) : ?><div class="rd-view-solution hidden">
								<h4 class="mb-2 font-semibold text-[var(--reflection-accent)]">Path Forward</h4>
								<p><?php echo esc_html($card['solution']); ?></p>
							</div><?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="mt-12 text-center">
			<p class="mb-5 text-base text-[var(--reflection-muted)]"><?php echo esc_html($args['footer_text']); ?></p>
			<a class="inline-flex items-center justify-center border border-black px-8 py-3 font-mono text-sm uppercase tracking-[0.12em] text-black no-underline" href="<?php echo esc_url($args['footer_href']); ?>"><?php echo esc_html($args['footer_label']); ?></a>
		</div>
	</div>
</section>