<?php

/**
 * Shared assessment helpers, submissions, import/export, and admin tools.
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! function_exists('reci_media_hub_get_assessment_submission_post_type')) {
	function reci_media_hub_get_assessment_submission_post_type(): string
	{
		// WP post type keys must be 20 chars or fewer.
		return 'reci_assess_submit';
	}
}

if (! function_exists('reci_media_hub_register_assessment_submission_type')) {
	function reci_media_hub_register_assessment_submission_type(): void
	{
		register_post_type(
			reci_media_hub_get_assessment_submission_post_type(),
			[
				'labels' => [
					'name'          => __('Quiz Submissions', 'reci-media-hub'),
					'singular_name' => __('Quiz Submission', 'reci-media-hub'),
				],
				'public'             => false,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'show_ui'            => false,
				'show_in_menu'       => false,
				'supports'           => ['title'],
				'capability_type'    => 'post',
			]
		);
	}
}

add_action('init', 'reci_media_hub_register_assessment_submission_type');

if (! function_exists('reci_media_hub_get_assessment_notice_key')) {
	function reci_media_hub_get_assessment_notice_key(): string
	{
		return 'reci_assessment_notice_' . get_current_user_id();
	}
}

if (! function_exists('reci_media_hub_set_assessment_admin_notice')) {
	function reci_media_hub_set_assessment_admin_notice(string $message, string $type = 'success'): void
	{
		if (get_current_user_id() <= 0) {
			return;
		}

		set_transient(
			reci_media_hub_get_assessment_notice_key(),
			[
				'type'    => $type,
				'message' => $message,
			],
			MINUTE_IN_SECONDS * 5
		);
	}
}

if (! function_exists('reci_media_hub_render_assessment_admin_notice')) {
	function reci_media_hub_render_assessment_admin_notice(): void
	{
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;
		if (! $screen) {
			return;
		}

		$allowed = [
			'reci_assessment',
			'reci_assessment_page_reci-assessment-submissions',
		];

		if (! in_array((string) $screen->id, $allowed, true)) {
			return;
		}

		$notice = get_transient(reci_media_hub_get_assessment_notice_key());
		if (! is_array($notice) || empty($notice['message'])) {
			return;
		}

		delete_transient(reci_media_hub_get_assessment_notice_key());
		$type = isset($notice['type']) && $notice['type'] === 'error' ? 'notice notice-error' : 'notice notice-success';

		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			esc_attr($type),
			esc_html((string) $notice['message'])
		);
	}
}

add_action('admin_notices', 'reci_media_hub_render_assessment_admin_notice');

if (! function_exists('reci_media_hub_get_assessment_csv_sample_url')) {
	function reci_media_hub_get_assessment_csv_sample_url(): string
	{
		return trailingslashit(get_template_directory_uri()) . 'assets/samples/assessment-import-sample.csv';
	}
}

if (! function_exists('reci_media_hub_assessment_bool_from_mixed')) {
	function reci_media_hub_assessment_bool_from_mixed($value): bool
	{
		if (is_bool($value)) {
			return $value;
		}

		$value = strtolower(trim((string) $value));
		return in_array($value, ['1', 'true', 'yes', 'y', 'on'], true);
	}
}

if (! function_exists('reci_media_hub_parse_multiline_list')) {
	function reci_media_hub_parse_multiline_list(string $raw): array
	{
		$lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
		$items = [];

		foreach ($lines as $line) {
			$line = trim((string) $line);
			if ($line === '') {
				continue;
			}
			$items[] = sanitize_text_field($line);
		}

		return array_values(array_unique(array_filter($items)));
	}
}

if (! function_exists('reci_media_hub_parse_pipe_list')) {
	function reci_media_hub_parse_pipe_list(string $raw): array
	{
		$parts = array_map('trim', explode('|', $raw));
		$parts = array_filter($parts, static function ($item) {
			return $item !== '';
		});

		return array_values(array_map('sanitize_text_field', $parts));
	}
}

if (! function_exists('reci_media_hub_normalize_assessment_question')) {
	function reci_media_hub_normalize_assessment_question(array $raw_question, int $index = 0): array
	{
		$prompt = sanitize_text_field((string) ($raw_question['prompt'] ?? $raw_question['question'] ?? ''));
		if ($prompt === '') {
			return [];
		}

		$id = sanitize_title((string) ($raw_question['id'] ?? 'question-' . ($index + 1)));
		if ($id === '') {
			$id = 'question-' . ($index + 1);
		}

		$type = sanitize_key((string) ($raw_question['type'] ?? ''));
		if ($type === '') {
			$type = ! empty($raw_question['options']) ? 'single_choice' : 'text';
		}

		if ($type === 'likert') {
			$type = 'scale';
		}

		$allowed_types = ['scale', 'single_choice', 'multiple_choice', 'text', 'textarea'];
		if (! in_array($type, $allowed_types, true)) {
			$type = 'single_choice';
		}

		$options = $raw_question['options'] ?? [];
		if (is_string($options)) {
			$has_pipe = str_contains($options, '|');
			$options  = $has_pipe ? reci_media_hub_parse_pipe_list($options) : reci_media_hub_parse_multiline_list($options);
		}
		if (! is_array($options)) {
			$options = [];
		}
		$options = array_values(array_filter(array_map(static function ($option) {
			return sanitize_text_field((string) $option);
		}, $options)));

		$scale_steps     = max(2, min(10, (int) ($raw_question['scale_steps'] ?? 5)));
		$scale_min_label = sanitize_text_field((string) ($raw_question['scale_min_label'] ?? __('Not at all like me', 'reci-media-hub')));
		$scale_max_label = sanitize_text_field((string) ($raw_question['scale_max_label'] ?? __('Very much like me', 'reci-media-hub')));
		$help_text       = sanitize_textarea_field((string) ($raw_question['help_text'] ?? ''));
		$required        = array_key_exists('required', $raw_question)
			? reci_media_hub_assessment_bool_from_mixed($raw_question['required'])
			: true;

		$correct_answers = $raw_question['correct_answers'] ?? [];
		if (! is_array($correct_answers)) {
			$single_correct = (string) ($raw_question['correct_answer'] ?? '');
			$correct_answers = $single_correct !== '' ? [$single_correct] : [];
		}
		$correct_answers = array_values(array_filter(array_map(static function ($item) {
			return sanitize_text_field((string) $item);
		}, $correct_answers)));

		if (in_array($type, ['single_choice', 'multiple_choice'], true) && empty($options)) {
			$type = 'text';
		}

		return [
			'id'              => $id,
			'prompt'          => $prompt,
			'type'            => $type,
			'help_text'       => $help_text,
			'required'        => $required,
			'options'         => in_array($type, ['single_choice', 'multiple_choice'], true) ? $options : [],
			'correct_answers' => in_array($type, ['single_choice', 'multiple_choice'], true) ? $correct_answers : [],
			'scale_steps'     => $type === 'scale' ? $scale_steps : 0,
			'scale_min_label' => $type === 'scale' ? $scale_min_label : '',
			'scale_max_label' => $type === 'scale' ? $scale_max_label : '',
		];
	}
}

if (! function_exists('reci_media_hub_get_assessment_questions')) {
	function reci_media_hub_get_assessment_questions(int $post_id): array
	{
		$questions_raw = (string) get_post_meta($post_id, '_reci_assessment_questions', true);
		if ($questions_raw === '') {
			return [];
		}

		$decoded = json_decode($questions_raw, true);
		if (! is_array($decoded)) {
			return [];
		}

		$questions = [];
		foreach ($decoded as $index => $raw_question) {
			if (! is_array($raw_question)) {
				continue;
			}

			$normalized = reci_media_hub_normalize_assessment_question($raw_question, (int) $index);
			if (! empty($normalized)) {
				$questions[] = $normalized;
			}
		}

		return $questions;
	}
}

if (! function_exists('reci_media_hub_get_assessment_settings')) {
	function reci_media_hub_get_assessment_result_ranges(int $post_id): array
	{
		$raw = (string) get_post_meta($post_id, '_reci_assessment_result_ranges', true);
		if ($raw === '') {
			return [];
		}

		$decoded = json_decode($raw, true);
		if (! is_array($decoded)) {
			return [];
		}

		$ranges = [];
		foreach ($decoded as $idx => $item) {
			if (! is_array($item)) {
				continue;
			}
			$label = sanitize_text_field((string) ($item['label'] ?? ''));
			$message = sanitize_textarea_field((string) ($item['message'] ?? ''));
			if ($label === '' || $message === '') {
				continue;
			}
			$min = max(0, min(100, (int) ($item['min_percent'] ?? 0)));
			$max = max(0, min(100, (int) ($item['max_percent'] ?? 0)));
			if ($min > $max) {
				[$min, $max] = [$max, $min];
			}
			$topics = $item['recommended_topics'] ?? [];
			if (! is_array($topics)) {
				$topics = [];
			}
			$topics = array_values(array_filter(array_map('sanitize_title', $topics)));

			$ranges[] = [
				'key'                => sanitize_key((string) ($item['key'] ?? ('band_' . ($idx + 1)))),
				'label'              => $label,
				'min_percent'        => $min,
				'max_percent'        => $max,
				'message'            => $message,
				'recommended_topics' => $topics,
			];
		}

		usort($ranges, static function (array $a, array $b): int {
			return ((int) $a['min_percent']) <=> ((int) $b['min_percent']);
		});

		return $ranges;
	}

	function reci_media_hub_get_assessment_settings(int $post_id): array
	{
		$type = (string) get_post_meta($post_id, '_reci_assessment_type', true);
		if ($type === '') {
			$type = 'survey';
		}

		$completion_title = (string) get_post_meta($post_id, '_reci_assessment_completion_title', true);
		$completion_message = (string) get_post_meta($post_id, '_reci_assessment_completion_message', true);

		return [
			'type'               => $type,
			'intro'              => (string) get_post_meta($post_id, '_reci_assessment_intro', true),
			'instructions'       => (string) get_post_meta($post_id, '_reci_assessment_instructions', true),
			'estimated_time'     => (string) get_post_meta($post_id, '_reci_assessment_estimated_time', true),
			'completion_title'   => $completion_title ?: __('Thanks for completing this quiz.', 'reci-media-hub'),
			'completion_message' => $completion_message ?: __('Your responses have been saved.', 'reci-media-hub'),
			'questions'          => reci_media_hub_get_assessment_questions($post_id),
			'result_ranges'      => reci_media_hub_get_assessment_result_ranges($post_id),
		];
	}
}

if (! function_exists('reci_media_hub_parse_assessment_csv_file')) {
	function reci_media_hub_parse_assessment_csv_file(string $file_path): array
	{
		if (! file_exists($file_path) || ! is_readable($file_path)) {
			return [
				'questions' => [],
				'error'     => __('The uploaded CSV file could not be read.', 'reci-media-hub'),
			];
		}

		$handle = fopen($file_path, 'r');
		if (! $handle) {
			return [
				'questions' => [],
				'error'     => __('The uploaded CSV file could not be opened.', 'reci-media-hub'),
			];
		}

		$headers = fgetcsv($handle);
		if (! is_array($headers)) {
			fclose($handle);
			return [
				'questions' => [],
				'error'     => __('The CSV file is empty.', 'reci-media-hub'),
			];
		}

		$headers = array_map(static function ($header) {
			$header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);
			return sanitize_key((string) $header);
		}, $headers);

		$questions = [];
		$row_index = 0;
		while (($row = fgetcsv($handle)) !== false) {
			$row_index++;
			if (! is_array($row) || empty(array_filter($row, static function ($cell) {
				return trim((string) $cell) !== '';
			}))) {
				continue;
			}

			$mapped = [];
			foreach ($headers as $index => $header) {
				$mapped[$header] = $row[$index] ?? '';
			}

			$question = reci_media_hub_normalize_assessment_question(
				[
					'prompt'          => $mapped['question'] ?? '',
					'type'            => $mapped['type'] ?? '',
					'required'        => $mapped['required'] ?? '1',
					'help_text'       => $mapped['help_text'] ?? '',
					'options'         => $mapped['options'] ?? '',
					'scale_min_label' => $mapped['scale_min_label'] ?? '',
					'scale_max_label' => $mapped['scale_max_label'] ?? '',
					'scale_steps'     => $mapped['scale_steps'] ?? '',
				],
				$row_index - 1
			);

			if (! empty($question)) {
				$questions[] = $question;
			}
		}

		fclose($handle);

		if (empty($questions)) {
			return [
				'questions' => [],
				'error'     => __('No valid questions were found in the CSV file.', 'reci-media-hub'),
			];
		}

		return [
			'questions' => $questions,
			'error'     => '',
		];
	}
}

if (! function_exists('reci_media_hub_collect_assessment_questions_from_request')) {
	function reci_media_hub_collect_assessment_questions_from_request(): array
	{
		$uploaded_file = $_FILES['_reci_assessment_csv_import'] ?? null;
		if (is_array($uploaded_file) && ! empty($uploaded_file['tmp_name']) && (int) ($uploaded_file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
			$parsed = reci_media_hub_parse_assessment_csv_file((string) $uploaded_file['tmp_name']);
			if (! empty($parsed['error'])) {
				reci_media_hub_set_assessment_admin_notice((string) $parsed['error'], 'error');
			} else {
				reci_media_hub_set_assessment_admin_notice(
					sprintf(
						/* translators: %d: question count */
						__('Imported %d questions from CSV.', 'reci-media-hub'),
						count($parsed['questions'])
					)
				);
				return $parsed['questions'];
			}
		}

		$raw_questions = $_POST['_reci_assessment_questions'] ?? [];
		if (! is_array($raw_questions)) {
			return [];
		}

		$questions = [];
		foreach ($raw_questions as $index => $raw_question) {
			if (! is_array($raw_question)) {
				continue;
			}

			$normalized = reci_media_hub_normalize_assessment_question($raw_question, (int) $index);
			if (! empty($normalized)) {
				$questions[] = $normalized;
			}
		}

		return $questions;
	}
}

