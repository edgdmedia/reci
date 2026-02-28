<?php
/**
 * Register shared taxonomy for media content.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_register_taxonomies')) {
	function reci_media_hub_register_taxonomies(): void {
		register_taxonomy(
			'reci_topic',
			['reci_article', 'reci_video', 'reci_podcast'],
			[
				'labels'       => [
					'name'          => __('Topics', 'reci-media-hub'),
					'singular_name' => __('Topic', 'reci-media-hub'),
				],
				'public'       => true,
				'show_in_rest' => true,
				'hierarchical' => false,
			]
		);
	}
}

add_action('init', 'reci_media_hub_register_taxonomies');
