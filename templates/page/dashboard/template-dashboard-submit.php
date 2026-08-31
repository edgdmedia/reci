<?php
/**
 * Template Name: Dashboard — Submit Content
 *
 * Mounts the RECMH multi-step React submission experience inside the dashboard layout.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_safe_redirect( home_url( '/submit/' ) );
exit;
