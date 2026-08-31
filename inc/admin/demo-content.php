<?php

/**
 * RECI Media Hub — Demo Content Installer.
 *
 * Provides a one-click demo content importer available in
 * Appearance → RECI Settings → Demo.
 *
 * - Idempotent: skips posts that already exist (matched by slug).
 * - Attaches theme-bundled images to posts as featured images.
 * - Creates taxonomy terms (topics, locations) before posts.
 * - Provides a reset handler that removes only demo-flagged posts.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Admin-post handlers
// ---------------------------------------------------------------------------

add_action( 'admin_post_reci_install_demo', 'reci_handle_install_demo' );
add_action( 'admin_post_reci_reset_demo',   'reci_handle_reset_demo' );
add_action( 'wp_ajax_reci_demo_start_import', 'reci_demo_ajax_start_import' );
add_action( 'wp_ajax_reci_demo_process_step', 'reci_demo_ajax_process_step' );

add_action( 'admin_menu', function (): void {
	add_submenu_page(
		'reci-settings',
		'Demo Content',
		'Demo Content',
		'manage_options',
		'reci-demo-import',
		'reci_demo_import_page_html'
	);
});

if ( ! function_exists( 'reci_demo_content_types' ) ) {
	function reci_demo_content_types(): array {
		$image_groups = function_exists( 'reci_demo_image_groups' ) ? reci_demo_image_groups() : [];
		return [
			'reci_demo_images_reflections' => [ 'label' => 'Reflection Images', 'count' => count( $image_groups['reci_demo_images_reflections'] ?? [] ) ],
			'reci_demo_images_articles'    => [ 'label' => 'Article Images',    'count' => count( $image_groups['reci_demo_images_articles'] ?? [] ) ],
			'reci_demo_images_events'      => [ 'label' => 'Event Images',      'count' => count( $image_groups['reci_demo_images_events'] ?? [] ) ],
			'reci_demo_images_courses'     => [ 'label' => 'Course Images',     'count' => count( $image_groups['reci_demo_images_courses'] ?? [] ) ],
			'reci_demo_images_podcasts'    => [ 'label' => 'Podcast Images',    'count' => count( $image_groups['reci_demo_images_podcasts'] ?? [] ) ],
			'reci_demo_images_videos'      => [ 'label' => 'Video Images',      'count' => count( $image_groups['reci_demo_images_videos'] ?? [] ) ],
			'reci_demo_images_quizzes'     => [ 'label' => 'Quiz Images',       'count' => count( $image_groups['reci_demo_images_quizzes'] ?? [] ) ],
			'reci_demo_images_partners'    => [ 'label' => 'Partner Images',    'count' => count( $image_groups['reci_demo_images_partners'] ?? [] ) ],
			'reci_demo_images_misc'        => [ 'label' => 'Shared / Misc Images', 'count' => count( $image_groups['reci_demo_images_misc'] ?? [] ) ],
			'reci_demo_taxonomies'         => [ 'label' => 'Taxonomies (Spheres, SDGs, Topics, Locations)', 'count' => 6 + 17 + 8 + 4 ],
			'post'   => [ 'label' => 'Articles',     'count' => 15 ],
			'reci_podcast'   => [ 'label' => 'Podcasts',     'count' => 3 ],
			'reci_video'     => [ 'label' => 'Videos',       'count' => 6 ],
			'reci_event'     => [ 'label' => 'Events',       'count' => 5 ],
			'reci_reflection'=> [ 'label' => 'Reflections',  'count' => 6 ],
			'reci_quote'     => [ 'label' => 'Quotes',       'count' => 3 ],
			'reci_assessment'=> [ 'label' => 'Quizzes',      'count' => 5 ],
			'reci_course'    => [ 'label' => 'Courses',      'count' => 17 ],
			'reci_team'      => [ 'label' => 'Team',         'count' => 3 ],
			'reci_testimonial'    => [ 'label' => 'Testimonials',     'count' => 6 ],
			'reci_glossary_term' => [ 'label' => 'Glossary Terms',  'count' => 42 ],
			'reci_author'        => [ 'label' => 'Collaborators', 'count' => 110 ],
			'reci_partner'       => [ 'label' => 'Partners',        'count' => 7 ],
			'reci_page'          => [ 'label' => 'Core Pages',      'count' => 18 ],
		];
	}
}

function reci_handle_install_demo(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized.' );
	}
	check_admin_referer( 'reci_demo_action' );

	$selected = isset( $_POST['reci_demo_types'] ) && is_array( $_POST['reci_demo_types'] )
		? array_map( 'sanitize_key', $_POST['reci_demo_types'] )
		: [];

	reci_install_demo_content( $selected );

	wp_safe_redirect( add_query_arg( [
		'page'         => 'reci-settings',
		'tab'          => 'demo',
		'demo_notice'  => 'installed',
	], admin_url( 'admin.php' ) ) );
	exit;
}

function reci_handle_reset_demo(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Unauthorized.' );
	}
	check_admin_referer( 'reci_demo_action' );

	reci_reset_demo_content();

	wp_safe_redirect( add_query_arg( [
		'page'         => 'reci-settings',
		'tab'          => 'demo',
		'demo_notice'  => 'reset',
	], admin_url( 'admin.php' ) ) );
	exit;
}

// ---------------------------------------------------------------------------
// Async import state
// ---------------------------------------------------------------------------

function reci_demo_job_option_key(): string {
	return 'reci_demo_import_job';
}

function reci_demo_asset_registry_option_key(): string {
	return 'reci_demo_asset_registry';
}

function reci_demo_get_job(): array {
	$job = get_option( reci_demo_job_option_key(), [] );
	return is_array( $job ) ? $job : [];
}

function reci_demo_set_job( array $job ): void {
	update_option( reci_demo_job_option_key(), $job, false );
}

function reci_demo_clear_job(): void {
	delete_option( reci_demo_job_option_key() );
}

function reci_demo_get_asset_registry(): array {
	$registry = get_option( reci_demo_asset_registry_option_key(), [] );
	return is_array( $registry ) ? $registry : [];
}

function reci_demo_set_asset_registry( array $registry ): void {
	update_option( reci_demo_asset_registry_option_key(), $registry, false );
}

function reci_demo_append_activity( array $job, string $message ): array {
	$activity   = is_array( $job['activity'] ?? null ) ? $job['activity'] : [];
	$activity[] = $message;
	$job['activity'] = array_slice( $activity, -30 );
	return $job;
}

function reci_demo_present_job_state( array $job ): array {
	if ( empty( $job ) ) {
		return [];
	}

	$total     = count( $job['queue'] ?? [] );
	$cursor    = (int) ( $job['cursor'] ?? 0 );
	$processed = min( $cursor, $total );
	$percent   = $total > 0 ? (int) round( ( $processed / $total ) * 100 ) : 0;
	$running   = ! empty( $job['running'] ) && empty( $job['finished'] );

	return [
		'job_id'        => (string) ( $job['id'] ?? '' ),
		'running'       => $running,
		'finished'      => ! empty( $job['finished'] ),
		'current_label' => (string) ( $job['current_label'] ?? ( $running ? 'Running import…' : 'Idle' ) ),
		'progress_text' => sprintf( 'Processed %1$d of %2$d steps', $processed, $total ),
		'percent'       => $percent,
		'activity'      => array_values( is_array( $job['activity'] ?? null ) ? $job['activity'] : [] ),
		'completed'     => array_values( is_array( $job['completed'] ?? null ) ? $job['completed'] : [] ),
		'failed'        => array_values( is_array( $job['failed'] ?? null ) ? $job['failed'] : [] ),
		'skipped'       => array_values( is_array( $job['skipped'] ?? null ) ? $job['skipped'] : [] ),
		'group_status'  => reci_demo_group_status_map(),
	];
}

function reci_demo_expected_group_paths( string $group ): array {
	$image_groups = reci_demo_image_groups();
	if ( isset( $image_groups[ $group ] ) && is_array( $image_groups[ $group ] ) ) {
		return array_values( $image_groups[ $group ] );
	}

	$remote_group = function_exists( 'reci_remote_demo_group_definition' ) ? reci_remote_demo_group_definition( $group ) : [];
	if ( ! empty( $remote_group ) ) {
		$root = ltrim( (string) ( $remote_group['registry_prefix'] ?? $remote_group['extract_root'] ?? '' ), '/' );
		$assets = reci_demo_remote_group_assets( $group, $remote_group );
		if ( ! empty( $assets ) ) {
			return array_values( array_map( static fn( $asset ) => (string) ( $asset['path'] ?? '' ), $assets ) );
		}

		if ( 'release-archive' === sanitize_key( (string) ( $remote_group['type'] ?? '' ) ) && $root !== '' ) {
			$local_root = get_template_directory() . '/demo-content/images/' . str_replace( 'site/', '', trailingslashit( $root ) );
			if ( is_dir( $local_root ) ) {
				$paths = reci_demo_collect_image_paths_from_directory( trailingslashit( str_replace( 'site/', 'site/', $root ) ) );
				if ( ! empty( $paths ) ) {
					return $paths;
				}
			}
		}
	}

	return [];
}

function reci_demo_group_status_map(): array {
	$types    = reci_demo_content_types();
	$registry = reci_demo_get_asset_registry();
	$slugs    = get_option( 'reci_demo_slugs', [] );
	$job      = reci_demo_get_job();
	$queue    = is_array( $job['queue'] ?? null ) ? $job['queue'] : [];
	$pending_groups = array_values( array_unique( array_map( static fn( $step ) => (string) ( $step['group'] ?? '' ), $queue ) ) );
	$result = [];

	foreach ( $types as $group => $info ) {
		$expected = (int) ( $info['count'] ?? 0 );
		$imported = 0;

		if ( str_starts_with( $group, 'reci_demo_images_' ) ) {
			$paths = reci_demo_expected_group_paths( $group );
			$expected = count( $paths );
			foreach ( $paths as $path ) {
				if ( ! empty( $registry[ $path ]['attachment_id'] ) ) {
					$imported++;
				}
			}
		} elseif ( 'reci_demo_taxonomies' === $group ) {
			$imported = get_option( 'reci_demo_taxonomies_seeded', false ) ? $expected : 0;
		} elseif ( reci_demo_group_uses_demo_posts( $group ) ) {
			$imported = reci_demo_imported_post_count_for_group( $group );
		} else {
			$imported = reci_demo_imported_slug_count_for_group( $group, $slugs );
		}

		$status = 'not_started';
		if ( in_array( $group, $pending_groups, true ) ) {
			$status = ! empty( $job['running'] ) ? 'in_progress' : 'partial';
		}
		if ( $expected > 0 && $imported >= $expected ) {
			$status = 'completed';
		} elseif ( $imported > 0 ) {
			$status = 'partial';
		}

		$result[ $group ] = [
			'label'     => (string) ( $info['label'] ?? $group ),
			'expected'  => $expected,
			'imported'  => $imported,
			'remaining' => max( 0, $expected - $imported ),
			'status'    => $status,
		];
	}

	return $result;
}

function reci_demo_group_uses_demo_posts( string $group ): bool {
	return in_array( $group, [
		'post',
		'reci_podcast',
		'reci_video',
		'reci_event',
		'reci_reflection',
		'reci_quote',
		'reci_assessment',
		'reci_course',
		'reci_team',
		'reci_testimonial',
		'reci_glossary_term',
		'reci_author',
		'reci_partner',
		'reci_page',
	], true );
}

function reci_demo_imported_post_count_for_group( string $group ): int {
	$post_type = 'page' === $group || 'reci_page' === $group ? 'page' : $group;
	$query = new WP_Query([
		'post_type'              => $post_type,
		'post_status'            => [ 'publish', 'draft', 'pending', 'private' ],
		'posts_per_page'         => 1,
		'fields'                 => 'ids',
		'no_found_rows'          => false,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'meta_query'             => [
			[
				'key'   => '_reci_demo',
				'value' => '1',
			],
		],
	]);

	return (int) $query->found_posts;
}

function reci_demo_imported_slug_count_for_group( string $group, array $slugs ): int {
	if ( empty( $slugs ) ) {
		return 0;
	}

	$matched = [];
	$datasets = [
		'post' => 'articles',
		'reci_podcast' => 'podcasts',
		'reci_video' => 'videos',
		'reci_event' => 'events',
		'reci_quote' => 'quotes',
		'reci_assessment' => 'assessments',
		'reci_course' => 'courses',
		'reci_team' => 'team',
		'reci_testimonial' => 'testimonials',
		'reci_glossary_term' => 'glossary',
		'reci_author' => 'authors',
		'reci_partner' => 'partners',
	];

	if ( 'reci_reflection' === $group ) {
		foreach ( reci_demo_reflection_queue_items() as $item ) {
			$matched[] = (string) ( $item['slug'] ?? '' );
		}
	} elseif ( 'reci_page' === $group ) {
		$page_dataset = reci_demo_load_php_dataset( 'pages' );
		foreach ( (array) ( $page_dataset['core'] ?? [] ) as $slug => $cfg ) {
			$matched[] = (string) $slug;
		}
		$matched[] = 'dashboard';
		foreach ( (array) ( $page_dataset['dashboard']['children'] ?? [] ) as $slug => $cfg ) {
			$matched[] = 'dashboard/' . (string) $slug;
		}
	} elseif ( isset( $datasets[ $group ] ) ) {
		$items = reci_demo_load_group_items( $datasets[ $group ] );
		foreach ( (array) $items as $item ) {
			if ( is_array( $item ) && ! empty( $item['slug'] ) ) {
				$matched[] = (string) $item['slug'];
			}
		}
	}

	return count( array_intersect( array_unique( array_filter( $matched ) ), array_unique( $slugs ) ) );
}

function reci_demo_load_group_items( string $dataset_name ): array {
	$path = get_template_directory() . '/demo-content/' . $dataset_name;
	if ( is_dir( $path ) ) {
		$items = [];
		foreach ( glob( trailingslashit( $path ) . '*.php' ) ?: [] as $file ) {
			$data = require $file;
			if ( is_array( $data ) ) {
				$items = array_merge( $items, $data );
			}
		}
		return $items;
	}

	$data = reci_demo_load_php_dataset( $dataset_name );
	return isset( $data['items'] ) && is_array( $data['items'] ) ? $data['items'] : ( is_array( $data ) ? $data : [] );
}

function reci_demo_result_entry( string $status, string $label, string $message, array $extra = [] ): array {
	return array_merge(
		[
			'status'  => $status,
			'label'   => $label,
			'message' => $message,
		],
		$extra
	);
}

function reci_demo_collect_image_paths_from_directory( string $relative_dir ): array {
	$base_dir = trailingslashit( get_template_directory() . '/demo-content/images/' . trim( $relative_dir, '/' ) );
	if ( ! is_dir( $base_dir ) ) {
		return [];
	}

	$paths    = [];
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base_dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $file ) {
		if ( ! $file instanceof SplFileInfo || ! $file->isFile() ) {
			continue;
		}
		$extension = strtolower( $file->getExtension() );
		if ( ! in_array( $extension, [ 'jpg', 'jpeg', 'png', 'webp', 'gif', 'avif' ], true ) ) {
			continue;
		}
		$relative = str_replace( trailingslashit( get_template_directory() . '/demo-content/images/' ), '', $file->getPathname() );
		$paths[]  = str_replace( DIRECTORY_SEPARATOR, '/', $relative );
	}

	sort( $paths );
	return $paths;
}

function reci_demo_misc_image_paths(): array {
	return [];
}

function reci_demo_image_groups(): array {
	return [
		'reci_demo_images_reflections' => reci_demo_collect_image_paths_from_directory( 'site/reflections' ),
		'reci_demo_images_articles'    => reci_demo_collect_image_paths_from_directory( 'site/articles' ),
		'reci_demo_images_events'      => reci_demo_collect_image_paths_from_directory( 'site/events' ),
		'reci_demo_images_courses'     => reci_demo_collect_image_paths_from_directory( 'site/learn' ),
		'reci_demo_images_podcasts'    => reci_demo_collect_image_paths_from_directory( 'site/podcasts' ),
		'reci_demo_images_videos'      => reci_demo_collect_image_paths_from_directory( 'site/videos' ),
		'reci_demo_images_quizzes'     => reci_demo_collect_image_paths_from_directory( 'site/quizzes' ),
		'reci_demo_images_partners'    => reci_demo_collect_image_paths_from_directory( 'site/partners' ),
		'reci_demo_images_misc'        => reci_demo_misc_image_paths(),
	];
}

function reci_demo_load_php_dataset( string $name ): array {
	static $cache = [];

	if ( array_key_exists( $name, $cache ) ) {
		return $cache[ $name ];
	}

	$file = get_template_directory() . '/demo-content/' . $name . '.php';
	if ( ! file_exists( $file ) ) {
		$cache[ $name ] = [];
		return $cache[ $name ];
	}

	$data = require $file;
	$cache[ $name ] = is_array( $data ) ? $data : [];

	return $cache[ $name ];
}

function reci_demo_reflection_queue_items(): array {
	$dataset = reci_demo_load_php_dataset( 'reflections' );
	return array_values( (array) ( $dataset['items'] ?? [] ) );
}

function reci_demo_bootstrap_taxonomies(): array {
	$topics = reci_demo_ensure_terms( 'reci_topic', [
		'Systemic Racism',
		'Intersectionality',
		'Cultural Identity',
		'Workplace Equity',
		'Community Action',
		'Education',
		'Health Disparities',
		'Criminal Justice',
		'Indigenous Rights',
		'Technology & Equity',
		'Public Service',
		'Rural Equity',
		'Health Determinants',
		'Inclusion',
		'Access',
		'Economic Stability',
		'Cultural Competence',
	] );

	foreach ( [
		'Systemic Racism', 'Public Service', 'Rural Equity', 'Health Determinants',
		'Health Disparities', 'Inclusion', 'Cultural Competence', 'Education',
		'Workplace Equity', 'Access', 'Economic Stability', 'Technology & Equity',
	] as $name ) {
		$term = term_exists( $name, 'category' );
		if ( ! $term ) {
			wp_insert_term( $name, 'category' );
		}
	}

	$locations = reci_demo_ensure_terms( 'reci_location', [ 'Pittsburgh', 'Allegheny County', 'Pennsylvania', 'National' ] );
	reci_demo_ensure_terms( 'reci_sphere', [ 'Recognizing Racial Oppression', 'Gauging Racial Inequities', 'Embracing Racial Diversity', 'Building Racial Empathy' ] );
	reci_demo_ensure_terms( 'reci_practice_focus', [ 'Framework / Model', 'Community-Based Approach', 'Policy / Legislation', 'Curriculum / Training', 'Practice / Intervention', 'Research / Evaluation', 'Organizational Strategy', 'Other' ] );
	reci_demo_ensure_terms( 'reci_show', [ 'Healing Overflow with Dr Toy' ] );
	if ( function_exists( 'reci_media_hub_seed_default_spheres' ) ) {
		reci_media_hub_seed_default_spheres();
	}
	if ( function_exists( 'reci_media_hub_seed_default_sdgs' ) ) {
		reci_media_hub_seed_default_sdgs();
	}
	if ( function_exists( 'reci_media_hub_backfill_sdg_descriptions' ) ) {
		reci_media_hub_backfill_sdg_descriptions();
	}
	reci_media_hub_seed_default_shows();

	return [ 'topics' => $topics, 'locations' => $locations ];
}

function reci_demo_build_import_queue( array $selected ): array {
	$queue = [];
	$image_groups = reci_demo_image_groups();

	foreach ( $selected as $group ) {
		$remote_group = function_exists( 'reci_remote_demo_group_definition' ) ? reci_remote_demo_group_definition( $group ) : [];
		if ( ! empty( $remote_group ) ) {
			$archive_step = reci_demo_remote_group_archive_step( $group, $remote_group );
			if ( ! empty( $archive_step ) ) {
				$queue[] = $archive_step;
				continue;
			}

			$remote_assets = reci_demo_remote_group_assets( $group, $remote_group );
			foreach ( $remote_assets as $asset ) {
				$queue[] = [ 'type' => 'import_remote_image', 'group' => $group, 'asset' => $asset ];
			}
			continue;
		}

		if ( 'reci_demo_taxonomies' === $group ) {
			reci_demo_bootstrap_taxonomies();
			update_option( 'reci_demo_taxonomies_seeded', 1, false );
			$queue[] = [ 'type' => 'skip', 'group' => $group, 'message' => 'Taxonomies seeded.' ];
			continue;
		}

		if ( isset( $image_groups[ $group ] ) ) {
			foreach ( $image_groups[ $group ] as $path ) {
				$queue[] = [ 'type' => 'import_image', 'group' => $group, 'path' => $path ];
			}
			continue;
		}

		if ( 'reci_reflection' === $group ) {
			foreach ( reci_demo_reflection_queue_items() as $item ) {
				$queue[] = [ 'type' => 'import_reflection', 'group' => $group, 'item' => $item ];
			}
			continue;
		}

		$queue[] = [ 'type' => 'import_legacy_group', 'group' => $group ];
	}

	return $queue;
}

function reci_demo_remote_group_archive_step( string $group_id, array $group_definition ): array {
	$type = sanitize_key( (string) ( $group_definition['type'] ?? '' ) );
	$url  = esc_url_raw( (string) ( $group_definition['url'] ?? '' ) );

	if ( 'release-archive' !== $type || '' === $url ) {
		return [];
	}

	return [
		'type'  => 'import_remote_archive',
		'group' => $group_id,
		'url'   => $url,
		'root'  => ltrim( (string) ( $group_definition['extract_root'] ?? '' ), '/' ),
		'registry_prefix' => ltrim( (string) ( $group_definition['registry_prefix'] ?? '' ), '/' ),
		'label' => (string) ( $group_definition['label'] ?? $group_id ),
	];
}

function reci_demo_remote_group_assets( string $group_id, array $group_definition ): array {
	$assets = $group_definition['assets'] ?? [];
	if ( ! is_array( $assets ) ) {
		return [];
	}

	$normalized = [];
	foreach ( $assets as $asset ) {
		if ( ! is_array( $asset ) ) {
			continue;
		}

		$path = isset( $asset['path'] ) ? ltrim( (string) $asset['path'], '/' ) : '';
		$url  = isset( $asset['url'] ) ? esc_url_raw( (string) $asset['url'] ) : '';
		if ( '' === $path || '' === $url ) {
			continue;
		}

		$normalized[] = [
			'path' => $path,
			'url'  => $url,
		];
	}

	return $normalized;
}

function reci_demo_get_registered_asset( string $path ): array {
	$registry = reci_demo_get_asset_registry();
	$entry = $registry[ $path ] ?? [];
	return is_array( $entry ) ? $entry : [];
}

function reci_demo_import_image_asset( string $path ): array {
	$existing = reci_demo_get_registered_asset( $path );
	if ( ! empty( $existing['attachment_id'] ) ) {
		return reci_demo_result_entry( 'skipped', $path, 'Image already imported.', [ 'path' => $path ] );
	}

	$imgs = [];
	$error_message = '';
	$attachment_id = reci_demo_sideload_image( $path, 0, $imgs, $error_message );
	if ( ! $attachment_id ) {
		return reci_demo_result_entry( 'failed', $path, 'Image import failed.' . ( '' !== $error_message ? ' ' . $error_message : '' ), [ 'path' => $path ] );
	}

	$entry = [
		'attachment_id' => $attachment_id,
		'url'           => wp_get_attachment_url( $attachment_id ) ?: '',
		'path'          => $path,
		'imported_at'   => time(),
	];

	$registry = reci_demo_get_asset_registry();
	$registry[ $path ] = $entry;
	reci_demo_set_asset_registry( $registry );

	return reci_demo_result_entry( 'completed', $path, 'Image imported successfully.', [ 'path' => $path ] );
}

function reci_demo_import_remote_image_asset( array $asset ): array {
	$path = ltrim( (string) ( $asset['path'] ?? '' ), '/' );
	$url  = esc_url_raw( (string) ( $asset['url'] ?? '' ) );

	if ( '' === $path || '' === $url ) {
		return reci_demo_result_entry( 'failed', 'Remote image', 'Remote image asset is missing a path or URL.' );
	}

	$existing = reci_demo_get_registered_asset( $path );
	if ( ! empty( $existing['attachment_id'] ) ) {
		return reci_demo_result_entry( 'skipped', $path, 'Remote image already imported.', [ 'path' => $path ] );
	}

	$imgs = [];
	$error_message = '';
	$attachment_id = reci_demo_sideload_image_from_url( $path, $url, 0, $imgs, $error_message );
	if ( ! $attachment_id ) {
		return reci_demo_result_entry( 'failed', $path, 'Remote image import failed.' . ( '' !== $error_message ? ' ' . $error_message : '' ), [ 'path' => $path ] );
	}

	$entry = [
		'attachment_id' => $attachment_id,
		'url'           => wp_get_attachment_url( $attachment_id ) ?: '',
		'path'          => $path,
		'source_url'    => $url,
		'imported_at'   => time(),
	];

	$registry = reci_demo_get_asset_registry();
	$registry[ $path ] = $entry;
	reci_demo_set_asset_registry( $registry );

	return reci_demo_result_entry( 'completed', $path, 'Remote image imported successfully.', [ 'path' => $path ] );
}

function reci_demo_import_remote_archive_group( array $step ): array {
	$url   = esc_url_raw( (string) ( $step['url'] ?? '' ) );
	$root  = ltrim( (string) ( $step['root'] ?? '' ), '/' );
	$registry_prefix = ltrim( (string) ( $step['registry_prefix'] ?? $root ), '/' );
	$label = (string) ( $step['label'] ?? ( $step['group'] ?? 'Remote archive' ) );

	if ( '' === $url ) {
		return reci_demo_result_entry( 'failed', $label, 'Remote archive URL is missing.' );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	$temp_zip = download_url( $url, 60 );
	if ( is_wp_error( $temp_zip ) ) {
		return reci_demo_result_entry( 'failed', $label, 'Archive download failed: ' . $temp_zip->get_error_message() );
	}

	$extract_dir = trailingslashit( get_temp_dir() ) . 'reci-demo-' . wp_generate_password( 8, false, false );
	wp_mkdir_p( $extract_dir );

	$unzipped = unzip_file( $temp_zip, $extract_dir );
	@unlink( $temp_zip );
	if ( is_wp_error( $unzipped ) ) {
		reci_demo_delete_directory( $extract_dir );
		return reci_demo_result_entry( 'failed', $label, 'Archive extraction failed: ' . $unzipped->get_error_message() );
	}

	$base_dir = '' !== $root ? trailingslashit( $extract_dir ) . $root : $extract_dir;
	if ( ! is_dir( $base_dir ) ) {
		reci_demo_delete_directory( $extract_dir );
		return reci_demo_result_entry( 'failed', $label, 'Archive root not found after extraction.' );
	}

	$files = reci_demo_collect_importable_files_from_directory( $base_dir );
	if ( empty( $files ) ) {
		reci_demo_delete_directory( $extract_dir );
		return reci_demo_result_entry( 'failed', $label, 'Archive contained no importable image files.' );
	}

	$completed = 0;
	$skipped   = 0;
	$failed    = [];
	$imgs      = [];

	foreach ( $files as $file_path ) {
		$relative = ltrim( str_replace( trailingslashit( $base_dir ), '', $file_path ), '/' );
		$registry_key = ( '' !== $registry_prefix ? trailingslashit( $registry_prefix ) : '' ) . str_replace( DIRECTORY_SEPARATOR, '/', $relative );

		$existing = reci_demo_get_registered_asset( $registry_key );
		if ( ! empty( $existing['attachment_id'] ) ) {
			$skipped++;
			continue;
		}

		$error_message = '';
		$attachment_id = reci_demo_sideload_image_from_path( $registry_key, $file_path, 0, $imgs, $error_message );
		if ( ! $attachment_id ) {
			$failed[] = $registry_key . ( '' !== $error_message ? ' (' . $error_message . ')' : '' );
			continue;
		}

		$registry = reci_demo_get_asset_registry();
		$registry[ $registry_key ] = [
			'attachment_id' => $attachment_id,
			'url'           => wp_get_attachment_url( $attachment_id ) ?: '',
			'path'          => $registry_key,
			'source_url'    => $url,
			'imported_at'   => time(),
		];
		reci_demo_set_asset_registry( $registry );
		$completed++;
	}

	reci_demo_delete_directory( $extract_dir );

	if ( ! empty( $failed ) ) {
		return reci_demo_result_entry( 'failed', $label, sprintf( 'Archive imported with %1$d successes, %2$d skipped, %3$d failures.', $completed, $skipped, count( $failed ) ), [ 'activity' => $failed ] );
	}

	return reci_demo_result_entry( 'completed', $label, sprintf( 'Archive imported successfully (%1$d imported, %2$d skipped).', $completed, $skipped ) );
}

function reci_demo_resolve_registry_url( string $path ): string {
	$entry = reci_demo_get_registered_asset( $path );
	return (string) ( $entry['url'] ?? '' );
}

function reci_demo_attach_registry_thumbnail( int $post_id, string $path ): bool {
	$entry = reci_demo_get_registered_asset( $path );

	$attachment_id = (int) ( $entry['attachment_id'] ?? 0 );
	if ( $attachment_id > 0 ) {
		delete_post_meta( $post_id, '_reci_reflection_background_image_url' );
		update_post_meta( $post_id, '_reci_reflection_background_image_id', $attachment_id );
		return (bool) set_post_thumbnail( $post_id, $attachment_id );
	}

	return false;
}

function reci_demo_validate_required_assets( array $paths ): array {
	$missing = [];
	foreach ( $paths as $path ) {
		$entry = reci_demo_get_registered_asset( $path );
		if ( empty( $entry['attachment_id'] ) ) {
			$missing[] = $path;
		}
	}
	return $missing;
}

function reci_demo_import_reflection_item( array $item ): array {
	$slug = (string) ( $item['slug'] ?? '' );
	$title = (string) ( $item['title'] ?? $slug );
	$activity = [ 'Validating reflection assets' ];
	if ( '' === $slug ) {
		return reci_demo_result_entry( 'failed', 'Reflection', 'Missing reflection slug.' );
	}
	if ( get_page_by_path( $slug, OBJECT, 'reci_reflection' ) ) {
		return reci_demo_result_entry( 'skipped', $title, 'Reflection already exists.', [ 'slug' => $slug ] );
	}

	$required = [];
	if ( 'style' === ( $item['kind'] ?? '' ) ) {
		$folder = (string) ( $item['folder'] ?? '' );
		if ( $folder !== '' && ! empty( $item['featured'] ) ) {
			$required[] = 'site/reflections/' . $folder . '/' . $item['featured'];
		}
		foreach ( (array) ( $item['inner'] ?? [] ) as $file ) {
			$required[] = 'site/reflections/' . $folder . '/' . $file;
		}
	} else {
		$required = array_values( (array) ( $item['assets'] ?? [] ) );
	}

	$missing = reci_demo_validate_required_assets( $required );
	if ( ! empty( $missing ) ) {
		return reci_demo_result_entry( 'failed', $title, 'Missing required reflection assets: ' . implode( ', ', $missing ), [ 'slug' => $slug, 'activity' => $activity ] );
	}

	$bootstrap = reci_demo_bootstrap_taxonomies();
	$imgs = [];
	$activity[] = 'Creating reflection post';
	reci_demo_insert_post( 'reci_reflection', [
		'slug'     => $slug,
		'title'    => $title,
		'excerpt'  => (string) ( $item['excerpt'] ?? '' ),
		'content'  => '',
		'category' => (string) ( $item['category'] ?? '' ),
		'topics'   => (array) ( $item['topics'] ?? [] ),
		'spheres'  => (array) ( $item['spheres'] ?? [] ),
		'meta'     => [],
	], $bootstrap['topics'], $bootstrap['locations'], $imgs );

	$post = get_page_by_path( $slug, OBJECT, 'reci_reflection' );
	if ( ! $post ) {
		return reci_demo_result_entry( 'failed', $title, 'Could not create reflection post.', [ 'slug' => $slug, 'activity' => $activity ] );
	}

	$featured_path = 'style' === ( $item['kind'] ?? '' )
		? 'site/reflections/' . (string) ( $item['folder'] ?? '' ) . '/' . (string) ( $item['featured'] ?? '' )
		: (string) ( $item['featured'] ?? '' );

	$activity[] = 'Attaching featured image';
	if ( '' === $featured_path || ! reci_demo_attach_registry_thumbnail( (int) $post->ID, $featured_path ) ) {
		wp_delete_post( $post->ID, true );
		return reci_demo_result_entry( 'failed', $title, 'Could not attach featured image from imported assets.', [ 'slug' => $slug, 'activity' => $activity ] );
	}

	if ( 'style' === ( $item['kind'] ?? '' ) ) {
		$urls = [];
		foreach ( (array) ( $item['inner'] ?? [] ) as $file ) {
			$urls[] = reci_demo_resolve_registry_url( 'site/reflections/' . (string) ( $item['folder'] ?? '' ) . '/' . $file );
		}
		$blueprint = reci_demo_blueprint_inject_images( reci_demo_blueprint_from_style( (string) ( $item['style'] ?? '' ) ), array_values( array_filter( $urls ) ) );
	} elseif ( 'black-wall' === ( $item['kind'] ?? '' ) ) {
		$img = [];
		foreach ( (array) ( $item['assets'] ?? [] ) as $path ) {
			$img[ basename( $path ) ] = reci_demo_resolve_registry_url( $path );
		}
		$blueprint = reci_demo_black_wall_street_blueprint( $img );
	} else {
		$img = [];
		foreach ( (array) ( $item['assets'] ?? [] ) as $path ) {
			$img[ basename( $path ) ] = reci_demo_resolve_registry_url( $path );
		}
		$blueprint = reci_demo_we_humans_blueprint( $img );
	}

	$activity[] = 'Writing reflection blueprint';
	update_post_meta( $post->ID, '_reci_reflection_blueprint', wp_slash( wp_json_encode( $blueprint, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
	if ( class_exists( 'RECI_Reflection_Content_Service' ) ) {
		RECI_Reflection_Content_Service::invalidate_cache( (int) $post->ID );
	}
	update_option( 'reci_demo_installed', true );

	return reci_demo_result_entry( 'completed', $title, 'Reflection imported successfully.', [ 'slug' => $slug, 'activity' => $activity ] );
}

function reci_demo_process_next_job_step( array $job ): array {
	$queue = array_values( is_array( $job['queue'] ?? null ) ? $job['queue'] : [] );
	$cursor = (int) ( $job['cursor'] ?? 0 );
	$total = count( $queue );

	if ( $cursor >= $total ) {
		$job['finished'] = true;
		$job['running'] = false;
		$job['current_label'] = 'Import complete';
		$job = reci_demo_append_activity( $job, 'Import complete.' );
		return $job;
	}

	$step = $queue[ $cursor ];
	$result = reci_demo_result_entry( 'failed', 'Import step', 'Unknown step.' );

	if ( 'import_image' === ( $step['type'] ?? '' ) ) {
		$path = (string) ( $step['path'] ?? '' );
		$job['current_label'] = 'Importing image: ' . $path;
		$job = reci_demo_append_activity( $job, $job['current_label'] );
		$result = reci_demo_import_image_asset( $path );
	} elseif ( 'import_remote_image' === ( $step['type'] ?? '' ) ) {
		$asset = is_array( $step['asset'] ?? null ) ? $step['asset'] : [];
		$path = (string) ( $asset['path'] ?? 'remote-image' );
		$job['current_label'] = 'Importing remote image: ' . $path;
		$job = reci_demo_append_activity( $job, $job['current_label'] );
		$result = reci_demo_import_remote_image_asset( $asset );
	} elseif ( 'import_remote_archive' === ( $step['type'] ?? '' ) ) {
		$job['current_label'] = 'Importing remote archive: ' . (string) ( $step['label'] ?? $step['group'] ?? 'Archive' );
		$job = reci_demo_append_activity( $job, $job['current_label'] );
		$result = reci_demo_import_remote_archive_group( $step );
	} elseif ( 'import_reflection' === ( $step['type'] ?? '' ) ) {
		$item = is_array( $step['item'] ?? null ) ? $step['item'] : [];
		$job['current_label'] = 'Creating reflection: ' . (string) ( $item['title'] ?? $item['slug'] ?? 'Reflection' );
		$job = reci_demo_append_activity( $job, $job['current_label'] );
		$result = reci_demo_import_reflection_item( $item );
	} elseif ( 'skip' === ( $step['type'] ?? '' ) ) {
		$message = (string) ( $step['message'] ?? 'Skipped.' );
		$group = (string) ( $step['group'] ?? '' );
		$label = (string) ( reci_demo_content_types()[ $group ]['label'] ?? $group );
		$job['current_label'] = $label;
		$job = reci_demo_append_activity( $job, $job['current_label'] );
		$result = reci_demo_result_entry( 'completed', $label, $message );
	} elseif ( 'import_legacy_group' === ( $step['type'] ?? '' ) ) {
		$group = (string) ( $step['group'] ?? '' );
		$label = (string) ( reci_demo_content_types()[ $group ]['label'] ?? $group );
		$job['current_label'] = 'Installing group: ' . $label;
		$job = reci_demo_append_activity( $job, $job['current_label'] );
		reci_install_demo_content( [ $group ] );
		$result = reci_demo_result_entry( 'completed', $label, 'Legacy content group imported.' );
	}

	$bucket = 'failed';
	if ( 'completed' === $result['status'] ) {
		$bucket = 'completed';
	} elseif ( 'skipped' === $result['status'] ) {
		$bucket = 'skipped';
	}
	$job[ $bucket ][] = $result;
	foreach ( (array) ( $result['activity'] ?? [] ) as $activity_message ) {
		$job = reci_demo_append_activity( $job, (string) $activity_message );
	}
	$job = reci_demo_append_activity( $job, $result['label'] . ': ' . $result['message'] );
	$job['cursor'] = $cursor + 1;
	$job['updated'] = time();
	$job['running'] = $job['cursor'] < $total;
	$job['finished'] = ! $job['running'];
	if ( $job['finished'] ) {
		$job['current_label'] = 'Import complete';
	}

	return $job;
}

function reci_demo_ajax_start_import(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'Unauthorized.' ], 403 );
	}

	check_ajax_referer( 'reci_demo_action', 'nonce' );

	$selected = isset( $_POST['selected'] ) && is_array( $_POST['selected'] )
		? array_values( array_unique( array_map( 'sanitize_key', wp_unslash( $_POST['selected'] ) ) ) )
		: [];

	if ( empty( $selected ) ) {
		wp_send_json_error( [ 'message' => 'Select at least one import group.' ], 400 );
	}

	$job = [
		'id'          => wp_generate_uuid4(),
		'selected'    => $selected,
		'queue'       => reci_demo_build_import_queue( $selected ),
		'cursor'      => 0,
		'completed'   => [],
		'failed'      => [],
		'skipped'     => [],
		'activity'    => [],
		'current_label' => 'Preparing import queue…',
		'running'     => true,
		'finished'    => false,
		'started'     => time(),
		'updated'     => time(),
	];
	$job = reci_demo_append_activity( $job, 'Import job created.' );
	if ( empty( $job['queue'] ) ) {
		$job['running'] = false;
		$job['finished'] = true;
		$job['current_label'] = 'Nothing to import';
		$job = reci_demo_append_activity( $job, 'No import steps were generated.' );
	}

	reci_demo_set_job( $job );
	wp_send_json_success( reci_demo_present_job_state( $job ) );
}

function reci_demo_ajax_process_step(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'Unauthorized.' ], 403 );
	}

	check_ajax_referer( 'reci_demo_action', 'nonce' );

	$job = reci_demo_get_job();
	if ( empty( $job ) ) {
		wp_send_json_error( [ 'message' => 'No active import job.' ], 404 );
	}

	$job = reci_demo_process_next_job_step( $job );
	reci_demo_set_job( $job );

	// Clean output buffer to prevent PHP warnings/notices from breaking the JSON response
	if ( ob_get_length() ) {
		ob_clean();
	}

	wp_send_json_success( reci_demo_present_job_state( $job ) );
}

// ---------------------------------------------------------------------------
// Core installer
// ---------------------------------------------------------------------------

function reci_install_demo_content( array $only_types = [] ): void {
	$all  = array_keys( reci_demo_content_types() );
	$want = $only_types ? array_intersect( $only_types, $all ) : $all;
	if ( empty( $want ) ) {
		return;
	}

	$do_all = $want === $all;

	// Taxonomy terms first.
	$topics = reci_demo_ensure_terms( 'reci_topic', [
		'Systemic Racism',
		'Intersectionality',
		'Cultural Identity',
		'Workplace Equity',
		'Community Action',
		'Education',
		'Health Disparities',
		'Criminal Justice',
		'Indigenous Rights',
		'Technology & Equity',
	] );

	$category_names = [
		'Systemic Racism',
		'Intersectionality',
		'Cultural Identity',
		'Workplace Equity',
		'Community Action',
		'Education',
		'Health Disparities',
		'Criminal Justice',
		'Indigenous Rights',
		'Technology & Equity',
	];
	$categories = [];
	foreach ( $category_names as $name ) {
		$term = term_exists( $name, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'category' );
		}
		if ( ! is_wp_error( $term ) ) {
			$categories[ $name ] = (int) ( $term['term_id'] ?? $term );
		}
	}

	$locations = reci_demo_ensure_terms( 'reci_location', [
		'Pittsburgh',
		'Allegheny County',
		'Pennsylvania',
		'National',
	] );

	reci_demo_ensure_terms( 'reci_sphere', [
		'Recognizing Racial Oppression',
		'Embracing Racial Diversity',
		'Building Racial Empathy',
		'Fostering Racial Literacy',
		'Acknowledging Racial Trauma',
		'Gauging Racial Inequities',
	] );

	reci_demo_ensure_terms( 'reci_practice_focus', [
		'Framework / Model',
		'Community-Based Approach',
		'Policy / Legislation',
		'Curriculum / Training',
		'Practice / Intervention',
		'Research / Evaluation',
		'Organizational Strategy',
		'Other',
	] );

	reci_demo_ensure_terms( 'reci_show', [
		'Healing Overflow with Dr Toy',
	] );
	// Delete seed flag so missing spheres/sdgs get created on re-install.
	delete_option( 'reci_media_hub_spheres_seeded' );
	delete_option( 'reci_media_hub_sdgs_seeded' );
	if ( function_exists( 'reci_media_hub_seed_default_spheres' ) ) {
		reci_media_hub_seed_default_spheres();
	}
	if ( function_exists( 'reci_media_hub_seed_default_sdgs' ) ) {
		reci_media_hub_seed_default_sdgs();
	}
	if ( function_exists( 'reci_media_hub_backfill_sdg_descriptions' ) ) {
		reci_media_hub_backfill_sdg_descriptions();
	}
	reci_media_hub_seed_default_shows();

	// Image map: file basename (in demo-content/images/) => attachment ID (lazily sideloaded).
	$imgs = [];

	$article_asset_url = static function ( string $path ): string {
		return get_template_directory_uri() . '/demo-content/images/' . ltrim( $path, '/' );
	};

	$article_figure = static function ( string $path, string $alt, string $caption = '' ) use ( $article_asset_url ): string {
		$html  = '<figure class="wp-block-image size-large">';
		$html .= '<img src="' . esc_url( $article_asset_url( $path ) ) . '" alt="' . esc_attr( $alt ) . '" />';
		if ( $caption !== '' ) {
			$html .= '<figcaption>' . esc_html( $caption ) . '</figcaption>';
		}
		$html .= '</figure>';

		return $html;
	};

	$article_content = static function ( string $title, string $figure_path = '', string $figure_alt = '', string $figure_caption = '' ) use ( $article_figure ): string {
		$figure_html = $figure_path !== '' ? $article_figure( $figure_path, $figure_alt, $figure_caption ) : '';
		return reci_demo_render_markdown_article( $title, $figure_html );
	};

	$article_excerpt = static function ( string $title ): string {
		return reci_demo_get_markdown_article_excerpt( $title );
	};

	// Articles.
	$articles = [
		[
			'slug'     => 'aging-racism-equity-consciousness-pittsburgh',
			'title'    => 'Aging, Racism, and Equity Consciousness in Pittsburgh',
			'sdgs'     => [ 'Good Health and Well-being', 'Reduced Inequalities' ],
			'author_name' => 'Anuj Peri',
			'excerpt'  => $article_excerpt( 'Aging, Racism, and Equity Consciousness in Pittsburgh' ),
			'content'  => $article_content( 'Aging, Racism, and Equity Consciousness in Pittsburgh', 'site/articles/aging-racism-equity-consciousness-pittsburgh/community.webp', 'Community members gathered during a racial healing event.', 'Intergenerational spaces can help translate racial equity principles into lived community care.' ),
			'category' => 'Health Disparities',
			'topics'   => [ 'Health Disparities', 'Community Action', 'Cultural Competence' ],
			'tags'     => [ 'racialized aging', 'intergenerational wellness', 'Hill District', 'elder equity' ],
			'spheres'  => [ 'Building Racial Empathy' ],
			'image'    => 'site/articles/aging-racism-equity-consciousness-pittsburgh/featured.webp',
			'post_date'=> '2024-06-22 16:02:08',
			'meta'     => [ '_post_read_time_label' => '6 min read' ],
		],
		[
			'slug'     => 'freedom-house',
			'title'    => 'Freedom House',
			'sdgs'     => [ 'Good Health and Well-being', 'Sustainable Cities and Communities' ],
			'author_name' => 'Alexia Wagurak',
			'excerpt'  => $article_excerpt( 'Freedom House' ),
			'content'  => $article_content( 'Freedom House', 'site/articles/freedom-house/community.webp', 'Community gathering connected to public service and healing.', 'Freedom House became trusted because it served Black communities with speed, dignity, and care.' ),
			'category' => 'Public Service',
			'topics'   => [ 'Public Service', 'Health Disparities', 'Community Action' ],
			'tags'     => [ 'Freedom House', 'EMS history', 'Hill District', 'emergency medicine' ],
			'spheres'  => [ 'Recognizing Racial Oppression' ],
			'image'    => 'site/articles/freedom-house/featured.webp',
			'post_date'=> '2024-04-20 13:27:45',
			'meta'     => [ '_post_read_time_label' => '7 min read' ],
		],
		[
			'slug'     => 'recmh-origin',
			'title'    => 'RECMH Origin',
			'sdgs'     => [ 'Industry, Innovation and Infrastructure', 'Reduced Inequalities' ],
			'excerpt'  => $article_excerpt( 'RECMH Origin' ),
			'content'  => $article_content( 'RECMH Origin', 'site/articles/recmh-origin/collaboration.webp', 'Collaborative RECI event with participants in discussion.', 'The media hub emerged from a collaborative attempt to make racial equity research more usable and visible.' ),
			'category' => 'Technology & Equity',
			'topics'   => [ 'Technology & Equity', 'Education', 'Community Action' ],
			'tags'     => [ 'media hub', 'RECI history', 'UN forum', 'knowledge sharing' ],
			'spheres'  => [ 'Embracing Racial Diversity' ],
			'image'    => 'site/articles/recmh-origin/featured.webp',
			'post_date'=> '2024-05-08 15:13:11',
			'meta'     => [ '_post_read_time_label' => '4 min read' ],
		],
		[
			'slug'     => 'minority-language-genocide-cultural-awareness',
			'title'    => 'Minority Language Genocide and Cultural Awareness',
			'sdgs'     => [ 'Reduced Inequalities', 'Peace, Justice and Strong Institutions' ],
			'author_name' => 'Tracy Wang',
			'excerpt'  => $article_excerpt( 'Minority Language Genocide and Cultural Awarness' ),
			'content'  => $article_content( 'Minority Language Genocide and Cultural Awarness', 'site/articles/minority-language-genocide-cultural-awareness/global-dialogue.webp', 'International conference gathering focused on cross-cultural dialogue.', 'Language justice is inseparable from cultural survival and equitable participation.' ),
			'category' => 'Cultural Competence',
			'topics'   => [ 'Cultural Identity', 'Education', 'Inclusion' ],
			'tags'     => [ 'language justice', 'cultural erasure', 'assimilation policy', 'indigenous languages' ],
			'spheres'  => [ 'Recognizing Racial Oppression' ],
			'image'    => 'site/articles/minority-language-genocide-cultural-awareness/featured.webp',
			'post_date'=> '2026-01-16 10:36:05',
			'meta'     => [ '_post_read_time_label' => '7 min read' ],
		],
		[
			'slug'     => 'the-pennsylvania-black-maternal-health-caucus',
			'title'    => 'The Pennsylvania Black Maternal Health Caucus: What It Is and Why You Should Care',
			'sdgs'     => [ 'Good Health and Well-being', 'Gender Equality' ],
			'author_name' => 'Vivian Greenwood',
			'excerpt'  => $article_excerpt( 'The Pennsylvania Black Maternal Health Caucus: What It Is and Why You Should Care' ),
			'content'  => $article_content( 'The Pennsylvania Black Maternal Health Caucus: What It Is and Why You Should Care', 'site/articles/pennsylvania-black-maternal-health-caucus/advocacy.webp', 'Community and advocacy gathering connected to health equity organizing.', 'Maternal health equity depends on policy, public awareness, and community-rooted advocacy.' ),
			'category' => 'Health Disparities',
			'topics'   => [ 'Health Disparities', 'Public Service', 'Community Action' ],
			'tags'     => [ 'maternal health', 'Pennsylvania policy', 'doula access', 'birth equity' ],
			'spheres'  => [ 'Gauging Racial Inequities' ],
			'image'    => 'site/articles/pennsylvania-black-maternal-health-caucus/featured.webp',
			'post_date'=> '2024-02-25 15:20:56',
			'meta'     => [ '_post_read_time_label' => '8 min read' ],
		],
		[
			'slug'     => 'how-neuroscience-and-community-based-practices-advance-racial-equity',
			'title'    => 'How Neuroscience and Community-Based Practices Advance Racial Equity in Research',
			'sdgs'     => [ 'Good Health and Well-being', 'Reduced Inequalities' ],
			'author_name' => 'Arshia Sista',
			'excerpt'  => $article_excerpt( 'How can Neuroscience and Community-Based Practices Advance Racial Equity in Research' ),
			'content'  => $article_content( 'How can Neuroscience and Community-Based Practices Advance Racial Equity in Research', 'site/articles/neuroscience-community-based-practices-racial-equity-research/lab-dialogue.webp', 'Participants in discussion during a research-oriented event.', 'Equity-centered neuroscience requires accountability to communities, not just better lab instruments.' ),
			'category' => 'Technology & Equity',
			'topics'   => [ 'Technology & Equity', 'Education', 'Health Disparities' ],
			'tags'     => [ 'neuroscience', 'CBPR', 'SCBT', 'research ethics' ],
			'spheres'  => [ 'Gauging Racial Inequities' ],
			'image'    => 'site/articles/neuroscience-community-based-practices-racial-equity-research/featured.webp',
			'post_date'=> '2026-02-08 20:15:48',
			'meta'     => [ '_post_read_time_label' => '7 min read' ],
		],
		[
			'slug'     => 'neuroscience-cant-fix-racism-right-questions',
			'title'    => 'Neuroscience Can’t Fix Racism—But It Can Help If We Start Asking the Right Questions',
			'sdgs'     => [ 'Good Health and Well-being', 'Reduced Inequalities' ],
			'author_name' => 'Arshia Sista',
			'excerpt'  => $article_excerpt( 'Neuroscience Can’t Fix Racism—But It Can Help If We Start Asking the Right Questions' ),
			'content'  => $article_content( 'Neuroscience Can’t Fix Racism—But It Can Help If We Start Asking the Right Questions', 'site/articles/neuroscience-cant-fix-racism-right-questions/research-community.webp', 'Researchers and participants in a collaborative event space.', 'Equity-centered neuroscience asks who is represented, who benefits, and what structural change the research supports.' ),
			'category' => 'Technology & Equity',
			'topics'   => [ 'Technology & Equity', 'Systemic Racism', 'Education' ],
			'tags'     => [ 'implicit bias', 'fMRI', 'racial anxiety', 'community research' ],
			'spheres'  => [ 'Recognizing Racial Oppression' ],
			'image'    => 'site/articles/neuroscience-cant-fix-racism-right-questions/featured.webp',
			'post_date'=> '2026-04-02 23:56:19',
			'meta'     => [ '_post_read_time_label' => '6 min read' ],
		],
		[
			'slug'     => 'understanding-systemic-racism-comprehensive-overview',
			'title'    => 'Understanding Systemic Racism: A Comprehensive Overview',
			'sdgs'     => [ 'Reduced Inequalities', 'Peace, Justice and Strong Institutions' ],
			'excerpt'  => $article_excerpt( 'Understanding Systemic Racism: A Comprehensive Overview' ),
			'content'  => $article_content( 'Understanding Systemic Racism: A Comprehensive Overview', 'site/articles/understanding-systemic-racism-comprehensive-overview/community.webp', 'Community members in conversation during a public event.', 'Systemic racism becomes clearer when policy, data, and lived experience are viewed together.' ),
			'category' => 'Systemic Racism',
			'topics'   => [ 'Systemic Racism', 'Education', 'Community Action' ],
			'tags'     => [ 'systems change', 'equity framework', 'institutional racism', 'community knowledge' ],
			'spheres'  => [ 'Recognizing Racial Oppression' ],
			'image'    => 'site/articles/understanding-systemic-racism-comprehensive-overview/featured.webp',
			'post_date'=> '2026-05-24 21:58:27',
			'meta'     => [ '_post_read_time_label' => '4 min read' ],
		],
		[
			'slug'     => 'exploring-engagement-insights-racial-equity-consciousness-institute',
			'title'    => 'Exploring Engagement And Insights From The Racial Equity Consciousness Institute',
			'sdgs'     => [ 'Quality Education', 'Reduced Inequalities' ],
			'excerpt'  => $article_excerpt( 'Exploring Engagement And Insights From The Racial Equity Consciousness Institute' ),
			'content'  => $article_content( 'Exploring Engagement And Insights From The Racial Equity Consciousness Institute', 'site/articles/exploring-engagement-insights-racial-equity-consciousness-institute/community.webp', 'Participants gathered in a reflection-centered RECI event.', 'Student engagement with RECI highlights the role of reflection, dialogue, and practical application.' ),
			'category' => 'Education',
			'topics'   => [ 'Education', 'Community Action', 'Cultural Competence' ],
			'tags'     => [ 'student engagement', 'qualitative research', 'RECI cohorts', 'equity learning' ],
			'spheres'  => [ 'Building Racial Empathy' ],
			'image'    => 'site/articles/exploring-engagement-insights-racial-equity-consciousness-institute/featured.webp',
			'post_date'=> '2024-03-14 07:47:04',
			'meta'     => [ '_post_read_time_label' => '3 min read' ],
		],
		[
			'slug'     => 'cultivating-equity-minded-leaders-every-level',
			'title'    => 'Cultivating Equity-Minded Leaders At Every Level',
			'sdgs'     => [ 'Decent Work and Economic Growth', 'Reduced Inequalities' ],
			'excerpt'  => $article_excerpt( 'Cultivating Equity-Minded Leaders At Every Level' ),
			'content'  => $article_content( 'Cultivating Equity-Minded Leaders At Every Level', 'site/articles/cultivating-equity-minded-leaders-every-level/community.webp', 'Participants in a leadership and equity gathering.', 'Equity-minded leadership requires structure, trust, and long-term accountability.' ),
			'category' => 'Workplace Equity',
			'topics'   => [ 'Workplace Equity', 'Education', 'Community Action' ],
			'tags'     => [ 'leadership development', 'equity practice', 'institutional culture', 'shared accountability' ],
			'spheres'  => [ 'Embracing Racial Diversity' ],
			'image'    => 'site/articles/cultivating-equity-minded-leaders-every-level/featured.webp',
			'post_date'=> '2024-01-19 16:34:17',
			'meta'     => [ '_post_read_time_label' => '3 min read' ],
		],
		[
			'slug'     => 'new-research-highlights-health-disparities-allegheny-county',
			'title'    => 'New Research Highlights Health Disparities Across Allegheny County',
			'sdgs'     => [ 'Good Health and Well-being', 'Reduced Inequalities' ],
			'excerpt'  => $article_excerpt( 'New Research Highlights Health Disparities Across Allegheny County' ),
			'content'  => $article_content( 'New Research Highlights Health Disparities Across Allegheny County', 'site/articles/new-research-highlights-health-disparities-allegheny-county/community.webp', 'Public-facing health equity event in community space.', 'Health disparities research matters most when it drives action in the places most affected.' ),
			'category' => 'Health Disparities',
			'topics'   => [ 'Health Disparities', 'Technology & Equity', 'Public Service' ],
			'tags'     => [ 'Allegheny County', 'public health data', 'equity research', 'policy response' ],
			'spheres'  => [ 'Gauging Racial Inequities' ],
			'image'    => 'site/articles/new-research-highlights-health-disparities-allegheny-county/featured.webp',
			'post_date'=> '2025-12-01 22:00:11',
			'meta'     => [ '_post_read_time_label' => '3 min read' ],
		],
		[
			'slug'     => 'community-pulse-pittsburgh-racial-justice-landscape-2025',
			'title'    => 'Community Pulse: Pittsburgh’s Racial Justice Landscape In 2025',
			'sdgs'     => [ 'Sustainable Cities and Communities', 'Reduced Inequalities' ],
			'excerpt'  => $article_excerpt( 'Community Pulse: Pittsburgh’s Racial Justice Landscape In 2025' ),
			'content'  => $article_content( 'Community Pulse: Pittsburgh’s Racial Justice Landscape In 2025', 'site/articles/community-pulse-pittsburgh-racial-justice-landscape-2025/community.webp', 'Community members gathered in a racial justice event.', 'Community pulse reporting is strongest when data is grounded in everyday lived realities.' ),
			'category' => 'Technology & Equity',
			'topics'   => [ 'Community Action', 'Technology & Equity', 'Access' ],
			'tags'     => [ 'Pittsburgh data', 'racial justice indicators', 'community pulse', 'civic accountability' ],
			'spheres'  => [ 'Gauging Racial Inequities' ],
			'image'    => 'site/articles/community-pulse-pittsburgh-racial-justice-landscape-2025/featured.webp',
			'post_date'=> '2025-07-05 13:59:36',
			'meta'     => [ '_post_read_time_label' => '4 min read' ],
		],
		[
			'slug'     => 'pennsylvania-black-maternal-health-caucus',
			'title'    => 'The Pennsylvania Black Maternal Health Caucus',
			'author_name' => reci_demo_get_markdown_article_author( 'The Pennsylvania Black Maternal Health Caucus' ),
			'excerpt'  => $article_excerpt( 'The Pennsylvania Black Maternal Health Caucus' ),
			'content'  => $article_content( 'The Pennsylvania Black Maternal Health Caucus', 'site/articles/pennsylvania-black-maternal-health-caucus-overview/community.webp', 'Community members engaged in maternal health advocacy.', 'Legislative caucuses matter when they connect structural problems to concrete support and accountability.' ),
			'category' => 'Health Disparities',
			'topics'   => [ 'Health Disparities', 'Public Service', 'Education' ],
			'tags'     => [ 'maternal health caucus', 'Pennsylvania legislature', 'health equity', 'advocacy' ],
			'spheres'  => [ 'Gauging Racial Inequities' ],
			'sdgs'     => [ 'Good Health and Well-being' ],
			'image'    => 'site/articles/pennsylvania-black-maternal-health-caucus-overview/featured.webp',
			'post_date'=> '2025-10-07 20:02:27',
			'meta'     => [ '_post_read_time_label' => '5 min read' ],
		],
		[
			'slug'     => 'arts-culture-community-healing-racial-trauma',
			'title'    => 'Arts, Culture, And Community Healing After Racial Trauma',
			'sdgs'     => [ 'Good Health and Well-being', 'Sustainable Cities and Communities' ],
			'excerpt'  => $article_excerpt( 'Arts, Culture, And Community Healing After Racial Trauma' ),
			'content'  => $article_content( 'Arts, Culture, And Community Healing After Racial Trauma', 'site/articles/arts-culture-community-healing-racial-trauma/community.webp', 'Racial healing day gathering with shared cultural practice.', 'Creative practice and cultural expression can become tools for collective racial healing.' ),
			'category' => 'Cultural Competence',
			'topics'   => [ 'Cultural Identity', 'Community Action', 'Inclusion' ],
			'tags'     => [ 'racial healing', 'arts and culture', 'collective memory', 'community care' ],
			'spheres'  => [ 'Building Racial Empathy' ],
			'image'    => 'site/articles/arts-culture-community-healing-racial-trauma/featured.webp',
			'post_date'=> '2024-02-27 12:08:57',
			'meta'     => [ '_post_read_time_label' => '3 min read' ],
		],
		[
			'slug'     => 'building-equity-centered-education-pipeline',
			'title'    => 'Building An Equity-Centered Education Pipeline',
			'excerpt'  => $article_excerpt( 'Building An Equity-Centered Education Pipeline' ),
			'content'  => $article_content( 'Building An Equity-Centered Education Pipeline', 'site/articles/building-equity-centered-education-pipeline/community.webp', 'Education-focused gathering with community participants.', 'Equity-centered education pipelines depend on trust, strategy, and long-term collaboration across systems.' ),
			'category' => 'Education',
			'topics'   => [ 'Education', 'Workplace Equity', 'Access' ],
			'tags'     => [ 'education pipeline', 'student opportunity', 'institutional partnership', 'equity access' ],
			'spheres'  => [ 'Embracing Racial Diversity' ],
			'sdgs'     => [ 'Quality Education' ],
			'image'    => 'site/articles/building-equity-centered-education-pipeline/featured.webp',
			'post_date'=> '2024-06-21 10:46:02',
			'meta'     => [ '_post_read_time_label' => '3 min read' ],
		],
	];

	if ( in_array( 'post', $want, true ) ) {
		foreach ( $articles as $d ) {
			reci_demo_insert_post( 'post', $d, $topics, $locations, $imgs );
		}
	}

	if ( in_array( 'reci_podcast', $want, true ) ) {
		$podcasts = reci_demo_load_php_dataset( 'podcasts' );
		foreach ( $podcasts as $d ) {
			reci_demo_insert_post( 'reci_podcast', $d, $topics, $locations, $imgs );
		}
	}

	if ( in_array( 'reci_video', $want, true ) ) {
		$videos = reci_demo_load_php_dataset( 'videos' );
		foreach ( $videos as $d ) {
			reci_demo_insert_post( 'reci_video', $d, $topics, $locations, $imgs );
		}
	}

	if ( in_array( 'reci_event', $want, true ) ) {
		$events = reci_demo_load_php_dataset( 'events' );
		foreach ( $events as $d ) {
			reci_demo_insert_post( 'reci_event', $d, $topics, $locations, $imgs );
		}
	}

	if ( in_array( 'reci_team', $want, true ) ) {
		$team = reci_demo_load_php_dataset( 'team' );
		foreach ( $team as $d ) {
			reci_demo_insert_post( 'reci_team', $d, [], [], $imgs );
		}
	}

	// Reflections.
	// One demo reflection per registry template ("style"), each built from that
	// template so it can be reviewed end-to-end. Featured images are left empty
	// for now — add real imagery in the builder per reflection.
	$reflections = [
		[
			'slug'     => 'reci-demo-reflection-voices-of-resistance',
			'title'    => 'Voices of Resistance',
			'excerpt'  => 'An immersive testimony template — first-person voices remembering the moment they decided enough was enough.',
			'category' => 'Racial Justice',
			'topics'   => [ 'Community Action', 'Cultural Identity' ],
			'spheres'  => [ 'Building Racial Empathy' ],
			'style'    => 'voices-of-resistance',
			'folder'   => 'voices-of-resistance',
			'featured' => 'pexels-alfomedeiros-11662107.webp',
			'inner'    => [
				'wikiimages-speech-67628 copy.webp',               // hero background (RFK at megaphone)
				'pexels-zeeshaanshabbir-9746518.webp',             // hotspot-stage background (street march)
				'pexels-guimaraesm-8547571.webp',                  // panel 1 (megaphone + raised fists)
				'pexels-mohammed-abubakr-201794886-19488923.webp', // panel 2 (flags crowd at dusk)
				'wikiimages-speech-67628.webp',                    // panel 3 (RFK portrait)
			],
		],
		[
			'slug'     => 'reci-demo-reflection-breaking-chains',
			'title'    => 'Breaking Chains',
			'excerpt'  => 'A parallax journey exploring mass incarceration, systemic justice, and visions for reform.',
			'category' => 'Racial Justice',
			'topics'   => [ 'Systemic Racism' ],
			'spheres'  => [ 'Recognizing Racial Oppression', 'Building Racial Empathy' ],
			'style'    => 'breaking-chains',
			'folder'   => 'breaking-chains',
			'featured' => 'pexels-charles-awelewa-2147613783-29676620.webp',
			'inner'    => [ '360_F_696348926_NB7rL1amh93OtY0mHkjLpiMZ6jP0Q6tq.webp', 'pexels-wal_-172619-2156618639-35930741.webp' ],
		],
		[
			'slug'     => 'reci-demo-reflection-march-toward-justice',
			'title'    => 'The March Toward Justice',
			'excerpt'  => 'A documentary-march template tracing the 1965 fight for the ballot — step by step toward the soul of democracy.',
			'category' => 'Racial Justice',
			'topics'   => [ 'Community Action', 'Public Service' ],
			'spheres'  => [ 'Recognizing Racial Oppression', 'Building Racial Empathy' ],
			'style'    => 'march-toward-justice',
			'folder'   => 'march-towards-justice',
			'featured' => 'Dr-Martin-Luther-King-Jr-in-the-midst-at-the-March-on-Washington.webp',
			'inner'    => [ 'Civil-Rights-Leaders-in-Selma-600x428.webp', 'GettyImages-525580854-1.webp', 'images.webp', 'pexels-airamdphoto-9751037.webp' ],
		],
		[
			'slug'     => 'reci-demo-reflection-racial-disparities',
			'title'    => 'Racial Disparities',
			'excerpt'  => 'An analytical template that turns the data of inequity into an interactive, card-based exploration.',
			'category' => 'Racial Justice',
			'topics'   => [ 'Health Disparities', 'Systemic Racism' ],
			'spheres'  => [ 'Gauging Racial Inequities' ],
			'style'    => 'racial-disparities',
			'folder'   => 'racial-disparities',
			'featured' => 'pexels-anna-nekrashevich-8058540.webp',
			'inner'    => ['Diversity_2-1024x844.webp'],
		],
	];

	if ( in_array( 'reci_reflection', $want, true ) ) {
		// Each template reflection: insert the post, then build its blueprint from
		// the registry style and inject any sideloaded images (in chapter order).
		foreach ( $reflections as $d ) {
			if ( get_page_by_path( $d['slug'], OBJECT, 'reci_reflection' ) ) {
				continue;
			}
			$folder = $d['folder'] ?? '';
			$insert = [
				'slug'     => $d['slug'],
				'title'    => $d['title'],
				'excerpt'  => $d['excerpt'],
				'content'  => '',
				'category' => $d['category'] ?? '',
				'topics'   => $d['topics'] ?? [],
				'focus'    => $d['focus'] ?? [],
				'spheres'  => $d['spheres'] ?? [],
				'meta'     => [],
			];
			if ( $folder && ! empty( $d['featured'] ) ) {
				$insert['image'] = 'site/reflections/' . $folder . '/' . $d['featured'];
			}
			reci_demo_insert_post( 'reci_reflection', $insert, $topics, $locations, $imgs );

			$post = get_page_by_path( $d['slug'], OBJECT, 'reci_reflection' );
			if ( ! $post ) {
				continue;
			}
			$bp = reci_demo_blueprint_from_style( $d['style'] );
			if ( $folder && ! empty( $d['inner'] ) ) {
				$urls = [];
				foreach ( $d['inner'] as $file ) {
					$theme_url = reci_demo_theme_image_url( 'site/reflections/' . $folder . '/' . $file );
					if ( '' !== $theme_url ) {
						$urls[] = $theme_url;
					}
				}
				if ( $urls ) {
					$bp = reci_demo_blueprint_inject_images( $bp, $urls );
				}
			}
			update_post_meta(
				$post->ID,
				'_reci_reflection_blueprint',
				wp_slash( wp_json_encode( $bp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) )
			);
		}

		// Black Wall Street — a full immersive reflection built from existing chapters.
		// Only build it once; the blueprint embeds sideloaded image URLs that need the
		// post (and media library) to exist first, so we set the meta after insertion.
		if ( ! get_page_by_path( 'reci-demo-black-wall-street', OBJECT, 'reci_reflection' ) ) {
			reci_demo_insert_post( 'reci_reflection', [
				'slug'    => 'reci-demo-black-wall-street',
				'title'   => 'Black Wall Street',
				'excerpt' => 'An immersive reflection on Greenwood: its rise, the 1921 Tulsa Race Massacre, the cover-up, and the rebuilding.',
				'content'  => '',
				'category' => 'Racial Justice',
				'topics'   => [ 'Community Action', 'Systemic Racism' ],
				'spheres'  => [ 'Recognizing Racial Oppression', 'Building Racial Empathy' ],
				'image'    => 'site/reflections/black-wall/main.webp', // featured image
				'meta'     => [],
			], $topics, $locations, $imgs );

			$bws = get_page_by_path( 'reci-demo-black-wall-street', OBJECT, 'reci_reflection' );
			if ( $bws ) {
				// Sideload every image the blueprint references and build a
				// filename => attachment-URL map keyed by the bare filename.
				$bws_files = [
					'main.webp', 'Blackwall-street.webp',
					'bws-07.webp', 'bws-08.webp', 'bws-09.webp', 'bws-12.webp', 'bws-13.webp',
					'bws-14.webp', 'bws-15.webp', 'bws-16.webp', 'bws-18.webp', 'bws-19.webp',
				];
				$bws_img = [];
				foreach ( $bws_files as $file ) {
					$theme_url = reci_demo_theme_image_url( 'site/reflections/black-wall/' . $file );
					if ( '' !== $theme_url ) {
						$bws_img[ $file ] = $theme_url;
					}
				}

				update_post_meta(
					$bws->ID,
					'_reci_reflection_blueprint',
					wp_slash( wp_json_encode(
						reci_demo_black_wall_street_blueprint( $bws_img ),
						JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
					) )
				);
			}
		}

		// We Humans — a light/documentary museum exhibit built from existing chapters.
		if ( ! get_page_by_path( 'reci-demo-we-humans', OBJECT, 'reci_reflection' ) ) {
			reci_demo_insert_post( 'reci_reflection', [
				'slug'     => 'reci-demo-we-humans',
				'title'    => '"We Humans": Educating Pittsburgh on Race in the 1950s',
				'excerpt'  => 'A digital exhibit on the 1955 Carnegie Museum "We Humans" exhibit — how Pittsburgh civic, labor, and education leaders taught about race in the 1950s, and what its ambitions and shortcomings teach us now.',
				'content'  => '',
				'category' => 'History',
				'topics'   => [ 'Education', 'Systemic Racism' ],
				'focus'    => [ 'Curriculum / Training' ],
				'spheres'  => [ 'Recognizing Racial Oppression', 'Building Racial Empathy' ],
				'image'    => 'site/reflections/we-humans/students-1959.webp',
				'meta'     => [],
			], $topics, $locations, $imgs );

			$wh = get_page_by_path( 'reci-demo-we-humans', OBJECT, 'reci_reflection' );
			if ( $wh ) {
				$wh_files = [
					'students-1959.webp', 'are-you-ethnocentric.webp', 'ethnocentric.webp', 'teacher-1959.webp',
					'courier-1956.webp', 'sf-library-1959.webp', 'indianapolis-1.webp', 'indianapolis-2.webp',
					'panel-1a.webp', 'panel-1b.webp', 'panel-2a.webp', 'panel-2b.webp',
					'panel-3a.webp', 'panel-3b.webp', 'panel-4a.webp', 'panel-4b.webp', 'about.webp', 'at-70.webp',
					'comments-from-teachers.pdf', 'swauger-museum-as-teacher.pdf',
				];
				$wh_img = [];
				foreach ( $wh_files as $file ) {
					$theme_url = reci_demo_theme_image_url( 'site/reflections/we-humans/' . $file );
					if ( '' !== $theme_url ) {
						$wh_img[ $file ] = $theme_url;
					}
				}

				update_post_meta(
					$wh->ID,
					'_reci_reflection_blueprint',
					wp_slash( wp_json_encode(
						reci_demo_we_humans_blueprint( $wh_img ),
						JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
					) )
				);
			}
		}
	}

	if ( in_array( 'reci_quote', $want, true ) ) {
		$quotes = reci_demo_load_php_dataset( 'quotes' );
		foreach ( $quotes as $d ) {
			reci_demo_insert_quote( $d );
		}
	}

	if ( in_array( 'reci_assessment', $want, true ) ) {
		$assessments = reci_demo_load_php_dataset( 'assessments' );
		foreach ( $assessments as $d ) {
			reci_demo_insert_post( 'reci_assessment', $d, $topics, $locations, $imgs );
		}
	}
	

	if ( in_array( 'reci_course', $want, true ) ) {
		$courses = reci_demo_load_php_dataset( 'courses' );
		foreach ( $courses as $d ) {
			reci_demo_insert_post( 'reci_course', $d, $topics, $locations, $imgs );
		}
	}
	

	if ( in_array( 'reci_testimonial', $want, true ) ) {
		$testimonials = reci_demo_load_php_dataset( 'testimonials' );
		foreach ( $testimonials as $d ) {
			reci_demo_insert_testimonial( $d );
		}
	}

	if ( in_array( 'reci_glossary_term', $want, true ) ) {
		$glossary_terms = reci_demo_load_php_dataset( 'glossary' );
		foreach ( $glossary_terms as $d ) {
			reci_demo_insert_glossary_term( $d );
		}
	}

	if ( in_array( 'reci_author', $want, true ) ) {
		$authors = reci_demo_load_php_dataset( 'authors' );
		foreach ( $authors as $d ) {
			reci_demo_insert_author( $d );
		}

		// The real CRSP collaboratory directory. Idempotent and asset-heavy, so
		// it runs in batches and skips anything already imported.
		if ( function_exists( 'reci_collaborator_import_run' ) ) {
			$offset = 0;
			do {
				$batch  = reci_collaborator_import_run( 10, $offset );
				$offset = (int) ( $batch['offset'] ?? 0 );
			} while ( empty( $batch['done'] ) );
		}
	}

	if ( in_array( 'reci_partner', $want, true ) ) {
		$partners = reci_demo_load_php_dataset( 'partners' );
		foreach ( $partners as $d ) {
			reci_demo_insert_partner( $d, $imgs );
		}
	}

	// Core pages.
	if ( in_array( 'reci_page', $want, true ) ) {
		$page_dataset = reci_demo_load_php_dataset( 'pages' );
		$core_pages   = (array) ( $page_dataset['core'] ?? [] );
		foreach ( $core_pages as $slug => $cfg ) {
			$page_id = reci_demo_ensure_page( $slug, $cfg['title'], $cfg['template'] );
			if ( $page_id ) {
				$slugs   = get_option( 'reci_demo_slugs', [] );
				$slugs[] = $slug;
				update_option( 'reci_demo_slugs', array_unique( $slugs ) );
				if ( $slug === 'articles' ) {
					update_option( 'show_on_front', 'page' );
					update_option( 'page_for_posts', $page_id );
				}
				if ( $slug === 'home' ) {
					update_option( 'show_on_front', 'page' );
					update_option( 'page_on_front', $page_id );
				}
			}
		}

		// Dashboard — parent first, then children with parent ID.
		$dashboard_parent = (array) ( $page_dataset['dashboard']['parent'] ?? [] );
		$dashboard_id = reci_demo_ensure_page(
			(string) ( $dashboard_parent['slug'] ?? 'dashboard' ),
			(string) ( $dashboard_parent['title'] ?? 'Dashboard' ),
			(string) ( $dashboard_parent['template'] ?? 'templates/page/dashboard/template-dashboard.php' )
		);
		if ( $dashboard_id ) {
			$dashboard_slugs = get_option( 'reci_demo_slugs', [] );
			$dashboard_slugs[] = 'dashboard';
			update_option( 'reci_demo_slugs', array_unique( $dashboard_slugs ) );

			$dashboard_subpages = (array) ( $page_dataset['dashboard']['children'] ?? [] );
			foreach ( $dashboard_subpages as $slug => $cfg ) {
				$child_id = reci_demo_ensure_page( $slug, $cfg['title'], $cfg['template'], $dashboard_id );
				if ( $child_id ) {
					$slugs = get_option( 'reci_demo_slugs', [] );
					$slugs[] = 'dashboard/' . $slug;
					update_option( 'reci_demo_slugs', array_unique( $slugs ) );
				}
			}
		}
	}

	// Seed default About Cards into theme settings if not already set.
	$options = get_option( 'reci_theme_settings', [] );
	$defaults = [
		'about_c1_title' => 'Our Mission',
		'about_c1_copy'  => 'The Racial Equity Consciousness Institute (RECI) advances racial equity through evidence-based education, research, and community engagement. Housed at the University of Pittsburgh\'s Center on Race and Social Problems, RECI develops and delivers innovative tools for consciousness development across six spheres of influence — Self, Family, Community, Organization, Society, and Global — through Structured Cognitive Behavioral Training (SCBT).',
		'about_c2_title' => 'Our Vision',
		'about_c2_copy'  => 'We envision a world where racial equity consciousness is a universal competency — where individuals, communities, and institutions possess the awareness, knowledge, and commitment to dismantle racism and build systems that serve everyone equitably.',
		'about_c3_title' => 'Our Approach',
		'about_c3_copy'  => 'RECI\'s work is grounded in the metaphor that racism operates as a social virus — and that racial equity consciousness is the vaccine. Through our Structured Cognitive Behavioral Training (SCBT) framework, we guide individuals and organizations through a process-oriented journey of consciousness development across six bilateral spheres. Each sphere represents both an awareness dimension and an action dimension, reflecting the journey from recognition to transformation. This framework has been developed through years of research, tested across multiple cohorts, and is currently supported by NIH-funded research.',
	];

	$updated_options = false;
	foreach ( $defaults as $key => $val ) {
		if ( empty( $options[ $key ] ) ) {
			$options[ $key ] = $val;
			$updated_options = true;
		}
	}

	if ( $updated_options ) {
		update_option( 'reci_theme_settings', $options );
	}

	update_option( 'reci_demo_installed', true );
}

// ---------------------------------------------------------------------------
// Reset handler
// ---------------------------------------------------------------------------

function reci_reset_demo_content(): void {
	$slugs = get_option( 'reci_demo_slugs', [] );

	// Sort slugs by depth descending so child pages are deleted before their parents.
	usort( $slugs, function( $a, $b ) {
		return substr_count( $b, '/' ) <=> substr_count( $a, '/' );
	} );

	foreach ( $slugs as $slug ) {
		$post = get_page_by_path( $slug, OBJECT, [
			'post', 'reci_podcast', 'reci_video', 'reci_event',
			'reci_reflection', 'reci_quote', 'reci_assessment', 'reci_course',
			'reci_testimonial', 'reci_glossary_term', 'reci_author', 'reci_team', 'page',
		] );
		if ( ! $post && str_contains( $slug, '/' ) ) {
			// Fallback 1: Try explicitly checking the page post type with the full slug.
			$post = get_page_by_path( $slug, OBJECT, 'page' );
			// Fallback 2: If parent was already deleted, the child might be orphaned. Try basename.
			if ( ! $post ) {
				$post = get_page_by_path( basename( $slug ), OBJECT, 'page' );
			}
		}
		if ( $post ) {
			wp_delete_post( $post->ID, true );
		}
	}

	$registry = reci_demo_get_asset_registry();
	foreach ( $registry as $entry ) {
		$attachment_id = (int) ( $entry['attachment_id'] ?? 0 );
		if ( $attachment_id > 0 ) {
			wp_delete_attachment( $attachment_id, true );
		}
	}

	delete_option( 'reci_demo_installed' );
	delete_option( 'reci_demo_slugs' );
	delete_option( 'reci_demo_taxonomies_seeded' );
	delete_option( reci_demo_asset_registry_option_key() );
	delete_option( reci_demo_job_option_key() );
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Ensure a WordPress page exists with the given slug and template.
 *
 * Skips if the slug already exists. Flagged as demo content.
 *
 * @param string $slug     Page slug.
 * @param string $title    Page title.
 * @param string $template Template filename (without path).
 * @return int|false Page ID on success, false on failure.
 */
