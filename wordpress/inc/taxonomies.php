<?php
/**
 * Register shared taxonomies for media content.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_taxonomy_post_types')) {
	/**
	 * Post types that should share category, tag, and location taxonomies.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_taxonomy_post_types(): array {
		return [
			'post',
			'reci_article',
			'reci_podcast',
			'reci_video',
			'reci_event',
			'reci_reflection',
			'reci_assessment',
			'reci_course',
		];
	}
}

if (! function_exists('reci_media_hub_submission_taxonomy_post_types')) {
	/**
	 * Post types that should support submission-alignment taxonomies.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_submission_taxonomy_post_types(): array {
		return [
			'post',
			'reci_article',
			'reci_podcast',
			'reci_video',
			'reci_event',
			'reci_reflection',
			'reci_assessment',
			'reci_course',
			'reci_team',
			'reci_author',
		];
	}
}

if (! function_exists('reci_media_hub_default_spheres')) {
	/**
	 * Canonical RECI sphere defaults used by taxonomy seed/config.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	function reci_media_hub_default_spheres(): array {
		return [
			[
				'slug'          => 'recognizing-racial-oppression',
				'num'           => '01',
				'awareness'     => 'Recognizing Racial Oppression',
				'action'        => 'Advancing Racial Liberation',
				'color'         => '#5875FF',
				'gradient'      => 'linear-gradient(135deg, #5875FF, #8AA0FF)',
				'image_file'    => 'Sphere 1 Recognizing Racial Oppression and Advancing Racial Liberation.png',
				'desc'          => 'Content that helps individuals and communities identify the structures, systems, and mechanisms of racial oppression—and that illuminates pathways toward racial liberation.',
				'guideQuestions' => [
					'Does your content examine how racial oppression operates within specific systems (education, healthcare, housing, criminal justice, etc.)?',
					'Does it identify concrete structures or policies that perpetuate racial inequity?',
					'Does it offer frameworks, models, or examples of how racial liberation is being advanced?',
					'Does it center the experiences and agency of racially oppressed communities?',
				],
				'exampleTopics' => 'Structural racism analysis, abolition frameworks, liberation movements, decolonization, reparations discourse, anti-racist policy design',
			],
			[
				'slug'          => 'examining-racial-identities',
				'num'           => '02',
				'awareness'     => 'Examining Racial Identities',
				'action'        => 'Addressing Racial Biases',
				'color'         => '#E5B13B',
				'gradient'      => 'linear-gradient(135deg, #E5B13B, #F2D184)',
				'image_file'    => 'Sphere 2 Examining Racial Identities and Addressing Racial Biases.png',
				'desc'          => 'Content that supports deep examination of racial identity formation and the biases that emerge from socialization—and that provides tools for confronting and transforming those biases.',
				'guideQuestions' => [
					'Does your content explore how racial identities are formed, performed, or perceived?',
					'Does it address implicit or explicit racial biases and their impacts?',
					'Does it offer evidence-based approaches for identifying and mitigating racial bias?',
					'Does it help individuals understand their own racial identity in relation to others?',
				],
				'exampleTopics' => 'Racial identity development, implicit bias interventions, racial socialization, whiteness studies, multiracial identity, colorism',
			],
			[
				'slug'          => 'embracing-racial-diversity',
				'num'           => '03',
				'awareness'     => 'Embracing Racial Diversity',
				'action'        => 'Growing Racial Literacy',
				'color'         => '#66B570',
				'gradient'      => 'linear-gradient(135deg, #66B570, #95D29C)',
				'image_file'    => 'Sphere 3 Embracing Racial Diversity and Growing Racial Literacy.png',
				'desc'          => 'Content that celebrates and engages with the richness of racial diversity while building the knowledge and competencies needed to navigate racial dynamics with skill and understanding.',
				'guideQuestions' => [
					'Does your content promote genuine engagement with racial diversity beyond surface-level representation?',
					'Does it build racial literacy—the ability to read, interpret, and respond to racial dynamics?',
					'Does it provide frameworks for understanding cultural differences and commonalities?',
					'Does it move beyond tolerance toward meaningful inclusion and belonging?',
				],
				'exampleTopics' => 'Cultural competency development, racial literacy curricula, inclusive organizational design, cross-racial dialogue, multicultural education, belonging frameworks',
			],
			[
				'slug'          => 'building-racial-empathy',
				'num'           => '04',
				'awareness'     => 'Building Racial Empathy',
				'action'        => 'Enhancing Racial Stamina',
				'color'         => '#6099B1',
				'gradient'      => 'linear-gradient(135deg, #6099B1, #8ABBCF)',
				'image_file'    => 'Sphere 4 Building Racial Empathy and Enhancing Racial Stamina.png',
				'desc'          => "Content that cultivates the capacity to understand and share in the racial experiences of others, and that builds the endurance needed to sustain engagement with difficult racial realities over time.",
				'guideQuestions' => [
					'Does your content foster the ability to empathize across racial difference?',
					"Does it help people develop stamina for sustained racial equity engagement—even when it's uncomfortable?",
					'Does it address racial anxiety, fragility, or fatigue and offer strategies for resilience?',
					'Does it model what sustained, courageous engagement with racial equity looks like?',
				],
				'exampleTopics' => 'Racial anxiety research, racial stamina development, courageous conversations, intergroup contact theory, empathy-building practices, sustaining activism',
			],
			[
				'slug'          => 'acknowledging-racial-trauma',
				'num'           => '05',
				'awareness'     => 'Acknowledging Racial Trauma',
				'action'        => 'Fostering Racial Healing',
				'color'         => '#9368AC',
				'gradient'      => 'linear-gradient(135deg, #9368AC, #B896CC)',
				'image_file'    => 'Sphere 5 Acknowledging Racial Trauma and Fostering Racial Healing.png',
				'desc'          => 'Content that names and validates the individual and collective trauma caused by racism, and that offers evidence-based or culturally grounded pathways toward healing and restoration.',
				'guideQuestions' => [
					'Does your content acknowledge the reality and depth of racial trauma—individual, intergenerational, and collective?',
					'Does it avoid retraumatization while still bearing witness to painful realities?',
					'Does it offer healing frameworks, restorative practices, or therapeutic approaches?',
					'Does it center the voices and agency of those most impacted by racial trauma?',
				],
				'exampleTopics' => 'Racial trauma research, healing-centered engagement, restorative justice, truth and reconciliation, intergenerational trauma, community care models',
			],
			[
				'slug'          => 'gauging-racial-inequities',
				'num'           => '06',
				'awareness'     => 'Gauging Racial Inequities',
				'action'        => 'Championing Racial Justice',
				'color'         => '#45938D',
				'gradient'      => 'linear-gradient(135deg, #45938D, #72B8B3)',
				'image_file'    => 'Sphere 6 Gauging Racial Inequity and Championing Racial Justice.png',
				'desc'          => 'Content that measures, documents, and analyzes racial inequities with rigor—and that champions concrete actions, policies, and movements advancing racial justice.',
				'guideQuestions' => [
					'Does your content provide data, metrics, or analysis that illuminate the scope of racial inequities?',
					'Does it document racial disparities in specific domains with evidentiary support?',
					'Does it advocate for or evaluate specific policies, programs, or interventions advancing racial justice?',
					'Does it connect measurement of inequity to actionable change strategies?',
				],
				'exampleTopics' => 'Racial equity audits, disparity data analysis, policy evaluation, social impact measurement, advocacy strategies, equity scorecards, justice reform',
			],
		];
	}
}

if (! function_exists('reci_media_hub_default_practice_focus_terms')) {
	/**
	 * Canonical default practice/focus-area terms.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_default_practice_focus_terms(): array {
		return [
			'Practice / Intervention',
			'Policy / Legislation',
			'Program / Initiative',
			'Framework / Model',
			'Curriculum / Training',
			'Community-Based Approach',
			'Organizational Strategy',
			'Research / Evaluation',
			'Other',
		];
	}
}

if (! function_exists('reci_media_hub_default_target_audience_terms')) {
	/**
	 * Canonical default target-audience terms.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_default_target_audience_terms(): array {
		return [
			'Educators / Academics',
			'Community Organizers',
			'Policy Makers',
			'Organizational Leaders',
			'Students',
			'General Public',
			'Healthcare Professionals',
			'Social Workers',
			'Legal Professionals',
			'Faith Communities',
		];
	}
}

if (! function_exists('reci_media_hub_register_taxonomies')) {
	function reci_media_hub_register_taxonomies(): void {
		$post_types            = reci_media_hub_taxonomy_post_types();
		$submission_post_types = reci_media_hub_submission_taxonomy_post_types();

		foreach ($post_types as $post_type) {
			register_taxonomy_for_object_type('category', $post_type);
			register_taxonomy_for_object_type('post_tag', $post_type);
		}

		register_taxonomy(
			'reci_location',
			$post_types,
			[
				'labels'            => [
					'name'          => __('Locations', 'reci-media-hub'),
					'singular_name' => __('Location', 'reci-media-hub'),
					'search_items'  => __('Search Locations', 'reci-media-hub'),
					'all_items'     => __('All Locations', 'reci-media-hub'),
					'edit_item'     => __('Edit Location', 'reci-media-hub'),
					'update_item'   => __('Update Location', 'reci-media-hub'),
					'add_new_item'  => __('Add New Location', 'reci-media-hub'),
					'menu_name'     => __('Locations', 'reci-media-hub'),
				],
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => ['slug' => 'location'],
			]
		);

		register_taxonomy(
			'reci_sphere',
			$submission_post_types,
			[
				'labels'            => [
					'name'          => __('RECI Spheres', 'reci-media-hub'),
					'singular_name' => __('RECI Sphere', 'reci-media-hub'),
					'search_items'  => __('Search RECI Spheres', 'reci-media-hub'),
					'all_items'     => __('All RECI Spheres', 'reci-media-hub'),
					'edit_item'     => __('Edit RECI Sphere', 'reci-media-hub'),
					'update_item'   => __('Update RECI Sphere', 'reci-media-hub'),
					'add_new_item'  => __('Add New RECI Sphere', 'reci-media-hub'),
					'menu_name'     => __('RECI Spheres', 'reci-media-hub'),
				],
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => ['slug' => 'reci-sphere'],
			]
		);

		register_taxonomy(
			'reci_practice_focus',
			$submission_post_types,
			[
				'labels'            => [
					'name'          => __('Practice / Focus Areas', 'reci-media-hub'),
					'singular_name' => __('Practice / Focus Area', 'reci-media-hub'),
					'search_items'  => __('Search Practice / Focus Areas', 'reci-media-hub'),
					'all_items'     => __('All Practice / Focus Areas', 'reci-media-hub'),
					'edit_item'     => __('Edit Practice / Focus Area', 'reci-media-hub'),
					'update_item'   => __('Update Practice / Focus Area', 'reci-media-hub'),
					'add_new_item'  => __('Add New Practice / Focus Area', 'reci-media-hub'),
					'menu_name'     => __('Practice / Focus Areas', 'reci-media-hub'),
				],
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => ['slug' => 'practice-focus'],
			]
		);

		register_taxonomy(
			'reci_target_audience',
			$submission_post_types,
			[
				'labels'            => [
					'name'          => __('Target Audiences', 'reci-media-hub'),
					'singular_name' => __('Target Audience', 'reci-media-hub'),
					'search_items'  => __('Search Target Audiences', 'reci-media-hub'),
					'all_items'     => __('All Target Audiences', 'reci-media-hub'),
					'edit_item'     => __('Edit Target Audience', 'reci-media-hub'),
					'update_item'   => __('Update Target Audience', 'reci-media-hub'),
					'add_new_item'  => __('Add New Target Audience', 'reci-media-hub'),
					'menu_name'     => __('Target Audiences', 'reci-media-hub'),
				],
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => false,
				'show_admin_column' => true,
				'rewrite'           => ['slug' => 'target-audience'],
			]
		);
	}
}

if (! function_exists('reci_media_hub_get_sphere_default_by_slug')) {
	/**
	 * Get default sphere definition by slug.
	 *
	 * @param string $slug Sphere slug.
	 *
	 * @return array<string,mixed>|null
	 */
	function reci_media_hub_get_sphere_default_by_slug(string $slug): ?array {
		foreach (reci_media_hub_default_spheres() as $sphere) {
			if (($sphere['slug'] ?? '') === $slug) {
				return $sphere;
			}
		}
		return null;
	}
}

