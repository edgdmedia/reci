<?php
/**
 * Inline SVG helper.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_inline_svg')) {
	/**
	 * Render an SVG file inline.
	 *
	 * @param string               $relative_path Relative theme path to svg.
	 * @param string               $class         Optional class string.
	 * @param array<string,string> $attributes    Extra root svg attributes.
	 */
	function reci_inline_svg(string $relative_path, string $class = '', array $attributes = []): string {
		$relative_path = ltrim($relative_path, '/');
		$file_path     = get_template_directory() . '/' . $relative_path;

		if (! is_readable($file_path) || strtolower((string) pathinfo($file_path, PATHINFO_EXTENSION)) !== 'svg') {
			return '';
		}

		$svg = file_get_contents($file_path);
		if (! is_string($svg) || trim($svg) === '') {
			return '';
		}

		$svg = preg_replace('/<\\?xml.*?\\?>/s', '', $svg) ?? $svg;

		if ($class !== '') {
			if (preg_match('/<svg\\b[^>]*class="/i', $svg) === 1) {
				$svg = preg_replace('/(<svg\\b[^>]*class=")([^"]*)"/i', '$1$2 ' . esc_attr($class) . '"', $svg, 1) ?? $svg;
			} else {
				$svg = preg_replace('/<svg\\b/i', '<svg class="' . esc_attr($class) . '"', $svg, 1) ?? $svg;
			}
		}

		if (! empty($attributes)) {
			$attribute_markup = '';
			foreach ($attributes as $key => $value) {
				$attribute_markup .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
			}
			$svg = preg_replace('/<svg\\b/i', '<svg' . $attribute_markup, $svg, 1) ?? $svg;
		}

		return trim($svg);
	}
}
