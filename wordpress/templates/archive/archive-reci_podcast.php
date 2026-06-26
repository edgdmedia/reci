<?php
/**
 * Native archive route for reci_podcast.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-podcast-archive.php');
if ($template) {
	require $template;
}
