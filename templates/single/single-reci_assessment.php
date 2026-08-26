<?php

if (!defined("ABSPATH")) {
    exit();
}

the_post();

$post_id = get_the_ID();
$display_author = reci_media_hub_get_display_author($post_id);
$submission_state = reci_media_hub_get_assessment_submission_state($post_id);
$settings = reci_media_hub_get_assessment_settings($post_id);

$type = (string) $settings["type"];
$instructions =
    (string) $settings["instructions"] ?: (string) $settings["intro"];
$estimated_time = (string) $settings["estimated_time"];
$completion_title = (string) $settings["completion_title"];
$completion_message = (string) $settings["completion_message"];
$questions = is_array($settings["questions"]) ? $settings["questions"] : [];

$total = count($questions);
$type_label =
    $type === "quiz"
        ? __("Quiz", "reci-media-hub")
        : ($type === "checklist"
            ? __("Checklist", "reci-media-hub")
            : __("Survey", "reci-media-hub"));
$instructions = $instructions ?: (string) ($settings["intro"] ?? "");
$stored_answers = $submission_state["answers"];

$submission_result = is_array($submission_state["result"] ?? null) ? $submission_state["result"] : [];

// Emergency Fallback: If score is null (not computed) or result is empty, recalculate
if ($submission_state['submitted'] && $submission_state['submission_id'] > 0 && (empty($submission_result) || !isset($submission_result['score']))) {
    $submission_result = reci_media_hub_get_assessment_submission_result($post_id, $submission_state['answers']);
}

$result_band = is_array($submission_result["band"] ?? null) ? $submission_result["band"] : [];
$result_recommendations = is_array($submission_result["recommendations"] ?? null) ? $submission_result["recommendations"] : [];

$first_error_question_id = "";
if (!empty($submission_state["errors"])) {
    foreach (array_keys($submission_state["errors"]) as $maybe_question_id) {
        if ($maybe_question_id !== "form") {
            $first_error_question_id = (string) $maybe_question_id;
            break;
        }
    }
}
$initial_question_index = 0;
if ($first_error_question_id !== "") {
    foreach ($questions as $question_index => $question) {
        if ((string) ($question["id"] ?? "") === $first_error_question_id) {
            $initial_question_index = (int) $question_index;
            break;
        }
    }
}
$initial_question_number = $initial_question_index + 1;
$initial_progress_pct = (int) round(
    ($initial_question_number / max(1, $total)) * 100,
);

$featured_image_url = (string) get_the_post_thumbnail_url($post_id, "large");
$featured_image_alt =
    (string) get_post_meta(
        get_post_thumbnail_id($post_id),
        "_wp_attachment_image_alt",
        true,
    ) ?:
    get_the_title($post_id);
$post_date = (string) get_the_date("d M Y", $post_id);
$share_url = rawurlencode(get_permalink($post_id));
$share_title = rawurlencode(get_the_title($post_id));

$category = '';
$cats = wp_get_post_categories($post_id, ['fields' => 'names']);
if (!empty($cats) && !is_wp_error($cats)) {
    $category = (string) $cats[0];
}

get_header();
?>

