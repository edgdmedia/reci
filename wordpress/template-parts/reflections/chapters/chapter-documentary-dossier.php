<?php
/**
 * Reflection chapter documentary dossier loader.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$args = $args ?? [];
$variant = (string) ($args['variant'] ?? 'archival');
reci_reflection_render_variant('chapters/documentary-dossier', $args, $variant);
