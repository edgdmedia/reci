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
			'reci_article'    => [
				'_reci_article_read_time_label' => ['type' => 'string', 'single' => true, 'default' => '3 mins'],
				'_reci_article_featured_rank'   => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_article_source_name'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_article_source_url'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_article_canonical_url'   => ['type' => 'string', 'single' => true, 'default' => ''],
			],
			'reci_event'      => [
				'_reci_event_status'            => ['type' => 'string', 'single' => true, 'default' => 'upcoming'],
				'_reci_event_start_date'        => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_start_time'        => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_end_time'          => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_timezone'          => ['type' => 'string', 'single' => true, 'default' => 'UTC'],
				'_reci_event_location_name'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_location_address'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_is_virtual'        => ['type' => 'boolean', 'single' => true, 'default' => false],
				'_reci_event_registration_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_event_cta_label'         => ['type' => 'string', 'single' => true, 'default' => 'Register'],
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
			],
			'reci_video'      => [
				'_reci_video_url'             => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_video_platform'        => ['type' => 'string', 'single' => true, 'default' => 'youtube'],
				'_reci_video_duration_label'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_video_duration_secs'   => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_video_external_id'     => ['type' => 'string', 'single' => true, 'default' => ''],
			],
			'reci_reflection' => [
				'_reci_reflection_type'       => ['type' => 'string', 'single' => true, 'default' => 'story'],
				'_reci_reflection_quote'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_reflection_speaker'    => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_reflection_role'       => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_reflection_video_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_reflection_audio_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
			],
			'reci_quote'      => [
				'_reci_quote_text'            => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_quote_author_name'     => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_quote_author_title'    => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_quote_author_image_url' => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_quote_source_url'      => ['type' => 'string', 'single' => true, 'default' => ''],
			],
			'reci_assessment' => [
				'_reci_assessment_type'           => ['type' => 'string', 'single' => true, 'default' => 'quiz'],
				'_reci_assessment_intro'          => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_assessment_estimated_time' => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_assessment_questions'      => ['type' => 'string', 'single' => true, 'default' => '[]'],
				'_reci_assessment_result_ranges'  => ['type' => 'string', 'single' => true, 'default' => '[]'],
			],
			'reci_course'     => [
				'_reci_course_level'           => ['type' => 'string', 'single' => true, 'default' => 'beginner'],
				'_reci_course_duration_weeks'  => ['type' => 'integer', 'single' => true, 'default' => 0],
				'_reci_course_format'          => ['type' => 'string', 'single' => true, 'default' => 'self_paced'],
				'_reci_course_start_date'      => ['type' => 'string', 'single' => true, 'default' => ''],
				'_reci_course_fee_label'       => ['type' => 'string', 'single' => true, 'default' => 'Free'],
				'_reci_course_enrollment_url'  => ['type' => 'string', 'single' => true, 'default' => ''],
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
		add_meta_box('reci-article-details', __('Article Details', 'reci-media-hub'), 'reci_media_hub_render_article_metabox', 'reci_article', 'normal', 'high');
		add_meta_box('reci-event-details', __('Event Details', 'reci-media-hub'), 'reci_media_hub_render_event_metabox', 'reci_event', 'normal', 'high');
		add_meta_box('reci-podcast-details', __('Podcast Details', 'reci-media-hub'), 'reci_media_hub_render_podcast_metabox', 'reci_podcast', 'normal', 'high');
		add_meta_box('reci-video-details', __('Video Details', 'reci-media-hub'), 'reci_media_hub_render_video_metabox', 'reci_video', 'normal', 'high');
		add_meta_box('reci-reflection-details', __('Reflection Details', 'reci-media-hub'), 'reci_media_hub_render_reflection_metabox', 'reci_reflection', 'normal', 'high');
		add_meta_box('reci-quote-details', __('Quote Details', 'reci-media-hub'), 'reci_media_hub_render_quote_metabox', 'reci_quote', 'normal', 'high');
		add_meta_box('reci-assessment-details', __('Assessment Details', 'reci-media-hub'), 'reci_media_hub_render_assessment_metabox', 'reci_assessment', 'normal', 'high');
		add_meta_box('reci-course-details', __('Course Details', 'reci-media-hub'), 'reci_media_hub_render_course_metabox', 'reci_course', 'normal', 'high');
	}
}

add_action('add_meta_boxes', 'reci_media_hub_add_meta_boxes');

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
	<?php
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

