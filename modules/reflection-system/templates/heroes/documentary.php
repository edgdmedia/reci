<?php
/**
 * Reflection hero variant: documentary.
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
			<div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(180deg, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(($args['overlay_opacity'] ?? 0.72) * 0.86, 2)); ?>) 0%, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format($args['overlay_opacity'] ?? 0.72, 2)); ?>) 72%, rgba(<?php echo esc_attr($args['overlay_rgb'] ?? '0,0,0'); ?>,<?php echo esc_attr(number_format(min(1, ($args['overlay_opacity'] ?? 0.72) * 1.1), 2)); ?>) 100%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
		<?php endif; ?>
		<div class="relative z-10 flex min-h-screen flex-col <?php echo esc_attr($args['align_v_class']); ?> gap-4 px-5 pb-10 pt-24 sm:px-6 lg:px-20 lg:pb-16 <?php echo esc_attr($args['align_h_class']); ?> <?php echo esc_attr($args['align_text_class']); ?>">
			<?php if ($args['eyebrow']) : ?>
				<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
			<?php endif; ?>
			<?php if ($args['title']) : ?>
				<h1 class="max-w-[10ch] font-['Playfair_Display'] text-5xl font-semibold leading-[0.95] reci-reflection-text [text-shadow:0_8px_28px_rgba(0,0,0,0.7)] sm:text-7xl lg:text-[7rem]"><?php echo esc_html($args['title']); ?></h1>
			<?php endif; ?>
			<?php if ($args['subtitle']) : ?>
				<div class="max-w-[68rem] text-xl leading-8 reci-reflection-text [text-shadow:0_4px_18px_rgba(0,0,0,0.72)] sm:text-2xl"><?php echo esc_html($args['subtitle']); ?></div>
			<?php endif; ?>
			<?php if ($args['body']) : ?>
				<p class="max-w-[68rem] text-lg leading-8 reci-reflection-text [text-shadow:0_4px_16px_rgba(0,0,0,0.72)]"><?php echo esc_html($args['body']); ?></p>
			<?php endif; ?>
			<?php if (!empty($args['caption'])) : ?>
				<p class="max-w-[58rem] text-sm leading-7 reci-reflection-soft-text [text-shadow:0_3px_12px_rgba(0,0,0,0.72)]"><?php echo esc_html($args['caption']); ?></p>
			<?php endif; ?>
			<?php if (! empty($args['actions']) && ($args['transition_mode'] ?? 'button') === 'button') : ?>
				<div class="mt-2 flex flex-wrap gap-4">
					<?php foreach ($args['actions'] as $action) : ?>
						<?php
						$href = (string) ($action['href'] ?? '#');
						$stage_target = '';
						if ($href !== '' && $href[0] === '#') {
							$stage_target = ltrim($href, '#');
						}
						$action_attributes = '';
						foreach ((array) ($action['attributes'] ?? []) as $attr_key => $attr_value) {
							$action_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
						}
						if ($stage_target !== '') {
							$action_attributes .= sprintf(' data-stage-target="%s"', esc_attr($stage_target));
						}
						?>
						<a class="inline-flex items-center justify-center rounded-full border border-current/30 px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.1em] no-underline reci-reflection-text <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($href); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Explore'); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
