<?php
/**
 * Reflection timeline.
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
			<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
			<h2 class="font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-5xl lg:text-[4rem]"><?php echo esc_html($args['title']); ?></h2>
			<p class="max-w-[74rem] text-base leading-8 reci-reflection-soft-text sm:text-[1.05rem] sm:leading-9"><?php echo esc_html($args['intro']); ?></p>
		</div>
		<div class="relative mt-8 grid gap-4 before:absolute before:bottom-2 before:left-[0.55rem] before:top-2 before:w-[2px] before:bg-[color:var(--reflection-muted)] before:content-['']">
			<?php foreach ($args['items'] as $item) : ?>
				<article class="relative ml-8 rounded-3xl border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface)] p-6 before:absolute before:-left-[1.7rem] before:top-6 before:h-[14px] before:w-[14px] before:rounded-full before:bg-[var(--reflection-accent)] before:shadow-[0_0_0_6px_rgba(197,198,199,0.18)] before:content-['']">
					<h3 class="mb-3 font-['Playfair_Display'] text-3xl font-semibold reci-reflection-text"><?php echo esc_html($item['title'] ?? ''); ?></h3>
					<p class="text-base leading-8 reci-reflection-soft-text"><?php echo esc_html($item['body'] ?? ''); ?></p>
					<?php if (! empty($item['media'])) : ?>
						<div class="mt-4 grid gap-4 <?php echo count($item['media']) > 1 ? 'lg:grid-cols-2' : 'lg:grid-cols-1'; ?>">
							<?php foreach ($item['media'] as $media) : ?>
								<figure class="overflow-hidden rounded-[18px] bg-[var(--reflection-bg)]">
									<img class="block h-[260px] w-full object-cover" src="<?php echo esc_url($media['src']); ?>" alt="<?php echo esc_attr($media['alt']); ?>">
									<figcaption class="px-4 pb-4 pt-3 text-sm reci-reflection-soft-text"><?php echo esc_html($media['caption']); ?></figcaption>
								</figure>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if (! empty($item['link'])) : ?>
						<p class="mt-4"><a class="font-semibold reci-reflection-accent no-underline" href="<?php echo esc_url($item['link']['href']); ?>" target="_blank" rel="noopener"><?php echo esc_html($item['link']['label']); ?></a></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