if (! function_exists('reci_media_hub_seed_default_spheres')) {
	/**
	 * Ensure the canonical RECI sphere terms and metadata exist.
	 */
	function reci_media_hub_seed_default_spheres(): void {
		foreach (reci_media_hub_default_spheres() as $index => $sphere) {
			$awareness = (string) ($sphere['awareness'] ?? '');
			$slug      = (string) ($sphere['slug'] ?? '');
			if ($awareness === '' || $slug === '') {
				continue;
			}

			$term_result = term_exists($slug, 'reci_sphere');
			$term_id = 0;
			if (is_array($term_result) && isset($term_result['term_id'])) {
				$term_id = (int) $term_result['term_id'];
			} elseif (is_int($term_result)) {
				$term_id = $term_result;
			}

			if ($term_id <= 0) {
				$inserted = wp_insert_term(
					$awareness,
					'reci_sphere',
					[
						'slug'        => $slug,
						'description' => (string) ($sphere['desc'] ?? ''),
					]
				);
				if (is_wp_error($inserted) || ! isset($inserted['term_id'])) {
					continue;
				}
				$term_id = (int) $inserted['term_id'];
			}

			$default_values = [
				'reci_sphere_awareness'      => (string) ($sphere['awareness'] ?? ''),
				'reci_sphere_action'         => (string) ($sphere['action'] ?? ''),
				'reci_sphere_color'          => (string) ($sphere['color'] ?? ''),
				'reci_sphere_gradient'       => (string) ($sphere['gradient'] ?? ''),
				'reci_sphere_desc'           => (string) ($sphere['desc'] ?? ''),
				'reci_sphere_num'            => (string) ($sphere['num'] ?? sprintf('%02d', $index + 1)),
				'reci_sphere_guide_questions' => implode("\n", (array) ($sphere['guideQuestions'] ?? [])),
				'reci_sphere_example_topics' => (string) ($sphere['exampleTopics'] ?? ''),
			];

			foreach ($default_values as $meta_key => $default_value) {
				if ($default_value === '') {
					continue;
				}
				$current_value = (string) get_term_meta($term_id, $meta_key, true);
				if ($current_value === '' || $current_value !== $default_value) {
					update_term_meta($term_id, $meta_key, $default_value);
				}
			}
		}
	}
}

