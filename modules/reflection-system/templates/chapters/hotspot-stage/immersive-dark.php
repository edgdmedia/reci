<?php
/**
 * Voices of Resistance hotspot stage.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-explore',
	'instruction' => 'Tap the Icons to Uncover the Story',
	'background_image' => '',
	'hotspots' => [],
	'continue_label' => 'Scroll Down to Continue ↓',
	'continue_target' => 'hold',
	'transition_mode' => 'button',
]);

$instruction = is_array($args['instruction'] ?? '') ? implode(' ', $args['instruction']) : (string) ($args['instruction'] ?? '');
$bg_image = is_array($args['background_image'] ?? '') ? ($args['background_image']['url'] ?? $args['background_image'][0] ?? '') : (string) ($args['background_image'] ?? '');
$continue_target = ltrim((string) ($args['continue_target'] ?? ''), '#');
$transition_mode = (string) ($args['transition_mode'] ?? 'button');
?>
<section class="reci-stage chapter-interact" id="<?php echo esc_attr($args['id']); ?>" data-stage="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($transition_mode); ?>" data-continue-target="<?php echo esc_attr($continue_target); ?>">
	<div class="instruction-overlay">
		<h3><?php echo esc_html($instruction); ?></h3>
	</div>
	<div class="interact-stage" id="exploreStage" style="background-image:url('<?php echo esc_url($bg_image); ?>')">
		<?php foreach ((array) $args['hotspots'] as $hotspot) : 
			$top = is_array($hotspot['top'] ?? '') ? ($hotspot['top'][0] ?? '50%') : (string) ($hotspot['top'] ?? '50%');
			$left = is_array($hotspot['left'] ?? '') ? ($hotspot['left'][0] ?? '50%') : (string) ($hotspot['left'] ?? '50%');
			$key = is_array($hotspot['key'] ?? '') ? ($hotspot['key'][0] ?? '') : (string) ($hotspot['key'] ?? '');
			$title = is_array($hotspot['title'] ?? '') ? implode(' ', $hotspot['title']) : (string) ($hotspot['title'] ?? '');
			$body = is_array($hotspot['body'] ?? '') ? implode("\n\n", $hotspot['body']) : (string) ($hotspot['body'] ?? '');
		?>
			<div class="hotspot" style="top: <?php echo esc_attr($top); ?>; left: <?php echo esc_attr($left); ?>;" data-detail-key="<?php echo esc_attr($key); ?>" data-title="<?php echo esc_attr($title); ?>" data-text="<?php echo esc_attr($body); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
					<circle cx="12" cy="12" r="10" />
					<line x1="12" y1="8" x2="12" y2="16" />
					<line x1="8" y1="12" x2="16" y2="12" />
				</svg>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="hotspot-detail-panel" id="detailPanel">
		<h4 class="detail-title" id="dTitle">Title</h4>
		<p class="detail-text" id="dText">Text</p>
		<button type="button" id="detailClose" class="detail-close">CLOSE</button>
	</div>
	<div class="continue-hint" id="exploreContinue" data-stage-target="<?php echo esc_attr($continue_target); ?>"><?php echo esc_html($args['continue_label']); ?></div>
</section>
