<?php
/**
 * Reflection hero variant: analytical (retired).
 *
 * Split into analytical-light and analytical-dark. Blueprints saved before
 * that split still name this variant, so it aliases to the light card rather
 * than falling through to the documentary hero.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

reci_reflection_render_variant('heroes', $args ?? [], 'analytical-light');