function reci_demo_ensure_page( string $slug, string $title, string $template, int $parent_id = 0 ) {
	$existing = null;

	if ( $parent_id > 0 ) {
		$existing_pages = get_posts( [
			'post_type'      => 'page',
			'post_status'    => [ 'publish', 'draft', 'pending', 'private' ],
			'name'           => $slug,
			'post_parent'    => $parent_id,
			'posts_per_page' => 1,
		] );
		$existing = $existing_pages[0] ?? null;
	} else {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
	}

	if ( $existing ) {
		if ( $template !== '' ) {
			update_post_meta( $existing->ID, '_wp_page_template', $template );
		}
		
		$update_args = [];
		if ( $parent_id && (int) $existing->post_parent !== $parent_id ) {
			$update_args['post_parent'] = $parent_id;
		}
		
		if ( $existing->post_status !== 'publish' ) {
			if ( $existing->post_status === 'trash' ) {
				wp_untrash_post( $existing->ID );
			}
			$update_args['post_status'] = 'publish';
		}
		
		if ( ! empty( $update_args ) ) {
			$update_args['ID'] = $existing->ID;
			wp_update_post( $update_args );
		}
		
		update_post_meta( $existing->ID, '_reci_demo', '1' );
		return $existing->ID;
	}

	$page_id = wp_insert_post( [
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => '',
		'post_parent'  => $parent_id,
	] );

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return false;
	}

	if ( $template !== '' ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
	}
	update_post_meta( $page_id, '_reci_demo', '1' );

	return $page_id;
}

