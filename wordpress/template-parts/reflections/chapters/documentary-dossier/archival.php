<?php
/**
 * Reflection chapter documentary dossier variant: archival.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'wh-origins',
	'eyebrow' => '',
	'title' => '',
	'intro' => [],
	'sections' => [],
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body">
			<div class="reci-stage-grid lg:grid-cols-[minmax(0,0.9fr)_minmax(360px,1.1fr)] lg:items-start">
				<div class="flex max-h-[70vh] flex-col pr-0 lg:pr-8">
					<div class="reci-scroll-panel pr-2">
						<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
						<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4.25rem]"><?php echo esc_html($args['title']); ?></h2>
						<?php foreach ((array) $args['intro'] as $paragraph) : ?>
							<p class="mt-4 max-w-[42rem] text-base leading-8 text-[var(--reflection-soft-text)] sm:text-lg sm:leading-9"><?php echo esc_html($paragraph); ?></p>
						<?php endforeach; ?>
					</div>
					<div class="mt-6 flex flex-wrap gap-4">
						<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
					</div>
				</div>
				<div class="reci-scroll-panel max-h-[70vh] rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
					<div class="grid gap-4">
						<?php foreach ((array) $args['sections'] as $section) : ?>
							<article class="rounded-[1.75rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-panel)] p-5">
								<h3 class="mb-3 font-['Playfair_Display'] text-2xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($section['title'] ?? ''); ?></h3>
								<?php foreach ((array) ($section['paragraphs'] ?? []) as $paragraph) : ?>
									<p class="mt-4 text-sm leading-7 text-[var(--reflection-soft-text)] sm:text-base sm:leading-8"><?php echo esc_html($paragraph); ?></p>
								<?php endforeach; ?>
								<?php if (! empty($section['links'])) : ?>
									<div class="mt-4 grid gap-3">
										<?php foreach ($section['links'] as $link) : ?>
											<a class="font-semibold text-[var(--reflection-accent)] no-underline" href="<?php echo esc_url($link['href']); ?>" target="_blank" rel="noopener"><?php echo esc_html($link['label']); ?></a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
