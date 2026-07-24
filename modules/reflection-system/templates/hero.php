<?php
/**
 * Reflection hero loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'documentary');
reci_reflection_render_variant('heroes', $args, $variant);
