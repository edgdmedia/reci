<?php
/**
 * Register custom post types for media content.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_register_content_types')) {
	function reci_media_hub_register_content_types(): void {
		$types = [
			'reci_article' => [
				'singular' => 'Article',
				'plural'   => 'Articles',
				'slug'     => 'articles',
			],
			'reci_video'   => [
				'singular' => 'Video',
				'plural'   => 'Videos',
				'slug'     => 'videos',
			],
			'reci_podcast' => [
				'singular' => 'Podcast',
				'plural'   => 'Podcasts',
				'slug'     => 'podcasts',
			],
		];

		foreach ($types as $post_type => $config) {
			register_post_type(
				$post_type,
				[
					'labels' => [
						'name'          => __($config['plural'], 'reci-media-hub'),
						'singular_name' => __($config['singular'], 'reci-media-hub'),
						'add_new_item'  => sprintf(__('Add New %s', 'reci-media-hub'), __($config['singular'], 'reci-media-hub')),
						'edit_item'     => sprintf(__('Edit %s', 'reci-media-hub'), __($config['singular'], 'reci-media-hub')),
					],
					'public'             => true,
					'show_in_rest'       => true,
					'has_archive'        => true,
					'menu_position'      => 21,
					'menu_icon'          => 'dashicons-media-document',
					'rewrite'            => ['slug' => $config['slug']],
					'supports'           => ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'],
					'publicly_queryable' => true,
					'show_in_nav_menus'  => true,
				]
			);
		}
	}
}

add_action('init', 'reci_media_hub_register_content_types');
