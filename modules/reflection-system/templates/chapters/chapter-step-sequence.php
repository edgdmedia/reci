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
$variant = (string) ($args['variant'] ?? 'march-history');
reci_reflection_render_variant('chapters/step-sequence', $args, $variant);
