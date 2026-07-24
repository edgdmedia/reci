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
<section class="reci-stage section crowd-section" id="<?php echo esc_attr($args['id']); ?>" data-transition-mode="<?php echo esc_attr($args['transition_mode'] ?? 'button'); ?>" data-continue-target="<?php echo esc_attr(ltrim((string) ($args['continue_target'] ?? ''), '#')); ?>">
	<?php foreach ((array) $args['layers'] as $index => $layer) : ?>
		<div class="crowd-layer cl-<?php echo esc_attr((string) ($index + 1)); ?>" data-parallax-layer="<?php echo esc_attr((string) $index); ?>" style="background-image:url('<?php echo esc_url($layer['src']); ?>'); opacity: <?php echo esc_attr((string) ($layer['opacity'] ?? '0.5')); ?>; mix-blend-mode: <?php echo esc_attr($layer['blend'] ?? 'normal'); ?>;"></div>
	<?php endforeach; ?>
	<div class="crowd-content">
		<div class="crowd-body">
			<div class="crowd-kicker">Crowd Memory</div>
			<div class="crowd-text"><?php echo esc_html($args['text']); ?></div>
		</div>
		<?php if (($args['transition_mode'] ?? 'button') === 'button') : ?>
		<button type="button" class="start-btn crowd-cta" data-stage-target="<?php echo esc_attr($args['continue_target']); ?>"><?php echo esc_html($args['button_label']); ?></button>
		<?php endif; ?>
	</div>
</section>
