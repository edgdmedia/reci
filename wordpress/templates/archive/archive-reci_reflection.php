<?php
/**
 * Native archive route for reci_reflection.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-reflection-archive.php');
if ($template) {
	require $template;
}
