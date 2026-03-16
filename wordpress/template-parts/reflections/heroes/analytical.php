<?php
/**
 * Reflection hero variant: analytical.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'id' => 'top',
		'eyebrow' => '',
		'title' => '',
		'subtitle' => '',
		'body' => '',
		'caption' => '',
		'background_image' => '',
		'actions' => [],
		'section_class' => '',
		'section_attributes' => [],
	]
);

$section_attributes = '';
foreach ((array) $args['section_attributes'] as $attr_key => $attr_value) {
	$section_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
}
?>
<section class="relative min-h-screen overflow-hidden <?php echo esc_attr($args['section_class']); ?>" id="<?php echo esc_attr($args['id']); ?>"<?php echo $section_attributes; ?>>
	<div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.94)_0%,rgba(255,255,255,0.9)_42%,rgba(255,255,255,0.82)_100%),url('<?php echo esc_url($args['background_image']); ?>')] bg-cover bg-center"></div>
	<div class="relative z-10 flex min-h-screen items-center px-5 py-20 sm:px-6 lg:px-12 xl:px-20">
		<div class="grid w-full gap-10 lg:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)] lg:items-end">
			<div>
				<?php if ($args['eyebrow']) : ?>
					<div class="font-mono text-xs font-medium uppercase tracking-[0.22em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
				<?php endif; ?>
				<?php if ($args['title']) : ?>
					<h1 class="mt-4 max-w-[12ch] font-['Oswald'] text-5xl font-semibold uppercase leading-[0.95] text-[var(--reflection-accent-contrast)] sm:text-7xl lg:text-[6.75rem]"><?php echo esc_html($args['title']); ?></h1>
				<?php endif; ?>
				<?php if ($args['subtitle']) : ?>
					<p class="mt-6 max-w-[48rem] text-xl leading-8 text-[var(--reflection-accent-contrast)]/85 sm:text-2xl"><?php echo esc_html($args['subtitle']); ?></p>
				<?php endif; ?>
				<?php if ($args['body']) : ?>
					<p class="mt-5 max-w-[52rem] text-base leading-8 text-[var(--reflection-accent-contrast)]/70 sm:text-lg sm:leading-9"><?php echo esc_html($args['body']); ?></p>
				<?php endif; ?>
				<?php if ($args['actions']) : ?>
					<div class="mt-8 flex flex-wrap gap-4">
						<?php foreach ($args['actions'] as $action) : ?>
							<?php
							$action_attributes = '';
							foreach ((array) ($action['attributes'] ?? []) as $attr_key => $attr_value) {
								$action_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
							}
							?>
							<a class="inline-flex items-center justify-center rounded-full border px-6 py-4 font-mono text-xs font-medium uppercase tracking-[0.18em] no-underline <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Continue'); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="rounded-[2rem] border border-black/10 bg-white/55 p-6 shadow-[0_18px_50px_rgba(0,0,0,0.08)] backdrop-blur-sm">
				<div class="font-mono text-xs uppercase tracking-[0.18em] text-[var(--reflection-accent)]">Key framing</div>
				<p class="mt-4 text-sm leading-7 text-[var(--reflection-accent-contrast)]/75"><?php echo esc_html($args['caption'] ?: 'Use this variant when the opening needs to feel like a structured argument or data-led thesis rather than a cinematic scene.'); ?></p>
			</div>
		</div>
	</div>
</section>
