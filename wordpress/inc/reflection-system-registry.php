<?php
/**
 * Reflection system registry.
 *
 * Source of truth for the new family/variant based reflection system.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_reflection_system_registry')) {
	/**
	 * @param array<string,array<string,mixed>> $fields
	 * @return array<string,array<string,mixed>>
	 */
	function reci_reflection_system_with_menu_fields(array $fields): array {
		return array_merge(
			[
				'include_in_menu' => ['type' => 'select', 'label' => 'Include in menu', 'options' => ['0' => 'No', '1' => 'Yes']],
				'menu_label' => ['type' => 'text', 'label' => 'Menu label'],
				'menu_description' => ['type' => 'textarea', 'label' => 'Menu description'],
			],
			$fields
		);
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	function reci_reflection_system_registry(): array {
		return [
			'hero' => [
				'label' => 'Hero',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/hero',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
					'narrative' => 'Narrative',
					'testimonial' => 'Testimonial',
					'analytical' => 'Analytical',
					'voices-of-resistance' => 'Voices of Resistance',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'subtitle' => ['type' => 'text', 'label' => 'Subtitle'],
					'body' => ['type' => 'textarea', 'label' => 'Body'],
					'caption' => ['type' => 'textarea', 'label' => 'Caption'],
					'background_image' => ['type' => 'media', 'label' => 'Background image'],
					'actions' => ['type' => 'repeater', 'label' => 'Actions', 'itemFields' => ['label' => ['type' => 'text', 'label' => 'Label'], 'href' => ['type' => 'text', 'label' => 'Target'], 'class' => ['type' => 'text', 'label' => 'Classes']]],
				]),
			],
			'feature-split' => [
				'label' => 'Feature Split',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-feature-split',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'body' => ['type' => 'textarea', 'label' => 'Body', 'required' => true],
					'note' => ['type' => 'textarea', 'label' => 'Note'],
					'caption' => ['type' => 'textarea', 'label' => 'Caption'],
					'image' => ['type' => 'media', 'label' => 'Image', 'required' => true],
					'image_alt' => ['type' => 'text', 'label' => 'Image alt'],
					'media_side' => ['type' => 'select', 'label' => 'Image side', 'options' => ['left' => 'Left', 'right' => 'Right']],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'documentary-dossier' => [
				'label' => 'Documentary Dossier',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-documentary-dossier',
				'default_variant' => 'archival',
				'variants' => [
					'archival' => 'Archival',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'repeater', 'label' => 'Intro paragraphs', 'itemFields' => ['text' => ['type' => 'textarea', 'label' => 'Paragraph']]],
					'sections' => ['type' => 'repeater', 'label' => 'Sections', 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'paragraphs' => ['type' => 'repeater', 'label' => 'Paragraphs', 'itemFields' => ['text' => ['type' => 'textarea', 'label' => 'Paragraph']]], 'links' => ['type' => 'repeater', 'label' => 'Links', 'itemFields' => ['label' => ['type' => 'text', 'label' => 'Label'], 'href' => ['type' => 'text', 'label' => 'URL']]]]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'timeline-world' => [
				'label' => 'Timeline World',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-timeline-world',
				'default_variant' => 'horizontal',
				'variants' => [
					'horizontal' => 'Horizontal',
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'items' => ['type' => 'repeater', 'label' => 'Timeline items', 'required' => true, 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body'], 'media' => ['type' => 'repeater', 'label' => 'Media', 'itemFields' => ['src' => ['type' => 'media', 'label' => 'Image'], 'alt' => ['type' => 'text', 'label' => 'Alt text'], 'caption' => ['type' => 'textarea', 'label' => 'Caption']]], 'link' => ['type' => 'repeater', 'label' => 'Link', 'itemFields' => ['label' => ['type' => 'text', 'label' => 'Label'], 'href' => ['type' => 'text', 'label' => 'URL']]]]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'panel-explorer' => [
				'label' => 'Panel Explorer',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-panel-explorer',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'textarea', 'label' => 'Intro'],
					'items' => ['type' => 'repeater', 'label' => 'Panels', 'required' => true, 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'src' => ['type' => 'media', 'label' => 'Image'], 'alt' => ['type' => 'text', 'label' => 'Alt text'], 'description' => ['type' => 'textarea', 'label' => 'Description']]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'reflection-prompt' => [
				'label' => 'Reflection Prompt',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-reflection-prompt',
				'default_variant' => 'journal',
				'variants' => [
					'journal' => 'Journal',
					'exit-stage' => 'Exit Stage',
					'minimal' => 'Minimal',
					'voices-of-resistance' => 'Voices of Resistance',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'textarea', 'label' => 'Intro'],
					'cards' => ['type' => 'repeater', 'label' => 'Cards', 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body']]],
					'prompt' => ['type' => 'textarea', 'label' => 'Prompt', 'required' => true],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'about' => [
				'label' => 'About',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-about',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'textarea', 'label' => 'Intro'],
					'items' => ['type' => 'repeater', 'label' => 'Cards', 'required' => true, 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'paragraphs' => ['type' => 'repeater', 'label' => 'Paragraphs', 'itemFields' => ['text' => ['type' => 'textarea', 'label' => 'Paragraph']]]]],
				]),
			],
			'hotspot-stage' => [
				'label' => 'Hotspot Stage',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-hotspot-stage',
				'default_variant' => 'story',
				'variants' => [
					'story' => 'Story',
					'voices-of-resistance' => 'Voices of Resistance',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'instruction' => ['type' => 'text', 'label' => 'Instruction'],
					'background_image' => ['type' => 'media', 'label' => 'Background image', 'required' => true],
					'hotspots' => ['type' => 'repeater', 'label' => 'Hotspots', 'required' => true, 'itemFields' => ['key' => ['type' => 'text', 'label' => 'Key'], 'top' => ['type' => 'text', 'label' => 'Top position'], 'left' => ['type' => 'text', 'label' => 'Left position'], 'title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body']]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'progressive-text' => [
				'label' => 'Progressive Text',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-progressive-text',
				'default_variant' => 'narrative',
				'variants' => [
					'narrative' => 'Narrative',
					'voices-of-resistance' => 'Voices of Resistance',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'paragraphs' => ['type' => 'repeater', 'label' => 'Paragraphs', 'required' => true, 'itemFields' => ['text' => ['type' => 'textarea', 'label' => 'Paragraph']]],
					'prompt' => ['type' => 'text', 'label' => 'Prompt label'],
					'button_label' => ['type' => 'text', 'label' => 'Reveal button label'],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'threshold-message' => [
				'label' => 'Threshold Message',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-threshold-message',
				'default_variant' => 'threshold',
				'variants' => [
					'threshold' => 'Threshold',
					'voices-of-resistance' => 'Voices of Resistance',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'horizontal-panels' => [
				'label' => 'Horizontal Panels',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-horizontal-panels',
				'default_variant' => 'quote-march',
				'variants' => [
					'quote-march' => 'Quote March',
					'voices-of-resistance' => 'Voices of Resistance',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'items' => ['type' => 'repeater', 'label' => 'Panels', 'required' => true, 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body'], 'background_image' => ['type' => 'media', 'label' => 'Background image']]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
			'step-sequence' => [
				'label' => 'Step Sequence',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-step-sequence',
				'default_variant' => 'march-history',
				'variants' => [
					'march-history' => 'March History',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'backdrop' => ['type' => 'text', 'label' => 'Backdrop text'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'body' => ['type' => 'textarea', 'label' => 'Body'],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
					'dark' => ['type' => 'select', 'label' => 'Dark stage', 'options' => ['0' => 'No', '1' => 'Yes']],
				]),
			],
			'data-cards' => [
				'label' => 'Data Cards',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-data-cards',
				'default_variant' => 'analytical',
				'variants' => [
					'analytical' => 'Analytical',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'textarea', 'label' => 'Intro'],
					'cards' => [
						'type' => 'repeater',
						'label' => 'Cards',
						'required' => true,
						'itemFields' => [
							'icon' => ['type' => 'text', 'label' => 'Icon'],
							'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
							'stat' => ['type' => 'text', 'label' => 'Statistic'],
							'unit' => ['type' => 'text', 'label' => 'Unit'],
							'summary' => ['type' => 'textarea', 'label' => 'Summary'],
							'toggle' => ['type' => 'text', 'label' => 'Toggle label'],
							'detail' => ['type' => 'textarea', 'label' => 'Detail'],
							'solution' => ['type' => 'textarea', 'label' => 'Solution'],
							'bars' => [
								'type' => 'repeater',
								'label' => 'Bars',
								'itemFields' => [
									'label' => ['type' => 'text', 'label' => 'Label'],
									'width' => ['type' => 'text', 'label' => 'Width'],
									'alert' => ['type' => 'select', 'label' => 'Alert', 'options' => ['0' => 'No', '1' => 'Yes']],
								],
							],
						],
					],
					'footer_text' => ['type' => 'textarea', 'label' => 'Footer text'],
					'footer_href' => ['type' => 'text', 'label' => 'Footer URL'],
					'footer_label' => ['type' => 'text', 'label' => 'Footer label'],
				]),
			],
			'drag-reveal' => [
				'label' => 'Drag Reveal',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-drag-reveal',
				'default_variant' => 'chain',
				'variants' => [
					'chain' => 'Chain',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'text' => ['type' => 'textarea', 'label' => 'Text'],
					'instruction' => ['type' => 'text', 'label' => 'Instruction'],
				]),
			],
			'word-shift' => [
				'label' => 'Word Shift',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-word-shift',
				'default_variant' => 'liberation',
				'variants' => [
					'liberation' => 'Liberation',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'html' => ['type' => 'textarea', 'label' => 'HTML'],
				]),
			],
			'parallax-stage' => [
				'label' => 'Parallax Stage',
				'kind' => 'chapter',
				'loader' => 'template-parts/reflections/chapters/chapter-parallax-stage',
				'default_variant' => 'crowd',
				'variants' => [
					'crowd' => 'Crowd',
				],
				'fields' => reci_reflection_system_with_menu_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'layers' => ['type' => 'repeater', 'label' => 'Layers', 'required' => true, 'itemFields' => ['src' => ['type' => 'media', 'label' => 'Layer image'], 'opacity' => ['type' => 'text', 'label' => 'Opacity'], 'blend' => ['type' => 'text', 'label' => 'Blend mode']]],
					'text' => ['type' => 'textarea', 'label' => 'Text'],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'continue_target' => ['type' => 'text', 'label' => 'Continue target'],
				]),
			],
		];
	}
}

if (! function_exists('reci_reflection_system_default_blueprint')) {
	/**
	 * @return array<string,mixed>
	 */
	function reci_reflection_system_default_blueprint(): array {
		return [
			'version' => 2,
			'system' => 'reflections',
			'chapters' => [],
			'settings' => [
				'mode' => 'immersive',
				'palette' => '',
				'stage_controller' => 'default',
				'menu_enabled' => true,
				'menu_back_url' => home_url('/reflections/'),
			],
		];
	}
}

if (! function_exists('reci_reflection_system_normalize_blueprint')) {
	/**
	 * @param array<string,mixed> $blueprint
	 * @return array<string,mixed>
	 */
	function reci_reflection_system_normalize_blueprint(array $blueprint): array {
		$normalized = array_merge(
			reci_reflection_system_default_blueprint(),
			$blueprint
		);

		$normalized['version'] = 2;
		$normalized['system'] = 'reflections';
		$normalized['chapters'] = array_values(array_filter(
			is_array($normalized['chapters'] ?? null) ? $normalized['chapters'] : [],
			'is_array'
		));
		unset($normalized['elements']);
		$normalized['settings'] = array_merge(
			reci_reflection_system_default_blueprint()['settings'],
			is_array($normalized['settings'] ?? null) ? $normalized['settings'] : []
		);

		return $normalized;
	}
}

if (! function_exists('reci_reflection_system_component_definition')) {
	/**
	 * @return array<string,mixed>|null
	 */
	function reci_reflection_system_component_definition(string $family): ?array {
		$registry = reci_reflection_system_registry();
		return $registry[$family] ?? null;
	}
}


if (! function_exists('reci_reflection_blueprint_uses_new_system')) {
	function reci_reflection_blueprint_uses_new_system(int $post_id): bool {
		if ($post_id <= 0) {
			return false;
		}

		$raw = (string) get_post_meta($post_id, '_reci_reflection_blueprint', true);
		if ($raw === '') {
			return false;
		}

		$decoded = json_decode($raw, true);
		if (! is_array($decoded)) {
			return false;
		}

		return (($decoded['system'] ?? '') === 'reflections') && ((int) ($decoded['version'] ?? 0) >= 2);
	}
}
