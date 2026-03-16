<?php
/**
 * Voices of Resistance progressive text stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-hold',
	'title' => 'The Decision',
	'paragraphs' => [],
	'prompt' => 'Click to Reveal',
	'button_label' => '▼',
	'continue_label' => 'Face the Moment →',
]);
?>
<section class="chapter-hold" id="<?php echo esc_attr($args['id']); ?>">
	<h2 class="hold-title"><?php echo esc_html($args['title']); ?></h2>
	<div class="hold-text-container" id="holdTextTarget">
		<?php foreach ((array) $args['paragraphs'] as $paragraph) : ?>
			<p class="hold-paragraph"><?php echo esc_html($paragraph); ?></p>
		<?php endforeach; ?>
	</div>
	<div class="hold-controls">
		<div class="hold-prompt"><?php echo esc_html($args['prompt']); ?></div>
		<button class="hold-btn" id="holdRevealBtn" type="button"><?php echo esc_html($args['button_label']); ?></button>
	</div>
	<button id="toThresholdBtn" class="enter-btn" type="button" data-stage-target="threshold" style="display:none; margin-top:50px;"><?php echo esc_html($args['continue_label']); ?></button>
</section>
