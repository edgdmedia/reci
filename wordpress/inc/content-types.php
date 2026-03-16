<?php
/**
 * Register custom post types for media content.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_cpt_labels')) {
	/**
	 * Build labels for a post type.
	 */
	function reci_media_hub_cpt_labels(string $singular, string $plural): array {
		return [
			'name'               => __($plural, 'reci-media-hub'),
			'singular_name'      => __($singular, 'reci-media-hub'),
			'add_new'            => __('Add New', 'reci-media-hub'),
			'add_new_item'       => sprintf(__('Add New %s', 'reci-media-hub'), __($singular, 'reci-media-hub')),
			'edit_item'          => sprintf(__('Edit %s', 'reci-media-hub'), __($singular, 'reci-media-hub')),
			'new_item'           => sprintf(__('New %s', 'reci-media-hub'), __($singular, 'reci-media-hub')),
			'view_item'          => sprintf(__('View %s', 'reci-media-hub'), __($singular, 'reci-media-hub')),
			'view_items'         => sprintf(__('View %s', 'reci-media-hub'), __($plural, 'reci-media-hub')),
			'search_items'       => sprintf(__('Search %s', 'reci-media-hub'), __($plural, 'reci-media-hub')),
			'not_found'          => sprintf(__('No %s found', 'reci-media-hub'), strtolower($plural)),
			'not_found_in_trash' => sprintf(__('No %s found in Trash', 'reci-media-hub'), strtolower($plural)),
			'all_items'          => sprintf(__('All %s', 'reci-media-hub'), __($plural, 'reci-media-hub')),
			'archives'           => sprintf(__('%s Archives', 'reci-media-hub'), __($singular, 'reci-media-hub')),
			'menu_name'          => __($plural, 'reci-media-hub'),
		];
	}
}

if (! function_exists('reci_media_hub_register_content_types')) {
	function reci_media_hub_register_content_types(): void {
		$types = [
			'reci_article'    => [
				'singular'      => 'Article',
				'plural'        => 'Articles',
				'slug'          => 'articles',
				'menu_icon'     => 'dashicons-media-document',
				'menu_position' => 21,
			],
			'reci_podcast'    => [
				'singular'      => 'Podcast',
				'plural'        => 'Podcasts',
				'slug'          => 'podcasts',
				'menu_icon'     => 'dashicons-microphone',
				'menu_position' => 22,
			],
			'reci_video'      => [
				'singular'      => 'Video',
				'plural'        => 'Videos',
				'slug'          => 'videos',
				'menu_icon'     => 'dashicons-video-alt3',
				'menu_position' => 23,
			],
			'reci_event'      => [
				'singular'      => 'Event',
				'plural'        => 'Events',
				'slug'          => 'events',
				'menu_icon'     => 'dashicons-calendar-alt',
				'menu_position' => 24,
			],
			'reci_reflection' => [
				'singular'      => 'Reflection',
				'plural'        => 'Reflections',
				'slug'          => 'reflections',
				'menu_icon'     => 'dashicons-format-status',
				'menu_position' => 25,
			],
			'reci_quote'      => [
				'singular'      => 'Quote',
				'plural'        => 'Quotes',
				'slug'          => 'quotes',
				'menu_icon'     => 'dashicons-format-quote',
				'menu_position' => 26,
			],
			'reci_assessment' => [
				'singular'      => 'Assessment',
				'plural'        => 'Assessments',
				'slug'          => 'assessments',
				'menu_icon'     => 'dashicons-clipboard',
				'menu_position' => 27,
			],
			'reci_course'     => [
				'singular'      => 'Course',
				'plural'        => 'Courses',
				'slug'          => 'courses',
				'menu_icon'     => 'dashicons-welcome-learn-more',
				'menu_position' => 28,
			],
		];

		foreach ($types as $post_type => $config) {
			register_post_type(
				$post_type,
				[
					'labels' => reci_media_hub_cpt_labels($config['singular'], $config['plural']),
					'public'             => true,
					'show_in_rest'       => true,
					'has_archive'        => true,
					'rewrite'            => ['slug' => $config['slug']],
					'menu_icon'          => $config['menu_icon'],
					'menu_position'      => $config['menu_position'],
					'supports'           => $post_type === 'reci_reflection'
					? ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions', 'page-attributes']
					: ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'],
					'publicly_queryable' => true,
					'show_in_nav_menus'  => true,
				]
			);
		}
	}
}

add_action('init', 'reci_media_hub_register_content_types');
