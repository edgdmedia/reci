<?php
/**
 * Enable hardcoded reflection gallery page templates on reci_reflection posts.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_reflection_gallery_template_map')) {
	/**
	 * Hardcoded reflection gallery templates exposed on reflection posts.
	 *
	 * @return array<string,string>
	 */
	function reci_reflection_gallery_template_map(): array {
		return [
			'page-templates/template-reflection-gallery-static.php' => __('Reflection Gallery - Index', 'reci-media-hub'),
			'page-templates/template-reflection-voices-of-resistance.php' => __('Reflection Gallery - Voices of Resistance', 'reci-media-hub'),
			'page-templates/template-reflection-march-toward-justice.php' => __('Reflection Gallery - March Toward Justice', 'reci-media-hub'),
			'page-templates/template-reflection-racial-disparities.php' => __('Reflection Gallery - Racial Disparities', 'reci-media-hub'),
			'page-templates/template-reflection-breaking-chains.php' => __('Reflection Gallery - Breaking Chains', 'reci-media-hub'),
			'page-templates/template-reflection-we-humans.php' => __('Reflection Gallery - We Humans', 'reci-media-hub'),
		];
	}
}

if (! function_exists('reci_register_reflection_gallery_templates')) {
	/**
	 * Register custom templates for the reflection post type.
	 *
	 * @param array<string,string> $templates Existing templates.
	 * @return array<string,string>
	 */
	function reci_register_reflection_gallery_templates(array $templates): array {
		return array_merge($templates, reci_reflection_gallery_template_map());
	}
}
add_filter('theme_reci_reflection_templates', 'reci_register_reflection_gallery_templates');

if (! function_exists('reci_use_reflection_gallery_template')) {
	/**
	 * Load selected page-style template for reci_reflection singulars.
	 *
	 * @param string $template Default resolved template.
	 * @return string
	 */
	function reci_use_reflection_gallery_template(string $template): string {
		if (! is_singular('reci_reflection')) {
			return $template;
		}

		$post_id = get_queried_object_id();
		if (! $post_id) {
			return $template;
		}

		$selected = (string) get_post_meta($post_id, '_wp_page_template', true);
		if ($selected === '' || $selected === 'default') {
			return $template;
		}

		$allowed = reci_reflection_gallery_template_map();
		if (! isset($allowed[$selected])) {
			return $template;
		}

		$candidate = get_theme_file_path($selected);
		if (is_file($candidate)) {
			return $candidate;
		}

		return $template;
	}
}
add_filter('template_include', 'reci_use_reflection_gallery_template', 99);
