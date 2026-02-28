<?php

/**
 * Theme bootstrap for Reci Media Hub.
 */

if (! defined('ABSPATH')) {
	exit;
}

$reci_media_hub_includes = [
	'/inc/theme-setup.php',
	'/inc/content-types.php',
	'/inc/taxonomies.php',
];

foreach ($reci_media_hub_includes as $include_path) {
	require_once get_template_directory() . $include_path;
}