/**
 * Insert a demo testimonial post.
 */
function reci_demo_insert_testimonial( array $data ): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_testimonial' );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post( [
		'post_type'    => 'reci_testimonial',
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_content' => '',
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_testimonial_text', $data['text'] );
	update_post_meta( $post_id, '_reci_testimonial_full_name', $data['name'] );
	update_post_meta( $post_id, '_reci_testimonial_role', $data['role'] );
	update_post_meta( $post_id, '_reci_testimonial_organization', $data['org'] );
	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Insert a demo glossary term.
 */
function reci_demo_insert_glossary_term( array $data ): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_glossary_term' );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post( [
		'post_type'    => 'reci_glossary_term',
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_content' => $data['content'],
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Insert a demo author profile.
 */
function reci_demo_insert_author( array $data ): void {
	reci_demo_ensure_author_profile( $data );
}

function reci_demo_insert_partner( array $data, array &$imgs ): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_partner' );
	if ( $existing ) {
		update_post_meta( $existing->ID, '_reci_partner_url', $data['url'] );
		update_post_meta( $existing->ID, '_reci_demo', '1' );
		$slugs   = get_option( 'reci_demo_slugs', [] );
		$slugs[] = $data['slug'];
		update_option( 'reci_demo_slugs', array_unique( $slugs ) );
		if ( ! empty( $data['image'] ) && ! has_post_thumbnail( $existing->ID ) ) {
			$attachment_id = reci_demo_sideload_image( $data['image'], $existing->ID, $imgs );
			if ( $attachment_id ) {
				set_post_thumbnail( $existing->ID, $attachment_id );
			}
		}

		return;
	}

	$post_id = wp_insert_post( [
		'post_type'   => 'reci_partner',
		'post_status' => 'publish',
		'post_name'   => $data['slug'],
		'post_title'  => $data['name'],
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_partner_url', $data['url'] );
	update_post_meta( $post_id, '_reci_demo', '1' );

	if ( ! empty( $data['image'] ) ) {
		$file_path = get_template_directory() . '/demo-content/images/' . ltrim( $data['image'], '/' );
		if ( file_exists( $file_path ) ) {
			$attachment_id = reci_demo_sideload_image( $data['image'], $post_id, $imgs );
			if ( $attachment_id ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
	}

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

function reci_demo_ensure_author_profile( array $data ): int {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_author' );
	if ( $existing ) {
		if ( ! empty( $data['title'] ) ) {
			update_post_meta( $existing->ID, '_reci_author_profile_title', $data['title'] );
		}
		update_post_meta( $existing->ID, '_reci_demo', '1' );
		$slugs   = get_option( 'reci_demo_slugs', [] );
		$slugs[] = $data['slug'];
		update_option( 'reci_demo_slugs', array_unique( $slugs ) );
		return (int) $existing->ID;
	}

	$post_id = wp_insert_post( [
		'post_type'    => 'reci_author',
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['name'],
		'post_excerpt' => $data['bio'],
		'post_content' => $data['content'],
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	update_post_meta( $post_id, '_reci_author_profile_title', $data['title'] );
	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );

	return (int) $post_id;
}

function reci_demo_insert_sample_collaborator_application(): void {
	if ( ! function_exists( 'reci_get_collaborator_application_post_type' ) ) {
		return;
	}

	$post_type = reci_get_collaborator_application_post_type();
	$slug      = 'sample-collaborator-application';
	$existing  = get_page_by_path( $slug, OBJECT, $post_type );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post([
		'post_type'    => $post_type,
		'post_status'  => 'pending',
		'post_name'    => $slug,
		'post_title'   => 'Sample Collaborator Application',
		'post_content' => 'This sample application demonstrates the collaborator review workflow in wp-admin.',
	]);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_collaborator_application_status', 'pending' );
	update_post_meta( $post_id, '_reci_submission_first_name', 'Jordan' );
	update_post_meta( $post_id, '_reci_submission_last_name', 'Reed' );
	update_post_meta( $post_id, '_reci_submission_email', 'jordan.reed@example.com' );
	update_post_meta( $post_id, '_reci_collaborator_affiliated_with_pitt', 'Yes' );
	update_post_meta( $post_id, '_reci_collaborator_pitt_affiliation', 'Graduate Student' );
	update_post_meta( $post_id, '_reci_submission_organization', 'University of Pittsburgh' );
	update_post_meta( $post_id, '_reci_collaborator_department', 'School of Social Work' );
	update_post_meta( $post_id, '_reci_submission_role', 'Community Research Fellow' );
	update_post_meta( $post_id, '_reci_submission_bio', 'Jordan Reed works at the intersection of community partnership, racial equity education, and public-facing research communication.' );
	update_post_meta( $post_id, '_reci_submission_website', 'https://example.com/jordan-reed' );
	update_post_meta( $post_id, '_reci_collaborator_social_handles', '@jordanreed' );
	update_post_meta( $post_id, '_reci_collaborator_membership_objective', 'To contribute equity-centered resources and connect research to community learning.' );
	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $slug;
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

function reci_demo_get_markdown_articles(): array {
	static $articles = null;

	if ( is_array( $articles ) ) {
		return $articles;
	}

	$dir = get_template_directory() . '/demo-content/articles';
	if ( ! is_dir( $dir ) ) {
		$articles = [];
		return $articles;
	}
	$articles = [];
	$files = glob( $dir . '/*.md' ) ?: [];

	foreach ( $files as $file ) {
		$title = basename( $file, '.md' );
		$raw   = (string) file_get_contents( $file );
		$body  = trim( preg_replace( '/^#\s+.+$(\r\n|\n|\r)?/m', '', $raw, 1 ) ?? $raw );
		$author = '';

		if ( preg_match( '/^Author:\s*(.+)$/mi', $body, $author_matches ) ) {
			$author = trim( (string) $author_matches[1] );
			$body   = preg_replace( '/^Author:\s*.+$(\n+)?/mi', '', $body, 1 ) ?? $body;
		}

		$articles[ $title ] = [
			'author' => $author,
			'body'   => trim( $body ),
		];
	}

	return $articles;
}

function reci_demo_get_markdown_article_author( string $title ): string {
	$articles = reci_demo_get_markdown_articles();
	return (string) ( $articles[ $title ]['author'] ?? '' );
}

function reci_demo_get_markdown_article_excerpt( string $title ): string {
	$articles = reci_demo_get_markdown_articles();
	$body     = (string) ( $articles[ $title ]['body'] ?? '' );

	if ( $body === '' ) {
		return '';
	}

	$body       = preg_replace( '/!\[[^\]]*\]\([^\)]+\)/', '', $body ) ?? $body;
	$paragraphs = preg_split( '/\n\s*\n/', trim( $body ) );

	foreach ( $paragraphs as $paragraph ) {
		$text = trim( preg_replace( '/^#+\s+/', '', $paragraph ) ?? $paragraph );
		if ( $text === '' || preg_match( '/^(\*|Sources?$)/i', $text ) ) {
			continue;
		}

		$text = wp_strip_all_tags( reci_demo_markdown_inline( $text ) );
		if ( $text !== '' ) {
			return wp_trim_words( $text, 28, '...' );
		}
	}

	return '';
}

function reci_demo_render_markdown_article( string $title, string $figure_html = '' ): string {
	$articles = reci_demo_get_markdown_articles();
	$body     = (string) ( $articles[ $title ]['body'] ?? '' );

	if ( $body === '' ) {
		return '';
	}

	$lines           = preg_split( "/\r\n|\n|\r/", $body );
	$blocks          = [];
	$paragraph       = [];
	$in_list         = false;
	$figure_inserted = false;
	$paragraph_count = 0;

	$flush_paragraph = static function () use ( &$paragraph, &$blocks, &$figure_inserted, &$paragraph_count, $figure_html ): void {
		if ( empty( $paragraph ) ) {
			return;
		}

		$text     = implode( ' ', array_map( 'trim', $paragraph ) );
		$blocks[] = '<p>' . reci_demo_markdown_inline( $text ) . '</p>';
		$paragraph = [];
		$paragraph_count++;

		if ( ! $figure_inserted && $figure_html !== '' && $paragraph_count >= 3 ) {
			$blocks[]         = $figure_html;
			$figure_inserted = true;
		}
	};

	$close_list = static function () use ( &$in_list, &$blocks ): void {
		if ( $in_list ) {
			$blocks[] = '</ul>';
			$in_list  = false;
		}
	};

	foreach ( $lines as $line ) {
		$trimmed = trim( (string) $line );

		if ( $trimmed === '' ) {
			$flush_paragraph();
			$close_list();
			continue;
		}

		if ( preg_match( '/^!\[[^\]]*\]\([^\)]+\)$/', $trimmed ) ) {
			$flush_paragraph();
			$close_list();
			if ( ! $figure_inserted && $figure_html !== '' ) {
				$blocks[]         = $figure_html;
				$figure_inserted = true;
			}
			continue;
		}

		if ( preg_match( '/^(#{2,4})\s+(.+)$/', $trimmed, $matches ) ) {
			$flush_paragraph();
			$close_list();
			$level    = strlen( $matches[1] );
			$blocks[] = '<h' . $level . '>' . reci_demo_markdown_inline( trim( (string) $matches[2] ) ) . '</h' . $level . '>';
			continue;
		}

		if ( preg_match( '/^\*\s+(.+)$/', $trimmed, $matches ) ) {
			$flush_paragraph();
			if ( ! $in_list ) {
				$blocks[] = '<ul>';
				$in_list  = true;
			}
			$blocks[] = '<li>' . reci_demo_markdown_inline( trim( (string) $matches[1] ) ) . '</li>';
			continue;
		}

		$close_list();
		$paragraph[] = $trimmed;
	}

	$flush_paragraph();
	$close_list();

	if ( ! $figure_inserted && $figure_html !== '' ) {
		$blocks[] = $figure_html;
	}

	return implode( '', $blocks );
}

function reci_demo_markdown_inline( string $text ): string {
	$text = preg_replace_callback(
		'/\[([^\]]+)\]\(([^\)]+)\)/',
		static function ( array $matches ): string {
			return '<a href="' . esc_url( trim( (string) $matches[2] ) ) . '">' . esc_html( trim( (string) $matches[1] ) ) . '</a>';
		},
		$text
	) ?? $text;

	$text = preg_replace( '/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text ) ?? $text;

	return $text;
}

/**
 * Build a reflection blueprint from a registry template ("style").
 *
 * Produces exactly what the builder would create when you pick the template:
 * the template's colours as global settings plus its chapters verbatim. Image
 * slots come from whatever the template defines (placeholder / gallery refs).
 *
 * @return array<string,mixed> Empty array if the style is unknown.
 */
function reci_demo_blueprint_from_style( string $style_key ): array {
	if ( ! function_exists( 'reci_reflection_system_styles' ) ) {
		return [];
	}
	$styles = reci_reflection_system_styles();
	$style  = $styles[ $style_key ] ?? null;
	if ( ! is_array( $style ) ) {
		return [];
	}
	$c = $style['colors'] ?? [];

	$chapters = array_values( (array) ( $style['chapters'] ?? [] ) );
	foreach ( $chapters as &$chapter ) {
		if ( empty( $chapter['id'] ) ) {
			$chapter['id'] = ! empty( $chapter['props']['id'] ) ? $chapter['props']['id'] : uniqid();
		}
	}
	unset( $chapter );

	return [
		'version'  => 2,
		'system'   => 'reflections',
		'settings' => [
			'mode'          => 'immersive',
			'global_style'  => $style_key,
			'color_primary' => $c['primary'] ?? '',
			'color_bg'      => $c['bg'] ?? '',
			'color_heading' => $c['heading'] ?? '',
			'color_body'    => $c['body'] ?? '',
			'color_accent'  => $c['accent'] ?? '',
			'menu_enabled'  => false,
			'nav_enabled'   => false,
		],
		'chapters' => $chapters,
	];
}

/**
 * Inject image URLs into a blueprint's image slots, in chapter order.
 *
 * Walks chapters top-to-bottom and consumes $urls one per slot:
 *   hero → background image, hotspot-stage → background, feature-split → image,
 *   horizontal-panels → each item background, parallax-stage → each layer src.
 * Slots beyond the supplied URLs are left untouched (keep their placeholder).
 *
 * @param array<string,mixed> $bp
 * @param string[]            $urls
 * @return array<string,mixed>
 */
function reci_demo_blueprint_inject_images( array $bp, array $urls ): array {
	$i = 0;
	$next = static function () use ( &$urls, &$i ) {
		return $urls[ $i++ ] ?? null;
	};

	foreach ( $bp['chapters'] as &$chapter ) {
		$family = $chapter['family'] ?? '';
		if ( ! isset( $chapter['props'] ) || ! is_array( $chapter['props'] ) ) {
			continue;
		}
		$props = &$chapter['props'];

		if ( 'hero' === $family ) {
			$u = $next();
			if ( $u ) {
				$props['use_background_image'] = '1';
				$props['background_image']     = $u;
			}
		} elseif ( 'hotspot-stage' === $family ) {
			$u = $next();
			if ( $u ) {
				$props['background_image'] = $u;
			}
		} elseif ( 'feature-split' === $family ) {
			$u = $next();
			if ( $u ) {
				$props['image'] = $u;
			}
		} elseif ( 'horizontal-panels' === $family ) {
			if ( ! empty( $props['items'] ) && is_array( $props['items'] ) ) {
				foreach ( $props['items'] as &$item ) {
					$u = $next();
					if ( $u ) {
						$item['background_image'] = $u;
					}
				}
				unset( $item );
			}
		} elseif ( 'parallax-stage' === $family ) {
			if ( ! empty( $props['layers'] ) && is_array( $props['layers'] ) ) {
				foreach ( $props['layers'] as &$layer ) {
					$u = $next();
					if ( $u ) {
						$layer['src'] = $u;
					}
				}
				unset( $layer );
			}
		}

		unset( $props );
	}
	unset( $chapter );

	return $bp;
}

/**
 * Blueprint for the "Black Wall Street" demo reflection.
 *
 * Built entirely from existing chapter families/variants — no new templates.
 * Image slots use the bundled placeholder; replace them with real Greenwood
 * imagery in the builder after import.
 *
 * @return array<string,mixed>
 */
function reci_demo_black_wall_street_blueprint( array $img = [] ): array {
	$ph = function_exists( 'reci_reflection_placeholder_image' ) ? reci_reflection_placeholder_image() : '';
	// Resolve an image URL by key, falling back to the neutral placeholder.
	$im = static function ( string $key ) use ( $img, $ph ): string {
		return ! empty( $img[ $key ] ) ? $img[ $key ] : $ph;
	};

	return [
		'version'  => 2,
		'system'   => 'reflections',
		'settings' => [
			'mode'          => 'immersive',
			'global_style'  => 'immersive-dark',
			'color_primary' => '#D4AF37',
			'color_bg'      => '#140f0a',
			'color_heading' => '#f0e6d2',
			'color_body'    => '#c9c0b0',
			'color_accent'  => '#D4AF37',
			'color_muted'   => '#9a8f7a',
			'menu_enabled'  => false,
			'nav_enabled'   => false,
		],
		'chapters' => [
			// 1. Hero / Title.
			[
				'id'      => 'bws-title',
				'family'  => 'hero',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'                   => 'bws-title',
					'eyebrow'              => 'Tulsa, Oklahoma · Greenwood District',
					'title'                => 'Black Wall',
					'title_accent'         => 'Street',
					'subtitle'             => 'The Rise, Destruction, and Resilience of Tulsa\'s Greenwood District',
					'use_background_image' => '1',
					'background_image'     => $im( 'main.webp' ),
					'actions'              => [ [ 'label' => 'Enter the Story', 'href' => 'bws-beacon' ] ],
				],
			],
			// 2. A Beacon of Black Excellence (text + image).
			[
				'id'      => 'bws-beacon',
				'family'  => 'feature-split',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-beacon',
					'eyebrow'         => 'A Beacon of Black Excellence',
					'title'           => 'A Declaration',
					'body'            => '<p>In the early 20th century, while Jim Crow laws strangled opportunity across America, an extraordinary community flourished in Tulsa, Oklahoma. The Greenwood District, known as "Black Wall Street," stood as a testament to what Black Americans could achieve despite systemic racism.</p><p>This wasn\'t just a neighborhood. It was a declaration of independence — a 35-block sanctuary where Black excellence wasn\'t the exception, it was the rule.</p>',
					'image'           => $im( 'bws-15.webp' ),
					'image_alt'       => 'Greenwood Avenue in its prosperity',
					'media_side'      => 'right',
					'continue_label'  => 'Hear from a resident →',
					'continue_target' => 'bws-quote-1',
				],
			],
			// 3. Quote — James Homer Johnson.
			[
				'id'      => 'bws-quote-1',
				'family'  => 'quote',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'               => 'bws-quote-1',
					'quote'            => 'What Greenwood meant to the Black community was the very center of activity — commercial, social, religious. Every conceivable type of business was on Greenwood. I didn\'t really feel the full effects of segregation because we were living in this self-contained environment where we didn\'t have to go outside for anything.',
					'attribution'      => 'James Homer Johnson',
					'background_image' => $im( 'bws-09.webp' ),
					'button_label'     => 'Continue',
					'continue_target'  => 'bws-prosperity-stats',
				],
			],
			// 4. Prosperity stats.
			[
				'id'      => 'bws-prosperity-stats',
				'family'  => 'data-cards',
				'variant' => 'analytical',
				'props'   => [
					'id'              => 'bws-prosperity-stats',
					'title'           => 'A Self-Made Economy',
					'intro'           => 'By 1921, Greenwood was one of the wealthiest Black communities in America.',
					'cards'           => [
						[ 'icon' => '🏪', 'eyebrow' => 'Enterprise', 'stat' => '600+', 'unit' => 'Black-Owned Businesses', 'summary' => 'Grocers, hotels, theaters, salons, and law offices lined Greenwood Avenue.' ],
						[ 'icon' => '⚕️', 'eyebrow' => 'Medicine', 'stat' => '15+', 'unit' => 'Black Physicians', 'summary' => 'Including the surgeon Dr. A.C. Jackson, called "the most able Negro surgeon in America."' ],
						[ 'icon' => '⛪', 'eyebrow' => 'Faith', 'stat' => '21', 'unit' => 'Churches', 'summary' => 'Anchors of community life, several of them grand brick sanctuaries.' ],
						[ 'icon' => '💵', 'eyebrow' => 'Wealth', 'stat' => '$1M+', 'unit' => 'in Black Wealth', 'summary' => 'A dollar circulated within Greenwood dozens of times before it ever left.' ],
					],
					'continue_label'  => 'Continue',
					'continue_target' => 'bws-economy',
				],
			],
			// 5. The community that built it (text + image).
			[
				'id'      => 'bws-economy',
				'family'  => 'feature-split',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-economy',
					'eyebrow'         => 'Survival Became Prosperity',
					'title'           => 'A Multiplier of Wealth',
					'body'            => '<p>Greenwood was home to Black doctors who had studied at prestigious universities, lawyers who defended their community\'s rights, entrepreneurs who built hotels and theaters, and educators who shaped young minds in their own schools.</p><p>Segregation forced Black Tulsans to create their own economy — but what began as survival became prosperity. Money circulated within the community dozens of times before leaving, creating a multiplier effect that built generational wealth.</p>',
					'image'           => $im( 'Blackwall-street.webp' ),
					'image_alt'       => 'Greenwood professionals and business district',
					'media_side'      => 'left',
					'continue_label'  => 'Then came the spark →',
					'continue_target' => 'bws-spark',
				],
			],
			// 6. May 30, 1921: A Spark (progressive reveal).
			[
				'id'      => 'bws-spark',
				'family'  => 'progressive-text',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-spark',
					'title'           => 'May 30, 1921: A Spark',
					'paragraphs'      => [
						[ 'text' => 'On a spring morning, 19-year-old Dick Rowland, a Black shoe shiner, stepped into an elevator in downtown Tulsa. Sarah Page, a white elevator operator, was inside. What happened next remains uncertain — perhaps he stumbled, perhaps he stepped on her foot. The details were never clear.' ],
						[ 'text' => 'But in 1921 Oklahoma, details didn\'t matter. The accusation alone was enough.' ],
						[ 'text' => 'By the next day, an inflammatory article in the Tulsa Tribune had transformed an incident into ammunition. A white mob gathered at the courthouse where police held Rowland. The city teetered on the edge.' ],
					],
					'prompt'          => 'Tap to reveal',
					'button_label'    => '↓',
					'continue_label'  => 'The night it began →',
					'continue_target' => 'bws-night',
				],
			],
			// 7. The night of the massacre — timeline (May 31 9PM, 10PM, June 1 Dawn).
			[
				'id'      => 'bws-night',
				'family'  => 'timeline-world',
				'variant' => 'documentary',
				'props'   => [
					'id'              => 'bws-night',
					'eyebrow'         => 'The Night It Began',
					'title'           => 'May 31 – June 1, 1921',
					'items'           => [
						[
							'date'  => 'May 31 · 9:00 PM',
							'title' => 'A Stand at the Courthouse',
							'body'  => 'Twenty-five armed Black men, many of them World War I veterans, arrived at the courthouse to protect Dick Rowland from lynching. They knew the law wouldn\'t. After the sheriff refused their help, they left — but the rumor of an "uprising" spread like wildfire.',
						],
						[
							'date'  => 'May 31 · 10:00 PM',
							'title' => 'The First Shot',
							'body'  => 'About 75 Black men returned to the courthouse, now facing 1,500 white men. Someone fired a shot. Chaos erupted. Outnumbered, the Black defenders retreated to Greenwood to protect their homes and families.',
						],
						[
							'date'  => 'June 1 · Dawn',
							'title' => 'The Invasion',
							'body'  => 'Thousands of white Tulsans — some deputized by city officials and armed by police — invaded Greenwood. They didn\'t come to restore order. They came to destroy.',
							'media' => [
								[ 'src' => $im( 'bws-14.webp' ), 'alt' => 'Black Tulsans rounded up and marched to internment centers', 'caption' => 'Residents rounded up at gunpoint and marched to internment centers.' ],
							],
						],
					],
					'continue_label'  => 'What followed →',
					'continue_target' => 'bws-massacre',
				],
			],
			// 8. The Massacre (text + image).
			[
				'id'      => 'bws-massacre',
				'family'  => 'feature-split',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-massacre',
					'eyebrow'         => 'May 31 – June 1, 1921',
					'title'           => 'The Massacre',
					'body'            => '<p>What followed was systematic annihilation. Homes were looted and burned. Businesses were reduced to ash. A hospital, a library, schools, and churches — all destroyed. When firefighters arrived to help, white rioters turned them away at gunpoint.</p><p>Some attackers used airplanes to drop incendiary devices and shoot at fleeing residents from above. Black Tulsans who tried to defend their property were shot. Those who surrendered were rounded up at gunpoint and marched to internment centers.</p>',
					'image'           => $im( 'bws-08.webp' ),
					'image_alt'       => 'Greenwood burning during the massacre',
					'media_side'      => 'left',
					'continue_label'  => 'Count the cost →',
					'continue_target' => 'bws-toll',
				],
			],
			// 9. The toll (text + image).
			[
				'id'      => 'bws-toll',
				'family'  => 'feature-split',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-toll',
					'eyebrow'         => '18 Hours',
					'title'           => 'Everything, Gone',
					'body'            => '<p>By noon on June 1, 1921, 35 city blocks lay in ruins. Over 1,256 homes burned. Everything Black Tulsans had built was gone in 18 hours.</p>',
					'image'           => $im( 'bws-19.webp' ),
					'image_alt'       => 'The gutted ruins of Greenwood',
					'media_side'      => 'right',
					'continue_label'  => 'The full toll →',
					'continue_target' => 'bws-toll-stats',
				],
			],
			// 10. Toll stats.
			[
				'id'      => 'bws-toll-stats',
				'family'  => 'data-cards',
				'variant' => 'analytical-dark',
				'props'   => [
					'id'              => 'bws-toll-stats',
					'title'           => 'The Cost of 18 Hours',
					'cards'           => [
						[ 'icon' => '🏚️', 'eyebrow' => 'Destroyed', 'stat' => '35', 'unit' => 'Blocks Destroyed', 'summary' => 'The entire Greenwood District reduced to ruins.' ],
						[ 'icon' => '🔥', 'eyebrow' => 'Burned', 'stat' => '1,256', 'unit' => 'Homes Burned', 'summary' => 'Along with a hospital, a library, schools, and churches.' ],
						[ 'icon' => '⛓️', 'eyebrow' => 'Detained', 'stat' => '6,000+', 'unit' => 'People Detained', 'summary' => 'Black Tulsans held at gunpoint in internment centers.' ],
						[ 'icon' => '🕯️', 'eyebrow' => 'Lost', 'stat' => '100–300', 'unit' => 'Estimated Deaths', 'summary' => 'Many buried in unmarked graves, still being searched for today.' ],
					],
					'continue_label'  => 'And then —',
					'continue_target' => 'bws-coverup',
				],
			],
			// 11. The Cover-Up (progressive reveal).
			[
				'id'      => 'bws-coverup',
				'family'  => 'progressive-text',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-coverup',
					'title'           => 'The Cover-Up',
					'paragraphs'      => [
						[ 'text' => 'Not one person was ever prosecuted for the violence. Not one. Instead, city officials blamed Black Tulsans for defending themselves. Insurance companies refused to pay out, calling it a "riot" rather than what it was: domestic terrorism.' ],
						[ 'text' => 'The Tulsa Tribune removed its inflammatory article from its archives. Police records disappeared. State militia documents vanished. For decades, this massacre was erased from textbooks, from public memory, from history itself.' ],
						[ 'text' => 'Children grew up in Tulsa not knowing that their city had once been a war zone. Families who lost everything were silenced. The dead remained in unmarked graves, their names forgotten.' ],
					],
					'prompt'          => 'Tap to reveal',
					'button_label'    => '↓',
					'continue_label'  => 'A survivor speaks →',
					'continue_target' => 'bws-quote-2',
				],
			],
			// 12. Quote — Vanessa Hall-Harper.
			[
				'id'      => 'bws-quote-2',
				'family'  => 'quote',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'               => 'bws-quote-2',
					'quote'            => 'It wasn\'t a riot. It was a massacre. Back in 1921, they used the fact that it was a "riot" technically to not pay insurance claims. It was only because of our own strength — of saying "no, we\'re not going anywhere, we\'re going to come back and we\'re going to rebuild." But the government did not help with that. The business community did not help with that. It was our own strength and belief in ourselves that rebuilt Greenwood.',
					'attribution'      => 'Vanessa Hall-Harper',
					'background_image' => $im( 'bws-13.webp' ),
					'button_label'     => 'Continue',
					'continue_target'  => 'bws-ashes',
				],
			],
			// 13. Rising from the Ashes (text + image).
			[
				'id'      => 'bws-ashes',
				'family'  => 'feature-split',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-ashes',
					'eyebrow'         => 'Resilience',
					'title'           => 'Rising from the Ashes',
					'body'            => '<p>But Black Tulsans did what they had always done: they survived. They rebuilt. Without government assistance. Without insurance payouts. With their bare hands and unbreakable will.</p><p>Within five years, they had reconstructed much of Greenwood. The same doctors reopened their practices. The same lawyers returned to fight for justice. New businesses emerged from the rubble. The community that had been targeted for extinction refused to die.</p><p>The resilience wasn\'t just physical reconstruction — it was spiritual defiance. Every brick laid, every business reopened, every child educated was an act of resistance against those who wanted to erase them.</p>',
					'image'           => $im( 'bws-12.webp' ),
					'image_alt'       => 'Black Tulsans rebuilding Greenwood',
					'media_side'      => 'left',
					'continue_label'  => 'The reckoning →',
					'continue_target' => 'bws-reckoning',
				],
			],
			// 14. The Reckoning — timeline of acknowledgement (1996, 2001, 2020, today).
			[
				'id'      => 'bws-reckoning',
				'family'  => 'timeline-world',
				'variant' => 'documentary',
				'props'   => [
					'id'              => 'bws-reckoning',
					'eyebrow'         => 'The Reckoning',
					'title'           => 'A Truth Uncovered',
					'items'           => [
						[
							'date'  => '1996',
							'title' => 'A Memorial, at Last',
							'body'  => 'It took 75 years for Tulsa to begin acknowledging what happened. A memorial service was finally held for the victims of the massacre.',
							'media' => [
								[ 'src' => $im( 'bws-18.webp' ), 'alt' => '1921 Black Wall Street Memorial', 'caption' => 'The 1921 Black Wall Street Memorial in Greenwood.' ],
							],
						],
						[
							'date'  => '2001',
							'title' => 'The Commission Confirms',
							'body'  => 'The Oklahoma Commission released its report, confirming what survivors had always known: this was a massacre, not a riot.',
						],
						[
							'date'  => '2020',
							'title' => 'A Nation Looks Back',
							'body'  => 'As America confronted its racial history anew, searches for the Tulsa Race Massacre surged. Survivors in their 100s finally had their stories heard on national stages.',
							'media' => [
								[ 'src' => $im( 'bws-16.webp' ), 'alt' => 'A candlelight vigil in Greenwood', 'caption' => 'A candlelight vigil marking the centennial.' ],
							],
						],
						[
							'date'  => 'Today',
							'title' => 'Still Uncovering',
							'body'  => 'Excavations continue, searching for mass graves — the truth still being uncovered, one exhumation at a time.',
						],
					],
					'continue_label'  => 'What it means →',
					'continue_target' => 'bws-legacy',
				],
			],
			// 15. An Undefeated Legacy (text + image).
			[
				'id'      => 'bws-legacy',
				'family'  => 'feature-split',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'              => 'bws-legacy',
					'eyebrow'         => 'Legacy',
					'title'           => 'Undefeated',
					'body'            => '<p>The story of the Tulsa Race Massacre isn\'t just about destruction. It\'s about what Black Americans built against impossible odds, what was stolen from them through racist violence, and how they refused to be broken.</p><p>It\'s about doctors and lawyers and teachers and entrepreneurs who dared to thrive — a community that created its own prosperity in a nation that tried to deny them everything.</p><p>Black Wall Street may have been destroyed, but its legacy — of Black excellence, self-determination, and unshakeable resilience — remains undefeated.</p>',
					'image'           => $im( 'bws-07.webp' ),
					'image_alt'       => 'The Black Wall Street mural in modern Greenwood',
					'media_side'      => 'right',
					'continue_label'  => 'Reflect →',
					'continue_target' => 'bws-reflect',
				],
			],
			// 16. Closing reflection prompt.
			[
				'id'      => 'bws-reflect',
				'family'  => 'reflection-prompt',
				'variant' => 'immersive-dark',
				'props'   => [
					'id'           => 'bws-reflect',
					'title'        => 'Reflect',
					'prompt'       => '"What does remembering Black Wall Street ask of us today?"',
					'button_label' => 'Return to Gallery',
					'button_href'  => '/reflections/',
				],
			],
		],
	];
}

