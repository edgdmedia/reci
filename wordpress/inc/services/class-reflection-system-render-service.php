<?php
/**
 * Reflection system render service.
 *
 * Renders family + variant + props using the new template-parts/reflections system.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('RECI_Reflection_System_Render_Service')) {
	class RECI_Reflection_System_Render_Service {
		/**
		 * @param array<string,mixed> $component
		 */
		public static function render_component(array $component): void {
			$family = sanitize_key((string) ($component['family'] ?? $component['type'] ?? ''));
			if ($family === '') {
				return;
			}

			$definition = reci_reflection_system_component_definition($family);
			if (! is_array($definition)) {
				return;
			}

			$loader = (string) ($definition['loader'] ?? '');
			if ($loader === '') {
				return;
			}

			$props = is_array($component['props'] ?? null) ? $component['props'] : [];
			$variant = sanitize_key((string) ($component['variant'] ?? ($definition['default_variant'] ?? 'default')));

			$args = array_merge(
				$props,
				[
					'variant' => $variant,
					'component_family' => $family,
					'component_kind' => (string) ($definition['kind'] ?? 'chapter'),
				]
			);

			get_template_part($loader, null, $args);
		}

		/**
		 * @param array<int,array<string,mixed>> $components
		 */
		public static function render_components(array $components): void {
			foreach ($components as $component) {
				if (! is_array($component)) {
					continue;
				}
				self::render_component($component);
			}
		}

		/**
		 * @param array<string,mixed> $blueprint
		 * @return array<int,array<string,string>>
		 */
		private static function build_menu_items(array $blueprint): array {
			$items = [];
			$chapters = is_array($blueprint['chapters'] ?? null) ? $blueprint['chapters'] : [];
			foreach ($chapters as $chapter) {
				if (! is_array($chapter)) {
					continue;
				}
				$props = is_array($chapter['props'] ?? null) ? $chapter['props'] : [];
				$include = $props['include_in_menu'] ?? false;
				if (! $include) {
					continue;
				}
				$id = (string) ($props['id'] ?? $chapter['id'] ?? '');
				if ($id === '') {
					continue;
				}
				$title = (string) ($props['menu_label'] ?? $props['title'] ?? $props['eyebrow'] ?? $id);
				$description = (string) ($props['menu_description'] ?? $props['intro'] ?? $props['description'] ?? '');
				$items[] = [
					'title' => $title,
					'description' => $description,
					'href' => '#' . sanitize_title($id),
					'attributes' => ['data-stage-target' => sanitize_title($id)],
				];
			}
			return $items;
		}

		/**
		 * @param array<string,mixed> $blueprint
		 */
		private static function render_menu_overlay(array $blueprint): void {
			$settings = is_array($blueprint['settings'] ?? null) ? $blueprint['settings'] : [];
			if (empty($settings['menu_enabled'])) {
				return;
			}
			$items = self::build_menu_items($blueprint);
			if (! $items) {
				return;
			}
			get_template_part('template-parts/reflections/menu-overlay', null, [
				'variant' => 'exhibit',
				'back_url' => (string) ($settings['menu_back_url'] ?? home_url('/reflections/')),
				'items' => $items,
			]);
		}

		/**
		 * @param array<string,mixed> $blueprint
		 */
		public static function render_blueprint(array $blueprint): void {
			$blueprint = reci_reflection_system_normalize_blueprint($blueprint);
			self::render_menu_overlay($blueprint);
			self::render_components(
				array_values(array_filter(
					is_array($blueprint['chapters'] ?? null) ? $blueprint['chapters'] : [],
					'is_array'
				))
			);
		}
	}
}