if (! function_exists('reci_media_hub_collect_assessment_result_ranges_from_request')) {
	/**
	 * Sanitize and normalize result bands from admin request.
	 */
	function reci_media_hub_collect_assessment_result_ranges_from_request(): array
	{
		$raw = $_POST['_reci_assessment_result_ranges'] ?? [];
		if (! is_array($raw)) {
			// Handle cases where it might be sent as a JSON string from a block editor or similar.
			$decoded = json_decode((string) $raw, true);
			$raw = is_array($decoded) ? $decoded : [];
		}

		$bands = [];
		foreach ($raw as $idx => $row) {
			if (! is_array($row)) {
				continue;
			}

			$label   = sanitize_text_field((string) ($row['label'] ?? ''));
			$message = sanitize_textarea_field((string) ($row['message'] ?? ''));
			
			if ($label === '' && $message === '') {
				continue;
			}

			$min = max(0, min(100, (int) ($row['min_percent'] ?? 0)));
			$max = max(0, min(100, (int) ($row['max_percent'] ?? 0)));
			if ($min > $max) {
				[$min, $max] = [$max, $min];
			}

			$topics = $row['recommended_topics'] ?? [];
			if (is_string($topics)) {
				$topics = reci_media_hub_parse_pipe_list($topics);
			}
			if (! is_array($topics)) {
				$topics = [];
			}
			$topics = array_values(array_filter(array_map('sanitize_title', $topics)));

			$bands[] = [
				'key'                => sanitize_key((string) ($row['key'] ?? ('band_' . ($idx + 1)))),
				'label'              => $label,
				'min_percent'        => $min,
				'max_percent'        => $max,
				'message'            => $message,
				'recommended_topics' => $topics,
			];
		}

		// Sort by min_percent.
		usort($bands, static function (array $a, array $b): int {
			return $a['min_percent'] <=> $b['min_percent'];
		});

		return $bands;
	}
}

