<?php
/**
 * Reflection hero variant: testimonial.
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
	<div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(90deg, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.58) 45%, rgba(0,0,0,0.35) 100%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
	<div class="relative z-10 flex min-h-screen items-end px-5 pb-10 pt-24 sm:px-6 lg:px-12 xl:px-20">
		<div class="max-w-[48rem] rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-card)] p-6 backdrop-blur-md sm:p-8 lg:p-10">
			<?php if ($args['eyebrow']) : ?>
				<div class="font-['Oswald'] text-sm uppercase tracking-[0.14em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
			<?php endif; ?>
			<?php if ($args['title']) : ?>
				<h1 class="mt-4 font-['Playfair_Display'] text-4xl font-semibold leading-tight text-[var(--reflection-text)] sm:text-6xl lg:text-[5rem]"><?php echo esc_html($args['title']); ?></h1>
			<?php endif; ?>
			<?php if ($args['subtitle']) : ?>
				<p class="mt-5 max-w-[34rem] text-lg italic leading-8 text-[var(--reflection-soft-text)] sm:text-xl"><?php echo esc_html($args['subtitle']); ?></p>
			<?php endif; ?>
			<?php if ($args['body']) : ?>
				<p class="mt-5 text-base leading-8 text-[var(--reflection-text)] sm:text-lg sm:leading-9"><?php echo esc_html($args['body']); ?></p>
			<?php endif; ?>
			<?php if ($args['caption']) : ?>
				<p class="mt-6 text-sm uppercase tracking-[0.08em] text-[var(--reflection-muted)]"><?php echo esc_html($args['caption']); ?></p>
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
		</div>
	</div>
</section>
