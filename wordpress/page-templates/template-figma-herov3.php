<?php
/**
 * Template Name: Figma - Hero V3
 * Template Post Type: page
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$figma_template = get_template_directory() . '/figma/herov3.php';

if (file_exists($figma_template)) {
	include $figma_template;
} else {
	echo '<main class="reci-wrap"><p>Missing file: figma/herov3.php</p></main>';
}

get_footer();

