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
$variant = (string) ($args['variant'] ?? 'liberation');
reci_reflection_render_variant('chapters/word-shift', $args, $variant);
