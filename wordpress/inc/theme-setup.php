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
			[],
			wp_get_theme()->get('Version')
		);
	}
}

add_action('wp_enqueue_scripts', 'reci_media_hub_enqueue_assets');
