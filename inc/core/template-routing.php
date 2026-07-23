<?php

if (! defined('ABSPATH')) {
	exit;
}

add_filter('template_include', function ($template) {
	if (!is_string($template)) {
		return $template;
	}

	$theme_dir = get_template_directory();

	if (is_singular() && !is_page()) {
		$post_type = get_post_type();
		$candidate = $theme_dir . '/templates/single/single-' . $post_type . '.php';
		if (file_exists($candidate)) {
			return $candidate;
		}
		$candidate = $theme_dir . '/templates/single/single.php';
		if (file_exists($candidate)) {
			return $candidate;
		}
	}

	if (is_tax() || is_category() || is_tag()) {
		if (is_tax()) {
			$taxonomy = get_query_var('taxonomy');
			$candidate = $theme_dir . '/templates/taxonomy/taxonomy-' . $taxonomy . '.php';
			if (file_exists($candidate)) {
				return $candidate;
			}
		}
		
		if (is_category()) {
			$candidate = $theme_dir . '/templates/taxonomy/category.php';
			if (file_exists($candidate)) {
				return $candidate;
			}
		}
		
		if (is_tag()) {
			$candidate = $theme_dir . '/templates/taxonomy/tag.php';
			if (file_exists($candidate)) {
				return $candidate;
			}
		}

		$candidate = $theme_dir . '/templates/taxonomy/taxonomy.php';
		if (file_exists($candidate)) {
			return $candidate;
		}
	}

	if (is_archive() || is_home()) {
		if (is_post_type_archive()) {
			$post_type = get_post_type();
			$candidate = $theme_dir . '/templates/archive/archive-' . $post_type . '.php';
			if (file_exists($candidate)) {
				return $candidate;
			}
		}
		$candidate = $theme_dir . '/templates/archive/archive.php';
		if (file_exists($candidate)) {
			return $candidate;
		}
	}

	return $template;
}, 10);

add_filter('theme_page_templates', function ($templates) {
	$theme_dir = get_template_directory();
	$page_templates_dir = $theme_dir . '/templates/page';
	
	if (is_dir($page_templates_dir)) {
		$files = glob($page_templates_dir . '/*.php');
		foreach ($files as $file) {
			$headers = get_file_data($file, ['Template Name' => 'Template Name']);
			if (!empty($headers['Template Name'])) {
				$templates['templates/page/' . basename($file)] = $headers['Template Name'];
			}
		}
	}
	
	return $templates;
});
