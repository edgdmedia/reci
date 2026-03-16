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
]);
?>
<section class="chapter-interact" id="<?php echo esc_attr($args['id']); ?>">
	<div class="instruction-overlay">
		<h3><?php echo esc_html($args['instruction']); ?></h3>
	</div>
	<div class="interact-stage" id="exploreStage" style="background-image:url('<?php echo esc_url($args['background_image']); ?>')">
		<?php foreach ((array) $args['hotspots'] as $hotspot) : ?>
			<div class="hotspot" style="top: <?php echo esc_attr((string) ($hotspot['top'] ?? '50%')); ?>; left: <?php echo esc_attr((string) ($hotspot['left'] ?? '50%')); ?>;" data-detail-key="<?php echo esc_attr((string) ($hotspot['key'] ?? '')); ?>">
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
	<div class="continue-hint" id="exploreContinue" data-stage-target="hold"><?php echo esc_html($args['continue_label']); ?></div>
</section>
