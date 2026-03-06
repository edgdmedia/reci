<?php
/**
 * Native archive route for reci_article.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$template = locate_template('page-templates/template-article-archive.php');
if ($template) {
	require $template;
}
