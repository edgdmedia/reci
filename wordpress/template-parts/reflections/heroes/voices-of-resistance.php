<?php
/**
 * Reflection hero variant: Voices of Resistance.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'id' => 's-intro',
	'title' => 'Voices of Resistance',
	'subtitle' => '"I remember the day we decided enough was enough..."',
	'background_image' => '',
	'section_class' => '',
	'section_attributes' => [],
	'actions' => [],
]);

$section_attributes = '';
foreach ((array) $args['section_attributes'] as $attr_key => $attr_value) {
	$section_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
}
$action = $args['actions'][0] ?? ['href' => '#', 'label' => 'Enter the Story', 'attributes' => ['data-stage-target' => 'explore']];
$action_attributes = '';
foreach ((array) ($action['attributes'] ?? []) as $attr_key => $attr_value) {
	$action_attributes .= sprintf(' %s="%s"', esc_attr($attr_key), esc_attr((string) $attr_value));
}
?>
<section class="chapter-intro <?php echo esc_attr($args['section_class']); ?>" id="<?php echo esc_attr($args['id']); ?>"<?php echo $section_attributes; ?>>
	<div class="intro-bg" style="background-image:url('<?php echo esc_url($args['background_image']); ?>')"></div>
	<div class="intro-content">
		<h1 class="intro-title">Voices of<br><span style="color:var(--vor-accent)">Resistance</span></h1>
		<p class="intro-subtitle"><?php echo esc_html($args['subtitle']); ?></p>
		<a class="enter-btn" href="<?php echo esc_url($action['href'] ?? '#'); ?>"<?php echo $action_attributes; ?>><?php echo esc_html($action['label'] ?? 'Enter the Story'); ?></a>
	</div>
</section>
