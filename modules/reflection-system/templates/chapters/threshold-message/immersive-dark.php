<?php
/**
 * Threshold stage – immersive dark variant.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-threshold',
	'title' => '',
	'message' => '',
	'body' => '',
	'continue_label' => '',
	'button_label' => '',
	'continue_target' => '',
]);
$title = $args['title'] !== '' ? $args['title'] : ($args['message'] ?? '');
$continue_target = $args['continue_target'] ?? '';
$button_text = $args['continue_label'] !== '' ? $args['continue_label'] : ($args['button_label'] !== '' ? $args['button_label'] : 'Continue');
?>
<?php
$scope_class = 'rd-scope-' . uniqid();
$transition_mode = $args['transition_mode'] ?? 'button';
$continue_target = ltrim($args['continue_target'] ?? '', '#');
?>
<section class="reci-stage chapter-threshold <?php echo esc_attr($scope_class); ?>" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($transition_mode); ?>" data-continue-target="<?php echo esc_attr($continue_target); ?>">
	<style>
		.<?php echo esc_attr($scope_class); ?> .thresh-text {
			color: var(--reflection-muted, #333);
		}
		.<?php echo esc_attr($scope_class); ?>.active .thresh-text {
			color: var(--reflection-heading, white);
		}
		.<?php echo esc_attr($scope_class); ?> p.reci-reflection-text {
			color: var(--reflection-text, white);
		}
		.<?php echo esc_attr($scope_class); ?> .threshold-btn {
			border-color: var(--reflection-border, rgba(255, 255, 255, 0.5));
			color: var(--reflection-text, white);
		}
		.<?php echo esc_attr($scope_class); ?> .threshold-btn:hover {
			background-color: var(--reflection-heading, white);
			color: var(--reflection-bg, black);
		}
	</style>
	<div class="threshold-content">
		<h2 class="thresh-text"><?php echo esc_html($title); ?></h2>
		<?php if (! empty($args['body'])) : ?>
			<p class="mt-6 max-w-[620px] mx-auto text-lg leading-8 reci-reflection-text opacity-80"><?php echo esc_html($args['body']); ?></p>
		<?php endif; ?>
		<br>
		<?php if ($button_text !== '' && $continue_target !== '' && $transition_mode === 'button') : ?>
			<button class="enter-btn threshold-btn" type="button" data-stage-target="<?php echo esc_attr($continue_target); ?>"><?php echo esc_html($button_text); ?></button>
		<?php elseif ($button_text !== '' && $transition_mode === 'button') : ?>
			<button class="enter-btn threshold-btn" type="button"><?php echo esc_html($button_text); ?></button>
		<?php endif; ?>
	</div>
	<div class="threshold-overlay"></div>
</section>