if (! function_exists('reci_media_hub_get_assessment_submission_answer_label')) {
	function reci_media_hub_get_assessment_submission_answer_label(array $question, $answer): string
	{
		if (is_array($answer)) {
			$labels = array_map(static function ($item) {
				return sanitize_text_field((string) $item);
			}, $answer);
			return implode(', ', array_filter($labels));
		}

		$answer = sanitize_text_field((string) $answer);
		if ($question['type'] === 'scale' && $answer !== '') {
			return sprintf(
				/* translators: 1: value, 2: min label, 3: max label */
				__('%1$s (%2$s to %3$s)', 'reci-media-hub'),
				$answer,
				(string) $question['scale_min_label'],
				(string) $question['scale_max_label']
			);
		}

		return $answer;
	}
}

if (! function_exists('reci_media_hub_validate_assessment_submission')) {
	function reci_media_hub_validate_assessment_submission(int $post_id, array $raw_answers): array
	{
		$questions = reci_media_hub_get_assessment_questions($post_id);
		$errors    = [];
		$answers   = [];

		foreach ($questions as $question) {
			$question_id = (string) $question['id'];
			$raw_answer  = $raw_answers[$question_id] ?? null;

			if ($question['type'] === 'multiple_choice') {
				$submitted = is_array($raw_answer) ? array_values(array_map('sanitize_text_field', array_map('wp_unslash', $raw_answer))) : [];
				$submitted = array_values(array_filter($submitted));
				$valid     = array_values(array_intersect($submitted, $question['options']));

				if (! empty($question['required']) && empty($valid)) {
					$errors[$question_id] = __('Please select at least one answer.', 'reci-media-hub');
					continue;
				}

				$answers[$question_id] = [
					'question'      => (string) $question['prompt'],
					'type'          => (string) $question['type'],
					'value'         => $valid,
					'display_value' => reci_media_hub_get_assessment_submission_answer_label($question, $valid),
				];
				continue;
			}

			$submitted = is_array($raw_answer) ? '' : sanitize_textarea_field((string) wp_unslash((string) $raw_answer));

			if ($question['type'] === 'scale') {
				$value = (int) $submitted;
				if ($value < 1 || $value > (int) $question['scale_steps']) {
					$value = 0;
				}
				if (! empty($question['required']) && $value <= 0) {
					$errors[$question_id] = __('Please choose a rating before continuing.', 'reci-media-hub');
					continue;
				}

				$answers[$question_id] = [
					'question'      => (string) $question['prompt'],
					'type'          => (string) $question['type'],
					'value'         => $value > 0 ? (string) $value : '',
					'display_value' => $value > 0 ? reci_media_hub_get_assessment_submission_answer_label($question, (string) $value) : '',
				];
				continue;
			}

			if ($question['type'] === 'single_choice') {
				$submitted = sanitize_text_field($submitted);
				if ($submitted !== '' && ! in_array($submitted, $question['options'], true)) {
					$submitted = '';
				}
				if (! empty($question['required']) && $submitted === '') {
					$errors[$question_id] = __('Please select an answer before continuing.', 'reci-media-hub');
					continue;
				}
			}

			if (in_array($question['type'], ['text', 'textarea'], true) && ! empty($question['required']) && trim($submitted) === '') {
				$errors[$question_id] = __('Please enter a response before continuing.', 'reci-media-hub');
				continue;
			}

			$answers[$question_id] = [
				'question'      => (string) $question['prompt'],
				'type'          => (string) $question['type'],
				'value'         => $submitted,
				'display_value' => reci_media_hub_get_assessment_submission_answer_label($question, $submitted),
			];
		}

		return [
			'questions' => $questions,
			'errors'    => $errors,
			'answers'   => $answers,
		];
	}
}

