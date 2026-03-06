<?php
/**
 * Template Name: Figma Hero V3 (Sample)
 * Description: Renders the raw Figma sample export from figma/herov3.php.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$sample_template = get_template_directory() . '/figma/herov3.php';

if (file_exists($sample_template)) {
	include $sample_template;
} else {
	echo '<main class="reci-wrap"><p>Missing file: figma/herov3.php</p></main>';
}

get_footer();