/**
 * Blueprint for the "We Humans" demo reflection (light / documentary).
 *
 * Built from existing chapter families (documentary variants) + a light palette.
 * @return array<string,mixed>
 */
function reci_demo_we_humans_blueprint( array $img = [] ): array {
	$ph = function_exists( 'reci_reflection_placeholder_image' ) ? reci_reflection_placeholder_image() : '';
	$im = static function ( string $key ) use ( $img, $ph ): string {
		return ! empty( $img[ $key ] ) ? $img[ $key ] : $ph;
	};
	$pd = static function ( string $file ): string {
		return reci_demo_theme_image_url( 'site/reflections/we-humans/' . $file );
	};

	return [
		'version'  => 2,
		'system'   => 'reflections',
		'settings' => [
			'mode'             => 'immersive',
			'global_style'     => 'documentary',
			// Light "Hillman exhibit" palette — drives the documentary variants.
			'color_bg'           => '#D9E3C7', // Ambrosia (light green) overall
			'color_text'         => '#3F4446', // documentary titles
			'color_heading'      => '#3F4446',
			'color_body'         => '#4A4E50',
			'color_soft_text'    => '#4A4E50', // documentary body copy
			'color_surface'      => '#EDEFE8', // panel/card cream
			'color_surface_text' => '#3F4446',
			'color_card'         => '#EDEFE8',
			'color_card_strong'  => '#E3E7DD',
			'color_primary'      => '#5E7048',
			'color_accent'       => '#5E7048', // darkened Nile Green for contrast
			'color_muted'        => '#7E8478',
			'color_border'       => 'rgba(63,68,70,0.22)',
			'color_border_soft'  => 'rgba(63,68,70,0.12)',
			'color_hotspot_ring' => 'rgba(94,112,72,0.40)',
			'menu_enabled'     => true,
			'nav_enabled'      => true,
			'nav_back_label'   => '← RECI Media Hub',
			'nav_back_url'     => '/',
		],
		'chapters' => [
			// 1. Title / Hero — light text overlaid on the period photo (dark overlay).
			[
				'id'              => 'wh-title',
					'include_in_menu' => '0',
				'family'  => 'hero',
				'variant' => 'documentary',
				'props'   => [
					'id'              => 'wh-title',
					'include_in_menu' => '0',
					'override_colors'      => true,
					'color_text'           => '#F4F6EF',
					'color_soft_text'      => '#E7ECE0',
					'color_accent'         => '#C9D6B0',
					'eyebrow'              => 'A Digital Exhibit',
					'title'                => '"We Humans"',
					'subtitle'             => 'Educating Pittsburgh on Race in the 1950s',
					'body'                 => 'In the years following World War II, global shock over the Holocaust, growing demands to end race-based discrimination, and shifting ideas in science created a charged and consequential environment for social change. This digital exhibit shares one story of how civic, labor, and education leaders in Pittsburgh responded to that moment.',
					'caption'              => 'Students hearing the "We Humans" curriculum, Monongahela High School, 1959. Photograph by Michel Chalufour. Courtesy of Carnegie Museum of Natural History, Library & Archives.',
					'use_background_image' => '1',
					'background_image'     => $im( 'students-1959.webp' ),
					'overlay_rgb'          => '47,50,52',
					'overlay_opacity'      => 0.62,
					'actions'         => [
						[ 'label' => 'Introduction', 'href' => 'wh-intro' ],
					],
				],
			],
			// 2. Introduction — overview.
			[
				'id'              => 'wh-intro',
					'include_in_menu' => '1',
				'family'  => 'feature-split',
				'variant' => 'documentary',
				'props'   => [
					'id'                 => 'wh-intro',
					'include_in_menu'    => '1',
					'menu_label'         => 'Introduction',
					'menu_description'   => 'Return to the start of the exhibit',
					'eyebrow'            => 'Introduction',
					'title'           => 'What feels familiar and unfamiliar about this story?',
					'body'            => '"We Humans" was an exhibit on race and racism developed by two curators of anthropology employed at the Carnegie Museum (now Carnegie Museum of Natural History), James Swauger and Don Dragoo. Through punchy rhetoric informed by current science, Swauger and Dragoo encouraged workers, students, and citizens to question their assumptions about race and to value the lives and contributions of all people. The exhibit debuted in downtown Pittsburgh in 1955 and later reached a national audience through portable versions and publications. The exhibit was a collaborative effort, jointly planned and sponsored by the museum, the labor union the United Steelworkers of America, Mayor David L. Lawrence\'s Civic Unity Council, and Pittsburgh Public Schools.' . "\n\n" . '"We Humans" demonstrates the extent to which a version of anti-racism was being made an urgent public priority across the United States in the 1950s, but also shows the pitfalls of the institutional and scientific tactics employed in such efforts at this time. As you learn more about the story of "We Humans," ask yourself what its ambitions and shortcomings might have to teach people today.',
					'note'            => 'Please note that this exhibit includes racial terminology and imagery that are outdated and offensive.',
					'image'           => $im( 'students-1959.webp' ),
					'media_side'      => 'right',
					'actions'         => [
						[ 'label' => 'Origins', 'href' => 'wh-origins' ],
					],
				],
			],
			// 4. Origins (dossier: USW + UNESCO).
			[
				'id'              => 'wh-origins',
					'include_in_menu' => '1',
				'family'  => 'documentary-dossier',
				'variant' => 'archival',
				'props'   => [
					'id'                 => 'wh-origins',
					'include_in_menu'    => '1',
					'menu_label'         => 'Origins',
					'menu_description'   => 'Learn about how "We Humans" came to be',
					'eyebrow'            => 'Origins',
					'title'    => 'How did "We Humans" come to be?',
					'intro'    => [
						[ 'text' => '"We Humans" emerged as one of many responses to local, national, and global conversations about race and discrimination in the 1950s. Civic, labor, religious, and community leaders, alongside academics, collaborated to counter both anti-semitism and Nazi race science, and anti-Black racism and segregation.' ],
						[ 'text' => 'Of particular relevance were discussions convened by the Committee on Civil Rights of the United Steelworkers of America, and the first UNESCO Statement on Race of 1950.' ],
					],
					'sections' => [
						[
							'title'      => 'United Steelworkers of America',
							'paragraphs' => [
								[ 'text' => 'The Civil Rights Committee of the United Steelworkers (USW), established in 1948, organized seminars where leaders in labor, education, religion, and the social sciences debated how to mitigate prejudice in the workplace and in society.' ],
								[ 'text' => 'Carnegie Museum curator James Swauger was invited to a USW "Seminar on Human Relations" in 1951. He brought museum artifacts made by diverse cultural groups and — presenting them without identifying information — challenged participants to racially label or judge them. That collaboration eventually created the opportunity for "We Humans."' ],
							],
							'links'      => [
								[ 'label' => 'Read the opening remarks and see the list of participants at the October 1951 seminar. Document courtesy of the University of Pittsburgh Library System\'s Archives & Special Collections (Francis C. Shane Papers, 1942-1969, AIS.1996.03).', 'href' => 'https://drive.google.com/file/d/1vyM3GCXQ4XSBvbwI87INZNbqnSjcxaII/view?usp=sharing' ],
							],
						],
						[
							'title'      => 'UNESCO Statements on Race',
							'paragraphs' => [
								[ 'text' => 'In December 1949, a group of academics (mostly anthropologists) gathered in Paris to draft the first UNESCO Statement on Race of 1950, aiming to eliminate racial prejudice through knowledge. Ideas about race in anthropology were slowly shifting away from harmful comparisons between groups, even as many scholars still relied on now-discredited techniques like skull measuring to categorize people. The UNESCO Statement, and later "We Humans," reflect this moment of transition.' ],
								[ 'text' => 'The UNESCO Statement called racism out as a dangerous social myth not supported by science. It defined "races" as groups of humans who through geographic isolation and natural selection came to show distinct, but variable, physical traits. Nevertheless, it still named three main racial groups as known to science (none of which are recognized as accurate today): Caucasoid, Mongoloid, and Negroid.' ],
								[ 'text' => 'The UNESCO Statement thus combined messages about human equality that were progressive, and actually controversial at the time, alongside ideas that are now considered out of date and offensive. A few years later, "We Humans" repeated these same tensions.' ],
							],
							'links'      => [
								[ 'label' => 'Read the 2019 AABA statement on race that shows how ideas in anthropology have changed', 'href' => 'https://onlinelibrary.wiley.com/doi/10.1002/ajpa.23882' ],
								[ 'label' => 'Read What is Race? (UNESCO, 1952), which was owned by curator James Swauger, and had a strong influence on "We Humans"', 'href' => 'https://unesdoc.unesco.org/ark:/48223/pf0000067867' ],
							],
						],
					],
					'continue_label'  => 'Display & Reception',
					'continue_target' => 'wh-display',
				],
			],
			// 5. Display & Reception (timeline).
			[
				'id'              => 'wh-display',
					'include_in_menu' => '1',
				'family'  => 'timeline-world',
				'variant' => 'documentary',
				'props'   => [
					'id'                 => 'wh-display',
					'include_in_menu'    => '1',
					'menu_label'         => 'Display & Reception',
					'menu_description'   => 'Where was "We Humans" shown? And what did people say about it?',
					'eyebrow'            => 'Display & Reception',
					'title'   => 'Where was "We Humans" shown, and how did people respond?',
					'items'   => [
						[
							'date'  => 'February 1955',
							'title' => 'On view downtown',
							'body'  => '"We Humans" goes on view in the City-County Building in downtown Pittsburgh. Four display cases arrived on a staggered schedule to keep visitors returning.',
							'link'  => [ [ 'label' => 'See the first case (Teenie Harris, Pittsburgh Courier)', 'href' => 'https://collection.carnegieart.org/objects/2708a0a3-4fac-432f-9239-7613c333c102' ] ],
						],
						[
							'date'  => 'July 1955',
							'title' => '"Are You Ethnocentric?"',
							'body'  => 'A Pittsburgh Sun-Telegraph editorial praised the exhibit for challenging the belief that one\'s own group is superior. Some readers doubled down on their sense of superiority — one even claimed the anti-ethnocentric message would lead to Communism.',
							'media' => [
								[ 'src' => $im( 'are-you-ethnocentric.webp' ), 'alt' => 'Are You Ethnocentric? editorial', 'caption' => '"Are You Ethnocentric?," Pittsburgh Sun-Telegraph, July 8, 1955. From newspapers.com.' ],
								[ 'src' => $im( 'ethnocentric.webp' ), 'alt' => 'Ethnocentric reader responses', 'caption' => '"Ethnocentric," Pittsburgh Sun-Telegraph, July 15, 1955. From newspapers.com.' ],
							],
						],
						[
							'date'  => 'February 1956',
							'title' => 'Into the schools',
							'body'  => 'A modified, portable version tours Pittsburgh Public, parochial, and regional schools as a social-studies module. A USW-funded booklet circulated the content more broadly; the tour continued at least through 1959.',
							'media' => [ [ 'src' => $im( 'teacher-1959.webp' ), 'alt' => 'Teacher with a portable We Humans panel', 'caption' => 'A teacher at Monongahela High School with a portable "We Humans" panel, 1959. Photograph by Michel Chalufour. Courtesy of Carnegie Museum of Natural History Library and Archives.' ] ],
							'link'  => [ [ 'label' => 'Read responses from teachers and students at Schenley High School, the first school to host "We Humans." Document courtesy of Carnegie Museum of Natural History Section of Anthropology Archives.', 'href' => $pd( 'comments-from-teachers.pdf' ) ] ],
						],
						[
							'date'  => 'March 1956',
							'title' => 'Eleanor Roosevelt praises it',
							'body'  => 'First Lady Eleanor Roosevelt received a copy of the booklet from Swauger and praised it in her column "My Day," writing that "North and South alike must learn to evaluate human beings as such." Her column drew further interest.',
						],
						[
							'date'  => 'June 1956',
							'title' => 'In the Courier',
							'body'  => 'Ric Roberts wrote about the exhibit and its school tour in The Pittsburgh Courier. Its message — that humans differed by "type" but were fundamentally united and equal — resonated.',
							'media' => [ [ 'src' => $im( 'courier-1956.webp' ), 'alt' => 'Pittsburgh Courier article', 'caption' => 'Ric Roberts, "Treasure Trove: School, Churchmen Agree \'Race\' is Mythical," The Pittsburgh Courier, June 2, 1956. From newspapers.com.' ] ],
						],
						[
							'date'  => 'November 1959',
							'title' => 'A national tour',
							'body'  => 'With support from the American Jewish Committee and USW, a second portable version toured libraries nationwide. This month it opened at the San Francisco Public Library.',
							'media' => [ [ 'src' => $im( 'sf-library-1959.webp' ), 'alt' => 'We Humans at the San Francisco Public Library', 'caption' => 'Frank Clarvoe, Jr. and Hazel McFarlane of the San Francisco Public Library (left) with William K. Coblentz of the San Francisco chapter of the American Jewish Committee (right) with the temporary installation of "We Humans." Courtesy of the University of Pittsburgh Library System\'s Archives & Special Collections (Francis C. Shane Papers, 1942-1969, AIS.1996.03).' ] ],
						],
						[
							'date'  => 'September 1961',
							'title' => 'Miami',
							'body'  => '"We Humans" is displayed at the Miami Public Library in Florida, and a special about the exhibit airs on television there.',
						],
						[
							'date'  => 'January 1962',
							'title' => 'Plainville, Connecticut',
							'body'  => '"We Humans" is displayed at Plainville High School in Connecticut.',
						],
						[
							'date'  => 'October 1963',
							'title' => 'The Indiana Centennial',
							'body'  => '"We Humans" is displayed at the Indiana Centennial in Indianapolis.',
							'media' => [
								[ 'src' => $im( 'indianapolis-1.webp' ), 'alt' => 'We Humans on view in Indianapolis', 'caption' => '"We Humans" on view in Indianapolis, 1963. Courtesy of the University of Pittsburgh Library System\'s Archives & Special Collections (Francis C. Shane Papers, 1942-1969, AIS.1996.03).' ],
								[ 'src' => $im( 'indianapolis-2.webp' ), 'alt' => 'We Humans on view in Indianapolis', 'caption' => '"We Humans" on view in Indianapolis, 1963.' ],
							],
						],
						[
							'date'  => 'April 1964',
							'title' => 'Detroit',
							'body'  => '"We Humans" is displayed at the Detroit Historical Museum as part of Labor Education Week.',
						],
						[
							'date'  => 'October 1969',
							'title' => 'Swauger looks back',
							'body'  => 'Almost fifteen years after the debut of "We Humans," curator James Swauger reflected on the experience of developing the exhibit in the article "The Museum as Teacher," published in Current Anthropology. Still largely approving of the exhibit, he admitted it had flaws, writing: "We had to take complex, abstract concepts and present them as bald statements that would arouse curiosity in a viewer."',
							'link'  => [ [ 'label' => 'Read Swauger\'s full article. Document courtesy of Carnegie Museum of Natural History Section of Anthropology Archives.', 'href' => $pd( 'swauger-museum-as-teacher.pdf' ) ] ],
						],
						[
							'date'  => 'October 2014',
							'title' => 'Finding the Words',
							'body'  => 'When Carnegie Museum of Natural History hosts the acclaimed traveling exhibit RACE: Are We So Different? staff organized a small display on "We Humans" titled "Finding the Words: Pittsburgh and the Early Civil Rights Movement," striking a largely celebratory tone and emphasizing the positive intentions and unexpected reach of "We Humans" in its moment.',
						],
						[
							'date'  => 'September 2025',
							'title' => '"We Humans" at 70',
							'body'  => 'The exhibit "We Humans" at 70: Educating Pittsburgh on Race in the 1950s, organized by Deirdre Madeleine Smith (with Lindsey Kenny, then a University of Pittsburgh student and intern), opens at the Hyland Gallery at Hillman Library on the University of Pittsburgh campus. It shared the story of "We Humans" through original archival records and critical, reparative interpretation.',
							'media' => [ [ 'src' => $im( 'at-70.webp' ), 'alt' => 'We Humans at 70 exhibit in Hyland Gallery', 'caption' => '"We Humans" at 70: Educating Pittsburgh on Race in the 1950s. Hyland Gallery at Hillman Library, University of Pittsburgh. September 2025–July 2026. Photograph by Deirdre Madeleine Smith.' ] ],
						],
					],
					'continue_label'  => 'The Original Panels',
					'continue_target' => 'wh-panels-intro',
				],
			],
			// 6. Panels intro.
			[
				'id'              => 'wh-panels-intro',
					'include_in_menu' => '1',
				'family'  => 'feature-split',
				'variant' => 'documentary',
				'props'   => [
					'id'                 => 'wh-panels-intro',
					'include_in_menu'    => '1',
					'menu_label'         => 'The Original Panels',
					'menu_description'   => 'What was actually in "We Humans"?',
					'eyebrow'            => 'What was actually in "We Humans"?',
					'title'           => 'The Original Panels',
					'body'            => '"We Humans" consisted of eight panels that were originally displayed in four, double-sided cases. Each case paired one panel ("Panel A") aimed at sharing information about human biology and genetics with another ("Panel B") focused on human culture. The authors of these panels, anthropologists and curators James Swauger and Don Dragoo, aimed to encourage audiences to question their ideas about race and their attachments to the logic of racism. They did so in ways that today appear outdated, inaccurate, and detrimental. Using bold rhetoric and graphics, museum artifacts, and mannequin heads, Swauger and Dragoo presented messages that awkwardly shift between racial division and harmony.' . "\n\n" . 'Click on the panels to the right to study their content and learn more.',
					'image'           => $im( 'panel-1a.webp' ),
					'image_alt'       => 'We Humans, Case I, Panel A',
					'media_side'      => 'right',
					'continue_label'  => 'Explore the panels',
					'continue_target' => 'wh-panels',
				],
			],
			// 7. The Original Panels (gallery).
			[
				'id'              => 'wh-panels',
					'include_in_menu' => '0',
				'family'  => 'panel-explorer',
				'variant' => 'documentary',
				'props'   => [
					'id'              => 'wh-panels',
					'include_in_menu' => '0',
					'eyebrow' => 'The Original Panels',
					'title'   => 'Eight Panels, Four Cases',
					'intro'   => 'Click any panel to enlarge and learn more.',
					'items'   => [
						[
							'title'       => 'Case I, Panel A',
							'src'         => $im( 'panel-1a.webp' ),
							'alt'         => 'Case I, Panel A',
							'description' => 'In this first panel, Swauger and Dragoo both deconstructed and reinforced racial categories (a pattern throughout "We Humans"). They challenged their viewers to racially identify three mannequins each wearing "an American hat," thus suggesting that racial difference is not so easy (or relevant) to recognize. At the same time, they asserted that these categories were both real and knowable by anthropologists.',
							'annotations' => [
								[ 'x' => 26.6, 'y' => 22.9, 'title' => 'Physical differences are superficial', 'body' => 'Swauger and Dragoo emphasized that while physical differences existed between people, these were superficial, and mostly of concern to academics.' ],
								[ 'x' => 53.7, 'y' => 41.2, 'title' => 'Three mannequin heads', 'body' => 'This panel featured three mannequin heads whose features were based on an understanding from the time of the racial types: "Mongoloid," "Caucasoid," and "Negroid." These three categories were defined by anthropologists through differences in geographic origin and physical appearance, but are now known to be scientifically invalid.' ],
								[ 'x' => 50.7, 'y' => 69.5, 'title' => 'A response to Nazi race science', 'body' => 'The bold words at the bottom of this panel are an intended response to anti-semitism and Nazi race science. In the 1920s-1940s, the German Nazi Party had promoted rhetoric placing people into a false, racialized hierarchy in order to justify the genocidal violence of the Holocaust. "We Humans" was one of several public education campaigns led by anthropologists during and after the era of the Nazi regime which aimed at countering these falsehoods directly.' ],
								[ 'x' => 86.0, 'y' => 90.0, 'title' => 'Interpretation by Dr. Anthony Hazard', 'body' => 'Anthony Hazard is Professor of Ethnic Studies and History at Santa Clara University and author of Postwar Anti-racism: The U.S., Unesco and "Race," 1945-1968 (Palgrave, 2012) and Boasians At War: Anthropology, Race, and World War II (Palgrave, 2020). "This case portrays the tripartite schema of human populations or \'races\' which by and large remained a valid concept in physical anthropology at this time. In keeping with the context of the postwar period and mid-century anthropology, Case I recognizes the unity of the species Homo sapiens. Significantly, it describes ranges and broad distributions of physical characteristics in given \'races\' rather than fixed \'racial\' characteristics over time. Swauger and Dragoo thus embraced the theoretical synthesis of Darwinian evolution and Mendelian genetics which occurred over the first four decades of the twentieth century that rejected the notion of racial purity. Refuting basic tenets of Nazi race science, this case decouples nation and religion from race, while also placing emphasis on the \'scientific\' expertise of the anthropologists\' \'racket\' of \'laborious measuring.\' This embrace of physical anthropological methods of measuring bone, skull size, nose angle, etc. sits in tension with the presence of Neo-Darwinian ideas."' ],
							],
						],
						[
							'title'       => 'Case I, Panel B',
							'src'         => $im( 'panel-1b.webp' ),
							'alt'         => 'Case I, Panel B',
							'description' => 'In this panel, Swauger and Dragoo claimed that while racism, or "ethnocentrism," is inherently human, it can also be overcome by knowledge and reflection. They pulled examples from throughout human history of people either embracing or dismissing ethnocentric ideas.',
							'annotations' => [
								[ 'x' => 26.2, 'y' => 50.0, 'title' => 'Examples of "ethnocentric" thinking', 'body' => 'For examples of "ethnocentric" thinking, Swauger and Dragoo quote an ancient Egyptian pharaoh, the Greek dramatist Euripides, a late 19th century Chinese Minister of Education, and Adolf Hitler (the leader of the Nazi party), each claiming that one group is superior to another.' ],
								[ 'x' => 51.3, 'y' => 50.0, 'title' => 'Autonyms and group identity', 'body' => 'Swauger and Dragoo also stated that many Indigenous peoples espoused "ethnocentric" thinking, based on the fact that some Indigenous groups\' autonyms (the names they call themselves) can be translated into concepts like "the people." In this way, Swauger and Dragoo confused group identity with a sense of superiority. Not only did they make a harmful accusation here, they got information about group names and meanings wrong.' ],
								[ 'x' => 75.7, 'y' => 50.0, 'title' => 'Progressive Western voices', 'body' => 'As examples of humans with progressive views on human equality, Swauger and Dragoo quoted: Greek playwright Menander, Jesus of Nazareth, founding father Thomas Jefferson, German writer Johann Wolfgang von Goethe, and American anthropologist George P. Murdock. In privileging the ideas of Western figures in this way, Swauger and Dragoo seem to undermine their message of equality.' ],
								[ 'x' => 67.2, 'y' => 70.5, 'title' => 'A risk of trivializing', 'body' => 'While Swauger and Dragoo promoted a positive idea that racism could, and needed to, be challenged, they also risked trivializing the matter by writing things like, "And it is silly, isn\'t it?"' ],
							],
						],
						[
							'title'       => 'Case II, Panel A',
							'src'         => $im( 'panel-2a.webp' ),
							'alt'         => 'Case II, Panel A',
							'description' => 'In this panel, the mannequin heads from the first panel appear again, this time culturally (and "racially") marked by different headdresses, all borrowed from the Carnegie Museum of Natural History\'s anthropology collection. While the intention may have been to suggest that people picked up on cultural rather than racial cues when they tried to categorize each other, Swauger and Dragoo also inaccurately racially categorized the cultures from which these headdresses came. They also divorced these items from their original context and meaning.',
							'annotations' => [
								[ 'x' => 25.2, 'y' => 43.1, 'title' => 'War bonnet', 'body' => 'On the left of the panel, a Native American (identified as "Sioux Indian") war bonnet sits on the head of the "Mongoloid" mannequin. These feathered headdresses are markers of earned social status among Native peoples of the Great Plains.' ],
								[ 'x' => 53.6, 'y' => 36.0, 'title' => 'Turban', 'body' => 'In the center, the "Caucasoid" mannequin wears a turban and is identified as "Hindu." This choice was a possible allusion to the pagri, a kind of turban worn on the Indian subcontinent. It bears repeating, however, that all of the racial categorizations used by Swauger and Dragoo were based on faulty premises now rejected by science.' ],
								[ 'x' => 73.6, 'y' => 27.1, 'title' => 'Headdress from Malawi', 'body' => 'On the right, the "Negroid" mannequin wears a headdress from Malawi, a country in East Africa. While Swauger and Dragoo identify these items as coming from the Maasai people, the Maasai are not typically known to live in Malawi, thus placing this attribution into question.' ],
							],
						],
						[
							'title'       => 'Case II, Panel B',
							'src'         => $im( 'panel-2b.webp' ),
							'alt'         => 'Case II, Panel B',
							'description' => 'For this panel, Swauger and Dragoo selected, categorized and pinned to a board objects from the Carnegie Museum of Natural History\'s anthropology collections that are of a similar material and manufacture, but that come from different regions of the world. They intentionally left the items unlabeled in order to encourage viewers to question their ability to identify and judge the items in racial terms.',
							'annotations' => [
								[ 'x' => 19.3, 'y' => 72.4, 'title' => '"Are you a wizard?"', 'body' => 'Swauger and Dragoo pose a provocative question ("Are you a wizard?"), implying that it would take magic powers for someone to be able to label the items in this panel according to a racial group. This framing represents an interesting break from their usual emphasis on science and facts.' ],
								[ 'x' => 15.1, 'y' => 25.8, 'title' => 'Native American materials', 'body' => 'On the left materials from Native American cultures are displayed, such as this tray made of elm bark, which is attributed to the Seneca people and was made before 1910.' ],
								[ 'x' => 57.5, 'y' => 31.0, 'title' => 'African materials', 'body' => 'In the center are materials from the African continent. This is a cloth mat made by Kuba people from the Democratic Republic of the Congo.' ],
								[ 'x' => 89.9, 'y' => 57.1, 'title' => 'European objects', 'body' => 'To the right are objects with origins in present-day Europe, such as this stone blade, an object from prehistoric Scandinavia.' ],
								[ 'x' => 86.0, 'y' => 90.0, 'title' => 'Interpretation by Kristina Gaugler', 'body' => 'Kristina Gaugler is Anthropology Collection Manager, Carnegie Museum of Natural History. "As was typical for a 1950s museum exhibition, this panel showcases objects devoid of both context and humanity. By grouping these items together using a racial typology, all the nuance, care, history, and purpose that went into creating them is reduced to something with very little meaning. In museum spaces today, particularly when it comes to culture studies, we strive to tell authentic stories, with community involvement, where objects are displayed as complementary to these narratives rather than used as a prop for a particular ideology. I do commend our Carnegie Museum of Natural History forbearers for their effort in trying to educate our communities with the message that, ultimately, we are all not so different. Unfortunately, by today\'s standards, their execution leaves a lot to be desired."' ],
								[ 'x' => 92.0, 'y' => 90.0, 'title' => 'Interpretation by Amy Covell-Murthy', 'body' => 'Amy Covell-Murthy is Archaeology Collection Manager and Head of the Section of Anthropology, Carnegie Museum of Natural History. "This is not how we organize and interpret cultural material for exhibition purposes at Carnegie Museum of Natural History any longer. Our focus now is to provide a platform for authentic voices and to share authority with members of the communities from where these collections originated. We prioritize relationships over objects and celebrate the diversity of human experience. While Dragoo and Swauger\'s work in the 1950s was commendable, we can now acknowledge that taking a multicultural approach to understanding how inequities are rooted in systemic racism is crucial to building a better message."' ],
							],
						],
						[
							'title'       => 'Case III, Panel A',
							'src'         => $im( 'panel-3a.webp' ),
							'alt'         => 'Case III, Panel A',
							'description' => 'In this panel, Swauger and Dragoo use a modified graphic of a world map and simple, color-coded figurines to illustrate the idea of genetic variation due to geographic isolation. While they correctly identified geographic isolation as a driver of changes in outward appearance, they misidentified these variations as "racial" on a genetic level. They write that "Man\'s Birthplace" is unknown, but today we know that the human species (Homo sapiens) emerged in Africa.',
							'annotations' => [
								[ 'x' => 86.0, 'y' => 90.0, 'title' => 'Interpretation by Dr. Anthony Hazard', 'body' => 'Anthony Hazard is Professor of Ethnic Studies and History at Santa Clara University and author of Postwar Anti-racism: The U.S., Unesco and "Race," 1945-1968 (Palgrave, 2012) and Boasians At War: Anthropology, Race, and World War II (Palgrave, 2020). "Prior to paleoanthropologists Mary and Louis Leakey\'s discoveries of hominin fossils at Olduvai Gorge in northern Tanzania, scientists largely postulated that Homo sapiens originated in Europe or Asia. This panel offers southern Asia as the birthplace of the human species, with movement eastward resulting in the development of the Mongoloid race, movement westward into Africa resulting in the Negroid race, and movement to the northwest resulting in the Caucasoid race in Europe. Pointing to heredity, mutation, and natural selection, this panel echoes Case I, Panel A in highlighting the Neo-Darwinian synthesis as the evolutionary driver of human diversity. Geographic isolation of populations then results in the emergence of racial difference. Neither panel engages the controversial deconstructionist position of anthropologist Ashley Montagu, who in the 1940s defined race as a myth. Rather, Swauger and Dragoo confirm the existence of biological races in the human species."' ],
							],
						],
						[
							'title'       => 'Case III, Panel B',
							'src'         => $im( 'panel-3b.webp' ),
							'alt'         => 'Case III, Panel B',
							'description' => 'In this example of a "cultural" panel, an image of a person who appears coded as white, male, and middle class sits before a hearty breakfast. With this pairing of text and image, Swauger and Dragoo attempted to demonstrate how incoherent racism is: people will espouse discriminatory views of a group of people while enjoying food or other cultural exports from that same group.',
							'annotations' => [
								[ 'x' => 60.7, 'y' => 26.0, 'title' => 'Who are the "other peoples"?', 'body' => 'The language chosen here is telling: Who are the "other peoples" being referred to? "[T]he waitress" is the only time that a feminine-coded person is mentioned throughout "We Humans."' ],
								[ 'x' => 75.2, 'y' => 70.4, 'title' => 'Categorizing the breakfast table', 'body' => 'In this section, Swauger and Dragoo categorize the items on the breakfast table according to their racial scheme of Mongoloid, Negroid, and Caucasoid. In doing so, they conflated the geographic origins of these foods with the racial types of peoples who share the same origins.' ],
								[ 'x' => 92.0, 'y' => 90.0, 'title' => 'Interpretation by Tracy Teslow', 'body' => 'Tracy Teslow is Associate Professor of History, University of Cincinnati and author of Constructing Race: The Science of Bodies and Cultures in American Anthropology (Cambridge University Press, 2014). "Midcentury American anthropologists encouraged the public to question their assumptions of racial and cultural superiority through exhibitions and publications. In an era of great faith in the ability of science to offer objective truth and clear-eyed solutions to problems, scholars believed that factual instruction could effectively combat social ills such as racial prejudice. \'We Humans\' employed one of these anthropologists\' favorite approaches—illustrating the global origins of everyday items. Drawing on their encyclopedic knowledge of world history, cultures, and commerce, scholars sought to demonstrate the extensive borrowing underlying American culture (but rarely the colonial and commercial networks that enabled it). The newspaper the man in this panel might read with his breakfast was printed using a process invented in Germany, upon a material invented in China, in an Indo-European language, using characters invented by ancient Semites. The morning coffee he enjoys came from an Ethiopian plant discovered by Arabs. In \'We Humans\', this cultural diffusion has been reduced to racialized terms, \'Negroid,\' \'Mongoloid,\' and \'Caucasoid,\' standing in for more complex processes of heritage and exchange. This panel tried to undermine ethnocentric chauvinism, even as it reinforced unexamined racial categories. Similarly, the featured diner, a white man in a business suit, centers assumptions about who was a \'typical\' American, even as it points to the intended audience. \'We Humans\' echoed earlier \'Races of Mankind\' texts and exhibitions with similar anti-racist goals, including a 1943 pamphlet by anthropologists Ruth Benedict and Gene Weltfish."' ],
							],
						],
						[
							'title'       => 'Case IV, Panel A',
							'src'         => $im( 'panel-4a.webp' ),
							'alt'         => 'Case IV, Panel A',
							'description' => 'To produce this panel, James Swauger reached out to the World Health Organization to ask for examples of situations where a multi-racial coalition had collaborated to solve a health crisis. The panel focuses on Yaws, a bacterial infection that causes skin, tissue, and nerve damage. Through the evidence of Yaws, Swauger and Dragoo argued that not only do people of all races get sick with the same diseases, they also have the ability to work toward their cures.',
							'annotations' => [
								[ 'x' => 86.0, 'y' => 90.0, 'title' => 'Interpretation by Dr. Anthony Hazard', 'body' => 'Anthony Hazard is Professor of Ethnic Studies and History at Santa Clara University and author of Postwar Anti-racism: The U.S., Unesco and "Race," 1945-1968 (Palgrave, 2012) and Boasians At War: Anthropology, Race, and World War II (Palgrave, 2020). "Highlighting the prevalence of a particular infectious medical malady in various regions of the globe speaks to the biological unity of human beings despite the occurrence of phenotypic variation. This focus on global sameness also rejects the belief that certain \'races\' are biologically predisposed to certain medical diseases or conditions, though without a useful exploration of an environmental explanation for discrepancies between \'races\' in the prevalence of certain medical conditions. This case also makes a claim about the equal potential (\'equipotentiality\') of all people to become professionally trained medical experts, no matter who they are or where they are from, a position staked out in the early twentieth century by W. E. B. Du Bois, Franz Boas, and others. The spirit of global or transnational cooperation in medical training and the application of recently developed medicines and treatments is presented as a necessity in improving the living conditions of people around the globe."' ],
							],
						],
						[
							'title'       => 'Case IV, Panel B',
							'src'         => $im( 'panel-4b.webp' ),
							'alt'         => 'Case IV, Panel B',
							'description' => 'In the final panel, Swauger and Dragoo equate human cultures to "designs for living," reaffirming the assertions from Case II, Panel B that all groups are equally valid in their approach to fundamental, and universal, aspects of how to survive and live as humans.',
							'annotations' => [
								[ 'x' => 20.6, 'y' => 45.9, 'title' => 'Arts and Crafts', 'body' => 'This section displays (from top to bottom) spoons made by the Haida people of the Pacific Northwest (United States), knives from Russia, and spoons from Cameroon, West Africa.' ],
								[ 'x' => 40.5, 'y' => 34.7, 'title' => 'Religion', 'body' => 'This section displays (from left to right) a wooden figure from the Democratic Republic of the Congo, three metal figures from India, and a Hopi katsina from the American Southwest.' ],
								[ 'x' => 61.7, 'y' => 45.0, 'title' => 'War', 'body' => 'This section juxtaposes three swords (left to right): one from Japan, one from the Democratic Republic of the Congo, and one from Germany.' ],
							],
						],
					],
					'continue_label'  => 'Conclusion & Reflection',
					'continue_target' => 'wh-contradiction',
				],
			],
			// 8b. Conclusion — The Contradiction at the Core.
			[
				'id'              => 'wh-contradiction',
					'include_in_menu' => '1',
				'family'  => 'documentary-dossier',
				'variant' => 'archival',
				'props'   => [
					'id'                 => 'wh-contradiction',
					'include_in_menu'    => '1',
					'menu_label'         => 'Contradictions',
					'menu_description'   => 'How should we think and feel about "We Humans" today?',
					'eyebrow'            => 'Conclusion & Reflection',
					'title'              => 'The Contradiction at the Core',
					'intro'              => [
						'Both of these statements are true:',
						'"We Humans" was an optimistic, collaborative response to racial turmoil that had a national impact on diverse audiences of students, workers, and other Americans with a message of equality, grounded in science, that was widely praised in its time.',
						'"We Humans" was an effort by white men in positions of power to meet a moment of racial reckoning through an education campaign that masked structural racism behind condescending rhetoric that has since been disproved and largely forgotten.',
						'These competing realities challenge audiences today to make sense of "We Humans," and similar projects that emerged in the aftermath of World War II and at the dawn of the Civil Rights Movement. This final section of the exhibit provides some additional context for you to consider, and invites you to reflect on what you have learned.',
					],
					'sections'           => [
						[
							'title'      => 'The United Steelworkers of America',
							'paragraphs' => [
								'"We Humans" was touched off by the labor union The United Steelworkers of America and events being organized by their Civil Rights Committee. That committee was formed as a result of Black worker-led organizing to advocate for better and safer opportunities for Black workers in industry. When the Committee was formed, none of those workers were placed in positions of leadership. Instead, the leadership consisted entirely of white men, including secretary Francis Shane who initiated the collaboration with James Swauger and Carnegie Museum of Natural History. Over time, Shane\u2019s emphasis on seminars and public education and efforts like "We Humans" became increasingly frustrating to Black workers who felt that the Civil Rights Committee had proved ineffectual at securing their safety and advancement.',
							],
							'links'      => [],
						],
						[
							'title'      => 'The City of Pittsburgh',
							'paragraphs' => [
								'Mayor David L. Lawrence\u2019s \u201CCivic Unity Council\u201D was a key partner in the development of "We Humans," and was the entity that contracted with the Carnegie Museum to co-produce and host the initial exhibit. The Mayor\u2019s Council was officially tasked with promoting positive relations between racial, religious and other groups, and its activities included reports on housing and investigations into police brutality.',
								'In 1955, the same year that Lawrence\u2019s administration presented a message of racial and social unity with "We Humans," it approved actions that had disproportionate negative impacts on Black and other minority citizens. As part of his signature urban renewal plans, Lawrence allowed for the demolition of the Lower Hill District to make way for the Civic Arena (itself demolished in 2011). This resulted in the displacement of 8,000 residents, including 1,239 Black families.',
							],
							'links'      => [],
						],
						[
							'title'      => 'Pittsburgh Public Schools',
							'paragraphs' => [
								'Students hearing the messages of "We Humans" in their social studies classes had to reconcile them with their lives in a multi-racial, but in many contexts segregated, city where they would have encountered or witnessed forms of racism daily. They were also likely aware of the galvanizing events in the history of civil rights and race relations in the U.S. that were happening in the same years, including: the lynching of Emmett Till (August 1955), the Montgomery Bus Boycott (1955\u20131956), and the ongoing events and debates that surrounded the desegregation of schools after the Supreme Court decision striking down legal segregation in Brown v. Board of Education (1954).',
								'Scholar and author Ralph Proctor, Jr. (1938\u20132024) was born and raised in Pittsburgh\u2019s Hill District and attended Schenley High School in the same years that "We Humans" was taught there. In Proctor\u2019s experience, Schenley was a school where he and other Black students faced rampant discrimination by teachers and administrators. Later, he earned his doctorate at University of Pittsburgh where he wrote a dissertation on racial discrimination against Black teachers and professionals in Pittsburgh Public Schools. In it, he cited statistics showing that in the years that he attended Schenley, while the Black student population increased, the white student population decreased, and there were few Black teachers.',
							],
							'links'      => [],
						],
					],
					'continue_label'     => 'Reflection',
					'continue_target'    => 'wh-reflect',
				],
			],
			// 9. Conclusion & Reflection.
			[
				'id'              => 'wh-reflect',
					'include_in_menu' => '1',
				'family'  => 'reflection-prompt',
				'variant' => 'journal',
				'props'   => [
					'id'                 => 'wh-reflect',
					'include_in_menu'    => '1',
					'menu_label'         => 'Reflections',
					'menu_description'   => 'Share your thoughts on "We Humans"',
					'eyebrow'            => 'Conclusion & Reflection',
					'title'           => 'Reflection',
					'intro'            => 'The following questions are offered for you to reflect on what you have learned about "We Humans." Answer as many or few as you wish. When you are done, you may download your responses, or submit them to be shared. Your responses may be used in future publications or even featured on this site.',
					'cards'            => [
						[ 'title' => 'Familiarity', 'body' => 'What feels most familiar to you about "We Humans"? Have you encountered any similar education campaigns or exhibits in your life?' ],
						[ 'title' => 'Surprise', 'body' => 'What feels most unfamiliar, or surprising, about "We Humans"?' ],
						[ 'title' => 'Today', 'body' => 'Do you think a project like "We Humans" could happen today? Imagine how such a project might play out in your own social and political context.' ],
						[ 'title' => 'Forgetting', 'body' => 'Why do you think projects like "We Humans" have been forgotten over time?' ],
						[ 'title' => 'The Future', 'body' => 'What will people in the future say about the efforts at education on race and racism of your time?' ],
						[ 'title' => 'Action', 'body' => 'How are you called to address racism and prejudice in your moment?' ],
					],
					'prompt'          => 'How are you called to address racism and prejudice in your moment?',
					'continue_label'  => 'About this exhibit',
					'continue_target' => 'wh-about',
				],
			],
			// 10. About — curator.
			[
				'id'              => 'wh-about',
					'include_in_menu' => '1',
				'family'  => 'feature-split',
				'variant' => 'documentary',
				'props'   => [
					'id'                 => 'wh-about',
					'include_in_menu'    => '1',
					'menu_label'         => 'About',
					'menu_description'   => 'Credits, Acknowledgements, and Sources',
					'eyebrow'            => 'About',
					'title'           => 'About this exhibit',
					'body'            => 'This exhibit was developed in 2025–2026 by Deirdre Madeleine Smith, a Teaching Assistant Professor in the History of Art and Architecture Department at the University of Pittsburgh and Curator of Museum Studies and Art at Carnegie Museum of Natural History. An earlier version was hosted in the Hyland Gallery at Hillman Library, co-curated by student intern Lindsey Kenny with University of Pittsburgh Library System staff Megan Massanelli and Madeleine Chesek-Welch.',
					'image'           => $im( 'about.webp' ),
					'image_alt'       => 'Curator Deirdre Madeleine Smith in front of the We Humans exhibit, 2025',
					'caption'         => 'Curator Deirdre Madeleine Smith in front of the "We Humans" exhibit in Hyland Gallery, 2025. Photograph by Ron Idoko.',
					'media_side'      => 'left',
					'continue_label'  => 'Credits, Acknowledgements & Sources',
					'continue_target' => 'wh-credits',
				],
			],
			// 11. Credits, Acknowledgements & Sources.
			[
				'id'              => 'wh-credits',
					'include_in_menu' => '0',
				'family'  => 'documentary-dossier',
				'variant' => 'archival',
				'props'   => [
					'id'              => 'wh-credits',
					'include_in_menu' => '0',
					'eyebrow'  => 'About',
					'title'    => 'Credits, Acknowledgements & Sources',
					'intro'    => [],
					'sections' => [
						[
							'title'      => 'Acknowledgments',
							'paragraphs' => [
								[ 'text' => 'Thanks to the following individuals for their support in the development of this project:' ],
								[ 'text' => 'Gretchen Baker, Jenise Brown, Marie Corrado, Amy Covell-Murthy, Sarah Crawford, Sydney Dominick, Christopher Fleisher, Kristina Gaugler, Laurie Giarratani, Ron Idoko, Morgan Riggenbach, Keirstin Rotharmel, Ellen Sanin, Rachel Thomas-Beckel, Breann Thompson, Annick Vuissoz, Amy Whipple, Ginger White, Gina Winstead.' ],
							],
							'links'      => [],
						],
						[
							'title'      => 'Sources consulted and recommended',
							'paragraphs' => [
								[ 'text' => 'Archives:' ],
								[ 'text' => 'Carnegie Museum of Natural History, Section of Anthropology Archives.' ],
								[ 'text' => 'Carnegie Museum of Natural History, Library & Archives.' ],
								[ 'text' => 'Heinz History Center, Pittsburgh Public Schools Records.' ],
								[ 'text' => 'University of Pittsburgh Library System Archives & Special Collections, Francis C. Shane Papers, 1942–1969.' ],
								[ 'text' => 'Books & articles:' ],
								[ 'text' => 'Anthony Hazard, Postwar Anti-racism (Palgrave, 2012).' ],
								[ 'text' => 'James B. Stewart, "Civil Rights and Organized Labor" (2005).' ],
								[ 'text' => 'Tracy Teslow, Constructing Race (Cambridge, 2014).' ],
								[ 'text' => 'Joe W. Trotter Jr. & Jared N. Day, Race and Renaissance (Pittsburgh, 2010).' ],
							],
							'links'      => [
								[ 'label' => 'Curator: Deirdre Madeleine Smith (Pitt)', 'href' => 'https://www.haa.pitt.edu/people/deirdre-madeleine-smith' ],
							],
						],
					],
				],
			],
		],
	];
}