if (! function_exists('reci_media_hub_calculate_assessment_score')) {
	function reci_media_hub_calculate_assessment_score(array $questions, array $answers): array
	{
		$score = 0;
		$max   = 0;

		foreach ($questions as $question) {
			if (! is_array($question)) {
				continue;
			}
			$type = (string) ($question['type'] ?? '');
			if (! in_array($type, ['single_choice', 'multiple_choice'], true)) {
				continue;
			}

			$correct = $question['correct_answers'] ?? [];
			if (! is_array($correct) || empty($correct)) {
				continue;
			}

			$max++;
			$question_id = (string) ($question['id'] ?? '');
			$answer_val  = $answers[$question_id]['value'] ?? '';
			$user        = is_array($answer_val) ? $answer_val : [sanitize_text_field((string) $answer_val)];
			$user        = array_values(array_filter(array_map('sanitize_text_field', $user)));
			$correct     = array_values(array_filter(array_map('sanitize_text_field', $correct)));
			sort($user);
			sort($correct);

			if (! empty($user) && $user === $correct) {
				$score++;
			}
		}

		$percentage = $max > 0 ? (int) round(($score / $max) * 100) : 0;

		return [
			'score'      => $score,
			'max_score'  => $max,
			'percentage' => $percentage,
		];
	}
}

if (! function_exists('reci_media_hub_match_assessment_band')) {
	function reci_media_hub_match_assessment_band(int $percentage, array $ranges): ?array
	{
		foreach ($ranges as $range) {
			$min = (int) ($range['min_percent'] ?? 0);
			$max = (int) ($range['max_percent'] ?? 0);
			if ($percentage >= $min && $percentage <= $max) {
				return $range;
			}
		}

		return null;
	}
}

