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
$variant = (string) ($args['variant'] ?? 'crowd');
reci_reflection_render_variant('chapters/parallax-stage', $args, $variant);
