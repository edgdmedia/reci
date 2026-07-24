<?php

/**
 * Reflection appearance helpers.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_reflection_scene_option')) {
	function reci_reflection_scene_option(array $scene, string $key, string $default = ''): string
	{
		$value = isset($scene[$key]) ? (string) $scene[$key] : '';
		if ($value === '' || $value === 'inherit') {
			return $default;
		}
		return $value;
	}
}

if (! function_exists('reci_reflection_theme_map')) {
	function reci_reflection_theme_map(string $theme): array
	{
		$themes = [
			'immersive-dark' => [
				'section' => 'bg-neutral-950 text-white',
				'surface' => 'bg-white/10 border-white/15 text-white',
				'muted'   => 'text-zinc-300',
				'heading' => 'text-white',
				'accent'  => 'bg-amber-400 text-neutral-900',
				'dot'     => 'bg-amber-400',
			],
			'light' => [
				'section' => 'bg-white text-neutral-900',
				'surface' => 'bg-slate-50 border-stone-200 text-neutral-900',
				'muted'   => 'text-neutral-600',
				'heading' => 'text-neutral-900',
				'accent'  => 'bg-blue-900 text-white',
				'dot'     => 'bg-blue-900',
			],
			'archival' => [
				'section' => 'bg-stone-100 text-stone-900',
				'surface' => 'bg-white border-stone-300 text-stone-900',
				'muted'   => 'text-stone-600',
				'heading' => 'text-stone-900',
				'accent'  => 'bg-stone-900 text-stone-50',
				'dot'     => 'bg-stone-900',
			],
			'editorial' => [
				'section' => 'bg-slate-100 text-neutral-900',
				'surface' => 'bg-white border-slate-200 text-neutral-900',
				'muted'   => 'text-slate-600',
				'heading' => 'text-neutral-900',
				'accent'  => 'bg-reci-blue text-white',
				'dot'     => 'bg-reci-blue',
			],
			'spotlight' => [
				'section' => 'bg-neutral-900 text-white',
				'surface' => 'bg-neutral-800 border-neutral-700 text-white',
				'muted'   => 'text-zinc-300',
				'heading' => 'text-white',
				'accent'  => 'bg-white text-neutral-900',
				'dot'     => 'bg-white',
			],
		];

		return $themes[$theme] ?? $themes['immersive-dark'];
	}
}

if (! function_exists('reci_reflection_spacing_class')) {
	function reci_reflection_spacing_class(string $spacing): string
	{
		return match ($spacing) {
			'compact' => 'py-10 sm:py-12 lg:py-14',
			'spacious' => 'py-16 sm:py-20 lg:py-24',
			default => 'py-14 sm:py-16 lg:py-20',
		};
	}
}

if (! function_exists('reci_reflection_width_class')) {
	function reci_reflection_width_class(string $width): string
	{
		return match ($width) {
			'narrow' => 'max-w-3xl',
			'prose' => 'max-w-4xl',
			'wide' => 'max-w-6xl',
			'full' => 'max-w-none',
			default => '',
		};
	}
}

if (! function_exists('reci_reflection_columns_class')) {
	function reci_reflection_columns_class(string $columns, string $fallback = 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3'): string
	{
		return match ($columns) {
			'1' => 'grid-cols-1',
			'2' => 'grid-cols-1 md:grid-cols-2',
			'3' => 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3',
			'4' => 'grid-cols-1 md:grid-cols-2 xl:grid-cols-4',
			default => $fallback,
		};
	}
}

if (! function_exists('reci_reflection_layout_grid_class')) {
	function reci_reflection_layout_grid_class(string $layout, string $fallback = 'grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]'): string
	{
		return match ($layout) {
			'sidebar-left' => 'grid gap-8 lg:grid-cols-[360px_minmax(0,1fr)]',
			'split' => 'grid gap-8 lg:grid-cols-2',
			'centered', 'narrow', 'full' => 'grid gap-8',
			default => $fallback,
		};
	}
}

if (! function_exists('reci_reflection_surface_class')) {
	function reci_reflection_surface_class(array $scene): string
	{
		$theme = reci_reflection_scene_option($scene, 'theme', 'immersive-dark');
		$map = reci_reflection_theme_map($theme);
		return $map['surface'];
	}
}

if (! function_exists('reci_reflection_heading_class')) {
	function reci_reflection_heading_class(array $scene): string
	{
		$theme = reci_reflection_scene_option($scene, 'theme', 'immersive-dark');
		$map = reci_reflection_theme_map($theme);
		return $map['heading'];
	}
}

if (! function_exists('reci_reflection_muted_class')) {
	function reci_reflection_muted_class(array $scene): string
	{
		$theme = reci_reflection_scene_option($scene, 'theme', 'immersive-dark');
		$map = reci_reflection_theme_map($theme);
		return $map['muted'];
	}
}

if (! function_exists('reci_reflection_accent_class')) {
	function reci_reflection_accent_class(array $scene): string
	{
		$accent = reci_reflection_scene_option($scene, 'accent', 'amber');
		return match ($accent) {
			'blue' => 'bg-reci-blue text-white',
			'white' => 'bg-white text-neutral-900',
			'dark' => 'bg-neutral-900 text-white',
			default => 'bg-amber-400 text-neutral-900',
		};
	}
}

if (! function_exists('reci_reflection_dot_class')) {
	function reci_reflection_dot_class(array $scene): string
	{
		$accent = reci_reflection_scene_option($scene, 'accent', 'amber');
		return match ($accent) {
			'blue' => 'bg-reci-blue',
			'white' => 'bg-white',
			'dark' => 'bg-neutral-900',
			default => 'bg-amber-400',
		};
	}
}

if (! function_exists('reci_reflection_section_classes')) {
	function reci_reflection_section_classes(array $scene, string $base = 'reci-immersive-scene'): string
	{
		$theme = reci_reflection_scene_option($scene, 'theme', 'immersive-dark');
		$map = reci_reflection_theme_map($theme);
		return trim($base . ' ' . $map['section']);
	}
}

if (! function_exists('reci_reflection_inner_classes')) {
	function reci_reflection_inner_classes(array $scene, string $base = 'reci-scene-inner mx-auto px-4 sm:px-6 lg:px-12 xl:px-20'): string
	{
		$spacing = reci_reflection_scene_option($scene, 'spacing', 'comfortable');
		$width = reci_reflection_scene_option($scene, 'text_width', 'default');
		return trim($base . ' ' . reci_reflection_spacing_class($spacing) . ' ' . reci_reflection_width_class($width));
	}
}
