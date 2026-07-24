<?php
/**
 * Reflection hero variant: Protest March Dark (Context slide).
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
	]
);

$section_attributes = '';
foreach ((array) $args['section_attributes'] as $attr_key => $attr_value) {
	$section_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
}
?>
<section class="reci-stage bg-[#111] <?php echo esc_attr(str_replace('reci-stage', '', $args['section_class'])); ?>" id="<?php echo esc_attr($args['id']); ?>"<?php echo $section_attributes; ?>>
	<div class="relative flex min-h-screen w-full flex-col items-center justify-center overflow-hidden px-6 text-center">
		<?php if (! empty($args['use_background_image']) && ! empty($args['background_image'])) : ?>
			<div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo esc_url($args['background_image']); ?>');"></div>
			<div class="absolute inset-0" style="background: rgba(<?php echo esc_attr($args['overlay_rgb']); ?>, <?php echo esc_attr($args['overlay_opacity']); ?>);"></div>
		<?php endif; ?>
		<div class="relative z-10 w-full max-w-[900px] px-0 sm:px-7">
			<?php if ($args['eyebrow']) : ?>
				<div class="font-['Oswald'] text-3xl uppercase tracking-[0.12em] reci-reflection-accent"><?php echo esc_html($args['eyebrow']); ?></div>
			<?php endif; ?>
			<?php if ($args['title']) : ?>
				<h2 class="mb-6 font-['Oswald'] text-[clamp(2.4rem,4vw,3.5rem)] uppercase tracking-[0.04em] reci-reflection-accent"><?php echo esc_html($args['title']); ?></h2>
			<?php endif; ?>
			<?php if ($args['subtitle']) : ?>
				<div class="mx-auto mb-11 max-w-[760px] text-[1.15rem] leading-[1.85] text-[rgba(255,255,255,0.9)]"><?php echo wp_kses_post(nl2br($args['subtitle'])); ?></div>
			<?php endif; ?>
			<?php if ($args['body']) : ?>
				<p class="mx-auto mb-11 max-w-[760px] text-[1.15rem] leading-[1.85] text-[rgba(255,255,255,0.9)]"><?php echo wp_kses_post(nl2br($args['body'])); ?></p>
			<?php endif; ?>
			<?php if (! empty($args['actions']) && ($args['transition_mode'] ?? 'button') === 'button') : ?>
				<div class="mt-2 flex flex-wrap justify-center gap-4">
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
						<a class="inline-flex items-center justify-center border-2 border-[rgba(255,255,255,0.82)] bg-transparent px-[50px] py-[20px] font-['Oswald'] text-[1.2rem] uppercase tracking-[2px] text-white no-underline transition-transform hover:scale-105 <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($href); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Begin'); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