if (! function_exists('reci_media_hub_render_article_metabox')) {
	function reci_media_hub_render_article_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$read_time = (string) get_post_meta($post->ID, '_reci_article_read_time_label', true);
		$rank      = (string) get_post_meta($post->ID, '_reci_article_featured_rank', true);
		$source    = (string) get_post_meta($post->ID, '_reci_article_source_name', true);
		$source_url = (string) get_post_meta($post->ID, '_reci_article_source_url', true);
		$canonical = (string) get_post_meta($post->ID, '_reci_article_canonical_url', true);
	?>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_field('_reci_article_read_time_label', __('Read Time Label', 'reci-media-hub'), $read_time); ?>
			<?php reci_media_hub_render_field('_reci_article_featured_rank', __('Featured Rank', 'reci-media-hub'), $rank, 'number'); ?>
			<?php reci_media_hub_render_field('_reci_article_source_name', __('Source Name', 'reci-media-hub'), $source); ?>
			<?php reci_media_hub_render_field('_reci_article_source_url', __('Source URL', 'reci-media-hub'), $source_url, 'url'); ?>
			<?php reci_media_hub_render_field('_reci_article_canonical_url', __('Canonical URL', 'reci-media-hub'), $canonical, 'url', 'reci-meta-row--full'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_event_metabox')) {
	function reci_media_hub_render_event_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$status           = (string) get_post_meta($post->ID, '_reci_event_status', true);
		$start_date       = (string) get_post_meta($post->ID, '_reci_event_start_date', true);
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
			<div class="reci-meta-row">
				<label for="_reci_event_status"><strong><?php esc_html_e('Status', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_event_status" name="_reci_event_status">
					<?php foreach (['upcoming' => 'Upcoming', 'live' => 'Live', 'past' => 'Past'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($status, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_field('_reci_event_start_date', __('Start Date', 'reci-media-hub'), $start_date, 'date'); ?>
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

if (! function_exists('reci_media_hub_render_reflection_metabox')) {
	function reci_media_hub_render_reflection_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$type      = (string) get_post_meta($post->ID, '_reci_reflection_type', true);
		$quote     = (string) get_post_meta($post->ID, '_reci_reflection_quote', true);
		$speaker   = (string) get_post_meta($post->ID, '_reci_reflection_speaker', true);
		$role      = (string) get_post_meta($post->ID, '_reci_reflection_role', true);
		$video_url = (string) get_post_meta($post->ID, '_reci_reflection_video_url', true);
		$audio_url = (string) get_post_meta($post->ID, '_reci_reflection_audio_url', true);
	?>
		<div class="reci-meta-grid">
			<div class="reci-meta-row">
				<label for="_reci_reflection_type"><strong><?php esc_html_e('Reflection Type', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_reflection_type" name="_reci_reflection_type">
					<?php foreach (['story' => 'Story', 'gallery' => 'Gallery', 'testimony' => 'Testimony'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_textarea_field('_reci_reflection_quote', __('Featured Quote', 'reci-media-hub'), $quote, 'reci-meta-row--full', 3); ?>
			<?php reci_media_hub_render_field('_reci_reflection_speaker', __('Speaker Name', 'reci-media-hub'), $speaker); ?>
			<?php reci_media_hub_render_field('_reci_reflection_role', __('Speaker Role', 'reci-media-hub'), $role); ?>
			<?php reci_media_hub_render_field('_reci_reflection_video_url', __('Video URL', 'reci-media-hub'), $video_url, 'url', 'reci-meta-row--full'); ?>
			<?php reci_media_hub_render_field('_reci_reflection_audio_url', __('Audio URL', 'reci-media-hub'), $audio_url, 'url', 'reci-meta-row--full'); ?>
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
		$author_img  = (string) get_post_meta($post->ID, '_reci_quote_author_image_url', true);
		$source_url  = (string) get_post_meta($post->ID, '_reci_quote_source_url', true);
	?>
		<div class="reci-meta-grid">
			<?php reci_media_hub_render_textarea_field('_reci_quote_text', __('Quote Text', 'reci-media-hub'), $quote_text, 'reci-meta-row--full', 5); ?>
			<?php reci_media_hub_render_field('_reci_quote_author_name', __('Author Name', 'reci-media-hub'), $author_name); ?>
			<?php reci_media_hub_render_field('_reci_quote_author_title', __('Author Title', 'reci-media-hub'), $author_role); ?>
			<?php reci_media_hub_render_field('_reci_quote_author_image_url', __('Author Image URL', 'reci-media-hub'), $author_img, 'url'); ?>
			<?php reci_media_hub_render_field('_reci_quote_source_url', __('Source URL', 'reci-media-hub'), $source_url, 'url'); ?>
		</div>
	<?php
	}
}

if (! function_exists('reci_media_hub_render_assessment_metabox')) {
	function reci_media_hub_render_assessment_metabox(WP_Post $post): void
	{
		wp_nonce_field('reci_save_meta', 'reci_meta_nonce');
		reci_media_hub_metabox_styles();

		$type          = (string) get_post_meta($post->ID, '_reci_assessment_type', true);
		$intro         = (string) get_post_meta($post->ID, '_reci_assessment_intro', true);
		$estimated     = (string) get_post_meta($post->ID, '_reci_assessment_estimated_time', true);
		$questions_raw = (string) get_post_meta($post->ID, '_reci_assessment_questions', true);
		$ranges_raw    = (string) get_post_meta($post->ID, '_reci_assessment_result_ranges', true);
		$questions     = json_decode($questions_raw, true);

		if (! is_array($questions)) {
			$questions = [];
		}
	?>
		<div class="reci-meta-grid">
			<div class="reci-meta-row">
				<label for="_reci_assessment_type"><strong><?php esc_html_e('Assessment Type', 'reci-media-hub'); ?></strong></label>
				<select id="_reci_assessment_type" name="_reci_assessment_type">
					<?php foreach (['quiz' => 'Quiz', 'checklist' => 'Checklist', 'survey' => 'Survey'] as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php reci_media_hub_render_field('_reci_assessment_estimated_time', __('Estimated Time', 'reci-media-hub'), $estimated); ?>
			<?php reci_media_hub_render_textarea_field('_reci_assessment_intro', __('Intro Text', 'reci-media-hub'), $intro, 'reci-meta-row--full', 3); ?>
			<?php reci_media_hub_render_textarea_field('_reci_assessment_result_ranges', __('Result Ranges JSON', 'reci-media-hub'), $ranges_raw, 'reci-meta-row--full', 4); ?>
		</div>

		<div class="reci-meta-row reci-meta-row--full" style="margin-top:12px;">
			<label><strong><?php esc_html_e('Questions (Repeater)', 'reci-media-hub'); ?></strong></label>
			<div id="reci-assessment-questions" class="reci-repeater-list">
				<?php foreach ($questions as $index => $question) : ?>
					<div class="reci-repeater-item">
						<div class="reci-meta-grid">
							<div class="reci-meta-row reci-meta-row--full">
								<label><?php esc_html_e('Question', 'reci-media-hub'); ?></label>
								<input type="text" name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][question]" value="<?php echo esc_attr((string) ($question['question'] ?? '')); ?>" />
							</div>
							<div class="reci-meta-row reci-meta-row--full">
								<label><?php esc_html_e('Options (one per line)', 'reci-media-hub'); ?></label>
								<textarea name="_reci_assessment_questions[<?php echo esc_attr((string) $index); ?>][options]" rows="4"><?php echo esc_textarea((string) ($question['options'] ?? '')); ?></textarea>
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

		<script>
			(function() {
				const wrap = document.getElementById('reci-assessment-questions');
				const addButton = document.getElementById('reci-add-question');
				if (!wrap || !addButton) return;

				function questionHtml(index) {
					return '' +
						'<div class="reci-repeater-item">' +
						'<div class="reci-meta-grid">' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<label><?php echo esc_js(__('Question', 'reci-media-hub')); ?></label>' +
						'<input type="text" name="_reci_assessment_questions[' + index + '][question]" value="" />' +
						'</div>' +
						'<div class="reci-meta-row reci-meta-row--full">' +
						'<label><?php echo esc_js(__('Options (one per line)', 'reci-media-hub')); ?></label>' +
						'<textarea rows="4" name="_reci_assessment_questions[' + index + '][options]"></textarea>' +
						'</div>' +
						'</div>' +
						'<div class="reci-repeater-actions">' +
						'<button type="button" class="button button-link-delete reci-remove-question"><?php echo esc_js(__('Remove', 'reci-media-hub')); ?></button>' +
						'</div>' +
						'</div>';
				}

				addButton.addEventListener('click', function() {
					const index = wrap.querySelectorAll('.reci-repeater-item').length;
					wrap.insertAdjacentHTML('beforeend', questionHtml(index));
				});

				wrap.addEventListener('click', function(event) {
					const button = event.target.closest('.reci-remove-question');
					if (!button) return;
					const item = button.closest('.reci-repeater-item');
					if (item) item.remove();
				});
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
<?php
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

		$post_type = get_post_type($post_id);
		if (! $post_type) {
			return;
		}

		$definitions = reci_media_hub_meta_definitions();
		if (! isset($definitions[$post_type])) {
			return;
		}

		foreach ($definitions[$post_type] as $meta_key => $config) {
			if ($meta_key === '_reci_assessment_questions') {
				$raw_questions = $_POST['_reci_assessment_questions'] ?? [];
				$sanitized     = [];

				if (is_array($raw_questions)) {
					foreach ($raw_questions as $item) {
						$question_text = sanitize_text_field((string) ($item['question'] ?? ''));
						$options_text  = sanitize_textarea_field((string) ($item['options'] ?? ''));

						if ($question_text === '') {
							continue;
						}

						$sanitized[] = [
							'question' => $question_text,
							'options'  => $options_text,
						];
					}
				}

				update_post_meta($post_id, $meta_key, wp_json_encode($sanitized));
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
				if (in_array($meta_key, ['_reci_assessment_intro', '_reci_assessment_result_ranges', '_reci_quote_text'], true)) {
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
	}
}

add_action('save_post', 'reci_media_hub_save_meta_fields');
