<?php
/**
 * Voices of Resistance threshold stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-threshold',
	'title' => 'We stood at the edge of history.',
	'button_label' => 'March Forward',
]);
?>
<section class="chapter-threshold" id="<?php echo esc_attr($args['id']); ?>">
	<div class="threshold-content">
		<h2 class="thresh-text" id="vorThresholdText"><?php echo esc_html($args['title']); ?></h2>
		<br>
		<button class="enter-btn threshold-btn" id="vorThresholdBtn" type="button" data-stage-target="march"><?php echo esc_html($args['button_label']); ?></button>
	</div>
	<div class="threshold-overlay"></div>
</section>
