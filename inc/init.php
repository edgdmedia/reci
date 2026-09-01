<?php

/**
 * Theme initialization and module loading.
 */

if (! defined('ABSPATH')) {
	exit;
}

$reci_media_hub_includes = [
	'/inc/core/theme-setup.php',
	'/inc/core/theme-activation.php',
	'/inc/core/template-routing.php',
	'/inc/core/database.php',
	
	'/inc/content/content-types.php',
	'/inc/content/taxonomies.php',
	'/inc/content/meta-fields.php',
	'/inc/content/patterns.php',
	
	'/inc/features/assessments.php',
	'/inc/features/submissions.php',
	'/inc/features/reflections.php',
	'/inc/features/reflection-design.php',
	'/inc/features/reflection-gallery-templates.php',
	'/inc/features/reflection-responses.php',
	'/inc/features/sphere-helpers.php',
	'/inc/features/author-profiles.php',
	'/inc/features/collaborators.php',
	'/inc/features/emails.php',
	'/inc/features/auth.php',
	'/inc/features/notifications.php',
	'/inc/features/live-search.php',
	'/inc/features/listing-builder.php',
	'/inc/features/remote-demo-content.php',
	'/inc/features/theme-updates.php',
	
	'/inc/admin/theme-settings.php',
	'/inc/admin/dashboard.php',
	'/inc/admin/demo-content.php',
	'/inc/admin/collaborator-import.php',
	'/inc/admin/theme-setup-wizard.php',
	'/inc/admin/theme-setup-client.php',
	'/inc/admin/class-reci-journals-list-table.php',
	'/inc/admin/class-reci-assessments-list-table.php',
	
	'/inc/utils/svg-helper.php',
	'/inc/utils/temp-fix-vor-props.php',

	'/inc/services/class-post-format-service.php',
	'/inc/services/class-post-query-service.php',
	'/inc/services/class-content-feed.php',
	'/inc/services/class-related-posts-service.php',
	'/inc/services/class-single-post-service.php',
	
	'/modules/reflection-system/inc/reflection-system-registry.php',
	'/modules/reflection-system/inc/class-reflection-content-service.php',
	'/modules/reflection-system/inc/class-reflection-experience-service.php',
	'/modules/reflection-system/inc/class-reflection-render-service.php',
	'/modules/reflection-system/inc/class-reflection-system-render-service.php',
	'/modules/reflection-system/inc/builder/class-reflection-builder.php',
	'/modules/reflection-system/inc/builder/class-reflection-preview.php',
	'/modules/reflection-system/inc/builder/class-reflection-render-chapter.php',
];

foreach ($reci_media_hub_includes as $include_path) {
	$absolute_path = get_template_directory() . $include_path;
	if (file_exists($absolute_path)) {
		require_once $absolute_path;
	}
}
