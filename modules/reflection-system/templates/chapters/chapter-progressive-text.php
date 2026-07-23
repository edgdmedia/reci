<?php
/**
 * Reflection chapter progressive text loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'narrative');
reci_reflection_render_variant('chapters/progressive-text', $args, $variant);
