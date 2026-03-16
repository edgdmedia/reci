<?php
/**
 * Voices of Resistance reflection stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-reflect',
	'prompt' => '',
	'button_label' => 'Complete Journey',
	'button_href' => '#',
]);
?>
<section class="chapter-reflection" id="<?php echo esc_attr($args['id']); ?>">
	<h2 class="reflect-prompt"><?php echo esc_html($args['prompt']); ?></h2>
	<textarea class="reflect-input" placeholder="Share your thoughts..."></textarea>
	<button class="enter-btn" style="margin-top:30px;" type="button" id="vorCompleteBtn" data-complete-href="<?php echo esc_url($args['button_href']); ?>"><?php echo esc_html($args['button_label']); ?></button>
</section>