if (! function_exists('reci_media_hub_seed_default_taxonomy_terms')) {
	/**
	 * Ensure default practice-focus and target-audience terms exist.
	 */
	function reci_media_hub_seed_default_taxonomy_terms(): void {
		$taxonomy_defaults = [
			'reci_practice_focus'  => reci_media_hub_default_practice_focus_terms(),
			'reci_target_audience' => reci_media_hub_default_target_audience_terms(),
		];

		foreach ($taxonomy_defaults as $taxonomy => $terms) {
			foreach ($terms as $term_name) {
				$clean_name = sanitize_text_field((string) $term_name);
				if ($clean_name === '') {
					continue;
				}

				$exists = term_exists($clean_name, $taxonomy);
				if ($exists) {
					continue;
				}

				wp_insert_term($clean_name, $taxonomy);
			}
		}
	}
}

if (! function_exists('reci_media_hub_render_sphere_fields')) {
	/**
	 * Render custom RECI sphere fields.
	 *
	 * @param mixed       $term     Existing term when editing, taxonomy string when adding.
	 * @param string|null $taxonomy Taxonomy slug (optional in edit context).
	 */
	function reci_media_hub_render_sphere_fields($term = null, ?string $taxonomy = null): void {
		if (is_string($term) && $taxonomy === null) {
			$taxonomy = $term;
			$term = null;
		}

		$meta = [
			'awareness'      => '',
			'action'         => '',
			'color'          => '',
			'gradient'       => '',
			'desc'           => '',
			'num'            => '',
			'guideQuestions' => '',
			'exampleTopics'  => '',
			'contentImageId' => 0,
		];

		if ($term instanceof WP_Term) {
			$default = reci_media_hub_get_sphere_default_by_slug($term->slug);
			$meta['awareness'] = (string) get_term_meta($term->term_id, 'reci_sphere_awareness', true);
			$meta['action'] = (string) get_term_meta($term->term_id, 'reci_sphere_action', true);
			$meta['color'] = (string) get_term_meta($term->term_id, 'reci_sphere_color', true);
			$meta['gradient'] = (string) get_term_meta($term->term_id, 'reci_sphere_gradient', true);
			$meta['desc'] = (string) get_term_meta($term->term_id, 'reci_sphere_desc', true);
			$meta['num'] = (string) get_term_meta($term->term_id, 'reci_sphere_num', true);
			$meta['guideQuestions'] = (string) get_term_meta($term->term_id, 'reci_sphere_guide_questions', true);
			$meta['exampleTopics'] = (string) get_term_meta($term->term_id, 'reci_sphere_example_topics', true);
			$meta['contentImageId'] = (int) get_term_meta($term->term_id, 'reci_sphere_content_image_id', true);

			if (is_array($default)) {
				$meta['awareness'] = $meta['awareness'] !== '' ? $meta['awareness'] : (string) ($default['awareness'] ?? $term->name);
				$meta['action'] = $meta['action'] !== '' ? $meta['action'] : (string) ($default['action'] ?? '');
				$meta['color'] = $meta['color'] !== '' ? $meta['color'] : (string) ($default['color'] ?? '');
				$meta['gradient'] = $meta['gradient'] !== '' ? $meta['gradient'] : (string) ($default['gradient'] ?? '');
				$meta['desc'] = $meta['desc'] !== '' ? $meta['desc'] : (string) ($default['desc'] ?? $term->description);
				$meta['num'] = $meta['num'] !== '' ? $meta['num'] : (string) ($default['num'] ?? '');
				$meta['guideQuestions'] = $meta['guideQuestions'] !== '' ? $meta['guideQuestions'] : implode("\n", (array) ($default['guideQuestions'] ?? []));
				$meta['exampleTopics'] = $meta['exampleTopics'] !== '' ? $meta['exampleTopics'] : (string) ($default['exampleTopics'] ?? '');
			}
		}

		$is_edit = $term instanceof WP_Term;
		$fields = [
			[
				'key'   => 'awareness',
				'label' => __('Awareness (Title)', 'reci-media-hub'),
				'type'  => 'text',
				'help'  => __('Sphere awareness title shown in the form.', 'reci-media-hub'),
			],
			[
				'key'   => 'action',
				'label' => __('Action (Sub-title)', 'reci-media-hub'),
				'type'  => 'text',
				'help'  => __('Action subtitle shown under awareness.', 'reci-media-hub'),
			],
			[
				'key'   => 'num',
				'label' => __('Step Number', 'reci-media-hub'),
				'type'  => 'text',
				'help'  => __('Two-digit display number, e.g. 01.', 'reci-media-hub'),
			],
			[
				'key'   => 'color',
				'label' => __('Color', 'reci-media-hub'),
				'type'  => 'text',
				'help'  => __('Hex color used for badges and accents.', 'reci-media-hub'),
			],
			[
				'key'   => 'gradient',
				'label' => __('Gradient', 'reci-media-hub'),
				'type'  => 'text',
				'help'  => __('CSS gradient value for the sphere marker.', 'reci-media-hub'),
			],
			[
				'key'   => 'desc',
				'label' => __('Description', 'reci-media-hub'),
				'type'  => 'textarea',
				'help'  => __('Primary descriptive copy for this sphere.', 'reci-media-hub'),
			],
			[
				'key'   => 'guideQuestions',
				'label' => __('Guidelines', 'reci-media-hub'),
				'type'  => 'textarea',
				'help'  => __('One guideline question per line.', 'reci-media-hub'),
			],
			[
				'key'   => 'exampleTopics',
				'label' => __('Example Topics', 'reci-media-hub'),
				'type'  => 'textarea',
				'help'  => __('Comma-separated list displayed as examples.', 'reci-media-hub'),
			],
			[
				'key'   => 'contentImageId',
				'label' => __('Sphere Content Image', 'reci-media-hub'),
				'type'  => 'image',
				'help'  => __('Large content image displayed on the single sphere page.', 'reci-media-hub'),
			],
		];

		foreach ($fields as $field) {
			$key = (string) $field['key'];
			$value = $meta[$key] ?? '';
			$label = (string) $field['label'];
			$help = (string) $field['help'];

			if ($field['type'] === 'image') {
				$image_url = '';
				if ($value) {
					$image_url = wp_get_attachment_thumb_url((int)$value);
				}
				if ($is_edit) {
					?>
					<tr class="form-field">
						<th scope="row"><label for="reci_sphere_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
						<td>
							<div id="reci_sphere_<?php echo esc_attr($key); ?>-preview" style="margin-bottom:10px;">
								<?php if ($image_url) : ?>
									<img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:150px;max-height:150px;border-radius:4px;display:block;" />
								<?php endif; ?>
							</div>
							<input type="hidden" id="reci_sphere_<?php echo esc_attr($key); ?>" name="reci_sphere_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr((string)$value); ?>" />
							<button type="button" class="button reci-image-picker-button" data-target="reci_sphere_<?php echo esc_attr($key); ?>"><?php esc_html_e('Select Image', 'reci-media-hub'); ?></button>
							<button type="button" class="button reci-image-picker-remove" style="<?php echo $value ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove', 'reci-media-hub'); ?></button>
							<p class="description"><?php echo esc_html($help); ?></p>
						</td>
					</tr>
					<?php
				} else {
					?>
					<div class="form-field term-group">
						<label for="reci_sphere_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
						<div id="reci_sphere_<?php echo esc_attr($key); ?>-preview" style="margin-bottom:10px;"></div>
						<input type="hidden" id="reci_sphere_<?php echo esc_attr($key); ?>" name="reci_sphere_<?php echo esc_attr($key); ?>" value="" />
						<button type="button" class="button reci-image-picker-button" data-target="reci_sphere_<?php echo esc_attr($key); ?>"><?php esc_html_e('Select Image', 'reci-media-hub'); ?></button>
						<button type="button" class="button reci-image-picker-remove" style="display:none;"><?php esc_html_e('Remove', 'reci-media-hub'); ?></button>
						<p><?php echo esc_html($help); ?></p>
					</div>
					<?php
				}
				continue;
			}

			$is_textarea = $field['type'] === 'textarea';

			if ($is_edit) {
				?>
				<tr class="form-field">
					<th scope="row"><label for="reci_sphere_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
					<td>
						<?php if ($is_textarea) : ?>
							<textarea id="reci_sphere_<?php echo esc_attr($key); ?>" name="reci_sphere_<?php echo esc_attr($key); ?>" rows="4" class="large-text"><?php echo esc_textarea($value); ?></textarea>
						<?php else : ?>
							<input type="text" id="reci_sphere_<?php echo esc_attr($key); ?>" name="reci_sphere_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" />
						<?php endif; ?>
						<p class="description"><?php echo esc_html($help); ?></p>
					</td>
				</tr>
				<?php
			} else {
				?>
				<div class="form-field term-group">
					<label for="reci_sphere_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
					<?php if ($is_textarea) : ?>
						<textarea id="reci_sphere_<?php echo esc_attr($key); ?>" name="reci_sphere_<?php echo esc_attr($key); ?>" rows="4" class="large-text"><?php echo esc_textarea($value); ?></textarea>
					<?php else : ?>
						<input type="text" id="reci_sphere_<?php echo esc_attr($key); ?>" name="reci_sphere_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" />
					<?php endif; ?>
					<p><?php echo esc_html($help); ?></p>
				</div>
				<?php
			}
		}
	}
}

