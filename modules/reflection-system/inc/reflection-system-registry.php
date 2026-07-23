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

if (! function_exists('reci_reflection_placeholder_image')) {
	/**
	 * URL of the bundled "drop your image here" placeholder.
	 *
	 * Used as the default image in starter templates so empty image slots are
	 * visible. Users replace it via the builder's media field.
	 */
	function reci_reflection_placeholder_image(): string {
		return get_template_directory_uri() . '/modules/reflection-system/assets/images/placeholder.svg';
	}
}

if (! function_exists('reci_reflection_system_with_menu_fields')) {
	/**
	 * @param array<string,array<string,mixed>> $fields
	 * @return array<string,array<string,mixed>>
	 */
	function reci_reflection_system_with_menu_fields(array $fields): array {
		return array_merge(
			[
				'include_in_menu' => ['type' => 'select', 'label' => 'Include in menu', 'options' => ['0' => 'No', '1' => 'Yes']],
				'menu_label' => ['type' => 'text', 'label' => 'Menu label', 'show_if' => ['include_in_menu' => '1']],
				'menu_description' => ['type' => 'textarea', 'label' => 'Menu description', 'show_if' => ['include_in_menu' => '1']],
			],
			$fields
		);
	}
}

if (! function_exists('reci_reflection_system_with_transition_fields')) {
	/**
	 * @param array<string,array<string,mixed>> $fields
	 * @return array<string,array<string,mixed>>
	 */
	function reci_reflection_system_with_transition_fields(array $fields): array {
		return reci_reflection_system_with_menu_fields(array_merge(
			$fields,
			[
				'transition_mode' => [
					'type' => 'select',
					'label' => 'Transition mode',
					'options' => [
						'button' => 'Button (Manual)',
						'scroll' => 'Scroll (Automatic)',
						'auto' => 'Auto (No interaction)'
					]
				],
				'continue_label' => [
					'type' => 'text',
					'label' => 'Continue Button Label'
				],
				'continue_target' => [
					'type' => 'chapter-target',
					'label' => 'Continue Target'
				],
			]
		));
	}
}

