<?php
/**
 * Reflection menu overlay loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'exhibit');
reci_reflection_render_variant('menus', $args, $variant);
