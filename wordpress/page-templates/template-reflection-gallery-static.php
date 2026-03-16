<?php
/**
 * Template Name: Reflection Gallery - Index
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

require_once get_template_directory() . '/inc/reflection-gallery-static.php';
reci_render_static_reflection_gallery_template('index.html');
