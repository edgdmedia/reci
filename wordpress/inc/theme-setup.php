<?php

/**
 * Theme support and setup hooks.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_setup')) {
	function reci_media_hub_setup(): void
	{
		add_theme_support('title-tag');
		add_theme_support('post-thumbnails');
		add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			]
		);

		register_nav_menus(
			[
				'primary'        => __('Primary Menu', 'reci-media-hub'),
				'footer_primary' => __('Footer Primary Menu', 'reci-media-hub'),
				'footer_legal'   => __('Footer Legal Menu', 'reci-media-hub'),
			]
		);
	}
}

add_action('after_setup_theme', 'reci_media_hub_setup');

if (! function_exists('reci_media_hub_enqueue_assets')) {
	function reci_media_hub_enqueue_assets(): void
	{
		wp_enqueue_style(
			'reci-media-hub-fonts',
			'https://fonts.googleapis.com/css2?family=EB+Garamond:wght@600;700&display=swap',
			[],
			null
		);

		wp_enqueue_style(
			'reci-media-hub-style',
			get_stylesheet_uri(),
			['reci-media-hub-fonts'],
			wp_get_theme()->get('Version')
		);

		$tailwind_relative_path = '/assets/css/tailwind.css';
		$tailwind_file_path     = get_template_directory() . $tailwind_relative_path;

		if (file_exists($tailwind_file_path)) {
			wp_enqueue_style(
				'reci-media-hub-tailwind',
				get_template_directory_uri() . $tailwind_relative_path,
				['reci-media-hub-style'],
				(string) filemtime($tailwind_file_path)
			);
		}

		$js_relative_path = '/assets/js/theme.js';
		$js_file_path     = get_template_directory() . $js_relative_path;

		if (file_exists($js_file_path)) {
			wp_enqueue_script(
				'reci-media-hub-theme',
				get_template_directory_uri() . $js_relative_path,
				[],
				(string) filemtime($js_file_path),
				true
			);
		}
	}
}

add_action('wp_enqueue_scripts', 'reci_media_hub_enqueue_assets');

if (! function_exists('reci_media_hub_enqueue_reflection_renderer')) {
	function reci_media_hub_enqueue_reflection_renderer(): void
	{
		if (! is_singular('reci_reflection')) {
			return;
		}

		$path    = get_template_directory() . '/assets/js/reflection-renderer.js';
		$version = file_exists($path) ? (string) filemtime($path) : wp_get_theme()->get('Version');

		wp_enqueue_script(
			'reci-reflection-renderer',
			get_template_directory_uri() . '/assets/js/reflection-renderer.js',
			[],
			$version,
			true
		);
		add_filter('script_loader_tag', static function (string $tag, string $handle): string {
			if ($handle === 'reci-reflection-renderer') {
				return str_replace(' src=', ' type="module" src=', $tag);
			}
			return $tag;
		}, 10, 2);
	}
}
add_action('wp_enqueue_scripts', 'reci_media_hub_enqueue_reflection_renderer');
