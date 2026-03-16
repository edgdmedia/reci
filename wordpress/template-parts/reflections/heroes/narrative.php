<?php
/**
 * Reflection hero variant: narrative.
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
	<div class="absolute inset-0 bg-cover bg-center grayscale" style="background-image: linear-gradient(180deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.88) 82%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
	<div class="relative z-10 flex min-h-screen items-center px-5 py-20 sm:px-6 lg:px-12 xl:px-20">
		<div class="max-w-[56rem]">
			<?php if ($args['eyebrow']) : ?>
				<div class="font-['Oswald'] text-sm uppercase tracking-[0.18em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
			<?php endif; ?>
			<?php if ($args['title']) : ?>
				<h1 class="mt-4 font-['Playfair_Display'] text-5xl font-semibold leading-[0.92] text-[var(--reflection-text)] sm:text-7xl lg:text-[6.5rem]"><?php echo esc_html($args['title']); ?></h1>
			<?php endif; ?>
			<?php if ($args['subtitle']) : ?>
				<p class="mt-6 max-w-[38rem] text-xl leading-8 text-[var(--reflection-soft-text)] sm:text-2xl sm:leading-10"><?php echo esc_html($args['subtitle']); ?></p>
			<?php endif; ?>
			<?php if ($args['body']) : ?>
				<p class="mt-5 max-w-[42rem] text-base leading-8 text-[var(--reflection-muted)] sm:text-lg sm:leading-9"><?php echo esc_html($args['body']); ?></p>
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
						<a class="inline-flex items-center justify-center rounded-full px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.12em] no-underline <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Continue'); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ($args['caption']) : ?>
				<p class="mt-10 max-w-[46rem] border-l border-[color:var(--reflection-border)] pl-4 text-sm leading-7 text-[var(--reflection-muted)]"><?php echo esc_html($args['caption']); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
