<?php
/**
 * Reflection hero variant: Voices of Resistance.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-intro',
	'eyebrow' => '',
	'title' => 'Voices of Resistance',
	'title_accent' => '',
	'subtitle' => '"I remember the day we decided enough was enough..."',
	'body' => '',
	'caption' => '',
	'use_background_image' => '0',
	'background_image' => '',
	'overlay_opacity' => 0.72,
	'overlay_color' => '#000000',
	'section_class' => '',
	'section_attributes' => [],
	'actions' => [],
	'align_h_class' => 'items-center',
	'align_v_class' => 'justify-center',
	'align_text_class' => 'text-center',
]);

$section_attributes = '';
foreach ((array) $args['section_attributes'] as $attr_key => $attr_value) {
	$section_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
}
?>
<section class="chapter-intro <?php echo esc_attr($args['section_class']); ?>" id="<?php echo esc_attr($args['id']); ?>"<?php echo $section_attributes; ?>>
	<?php if (! empty($args['use_background_image']) && ! empty($args['background_image'])) : ?>
		<div class="intro-bg" style="background-color:<?php echo esc_attr($args['overlay_color'] ?? '#000'); ?>;">
			<div style="position:absolute; inset:0; background-image:url('<?php echo esc_url($args['background_image']); ?>'); background-size:cover; background-position:center; opacity:<?php echo esc_attr(1 - ($args['overlay_opacity'] ?? 0)); ?>;"></div>
		</div>
	<?php endif; ?>
	
	<div class="intro-content <?php echo esc_attr($args['align_text_class']); ?>">
		<?php if (! empty($args['foreground_image'])) : ?>
			<img src="<?php echo esc_url($args['foreground_image']); ?>" alt="" class="mx-auto mb-6 max-h-[200px] w-auto" />
		<?php endif; ?>
		
		<?php if ($args['eyebrow']) : ?>
			<div class="font-['Oswald'] text-sm uppercase tracking-[0.14em] reci-reflection-accent mb-4"><?php echo esc_html($args['eyebrow']); ?></div>
		<?php endif; ?>
		
		<?php if ($args['title']) : ?>
			<h1 class="intro-title"><?php echo esc_html($args['title']); ?><?php if (! empty($args['title_accent'])) : ?><br><span style="color: var(--reflection-accent);"><?php echo esc_html($args['title_accent']); ?></span><?php endif; ?></h1>
		<?php endif; ?>
		
		<?php if ($args['subtitle']) : ?>
			<p class="intro-subtitle"><?php echo esc_html($args['subtitle']); ?></p>
		<?php endif; ?>
		
		<?php if ($args['body']) : ?>
			<p class="mt-5 text-base leading-8 reci-reflection-text sm:text-lg sm:leading-9 max-w-[42rem] mx-auto"><?php echo esc_html($args['body']); ?></p>
		<?php endif; ?>
		
		<?php if ($args['caption']) : ?>
			<p class="mt-6 text-sm uppercase tracking-[0.08em] reci-reflection-muted"><?php echo esc_html($args['caption']); ?></p>
		<?php endif; ?>
		
		<?php if (! empty($args['actions']) && ($args['transition_mode'] ?? 'button') === 'button') : ?>
			<div class="mt-8 flex flex-wrap justify-center gap-4">
				<?php foreach ($args['actions'] as $action) : ?>
					<?php
					$action_attributes = '';
					foreach ((array) ($action['attributes'] ?? []) as $attr_key => $attr_value) {
						$action_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
					}
					?>
					<a class="enter-btn reci-reflection-text border border-current/30 <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Enter the Story'); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
