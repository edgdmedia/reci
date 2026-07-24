<?php
/**
 * Reflection chapter quote variant: immersive dark.
 *
 * Centered serif pull-quote with attribution. Reads inherited
 * --reflection-* color vars; supports an optional dimmed background image.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id'               => 'quote',
	'eyebrow'          => '',
	'quote'            => '',
	'attribution'      => '',
	'background_image' => '',
	'button_label'     => 'Continue',
	'continue_target'  => '#',
]);

$quote = is_array($args['quote'] ?? '') ? implode("\n\n", $args['quote']) : (string) ($args['quote'] ?? '');
$bg    = is_array($args['background_image'] ?? '')
	? ($args['background_image']['url'] ?? $args['background_image'][0] ?? '')
	: (string) ($args['background_image'] ?? '');
$transition_mode = $args['transition_mode'] ?? 'button';
$continue_target = ltrim((string) ($args['continue_target'] ?? ''), '#');

$h_align = $args['align_horizontal'] ?? 'center';
$v_align = $args['align_vertical'] ?? 'center';

// Map horizontal alignment to text alignment and flex item alignment
$text_align_class = $h_align === 'left' ? 'text-left items-start' : ($h_align === 'right' ? 'text-right items-end' : 'text-center items-center');

// Map vertical alignment to flex justify content for the wrapper
$justify_class = $v_align === 'top' ? '!justify-start' : ($v_align === 'bottom' ? '!justify-end' : '!justify-center');
?>
<section
	class="reci-stage chapter-quote"
	id="<?php echo esc_attr($args['id']); ?>"
	data-stage="<?php echo esc_attr($args['id']); ?>"
	data-transition-mode="<?php echo esc_attr($transition_mode); ?>"
	data-continue-target="<?php echo esc_attr($continue_target); ?>"
>
	<?php if ($bg !== '') : ?>
		<div class="quote-bg" style="background-image:url('<?php echo esc_url($bg); ?>');"></div>
	<?php endif; ?>
	<div class="quote-stage-wrap w-full <?php echo $justify_class; ?>">
		<div class="quote-inner w-full max-w-[1200px] mx-auto flex flex-col <?php echo $text_align_class; ?>">
			<?php if ($args['eyebrow'] !== '') : ?>
				<div class="quote-eyebrow"><?php echo esc_html($args['eyebrow']); ?></div>
			<?php endif; ?>
			<span class="quote-mark" aria-hidden="true">&ldquo;</span>
			<?php 
				$text_size_pref = $args['text_size'] ?? 'normal';
				if ($text_size_pref === 'large') {
					$dynamic_style = "text-align: {$h_align}; font-size: clamp(2rem, 5vw, 4rem); line-height: 1.2;";
				} elseif ($text_size_pref === 'small') {
					$dynamic_style = "text-align: {$h_align}; font-size: clamp(1.125rem, 2vw, 1.5rem); line-height: 1.6;";
				} else {
					// Normal
					$dynamic_style = "text-align: {$h_align}; font-size: clamp(1.5rem, 3.2vw, 2.4rem); line-height: 1.5;";
				}
			?>
			<blockquote class="quote-text" style="<?php echo esc_attr($dynamic_style); ?>"><?php echo esc_html($quote); ?></blockquote>
			<?php if ($args['attribution'] !== '') : ?>
				<cite class="quote-attribution">&mdash; <?php echo esc_html($args['attribution']); ?></cite>
			<?php endif; ?>
			<?php if ($transition_mode === 'button' && $continue_target !== '') : ?>
				<div class="quote-actions">
					<button type="button" class="enter-btn" data-stage-target="<?php echo esc_attr($continue_target); ?>"><?php echo esc_html($args['button_label']); ?></button>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