if (! function_exists('reci_media_hub_assessment_recommendations')) {
	function reci_media_hub_assessment_recommendations(array $topic_slugs, int $limit = 6): array
	{
		$topic_slugs = array_values(array_filter(array_map('sanitize_title', $topic_slugs)));
		if (empty($topic_slugs)) {
			return [];
		}

		$tax_query = [
			[
				'taxonomy' => 'reci_topic',
				'field'    => 'slug',
				'terms'    => $topic_slugs,
			],
		];

		$learn_posts = get_posts([
			'post_type'      => 'reci_course',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => $tax_query,
			'no_found_rows'  => true,
		]);

		$remaining = max(0, $limit - count($learn_posts));
		$fallback_posts = [];
		if ($remaining > 0) {
			$fallback_posts = get_posts([
				'post_type'      => ['reci_article', 'reci_podcast', 'reci_video'],
				'post_status'    => 'publish',
				'posts_per_page' => $remaining,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'tax_query'      => $tax_query,
				'post__not_in'   => wp_list_pluck($learn_posts, 'ID'),
				'no_found_rows'  => true,
			]);
		}

		$posts = array_merge($learn_posts, $fallback_posts);
		$items = [];
		foreach ($posts as $post) {
			if (! $post instanceof WP_Post) {
				continue;
			}
			$post_id = (int) $post->ID;
			$items[] = [
				'post_id'    => $post_id,
				'post_type'  => (string) get_post_type($post_id),
				'title'      => get_the_title($post_id),
				'permalink'  => get_permalink($post_id),
				'excerpt'    => wp_trim_words(has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_strip_all_tags($post->post_content), 18, '...'),
				'image_url'  => get_the_post_thumbnail_url($post_id, 'medium') ?: '',
				'date'       => get_the_date('d M Y', $post_id),
			];
		}

		return $items;
	}
}

if (! function_exists('reci_media_hub_get_assessment_submission_result')) {
	function reci_media_hub_get_assessment_submission_result(int $assessment_id, array $answers): array
	{
		$settings = reci_media_hub_get_assessment_settings($assessment_id);
		$type     = (string) ($settings['type'] ?? 'survey');
		$ranges   = is_array($settings['result_ranges'] ?? null) ? $settings['result_ranges'] : [];

		$result = [
			'assessment_type' => $type,
			'score'           => null,
			'max_score'       => null,
			'percentage'      => null,
			'band'            => null,
			'recommendations' => [],
			'fallback_mode'   => true,
		];

		if (in_array($type, ['quiz', 'checklist'], true)) {
			$score = reci_media_hub_calculate_assessment_score((array) ($settings['questions'] ?? []), $answers);
			$result['score'] = $score['score'];
			$result['max_score'] = $score['max_score'];
			$result['percentage'] = $score['percentage'];

			if (! empty($ranges)) {
				$band = reci_media_hub_match_assessment_band((int) $score['percentage'], $ranges);
				if (is_array($band)) {
					$result['band'] = $band;
					$result['recommendations'] = reci_media_hub_assessment_recommendations((array) ($band['recommended_topics'] ?? []));
					$result['fallback_mode'] = false;
				}
			}
			return $result;
		}

		if ($type === 'survey' && ! empty($ranges)) {
			$score = reci_media_hub_calculate_assessment_score((array) ($settings['questions'] ?? []), $answers);
			if ((int) $score['max_score'] > 0) {
				$band = reci_media_hub_match_assessment_band((int) $score['percentage'], $ranges);
				if (is_array($band)) {
					$result['score'] = $score['score'];
					$result['max_score'] = $score['max_score'];
					$result['percentage'] = $score['percentage'];
					$result['band'] = $band;
					$result['recommendations'] = reci_media_hub_assessment_recommendations((array) ($band['recommended_topics'] ?? []));
					$result['fallback_mode'] = false;
				}
			}
		}

		return $result;
	}
}

if (! function_exists('reci_media_hub_create_assessment_submission')) {
	function reci_media_hub_create_assessment_submission(int $assessment_id, array $answers): int
	{
		$user_id    = get_current_user_id();
		$user       = $user_id > 0 ? get_userdata($user_id) : null;
		$respondent = $user ? (string) $user->display_name : __('Anonymous visitor', 'reci-media-hub');
		$assessment_title = sanitize_text_field((string) get_the_title($assessment_id));
		$raw_title = sprintf(
			/* translators: 1: assessment title, 2: date */
			__('Submission: %1$s (%2$s)', 'reci-media-hub'),
			$assessment_title,
			wp_date('j M Y g:i a')
		);
		$title = function_exists('mb_substr')
			? (string) mb_substr($raw_title, 0, 240)
			: (string) substr($raw_title, 0, 240);

		$insert_payload = [
			'post_type'   => reci_media_hub_get_assessment_submission_post_type(),
			'post_status' => 'private',
			'post_title'  => $title,
		];
		if ($user_id > 0) {
			$insert_payload['post_author'] = $user_id;
		}

		$submission_id = wp_insert_post($insert_payload, true);

		if (is_wp_error($submission_id) || ! $submission_id) {
			if (is_wp_error($submission_id)) {
				error_log('RECI assessment submission insert failed (private): ' . $submission_id->get_error_message());
			}

			$insert_payload['post_status'] = 'draft';
			$submission_id = wp_insert_post($insert_payload, true);

			if (is_wp_error($submission_id) || ! $submission_id) {
				if (is_wp_error($submission_id)) {
					error_log('RECI assessment submission insert failed (draft fallback): ' . $submission_id->get_error_message());
				}
				return 0;
			}
		}

		update_post_meta($submission_id, '_reci_submission_assessment_id', $assessment_id);
		update_post_meta($submission_id, '_reci_submission_answers', wp_json_encode($answers));
		update_post_meta($submission_id, '_reci_submission_user_id', $user_id);
		update_post_meta($submission_id, '_reci_submission_respondent', $respondent);

		return (int) $submission_id;
	}
}

