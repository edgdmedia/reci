<?php
/**
 * Reflection chapter threshold message loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'threshold');
reci_reflection_render_variant('chapters/threshold-message', $args, $variant);
