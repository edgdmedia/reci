<?php
/**
 * Reflection chapter origins variant: documentary.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'origins',
	'eyebrow' => '',
	'title' => '',
	'intro_paragraphs' => [],
	'feature_title' => '',
	'feature_paragraphs' => [],
	'documents' => [],
	'continue_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>">
	<div class="reci-stage-shell">
		<div class="reci-stage-body">
			<div class="reci-stage-grid lg:grid-cols-[minmax(0,0.95fr)_minmax(320px,0.85fr)] lg:items-start">
				<div class="pr-0 lg:pr-8">
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
					<h2 class="mt-3 font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4.5rem]"><?php echo esc_html($args['title']); ?></h2>
					<?php foreach ((array) $args['intro_paragraphs'] as $paragraph) : ?>
						<p class="mt-4 max-w-[42rem] text-lg leading-9 text-[var(--reflection-soft-text)]"><?php echo esc_html($paragraph); ?></p>
					<?php endforeach; ?>
					<div class="mt-8 flex flex-wrap gap-4">
						<button class="reci-continue" type="button" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['continue_label']); ?></button>
					</div>
				</div>
				<div class="reci-scroll-panel rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
					<article>
						<h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($args['feature_title']); ?></h3>
						<?php foreach ((array) $args['feature_paragraphs'] as $paragraph) : ?>
							<p class="mt-4 text-base leading-8 text-[var(--reflection-soft-text)]"><?php echo esc_html($paragraph); ?></p>
						<?php endforeach; ?>
					</article>
					<div class="mt-6 grid gap-4">
						<?php foreach ((array) $args['documents'] as $document) : ?>
							<article class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)] p-5">
								<h3 class="mb-2 font-['Playfair_Display'] text-2xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($document['title'] ?? ''); ?></h3>
								<p class="text-sm leading-7 text-[var(--reflection-soft-text)]"><?php echo esc_html($document['body'] ?? ''); ?></p>
								<?php if (! empty($document['links'])) : ?>
									<?php foreach ($document['links'] as $link) : ?>
										<p class="mt-3"><a class="font-semibold text-[var(--reflection-accent)] no-underline" href="<?php echo esc_url($link['href']); ?>" target="_blank" rel="noopener"><?php echo esc_html($link['label']); ?></a></p>
									<?php endforeach; ?>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
