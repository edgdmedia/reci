<?php
/**
 * Native archive route for reci_event.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-event-archive.php');
if ($template) {
	require $template;
}