if (! function_exists('reci_media_hub_get_assessment_submission_state')) {
	function reci_media_hub_get_assessment_submission_state(int $post_id): array
	{
		$state = [
			'submitted'     => false,
			'submission_id' => 0,
			'answers'       => [],
			'errors'        => [],
			'result'        => null,
		];

		$query_submission = isset($_GET['assessment_submitted']) ? (int) $_GET['assessment_submitted'] : 0;
		if ($query_submission > 0 && (int) get_post_meta($query_submission, '_reci_submission_assessment_id', true) === $post_id) {
			$state['submitted']     = true;
			$state['submission_id'] = $query_submission;
			$state['answers']       = reci_media_hub_get_assessment_submission_answers($query_submission);
			$state['result']        = reci_media_hub_get_assessment_submission_result($post_id, $state['answers']);
			return $state;
		}

		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
			return $state;
		}

		$assessment_id = isset($_POST['reci_assessment_id']) ? (int) $_POST['reci_assessment_id'] : 0;
		if ($assessment_id !== $post_id) {
			return $state;
		}

		$nonce = isset($_POST['reci_assessment_nonce']) ? sanitize_text_field((string) wp_unslash($_POST['reci_assessment_nonce'])) : '';
		if (! wp_verify_nonce($nonce, 'reci_submit_assessment_' . $post_id)) {
			$state['errors']['form'] = __('Your session expired. Please refresh the page and try again.', 'reci-media-hub');
			return $state;
		}

		$raw_answers = $_POST['reci_assessment_answers'] ?? [];
		if (! is_array($raw_answers)) {
			$raw_answers = [];
		}

		$validated       = reci_media_hub_validate_assessment_submission($post_id, $raw_answers);
		$state['answers'] = $validated['answers'];
		$state['errors']  = $validated['errors'];

		if (! empty($state['errors'])) {
			return $state;
		}

		$submission_id = reci_media_hub_create_assessment_submission($post_id, $validated['answers']);
		if ($submission_id <= 0) {
			$state['errors']['form'] = __('We could not save this submission. Please try again.', 'reci-media-hub');
			return $state;
		}

		wp_safe_redirect(add_query_arg('assessment_submitted', $submission_id, get_permalink($post_id) ?: home_url('/')));
		exit;
	}
}

if (! function_exists('reci_media_hub_get_assessment_submission_answers')) {
	function reci_media_hub_get_assessment_submission_answers(int $submission_id): array
	{
		$answers_raw = (string) get_post_meta($submission_id, '_reci_submission_answers', true);
		if ($answers_raw === '') {
			return [];
		}

		$decoded = json_decode($answers_raw, true);
		return is_array($decoded) ? $decoded : [];
	}
}

if (! function_exists('reci_media_hub_register_assessment_submissions_page')) {
	function reci_media_hub_register_assessment_submissions_page(): void
	{
		add_submenu_page(
			'edit.php?post_type=reci_assessment',
			__('Quiz Submissions', 'reci-media-hub'),
			__('Submissions', 'reci-media-hub'),
			'edit_posts',
			'reci-assessment-submissions',
			'reci_media_hub_render_assessment_submissions_page'
		);
	}
}

add_action('admin_menu', 'reci_media_hub_register_assessment_submissions_page');

if (! function_exists('reci_media_hub_register_assessment_rest_routes')) {
	function reci_media_hub_register_assessment_rest_routes(): void
	{
		register_rest_route(
			'reci/v1',
			'/assessment-submit',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'reci_media_hub_handle_assessment_submit_rest',
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			'reci/v1',
			'/assessment-progress',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'reci_media_hub_handle_save_assessment_progress',
				'permission_callback' => 'is_user_logged_in',
			]
		);

		register_rest_route(
			'reci/v1',
			'/assessment-progress',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'reci_media_hub_handle_get_assessment_progress',
				'permission_callback' => 'is_user_logged_in',
			]
		);
	}
}

add_action('rest_api_init', 'reci_media_hub_register_assessment_rest_routes');

if (! function_exists('reci_media_hub_handle_save_assessment_progress')) {
	function reci_media_hub_handle_save_assessment_progress(WP_REST_Request $request): WP_REST_Response
	{
		$assessment_id = (int) $request->get_param('assessment_id');
		if ($assessment_id <= 0 || get_post_type($assessment_id) !== 'reci_assessment') {
			return new WP_REST_Response(['saved' => false, 'error' => __('Invalid quiz.', 'reci-media-hub')], 400);
		}

		$answers        = $request->get_param('answers');
		$current_index  = (int) $request->get_param('current_index');

		if (! is_array($answers)) {
			$answers = [];
		}

		$user_id = get_current_user_id();
		if ($user_id <= 0) {
			return new WP_REST_Response(['saved' => false, 'error' => __('Not logged in.', 'reci-media-hub')], 401);
		}

		$meta_key = '_reci_assessment_progress_' . $assessment_id;
		$data = [
			'answers'        => $answers,
			'current_index'  => max(0, $current_index),
			'updated_at'     => current_time('mysql'),
		];

		update_user_meta($user_id, $meta_key, $data);

		return new WP_REST_Response(['saved' => true, 'error' => ''], 200);
	}
}

