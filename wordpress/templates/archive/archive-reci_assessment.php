<?php
/**
 * Native archive route for reci_assessment.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-assessment-archive.php');
if ($template) {
	require $template;
}
