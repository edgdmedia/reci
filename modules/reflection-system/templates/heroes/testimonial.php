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
		'use_background_image' => '0',
		'background_image' => '',
		'overlay_opacity' => 0.72,
		'overlay_color' => '#000000',
		'overlay_rgb' => '0,0,0',
		'actions' => [],
		'section_class' => '',
		'section_attributes' => [],
		'align_h_class' => 'items-start',
		'align_v_class' => 'justify-end',
		'align_text_class' => 'text-left',
		'align_text_class' => 'text-left',
	]
);

$section_attributes = '';
foreach ((array) $args['section_attributes'] as $attr_key => $attr_value) {
	$section_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
}
?>
<section class="reci-stage <?php echo esc_attr(str_replace('reci-stage', '', $args['section_class'])); ?>" id="<?php echo esc_attr($args['id']); ?>"<?php echo $section_attributes; ?>>
	<div class="relative min-h-screen w-full overflow-hidden">
		<?php if (! empty($args['use_background_image']) && ! empty($args['background_image'])) : ?>
			<div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(90deg, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(($args['overlay_opacity'] ?? 0.72) * 1.28, 2)); ?>) 0%, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(($args['overlay_opacity'] ?? 0.72) * 0.81, 2)); ?>) 45%, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(($args['overlay_opacity'] ?? 0.72) * 0.49, 2)); ?>) 100%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
		<?php endif; ?>
		<div class="relative z-10 flex min-h-screen <?php echo esc_attr($args['align_v_class']); ?> px-5 pb-10 pt-24 sm:px-6 lg:px-12 xl:px-20 <?php echo esc_attr($args['align_h_class']); ?>">
			<div class="max-w-[48rem] rounded-[2rem] border border-[color:var(--reflection-border-soft)] bg-[var(--reflection-surface,var(--reflection-card))] p-6 backdrop-blur-md sm:p-8 lg:p-10 <?php echo esc_attr($args['align_text_class']); ?>" style="--reflection-body: var(--reflection-surface-text, var(--reflection-body)); --reflection-soft-text: var(--reflection-surface-text, var(--reflection-soft-text)); --reflection-muted: var(--reflection-surface-text, var(--reflection-muted));">
				<?php if ($args['eyebrow']) : ?>
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.14em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
				<?php endif; ?>
				<?php if ($args['title']) : ?>
					<h1 class="mt-4 font-['Playfair_Display'] text-4xl font-semibold leading-tight reci-reflection-text sm:text-6xl lg:text-[5rem]"><?php echo esc_html($args['title']); ?></h1>
				<?php endif; ?>
				<?php if ($args['subtitle']) : ?>
					<p class="mt-5 max-w-[34rem] text-lg italic leading-8 reci-reflection-soft-text sm:text-xl"><?php echo esc_html($args['subtitle']); ?></p>
				<?php endif; ?>
				<?php if ($args['body']) : ?>
					<p class="mt-5 text-base leading-8 reci-reflection-text sm:text-lg sm:leading-9"><?php echo esc_html($args['body']); ?></p>
				<?php endif; ?>
				<?php if ($args['caption']) : ?>
					<p class="mt-6 text-sm uppercase tracking-[0.08em] reci-reflection-muted"><?php echo esc_html($args['caption']); ?></p>
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
							<a class="inline-flex items-center justify-center rounded-full border border-current/30 px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.12em] no-underline reci-reflection-text <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Continue'); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
