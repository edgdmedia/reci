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
		'use_background_image' => '0',
		'background_image' => '',
		'overlay_opacity' => 0.72,
		'overlay_color' => '#ffffff',
		'overlay_rgb' => '255,255,255',
		'actions' => [],
		'section_class' => '',
		'section_attributes' => [],
		'align_h_class' => 'items-center',
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
	<div class="relative w-full overflow-hidden">
		<?php if (! empty($args['use_background_image']) && ! empty($args['background_image'])) : ?>
			<div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(135deg, rgba(<?php echo esc_attr($args['overlay_rgb']); ?>, <?php echo esc_attr(number_format(max(0, 0.94 - ($args['overlay_opacity'] - 0.72) * 0.4), 2)); ?>) 0%, rgba(<?php echo esc_attr($args['overlay_rgb']); ?>, <?php echo esc_attr(number_format(max(0, 0.9 - ($args['overlay_opacity'] - 0.72) * 0.4), 2)); ?>) 42%, rgba(<?php echo esc_attr($args['overlay_rgb']); ?>, <?php echo esc_attr(number_format(max(0, 0.82 - ($args['overlay_opacity'] - 0.72) * 0.4), 2)); ?>) 100%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
		<?php endif; ?>
		<div class="relative z-10 flex flex-col min-h-screen px-5 py-24 sm:px-6 lg:px-12 xl:px-20 <?php echo esc_attr($args['align_h_class']); ?> <?php echo esc_attr($args['align_v_class']); ?> <?php echo esc_attr($args['align_text_class']); ?>">
			<div class="w-full max-w-3xl">
				<?php if ($args['eyebrow']) : ?>
					<div class="font-mono text-xs font-medium uppercase tracking-[0.22em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
				<?php endif; ?>
				<?php if ($args['title']) : ?>
					<h1 class="mt-4 font-['Space_Grotesk'] text-5xl font-bold leading-none text-[var(--reflection-heading)] sm:text-[4rem]"><?php echo esc_html($args['title']); ?></h1>
				<?php endif; ?>
				<?php if ($args['body']) : ?>
					<p class="mx-auto mt-5 max-w-[600px] text-[1.2rem] text-[var(--reflection-body)] leading-normal"><?php echo esc_html($args['body']); ?></p>
				<?php endif; ?>
				<?php if ($args['actions']) : ?>
					<div class="mt-8 flex flex-wrap gap-4 <?php echo esc_attr(str_replace('items-', 'justify-', $args['align_h_class'])); ?>">
						<?php foreach ($args['actions'] as $action) : ?>
							<?php
							$action_attributes = '';
							foreach ((array) ($action['attributes'] ?? []) as $attr_key => $attr_value) {
								$action_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
							}
							?>
							<a class="inline-flex items-center justify-center border border-[var(--reflection-heading)] px-8 py-3 font-mono text-sm uppercase tracking-[0.12em] text-[var(--reflection-heading)] no-underline transition hover:bg-[var(--reflection-heading)] hover:text-[var(--reflection-bg)] <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Continue'); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
