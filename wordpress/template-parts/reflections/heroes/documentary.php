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
	<div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(180deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.9) 85%), url('<?php echo esc_url($args['background_image']); ?>');"></div>
	<div class="relative z-10 flex min-h-screen flex-col justify-end gap-4 px-5 pb-10 pt-24 sm:px-6 lg:px-20 lg:pb-16">
		<?php if ($args['eyebrow']) : ?>
			<div class="font-['Oswald'] text-sm uppercase tracking-[0.12em] text-[var(--reflection-accent)]"><?php echo esc_html($args['eyebrow']); ?></div>
		<?php endif; ?>
		<?php if ($args['title']) : ?>
			<h1 class="max-w-[10ch] font-['Playfair_Display'] text-5xl font-semibold leading-[0.95] text-[var(--reflection-text)] sm:text-7xl lg:text-[7rem]"><?php echo esc_html($args['title']); ?></h1>
		<?php endif; ?>
		<?php if ($args['subtitle']) : ?>
			<div class="max-w-[68rem] text-xl leading-8 text-[var(--reflection-text)] sm:text-2xl"><?php echo esc_html($args['subtitle']); ?></div>
		<?php endif; ?>
		<?php if ($args['body']) : ?>
			<p class="max-w-[68rem] text-lg leading-8 text-[var(--reflection-text)]"><?php echo esc_html($args['body']); ?></p>
		<?php endif; ?>
		<?php if ($args['caption']) : ?>
			<p class="max-w-[58rem] text-sm leading-7 text-[var(--reflection-soft-text)]"><?php echo esc_html($args['caption']); ?></p>
		<?php endif; ?>
		<?php if (! empty($args['actions'])) : ?>
			<div class="mt-2 flex flex-wrap gap-4">
				<?php foreach ($args['actions'] as $action) : ?>
					<?php
					$action_attributes = '';
					foreach ((array) ($action['attributes'] ?? []) as $attr_key => $attr_value) {
						$action_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
					}
					?>
					<a class="inline-flex items-center justify-center rounded-full px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.1em] no-underline <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Explore'); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
