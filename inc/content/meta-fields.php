<?php

/**
 * Register custom meta fields and admin metaboxes for RECI content types.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_meta_definitions')) {
	/**
	 * Meta field definitions grouped by post type.
	 */
	function reci_media_hub_meta_definitions(): array
	{
		return [
			'post'    => [
				'_post_read_time_label' => ['type' => 'string', 'single' => true, 'default' => '3 mins'],
				'_post_featured_rank'   => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_post_source_name'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_post_source_url'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_post_canonical_url'   => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
			'reci_event'      => [
				'_reci_event_start_date'        => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_end_date'          => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_start_time'        => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_end_time'          => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_timezone'          => ['type' => 'string', 'single' => true, 'default' => 'UTC'],
				'_reci_event_location_name'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_location_address'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_is_virtual'        => ['type' => 'boolean', 'single' => true, 'default' => false],
				'_reci_event_registration_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_cta_label'         => ['type' => 'string', 'single' => true, 'default' => 'Register'],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
			'reci_podcast'    => [
				'_reci_podcast_audio_url'       => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_podcast_video_url'		=> ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_podcast_duration_label'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_podcast_duration_secs'   => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_podcast_episode_number'  => ['type' => 'integer', 'single' => true, 'default' => 1],
				'_reci_podcast_season_number'   => ['type' => 'integer', 'single' => true, 'default' => 1],
				'_reci_podcast_transcript_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_podcast_spotify_url'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_podcast_apple_url'       => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
			'reci_video'      => [
				'_reci_video_url'             => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_video_platform'        => ['type' => 'string', 'single' => true, 'default' => 'youtube'],
				'_reci_video_duration_label'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_video_duration_secs'   => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_video_external_id'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
			'reci_reflection' => [
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
		'reci_quote'      => [
			'_reci_quote_text'                 => ['type' => 'string', 'single' => true, 'default' => ''],
			'_reci_quote_author_name'          => ['type' => 'string', 'single' => true, 'default' => ''],
			'_reci_quote_author_title'         => ['type' => 'string', 'single' => true, 'default' => ''],
			'_reci_display_author_profile_id'  => ['type' => 'integer', 'single' => true, 'default' => 0],
		],
			'reci_assessment' => [
				'_reci_assessment_type'           => ['type' => 'string', 'single' => true, 'default' => 'quiz'],
				'_reci_assessment_intro'          => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_assessment_estimated_time' => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_assessment_instructions'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_assessment_questions'      => ['type' => 'string', 'single' => true, 'default' => '[]'],
				'_reci_assessment_result_ranges'  => ['type' => 'string', 'single' => true, 'default' => '[]'],
				'_reci_assessment_completion_title' => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_assessment_completion_message' => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
			'reci_course'     => [
				'_reci_course_level'           => ['type' => 'string', 'single' => true, 'default' => 'beginner'],
				'_reci_course_duration_weeks'  => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_course_format'          => ['type' => 'string', 'single' => true, 'default' => 'self_paced'],
				'_reci_course_start_date'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_course_fee_label'       => ['type' => 'string', 'single' => true, 'default' => 'Free'],
				'_reci_course_enrollment_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_course_lessons'         => ['type' => 'string', 'single' => true, 'default' => '[]'],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
			'reci_author'     => [
				'_reci_author_profile_user_id'   => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_author_profile_title'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_email'             => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_organization'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_department'        => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_pitt_affiliation'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_website'           => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_social_links'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_cv_id'             => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_author_highlighted_links' => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_source_url'        => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_author_import_slug'       => ['type' => 'string', 'single' => true, 'default' => ''],
			],
			'reci_partner'    => [
				'_reci_partner_url' => ['type' => 'string', 'single' => true, 'default' => ''],
			],
			'reci_testimonial' => [
				'_reci_testimonial_text'         => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_testimonial_full_name'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_testimonial_role'          => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_testimonial_organization'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_display_author_profile_id' => ['type' => 'integer', 'single' => true, 'default' => 0],
			],
		];
	}
}

if (! function_exists('reci_media_hub_sanitize_meta_value')) {
	/**
	 * Sanitize meta values by registered primitive type.
	 *
	 * @param mixed  $value Raw value.
	 * @param string $type  Meta type.
	 * @return mixed
	 */
	function reci_media_hub_sanitize_meta_value($value, string $type)
	{
		switch ($type) {
			case 'boolean':
				return ! empty($value);
			case 'integer':
				return (int) $value;
			case 'string':
			default:
				return sanitize_text_field((string) $value);
		}
	}
}

if (! function_exists('reci_media_hub_register_meta_fields')) {
	/**
	 * Register post meta for all configured post types.
	 */
	function reci_media_hub_register_meta_fields(): void
	{
		$definitions = reci_media_hub_meta_definitions();

		foreach ($definitions as $post_type => $fields) {
			foreach ($fields as $key => $config) {
				register_post_meta(
					$post_type,
					$key,
					[
						'type'              => $config['type'],
						'single'            => $config['single'],
						'default'           => $config['default'],
						'show_in_rest'      => true,
						'sanitize_callback' => function ($value) use ($config) {
							return reci_media_hub_sanitize_meta_value($value, $config['type']);
						},
						'auth_callback'     => function () {
							return current_user_can('edit_posts');
						},
					]
				);
			}
		}
	}
}

add_action('init', 'reci_media_hub_register_meta_fields');

if (! function_exists('reci_media_hub_add_meta_boxes')) {
	/**
	 * Add admin metaboxes.
	 */
	function reci_media_hub_add_meta_boxes(): void
	{
		add_meta_box('reci-article-details', __('Article Details', 'reci-media-hub'), 'reci_media_hub_render_article_metabox', 'post', 'normal', 'high');
		add_meta_box('reci-event-details', __('Event Details', 'reci-media-hub'), 'reci_media_hub_render_event_metabox', 'reci_event', 'normal', 'high');
		add_meta_box('reci-podcast-details', __('Podcast Details', 'reci-media-hub'), 'reci_media_hub_render_podcast_metabox', 'reci_podcast', 'normal', 'high');
		add_meta_box('reci-video-details', __('Video Details', 'reci-media-hub'), 'reci_media_hub_render_video_metabox', 'reci_video', 'normal', 'high');
		add_meta_box('reci-quote-details', __('Quote Details', 'reci-media-hub'), 'reci_media_hub_render_quote_metabox', 'reci_quote', 'normal', 'high');
		add_meta_box('reci-assessment-details', __('Quiz Details', 'reci-media-hub'), 'reci_media_hub_render_assessment_metabox', 'reci_assessment', 'normal', 'high');
		add_meta_box('reci-course-details', __('Course Details', 'reci-media-hub'), 'reci_media_hub_render_course_metabox', 'reci_course', 'normal', 'high');
		add_meta_box('reci-author-profile-details', __('Author Profile Details', 'reci-media-hub'), 'reci_media_hub_render_author_profile_metabox', 'reci_author', 'normal', 'high');
		add_meta_box('reci-partner-details', __('Partner Details', 'reci-media-hub'), 'reci_media_hub_render_partner_metabox', 'reci_partner', 'normal', 'high');
		add_meta_box('reci-testimonial-details', __('Testimonial Details', 'reci-media-hub'), 'reci_media_hub_render_testimonial_metabox', 'reci_testimonial', 'normal', 'high');

		foreach (reci_media_hub_display_author_post_types() as $post_type) {
			add_meta_box('reci-display-author', __('Display Author', 'reci-media-hub'), 'reci_media_hub_render_display_author_metabox', $post_type, 'side', 'high');
		}
	}
}

add_action('add_meta_boxes', 'reci_media_hub_add_meta_boxes');

if (! function_exists('reci_media_hub_remove_quote_metaboxes')) {
	add_action('do_meta_boxes', 'reci_media_hub_remove_quote_metaboxes');
	function reci_media_hub_remove_quote_metaboxes(): void {
		remove_meta_box('postimagediv', 'reci_quote', 'side');
	}
}

if (! function_exists('reci_media_hub_quote_featured_image_label')) {
	add_filter('admin_post_thumbnail_html', 'reci_media_hub_quote_featured_image_label', 10, 2);
	function reci_media_hub_quote_featured_image_label(string $html, int $post_id): string {
		if (get_post_type($post_id) !== 'reci_quote') {
			return $html;
		}
		return str_replace(
			['Featured Image', 'Set featured image', 'Remove featured image'],
			["Author's Image", 'Set author image', 'Remove author image'],
			$html
		);
	}
}

if (! function_exists('reci_media_hub_quote_featured_image_title')) {
	add_filter('admin_post_thumbnail_title', 'reci_media_hub_quote_featured_image_title', 10, 2);
	function reci_media_hub_quote_featured_image_title(string $title, int $post_id): string {
		if (get_post_type($post_id) !== 'reci_quote') {
			return $title;
		}
		return __('Author\'s Image', 'reci-media-hub');
	}
}

if (! function_exists('reci_media_hub_metabox_styles')) {
	/**
	 * Lightweight shared styles for metabox form rows.
	 */
	function reci_media_hub_metabox_styles(): void
	{
?>
		<style>
			.reci-meta-grid {
				display: grid;
				gap: 12px;
				grid-template-columns: repeat(2, minmax(0, 1fr));
				margin-top: 12px;
			}

			.reci-meta-row {
				display: flex;
				flex-direction: column;
				gap: 6px;
			}

			.reci-meta-row--full {
				grid-column: 1/-1;
			}

			.reci-meta-row input[type="text"],
			.reci-meta-row input[type="url"],
			.reci-meta-row input[type="number"],
			.reci-meta-row input[type="date"],
			.reci-meta-row input[type="time"],
			.reci-meta-row select,
			.reci-meta-row textarea {
				width: 100%;
			}

			.reci-repeater-list {
				display: flex;
				flex-direction: column;
				gap: 10px;
				margin-top: 10px;
			}

			.reci-repeater-item {
				border: 1px solid #dcdcde;
				border-radius: 6px;
				padding: 10px;
				background: #fff;
			}

			.reci-repeater-actions {
				margin-top: 10px;
				display: flex;
				gap: 8px;
			}
		</style>
		<script>
		(function($) {
			function initImagePicker() {
				$('.reci-image-picker-button').each(function() {
					var btn = $(this);
					var target = $('#' + btn.data('target'));
					var preview = $('#' + btn.data('target') + '-preview');
					var removeBtn = btn.siblings('.reci-image-picker-remove');
					var frame;

					btn.on('click', function(e) {
						e.preventDefault();
						if (frame) {
							frame.open();
							return;
						}
						frame = wp.media({
							title: '<?php echo esc_js(__('Select Author Image', 'reci-media-hub')); ?>',
							library: { type: 'image' },
							button: { text: '<?php echo esc_js(__('Use Image', 'reci-media-hub')); ?>' },
							multiple: false
						});
						frame.on('select', function() {
							var attachment = frame.state().get('selection').first().toJSON();
							target.val(attachment.id);
							preview.show().html('<img src="' + attachment.sizes.thumbnail.url + '" alt="" style="max-width:150px;max-height:150px;border-radius:4px;display:block;" />');
							removeBtn.show();
						});
						frame.open();
					});

					removeBtn.on('click', function(e) {
						e.preventDefault();
						target.val('');
						preview.hide().empty();
						removeBtn.hide();
					});
				});
			}
			$(document).ready(initImagePicker);
		})(jQuery);
		</script>
	<?php
	}
}

if (! function_exists('reci_get_quote_author_data')) {
	function reci_get_quote_author_data(int $post_id): array {
		$name = (string) get_post_meta($post_id, '_reci_quote_author_name', true);
		$title = (string) get_post_meta($post_id, '_reci_quote_author_title', true);
		$image_url = get_the_post_thumbnail_url($post_id, 'thumbnail') ?: '';

		$profile_id = max(0, (int) get_post_meta($post_id, '_reci_display_author_profile_id', true));
		if ($profile_id > 0) {
			$profile = reci_media_hub_get_author_profile_data($profile_id);
			if (! empty($profile)) {
				$name = $profile['name'];
				$title = $profile['title'];
				$image_url = $profile['image_url'] ?: $image_url;
			}
		}

		return [
			'name'       => $name,
			'title'      => $title,
			'image_url'  => $image_url,
		];
	}
}

if (! function_exists('reci_get_author_avatar_url')) {
	function reci_get_author_avatar_url(int $post_id): string {
		$data = reci_get_quote_author_data($post_id);
		return $data['image_url'];
	}
}

if (! function_exists('reci_media_hub_enqueue_admin_assets')) {
	add_action('admin_enqueue_scripts', 'reci_media_hub_enqueue_admin_assets');
	function reci_media_hub_enqueue_admin_assets(string $hook): void {
		if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
			return;
		}
		$screen = get_current_screen();
		if (! $screen || $screen->post_type !== 'reci_quote') {
			return;
		}
		wp_enqueue_media();
	}
}

if (! function_exists('reci_media_hub_render_field')) {
	/**
	 * Render a text-like field row.
	 */
	function reci_media_hub_render_field(
		string $name,
		string $label,
		string $value,
		string $type = 'text',
		string $class = ''
	): void {
	?>
		<div class="reci-meta-row <?php echo esc_attr($class); ?>">
			<label for="<?php echo esc_attr($name); ?>"><strong><?php echo esc_html($label); ?></strong></label>
			<input type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" />
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_textarea_field')) {
	/**
	 * Render a textarea field row.
	 */
	function reci_media_hub_render_textarea_field(string $name, string $label, string $value, string $class = '', int $rows = 4): void
	{
	?>
		<div class="reci-meta-row <?php echo esc_attr($class); ?>">
			<label for="<?php echo esc_attr($name); ?>"><strong><?php echo esc_html($label); ?></strong></label>
			<textarea id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" rows="<?php echo esc_attr((string) $rows); ?>"><?php echo esc_textarea($value); ?></textarea>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_select_field')) {
	/**
	 * Render a select field row.
	 *
	 * @param array<int|string,string> $options Options keyed by value.
	 */
	function reci_media_hub_render_select_field(string $name, string $label, string $value, array $options, string $class = ''): void
	{
	?>
		<div class="reci-meta-row <?php echo esc_attr($class); ?>">
			<label for="<?php echo esc_attr($name); ?>"><strong><?php echo esc_html($label); ?></strong></label>
			<select id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>">
				<?php foreach ($options as $option_value => $option_label) : ?>
					<option value="<?php echo esc_attr((string) $option_value); ?>" <?php selected($value, (string) $option_value); ?>><?php echo esc_html($option_label); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_enqueue_admin_assets')) {
	add_action('admin_enqueue_scripts', 'reci_media_hub_enqueue_admin_assets');
	function reci_media_hub_enqueue_admin_assets(string $hook): void {
		// Reserved for admin asset enqueuing.
	}
}

if (! function_exists('reci_media_hub_render_article_metabox')) {
	function reci_media_hub_render_article_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$read_time = (string) get_post_meta($post->ID, '_post_read_time_label', true);
		$rank      = (string) get_post_meta($post->ID, '_post_featured_rank', true);
		$source    = (string) get_post_meta($post->ID, '_post_source_name', true);
		$source_url = (string) get_post_meta($post->ID, '_post_source_url', true);
		$canonical = (string) get_post_meta($post->ID, '_post_canonical_url', true);
	?>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_field('_post_read_time_label', __('Read Time Label', 'reci-media-hub'), $read_time); ?>
			<?php reci_media_hub_render_field('_post_featured_rank', __('Featured Rank', 'reci-media-hub'), $rank, 'number'); ?>
			<?php reci_media_hub_render_field('_post_source_name', __('Source Name', 'reci-media-hub'), $source); ?>
			<?php reci_media_hub_render_field('_post_source_url', __('Source URL', 'reci-media-hub'), $source_url, 'url'); ?>
			<?php reci_media_hub_render_field('_post_canonical_url', __('Canonical URL', 'reci-media-hub'), $canonical, 'url', 'reci-meta-row--full'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_event_metabox')) {
	function reci_media_hub_render_event_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$start_date       = (string) get_post_meta($post->ID, '_reci_event_start_date', true);
		$end_date         = (string) get_post_meta($post->ID, '_reci_event_end_date', true);
		$start_time       = (string) get_post_meta($post->ID, '_reci_event_start_time', true);
		$end_time         = (string) get_post_meta($post->ID, '_reci_event_end_time', true);
		$timezone         = (string) get_post_meta($post->ID, '_reci_event_timezone', true);
		$location_name    = (string) get_post_meta($post->ID, '_reci_event_location_name', true);
		$location_address = (string) get_post_meta($post->ID, '_reci_event_location_address', true);
		$is_virtual       = (bool) get_post_meta($post->ID, '_reci_event_is_virtual', true);
		$registration_url = (string) get_post_meta($post->ID, '_reci_event_registration_url', true);
		$cta_label        = (string) get_post_meta($post->ID, '_reci_event_cta_label', true);
	?>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_field('_reci_event_start_date', __('Start Date', 'reci-media-hub'), $start_date, 'date'); ?>
			<?php reci_media_hub_render_field('_reci_event_end_date', __('End Date', 'reci-media-hub'), $end_date, 'date'); ?>
			<?php reci_media_hub_render_field('_reci_event_start_time', __('Start Time', 'reci-media-hub'), $start_time, 'time'); ?>
			<?php reci_media_hub_render_field('_reci_event_end_time', __('End Time', 'reci-media-hub'), $end_time, 'time'); ?>
			<?php reci_media_hub_render_field('_reci_event_timezone', __('Timezone', 'reci-media-hub'), $timezone); ?>
			<?php reci_media_hub_render_field('_reci_event_location_name', __('Location Name', 'reci-media-hub'), $location_name); ?>
			<?php reci_media_hub_render_field('_reci_event_location_address', __('Location Address', 'reci-media-hub'), $location_address, 'text', 'reci-meta-row--full'); ?>
			<div class="reci-meta-row">
				<label for="_reci_event_is_virtual">
					<input type="checkbox" id="_reci_event_is_virtual" name="_reci_event_is_virtual" value="1" <?php checked($is_virtual); ?> />
					<?php esc_html_e('Virtual Event', 'reci-media-hub'); ?>
				</label>
			</div>
			<?php reci_media_hub_render_field('_reci_event_registration_url', __('Registration URL', 'reci-media-hub'), $registration_url, 'url', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_event_cta_label', __('CTA Label', 'reci-media-hub'), $cta_label ?: 'Register'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_podcast_metabox')) {
	function reci_media_hub_render_podcast_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$audio_url      = (string) get_post_meta($post->ID, '_reci_podcast_audio_url', true);
		$video_url      = (string) get_post_meta($post->ID, '_reci_podcast_video_url', true);
		$duration_label = (string) get_post_meta($post->ID, '_reci_podcast_duration_label', true);
		$duration_secs  = (string) get_post_meta($post->ID, '_reci_podcast_duration_secs', true);
		$episode_number = (string) get_post_meta($post->ID, '_reci_podcast_episode_number', true);
		$season_number  = (string) get_post_meta($post->ID, '_reci_podcast_season_number', true);
		$transcript_url = (string) get_post_meta($post->ID, '_reci_podcast_transcript_url', true);
		$spotify_url    = (string) get_post_meta($post->ID, '_reci_podcast_spotify_url', true);
		$apple_url      = (string) get_post_meta($post->ID, '_reci_podcast_apple_url', true);
	?>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_field('_reci_podcast_audio_url', __('Audio URL', 'reci-media-hub'), $audio_url, 'url', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_video_url', __('Video URL (YouTube / Vimeo / direct)', 'reci-media-hub'), $video_url, 'url', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_duration_label', __('Duration Label', 'reci-media-hub'), $duration_label); ?>
			<?php reci_media_hub_render_field('_reci_podcast_duration_secs', __('Duration (seconds)', 'reci-media-hub'), $duration_secs, 'number'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_episode_number', __('Episode Number', 'reci-media-hub'), $episode_number ?: '1', 'number'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_season_number', __('Season Number', 'reci-media-hub'), $season_number ?: '1', 'number'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_transcript_url', __('Transcript URL', 'reci-media-hub'), $transcript_url, 'url', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_spotify_url', __('Spotify URL', 'reci-media-hub'), $spotify_url, 'url'); ?>
			<?php reci_media_hub_render_field('_reci_podcast_apple_url', __('Apple URL', 'reci-media-hub'), $apple_url, 'url'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_video_metabox')) {
	function reci_media_hub_render_video_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$video_url      = (string) get_post_meta($post->ID, '_reci_video_url', true);
		$platform       = (string) get_post_meta($post->ID, '_reci_video_platform', true);
		$duration_label = (string) get_post_meta($post->ID, '_reci_video_duration_label', true);
		$duration_secs  = (string) get_post_meta($post->ID, '_reci_video_duration_secs', true);
		$external_id    = (string) get_post_meta($post->ID, '_reci_video_external_id', true);
	?>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_field('_reci_video_url', __('Video URL', 'reci-media-hub'), $video_url, 'url', 'reci-meta-row--full'); ?>
			<div class="reci-meta-row">
				<label for="_reci_video_platform"><strong><?php esc_html_e('Platform', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_video_platform" name="_reci_video_platform">
					<?php foreach (['youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'self_hosted' => 'Self Hosted', 'other' => 'Other'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($platform, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_field('_reci_video_external_id', __('External ID (optional)', 'reci-media-hub'), $external_id); ?>
			<?php reci_media_hub_render_field('_reci_video_duration_label', __('Duration Label', 'reci-media-hub'), $duration_label); ?>
			<?php reci_media_hub_render_field('_reci_video_duration_secs', __('Duration (seconds)', 'reci-media-hub'), $duration_secs, 'number'); ?>
		</div>
	<?php
	}
}

	if (! function_exists('reci_media_hub_render_quote_metabox')) {
		function reci_media_hub_render_quote_metabox(WP_Post $post): void
		{
			wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
			reci_media_hub_metabox_styles();

			$quote_text  = (string) get_post_meta($post->ID, '_reci_quote_text', true);
			$author_name = (string) get_post_meta($post->ID, '_reci_quote_author_name', true);
			$author_role = (string) get_post_meta($post->ID, '_reci_quote_author_title', true);
		?>
			<div class="reci-meta-grid" style="grid-template-columns: 1fr;">
				<?php reci_media_hub_render_textarea_field('_reci_quote_text', __('Quote Text', 'reci-media-hub'), $quote_text, 'reci-meta-row--full', 5); ?>

				<div class="reci-meta-row reci-meta-row--full" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;border-top:1px solid #dcdcde;padding-top:16px;margin-top:8px;">
					<?php reci_media_hub_render_field('_reci_quote_author_name', __('Author Name', 'reci-media-hub'), $author_name); ?>
					<?php reci_media_hub_render_field('_reci_quote_author_title', __('Author Title', 'reci-media-hub'), $author_role); ?>
				</div>

			<div class="reci-meta-row reci-meta-row--full" style="border-top:1px solid #dcdcde;padding-top:16px;margin-top:8px;">
					<strong style="display:block;margin-bottom:8px;"><?php esc_html_e("Author's Image", 'reci-media-hub'); ?></strong>
					<?php echo _wp_post_thumbnail_html(get_post_thumbnail_id($post->ID), $post->ID); ?>
				</div>
			</div>
		<?php
		}
	}

if (! function_exists('reci_media_hub_render_assessment_metabox')) {
	function reci_media_hub_render_assessment_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$settings           = reci_media_hub_get_assessment_settings($post->ID);
		$type               = (string) $settings['type'];
		$intro              = (string) $settings['intro'];
		$estimated          = (string) $settings['estimated_time'];
		$completion_title   = (string) $settings['completion_title'];
		$completion_message = (string) $settings['completion_message'];
		$questions          = is_array($settings['questions']) ? $settings['questions'] : [];
		$instructions       = (string) $settings['instructions'];
		$sample_url         = reci_media_hub_get_assessment_csv_sample_url();
	?>
		<div class="reci-meta-grid">
			<div class="reci-meta-row">
				<label for="_reci_assessment_type"><strong><?php esc_html_e('Quiz Type', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_assessment_type" name="_reci_assessment_type">
					<?php foreach (['survey' => 'Survey', 'quiz' => 'Quiz', 'checklist' => 'Checklist'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_field('_reci_assessment_estimated_time', __('Estimated Time', 'reci-media-hub'), $estimated); ?>
			<?php reci_media_hub_render_textarea_field('_reci_assessment_intro', __('Intro Text', 'reci-media-hub'), $intro, 'reci-meta-row--full', 3); ?>
			<?php reci_media_hub_render_field('_reci_assessment_completion_title', __('Completion Title', 'reci-media-hub'), $completion_title, 'text', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_textarea_field('_reci_assessment_completion_message', __('Completion Message', 'reci-media-hub'), $completion_message, 'reci-meta-row--full', 3); ?>
			<?php reci_media_hub_render_textarea_field('_reci_assessment_instructions', __('Instructions', 'reci-media-hub'), $instructions, 'reci-meta-row--full', 4); ?>
		</div>

		<div class="reci-meta-row reci-meta-row--full" style="margin-top:12px;">
			<label><strong><?php esc_html_e('CSV Import', 'reci-media-hub'); ?></strong></label>
			<input type="file" name="_reci_assessment_csv_import" accept=".csv" />
			<p class="description">
				<?php esc_html_e('Upload a CSV and save the quiz to replace the current questions. Use the sample file to match the expected column format.', 'reci-media-hub'); ?>
				<a href="<?php echo esc_url($sample_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Download sample CSV', 'reci-media-hub'); ?></a>
			</p>
		</div>

		<div class="reci-meta-row reci-meta-row--full" style="margin-top:12px;">
			<label><strong><?php esc_html_e('Questions', 'reci-media-hub'); ?></strong></label>
			<div id="reci-assessment-questions" class="reci-repeater-list">
				<?php foreach ($questions as $index => $question) : ?>
					<div class="reci-repeater-item">
						<div class="reci-meta-grid">
							<div class="reci-meta-row reci-meta-row--full">
								<label><?php esc_html_e('Question', 'reci-media-hub'); ?></label>
								<input type="text" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][prompt]" value="<?php echo esc_attr((string) ($question['prompt'] ?? '')); ?>" />
							</div>
							<div class="reci-meta-row">
								<label><?php esc_html_e('Type', 'reci-media-hub'); ?></label>
								<select class="reci-assessment-question-type" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][type]">
									<?php foreach (['scale' => 'Scale / Rating', 'single_choice' => 'Single Choice', 'multiple_choice' => 'Multiple Choice', 'text' => 'Short Text', 'textarea' => 'Long Text'] as $value => $label) : ?>
										<option value="<?php echo esc_attr($value); ?>" <?php selected((string) ($question['type'] ?? 'single_choice'), $value); ?>><?php echo esc_html($label); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="reci-meta-row" style="justify-content:flex-end;">
								<label style="margin-top:28px;">
									<input type="checkbox" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][required]" value="1" <?php checked(! empty($question['required'])); ?> />
									<?php esc_html_e('Required', 'reci-media-hub'); ?>
								</label>
							</div>
							<div class="reci-meta-row reci-meta-row--full">
								<label><?php esc_html_e('Help Text (optional)', 'reci-media-hub'); ?></label>
								<textarea name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][help_text]" rows="2"><?php echo esc_textarea((string) ($question['help_text'] ?? '')); ?></textarea>
							</div>
							<div class="reci-meta-row reci-meta-row--full reci-question-options" <?php if (! in_array((string) ($question['type'] ?? ''), ['single_choice', 'multiple_choice'], true)) : ?>style="display:none;"<?php endif; ?>>
								<label><?php esc_html_e('Options (one per line)', 'reci-media-hub'); ?></label>
								<textarea name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][options]" rows="3"><?php echo esc_textarea(implode("\n", is_array($question['options'] ?? null) ? $question['options'] : [])); ?></textarea>
							</div>
							<div class="reci-meta-row reci-meta-row--full reci-question-correct" <?php if (! in_array((string) ($question['type'] ?? ''), ['single_choice', 'multiple_choice'], true)) : ?>style="display:none;"<?php endif; ?>>
								<label><?php esc_html_e('Correct Answer(s) (one per line, must match an option)', 'reci-media-hub'); ?></label>
								<textarea name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][correct_answers]" rows="2"><?php echo esc_textarea(implode("\n", is_array($question['correct_answers'] ?? null) ? $question['correct_answers'] : [])); ?></textarea>
							</div>
							<div class="reci-meta-row reci-question-scale" <?php if ((string) ($question['type'] ?? '') !== 'scale') : ?>style="display:none;"<?php endif; ?>>
								<label><?php esc_html_e('Low Label', 'reci-media-hub'); ?></label>
								<input type="text" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][scale_min_label]" value="<?php echo esc_attr((string) ($question['scale_min_label'] ?? '')); ?>" />
							</div>
							<div class="reci-meta-row reci-question-scale" <?php if ((string) ($question['type'] ?? '') !== 'scale') : ?>style="display:none;"<?php endif; ?>>
								<label><?php esc_html_e('High Label', 'reci-media-hub'); ?></label>
								<input type="text" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][scale_max_label]" value="<?php echo esc_attr((string) ($question['scale_max_label'] ?? '')); ?>" />
							</div>
							<div class="reci-meta-row reci-question-scale" <?php if ((string) ($question['type'] ?? '') !== 'scale') : ?>style="display:none;"<?php endif; ?>>
								<label><?php esc_html_e('Scale Steps', 'reci-media-hub'); ?></label>
								<input type="number" min="2" max="10" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][scale_steps]" value="<?php echo esc_attr((string) max(2, (int) ($question['scale_steps'] ?? 5))); ?>" />
							</div>
						</div>
						<div class="reci-repeater-actions">
							<button type="button" class="button button-link-delete reci-remove-question"><?php esc_html_e('Remove', 'reci-media-hub'); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="reci-repeater-actions">
				<button type="button" class="button" id="reci-add-question"><?php esc_html_e('Add Question', 'reci-media-hub'); ?></button>
			</div>
		</div>

		<div class="reci-meta-row reci-meta-row--full" style="margin-top:24px; padding-top:24px; border-top:2px solid #dcdcde;">
			<label><strong><?php esc_html_e('Result Bands', 'reci-media-hub'); ?></strong></label>
			<p class="description"><?php esc_html_e('Define score ranges and feedback messages. For surveys, these are used for personalized recommendations.', 'reci-media-hub'); ?></p>
			<div id="reci-assessment-bands" class="reci-repeater-list">
				<?php foreach (($settings['result_ranges'] ?? []) as $index => $band) : ?>
					<div class="reci-repeater-item">
						<div class="reci-meta-grid">
							<div class="reci-meta-row">
								<label><?php esc_html_e('Label (e.g. Developing)', 'reci-media-hub'); ?></label>
								<input type="text" name="_reci_assessment_result_ranges[<?php echo esc_attr((string) $index); ?>][label]" value="<?php echo esc_attr((string) ($band['label'] ?? '')); ?>" />
							</div>
							<div class="reci-meta-row">
								<label><?php esc_html_e('Key (slug)', 'reci-media-hub'); ?></label>
								<input type="text" name="_reci_assessment_result_ranges[<?php echo esc_attr((string) $index); ?>][key]" value="<?php echo esc_attr((string) ($band['key'] ?? '')); ?>" />
							</div>
							<div class="reci-meta-row">
								<label><?php esc_html_e('Min %', 'reci-media-hub'); ?></label>
								<input type="number" min="0" max="100" name="_reci_assessment_result_ranges[<?php echo esc_attr((string) $index); ?>][min_percent]" value="<?php echo esc_attr((string) ($band['min_percent'] ?? 0)); ?>" />
							</div>
							<div class="reci-meta-row">
								<label><?php esc_html_e('Max %', 'reci-media-hub'); ?></label>
								<input type="number" min="0" max="100" name="_reci_assessment_result_ranges[<?php echo esc_attr((string) $index); ?>][max_percent]" value="<?php echo esc_attr((string) ($band['max_percent'] ?? 100)); ?>" />
							</div>
							<div class="reci-meta-row reci-meta-row--full">
								<label><?php esc_html_e('Feedback Message', 'reci-media-hub'); ?></label>
								<textarea name="_reci_assessment_result_ranges[<?php echo esc_attr((string) $index); ?>][message]" rows="3"><?php echo esc_textarea((string) ($band['message'] ?? '')); ?></textarea>
							</div>
							<div class="reci-meta-row reci-meta-row--full">
								<label><?php esc_html_e('Recommended Topic Slugs (one per line)', 'reci-media-hub'); ?></label>
								<textarea name="_reci_assessment_result_ranges[<?php echo esc_attr((string) $index); ?>][recommended_topics]" rows="2"><?php echo esc_textarea(implode("\n", (array)($band['recommended_topics'] ?? []))); ?></textarea>
							</div>
						</div>
						<div class="reci-repeater-actions">
							<button type="button" class="button button-link-delete reci-remove-band"><?php esc_html_e('Remove Band', 'reci-media-hub'); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="reci-repeater-actions">
				<button type="button" class="button" id="reci-add-band"><?php esc_html_e('Add Result Band', 'reci-media-hub'); ?></button>
			</div>
		</div>

		<script>
			(function() {
				// Questions Repeater
				const qWrap = document.getElementById('reci-assessment-questions');
				const qAdd = document.getElementById('reci-add-question');

				function syncQuestionType(item) {
					if (!item) return;
					const select = item.querySelector('.reci-assessment-question-type');
					if (!select) return;

					const type = select.value;
					const isChoice = (type === 'single_choice' || type === 'multiple_choice');
					
					const optRow = item.querySelector('.reci-question-options');
					const corRow = item.querySelector('.reci-question-correct');
					const scaRows = item.querySelectorAll('.reci-question-scale');

					if (optRow) optRow.style.display = isChoice ? '' : 'none';
					if (corRow) corRow.style.display = isChoice ? '' : 'none';
					scaRows.forEach(el => el.style.display = type === 'scale' ? '' : 'none');
				}

				function questionHtml(index) {
					return '<div class="reci-repeater-item">' +
						'<div class="reci-meta-grid">' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<label><?php echo esc_js(__('Question', 'reci-media-hub')); ?></label>' +
						'<input type="text" name="_reci_assessment_questions['+index+'][prompt]" value="" />' +
						'</div>' +
						'<div class="reci-meta-row">' +
						'<label><?php echo esc_js(__('Type', 'reci-media-hub')); ?></label>' +
						'<select class="reci-assessment-question-type" name="_reci_assessment_questions['+index+'][type]">' +
						'<option value="scale"><?php echo esc_js(__('Scale / Rating', 'reci-media-hub')); ?></option>' +
						'<option value="single_choice"><?php echo esc_js(__('Single Choice', 'reci-media-hub')); ?></option>' +
						'<option value="multiple_choice"><?php echo esc_js(__('Multiple Choice', 'reci-media-hub')); ?></option>' +
						'<option value="text"><?php echo esc_js(__('Short Text', 'reci-media-hub')); ?></option>' +
						'<option value="textarea"><?php echo esc_js(__('Long Text', 'reci-media-hub')); ?></option>' +
						'</select>' +
						'</div>' +
						'<div class="reci-meta-row" style="justify-content:flex-end;">' +
						'<label style="margin-top:28px;"><input type="checkbox" name="_reci_assessment_questions['+index+'][required]" value="1" checked /> <?php echo esc_js(__('Required', 'reci-media-hub')); ?></label>' +
						'</div>' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<label><?php echo esc_js(__('Help Text (optional)', 'reci-media-hub')); ?></label>' +
						'<textarea rows="2" name="_reci_assessment_questions['+index+'][help_text]"></textarea>' +
						'</div>' +
						'<div class="reci-meta-row reci-meta-row--full reci-question-options" style="display:none;">' +
						'<label><?php echo esc_js(__('Options (one per line)', 'reci-media-hub')); ?></label>' +
						'<textarea rows="3" name="_reci_assessment_questions['+index+'][options]"></textarea>' +
						'</div>' +
						'<div class="reci-meta-row reci-meta-row--full reci-question-correct" style="display:none;">' +
						'<label><?php echo esc_js(__('Correct Answer(s) (one per line)', 'reci-media-hub')); ?></label>' +
						'<textarea rows="2" name="_reci_assessment_questions['+index+'][correct_answers]"></textarea>' +
						'</div>' +
						'<div class="reci-meta-row reci-question-scale">' +
						'<label><?php echo esc_js(__('Low Label', 'reci-media-hub')); ?></label>' +
						'<input type="text" name="_reci_assessment_questions['+index+'][scale_min_label]" value="<?php echo esc_js(__('Not at all like me', 'reci-media-hub')); ?>" />' +
						'</div>' +
						'<div class="reci-meta-row reci-question-scale">' +
						'<label><?php echo esc_js(__('High Label', 'reci-media-hub')); ?></label>' +
						'<input type="text" name="_reci_assessment_questions['+index+'][scale_max_label]" value="<?php echo esc_js(__('Very much like me', 'reci-media-hub')); ?>" />' +
						'</div>' +
						'<div class="reci-meta-row reci-question-scale">' +
						'<label><?php echo esc_js(__('Scale Steps', 'reci-media-hub')); ?></label>' +
						'<input type="number" min="2" max="10" name="_reci_assessment_questions['+index+'][scale_steps]" value="5" />' +
						'</div>' +
						'</div>' +
						'<div class="reci-repeater-actions">' +
						'<button type="button" class="button button-link-delete reci-remove-question"><?php echo esc_js(__('Remove', 'reci-media-hub')); ?></button>' +
						'</div></div>';
				}

				if (qWrap && qAdd) {
					qAdd.addEventListener('click', () => {
						const idx = qWrap.querySelectorAll('.reci-repeater-item').length;
						qWrap.insertAdjacentHTML('beforeend', questionHtml(idx));
						syncQuestionType(qWrap.lastElementChild);
					});
					qWrap.addEventListener('click', e => {
						const btn = e.target.closest('.reci-remove-question');
						if (btn) btn.closest('.reci-repeater-item').remove();
					});
					qWrap.addEventListener('change', e => {
						const sel = e.target.closest('.reci-assessment-question-type');
						if (sel) syncQuestionType(sel.closest('.reci-repeater-item'));
					});
					qWrap.querySelectorAll('.reci-repeater-item').forEach(syncQuestionType);
				}

				// Bands Repeater
				const bWrap = document.getElementById('reci-assessment-bands');
				const bAdd = document.getElementById('reci-add-band');

				function bandHtml(index) {
					return '<div class="reci-repeater-item">' +
						'<div class="reci-meta-grid">' +
						'<div class="reci-meta-row">' +
						'<label><?php echo esc_js(__('Label', 'reci-media-hub')); ?></label>' +
						'<input type="text" name="_reci_assessment_result_ranges['+index+'][label]" value="" />' +
						'</div>' +
						'<div class="reci-meta-row">' +
						'<label><?php echo esc_js(__('Key', 'reci-media-hub')); ?></label>' +
						'<input type="text" name="_reci_assessment_result_ranges['+index+'][key]" value="" />' +
						'</div>' +
						'<div class="reci-meta-row">' +
						'<label><?php echo esc_js(__('Min %', 'reci-media-hub')); ?></label>' +
						'<input type="number" min="0" max="100" name="_reci_assessment_result_ranges['+index+'][min_percent]" value="0" />' +
						'</div>' +
						'<div class="reci-meta-row">' +
						'<label><?php echo esc_js(__('Max %', 'reci-media-hub')); ?></label>' +
						'<input type="number" min="0" max="100" name="_reci_assessment_result_ranges['+index+'][max_percent]" value="100" />' +
						'</div>' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<label><?php echo esc_js(__('Feedback Message', 'reci-media-hub')); ?></label>' +
						'<textarea name="_reci_assessment_result_ranges['+index+'][message]" rows="3"></textarea>' +
						'</div>' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<label><?php echo esc_js(__('Recommended Topics (one per line)', 'reci-media-hub')); ?></label>' +
						'<textarea name="_reci_assessment_result_ranges['+index+'][recommended_topics]" rows="2"></textarea>' +
						'</div>' +
						'</div>' +
						'<div class="reci-repeater-actions">' +
						'<button type="button" class="button button-link-delete reci-remove-band"><?php echo esc_js(__('Remove Band', 'reci-media-hub')); ?></button>' +
						'</div></div>';
				}

				if (bWrap && bAdd) {
					bAdd.addEventListener('click', () => {
						const idx = bWrap.querySelectorAll('.reci-repeater-item').length;
						bWrap.insertAdjacentHTML('beforeend', bandHtml(idx));
					});
					bWrap.addEventListener('click', e => {
						const btn = e.target.closest('.reci-remove-band');
						if (btn) btn.closest('.reci-repeater-item').remove();
					});
				}
			})();
		</script>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_course_metabox')) {
	function reci_media_hub_render_course_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$level      = (string) get_post_meta($post->ID, '_reci_course_level', true);
		$duration   = (string) get_post_meta($post->ID, '_reci_course_duration_weeks', true);
		$format     = (string) get_post_meta($post->ID, '_reci_course_format', true);
		$start_date = (string) get_post_meta($post->ID, '_reci_course_start_date', true);
		$fee_label  = (string) get_post_meta($post->ID, '_reci_course_fee_label', true);
		$enroll_url = (string) get_post_meta($post->ID, '_reci_course_enrollment_url', true);
		$lessons    = json_decode((string) get_post_meta($post->ID, '_reci_course_lessons', true), true) ?: [];
	?>
		<div class="reci-meta-grid">
			<div class="reci-meta-row">
				<label for="_reci_course_level"><strong><?php esc_html_e('Level', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_course_level" name="_reci_course_level">
					<?php foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($level, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_field('_reci_course_duration_weeks', __('Duration (weeks)', 'reci-media-hub'), $duration, 'number'); ?>
			<div class="reci-meta-row">
				<label for="_reci_course_format"><strong><?php esc_html_e('Format', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_course_format" name="_reci_course_format">
					<?php foreach (['self_paced' => 'Self Paced', 'cohort' => 'Cohort', 'live' => 'Live'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($format, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_field('_reci_course_start_date', __('Start Date', 'reci-media-hub'), $start_date, 'date'); ?>
			<?php reci_media_hub_render_field('_reci_course_fee_label', __('Fee Label', 'reci-media-hub'), $fee_label); ?>
			<?php reci_media_hub_render_field('_reci_course_enrollment_url', __('Enrollment URL', 'reci-media-hub'), $enroll_url, 'url', 'reci-meta-row--full'); ?>
		</div>

		<div class="reci-meta-row reci-meta-row--full" style="margin-top:12px;">
			<label><strong><?php esc_html_e('Lessons', 'reci-media-hub'); ?></strong></label>
			<div id="reci-course-lessons" class="reci-repeater-list">
				<?php foreach ($lessons as $index => $lesson) : ?>
					<div class="reci-repeater-item">
						<div class="reci-meta-row reci-meta-row--full">
							<input type="text" name="_reci_course_lessons[]" value="<?php echo esc_attr((string) $lesson); ?>" placeholder="Lesson title" />
						</div>
						<div class="reci-repeater-actions">
							<button type="button" class="button button-link-delete reci-remove-lesson"><?php esc_html_e('Remove', 'reci-media-hub'); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="reci-repeater-actions">
				<button type="button" class="button" id="reci-add-lesson"><?php esc_html_e('Add Lesson', 'reci-media-hub'); ?></button>
			</div>
		</div>

		<script>
			(function() {
				const wrap = document.getElementById('reci-course-lessons');
				const addBtn = document.getElementById('reci-add-lesson');
				if (!wrap || !addBtn) return;

				addBtn.addEventListener('click', () => {
					const html = '<div class="reci-repeater-item">' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<input type="text" name="_reci_course_lessons[]" value="" placeholder="Lesson title" />' +
						'</div>' +
						'<div class="reci-repeater-actions">' +
						'<button type="button" class="button button-link-delete reci-remove-lesson"><?php echo esc_js(__('Remove', 'reci-media-hub')); ?></button>' +
						'</div></div>';
					wrap.insertAdjacentHTML('beforeend', html);
				});

				wrap.addEventListener('click', e => {
					const btn = e.target.closest('.reci-remove-lesson');
					if (btn) btn.closest('.reci-repeater-item').remove();
				});
			})();
		</script>
<?php
	}
}

if (! function_exists('reci_media_hub_render_author_profile_metabox')) {
	/**
	 * Render author profile admin fields.
	 */
	function reci_media_hub_render_author_profile_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$linked_user_id = (string) get_post_meta($post->ID, '_reci_author_profile_user_id', true);
		$profile_title  = (string) get_post_meta($post->ID, '_reci_author_profile_title', true);

		$user_options = ['0' => __('No linked account yet', 'reci-media-hub')];
		$users        = get_users([
			'fields'  => ['ID', 'display_name'],
			'orderby' => 'display_name',
			'order'   => 'ASC',
		]);
		foreach ($users as $user) {
			$user_options[(string) $user->ID] = $user->display_name;
		}
	?>
		<p class="description"><?php esc_html_e('Use the post title as the public author name. Featured image becomes the avatar, excerpt/content becomes the bio, and the linked account is optional. When an account is linked, these fields are overwritten from that user on approval — edit the user profile instead.', 'reci-media-hub'); ?></p>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_select_field('_reci_author_profile_user_id', __('Linked User Account', 'reci-media-hub'), $linked_user_id, $user_options, 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_author_profile_title', __('Role / Title', 'reci-media-hub'), $profile_title, 'text', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_author_email', __('Contact Email', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_email', true), 'text'); ?>
			<?php reci_media_hub_render_field('_reci_author_website', __('Primary Link', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_website', true), 'text'); ?>
			<?php reci_media_hub_render_field('_reci_author_organization', __('Organization', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_organization', true), 'text'); ?>
			<?php reci_media_hub_render_field('_reci_author_department', __('Department', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_department', true), 'text'); ?>
			<?php reci_media_hub_render_field('_reci_author_pitt_affiliation', __('Pitt Affiliation', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_pitt_affiliation', true), 'text', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_author_social_links', __('Social Links (one per line)', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_social_links', true), 'textarea', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_author_highlighted_links', __('Highlighted Work (one per line)', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_highlighted_links', true), 'textarea', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_author_cv_id', __('CV Attachment ID', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_cv_id', true), 'number'); ?>
			<?php reci_media_hub_render_field('_reci_author_source_url', __('Imported From', 'reci-media-hub'), (string) get_post_meta($post->ID, '_reci_author_source_url', true), 'text'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_partner_metabox')) {
	/**
	 * Render partner admin fields.
	 */
	function reci_media_hub_render_partner_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$partner_url = (string) get_post_meta($post->ID, '_reci_partner_url', true);
	?>
		<p class="description"><?php esc_html_e('Post title is the organization name. Featured image is the logo.', 'reci-media-hub'); ?></p>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_field('_reci_partner_url', __('Partner Website URL', 'reci-media-hub'), $partner_url, 'url', 'reci-meta-row--full'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_testimonial_metabox')) {
	function reci_media_hub_render_testimonial_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$text       = (string) get_post_meta($post->ID, '_reci_testimonial_text', true);
		$full_name  = (string) get_post_meta($post->ID, '_reci_testimonial_full_name', true);
		$role       = (string) get_post_meta($post->ID, '_reci_testimonial_role', true);
		$org        = (string) get_post_meta($post->ID, '_reci_testimonial_organization', true);
	?>
		<div class="reci-meta-grid" style="grid-template-columns: 1fr;">
			<?php reci_media_hub_render_textarea_field('_reci_testimonial_text', __('Testimonial Text', 'reci-media-hub'), $text, 'reci-meta-row--full', 5); ?>

			<div class="reci-meta-row reci-meta-row--full" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;border-top:1px solid #dcdcde;padding-top:16px;margin-top:8px;">
				<?php reci_media_hub_render_field('_reci_testimonial_full_name', __('Full Name', 'reci-media-hub'), $full_name); ?>
				<?php reci_media_hub_render_field('_reci_testimonial_role', __('Title / Role', 'reci-media-hub'), $role); ?>
			</div>

			<div class="reci-meta-row reci-meta-row--full" style="border-top:1px solid #dcdcde;padding-top:16px;margin-top:8px;">
				<?php reci_media_hub_render_field('_reci_testimonial_organization', __('Organization', 'reci-media-hub'), $org); ?>
			</div>

			<div class="reci-meta-row reci-meta-row--full" style="border-top:1px solid #dcdcde;padding-top:16px;margin-top:8px;">
				<strong style="display:block;margin-bottom:8px;"><?php esc_html_e('Headshot', 'reci-media-hub'); ?></strong>
				<?php echo _wp_post_thumbnail_html(get_post_thumbnail_id($post->ID), $post->ID); ?>
			</div>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_remove_testimonial_metaboxes')) {
	add_action('do_meta_boxes', 'reci_media_hub_remove_testimonial_metaboxes');
	function reci_media_hub_remove_testimonial_metaboxes(): void {
		remove_meta_box('postimagediv', 'reci_testimonial', 'side');
	}
}

if (! function_exists('reci_media_hub_testimonial_featured_image_label')) {
	add_filter('admin_post_thumbnail_html', 'reci_media_hub_testimonial_featured_image_label', 10, 2);
	function reci_media_hub_testimonial_featured_image_label(string $html, int $post_id): string {
		if (get_post_type($post_id) !== 'reci_testimonial') {
			return $html;
		}
		return str_replace(
			['Featured Image', 'Set featured image', 'Remove featured image'],
			['Headshot', 'Set headshot', 'Remove headshot'],
			$html
		);
	}
}

if (! function_exists('reci_media_hub_testimonial_featured_image_title')) {
	add_filter('admin_post_thumbnail_title', 'reci_media_hub_testimonial_featured_image_title', 10, 2);
	function reci_media_hub_testimonial_featured_image_title(string $title, int $post_id): string {
		if (get_post_type($post_id) !== 'reci_testimonial') {
			return $title;
		}
		return __('Headshot', 'reci-media-hub');
	}
}

if (! function_exists('reci_media_hub_render_display_author_metabox')) {
	/**
	 * Render shared display-author selector on content items.
	 */
	function reci_media_hub_render_display_author_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$current_profile_id = (string) max(0, (int) get_post_meta($post->ID, '_reci_display_author_profile_id', true));
		$current_user_id    = (int) get_post_field('post_author', $post->ID);
		$linked_profile_id  = $current_user_id > 0 ? reci_media_hub_get_author_profile_by_user_id($current_user_id) : 0;

		$options = ['0' => __('Use linked account / post author fallback', 'reci-media-hub')];
		foreach (reci_media_hub_get_author_profile_options(false) as $profile) {
			$options[(string) $profile['ID']] = (string) $profile['display_name'];
		}
	?>
		<div class="reci-meta-grid" style="grid-template-columns: 1fr;">
			<?php reci_media_hub_render_select_field('_reci_display_author_profile_id', __('Public Author', 'reci-media-hub'), $current_profile_id, $options); ?>
		</div>
		<div class="reci-meta-grid" style="grid-template-columns: 1fr; margin-top: 16px;">
			<div class="reci-meta-row">
				<label for="_reci_new_author_name"><strong><?php esc_html_e('Create New Author', 'reci-media-hub'); ?></strong></label>
				<input type="text" id="_reci_new_author_name" name="_reci_new_author_name" value="" placeholder="<?php esc_attr_e('New author name', 'reci-media-hub'); ?>" />
			</div>
			<?php reci_media_hub_render_field('_reci_new_author_title', __('New Author Role / Title', 'reci-media-hub'), '', 'text'); ?>
			<?php reci_media_hub_render_textarea_field('_reci_new_author_bio', __('New Author Bio', 'reci-media-hub'), '', '', 3); ?>
		</div>
		<p class="description"><?php esc_html_e('If you enter a new author name here and save the post, a profile will be created and selected automatically.', 'reci-media-hub'); ?></p>
		<?php if ($linked_profile_id > 0) : ?>
			<p class="description"><?php echo esc_html(sprintf(__('This post author is linked to "%s". Leave the field on fallback to use that profile automatically.', 'reci-media-hub'), get_the_title($linked_profile_id))); ?></p>
		<?php elseif ($current_user_id > 0) : ?>
			<p class="description"><?php esc_html_e('If left on fallback, the frontend will use the WordPress account name until a profile is linked or selected.', 'reci-media-hub'); ?></p>
		<?php else : ?>
			<p class="description"><?php esc_html_e('Select an author profile when this content should credit someone who does not own the WordPress post.', 'reci-media-hub'); ?></p>
		<?php endif; ?>
	<?php
	}
}

if (! function_exists('reci_media_hub_create_or_get_author_profile')) {
	/**
	 * Create or reuse an author profile from inline post-edit input.
	 */
	function reci_media_hub_create_or_get_author_profile(string $name, string $title = '', string $bio = ''): int
	{
		$name = sanitize_text_field($name);
		$title = sanitize_text_field($title);
		$bio = sanitize_textarea_field($bio);

		if ($name === '') {
			return 0;
		}

		$existing = get_page_by_title($name, OBJECT, 'reci_author');
		if ($existing instanceof WP_Post) {
			if ($title !== '' && (string) get_post_meta((int) $existing->ID, '_reci_author_profile_title', true) === '') {
				update_post_meta((int) $existing->ID, '_reci_author_profile_title', $title);
			}
			if ($bio !== '' && ! has_excerpt((int) $existing->ID)) {
				wp_update_post([
					'ID' => (int) $existing->ID,
					'post_excerpt' => $bio,
				]);
			}

			return (int) $existing->ID;
		}

		$post_status = current_user_can('publish_posts') ? 'publish' : 'draft';
		$profile_id = wp_insert_post([
			'post_type' => 'reci_author',
			'post_status' => $post_status,
			'post_title' => $name,
			'post_excerpt' => $bio,
			'post_content' => $bio,
		], true);

		if (is_wp_error($profile_id) || ! $profile_id) {
			return 0;
		}

		if ($title !== '') {
			update_post_meta((int) $profile_id, '_reci_author_profile_title', $title);
		}

		return (int) $profile_id;
	}
}

if (! function_exists('reci_media_hub_save_meta_fields')) {
	/**
	 * Save all custom meta fields.
	 */
	function reci_media_hub_save_meta_fields(int $post_id): void
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		$nonce_raw = $_POST['reci_meta_nonce'] ?? '';
		$nonce     = is_string($nonce_raw) ? sanitize_text_field(wp_unslash($nonce_raw)) : '';
		if (! wp_verify_nonce($nonce, 'reci_save_meta')) {
			return;
		}

		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		// Builder metabox owns _reci_reflection_blueprint — skip generic save when builder nonce present.
		$builder_nonce_raw = $_POST['reci_builder_nonce'] ?? '';
		if (is_string($builder_nonce_raw) && wp_verify_nonce(sanitize_text_field(wp_unslash($builder_nonce_raw)), 'reci_builder_save')) {
			return;
		}

		$post_type = get_post_type($post_id);
		if (! $post_type) {
			return;
		}

		if ($post_type === 'post') {
			$content_raw = isset($_POST['content']) ? wp_strip_all_tags(wp_unslash((string) $_POST['content'])) : '';
			if (empty($content_raw)) {
				$post       = get_post($post_id);
				$content_raw = $post ? wp_strip_all_tags($post->post_content) : '';
			}
			if ($content_raw) {
				$word_count = str_word_count($content_raw);
				$minutes    = max(1, ceil($word_count / 200));
				update_post_meta($post_id, '_post_read_time_label', $minutes . ' min read');
			}
		}

		$definitions = reci_media_hub_meta_definitions();
		if (! isset($definitions[$post_type])) {
			return;
		}

		$new_author_name  = isset($_POST['_reci_new_author_name']) ? sanitize_text_field((string) wp_unslash($_POST['_reci_new_author_name'])) : '';
		$new_author_title = isset($_POST['_reci_new_author_title']) ? sanitize_text_field((string) wp_unslash($_POST['_reci_new_author_title'])) : '';
		$new_author_bio   = isset($_POST['_reci_new_author_bio']) ? sanitize_textarea_field((string) wp_unslash($_POST['_reci_new_author_bio'])) : '';
		$new_author_id    = 0;

		if ($new_author_name !== '' && in_array($post_type, reci_media_hub_display_author_post_types(), true)) {
			$new_author_id = reci_media_hub_create_or_get_author_profile($new_author_name, $new_author_title, $new_author_bio);
			if ($new_author_id > 0) {
				update_post_meta($post_id, '_reci_display_author_profile_id', $new_author_id);
			}
		}

		foreach ($definitions[$post_type] as $meta_key => $config) {
			if ($meta_key === '_post_read_time_label') {
				continue;
			}
			if ($meta_key === '_reci_assessment_questions') {
				$sanitized = reci_media_hub_collect_assessment_questions_from_request();
				update_post_meta($post_id, $meta_key, wp_json_encode($sanitized));
				continue;
			}

			if ($meta_key === '_reci_assessment_result_ranges') {
				$sanitized = reci_media_hub_collect_assessment_result_ranges_from_request();
				update_post_meta($post_id, $meta_key, wp_json_encode($sanitized));
				continue;
			}

			if ($meta_key === '_reci_course_lessons') {
				$lessons = isset($_POST['_reci_course_lessons']) && is_array($_POST['_reci_course_lessons'])
					? array_values(array_filter(array_map('sanitize_text_field', wp_unslash($_POST['_reci_course_lessons']))))
					: [];
				update_post_meta($post_id, $meta_key, wp_json_encode($lessons));
				continue;
			}

			if (! array_key_exists($meta_key, $_POST)) {
				if ($config['type'] === 'boolean') {
					update_post_meta($post_id, $meta_key, false);
				}
				continue;
			}

			$value = wp_unslash($_POST[$meta_key]);

			if ($config['type'] === 'string') {
				if (in_array($meta_key, ['_reci_assessment_intro', '_reci_assessment_instructions', '_reci_assessment_result_ranges', '_reci_assessment_completion_message', '_reci_quote_text', '_reci_testimonial_text'], true)) {
					$value = sanitize_textarea_field((string) $value);
				} else {
					$value = str_contains($meta_key, '_url')
						? esc_url_raw((string) $value)
						: sanitize_text_field((string) $value);
				}
			} elseif ($config['type'] === 'integer') {
				$value = (int) $value;
			} elseif ($config['type'] === 'boolean') {
				$value = ! empty($value);
			}

			update_post_meta($post_id, $meta_key, $value);
		}

		if ($new_author_id > 0) {
			update_post_meta($post_id, '_reci_display_author_profile_id', $new_author_id);
		}
	}
}

add_action('save_post', 'reci_media_hub_save_meta_fields');