/**
 * Insert a demo quote post.
 */
function reci_demo_insert_quote( array $data ): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, 'reci_quote' );
	if ( $existing ) {
		return;
	}

	$post_id = wp_insert_post( [
		'post_type'    => 'reci_quote',
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_content' => '',
	] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, '_reci_quote_text', $data['text'] );
	update_post_meta( $post_id, '_reci_quote_author_name', $data['author_name'] );
	update_post_meta( $post_id, '_reci_quote_author_title', $data['author_title'] );
	update_post_meta( $post_id, '_reci_demo', '1' );

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Ensure taxonomy terms exist; returns slug → term_id map.
 */
function reci_demo_ensure_terms( string $taxonomy, array $names ): array {
	$map = [];
	foreach ( $names as $name ) {
		$term = term_exists( $name, $taxonomy );
		if ( ! $term ) {
			$term = wp_insert_term( $name, $taxonomy );
		}
		if ( ! is_wp_error( $term ) ) {
			$map[ $name ] = (int) ( $term['term_id'] ?? $term );
		}
	}
	return $map;
}

/**
 * Insert a demo post if its slug doesn't already exist.
 */
function reci_demo_insert_post(
	string $post_type,
	array $data,
	array $topics,
	array $locations,
	array &$imgs
): void {
	$existing = get_page_by_path( $data['slug'], OBJECT, $post_type );
	if ( $existing ) {
		return;
	}

	$post_args = [
		'post_type'    => $post_type,
		'post_status'  => 'publish',
		'post_name'    => $data['slug'],
		'post_title'   => $data['title'],
		'post_excerpt' => $data['excerpt'],
		'post_content' => $data['content'],
	];

	if ( ! empty( $data['post_date'] ) ) {
		$post_args['post_date']     = (string) $data['post_date'];
		$post_args['post_date_gmt'] = get_gmt_from_date( (string) $data['post_date'] );
	}

	$post_id = wp_insert_post( $post_args );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	foreach ( ( $data['meta'] ?? [] ) as $key => $val ) {
		update_post_meta( $post_id, $key, $val );
	}

	if ( ! empty( $data['show'] ) ) {
		$term = term_exists( $data['show'], 'reci_show' );
		if ( ! $term ) {
			$term = wp_insert_term( $data['show'], 'reci_show' );
		}
		if ( ! is_wp_error( $term ) ) {
			$show_id = is_array( $term ) ? $term['term_id'] : $term;
			wp_set_object_terms( $post_id, [ (int) $show_id ], 'reci_show', false );
		}
	}

	if ( empty( $data['author_name'] ) ) {
		$data['author_name'] = 'RECI';
	}
	
	if ( ! empty( $data['author_name'] ) ) {
		$author_profile_id = reci_demo_ensure_author_profile([
			'slug'    => sanitize_title( (string) $data['author_name'] ),
			'name'    => (string) $data['author_name'],
			'title'   => (string) ( $data['author_title'] ?? 'RECI Contributor' ),
			'bio'     => (string) ( $data['author_bio'] ?? '' ),
			'content' => (string) ( $data['author_content'] ?? '' ),
		]);

		if ( $author_profile_id > 0 ) {
			update_post_meta( $post_id, '_reci_display_author_profile_id', $author_profile_id );
		}
	}
	update_post_meta( $post_id, '_reci_demo', '1' );

	$allowed_categories = [
		'Systemic Racism', 'Intersectionality', 'Cultural Identity', 'Workplace Equity',
		'Community Action', 'Education', 'Health Disparities', 'Criminal Justice',
		'Indigenous Rights', 'Technology & Equity'
	];

	// Assign category terms from either 'category' or 'topics' keys since 'reci_topic' was merged into 'category'
	$all_categories = array_unique(array_merge(
		(array) ($data['category'] ?? []),
		(array) ($data['topics'] ?? [])
	));

	$valid_cats = [];
	
	foreach ( $all_categories as $cat_name ) {
		if ( in_array( $cat_name, $allowed_categories, true ) ) {
			$valid_cats[] = $cat_name;
		}
	}

	if ( ! empty( $valid_cats ) ) {
		$cat_ids = [];
		foreach ( $valid_cats as $cat_name ) {
			$term = term_exists( $cat_name, 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $cat_name, 'category' );
			}
			if ( ! is_wp_error( $term ) ) {
				$cat_ids[] = (int) ( $term['term_id'] ?? $term );
			}
		}
		if ( ! empty( $cat_ids ) ) {
			wp_set_post_categories( $post_id, $cat_ids, false );
		}
	}

	// Assign post_tags.
	$tags_to_assign = array_unique( (array) ( $data['tags'] ?? [] ) );
	if ( ! empty( $tags_to_assign ) ) {
		wp_set_post_tags( $post_id, $tags_to_assign, true );
	}

	$sphere_ids = [];
	foreach ( ( $data['spheres'] ?? [] ) as $sphere_name ) {
		$term = term_exists( $sphere_name, 'reci_sphere' );
		if ( $term ) {
			$sphere_ids[] = (int) ( $term['term_id'] ?? $term );
		}
	}
	if ( $sphere_ids ) {
		wp_set_object_terms( $post_id, $sphere_ids, 'reci_sphere', false );
	}

	if ( ! empty( $data['sdgs'] ) ) {
		$sdg_ids = [];
		foreach ( (array) $data['sdgs'] as $sdg_name ) {
			$term = term_exists( $sdg_name, 'sdgs' );
			if ( ! $term ) {
				$term = wp_insert_term( $sdg_name, 'sdgs' );
			}
			if ( ! is_wp_error( $term ) ) {
				$sdg_ids[] = (int) ( $term['term_id'] ?? $term );
			}
		}
		if ( ! empty( $sdg_ids ) ) {
			wp_set_object_terms( $post_id, $sdg_ids, 'sdgs', false );
		}
	}

	if ( isset( $locations['Pittsburgh'] ) ) {
		wp_set_object_terms( $post_id, [ $locations['Pittsburgh'] ], 'reci_location', false );
	}

	if ( ! empty( $data['image'] ) ) {
		$attachment_id = reci_demo_sideload_image( $data['image'], $post_id, $imgs );
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	$slugs   = get_option( 'reci_demo_slugs', [] );
	$slugs[] = $data['slug'];
	update_option( 'reci_demo_slugs', array_unique( $slugs ) );
}

