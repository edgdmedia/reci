<?php
/**
 * Native archive route for reci_quote.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-quote-archive.php');
if ($template) {
	require $template;
}
