<?php
/**
 * Front page template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$home_body_template = get_template_directory() . '/figma/homepage-body.php';

if (file_exists($home_body_template)) {
	include $home_body_template;
} else {
	echo '<main class="reci-wrap"><p>Missing file: figma/homepage-body.php</p></main>';
}

get_footer();
