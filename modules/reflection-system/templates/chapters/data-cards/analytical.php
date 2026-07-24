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
	'footer_label' => 'Return',
]);

$scope_class = 'rd-scope-' . uniqid();
$transition_mode = $args['transition_mode'] ?? 'button';
$continue_target = ltrim($args['continue_target'] ?? '', '#');
?>
<section class="reci-stage py-16 sm:py-20 mx-auto <?php echo esc_attr($scope_class); ?>" id="<?php echo esc_attr($args['id'] ?? ''); ?>" style="<?php echo esc_attr($args['inline_style'] ?? ''); ?>" data-transition-mode="<?php echo esc_attr($transition_mode); ?>" data-continue-target="<?php echo esc_attr($continue_target); ?>">
	<style>
		.<?php echo esc_attr($scope_class); ?> .rd-data-card {
			background-color: var(--reflection-surface, var(--reflection-bg));
		}
		.<?php echo esc_attr($scope_class); ?> .rd-data-card.active {
			border-color: var(--reflection-accent);
		}
		.<?php echo esc_attr($scope_class); ?> .rd-data-card .rd-toggle-btn:hover {
			border-color: var(--reflection-accent);
			color: var(--reflection-accent);
		}
		.<?php echo esc_attr($scope_class); ?> .rd-bar-track {
			background-color: var(--reflection-heading);
			opacity: 0.1;
		}
		.<?php echo esc_attr($scope_class); ?> .rd-bar-fill {
			background-color: var(--reflection-accent);
		}
		.<?php echo esc_attr($scope_class); ?> .rd-bar-fill-alert {
			background-color: var(--reflection-alert);
		}
	</style>
	<div class="mx-auto w-full max-w-[1240px] px-5 sm:px-6 lg:px-12 xl:px-16">
		<div class="text-center">
			<h1 class="font-['Space_Grotesk'] text-5xl font-bold leading-none text-[var(--reflection-heading)] sm:text-6xl"><?php echo esc_html($args['title']); ?></h1>
			<p class="mx-auto mt-5 max-w-[600px] text-xl leading-8 reci-reflection-muted"><?php echo esc_html($args['intro']); ?></p>
		</div>
		<div class="mt-10 grid gap-[2px] bg-black/10 p-[2px] md:grid-cols-2" id="rdDataGrid">
			<?php foreach ((array) $args['cards'] as $index => $card) : ?>
				<article class="group rd-data-card cursor-pointer border-2 border-transparent group-[.active]:scale-[1.02] group-[.active]:shadow-2xl group-[.active]:z-10 relative p-8 transition-all duration-300" data-data-card data-card-index="<?php echo esc_attr((string) $index); ?>">
					<div class="text-3xl">
						<?php 
							$icon = $card['icon'] ?? '';
							$icon_url = is_array($icon) ? ($icon['url'] ?? $icon['src'] ?? '') : $icon;
							if (!empty($icon_url) && (preg_match('/^(https?:|\/\/|\/|data:image\/)/i', $icon_url))) {
								echo '<img src="' . esc_url($icon_url) . '" alt="" class="h-10 w-10 object-contain invert-[.7] opacity-80">';
							} else {
								echo esc_html($icon_url);
							}
						?>
					</div>
					<div class="mt-5 font-mono text-sm uppercase tracking-[0.08em] text-[var(--reflection-muted)] group-[.active]:text-[var(--reflection-accent)]"><?php echo esc_html($card['eyebrow'] ?? ''); ?></div>
					<div class="mt-3 flex items-baseline gap-3 text-5xl font-bold text-[var(--reflection-heading)]"><span><?php echo esc_html($card['stat'] ?? ''); ?></span><span class="text-base font-normal text-[var(--reflection-muted)]"><?php echo esc_html($card['unit'] ?? ''); ?></span></div>
					<p class="mt-4 text-base leading-7 text-[var(--reflection-body)]"><?php echo esc_html($card['summary'] ?? ''); ?></p>
					<div class="rd-card-detail mt-5 hidden group-[.active]:block border-t border-[var(--reflection-heading)]/20 pt-5 text-sm leading-7 text-[var(--reflection-body)]">
						<?php if (! empty($card['toggle'])) : ?>
							<div class="mb-4 flex gap-3 font-mono text-xs uppercase tracking-[0.08em]">
								<button type="button" class="rd-toggle-btn border border-[var(--reflection-heading)]/20 px-3 py-2 text-[var(--reflection-heading)] transition" data-toggle-solution><?php echo esc_html($card['toggle']); ?></button>
							</div>
						<?php endif; ?>
						<div class="rd-view-problem">
							<p><?php echo esc_html($card['detail'] ?? ''); ?></p>
							<?php if (! empty($card['bars'])) : ?>
								<div class="mt-5 grid gap-3">
									<?php foreach ($card['bars'] as $bar) : ?>
										<div class="flex items-center gap-3 text-xs">
											<div class="w-20 shrink-0 text-[var(--reflection-body)]"><?php echo esc_html($bar['label']); ?></div>
											<div class="h-2 flex-1 rounded-full relative overflow-hidden">
												<div class="absolute inset-0 rd-bar-track"></div>
												<div class="h-2 rounded-full relative z-10 <?php echo ! empty($bar['alert']) ? 'rd-bar-fill-alert' : 'rd-bar-fill'; ?>" style="width: <?php echo esc_attr($bar['width']); ?>;"></div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
						<?php if (! empty($card['solution'])) : ?><div class="rd-view-solution hidden">
								<h4 class="mb-2 font-semibold reci-reflection-accent">Path Forward</h4>
								<p><?php echo esc_html($card['solution']); ?></p>
							</div><?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="mt-12 text-center">
			<?php if (!empty($args['footer_text'])) : ?>
				<p class="mb-5 text-base reci-reflection-muted"><?php echo esc_html($args['footer_text']); ?></p>
			<?php endif; ?>
			<?php if ($transition_mode === 'button' && (!empty($args['continue_label']) || !empty($continue_target))) : ?>
				<?php 
					$continue_label = !empty($args['continue_label']) ? $args['continue_label'] : 'Continue';
				?>
				<button type="button" class="inline-flex items-center justify-center border border-[var(--reflection-heading)] px-8 py-3 font-mono text-sm uppercase tracking-[0.12em] text-[var(--reflection-heading)] transition hover:bg-[var(--reflection-heading)] hover:text-[var(--reflection-bg)]" data-stage-target="<?php echo esc_attr($continue_target); ?>">
					<?php echo esc_html($continue_label); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</section>