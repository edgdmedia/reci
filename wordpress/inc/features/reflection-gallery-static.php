<?php
/**
 * Static reflection gallery page template renderer.
 *
 * Loads copied standalone HTML files from the theme's reflection-gallery folder
 * and injects a base href so relative assets continue to work unchanged.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_render_static_reflection_gallery_template')) {
	/**
	 * Render a copied static reflection gallery page.
	 *
	 * @param string $file_name HTML file inside /reflection-gallery.
	 */
	function reci_render_static_reflection_gallery_template(string $file_name): void {
		$gallery_dir = trailingslashit(get_template_directory()) . 'reflection-gallery/';
		$gallery_root = realpath($gallery_dir);
		$target_path = realpath($gallery_dir . ltrim($file_name, '/'));

		if (! $gallery_root || ! $target_path || strpos($target_path, $gallery_root) !== 0 || ! is_file($target_path)) {
			status_header(404);
			wp_die(esc_html__('Reflection gallery file not found.', 'reci-media-hub'));
		}

		$html = file_get_contents($target_path);
		if ($html === false || $html === '') {
			status_header(500);
			wp_die(esc_html__('Unable to load reflection gallery file.', 'reci-media-hub'));
		}

		$base_href = trailingslashit(get_template_directory_uri()) . 'reflection-gallery/';
		if (stripos($html, '<base ') === false) {
			$html = preg_replace(
				'/<head(.*?)>/i',
				'<head$1>' . "\n" . '<base href="' . esc_url($base_href) . '">',
				$html,
				1
			);
		}

		$reflection_config = wp_json_encode(
			[
				'isLoggedIn' => is_user_logged_in(),
				'restUrl' => esc_url_raw(rest_url('reci/v1/reflection-responses')),
				'nonce' => wp_create_nonce('wp_rest'),
				'reflectionId' => is_singular('reci_reflection') ? get_queried_object_id() : 0,
				'currentUser' => is_user_logged_in() ? [
					'id' => get_current_user_id(),
					'name' => wp_get_current_user()->display_name,
				] : null,
			]
		);

		$html = preg_replace(
			'/<\/body>/i',
			'<script>window.RECIReflectionConfig = ' . $reflection_config . ';</script>' . "\n" . '</body>',
			$html,
			1
		);

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}
}
