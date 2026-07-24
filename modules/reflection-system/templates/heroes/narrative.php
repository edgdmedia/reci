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
		'use_background_image' => '0',
		'background_image' => '',
		'overlay_opacity' => 0.72,
		'overlay_color' => '#000000',
		'overlay_rgb' => '0,0,0',
		'actions' => [],
		'section_class' => '',
		'section_attributes' => [],
		'align_h_class' => 'items-start',
		'align_v_class' => 'justify-center',
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
			<div class="absolute inset-0 bg-cover bg-center grayscale" style="background-image: linear-gradient(180deg, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(($args['overlay_opacity'] ?? 0.72) * 0.69, 2)); ?>) 0%, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(($args['overlay_opacity'] ?? 0.72) * 1.22, 2)); ?>) 82%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
		<?php endif; ?>
		<div class="relative z-10 flex min-h-screen <?php echo esc_attr($args['align_v_class']); ?> px-5 py-20 sm:px-6 lg:px-12 xl:px-20 <?php echo esc_attr($args['align_h_class']); ?>">
			<div class="max-w-[56rem] <?php echo esc_attr($args['align_text_class']); ?>">
				<?php if ($args['eyebrow']) : ?>
					<div class="font-['Oswald'] text-sm uppercase tracking-[0.18em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
				<?php endif; ?>
				<?php if ($args['title']) : ?>
					<h1 class="mt-4 font-['Playfair_Display'] text-5xl font-semibold leading-[0.92] reci-reflection-text sm:text-7xl lg:text-[6.5rem]"><?php echo esc_html($args['title']); ?></h1>
				<?php endif; ?>
				<?php if ($args['subtitle']) : ?>
					<p class="mt-6 max-w-[38rem] text-xl leading-8 reci-reflection-soft-text sm:text-2xl sm:leading-10"><?php echo esc_html($args['subtitle']); ?></p>
				<?php endif; ?>
				<?php if ($args['body']) : ?>
					<p class="mt-5 max-w-[42rem] text-base leading-8 reci-reflection-muted sm:text-lg sm:leading-9"><?php echo esc_html($args['body']); ?></p>
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
				<?php if ($args['caption']) : ?>
					<p class="mt-10 max-w-[46rem] border-l border-[color:var(--reflection-border)] pl-4 text-sm leading-7 reci-reflection-muted"><?php echo esc_html($args['caption']); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
