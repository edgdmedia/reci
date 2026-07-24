<?php
/**
 * Reflection chapter hotspot stage loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'story');
reci_reflection_render_variant('chapters/hotspot-stage', $args, $variant);
