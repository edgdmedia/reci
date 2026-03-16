<?php
/**
 * Template Name: Reflection Gallery
 *
 * Deprecated compatibility template. Redirects to the reflection archive.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$archive_url = get_post_type_archive_link('reci_reflection') ?: home_url('/reflections/');
wp_safe_redirect($archive_url, 301);
exit;