if (! function_exists('reci_media_hub_handle_get_assessment_progress')) {
	function reci_media_hub_handle_get_assessment_progress(WP_REST_Request $request): WP_REST_Response
	{
		$assessment_id = (int) $request->get_param('assessment_id');
		if ($assessment_id <= 0 || get_post_type($assessment_id) !== 'reci_assessment') {
			return new WP_REST_Response(['found' => false, 'error' => __('Invalid quiz.', 'reci-media-hub')], 400);
		}

		$user_id = get_current_user_id();
		if ($user_id <= 0) {
			return new WP_REST_Response(['found' => false, 'error' => __('Not logged in.', 'reci-media-hub')], 401);
		}

		$meta_key = '_reci_assessment_progress_' . $assessment_id;
		$saved = get_user_meta($user_id, $meta_key, true);

		if (! is_array($saved) || empty($saved)) {
			return new WP_REST_Response(['found' => false, 'answers' => [], 'current_index' => 0], 200);
		}

		return new WP_REST_Response([
			'found'         => true,
			'answers'       => $saved['answers'] ?? [],
			'current_index' => (int) ($saved['current_index'] ?? 0),
		], 200);
	}
}

if (! function_exists('reci_media_hub_handle_assessment_submit_rest')) {
	function reci_media_hub_handle_assessment_submit_rest(WP_REST_Request $request): WP_REST_Response
	{
		$assessment_id = (int) $request->get_param('assessment_id');
		if ($assessment_id <= 0 || get_post_type($assessment_id) !== 'reci_assessment') {
			return new WP_REST_Response(
				[
					'submitted' => false,
					'errors'    => ['form' => __('Invalid quiz request.', 'reci-media-hub')],
				],
				400
			);
		}

		$nonce = sanitize_text_field((string) $request->get_param('nonce'));
		if (! wp_verify_nonce($nonce, 'reci_submit_assessment_' . $assessment_id)) {
			return new WP_REST_Response(
				[
					'submitted' => false,
					'errors'    => ['form' => __('Your session expired. Please refresh the page and try again.', 'reci-media-hub')],
				],
				403
			);
		}

		$raw_answers = $request->get_param('answers');
		if (! is_array($raw_answers)) {
			$raw_answers = [];
		}

		$validated = reci_media_hub_validate_assessment_submission($assessment_id, $raw_answers);
		if (! empty($validated['errors'])) {
			return new WP_REST_Response(
				[
					'submitted' => false,
					'errors'    => $validated['errors'],
				],
				422
			);
		}

		$submission_id = reci_media_hub_create_assessment_submission($assessment_id, $validated['answers']);
		if ($submission_id <= 0) {
			return new WP_REST_Response(
				[
					'submitted' => false,
					'errors'    => ['form' => __('We could not save this submission. Please try again.', 'reci-media-hub')],
				],
				500
			);
		}

		$settings = reci_media_hub_get_assessment_settings($assessment_id);
		$result = reci_media_hub_get_assessment_submission_result($assessment_id, $validated['answers']);

		return new WP_REST_Response(
			[
				'submitted'          => true,
				'submission_id'      => $submission_id,
				'completion_title'   => (string) ($settings['completion_title'] ?? __('Thanks for completing this quiz.', 'reci-media-hub')),
				'completion_message' => (string) ($settings['completion_message'] ?? __('Your responses have been saved.', 'reci-media-hub')),
				'result'             => $result,
			],
			200
		);
	}
}