<main class="layout">
	<section class="reci-container">
		<div class="border-l-0 lg:border-l-[0.50px] border-b-[0.50px] border-zinc-300 flex flex-col lg:flex-row justify-start items-start gap-10">
			<div class="w-full pl-0 lg:px-10 self-stretch py-14 flex flex-col justify-start items-start gap-10 lg:border-r-[0.50px] border-zinc-300">

				<div class="self-stretch flex flex-col justify-start items-start gap-5">
					<div class="self-stretch flex flex-col justify-start items-start gap-2">
							<div class="self-stretch inline-flex justify-start items-center gap-2.5 flex-wrap">
							<a href="<?php echo esc_url(get_post_type_archive_link('reci_assessment')); ?>" class="no-underline">
								<div class="px-2 py-1 bg-amber-400 rounded flex justify-center items-center gap-2.5">
									<span class="text-neutral-800 text-sm font-normal leading-4"><?php echo esc_html(
            $type_label,
        ); ?></span>
								</div>
							</a>
							<?php if ($category !== '') : ?>
								<div class="tag-dot"></div>
								<a href="<?php echo esc_url(get_category_link(get_cat_ID($category))); ?>" class="text-neutral-500 text-sm font-normal leading-4 no-underline hover:underline"><?php echo esc_html($category); ?></a>
							<?php endif; ?>
							<div class="flex items-center gap-2.5">
								<span class="tag-dot"></span>
								<span class="text-neutral-600 text-sm font-normal leading-4"><?php echo esc_html(
            $post_date,
        ); ?></span>
							</div>
							<?php if ($estimated_time): ?>
								<div class="flex items-center gap-2.5">
									<div class="tag-dot"></div>
									<div class="flex items-center gap-0.5">
										<?php echo reci_inline_svg(
               "assets/icons/timer-outline.svg",
               "w-3.5 h-3.5 opacity-60",
               ["aria-hidden" => "true"],
           ); ?>
										<div class="text-neutral-600 text-sm font-normal"><?php echo esc_html(
               $estimated_time,
           ); ?></div>
									</div>
								</div>
							<?php endif; ?>
						</div>
						<div class="self-stretch flex flex-col gap-5">
							<h1 class="self-stretch reci-single-title capitalize"><?php the_title(); ?></h1>
							<?php
$all_spheres = reci_get_post_spheres(get_the_ID());
if (!empty($all_spheres)) : ?>
    <div class="self-stretch overflow-hidden">
        <div class="inline-flex items-center gap-1.5 flex-nowrap">
            <?php foreach ($all_spheres as $s) : ?>
								<a href="<?php echo esc_url($s['url']); ?>" class="sphere" style="background-color: <?php echo esc_attr($s['color']); ?>1a;">
                    <span class="rounded-full w-2 h-2" style="background-color: <?php echo esc_attr($s['color']); ?>;"></span>
                    <span class="font-medium" style="color: <?php echo esc_attr($s['color']); ?>;"><?php echo esc_html($s['name']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
						</div>
					</div>
				</div>

				<?php if ($featured_image_url): ?>
					<img class="self-stretch rounded-lg w-full h-[350px] object-cover object-center" src="<?php echo esc_url(
            $featured_image_url,
        ); ?>" alt="<?php echo esc_attr($featured_image_alt); ?>" />
				<?php endif; ?>



				<article class="w-full reci-post-content py-10 flex flex-col gap-6">
					<?php the_content(); ?>
				</article>

				<?php
$post_tags = wp_get_post_tags(get_the_ID(), ['fields' => 'all']);
if (!empty($post_tags) && !is_wp_error($post_tags)) : ?>
    <div class="self-stretch mt-4 flex flex-wrap items-center gap-2">
        <?php foreach ($post_tags as $tag) : ?>
							<a href="<?php echo esc_url(get_tag_link((int) $tag->term_id)); ?>" class="tag"><?php echo esc_html($tag->name); ?></a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$sdgs = wp_get_post_terms(get_the_ID(), 'sdgs');
