<?php
/**
 * Reflection chapter quote loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'immersive-dark');
reci_reflection_render_variant('chapters/quote', $args, $variant);
