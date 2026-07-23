<?php
/**
 * Bare header for immersive reflection templates.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		:root {
			--reflection-bg: #141311;
			--reflection-surface: #201d18;
			--reflection-surface-alt: #120f0d;
			--reflection-panel: #171512;
			--reflection-card: rgba(255,255,255,0.04);
			--reflection-card-strong: rgba(255,255,255,0.06);
			--reflection-white: #ffffff;
			--reflection-text: #ffffff;
			--reflection-muted: #d5c8b0;
			--reflection-soft-text: #f7f1e6;
			--reflection-accent: #d4a63f;
			--reflection-accent-contrast: #1a1713;
			--reflection-border: rgba(255,255,255,0.08);
			--reflection-border-soft: rgba(255,255,255,0.05);
			--reflection-overlay: rgba(9,8,7,0.94);
			--reflection-hotspot-ring: rgba(212,166,63,0.14);
		}
	</style>
	<?php wp_head(); ?>
</head>
<body <?php body_class('font-[Inter]'); ?> style="background: var(--reflection-bg); color: var(--reflection-text);">
<?php wp_body_open(); ?>
