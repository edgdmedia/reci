<?php
if (! defined('ABSPATH')) { exit; }
$args = wp_parse_args($args ?? [], [
	'id' => 'crowd-stage',
	'layers' => [],
	'text' => '',
	'button_label' => 'Continue',
	'continue_target' => '#',
]);
?>
<section class="section crowd-section" id="<?php echo esc_attr($args['id']); ?>">
	<?php foreach ((array) $args['layers'] as $index => $layer) : ?>
		<div class="crowd-layer cl-<?php echo esc_attr((string) ($index + 1)); ?>" data-parallax-layer="<?php echo esc_attr((string) $index); ?>" style="background-image:url('<?php echo esc_url($layer['src']); ?>'); opacity: <?php echo esc_attr((string) ($layer['opacity'] ?? '0.5')); ?>; mix-blend-mode: <?php echo esc_attr($layer['blend'] ?? 'normal'); ?>;"></div>
	<?php endforeach; ?>
	<div class="crowd-content">
		<div class="crowd-kicker">Crowd Memory</div>
		<div class="crowd-text"><?php echo esc_html($args['text']); ?></div>
		<button type="button" class="start-btn crowd-cta" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['button_label']); ?></button>
	</div>
</section>
