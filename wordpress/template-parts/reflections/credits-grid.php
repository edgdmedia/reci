<?php
/**
 * Reflection credits grid.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'id' => '',
		'eyebrow' => '',
		'title' => '',
		'intro' => '',
		'items' => [],
	]
);
?>
<section class="px-5 py-14 sm:px-6 lg:px-10 lg:py-24" id="<?php echo esc_attr($args['id']); ?>">
	<div class="mx-auto w-full max-w-[1260px]">
		<div class="grid gap-4">
			<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
			<h2 class="font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4rem]"><?php echo esc_html($args['title']); ?></h2>
			<p class="max-w-[74rem] text-base leading-8 text-[var(--reflection-soft-text)] sm:text-[1.05rem] sm:leading-9"><?php echo esc_html($args['intro']); ?></p>
		</div>
		<div class="mt-8 grid gap-4 lg:grid-cols-2">
			<?php foreach ($args['items'] as $item) : ?>
				<article class="rounded-3xl border border-[color:var(--reflection-border-soft)] bg-gradient-to-b from-[var(--reflection-card-strong)] to-[var(--reflection-card)] p-6">
					<h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold text-[var(--reflection-text)]"><?php echo esc_html($item['title']); ?></h3>
					<?php foreach ((array) ($item['paragraphs'] ?? []) as $paragraph) : ?>
						<p class="<?php echo $paragraph !== (($item['paragraphs'] ?? [])[0] ?? null) ? 'mt-4 ' : ''; ?>text-base leading-8 text-[var(--reflection-soft-text)]"><?php echo wp_kses_post($paragraph); ?></p>
					<?php endforeach; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
