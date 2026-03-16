<?php
/**
 * Reflection panel grid.
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
		'section_class' => 'bg-[var(--reflection-surface-alt)]',
	]
);
?>
<section class="<?php echo esc_attr($args['section_class']); ?> px-5 py-14 sm:px-6 lg:px-10 lg:py-24" id="<?php echo esc_attr($args['id']); ?>">
	<div class="mx-auto w-full max-w-[1260px]">
		<div class="grid gap-4">
			<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
			<h2 class="font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-5xl lg:text-[4rem]"><?php echo esc_html($args['title']); ?></h2>
			<p class="max-w-[74rem] text-base leading-8 text-[var(--reflection-soft-text)] sm:text-[1.05rem] sm:leading-9"><?php echo esc_html($args['intro']); ?></p>
		</div>
		<div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<?php foreach ($args['items'] as $item) : ?>
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
</section>
