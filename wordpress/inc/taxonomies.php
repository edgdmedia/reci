<?php
/**
 * Register shared taxonomies for media content.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_taxonomy_post_types')) {
	/**
	 * Post types that should share category, tag, and location taxonomies.
	 */
	function reci_media_hub_taxonomy_post_types(): array {
		return [
			'post',
			'reci_article',
			'reci_podcast',
			'reci_video',
			'reci_event',
			'reci_reflection',
			'reci_quote',
			'reci_assessment',
			'reci_course',
		];
	}
}

if (! function_exists('reci_media_hub_register_taxonomies')) {
	function reci_media_hub_register_taxonomies(): void {
		$post_types = reci_media_hub_taxonomy_post_types();

		foreach ($post_types as $post_type) {
			register_taxonomy_for_object_type('category', $post_type);
			register_taxonomy_for_object_type('post_tag', $post_type);
		}

		register_taxonomy(
			'reci_location',
			$post_types,
			[
				'labels'            => [
					'name'          => __('Locations', 'reci-media-hub'),
					'singular_name' => __('Location', 'reci-media-hub'),
					'search_items'  => __('Search Locations', 'reci-media-hub'),
					'all_items'     => __('All Locations', 'reci-media-hub'),
					'edit_item'     => __('Edit Location', 'reci-media-hub'),
					'update_item'   => __('Update Location', 'reci-media-hub'),
					'add_new_item'  => __('Add New Location', 'reci-media-hub'),
					'menu_name'     => __('Locations', 'reci-media-hub'),
				],
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => ['slug' => 'location'],
			]
		);
	}
}

add_action('init', 'reci_media_hub_register_taxonomies');
