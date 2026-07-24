<?php
/**
 * Reflection chapter horizontal panels loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'quote-march');
reci_reflection_render_variant('chapters/horizontal-panels', $args, $variant);
