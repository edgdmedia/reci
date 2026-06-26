<?php
/**
 * Register custom pattern category for theme patterns.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_register_pattern_categories')) {
	function reci_media_hub_register_pattern_categories(): void
	{
		if (! function_exists('register_block_pattern_category')) {
			return;
		}

		register_block_pattern_category(
			'reci-media-hub',
			[
				'label' => __('Reci Media Hub', 'reci-media-hub'),
			]
		);
	}
}

add_action('init', 'reci_media_hub_register_pattern_categories');