if (! function_exists('reci_media_hub_save_sphere_fields')) {
	/**
	 * Save custom sphere metadata.
	 *
	 * @param int $term_id Term ID.
	 */
	function reci_media_hub_save_sphere_fields(int $term_id): void {
		if ($term_id <= 0) {
			return;
		}

		$map = [
			'awareness'      => ['meta' => 'reci_sphere_awareness', 'sanitize' => 'sanitize_text_field'],
			'action'         => ['meta' => 'reci_sphere_action', 'sanitize' => 'sanitize_text_field'],
			'num'            => ['meta' => 'reci_sphere_num', 'sanitize' => 'sanitize_text_field'],
			'color'          => ['meta' => 'reci_sphere_color', 'sanitize' => 'sanitize_text_field'],
			'gradient'       => ['meta' => 'reci_sphere_gradient', 'sanitize' => 'sanitize_text_field'],
			'desc'           => ['meta' => 'reci_sphere_desc', 'sanitize' => 'sanitize_textarea_field'],
			'guideQuestions' => ['meta' => 'reci_sphere_guide_questions', 'sanitize' => 'sanitize_textarea_field'],
			'exampleTopics'  => ['meta' => 'reci_sphere_example_topics', 'sanitize' => 'sanitize_textarea_field'],
			'contentImageId' => ['meta' => 'reci_sphere_content_image_id', 'sanitize' => 'absint'],
		];

		foreach ($map as $field => $config) {
			$raw = $_POST['reci_sphere_' . $field] ?? '';
			$value = is_string($raw) ? call_user_func($config['sanitize'], wp_unslash($raw)) : '';
			if ($value !== '') {
				update_term_meta($term_id, $config['meta'], $value);
			} else {
				delete_term_meta($term_id, $config['meta']);
			}
		}
	}
}

