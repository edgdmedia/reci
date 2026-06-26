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
