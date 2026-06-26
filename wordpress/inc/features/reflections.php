<?php
/**
 * Reflection template-part helpers.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_reflection_render_variant')) {
	function reci_reflection_render_variant(string $base_path, array $args = [], string $variant = 'default'): void {
		$variant = sanitize_key($variant ?: 'default');
		$candidates = array_values(array_unique([$variant, 'default']));

		foreach ($candidates as $candidate) {
			$template_path = get_theme_file_path('modules/reflection-system/templates/' . trim($base_path, '/') . '/' . $candidate . '.php');
			if (file_exists($template_path)) {
				include $template_path;
				return;
			}
		}
	}
}