if (! function_exists('reci_media_hub_get_assessment_submission_query_args')) {
	function reci_media_hub_get_assessment_submission_query_args(int $assessment_id = 0, int $paged = 1): array
	{
		$args = [
			'post_type'      => reci_media_hub_get_assessment_submission_post_type(),
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'paged'          => max(1, $paged),
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if ($assessment_id > 0) {
			$args['meta_query'] = [
				[
					'key'   => '_reci_submission_assessment_id',
					'value' => $assessment_id,
				],
			];
		}

		return $args;
	}
}

if (! function_exists('reci_media_hub_render_assessment_submissions_page')) {
	function reci_media_hub_render_assessment_submissions_page(): void
	{
		if (! current_user_can('edit_posts')) {
			wp_die(esc_html__('You do not have permission to view quiz submissions.', 'reci-media-hub'));
		}

		$assessment_id = isset($_GET['assessment_id']) ? (int) $_GET['assessment_id'] : 0;
		$paged         = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
		$query         = new WP_Query(reci_media_hub_get_assessment_submission_query_args($assessment_id, $paged));
		$assessments   = get_posts([
			'post_type'      => 'reci_assessment',
			'post_status'    => ['publish', 'draft', 'pending', 'future'],
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		]);

		$export_url = wp_nonce_url(
			add_query_arg(
				[
					'action'        => 'reci_export_assessment_submissions',
					'assessment_id' => $assessment_id,
				],
				admin_url('admin-post.php')
			),
			'reci_export_assessment_submissions'
		);
?>
		<div class="wrap">
			<h1><?php esc_html_e('Quiz Submissions', 'reci-media-hub'); ?></h1>
			<form method="get" action="">
				<input type="hidden" name="post_type" value="reci_assessment" />
				<input type="hidden" name="page" value="reci-assessment-submissions" />
				<div style="display:flex; gap:12px; align-items:center; margin:16px 0;">
					<label for="assessment_id"><strong><?php esc_html_e('Quiz', 'reci-media-hub'); ?></strong></label>
					<select id="assessment_id" name="assessment_id">
						<option value="0"><?php esc_html_e('All quizzes', 'reci-media-hub'); ?></option>
						<?php foreach ($assessments as $assessment) : ?>
							<option value="<?php echo esc_attr((string) $assessment->ID); ?>" <?php selected($assessment_id, (int) $assessment->ID); ?>>
								<?php echo esc_html(get_the_title($assessment->ID)); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<button type="submit" class="button"><?php esc_html_e('Filter', 'reci-media-hub'); ?></button>
					<a class="button button-secondary" href="<?php echo esc_url($export_url); ?>"><?php esc_html_e('Export CSV', 'reci-media-hub'); ?></a>
				</div>
			</form>

			<?php if (! $query->have_posts()) : ?>
				<p><?php esc_html_e('No submissions found yet for this filter.', 'reci-media-hub'); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e('Submitted', 'reci-media-hub'); ?></th>
							<th><?php esc_html_e('Quiz', 'reci-media-hub'); ?></th>
							<th><?php esc_html_e('Respondent', 'reci-media-hub'); ?></th>
							<th><?php esc_html_e('Answers', 'reci-media-hub'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php while ($query->have_posts()) : $query->the_post(); ?>
							<?php
							$submission_id = get_the_ID();
							$linked_assessment_id = (int) get_post_meta($submission_id, '_reci_submission_assessment_id', true);
							$answers = reci_media_hub_get_assessment_submission_answers($submission_id);
							$respondent = (string) get_post_meta($submission_id, '_reci_submission_respondent', true);
							?>
							<tr>
								<td><?php echo esc_html(get_the_date('j M Y g:i a', $submission_id)); ?></td>
								<td>
									<?php if ($linked_assessment_id > 0) : ?>
										<a href="<?php echo esc_url(get_edit_post_link($linked_assessment_id) ?: ''); ?>">
											<?php echo esc_html(get_the_title($linked_assessment_id)); ?>
										</a>
									<?php else : ?>
										<?php esc_html_e('Unknown quiz', 'reci-media-hub'); ?>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html($respondent ?: __('Anonymous visitor', 'reci-media-hub')); ?></td>
								<td>
									<details>
										<summary><?php esc_html_e('View answers', 'reci-media-hub'); ?></summary>
										<table class="widefat" style="margin-top:10px;">
											<tbody>
												<?php foreach ($answers as $answer) : ?>
													<tr>
														<td style="width:40%;"><strong><?php echo esc_html((string) ($answer['question'] ?? '')); ?></strong></td>
														<td><?php echo esc_html((string) ($answer['display_value'] ?? '')); ?></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</details>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
				<?php
				echo wp_kses_post(
					paginate_links([
						'base'      => add_query_arg('paged', '%#%'),
						'format'    => '',
						'current'   => $paged,
						'total'     => (int) $query->max_num_pages,
						'type'      => 'plain',
						'add_args'  => ['assessment_id' => $assessment_id],
						'prev_text' => __('Previous', 'reci-media-hub'),
						'next_text' => __('Next', 'reci-media-hub'),
					]) ?: ''
				);
				?>
			<?php endif; ?>
		</div>
<?php
		wp_reset_postdata();
	}
}

if (! function_exists('reci_media_hub_export_assessment_submissions')) {
	function reci_media_hub_export_assessment_submissions(): void
	{
		if (! current_user_can('edit_posts')) {
			wp_die(esc_html__('You do not have permission to export quiz submissions.', 'reci-media-hub'));
		}

		check_admin_referer('reci_export_assessment_submissions');

		$assessment_id = isset($_GET['assessment_id']) ? (int) $_GET['assessment_id'] : 0;
		$query = new WP_Query(array_merge(
			reci_media_hub_get_assessment_submission_query_args($assessment_id, 1),
			[
				'posts_per_page' => -1,
				'paged'          => 1,
			]
		));

		$filename = 'assessment-submissions-' . wp_date('Ymd-His') . '.csv';

		nocache_headers();
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $filename);

		$output = fopen('php://output', 'w');
		if (! $output) {
			exit;
		}

		fputcsv($output, ['submission_id', 'submitted_at', 'assessment_id', 'assessment_title', 'respondent', 'question', 'answer']);

		while ($query->have_posts()) {
			$query->the_post();
			$submission_id = get_the_ID();
			$linked_assessment_id = (int) get_post_meta($submission_id, '_reci_submission_assessment_id', true);
			$answers = reci_media_hub_get_assessment_submission_answers($submission_id);
			$respondent = (string) get_post_meta($submission_id, '_reci_submission_respondent', true);

			foreach ($answers as $answer) {
				fputcsv(
					$output,
					[
						$submission_id,
						get_the_date('c', $submission_id),
						$linked_assessment_id,
						get_the_title($linked_assessment_id),
						$respondent,
						(string) ($answer['question'] ?? ''),
						(string) ($answer['display_value'] ?? ''),
					]
				);
			}
		}

		wp_reset_postdata();
		fclose($output);
		exit;
	}
}

add_action('admin_post_reci_export_assessment_submissions', 'reci_media_hub_export_assessment_submissions');

add_filter('post_edit_form_tag', static function (): void {
	$screen = get_current_screen();
	if ($screen && $screen->post_type === 'reci_assessment') {
		echo ' enctype="multipart/form-data"';
	}
});
