<?php
/**
 * Reflection chapter loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'chain');
reci_reflection_render_variant('chapters/drag-reveal', $args, $variant);
