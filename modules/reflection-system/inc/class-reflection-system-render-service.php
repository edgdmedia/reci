<?php
/**
 * Reflection system render service.
 *
 * Renders family + variant + props using the new modules/reflection-system/templates system.
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
				$overlay_color = trim((string) ($props['overlay_color'] ?? '#000000'));
				if ($overlay_color === '') {
					$overlay_color = '#000000';
				}
				$props['overlay_color'] = $overlay_color;
				
				$hex = ltrim($overlay_color, '#');
				if (strlen($hex) === 3) {
					$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
				}
				if (strlen($hex) !== 6) {
					$props['overlay_rgb'] = '0,0,0';
				} else {
					$props['overlay_rgb'] = hexdec(substr($hex, 0, 2)) . ',' . hexdec(substr($hex, 2, 2)) . ',' . hexdec(substr($hex, 4, 2));
				}

				$bg_type = trim((string) ($props['background_type'] ?? 'image'));
				$props['bg_type'] = $bg_type;

				$align_h = trim((string) ($props['align_horizontal'] ?? 'center'));
				$align_v = trim((string) ($props['align_vertical'] ?? 'center'));
				$props['align_h_class'] = match ($align_h) { 'center' => 'items-center', 'right' => 'items-end', default => 'items-start' };
				$props['align_v_class'] = match ($align_v) { 'center' => 'justify-center', 'bottom' => 'justify-end', default => 'justify-start' };
				$props['align_text_class'] = match ($align_h) { 'center' => 'text-center', 'right' => 'text-right', default => 'text-left' };

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

			if ($family === 'progressive-text') {
				$props['paragraphs'] = self::normalize_text_repeater($props['paragraphs'] ?? []);
			}

			if (isset($props['actions']) && is_array($props['actions'])) {
				$props['actions'] = array_map(function ($action) {
					if (!is_array($action)) return $action;
					$href = (string) ($action['href'] ?? '');
					if ($href !== '' && !str_starts_with($href, 'http') && !str_starts_with($href, '/') && !str_starts_with($href, '#')) {
						$action['attributes'] = $action['attributes'] ?? [];
						$action['attributes']['data-stage-target'] = $href;
						$action['href'] = '#';
					}
					return $action;
				}, $props['actions']);
			}

			return $props;
		}

		/**
		 * @param array<string,mixed> $component
		 */
		public static function render_component(array $component, array $global_settings = []): void {
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
				
				// Handle local color overrides with fallback to global settings
				$is_override = !empty($props['override_colors']) || !empty($props['__overrideColors']);
				$style = '';
				
				$bg = $is_override ? ($props['color_bg'] ?? $props['__color_bg'] ?? '') : '';
				$bg = $bg ?: ($global_settings['color_bg'] ?? '');
				
				$heading = $is_override ? ($props['color_heading'] ?? $props['__color_heading'] ?? '') : '';
				$heading = $heading ?: ($global_settings['color_heading'] ?? '');
				
				$body = $is_override ? ($props['color_body'] ?? $props['__color_body'] ?? '') : '';
				$body = $body ?: ($global_settings['color_body'] ?? '');
				
				$primary = $is_override ? ($props['color_primary'] ?? $props['__color_primary'] ?? '') : '';
				$primary = $primary ?: ($global_settings['color_primary'] ?? '');
				
				$accent = $is_override ? ($props['color_accent'] ?? $props['__color_accent'] ?? '') : '';
				$accent = $accent ?: ($global_settings['color_accent'] ?? '');
				
				$surface = $is_override ? ($props['color_surface'] ?? $props['__color_surface'] ?? '') : '';
				$surface = $surface ?: ($global_settings['color_surface'] ?? '');
				
				$surface_text = $is_override ? ($props['color_surface_text'] ?? $props['__color_surface_text'] ?? '') : '';
				$surface_text = $surface_text ?: ($global_settings['color_surface_text'] ?? '');
				
				$muted = $is_override ? ($props['color_muted'] ?? $props['__color_muted'] ?? '') : '';
				$muted = $muted ?: ($global_settings['color_muted'] ?? '');
				$text = $is_override ? ($props['color_text'] ?? '') : '';
				$text = $text ?: ($global_settings['color_text'] ?? '');
				$soft_text = $is_override ? ($props['color_soft_text'] ?? '') : '';
				$soft_text = $soft_text ?: ($global_settings['color_soft_text'] ?? '');
				$card = $is_override ? ($props['color_card'] ?? '') : '';
				$card = $card ?: ($global_settings['color_card'] ?? '');
				$card_strong = $is_override ? ($props['color_card_strong'] ?? '') : '';
				$card_strong = $card_strong ?: ($global_settings['color_card_strong'] ?? '');
				$border = $is_override ? ($props['color_border'] ?? '') : '';
				$border = $border ?: ($global_settings['color_border'] ?? '');
				$border_soft = $is_override ? ($props['color_border_soft'] ?? '') : '';
				$border_soft = $border_soft ?: ($global_settings['color_border_soft'] ?? '');
				$hotspot_ring = $is_override ? ($props['color_hotspot_ring'] ?? '') : '';
				$hotspot_ring = $hotspot_ring ?: ($global_settings['color_hotspot_ring'] ?? '');

				if ($bg !== '') $style .= '--reflection-bg: ' . esc_attr($bg) . ';';
				if ($heading !== '') $style .= '--reflection-heading: ' . esc_attr($heading) . ';';
				if ($body !== '') $style .= '--reflection-body: ' . esc_attr($body) . ';';
				if ($primary !== '') $style .= '--reflection-primary: ' . esc_attr($primary) . ';';
				if ($accent !== '') $style .= '--reflection-accent: ' . esc_attr($accent) . ';';
				if ($surface !== '') $style .= '--reflection-surface: ' . esc_attr($surface) . ';';
				if ($surface_text !== '') $style .= '--reflection-surface-text: ' . esc_attr($surface_text) . ';';
				if ($muted !== '') $style .= '--reflection-muted: ' . esc_attr($muted) . ';';
				if ($text !== '') $style .= '--reflection-text: ' . esc_attr($text) . ';';
				if ($soft_text !== '') $style .= '--reflection-soft-text: ' . esc_attr($soft_text) . ';';
				if ($card !== '') $style .= '--reflection-card: ' . esc_attr($card) . ';';
				if ($card_strong !== '') $style .= '--reflection-card-strong: ' . esc_attr($card_strong) . ';';
				if ($border !== '') $style .= '--reflection-border: ' . esc_attr($border) . ';';
				if ($border_soft !== '') $style .= '--reflection-border-soft: ' . esc_attr($border_soft) . ';';
				if ($hotspot_ring !== '') $style .= '--reflection-hotspot-ring: ' . esc_attr($hotspot_ring) . ';';
				
				if ($style !== '') {
					$section_attrs['style'] = (isset($section_attrs['style']) ? $section_attrs['style'] . ' ' : '') . $style;
				}
				
				$tmode = (string) ($props['transition_mode'] ?? 'button');
				$section_attrs['data-transition-mode'] = $tmode;
				
				$ctarget = (string) ($props['continue_target'] ?? '');
				if ($ctarget === '' && !empty($props['actions']) && is_array($props['actions'])) {
				    $first_action_href = (string) ($props['actions'][0]['href'] ?? '');
				    if ($first_action_href !== '') {
				        $ctarget = ltrim($first_action_href, '#');
				    }
				}
				if ($ctarget !== '' && !str_starts_with($ctarget, 'stage-')) {
				    $ctarget = 'stage-' . $ctarget;
				    $props['continue_target'] = $ctarget;
				}
				if ($ctarget !== '') {
				    $section_attrs['data-continue-target'] = $ctarget;
				}
				
				$props['section_attributes'] = $section_attrs;
			}

			if (empty($props['id'])) {
				$props['id'] = 'stage-' . ($cid !== '' ? $cid : uniqid());
			}

			$variant = sanitize_key((string) ($component['variant'] ?? ''));
			if ($variant === '' || $variant === 'inherit') {
			    $variant = (string) ($global_settings['global_style'] ?? ($definition['default_variant'] ?? 'default'));
			}

			$args = array_merge(
				$props,
				[
					'variant' => $variant,
					'component_family' => $family,
					'component_kind' => (string) ($definition['kind'] ?? 'chapter'),
				]
			);

			$component_id = (string) ($component['id'] ?? '');
			
			ob_start();
			get_template_part($loader, null, $args);
			$html = (string) ob_get_clean();

			$attr_string = '';
			$section_attrs = is_array($props['section_attributes'] ?? null) ? $props['section_attributes'] : [];
			
			// Only inject if there are attributes to inject (or if preview mode needs chapter id)
			$is_preview_mode = ! empty($_GET['reci_preview']) || ! empty($GLOBALS['reci_reflection_builder_preview']);
			if ($is_preview_mode && $component_id !== '' && !isset($section_attrs['data-chapter-id'])) {
				$section_attrs['data-chapter-id'] = $component_id;
			}

			foreach ($section_attrs as $k => $v) {
				// Don't re-inject if the template itself manually output it (naive check to avoid duplication)
				if ($k === 'data-chapter-id' && strpos($html, 'data-chapter-id=') !== false) {
					continue;
				}
				if ($k === 'style' && strpos($html, 'style="--reflection-') !== false) {
					// We can't easily merge styles, but assume the template handled it if it output reflection vars
					continue;
				}
				$attr_string .= ' ' . esc_attr($k) . '="' . esc_attr((string) $v) . '"';
			}

			if ($attr_string !== '') {
				$patched = preg_replace('/<([a-zA-Z][a-zA-Z0-9:-]*)([^>]*)>/', '<$1$2' . $attr_string . '>', $html, 1);
				echo is_string($patched) ? $patched : $html;
			} else {
				echo $html;
			}
		}

		/**
		 * @param array<int,array<string,mixed>> $components
		 * @param array<string,mixed> $global_settings
		 */
		public static function render_components(array $components, array $global_settings = []): void {
			foreach ($components as $component) {
				if (! is_array($component)) {
					continue;
				}
				self::render_component($component, $global_settings);
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
				$title = is_array($props['menu_label'] ?? null) ? '' : (string) ($props['menu_label'] ?? $props['title'] ?? $props['eyebrow'] ?? $id);
				
				$raw_desc = $props['menu_description'] ?? '';
				$description = '';
				if (is_array($raw_desc)) {
					// Grab the first text item if it's an array of paragraphs
					$description = (string) ($raw_desc[0]['text'] ?? $raw_desc[0] ?? '');
				} else {
					$description = (string) $raw_desc;
				}
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
			get_template_part('modules/reflection-system/templates/menu-overlay', null, [
				'variant' => 'exhibit',
				'back_url' => (string) ($settings['nav_back_url'] ?? $settings['menu_back_url'] ?? home_url('/reflections/')),
				'back_label' => (string) ($settings['nav_back_label'] ?? 'Back to Gallery'),
				'items' => $items,
			]);
		}

		/**
		 * @param array<string,mixed> $blueprint
		 */
		public static function render_blueprint(array $blueprint): void {
			$blueprint = reci_reflection_system_normalize_blueprint($blueprint);
			self::render_menu_overlay($blueprint);
			
			$global_settings = $blueprint['settings'] ?? [];
			
			$audio_url = (string) ($global_settings['nav_audio_url'] ?? '');
			if ($audio_url !== '') {
				get_template_part('modules/reflection-system/templates/audio-player', null, [
					'audio_url' => $audio_url,
					'audio_label' => (string) ($global_settings['nav_audio_label'] ?? 'Full Analysis'),
				]);
			}
			
			self::render_components(
				array_values(array_filter(
					is_array($blueprint['chapters'] ?? null) ? $blueprint['chapters'] : [],
					'is_array'
				)),
				$global_settings
			);
			
			get_template_part('modules/reflection-system/templates/annotated-lightbox');
		}
	}
}
