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
$variant = (string) ($args['variant'] ?? 'analytical');
reci_reflection_render_variant('chapters/data-cards', $args, $variant);
