<?php
/**
 * Front page template — delegates to the Homepage page template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-homepage.php');
if ($template) {
	require $template;
}