if (! function_exists('reci_media_hub_get_submission_spheres')) {
	/**
	 * Read configured spheres from taxonomy for submission UI.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	function reci_media_hub_get_submission_spheres(): array {
		$terms = get_terms([
			'taxonomy'   => 'reci_sphere',
			'hide_empty' => false,
		]);

		if (is_wp_error($terms) || ! is_array($terms) || empty($terms)) {
			$fallback = reci_media_hub_default_spheres();
			foreach ($fallback as $index => $sphere) {
				$fallback[$index]['id'] = $index + 1;
			}
			return $fallback;
		}

		$spheres = [];
		foreach ($terms as $term) {
			if (! $term instanceof WP_Term) {
				continue;
			}

			$default = reci_media_hub_get_sphere_default_by_slug($term->slug) ?? [];
			$num_raw = (string) get_term_meta($term->term_id, 'reci_sphere_num', true);
			$awareness = (string) get_term_meta($term->term_id, 'reci_sphere_awareness', true);
			$action = (string) get_term_meta($term->term_id, 'reci_sphere_action', true);
			$color = (string) get_term_meta($term->term_id, 'reci_sphere_color', true);
			$gradient = (string) get_term_meta($term->term_id, 'reci_sphere_gradient', true);
			$desc = (string) get_term_meta($term->term_id, 'reci_sphere_desc', true);
			$guide_questions_raw = (string) get_term_meta($term->term_id, 'reci_sphere_guide_questions', true);
			$example_topics = (string) get_term_meta($term->term_id, 'reci_sphere_example_topics', true);
			$image_id = (int) get_term_meta($term->term_id, 'reci_sphere_content_image_id', true);
			$image_url = '';
			if ($image_id > 0) {
				$image_url = wp_get_attachment_url($image_id);
			} else {
				$filename = (string) ($default['image_file'] ?? '');
				if ($filename !== '') {
					$image_url = get_template_directory_uri() . '/assets/images/site/reci-spheres/' . $filename;
				}
			}

			$guide_questions_lines = preg_split("/\\r\\n|\\r|\\n/", $guide_questions_raw) ?: [];
			$guide_questions = array_values(array_filter(array_map('trim', $guide_questions_lines), static function (string $line): bool {
				return $line !== '';
			}));

			$num = $num_raw !== '' ? $num_raw : (string) ($default['num'] ?? '');
			$awareness = $awareness !== '' ? $awareness : (string) ($default['awareness'] ?? $term->name);
			$action = $action !== '' ? $action : (string) ($default['action'] ?? '');
			$color = $color !== '' ? $color : (string) ($default['color'] ?? '#9B4D3A');
			$gradient = $gradient !== '' ? $gradient : (string) ($default['gradient'] ?? ('linear-gradient(135deg, ' . $color . ', ' . $color . ')'));
			$desc = $desc !== '' ? $desc : (string) ($default['desc'] ?? $term->description);
			$example_topics = $example_topics !== '' ? $example_topics : (string) ($default['exampleTopics'] ?? '');
			if (empty($guide_questions) && isset($default['guideQuestions']) && is_array($default['guideQuestions'])) {
				$guide_questions = array_values(array_filter(array_map(static function ($item): string {
					return is_string($item) ? trim($item) : '';
				}, $default['guideQuestions']), static function (string $line): bool {
					return $line !== '';
				}));
			}

			$spheres[] = [
				'id'            => (int) $term->term_id,
				'termId'        => (int) $term->term_id,
				'termSlug'      => (string) $term->slug,
				'num'           => $num !== '' ? $num : sprintf('%02d', count($spheres) + 1),
				'awareness'     => $awareness,
				'action'        => $action,
				'color'         => $color,
				'gradient'      => $gradient,
				'desc'          => $desc,
				'guideQuestions' => $guide_questions,
				'exampleTopics' => $example_topics,
				'content_image_url' => $image_url,
			];
		}

		usort($spheres, static function (array $a, array $b): int {
			$num_a = (string) ($a['num'] ?? '');
			$num_b = (string) ($b['num'] ?? '');
			return strcmp($num_a, $num_b);
		});

		return $spheres;
	}
}

if (! function_exists('reci_media_hub_get_taxonomy_term_names')) {
	/**
	 * Preferred sort order for submission taxonomy labels.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_get_taxonomy_preferred_order(string $taxonomy): array {
		if ($taxonomy === 'reci_practice_focus') {
			return reci_media_hub_default_practice_focus_terms();
		}
		if ($taxonomy === 'reci_target_audience') {
			return reci_media_hub_default_target_audience_terms();
		}
		return [];
	}
}

if (! function_exists('reci_media_hub_sort_taxonomy_term_labels')) {
	/**
	 * Sort taxonomy labels using preferred order with alpha fallback.
	 *
	 * @param array<int,string> $names    Labels to sort.
	 * @param array<int,string> $preferred Preferred order labels.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_sort_taxonomy_term_labels(array $names, array $preferred): array {
		if (empty($preferred)) {
			sort($names);
			return $names;
		}

		$order_map = [];
		foreach (array_values($preferred) as $index => $label) {
			$order_map[$label] = $index;
		}

		usort($names, static function (string $a, string $b) use ($order_map): int {
			$a_rank = $order_map[$a] ?? PHP_INT_MAX;
			$b_rank = $order_map[$b] ?? PHP_INT_MAX;
			if ($a_rank === $b_rank) {
				return strcasecmp($a, $b);
			}
			return $a_rank <=> $b_rank;
		});

		return $names;
	}
}

if (! function_exists('reci_media_hub_get_taxonomy_terms_for_submission')) {
	/**
	 * Get taxonomy terms as id/name/slug option objects for form mapping.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	function reci_media_hub_get_taxonomy_terms_for_submission(string $taxonomy): array {
		$terms = get_terms([
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		]);

		if (is_wp_error($terms) || ! is_array($terms)) {
			return [];
		}

		$options = [];
		foreach ($terms as $term) {
			if (! $term instanceof WP_Term) {
				continue;
			}
			$name = trim((string) $term->name);
			if ($name === '') {
				continue;
			}
			$options[] = [
				'id'   => (int) $term->term_id,
				'name' => $name,
				'slug' => (string) $term->slug,
			];
		}

		$preferred = reci_media_hub_get_taxonomy_preferred_order($taxonomy);
		if (empty($preferred)) {
			usort($options, static function (array $a, array $b): int {
				return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
			});
			return $options;
		}

		$order_map = [];
		foreach (array_values($preferred) as $index => $label) {
			$order_map[$label] = $index;
		}

		usort($options, static function (array $a, array $b) use ($order_map): int {
			$a_name = (string) ($a['name'] ?? '');
			$b_name = (string) ($b['name'] ?? '');
			$a_rank = $order_map[$a_name] ?? PHP_INT_MAX;
			$b_rank = $order_map[$b_name] ?? PHP_INT_MAX;
			if ($a_rank === $b_rank) {
				return strcasecmp($a_name, $b_name);
			}
			return $a_rank <=> $b_rank;
		});

		return $options;
	}
}

if (! function_exists('reci_media_hub_get_taxonomy_term_names')) {
	/**
	 * Get all term names for a taxonomy.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array<int,string>
	 */
	function reci_media_hub_get_taxonomy_term_names(string $taxonomy): array {
		$terms = get_terms([
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		]);

		if (is_wp_error($terms) || ! is_array($terms)) {
			return [];
		}

		$names = [];
		foreach ($terms as $term) {
			if ($term instanceof WP_Term) {
				$names[] = $term->name;
			}
		}

		$names = array_values(array_unique(array_filter(array_map('trim', $names), static function (string $name): bool {
			return $name !== '';
		})));
		return reci_media_hub_sort_taxonomy_term_labels($names, reci_media_hub_get_taxonomy_preferred_order($taxonomy));
	}
}

