<?php
/**
 * Front page template.
 *
 * Uses exported Figma markup directly to preserve fidelity.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$figma_front_page = get_template_directory() . '/figma/herov3.php';

if (file_exists($figma_front_page)) {
	include $figma_front_page;
} else {
	echo '<main class="reci-wrap"><p>Missing file: figma/herov3.php</p></main>';
}

get_footer();

