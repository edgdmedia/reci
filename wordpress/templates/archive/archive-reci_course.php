<?php
/**
 * Native archive route for reci_course.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-course-archive.php');
if ($template) {
	require $template;
}
