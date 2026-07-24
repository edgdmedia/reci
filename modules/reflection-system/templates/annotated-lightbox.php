<?php
/**
 * Reflection annotated lightbox loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'annotated');
reci_reflection_render_variant('lightboxes', $args, $variant);