add_action('init', 'reci_media_hub_register_taxonomies');
add_action('admin_enqueue_scripts', function($hook) {
	if ($hook !== 'edit-tags.php' && $hook !== 'term.php') return;
	$screen = get_current_screen();
	if (!$screen || $screen->taxonomy !== 'reci_sphere') return;
	wp_enqueue_media();
});

add_action('admin_footer', function() {
	$screen = get_current_screen();
	if (!$screen || $screen->taxonomy !== 'reci_sphere') return;
	?>
	<script>
	(function($) {
		function initImagePicker() {
			$('.reci-image-picker-button').on('click', function(e) {
				e.preventDefault();
				var btn = $(this);
				var target = $('#' + btn.data('target'));
				var preview = $('#' + btn.data('target') + '-preview');
				var removeBtn = btn.siblings('.reci-image-picker-remove');
				var frame = wp.media({
					title: '<?php echo esc_js(__('Select Sphere Content Image', 'reci-media-hub')); ?>',
					library: { type: 'image' },
					button: { text: '<?php echo esc_js(__('Use Image', 'reci-media-hub')); ?>' },
					multiple: false
				});
				frame.on('select', function() {
					var attachment = frame.state().get('selection').first().toJSON();
					target.val(attachment.id);
					var url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
					preview.show().html('<img src="' + url + '" alt="" style="max-width:150px;max-height:150px;border-radius:4px;display:block;" />');
					removeBtn.show();
				});
				frame.open();
			});

			$('.reci-image-picker-remove').on('click', function(e) {
				e.preventDefault();
				var btn = $(this);
				btn.siblings('input[type="hidden"]').val('');
				btn.siblings('div[id$="-preview"]').hide().empty();
				btn.hide();
			});
		}
		$(document).ready(initImagePicker);
	})(jQuery);
	</script>
	<?php
});

add_action('init', 'reci_media_hub_seed_default_spheres', 20);
add_action('init', 'reci_media_hub_seed_default_taxonomy_terms', 21);
add_action('reci_sphere_add_form_fields', 'reci_media_hub_render_sphere_fields');
add_action('reci_sphere_edit_form_fields', 'reci_media_hub_render_sphere_fields');
add_action('created_reci_sphere', 'reci_media_hub_save_sphere_fields');
add_action('edited_reci_sphere', 'reci_media_hub_save_sphere_fields');