if (! function_exists('reci_reflection_system_styles')) {
	/**
	 * @return array<string,array<string,mixed>>
	 */
	function reci_reflection_system_styles(): array {
		return [
			'voices-of-resistance' => [
				'label' => 'Voices of Resistance',
				'base_variant' => 'immersive-dark',
				'colors' => [
					'primary' => '#8B4513',
					'bg' => '#111111',
					'heading' => '#e0e0e0',
					'body' => '#a0a0a0',
					'accent' => '#FFB81C',
				],
				'chapters' => [
					[
						'family' => 'hero',
						'variant' => 'immersive-dark',
						'props' => ['id' => 'vor-title', 'title' => 'Voices of', 'title_accent' => 'Resistance', 'subtitle' => '"I remember the day we decided enough was enough..."', 'actions' => [['label' => 'Enter the Story', 'href' => 'vor-explore']]]
					],
					[
						'family' => 'hotspot-stage',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'vor-explore',
							'background_image' => reci_reflection_placeholder_image(),
							'instruction' => 'Tap the Icons to Uncover the Story',
							'hotspots' => [
								['key' => 'strategy', 'left' => '30%', 'top' => '40%', 'title' => 'The Strategy', 'body' => 'We gathered in the church basement not just to pray, but to plan. Every route was mapped, every risk calculated.'],
								['key' => 'resolve', 'left' => '60%', 'top' => '60%', 'title' => 'The Resolve', 'body' => 'Standing tall wasn\'t just physical. It was a reclaiming of space we were told we couldn\'t occupy.'],
								['key' => 'community', 'left' => '70%', 'top' => '30%', 'title' => 'The Community', 'body' => 'Young and old, doctors and janitors. In this room, titles dissolved. We were simply one people.']
							],
							'transition_mode' => 'scroll', 'continue_label' => 'Scroll Down to Continue ↓',
							'continue_target' => 'vor-hold'
						]
					],
					[
						'family' => 'progressive-text',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'vor-hold',
							'title' => 'The Decision',
							'paragraphs' => [
								['text' => '"The march was scheduled for dawn. We knew the risks."'],
								['text' => '"But staying silent had become more dangerous than speaking out."'],
								['text' => '"As we walked down those streets, arm in arm, I felt the weight of generations."']
							],
							'prompt' => 'Click to Reveal',
							'button_label' => '▼',
							'continue_label' => 'Face the Moment →',
							'continue_target' => 'vor-threshold'
						]
					],
					[
						'family' => 'threshold-message',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'vor-threshold',
							'title' => 'We stood at the edge of history.',
							'body' => 'Every step forward was a step into the unknown, carried only by the collective faith of those who marched. There was no turning back.',
							'continue_label' => 'March Forward',
							'continue_target' => 'vor-march'
						]
					],
					[
						'family' => 'horizontal-panels',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'vor-march',
							'items' => [
								['title' => '"Every step was an act of defiance."'],
								['title' => '"They could beat us, but they could not break our spirit."'],
								['title' => 'The Struggle Continues']
							],
							'button_label' => 'Reflect',
							'continue_target' => 'vor-reflect'
						]
					],
					[
						'family' => 'reflection-prompt',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'vor-reflect',
							'prompt' => '"What does courage mean to you in the context of social justice?"'
						]
					]
				]
			],
			'breaking-chains' => [
				'label' => 'Breaking Chains',
				'base_variant' => 'immersive-dark',
				'colors' => [
					'primary' => '#111111',
					'bg' => '#0a0a0a',
					'heading' => '#e0e0e0',
					'body' => '#e0e0e0',
					'accent' => '#D4AF37',
				],
				'chapters' => [
					[
						'family' => 'hero',
						'variant' => 'immersive-dark',
						'props' => ['id' => 'bc-title', 'title' => 'Breaking Chains', 'subtitle' => 'Liberation is not merely the absence of physical chains. It is the transformation of consciousness.', 'actions' => [['label' => 'Scroll Down to Begin', 'href' => 'bc-chain-stage']]]
					],
					[
						'family' => 'drag-reveal',
						'variant' => 'chain',
						'props' => ['id' => 'bc-chain-stage', 'text' => 'Historically, systems of oppression have relied on more than physical force...', 'instruction' => 'Drag Down to Break']
					],
					[
						'family' => 'word-shift',
						'variant' => 'liberation',
						'props' => ['id' => 'word-shift', 'title' => 'Internal Narratives', 'html' => "The process of liberation requires <span class=\"shift-word\" data-shift=\"CONSCIENTIZATION\">awakening</span>... a shift from feeling <span class=\"shift-word\" data-shift=\"POWERFUL\">powerless</span> to recognizing one's own <span class=\"shift-word\" data-shift=\"AGENCY\">fate</span>.", 'continue_label' => 'Continue →', 'continue_target' => 'bc-hands']
					],
					[
						'family' => 'hero',
						'variant' => 'immersive-dark',
						'props' => ['id' => 'bc-hands', 'title' => 'Unity', 'subtitle' => 'When people come together, they build the collective power necessary for change.', 'foreground_image' => trailingslashit(get_template_directory_uri()) . 'reflection-gallery/assets/images/hands-unity.png', 'actions' => [['label' => 'Step Forward', 'href' => 'bc-freedom']]]
					],
					[
						'family' => 'hero',
						'variant' => 'immersive-dark',
						'props' => ['id' => 'bc-freedom', 'title' => 'Freedom', 'subtitle' => 'A world where all people can live fully, freely, and with dignity.', 'actions' => [['label' => 'Return to Gallery', 'href' => '/reflections/']]]
					]
				]
			],
			'march-toward-justice' => [
				'label' => 'March Toward Justice',
				'base_variant' => 'documentary',
				'colors' => [
					'primary' => '#8a0e0e',
					'bg' => '#e6e6e6',
					'heading' => '#111111',
					'body' => '#a0a0a0',
					'accent' => '#8a0e0e',
				],
				'chapters' => [
					[
						'family' => 'hero',
						'variant' => 'protest-march',
						'props' => ['id' => 'march-title', 'eyebrow' => '1965', 'title' => 'The March Toward Justice', 'use_background_image' => '1', 'background_image' => reci_reflection_placeholder_image(), 'actions' => [['label' => 'Take the First Step', 'href' => 'march-context']]]
					],
					[
						'family' => 'hero',
						'variant' => 'protest-march-dark',
						'props' => ['id' => 'march-context', 'override_colors' => true, 'color_bg' => '#111111', 'color_heading' => '#8a0e0e', 'color_body' => '#dddddd', 'color_accent' => '#8a0e0e', 'title' => 'The Weight of History', 'body' => "The fight for justice didn't start on a bridge in Selma. It began in the fields, in the courtrooms, and in the quiet resolve of millions who refused to accept inequality as their fate.\n\nBy 1965, the Civil Rights Act had passed, but the ballot box remained locked to Black Americans in the South. The march you are about to witness was not just a protest—it was a demand for the soul of democracy.", 'actions' => [['label' => 'Begin the March →', 'href' => 'march-selma']]]
					],
					[
						'family' => 'step-sequence',
						'variant' => 'march-history',
						'props' => ['id' => 'march-selma', 'override_colors' => true, 'color_bg' => '#222222', 'color_body' => '#f0f0f0', 'color_accent' => '#8a0e0e', 'backdrop' => 'SELMA', 'title' => 'The Bridge', 'body' => 'We stood at the edge of the bridge, looking down at the water below and the line of troopers ahead. The air was cold, but our resolve was burning.', 'button_label' => 'Forward to 1968 →', 'continue_target' => 'march-1968', 'dark' => '1']
					],
					[
						'family' => 'step-sequence',
						'variant' => 'march-history',
						'props' => ['id' => 'march-1968', 'override_colors' => true, 'color_bg' => '#222222', 'color_body' => '#f0f0f0', 'color_accent' => '#8a0e0e', 'backdrop' => '1968', 'title' => 'The Mourning', 'body' => 'When the news broke, the world stopped. But silence didn\'t last long. The grief turned into a new kind of fire, one that burned across cities and decades.', 'button_label' => 'Forward to 2020 →', 'continue_target' => 'march-2020', 'dark' => '1']
					],
					[
						'family' => 'step-sequence',
						'variant' => 'march-history',
						'props' => ['id' => 'march-2020', 'override_colors' => true, 'color_bg' => '#222222', 'color_body' => '#f0f0f0', 'color_accent' => '#8a0e0e', 'backdrop' => '2020', 'title' => 'The Awakening', 'body' => 'A global chorus rose up. "I can\'t breathe" became a rallying cry heard in every language. The march hadn\'t ended; it had just found new feet.', 'button_label' => 'Join the Crowd →', 'continue_target' => 'march-crowd', 'dark' => '1']
					],
					[
						'family' => 'parallax-stage',
						'variant' => 'crowd',
						'props' => ['id' => 'march-crowd', 'override_colors' => true, 'color_bg' => '#88aaaa', 'color_accent' => '#8a0e0e', 'text' => '"We are not just marching for ourselves. We are marching for the soul of this nation."', 'button_label' => 'Reflect on the Journey →', 'continue_target' => 'march-reflect', 'layers' => [['src' => reci_reflection_placeholder_image(), 'opacity' => '0.4', 'blend' => 'normal'], ['src' => reci_reflection_placeholder_image(), 'opacity' => '0.6', 'blend' => 'multiply']]]
					],
					[
						'family' => 'reflection-prompt',
						'variant' => 'protest-march',
						'props' => ['id' => 'march-reflect', 'override_colors' => true, 'color_bg' => '#000000', 'color_body' => '#f0f0f0', 'color_accent' => '#8a0e0e', 'prompt' => '"Which moment in this timeline resonates most with you, and why?"']
					],
				]
			],
			'breaking-chains' => [
				'label' => 'Breaking Chains',
				'base_variant' => 'immersive-dark',
				'colors' => [
					'primary' => '#D4AF37',
					'bg' => '#0a0a0a',
					'heading' => '#e0e0e0',
					'body' => '#a0a0a0',
					'accent' => '#D4AF37',
				],
				'chapters' => [
					[
						'family' => 'hero',
						'variant' => 'immersive-dark',
						'props' => ['id' => 'bc-hero', 'title' => 'Breaking Chains', 'body' => '"Liberation is not merely the absence of physical chains. It is the transformation of consciousness."', 'actions' => [['label' => 'Scroll Down to Begin', 'href' => 'bc-chain']], 'transition_mode' => 'scroll', 'continue_target' => 'bc-chain']
					],
					[
						'family' => 'drag-reveal',
						'variant' => 'chain',
						'props' => ['id' => 'bc-chain', 'text' => '"Historically, systems of oppression have relied on more than physical force..."', 'instruction' => 'Drag Down The Chain To Break', 'continue_target' => 's3', 'continue_label' => 'Continue', 'transition_mode' => 'auto']
					],
					[
						'family' => 'word-shift',
						'variant' => 'liberation',
						'props' => ['id' => 's3', 'title' => 'Internal Narratives', 'html' => '<p>"The process of liberation requires <span class="shift-word" data-shift="CONSCIENTIZATION">awakening</span>... a shift from feeling <span class="shift-word" data-shift="POWERFUL">powerless</span> to recognizing one\'s own <span class="shift-word" data-shift="AGENCY">fate</span>."</p>', 'continue_target' => 'bc-power', 'continue_label' => 'Continue', 'transition_mode' => 'scroll']
					],
					[
						'family' => 'feature-split',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'bc-power',
							'title' => 'Collective Power',
							'body' => '"When people come together, they build the collective power necessary for change."',
							'continue_target' => 'bc-freedom',
							'continue_label' => 'Continue',
							'transition_mode' => 'scroll'
						]
					],
					[
						'family' => 'threshold-message',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'bc-freedom',
							'title' => 'Freedom',
							'body' => '"A world where all people can live fully, freely, and with dignity."',
							'continue_target' => 'bc-reflect',
							'continue_label' => 'Reflect on Freedom',
							'transition_mode' => 'scroll'
						]
					],
					[
						'family' => 'reflection-prompt',
						'variant' => 'immersive-dark',
						'props' => [
							'id' => 'bc-reflect',
							'prompt' => 'What does breaking chains mean to you in your own life?',
							'button_label' => 'Submit Reflection',
							'button_href' => '/reflections/'
						]
					]
				]
			],
			'racial-disparities' => [
				'label' => 'Racial Disparities',
				'base_variant' => 'analytical',
				'colors' => [
					'primary' => '#2A4494',
					'bg' => '#f4f4f4',
					'heading' => '#111111',
					'body' => '#a0a0a0',
					'accent' => '#2A4494',
				],
				'chapters' => [
					[
						'family' => 'hero',
						'variant' => 'analytical',
						'props' => [
							'id' => 'rd-hero',
							'use_background_image' => '1',
							'background_image' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/site/reflections/racial-disparities/pexels-anna-nekrashevich-8058540.jpg',
							'overlay_rgb' => '255,255,255',
							'overlay_opacity' => 0.70,
							'title' => 'The Data Gap',
							'body' => 'Racial disparities are not just numbers. They are structural realities that affect lives. Tap a domain to examine the evidence.',
							'actions' => [['label' => 'View Data', 'href' => 'rd-analysis']]
						]
					],
					[
						'family' => 'data-cards',
						'variant' => 'analytical',
						'props' => [
							'id' => 'rd-analysis',
							'title' => 'The Data',
							'cards' => [
								[
									'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxsaW5lIHgxPSIxMiIgeTE9IjEiIHgyPSIxMiIgeTI9IjIzIj48L2xpbmU+PHBhdGggZD0iTTE3IDVIOS41YTMuNSAzLjUgMCAwIDAgMCA3aDVhMy41IDMuNSAwIDAgMSAwIDdINyI+PC9wYXRoPjwvc3ZnPg==',
									'eyebrow' => 'Economic Inequality',
									'stat' => '10x',
									'unit' => 'Wealth Gap',
									'summary' => 'Median white family wealth vs. Black family wealth.',
									'toggle' => 'View Solution',
									'detail' => '"The racial wealth gap has widened in recent decades, reflecting centuries of discriminatory policies."',
									'bars' => [
										['label' => 'White', 'width' => '100%', 'alert' => false],
										['label' => 'Black', 'width' => '10%', 'alert' => true]
									],
									'solution' => 'Reparative policies such as baby bonds, down payment assistance, and strict enforcement of fair lending laws are evidenced-based ways to close the gap.'
								],
								[
									'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxwYXRoIGQ9Ik0xMiAzbTAtMiAwIDIyIi8+PHBhdGggZD0iTTkgM2g2Ii8+PHBhdGggZD0iTTMgMTBoMTgiLz48cGF0aCBkPSJNMTEgMjBoNCIvPjxwb2x5Z29uIHBvaW50cz0iMyAxMCA2IDIwIDkgMTAiLz48cG9seWdvbiBwb2ludHM9IjE1IDEwIDE4IDIwIDIxIDEwIi8+PC9zdmc+',
									'eyebrow' => 'Criminal Justice',
									'stat' => '5x',
									'unit' => 'Incarceration Rate',
									'summary' => 'Black Americans are incarcerated at 5x the rate of whites.',
									'detail' => '"Despite similar rates of drug use, the justice system disproportionately targets communities of color."',
									'bars' => [
										['label' => 'White', 'width' => '20%', 'alert' => false],
										['label' => 'Black', 'width' => '100%', 'alert' => true]
									],
									'solution' => 'Criminal justice reform and community investments are key paths forward.'
								],
								[
									'icon' => '🏥',
									'eyebrow' => 'Healthcare Access',
									'stat' => '40%',
									'unit' => 'Higher Mortality',
									'summary' => 'Maternal mortality rates for Black women vs. white women.',
									'detail' => '"These differences are not explained by genetics, but by systemic factors and bias in medical treatment."',
									'solution' => 'Addressing implicit bias in healthcare and universal coverage are critical steps.'
								],
								[
									'icon' => '🎓',
									'eyebrow' => 'Education',
									'stat' => '$23B',
									'unit' => 'Funding Gap',
									'summary' => 'Annual funding difference between white and non-white districts.',
									'detail' => '"Students of color are more likely to attend underfunded schools with fewer resources and advanced courses."',
									'solution' => 'Equitable school funding formulas and increasing teacher diversity.'
								]
							],
						]
					],
				]
			]
		];
	}
}

