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
		 * @param mixed $value
		 * @return array<int,string>
		 */
		private static function normalize_text_repeater($value): array {
			$items = is_array($value) ? $value : [];
			$normalized = [];
			foreach ($items as $item) {
				if (is_string($item) && $item !== '') {
					$normalized[] = $item;
					continue;
				}
				if (is_array($item)) {
					$text = trim((string) ($item['text'] ?? ''));
					if ($text !== '') {
						$normalized[] = $text;
					}
				}
			}
			return $normalized;
		}

		/**
		 * @param mixed $value
		 * @return array<int,array<string,string>>
		 */
		private static function normalize_link_repeater($value): array {
			$items = is_array($value) ? $value : [];
			$normalized = [];
			foreach ($items as $item) {
				if (! is_array($item)) {
					continue;
				}
				$label = trim((string) ($item['label'] ?? ''));
				$href = trim((string) ($item['href'] ?? ''));
				if ($label === '' && $href === '') {
					continue;
				}
				$normalized[] = [
					'label' => $label,
					'href' => $href,
				];
			}
			return $normalized;
		}

		private static function is_dark_color(string $hex): bool {
			$hex = ltrim($hex, '#');
			if (strlen($hex) === 3) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}
			if (strlen($hex) !== 6) {
				return true;
			}
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
			$luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
			return $luminance < 0.5;
		}

		/**
		 * @param array<string,mixed> $props
		 * @return array<string,mixed>
		 */
		private static function normalize_component_props(string $family, array $props): array {
			if ($family === 'hero') {
				$section_class = trim((string) ($props['section_class'] ?? ''));
				if ($section_class === '') {
					$props['section_class'] = 'reci-stage';
				} elseif (strpos($section_class, 'reci-stage') === false) {
					$props['section_class'] = trim($section_class . ' reci-stage');
				}

				$props['overlay_opacity'] = min(1, max(0, (int) ($props['overlay_intensity'] ?? 72) / 100));
				$bg_type = trim((string) ($props['background_type'] ?? 'image'));
				$props['bg_type'] = $bg_type;

				$align_h = trim((string) ($props['align_horizontal'] ?? 'left'));
				$align_v = trim((string) ($props['align_vertical'] ?? 'center'));
				$props['align_h_class'] = match ($align_h) { 'center' => 'items-center', 'right' => 'items-end', default => 'items-start' };
				$props['align_v_class'] = match ($align_v) { 'center' => 'justify-center', 'bottom' => 'justify-end', default => 'justify-start' };
				$props['align_text_class'] = match ($align_h) { 'center' => 'text-center', 'right' => 'text-right', default => 'text-left' };

				$heading_color = trim((string) ($props['heading_color'] ?? ''));
				$body_color = trim((string) ($props['body_color'] ?? ''));

				$section_attrs = is_array($props['section_attributes'] ?? null) ? $props['section_attributes'] : [];
				$existing_style = (string) ($section_attrs['style'] ?? '');
				$new_style = '';

				if ($bg_type === 'color') {
					$bg_color = trim((string) ($props['background_color'] ?? ''));
					if ($bg_color !== '') {
						$is_dark = self::is_dark_color($bg_color);
						$auto_text = $is_dark ? '#ffffff' : '#111111';
						$auto_soft = $is_dark ? '#e5e5e5' : '#444444';
						$auto_muted = $is_dark ? '#a0a0a0' : '#888888';

						$final_heading = $heading_color ?: $auto_text;
						$final_body = $body_color ?: $auto_soft;
						$final_muted = $body_color ?: $auto_muted;

						$new_style = '--reflection-text:' . $final_heading . ';--reflection-soft-text:' . $final_body . ';--reflection-muted:' . $final_muted . ';--reflection-bg:' . $bg_color;
					}
				} else {
					$final_heading = $heading_color ?: '#ffffff';
					$final_body = $body_color ?: '#e5e5e5';
					$final_muted = $body_color ?: '#a0a0a0';

					$new_style = '--reflection-text:' . $final_heading . ';--reflection-soft-text:' . $final_body . ';--reflection-muted:' . $final_muted;
				}

				if ($new_style !== '') {
					$section_attrs['style'] = $existing_style !== '' ? $existing_style . ';' . $new_style : $new_style;
					$props['section_attributes'] = $section_attrs;
				}
			}

			if ($family === 'documentary-dossier') {
				$props['intro'] = self::normalize_text_repeater($props['intro'] ?? []);
				$sections = is_array($props['sections'] ?? null) ? $props['sections'] : [];
				$props['sections'] = array_map(
					static function ($section): array {
						$section = is_array($section) ? $section : [];
						$section['paragraphs'] = self::normalize_text_repeater($section['paragraphs'] ?? []);
						$section['links'] = self::normalize_link_repeater($section['links'] ?? []);
						return $section;
					},
					$sections
				);
			}

			if ($family === 'timeline-world') {
				$items = is_array($props['items'] ?? null) ? $props['items'] : [];
				$props['items'] = array_map(
					static function ($item): array {
						$item = is_array($item) ? $item : [];
						if (isset($item['link']) && is_array($item['link'])) {
							$links = self::normalize_link_repeater($item['link']);
							$item['link'] = $links[0] ?? [];
						}
						return $item;
					},
					$items
				);
			}

			return $props;
		}

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
			$props = self::normalize_component_props($family, $props);

			// Attach data-chapter-id for preview DOM targeting.
			$cid = (string) ($component['id'] ?? '');
			if ($cid !== '') {
				$section_attrs = is_array($props['section_attributes'] ?? null) ? $props['section_attributes'] : [];
				$section_attrs['data-chapter-id'] = $cid;
				$props['section_attributes'] = $section_attrs;
			}

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
				$include = ! empty($props['include_in_menu']);
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
