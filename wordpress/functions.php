<?php

/**
 * Theme bootstrap for Reci Media Hub.
 */

if (! defined('ABSPATH')) {
	exit;
}

$reci_media_hub_includes = [
	'/inc/theme-setup.php',
	'/inc/svg-helper.php',
	'/inc/reflections.php',
	'/inc/reflection-system-registry.php',
	'/inc/reflection-gallery-templates.php',
	'/inc/content-types.php',
	'/inc/author-profiles.php',
	'/inc/assessments.php',
	'/inc/taxonomies.php',
	'/inc/submissions.php',
	'/inc/meta-fields.php',
	'/inc/listing-builder.php',
	'/inc/live-search.php',
	'/inc/services/class-post-format-service.php',
	'/inc/services/class-post-query-service.php',
	'/inc/services/class-content-feed.php',
	'/inc/services/class-related-posts-service.php',
	'/inc/services/class-single-post-service.php',
	'/inc/services/class-reflection-content-service.php',
	'/inc/services/class-reflection-experience-service.php',
	'/inc/services/class-reflection-render-service.php',
	'/inc/services/class-reflection-system-render-service.php',
	'/inc/theme-settings.php',
	'/inc/demo-content.php',
	'/inc/auth.php',
	'/inc/reflection-responses.php',
	'/inc/builder/class-reflection-builder.php',
	'/inc/builder/class-reflection-preview.php',
	'/inc/builder/class-reflection-render-chapter.php',
	'/inc/theme-activation.php',
	'/inc/sphere-helpers.php',
];

foreach ($reci_media_hub_includes as $include_path) {
	require_once get_template_directory() . $include_path;
}