if (! function_exists('reci_reflection_system_registry')) {
	/**
	 * @return array<string,array<string,mixed>>
	 */
	function reci_reflection_system_registry(): array {
		return [
			'hero' => [
				'label' => 'Hero',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/hero',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
					'narrative' => 'Narrative',
					'testimonial' => 'Testimonial',
					'analytical' => 'Analytical',
					'immersive-dark' => 'Immersive Dark',
					'protest-march' => 'Protest March',
					'protest-march-dark' => 'Protest March Dark',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'title_accent' => ['type' => 'text', 'label' => 'Title (highlighted line, accent color)', 'show_if' => ['variant' => 'immersive-dark']],
					'subtitle' => ['type' => 'text', 'label' => 'Subtitle'],
					'body' => ['type' => 'textarea', 'label' => 'Body'],
					'foreground_image' => ['type' => 'media', 'label' => 'Foreground image', 'show_if' => ['variant' => ['immersive-dark']]],
					'caption' => ['type' => 'textarea', 'label' => 'Caption'],
					'use_background_image' => ['type' => 'select', 'label' => 'Enable background image?', 'options' => ['0' => 'No', '1' => 'Yes']],
					'background_image' => ['type' => 'media', 'label' => 'Background image', 'show_if' => ['use_background_image' => '1']],
					'overlay_intensity' => ['type' => 'range', 'label' => 'Overlay intensity', 'show_if' => ['use_background_image' => '1', 'variant' => ['documentary', 'narrative', 'testimonial', 'immersive-dark', 'analytical', 'protest-march']]],
					'overlay_color' => ['type' => 'color', 'label' => 'Overlay color', 'show_if' => ['use_background_image' => '1', 'variant' => ['documentary', 'narrative', 'testimonial', 'immersive-dark', 'analytical', 'protest-march']]],
					'align_horizontal' => ['type' => 'select', 'label' => 'Horizontal align', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'], 'default' => 'center'],
					'align_vertical' => ['type' => 'select', 'label' => 'Vertical align', 'options' => ['top' => 'Top', 'center' => 'Center', 'bottom' => 'Bottom'], 'default' => 'center'],
					'actions' => ['type' => 'repeater', 'label' => 'Actions', 'itemFields' => ['label' => ['type' => 'text', 'label' => 'Label'], 'href' => ['type' => 'chapter-target', 'label' => 'Target'], 'class' => ['type' => 'text', 'label' => 'Classes']]],
				]),
			],
			'feature-split' => [
				'label' => 'Feature Split',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-feature-split',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_transition_fields([
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
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'documentary-dossier' => [
				'label' => 'Documentary Dossier',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-documentary-dossier',
				'default_variant' => 'archival',
				'variants' => [
					'archival' => 'Archival',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'repeater', 'label' => 'Intro paragraphs', 'itemFields' => ['text' => ['type' => 'textarea', 'label' => 'Paragraph']]],
					'sections' => ['type' => 'repeater', 'label' => 'Sections', 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'paragraphs' => ['type' => 'repeater', 'label' => 'Paragraphs', 'itemFields' => ['text' => ['type' => 'textarea', 'label' => 'Paragraph']]], 'links' => ['type' => 'repeater', 'label' => 'Links', 'itemFields' => ['label' => ['type' => 'text', 'label' => 'Label'], 'href' => ['type' => 'chapter-target', 'label' => 'Target Chapter']]]]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'timeline-world' => [
				'label' => 'Timeline World',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-timeline-world',
				'default_variant' => 'horizontal',
				'variants' => [
					'horizontal' => 'Horizontal',
					'documentary' => 'Documentary',
					'march-history' => 'March History',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'items' => ['type' => 'repeater', 'label' => 'Timeline items', 'required' => true, 'itemFields' => ['date' => ['type' => 'text', 'label' => 'Date / Year'], 'title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body'], 'media' => ['type' => 'repeater', 'label' => 'Media', 'itemFields' => ['src' => ['type' => 'media', 'label' => 'Image'], 'alt' => ['type' => 'text', 'label' => 'Alt text'], 'caption' => ['type' => 'textarea', 'label' => 'Caption']]], 'link' => ['type' => 'repeater', 'label' => 'Link', 'itemFields' => ['label' => ['type' => 'text', 'label' => 'Label'], 'href' => ['type' => 'text', 'label' => 'URL']]]]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'panel-explorer' => [
				'label' => 'Panel Explorer',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-panel-explorer',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'textarea', 'label' => 'Intro'],
					'items' => ['type' => 'repeater', 'label' => 'Panels', 'required' => true, 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'src' => ['type' => 'media', 'label' => 'Image'], 'alt' => ['type' => 'text', 'label' => 'Alt text'], 'description' => ['type' => 'textarea', 'label' => 'Description']]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'reflection-prompt' => [
				'label' => 'Reflection Prompt',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-reflection-prompt',
				'default_variant' => 'journal',
				'variants' => [
					'journal' => 'Journal',
					'exit-stage' => 'Exit Stage',
					'minimal' => 'Minimal',
					'immersive-dark' => 'Immersive Dark',
					'protest-march' => 'Protest March',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'prompt' => ['type' => 'textarea', 'label' => 'Prompt', 'required' => true],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'button_href' => ['type' => 'text', 'label' => 'Button href'],
				]),
			],
			'quote' => [
				'label' => 'Quote',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-quote',
				'default_variant' => 'immersive-dark',
				'variants' => [
					'immersive-dark' => 'Immersive Dark',
					'default' => 'Default',
					'editorial' => 'Editorial',
					'cinematic' => 'Cinematic',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow'],
					'quote' => ['type' => 'textarea', 'label' => 'Quote', 'required' => true],
					'attribution' => ['type' => 'text', 'label' => 'Attribution (— Name)'],
					'text_size' => ['type' => 'select', 'label' => 'Text Size', 'options' => ['small' => 'Small', 'normal' => 'Normal', 'large' => 'Large']],
					'align_horizontal' => ['type' => 'select', 'label' => 'Horizontal Alignment', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']],
					'align_vertical' => ['type' => 'select', 'label' => 'Vertical Alignment', 'options' => ['top' => 'Top', 'center' => 'Center', 'bottom' => 'Bottom']],
					'background_image' => ['type' => 'media', 'label' => 'Background image (optional)'],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'about' => [
				'label' => 'About',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-about',
				'default_variant' => 'documentary',
				'variants' => [
					'documentary' => 'Documentary',
				],
				'fields' => reci_reflection_system_with_transition_fields([
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
				'loader' => 'modules/reflection-system/templates/chapters/chapter-hotspot-stage',
				'default_variant' => 'story',
				'variants' => [
					'story' => 'Story',
					'immersive-dark' => 'Immersive Dark',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'instruction' => ['type' => 'text', 'label' => 'Instruction'],
					'background_image' => ['type' => 'media', 'label' => 'Background image', 'required' => true],
					'hotspots' => ['type' => 'repeater', 'label' => 'Hotspots', 'required' => true, 'itemFields' => ['key' => ['type' => 'text', 'label' => 'Key'], 'top' => ['type' => 'text', 'label' => 'Top position'], 'left' => ['type' => 'text', 'label' => 'Left position'], 'title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body']]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'progressive-text' => [
				'label' => 'Progressive Text',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-progressive-text',
				'default_variant' => 'narrative',
				'variants' => [
					'narrative' => 'Narrative',
					'immersive-dark' => 'Immersive Dark',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'paragraphs' => [
						'type' => 'repeater', 
						'label' => 'Paragraphs', 
						'required' => true, 
						'maxItems' => 4,
						'description' => 'Maximum 4 paragraphs. Fades out smoothly as they accumulate.',
						'itemFields' => [
							'text' => [
								'type' => 'textarea', 
								'label' => 'Paragraph',
								'maxLength' => 150,
								'description' => 'Keep it brief and punchy for maximum impact.'
							]
						]
					],
					'prompt' => ['type' => 'text', 'label' => 'Prompt label'],
					'button_label' => ['type' => 'text', 'label' => 'Reveal button label'],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'threshold-message' => [
				'label' => 'Threshold Message',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-threshold-message',
				'default_variant' => 'threshold',
				'variants' => [
					'threshold' => 'Threshold',
					'immersive-dark' => 'Immersive Dark',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'body' => ['type' => 'textarea', 'label' => 'Body Message'],
				]),
			],
			'horizontal-panels' => [
				'label' => 'Horizontal Panels',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-horizontal-panels',
				'default_variant' => 'quote-march',
				'variants' => [
					'quote-march' => 'Quote March',
					'immersive-dark' => 'Immersive Dark',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'items' => ['type' => 'repeater', 'label' => 'Panels', 'required' => true, 'itemFields' => ['title' => ['type' => 'text', 'label' => 'Title'], 'body' => ['type' => 'textarea', 'label' => 'Body'], 'background_image' => ['type' => 'media', 'label' => 'Background image']]],
					'continue_label' => ['type' => 'text', 'label' => 'Continue label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
				]),
			],
			'step-sequence' => [
				'label' => 'Step Sequence',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-step-sequence',
				'default_variant' => 'march-history',
				'variants' => [
					'march-history' => 'March History',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'backdrop' => ['type' => 'text', 'label' => 'Backdrop text'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'body' => ['type' => 'textarea', 'label' => 'Body'],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
					'dark' => ['type' => 'select', 'label' => 'Dark stage', 'options' => ['0' => 'No', '1' => 'Yes']],
				]),
			],
			'data-cards' => [
				'label' => 'Data Cards',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-data-cards',
				'default_variant' => 'analytical',
				'variants' => [
					'analytical' => 'Analytical (Inherit)',
					'analytical-light' => 'Analytical (Light Card)',
					'analytical-dark' => 'Analytical (Dark Card)',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title', 'required' => true],
					'intro' => ['type' => 'textarea', 'label' => 'Intro'],
					'cards' => [
						'type' => 'repeater',
						'label' => 'Cards',
						'required' => true,
						'itemFields' => [
							'icon' => ['type' => 'media', 'label' => 'Icon (Image/SVG)'],
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
				]),
			],
			'drag-reveal' => [
				'label' => 'Drag Reveal',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-drag-reveal',
				'default_variant' => 'chain',
				'variants' => [
					'chain' => 'Chain',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'text' => ['type' => 'textarea', 'label' => 'Text'],
					'instruction' => ['type' => 'text', 'label' => 'Instruction'],
				]),
			],
			'word-shift' => [
				'label' => 'Word Shift',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-word-shift',
				'default_variant' => 'liberation',
				'variants' => [
					'liberation' => 'Liberation',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'title' => ['type' => 'text', 'label' => 'Title'],
					'html' => ['type' => 'textarea', 'label' => 'HTML'],
				]),
			],
			'parallax-stage' => [
				'label' => 'Parallax Stage',
				'kind' => 'chapter',
				'loader' => 'modules/reflection-system/templates/chapters/chapter-parallax-stage',
				'default_variant' => 'crowd',
				'variants' => [
					'crowd' => 'Crowd',
				],
				'fields' => reci_reflection_system_with_transition_fields([
					'id' => ['type' => 'text', 'label' => 'Section ID'],
					'layers' => ['type' => 'repeater', 'label' => 'Layers', 'required' => true, 'itemFields' => ['src' => ['type' => 'media', 'label' => 'Layer image'], 'opacity' => ['type' => 'text', 'label' => 'Opacity'], 'blend' => ['type' => 'text', 'label' => 'Blend mode']]],
					'text' => ['type' => 'textarea', 'label' => 'Text'],
					'button_label' => ['type' => 'text', 'label' => 'Button label'],
					'continue_target' => ['type' => 'chapter-target', 'label' => 'Continue target'],
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
				'global_style' => 'immersive-dark',
				'color_primary' => '#8B4513',
				'color_bg' => '#111111',
				'color_heading' => '#e0e0e0',
				'color_body' => '#a0a0a0',
				'color_accent' => '#FFB81C',
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
		// Migration block removed as per user request.

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
