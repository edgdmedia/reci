<?php
/**
 * Reflection chapter threshold message variant: threshold.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 'threshold',
	'title' => '',
	'body' => '',
	'continue_label' => '',
	'button_label' => '',
	'continue_target' => '#',
]);
$button_text = $args['continue_label'] !== '' ? $args['continue_label'] : ($args['button_label'] !== '' ? $args['button_label'] : 'Continue');
?>
<?php
$transition_mode = $args['transition_mode'] ?? 'button';
$continue_target = ltrim($args['continue_target'] ?? '', '#');
?>
<section class="reci-stage" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($transition_mode); ?>" data-continue-target="<?php echo esc_attr($continue_target); ?>">
	<div class="relative flex min-h-screen w-full items-center justify-center bg-[var(--reflection-bg)] px-5 py-16 text-center">
		<div class="absolute inset-0 bg-[var(--reflection-primary)] opacity-5"></div>
		<div class="relative z-10 max-w-[800px]">
			<h2 class="font-['Oswald'] text-4xl uppercase tracking-[0.25em] transition-[color] duration-[2000ms] sm:text-5xl lg:text-[3rem]" style="color: var(--reflection-heading);"><?php echo esc_html($args['title']); ?></h2>
			<?php if (! empty($args['body'])) : ?>
				<p class="mt-6 text-lg leading-8 opacity-80" style="color: var(--reflection-text);"><?php echo esc_html($args['body']); ?></p>
			<?php endif; ?>
			<?php if ($transition_mode === 'button') : ?>
			<button type="button" class="mt-8 border px-10 py-4 font-['Oswald'] text-sm uppercase tracking-[0.14em] transition-colors" style="border-color: var(--reflection-border); color: var(--reflection-text); background-color: transparent;" onmouseover="this.style.backgroundColor='var(--reflection-heading)'; this.style.color='var(--reflection-bg)';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--reflection-text)';" data-stage-target="<?php echo esc_attr($continue_target); ?>"><?php echo esc_html($button_text); ?></button>
			<?php endif; ?>
		</div>
	</div>
</section>