/**
 * Sideload an image from theme's demo-content/images/ directory into the media library.
 */
function reci_demo_sideload_image( string $filename, int $post_id, array &$imgs, string &$error_message = '' ): int {
	if ( isset( $imgs[ $filename ] ) ) {
		return $imgs[ $filename ];
	}

	$file_path = get_template_directory() . '/demo-content/images/' . ltrim( $filename, '/' );

	if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
		$error_message = 'Source file is missing or unreadable.';
		return 0;
	}

	$filesize = filesize( $file_path );
	if ( false === $filesize || $filesize <= 0 ) {
		$error_message = 'Source file is empty.';
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$file_contents = file_get_contents( $file_path );
	if ( false === $file_contents ) {
		wp_die( "<h1>Critical Import Error</h1><p>Your server is blocking PHP from reading local files. <br>Path: $file_path</p><p>You must either use HTTP sideloading or change your server's open_basedir/read permissions.</p>" );
	}

	$upload = wp_upload_bits( wp_basename( $file_path ), null, $file_contents );
	if ( ! empty( $upload['error'] ) ) {
		$error_message = 'Upload failed: ' . $upload['error'];
		return 0;
	}

	$check = wp_check_filetype( wp_basename( $file_path ) );
	$mime_type = ! empty( $check['type'] ) ? $check['type'] : 'image/jpeg';

	$base_name = pathinfo( wp_basename( $file_path ), PATHINFO_FILENAME );
	
	$attachment_id = wp_insert_attachment(
		[
			'post_mime_type' => $mime_type,
			'post_title'     => $base_name . ' Image',
			'post_name'      => sanitize_title( $base_name ) . '-img',
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
		],
		$upload['file'],
		$post_id
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		$error_message = 'Attachment creation failed' . ( is_wp_error( $attachment_id ) ? ': ' . $attachment_id->get_error_message() : '.' );
		if ( ! empty( $upload['file'] ) && file_exists( $upload['file'] ) ) {
			wp_delete_file( $upload['file'] );
		}
		return 0;
	}

	$metadata = reci_demo_generate_attachment_metadata( $attachment_id, $upload['file'] );
	if ( is_wp_error( $metadata ) ) {
		$error_message = 'Metadata generation failed: ' . $metadata->get_error_message();
		wp_delete_attachment( $attachment_id, true );
		return 0;
	}
	wp_update_attachment_metadata( $attachment_id, $metadata );

	$imgs[ $filename ] = $attachment_id;
	return $attachment_id;
}