if (!empty($sdgs) && !is_wp_error($sdgs)) : ?>
    <div class="self-stretch mt-4 flex flex-wrap items-center gap-2">
        <?php foreach ($sdgs as $sdg) : 
            $sdg_color = get_term_meta($sdg->term_id, 'sdg_color', true) ?: '#ccc';
        ?>
            <a href="<?php echo esc_url(get_term_link($sdg)); ?>" class="tag" style="background-color: <?php echo esc_attr($sdg_color); ?>; color: #fff;">
                <?php echo esc_html($sdg->name); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

				<?php if (empty($questions)): ?>
					<div class="w-full bg-white rounded-lg border-b-[6px] border-amber-400 overflow-hidden shadow-sm">
						<div class="px-10 py-10">
							<p class="text-slate-600 text-base leading-7"><?php esc_html_e(
            "No questions have been configured for this assessment yet.",
            "reci-media-hub",
        ); ?></p>
						</div>
					</div>
				<?php else: ?>

					<div class="quix-div w-full bg-white rounded-lg border border-b-[6px] border-amber-400 overflow-hidden shadow-sm" data-quiz <?php if (
          $submission_state["submitted"]
      ): ?>data-quiz-submitted<?php endif; ?>>

						<div class="w-full bg-amber-50 border-b border-amber-400 px-6 py-5 rounded">
							<h1 class="quiz-title-div text-2xl font-bold mb-4"><?php echo $submission_state[
            "submitted"
        ]
            ? esc_html($completion_title)
            : esc_html_e("Take This Quiz", "reci-media-hub"); ?></h1>
							<?php if (!$submission_state["submitted"] && $instructions): ?>
							<p class="text-neutral-800 text-base font-medium leading-6" data-quiz-instructions>
								<strong><?php esc_html_e("Instructions:", "reci-media-hub"); ?></strong>
								<?php echo esc_html($instructions); ?>
							</p>
								<?php endif; ?>
						</div>

						<?php if (!$submission_state["submitted"]): ?>
						<div class="px-10 pt-8" data-quiz-progress-wrapper>
							<div class="flex items-center justify-between mb-2">
								<span class="text-slate-600 text-sm font-medium" data-quiz-progress-label>
									<?php printf(
              esc_html__('Question %1$s of %2$s', "reci-media-hub"),
              (string) $initial_question_number,
              esc_html((string) $total),
          ); ?>
								</span>
								<span class="text-slate-600 text-sm font-medium" data-quiz-progress-pct>
									<?php echo esc_html((string) $initial_progress_pct); ?>%
								</span>
							</div>
							<div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
								<div
									class="h-full bg-amber-400 rounded-full transition-all duration-500"
									data-quiz-progress-bar
									style="width: <?php echo esc_attr((string) $initial_progress_pct); ?>%;"
									role="progressbar"
									aria-valuenow="<?php echo esc_attr((string) $initial_question_number); ?>"
									aria-valuemin="1"
									aria-valuemax="<?php echo esc_attr((string) $total); ?>"
									aria-label="<?php esc_attr_e("Quiz progress", "reci-media-hub"); ?>"></div>
							</div>
						</div>

						<form class="px-10 py-8 flex flex-col gap-0" action="<?php echo esc_url(
           get_permalink($post_id) ?: "",
       ); ?>" method="post" data-quiz-form>
							<input type="hidden" name="reci_assessment_id" value="<?php echo esc_attr(
            (string) $post_id,
        ); ?>" />
							<?php wp_nonce_field(
            "reci_submit_assessment_" . $post_id,
            "reci_assessment_nonce",
        ); ?>
							<div class="mb-4 hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" data-quiz-form-error></div>

							<?php foreach ($questions as $q_idx => $question):

            $q_num = $q_idx + 1;
            $question_id = (string) ($question["id"] ?? "question-" . $q_num);
            $question_type = (string) ($question["type"] ?? "text");
            $question_prompt = (string) ($question["prompt"] ?? "");
            $question_help = (string) ($question["help_text"] ?? "");
            $question_options = is_array($question["options"] ?? null)
                ? $question["options"]
                : [];
            $is_required = !empty($question["required"]);
            $current_answer = $stored_answers[$question_id]["value"] ?? "";
            $field_name = "reci_assessment_answers[" . $question_id . "]";
            $error_message = $submission_state["errors"][$question_id] ?? "";
            $is_active_question =
                $first_error_question_id !== ""
                    ? $first_error_question_id === $question_id
                    : $q_idx === 0;
            ?>
								<div data-quiz-question data-question-id="<?php echo esc_attr(
             $question_id,
         ); ?>" data-required="<?php echo $is_required
     ? "true"
     : "false"; ?>" <?php if (
     !$is_active_question
 ): ?>class="hidden"<?php endif; ?>>
									<div class="pb-5 border-b border-zinc-300">
										<p class="text-slate-700 text-lg font-medium leading-7">
											<?php echo esc_html($q_num . ". " . $question_prompt); ?>
										</p>
										<?php if ($question_help): ?>
											<p class="mt-2 text-sm leading-6 text-slate-500"><?php echo esc_html(
                $question_help,
            ); ?></p>
										<?php endif; ?>

										<?php if ($question_type === "scale"): ?>
											<fieldset class="mt-5 flex flex-col gap-3" aria-label="<?php esc_attr_e(
                "Rating scale",
                "reci-media-hub",
            ); ?>">
												<div class="flex items-center justify-between gap-4 text-xs font-medium uppercase tracking-wide text-slate-500">
													<span><?php echo esc_html(
                   (string) ($question["scale_min_label"] ?? ""),
               ); ?></span>
													<span><?php echo esc_html(
                   (string) ($question["scale_max_label"] ?? ""),
               ); ?></span>
												</div>
												<div class="grid gap-2" style="grid-template-columns: repeat(<?php echo esc_attr(
                  (string) max(2, min(10, (int) ($question["scale_steps"] ?? 5))),
              ); ?>, minmax(0, 1fr));">
													<?php for (
                   $step = 1;
                   $step <= (int) ($question["scale_steps"] ?? 5);
                   $step++
               ): ?>
														<label class="flex flex-col items-center gap-2 rounded-lg border border-zinc-200 px-3 py-3 text-center cursor-pointer hover:border-amber-400 hover:bg-amber-50">
															<input type="radio" name="<?php echo esc_attr(
                     $field_name,
                 ); ?>" value="<?php echo esc_attr(
     (string) $step,
 ); ?>" class="accent-amber-400" <?php checked(
     (string) $current_answer,
     (string) $step,
 ); ?> />
															<span class="text-sm font-medium text-neutral-800"><?php echo esc_html(
                     (string) $step,
                 ); ?></span>
														</label>
													<?php endfor; ?>
												</div>
											</fieldset>
										<?php elseif ($question_type === "single_choice"): ?>
											<fieldset class="mt-4 flex flex-col gap-2" aria-label="<?php esc_attr_e(
                "Answer choices",
                "reci-media-hub",
            ); ?>">
												<?php foreach ($question_options as $option): ?>
													<label class="flex items-center gap-2 py-1 rounded-lg cursor-pointer group">
														<input type="radio" name="<?php echo esc_attr(
                    $field_name,
                ); ?>" value="<?php echo esc_attr(
     (string) $option,
 ); ?>" class="w-5 h-5 border-2 border-zinc-300 focus:ring-amber-400 focus:ring-offset-0 cursor-pointer accent-amber-400" <?php checked(
     (string) $current_answer,
     (string) $option,
 ); ?> />
														<span class="text-slate-600 text-sm font-normal leading-6 group-hover:text-neutral-800 transition-colors"><?php echo esc_html(
                    (string) $option,
                ); ?></span>
													</label>
												<?php endforeach; ?>
											</fieldset>
										<?php elseif ($question_type === "multiple_choice"): ?>
											<?php $current_multi = is_array($current_answer) ? $current_answer : []; ?>
											<fieldset class="mt-4 flex flex-col gap-2" aria-label="<?php esc_attr_e(
                "Answer choices",
                "reci-media-hub",
            ); ?>">
												<?php foreach ($question_options as $option): ?>
													<label class="flex items-center gap-2 py-1 rounded-lg cursor-pointer group">
														<input type="checkbox" name="<?php echo esc_attr(
                    $field_name,
                ); ?>[]" value="<?php echo esc_attr(
     (string) $option,
 ); ?>" class="w-5 h-5 border-2 border-zinc-300 focus:ring-amber-400 focus:ring-offset-0 cursor-pointer accent-amber-400" <?php checked(
     in_array((string) $option, $current_multi, true),
 ); ?> />
														<span class="text-slate-600 text-sm font-normal leading-6 group-hover:text-neutral-800 transition-colors"><?php echo esc_html(
                    (string) $option,
                ); ?></span>
													</label>
												<?php endforeach; ?>
											</fieldset>
										<?php elseif ($question_type === "textarea"): ?>
											<textarea name="<?php echo esc_attr(
                 $field_name,
             ); ?>" rows="6" class="mt-4 w-full rounded-lg border border-zinc-300 px-4 py-3 text-sm text-neutral-800 focus:border-amber-400 focus:outline-none focus:ring-0"><?php echo esc_textarea(
     is_array($current_answer) ? "" : (string) $current_answer,
 ); ?></textarea>
										<?php else: ?>
											<input type="text" name="<?php echo esc_attr(
                 $field_name,
             ); ?>" value="<?php echo esc_attr(
     is_array($current_answer) ? "" : (string) $current_answer,
 ); ?>" class="mt-4 w-full rounded-lg border border-zinc-300 px-4 py-3 text-sm text-neutral-800 focus:border-amber-400 focus:outline-none focus:ring-0" />
										<?php endif; ?>

										<div class="mt-3 text-sm text-red-600 <?php echo $error_message
                ? ""
                : "hidden"; ?>" data-quiz-error>
											<?php echo esc_html((string) $error_message); ?>
										</div>
									</div>

									<div class="flex items-center justify-between pt-6 pb-2">
										<button type="button" data-quiz-prev class="px-6 py-3 border border-zinc-300 rounded-lg text-neutral-700 text-sm font-medium hover:bg-slate-50 transition-colors disabled:opacity-40" <?php if (
                $q_idx === 0
            ) {
                echo "disabled";
            } ?>>
											<?php esc_html_e("Previous", "reci-media-hub"); ?>
										</button>
										<div class="flex items-center gap-2">
											<?php for ($dot = 1; $dot <= $total; $dot++): ?>
												<div class="w-2.5 h-2.5 rounded-full <?php echo $dot === $q_num
                  ? "bg-amber-400"
                  : "bg-zinc-300"; ?>"></div>
											<?php endfor; ?>
										</div>
										<?php if ($q_idx < $total - 1): ?>
											<button type="button" data-quiz-next class="btn btn-primary btn-md min-w-40 hover:bg-amber-500">
												<?php esc_html_e("Next Question", "reci-media-hub"); ?>
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
												</svg>
											</button>
										<?php else: ?>
											<button type="submit" class="btn btn-primary btn-md min-w-40 hover:bg-amber-500">
												<?php esc_html_e("Submit", "reci-media-hub"); ?>
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
												</svg>
											</button>
										<?php endif; ?>
									</div>

									<div class="flex  items-center justify-center pt-3">
										<button type="button" data-quiz-save-progress class="text-sm text-neutral-600 hover:text-neutral-800 underline underline-offset-2 transition-colors">
										<svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
										</svg>
											<?php esc_html_e("Save &amp; Continue Later", "reci-media-hub"); ?>
										</button>
									</div>
								</div>
							<?php
         endforeach; ?>
						</form>
						<?php endif; ?>

						<div class="px-10 py-10 flex-col gap-4 <?php echo $submission_state["submitted"] ? "" : "hidden"; ?>" data-quiz-success>
							<?php
							echo '<!-- RECI Result Payload: ' . esc_html(wp_json_encode($submission_result)) . ' -->';
							?>

							<div class="mb-6">
								<h2 class="text-neutral-800 text-2xl font-bold font-serif mb-2"><?php esc_html_e('Your Results', 'reci-media-hub'); ?></h2>
								<div class="h-1 w-20 bg-amber-400 rounded"></div>
							</div>

							<p class="text-neutral-800 text-xl font-bold leading-8 <?php echo isset($submission_result["score"]) ? "" : "hidden"; ?>" data-quiz-score>
								<?php if (isset($submission_result["score"])) {
                    echo esc_html(sprintf(__("You scored %1\$s/%2\$s (%3\$s%%)", "reci-media-hub"), (string) ((int) ($submission_result["score"] ?? 0)), (string) ((int) ($submission_result["max_score"] ?? 0)), (string) ((int) ($submission_result["percentage"] ?? 0))));
                } ?>
							</p>

							<div class="mt-3 p-5 rounded-lg border border-zinc-200 bg-zinc-50 flex flex-col gap-2 <?php echo empty($result_band) ? "hidden" : ""; ?>" data-quiz-band>
								<p class="text-neutral-800 text-lg font-bold leading-6" data-quiz-band-label><?php echo esc_html((string) ($result_band["label"] ?? "")); ?></p>
								<p class="text-neutral-600 text-base leading-7" data-quiz-band-message><?php echo esc_html((string) ($result_band["message"] ?? "")); ?></p>
							</div>

							<p class="text-slate-600 text-base leading-7" data-quiz-success-message><?php echo esc_html($completion_message); ?></p>

							<div class="mt-4 flex flex-col gap-4 <?php echo empty($result_recommendations) ? "hidden" : ""; ?>" data-quiz-recommendations>
								<h3 class="text-neutral-800 text-xl font-bold font-serif leading-7"><?php esc_html_e("Recommended Resources", "reci-media-hub"); ?></h3>
								<div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-quiz-recommendations-grid>
									<?php if (!empty($result_recommendations)):
                    foreach ($result_recommendations as $item):
                        $item_title = (string) ($item["title"] ?? "");
                        $item_link = (string) ($item["permalink"] ?? "");
                        $item_excerpt = (string) ($item["excerpt"] ?? "");
                        $item_date = (string) ($item["date"] ?? "");
                        $item_image = (string) ($item["image_url"] ?? "");
                        if ($item_title === "" || $item_link === "") { continue; }
                    ?>
										<a href="<?php echo esc_url($item_link); ?>" class="no-underline rounded-lg border border-zinc-200 overflow-hidden bg-white hover:shadow-sm transition-shadow">
											<?php if ($item_image !== ""): ?>
												<img src="<?php echo esc_url($item_image); ?>" alt="<?php echo esc_attr($item_title); ?>" class="w-full h-32 object-cover" />
											<?php endif; ?>
											<div class="p-4 flex flex-col gap-2">
												<?php if ($item_date !== ""): ?>
													<p class="text-neutral-500 text-xs font-medium uppercase tracking-wide"><?php echo esc_html($item_date); ?></p>
												<?php endif; ?>
												<p class="text-neutral-800 text-base font-bold leading-6"><?php echo esc_html($item_title); ?></p>
												<?php if ($item_excerpt !== ""): ?>
													<p class="text-neutral-600 text-sm leading-6"><?php echo esc_html($item_excerpt); ?></p>
												<?php endif; ?>
											</div>
										</a>
									<?php endforeach;
                  endif; ?>
								</div>
							</div>
							<a href="<?php echo esc_url(
             get_post_type_archive_link("reci_assessment") ?:
  home_url("/quizzes/"),
          ); ?>" class="btn btn-primary btn-md w-fit mt-6">
								<?php esc_html_e("Back to Quizzes", "reci-media-hub"); ?>
							</a>
						</div>
					</div>
				<?php endif; ?>

					<div class="w-full flex items-center justify-between text-sm text-neutral-600 mt-4">
						<?php if ($estimated_time): ?>
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<circle cx="12" cy="12" r="10" />
									<polyline points="12 6 12 12 16 14" />
								</svg>
								<span><?php echo esc_html(
              sprintf(
                  __("Estimated time: %s", "reci-media-hub"),
                  $estimated_time,
              ),
          ); ?></span>
							</div>
						<?php endif; ?>
					</div>
			</div>

			<aside class="w-full lg:w-1/3 lg:py-14 lg:self-start lg:sticky lg:top-10">
				<div class="flex flex-col gap-10">


				<?php if (! is_user_logged_in()): ?>
					<div class="bg-neutral-800 p-5 flex flex-col gap-5">
						<div class="flex items-center gap-2">
							<svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
							</svg>
							<div class="text-white text-lg font-bold font-subhead leading-6"><?php esc_html_e(
              "Save Your Progress",
              "reci-media-hub",
          ); ?></div>
						</div>
						<p class="text-zinc-300 text-sm leading-5">
							<?php esc_html_e(
              "Sign in to save your answers and continue later.",
              "reci-media-hub",
          ); ?>
						</p>
						<div class="flex flex-col gap-3">
							<a href="<?php echo esc_url(
               wp_login_url(get_permalink($post_id)),
           ); ?>" class="btn btn-primary btn-sm w-full text-center"><?php esc_html_e(
               "Sign In",
               "reci-media-hub",
           ); ?></a>
						<a href="<?php echo esc_url(
               reci_get_sign_up_url(),
           ); ?>" class="text-center text-zinc-400 hover:text-white underline underline-offset-2 transition-colors text-xs"><?php esc_html_e(
               "Create an Account",
               "reci-media-hub",
           ); ?></a>
						</div>
					</div>
				<?php endif; ?>


					<div class="flex flex-col gap-5">
						<div class="flex items-center gap-2">
							<span class="text-neutral-800 text-lg font-bold font-subhead uppercase tracking-wider"><?php esc_html_e(
               "Share",
               "reci-media-hub",
           ); ?></span>
						</div>
						<div class="flex items-center gap-3">
							<button type="button" onclick="navigator.clipboard.writeText(window.location.href)" class="w-9 h-9 bg-gray-100 rounded-full flex justify-center items-center hover:bg-gray-200 transition-colors" aria-label="<?php esc_attr_e(
               "Copy link",
               "reci-media-hub",
           ); ?>">
								<svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
								</svg>
							</button>
							<a href="<?php echo esc_url(
               "https://www.facebook.com/sharer/sharer.php?u=" . $share_url,
           ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-gray-100 rounded-full flex justify-center items-center hover:bg-gray-200 transition-colors" aria-label="<?php esc_attr_e(
               "Share on Facebook",
               "reci-media-hub",
           ); ?>">
								<svg class="w-4 h-4 text-neutral-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
								</svg>
							</a>
							<a href="<?php echo esc_url(
               "https://twitter.com/intent/tweet?url=" .
  $share_url .
  "&text=" .
  $share_title,
           ); ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-gray-100 rounded-full flex justify-center items-center hover:bg-gray-200 transition-colors" aria-label="<?php esc_attr_e(
               "Share on X",
               "reci-media-hub",
           ); ?>">
								<svg class="w-4 h-4 text-neutral-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
								</svg>
							</a>
						</div>
					</div>

					<?php if (! empty($display_author['name'])) : ?>
						<div class="flex flex-col gap-6">
							<div class="flex items-center gap-2">
								<span class="text-neutral-800 text-lg font-bold font-subhead uppercase tracking-wider"><?php esc_html_e(
               "Author",
               "reci-media-hub",
           ); ?></span>
							</div>
							<div class="flex items-start gap-4">
								<?php if (! empty($display_author['image_url'])) : ?>
									<img src="<?php echo esc_url(
                 (string) $display_author['image_url'],
             ); ?>" alt="<?php echo esc_attr(
  (string) ($display_author['image_alt'] ?? $display_author['name']),
); ?>" class="w-16 h-16 rounded-full object-cover flex-shrink-0" />
								<?php endif; ?>
								<div class="flex flex-col gap-1">
									<span class="text-neutral-800 text-base font-bold"><?php echo esc_html(
               (string) $display_author['name'],
           ); ?></span>
									<?php if (! empty($display_author['title'])) : ?>
										<span class="text-neutral-500 text-sm"><?php echo esc_html(
               (string) $display_author['title'],
           ); ?></span>
									<?php endif; ?>
								</div>
							</div>
							<?php if (! empty($display_author['bio'])) : ?>
								<p class="text-neutral-600 text-sm leading-6"><?php echo esc_html(
            (string) $display_author['bio'],
        ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>
			</aside>
		</div>
	</section>
</main>

<script>
(function() {
	const isLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
	const progressEndpoint = <?php echo wp_json_encode(esc_url_raw(rest_url('reci/v1/assessment-progress'))); ?>;
	const restNonce = <?php echo wp_json_encode(wp_create_nonce('wp_rest')); ?>;
	const wrap = document.querySelector('[data-quiz]');
	if (!wrap) return;

	const form = wrap.querySelector('[data-quiz-form]');
	const questions = wrap.querySelectorAll('[data-quiz-question]');
	const progressBar = wrap.querySelector('[data-quiz-progress-bar]');
	const progressLabel = wrap.querySelector('[data-quiz-progress-label]');
	const progressPct = wrap.querySelector('[data-quiz-progress-pct]');
	const formError = wrap.querySelector('[data-quiz-form-error]');
	const successBlock = wrap.querySelector('[data-quiz-success]');
	const nextBtns = wrap.querySelectorAll('[data-quiz-next]');
	const prevBtns = wrap.querySelectorAll('[data-quiz-prev]');
	const saveBtns = wrap.querySelectorAll('[data-quiz-save-progress]');
	const modal = document.getElementById('reci-auth-modal');
	const total = questions.length;

	if (!total) return;

	let currentIndex = 0;

	function showQuestion(index) {
		questions.forEach((q, i) => {
			q.classList.toggle('hidden', i !== index);
		});
		currentIndex = index;
		updateProgress(index);
	}

	function updateProgress(index) {
		const num = index + 1;
		const pct = Math.round(num / total * 100);
		if (progressLabel) progressLabel.textContent = '<?php echo esc_js(
        __("Question", "reci-media-hub"),
    ); ?> ' + num + ' of ' + total;
		if (progressPct) progressPct.textContent = pct + '%';
		if (progressBar) {
			progressBar.style.width = pct + '%';
			progressBar.setAttribute('aria-valuenow', String(num));
		}
	}

	function hideFormError() {
		if (formError) formError.classList.add('hidden');
	}

	function collectAnswers() {
		const data = {};
		questions.forEach((q) => {
			const id = q.getAttribute('data-question-id');
			if (!id) return;
			const radios = q.querySelectorAll('input[type="radio"]:checked');
			if (radios.length) {
				data[id] = radios[0].value;
				return;
			}
			const checkboxes = q.querySelectorAll('input[type="checkbox"]:checked');
			if (checkboxes.length) {
				data[id] = Array.from(checkboxes).map(c => c.value);
				return;
			}
			const textareas = q.querySelectorAll('textarea');
			if (textareas.length) {
				const val = textareas[0].value.trim();
				if (val) data[id] = val;
				return;
			}
			const inputs = q.querySelectorAll('input[type="text"]');
			if (inputs.length) {
				const val = inputs[0].value.trim();
				if (val) data[id] = val;
				return;
			}
		});
		return data;
	}

	function saveToLocalStorage(answers, index) {
		try {
			const postId = <?php echo (int) $post_id; ?>;
			localStorage.setItem('reci_assessment_' + postId, JSON.stringify({
				answers: answers,
				current_index: index,
				updated_at: new Date().toISOString()
			}));
		} catch(e) {}
	}

	function handleSave() {
		const answers = collectAnswers();
		if (isLoggedIn && progressEndpoint) {
			fetch(progressEndpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': restNonce },
				body: JSON.stringify({ assessment_id: <?php echo (int) $post_id; ?>, answers: answers, current_index: currentIndex })
			})
			.then(r => r.json())
			.then(data => {
				if (data.saved) {
					const msg = document.createElement('div');
					msg.className = 'fixed bottom-6 right-6 bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg text-sm z-50';
					msg.textContent = '<?php echo esc_js(__("Progress saved!", "reci-media-hub")); ?>';
					document.body.appendChild(msg);
					setTimeout(() => msg.remove(), 2500);
				}
			});
		}
	}

	nextBtns.forEach(btn => btn.addEventListener('click', () => {
		const next = currentIndex + 1;
		if (next < total) showQuestion(next);
	}));

	prevBtns.forEach(btn => btn.addEventListener('click', () => {
		const prev = currentIndex - 1;
		if (prev >= 0) showQuestion(prev);
	}));

	saveBtns.forEach(btn => btn.addEventListener('click', e => {
		e.preventDefault();
		handleSave();
	}));

	if (form) {
		form.addEventListener('submit', () => {
			console.log('RECI Debug: Form submit triggered');
			hideFormError();
		});
	}

	const startIndex = <?php echo $first_error_question_id !== "" ? (int) $initial_question_index : "0"; ?>;
	showQuestion(startIndex);
})();
</script>

<!-- RECI Diagnostic: Template v1.3-feedback-fixed -->
<?php get_footer(); ?>
