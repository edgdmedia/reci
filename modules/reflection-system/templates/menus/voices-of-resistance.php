<?php
/**
 * Reflection menu/nav variant: Voices of Resistance.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = wp_parse_args($args ?? [], [
	'back_url' => '#',
]);
?>
<nav class="vor-nav">
	<a href="<?php echo esc_url($args['back_url']); ?>" class="back-link">← Gallery</a>
</nav>