function reci_demo_sideload_image_from_url( string $registry_key, string $url, int $post_id, array &$imgs, string &$error_message = '' ): int {
	if ( isset( $imgs[ $registry_key ] ) ) {
		return $imgs[ $registry_key ];
	}

	if ( '' === $url ) {
		$error_message = 'Remote source URL is empty.';
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$response = wp_remote_get( $url, [ 'timeout' => 30 ] );
	if ( is_wp_error( $response ) ) {
		$error_message = 'Remote download failed: ' . $response->get_error_message();
		return 0;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		$error_message = 'Remote download returned HTTP ' . $code . '.';
		return 0;
	}

	$file_contents = wp_remote_retrieve_body( $response );
	if ( '' === $file_contents ) {
		$error_message = 'Remote source returned an empty body.';
		return 0;
	}

	$filename = wp_basename( wp_parse_url( $url, PHP_URL_PATH ) ?: $registry_key );
	if ( '' === $filename ) {
		$filename = sanitize_title( basename( $registry_key ) ) . '.jpg';
	}

	$upload = wp_upload_bits( $filename, null, $file_contents );
	if ( ! empty( $upload['error'] ) ) {
		$error_message = 'Upload failed: ' . $upload['error'];
		return 0;
	}

	$check = wp_check_filetype( $filename );
	$mime_type = ! empty( $check['type'] ) ? $check['type'] : 'image/jpeg';
	$base_name = pathinfo( $filename, PATHINFO_FILENAME );

	$attachment_id = wp_insert_attachment(
		[
			'post_mime_type' => $mime_type,
			'post_title'     => $base_name . ' Image',
			'post_name'      => sanitize_title( $base_name ) . '-img',
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
		],
		$upload['file'],
		$post_id
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		$error_message = 'Attachment creation failed' . ( is_wp_error( $attachment_id ) ? ': ' . $attachment_id->get_error_message() : '.' );
		if ( ! empty( $upload['file'] ) && file_exists( $upload['file'] ) ) {
			wp_delete_file( $upload['file'] );
		}
		return 0;
	}

	$metadata = reci_demo_generate_attachment_metadata( $attachment_id, $upload['file'] );
	if ( is_wp_error( $metadata ) ) {
		$error_message = 'Metadata generation failed: ' . $metadata->get_error_message();
		wp_delete_attachment( $attachment_id, true );
		return 0;
	}

	wp_update_attachment_metadata( $attachment_id, $metadata );
	$imgs[ $registry_key ] = $attachment_id;

	return $attachment_id;
}

function reci_demo_sideload_image_from_path( string $registry_key, string $file_path, int $post_id, array &$imgs, string &$error_message = '' ): int {
	if ( isset( $imgs[ $registry_key ] ) ) {
		return $imgs[ $registry_key ];
	}

	if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
		$error_message = 'Extracted file is missing or unreadable.';
		return 0;
	}

	$file_contents = file_get_contents( $file_path );
	if ( false === $file_contents || '' === $file_contents ) {
		$error_message = 'Extracted file is empty.';
		return 0;
	}

	$filename = wp_basename( $file_path );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$upload = wp_upload_bits( $filename, null, $file_contents );
	if ( ! empty( $upload['error'] ) ) {
		$error_message = 'Upload failed: ' . $upload['error'];
		return 0;
	}

	$check = wp_check_filetype( $filename );
	$mime_type = ! empty( $check['type'] ) ? $check['type'] : 'image/jpeg';
	$base_name = pathinfo( $filename, PATHINFO_FILENAME );

	$attachment_id = wp_insert_attachment(
		[
			'post_mime_type' => $mime_type,
			'post_title'     => $base_name . ' Image',
			'post_name'      => sanitize_title( $base_name ) . '-img',
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
		],
		$upload['file'],
		$post_id
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		$error_message = 'Attachment creation failed' . ( is_wp_error( $attachment_id ) ? ': ' . $attachment_id->get_error_message() : '.' );
		if ( ! empty( $upload['file'] ) && file_exists( $upload['file'] ) ) {
			wp_delete_file( $upload['file'] );
		}
		return 0;
	}

	$metadata = reci_demo_generate_attachment_metadata( $attachment_id, $upload['file'] );
	if ( is_wp_error( $metadata ) ) {
		$error_message = 'Metadata generation failed: ' . $metadata->get_error_message();
		wp_delete_attachment( $attachment_id, true );
		return 0;
	}

	wp_update_attachment_metadata( $attachment_id, $metadata );
	$imgs[ $registry_key ] = $attachment_id;

	return $attachment_id;
}

