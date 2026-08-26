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
				'singular'      => 'Quiz',
				'plural'        => 'Quizzes',
				'slug'          => 'quizzes',
				'menu_icon'     => 'dashicons-clipboard',
				'menu_position' => 27,
			],
			'reci_course'     => [
				'singular'      => 'Course',
				'plural'        => 'Courses',
				'slug'          => 'learn',
				'menu_icon'     => 'dashicons-welcome-learn-more',
				'menu_position' => 28,
			],
			'reci_team'       => [
				'singular'      => 'Team Member',
				'plural'        => 'Team',
				'slug'          => 'team',
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 29,
			],
			'reci_author'     => [
				'singular'      => 'Collaborator',
				'plural'        => 'Collaborators',
				'slug'          => 'collaborators',
				'menu_icon'     => 'dashicons-admin-users',
				'menu_position' => 30,
			],
			'reci_document'   => [
				'singular'      => 'Resource',
				'plural'        => 'Resources',
				'slug'          => 'documents',
				'menu_icon'     => 'dashicons-media-document',
				'menu_position' => 30,
			],
			'reci_partner'    => [
				'singular'      => 'Partner',
				'plural'        => 'Partners',
				'slug'          => 'partners',
				'menu_icon'     => 'dashicons-networking',
				'menu_position' => 31,
			],
			'reci_testimonial' => [
				'singular'      => 'Testimonial',
				'plural'        => 'Testimonials',
				'slug'          => 'testimonials',
				'menu_icon'     => 'dashicons-format-status',
				'menu_position' => 32,
			],
			'reci_glossary_term' => [
				'singular'      => 'Glossary Term',
				'plural'        => 'Glossary',
				'slug'          => 'glossary',
				'menu_icon'     => 'dashicons-book-alt',
				'menu_position' => 33,
			],
		];

		foreach ($types as $post_type => $config) {
			$supports = ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'];
			if ($post_type === 'reci_author') {
				$supports = ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'];
			}
			if ($post_type === 'reci_document') {
				$supports = ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'];
			}
			if ($post_type === 'reci_reflection') {
				$supports = ['title', 'excerpt', 'thumbnail', 'author', 'revisions'];
				$supports[] = 'page-attributes';
			}
			if ($post_type === 'post') {
				$supports[] = 'comments';
			}
			if ($post_type === 'reci_partner') {
				$supports = ['title', 'thumbnail'];
			}
			if ($post_type === 'reci_quote') {
				$supports = ['thumbnail'];
			}
			if ($post_type === 'reci_testimonial') {
				$supports = ['thumbnail'];
			}
			if ($post_type === 'reci_glossary_term') {
				$supports = ['title', 'editor'];
			}

			$publicly_queryable = ! in_array($post_type, ['reci_partner', 'reci_testimonial', 'reci_glossary_term'], true);
			$has_archive = ! in_array($post_type, ['reci_partner', 'reci_testimonial', 'reci_glossary_term'], true);
			$rewrite = in_array($post_type, ['reci_partner', 'reci_testimonial', 'reci_glossary_term'], true) ? false : ['slug' => $config['slug']];

			register_post_type(
				$post_type,
				[
					'labels' => reci_media_hub_cpt_labels($config['singular'], $config['plural']),
					'public'             => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'show_in_rest'       => true,
					'has_archive'        => $has_archive,
					'rewrite'            => $rewrite,
					'menu_icon'          => $config['menu_icon'],
					'menu_position'      => $config['menu_position'],
					'supports'           => $supports,
					'publicly_queryable' => $publicly_queryable,
					'show_in_nav_menus'  => true,
				]
			);
		}
	}
}

add_action('init', 'reci_media_hub_register_content_types');

if (! function_exists('reci_media_hub_default_comment_status')) {
	/**
	 * Default article comments to open for new posts.
	 *
	 * @param string $status       Default status.
	 * @param string $post_type    Post type.
	 * @param string $comment_type Comment type.
	 */
	function reci_media_hub_default_comment_status(string $status, string $post_type, string $comment_type): string {
		if ($post_type === 'post' && $comment_type === 'comment') {
			return 'open';
		}

		return $status;
	}
}

add_filter('get_default_comment_status', 'reci_media_hub_default_comment_status', 10, 3);

if (! function_exists('reci_media_hub_article_comments_open')) {
	/**
	 * Keep article comments open on the frontend.
	 *
	 * @param bool        $open    Whether comments are open.
	 * @param int|WP_Post $post_id Post ID or object.
	 */
	function reci_media_hub_article_comments_open(bool $open, $post_id): bool {
		$post = is_numeric($post_id) ? get_post((int) $post_id) : $post_id;
		if ($post instanceof WP_Post && $post->post_type === 'post') {
			return true;
		}

		return $open;
	}
}

add_filter('comments_open', 'reci_media_hub_article_comments_open', 10, 2);

if (! function_exists('reci_media_hub_auto_quote_title')) {
	/**
	 * Auto-generate post title for reci_quote from quote text.
	 */
	function reci_media_hub_auto_quote_title(array $data, array $postarr): array {
		if ($data['post_type'] === 'reci_quote' && empty($data['post_title'])) {
			$quote_text = sanitize_text_field($_POST['_reci_quote_text'] ?? '');
			if ($quote_text !== '') {
				$data['post_title'] = wp_trim_words($quote_text, 10, '…');
			} else {
				$data['post_title'] = 'Quote – ' . current_time('Y-m-d H:i');
			}
		}
		if ($data['post_type'] === 'reci_quote') {
			$data['post_content'] = '';
		}
		return $data;
	}
}
add_filter('wp_insert_post_data', 'reci_media_hub_auto_quote_title', 10, 2);

if (! function_exists('reci_media_hub_relabel_post_to_article')) {
	/**
	 * Relabel default 'Posts' to 'Articles' in the WP Admin.
	 */
	function reci_media_hub_relabel_post_to_article(object $labels): object {
		$labels->name               = __('Articles', 'reci-media-hub');
		$labels->singular_name      = __('Article', 'reci-media-hub');
		$labels->add_new            = __('Add New', 'reci-media-hub');
		$labels->add_new_item       = __('Add New Article', 'reci-media-hub');
		$labels->edit_item          = __('Edit Article', 'reci-media-hub');
		$labels->new_item           = __('New Article', 'reci-media-hub');
		$labels->view_item          = __('View Article', 'reci-media-hub');
		$labels->view_items         = __('View Articles', 'reci-media-hub');
		$labels->search_items       = __('Search Articles', 'reci-media-hub');
		$labels->not_found          = __('No articles found', 'reci-media-hub');
		$labels->not_found_in_trash = __('No articles found in Trash', 'reci-media-hub');
		$labels->all_items          = __('All Articles', 'reci-media-hub');
		$labels->archives           = __('Article Archives', 'reci-media-hub');
		$labels->menu_name          = __('Articles', 'reci-media-hub');
		$labels->name_admin_bar     = __('Article', 'reci-media-hub');
		
		return $labels;
	}
}
add_filter('post_type_labels_post', 'reci_media_hub_relabel_post_to_article');

if (! function_exists('reci_media_hub_modify_post_type_args')) {
	/**
	 * Modify default post type arguments to enable /articles/ archive.
	 */
	function reci_media_hub_modify_post_type_args(array $args, string $post_type): array {
		if ($post_type === 'post') {
			$args['has_archive'] = 'articles';
			$args['rewrite'] = ['slug' => 'articles', 'with_front' => false];
		}
		return $args;
	}
}
add_filter('register_post_type_args', 'reci_media_hub_modify_post_type_args', 10, 2);
