<?php
/**
 * Native archive route for reci_video.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-video-archive.php');
if ($template) {
	require $template;
}