function reci_demo_collect_importable_files_from_directory( string $directory ): array {
	if ( ! is_dir( $directory ) ) {
		return [];
	}

	$paths = [];
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $file ) {
		if ( ! $file instanceof SplFileInfo || ! $file->isFile() ) {
			continue;
		}
		$extension = strtolower( $file->getExtension() );
		if ( ! in_array( $extension, [ 'jpg', 'jpeg', 'png', 'webp', 'gif', 'avif' ], true ) ) {
			continue;
		}
		$paths[] = $file->getPathname();
	}

	sort( $paths );
	return $paths;
}

function reci_demo_generate_attachment_metadata( int $attachment_id, string $file_path ) {
	$disable_intermediate_sizes = static function() {
		return [];
	};
	$disable_big_image_threshold = static function() {
		return false;
	};

	add_filter( 'intermediate_image_sizes_advanced', $disable_intermediate_sizes );
	add_filter( 'big_image_size_threshold', $disable_big_image_threshold );

	try {
		return wp_generate_attachment_metadata( $attachment_id, $file_path );
	} finally {
		remove_filter( 'intermediate_image_sizes_advanced', $disable_intermediate_sizes );
		remove_filter( 'big_image_size_threshold', $disable_big_image_threshold );
	}
}

function reci_demo_delete_directory( string $directory ): void {
	if ( ! is_dir( $directory ) ) {
		return;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);

	foreach ( $iterator as $item ) {
		if ( $item->isDir() ) {
			@rmdir( $item->getPathname() );
		} else {
			@unlink( $item->getPathname() );
		}
	}

	@rmdir( $directory );
}

/**
 * Return a bundled demo image URL when the file exists.
 */
function reci_demo_theme_image_url( string $filename ): string {
	$relative_path = ltrim( $filename, '/' );
	$file_path     = get_template_directory() . '/demo-content/images/' . $relative_path;

	if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
		return '';
	}

	return get_template_directory_uri() . '/demo-content/images/' . $relative_path;
}

/**
 * Generate placeholder body copy.
 */
function reci_demo_lorem( int $paragraphs = 2 ): string {
	$p   = 'Racial equity work requires sustained commitment, community partnership, and an unflinching willingness to examine systems — not just individual behaviors. Over decades of research, scholars and practitioners have demonstrated that disparities in education, health, housing, and economic opportunity are not the result of individual failings, but of policies and structures designed to produce unequal outcomes. Understanding this is the first step toward changing it.';
	$p2  = 'The path forward demands both urgency and patience. Urgency because lives and livelihoods hang in the balance every day that inequitable systems remain in place. Patience because lasting systemic change is never quick, and meaningful progress requires building trust across communities, institutions, and generations. RECI is committed to both, bridging rigorous scholarship with community-driven action.';
	$p3  = 'Communities most impacted by racial inequity are not simply problems to be solved — they are sources of expertise, resilience, and vision. Centering the voices and leadership of those who have navigated unjust systems firsthand is not charity; it is a prerequisite for effective and lasting change. When solutions emerge from within communities, they are more likely to endure.';
	$p4  = 'Data matters, but data without context can mislead. Numbers alone cannot capture the lived experience of systemic exclusion. Effective equity work weaves together quantitative research, qualitative stories, and community knowledge to create a fuller picture of both the problem and the possibility. RECI\'s approach integrates all three.';

	$all = [ $p, $p2, $p3, $p4 ];
	return implode( "\n\n", array_slice( $all, 0, min( $paragraphs, 4 ) ) );
}

function reci_demo_import_page_html(): void {
	$installed = get_option( 'reci_demo_installed', false );
	$count     = count( get_option( 'reci_demo_slugs', [] ) );
	$job       = function_exists( 'reci_demo_get_job' ) ? reci_demo_get_job() : [];
	$job_state = function_exists( 'reci_demo_present_job_state' ) ? reci_demo_present_job_state( $job ) : [];

	$all_types = reci_demo_content_types();

	$tabs = [
		'media'       => [ 'label' => 'Media',       'keys' => [ 'reci_demo_images_reflections', 'reci_demo_images_articles', 'reci_demo_images_events', 'reci_demo_images_courses', 'reci_demo_images_podcasts', 'reci_demo_images_videos', 'reci_demo_images_quizzes', 'reci_demo_images_partners', 'reci_demo_images_misc' ] ],
		'taxonomies'  => [ 'label' => 'Taxonomies',  'keys' => [ 'reci_demo_taxonomies' ] ],
		'content'     => [ 'label' => 'Content',     'keys' => [ 'post', 'reci_podcast', 'reci_video', 'reci_event', 'reci_reflection', 'reci_quote', 'reci_assessment', 'reci_course', 'reci_glossary_term' ] ],
		'pages'       => [ 'label' => 'Pages',       'keys' => [ 'reci_team', 'reci_testimonial', 'reci_author', 'reci_partner', 'reci_page' ] ],
	];

	// Ensure Alpine.js is loaded
	wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js', [], null, true );
	?>
	<div class="wrap" x-data="{ activeTab: 'media' }">
		<h1>Demo Content Importer</h1>

		<?php if ( $installed ) : ?>
			<div class="notice notice-info">
				<p>
					<span style="color:#007017;font-weight:600;">&#10003; Demo content is installed</span>
					— <?php echo (int) $count; ?> demo posts across all content types.
				</p>
			</div>
			<div style="background:#fff; padding: 20px; border:1px solid #ccd0d4; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
				<div>
					<h3 style="margin-top:0;">Reset Demo Content</h3>
					<p style="margin-bottom:0;">This will permanently delete all demo posts. This cannot be undone.</p>
				</div>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'reci_demo_action' ); ?>
					<input type="hidden" name="action" value="reci_reset_demo" />
					<button type="submit" class="button button-secondary" onclick="return confirm('Remove all demo content? This cannot be undone.');">Reset Demo Content</button>
				</form>
			</div>
		<?php else : ?>
			<p>Select the content types to import below.</p>
		<?php endif; ?>

		<h2 class="nav-tab-wrapper" style="margin-bottom: 16px;">
			<template x-for="(tabData, tabKey) in <?php echo esc_attr( wp_json_encode( $tabs ) ); ?>" :key="tabKey">
				<a href="#" class="nav-tab" :class="activeTab === tabKey ? 'nav-tab-active' : ''" @click.prevent="activeTab = tabKey" x-text="tabData.label"></a>
			</template>
		</h2>

		<form id="reci-demo-import-form">
			<div style="background: #fff; border: 1px solid #ccd0d4; padding: 0; margin-bottom: 24px;">
				<template x-for="(tabData, tabKey) in <?php echo esc_attr( wp_json_encode( $tabs ) ); ?>" :key="tabKey">
					<div x-show="activeTab === tabKey" style="padding: 20px;">
						<table class="widefat striped">
							<thead>
								<tr>
									<th style="width:40px;"><input type="checkbox" @change="$event.target.closest('table').querySelectorAll('tbody tr').forEach(tr => { if (tr.style.display !== 'none') { const cb = tr.querySelector('input[type=checkbox]'); if (cb) cb.checked = $event.target.checked; } })" /></th>
									<th>Content Type</th>
									<th>Items</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $all_types as $pt => $info ) : ?>
									<tr x-show="tabData.keys.includes('<?php echo esc_js( $pt ); ?>')">
										<td><input type="checkbox" name="reci_demo_types[]" value="<?php echo esc_attr( $pt ); ?>" /></td>
										<td><?php echo esc_html( $info['label'] ); ?></td>
										<td><?php echo (int) $info['count']; ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</template>
				<div style="padding: 16px 20px; background: #f9f9f9; border-top: 1px solid #ccd0d4;">
					<p style="color:#856404; background:#fff3cd; border-left:4px solid #ffc107; padding:8px 12px; margin-top:0;">
						<strong>Note:</strong> We recommend importing <strong>Media</strong> before Content and Pages so the images can be attached.
					</p>
					<button type="submit" class="button button-primary button-hero" id="reci-demo-start-import">Install Selected Content</button>
				</div>
			</div>
		</form>

		<div id="reci-demo-progress" style="margin-top:20px; border:1px solid #dcdcde; border-radius:6px; padding:16px; background:#fff; <?php echo empty( $job_state ) ? 'display:none;' : ''; ?>">
			<h3 style="margin-top:0;">Import Progress</h3>
			<p><strong id="reci-demo-progress-label"><?php echo esc_html( $job_state['current_label'] ?? 'Idle' ); ?></strong></p>
			<progress id="reci-demo-progress-bar" value="<?php echo esc_attr( (string) ( $job_state['percent'] ?? 0 ) ); ?>" max="100" style="width:100%;height:18px;"></progress>
			<p id="reci-demo-progress-meta" style="margin:8px 0 16px;"><?php echo esc_html( $job_state['progress_text'] ?? '' ); ?></p>
			<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
				<div>
					<h4 style="margin:0 0 8px;">Activity</h4>
					<ul id="reci-demo-activity-log" style="max-height:220px; overflow:auto; margin:0; padding-left:18px;"></ul>
				</div>
				<div>
					<h4 style="margin:0 0 8px;">Results</h4>
					<div id="reci-demo-completed"></div>
					<div id="reci-demo-failed" style="margin-top:12px;"></div>
					<div id="reci-demo-skipped" style="margin-top:12px;"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
	document.addEventListener('alpine:init', () => {
		const form = document.getElementById('reci-demo-import-form');
		if (!form) return;

		const startBtn = document.getElementById('reci-demo-start-import');
		const progressWrap = document.getElementById('reci-demo-progress');
		const label = document.getElementById('reci-demo-progress-label');
		const bar = document.getElementById('reci-demo-progress-bar');
		const meta = document.getElementById('reci-demo-progress-meta');
		const activity = document.getElementById('reci-demo-activity-log');
		const completed = document.getElementById('reci-demo-completed');
		const failed = document.getElementById('reci-demo-failed');
		const skipped = document.getElementById('reci-demo-skipped');

		const config = <?php echo wp_json_encode([
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'reci_demo_action' ),
			'initialState' => $job_state,
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>;

		function escapeHtml(value) {
			return String(value ?? '').replace(/[&<>"']/g, function(ch) {
				return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[ch];
			});
		}

		function renderList(target, title, items, tone) {
			if (!target) return;
			if (!Array.isArray(items) || items.length === 0) {
				target.innerHTML = '';
				return;
			}
			const color = tone === 'failed' ? '#b32d2e' : (tone === 'skipped' ? '#996800' : '#007017');
			target.innerHTML = '<strong style="color:' + color + ';">' + escapeHtml(title) + '</strong><ul style="margin:6px 0 0;padding-left:18px;">' + items.map(function(item){
				return '<li><strong>' + escapeHtml(item.label || item.path || item.slug || 'Item') + '</strong>: ' + escapeHtml(item.message || '') + '</li>';
			}).join('') + '</ul>';
		}

		function renderState(state) {
			if (!state || typeof state !== 'object') return;
			progressWrap.style.display = 'block';
			label.textContent = state.current_label || 'Idle';
			bar.value = Number(state.percent || 0);
			meta.textContent = state.progress_text || '';
			activity.innerHTML = (state.activity || []).map(function(entry){
				return '<li>' + escapeHtml(entry) + '</li>';
			}).join('');
			renderList(completed, 'Completed', state.completed || [], 'completed');
			renderList(failed, 'Failed', state.failed || [], 'failed');
			renderList(skipped, 'Skipped', state.skipped || [], 'skipped');
			if (startBtn) {
				startBtn.disabled = !!state.running;
				startBtn.textContent = state.running ? 'Import Running…' : 'Install Selected Content';
			}
		}

		async function postAction(action, extra) {
			const body = new URLSearchParams();
			body.set('action', action);
			body.set('nonce', config.nonce);
			Object.entries(extra || {}).forEach(function(entry) {
				const key = entry[0];
				const value = entry[1];
				if (Array.isArray(value)) {
					value.forEach(function(item) { body.append(key + '[]', item); });
				} else {
					body.set(key, value);
				}
			});

			const response = await fetch(config.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
				credentials: 'same-origin'
			});
			return await response.json();
		}

		async function runJob() {
			while (true) {
				const payload = await postAction('reci_demo_process_step');
				if (!payload || !payload.success) {
					throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'Import step failed.');
				}
				renderState(payload.data);
				if (payload.data.finished) break;
			}
		}

		form.addEventListener('submit', async function(event) {
			event.preventDefault();
			const selected = Array.from(form.querySelectorAll('[name="reci_demo_types[]"]:checked')).map(function(input){ return input.value; });
			if (!selected.length) {
				window.alert('Select at least one import group.');
				return;
			}

			startBtn.disabled = true;
			try {
				const payload = await postAction('reci_demo_start_import', { selected: selected });
				if (!payload || !payload.success) {
					throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'Could not start import.');
				}
				renderState(payload.data);
				await runJob();
			} catch (error) {
				progressWrap.style.display = 'block';
				label.textContent = 'Import Error';
				meta.textContent = error.message || 'Unknown error.';
			} finally {
				startBtn.disabled = false;
			}
		});

		if (config.initialState && config.initialState.running) {
			renderState(config.initialState);
			runJob().catch(function(error){
				label.textContent = 'Import Error';
				meta.textContent = error.message || 'Unknown error.';
				if (startBtn) {
					startBtn.disabled = false;
					startBtn.textContent = 'Start New Import';
				}
			});
		} else if (config.initialState && Object.keys(config.initialState).length) {
			renderState(config.initialState);
		}
	});
	</script>
	<?php
}
