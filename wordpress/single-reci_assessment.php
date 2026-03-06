<?php
/**
 * Single template for the reci_assessment post type.
 *
 * Renders a multi-question quiz driven by _reci_assessment_questions (JSON).
 * JS in theme.js handles show/hide navigation between questions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

the_post();

$post_id = get_the_ID();

// Meta.
$intro          = (string) get_post_meta( $post_id, '_reci_assessment_intro', true );
$estimated_time = (string) get_post_meta( $post_id, '_reci_assessment_estimated_time', true );
$questions_json = (string) get_post_meta( $post_id, '_reci_assessment_questions', true );

$questions = [];
if ( $questions_json ) {
	$decoded = json_decode( $questions_json, true );
	if ( is_array( $decoded ) ) {
		$questions = $decoded;
	}
}

// Fallback so layout still renders during development.
if ( empty( $questions ) ) {
	$questions = [
		[
			'question' => __( 'No questions configured yet. Please add questions via the post editor.', 'reci-media-hub' ),
			'options'  => [],
		],
	];
}

$total = count( $questions );

$instructions = $intro ?: wp_trim_words( wp_strip_all_tags( get_the_content() ), 50, '...' );

get_header();
?>

<div class="bg-slate-100 min-h-screen font-['SF_Pro_Display']">

	<!-- Hero title bar -->
	<div class="bg-neutral-800 border-b border-zinc-600">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16 flex items-center justify-center">
			<div class="flex items-center gap-3">
				<div class="w-3 h-3 bg-amber-400 rounded-sm flex-shrink-0"></div>
				<h1 class="text-white text-5xl font-medium font-['EB_Garamond'] leading-tight">
					<?php the_title(); ?>
				</h1>
			</div>
		</div>
	</div>

	<!-- Quiz content area -->
	<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">
		<div class="flex justify-center">
			<div class="w-full max-w-3xl">

				<!-- Quiz card -->
				<div class="bg-white rounded-lg border-b-[6px] border-amber-400 overflow-hidden shadow-sm" data-quiz>

					<!-- Quiz header / amber intro block -->
					<div class="bg-amber-400 px-10 py-8 flex flex-col gap-3">
						<h2 class="text-neutral-800 text-2xl font-bold font-['EB_Garamond'] leading-7">
							<?php the_title(); ?>
						</h2>
						<?php if ( $instructions ) : ?>
							<p class="text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
								<strong class="font-bold"><?php esc_html_e( 'Instructions:', 'reci-media-hub' ); ?></strong>
								<?php echo esc_html( $instructions ); ?>
							</p>
						<?php endif; ?>
					</div>

					<!-- Progress bar -->
					<div class="px-10 pt-8" data-quiz-progress-wrapper>
						<div class="flex items-center justify-between mb-2">
							<span class="text-slate-600 text-sm font-medium font-['SF_Pro_Display']" data-quiz-progress-label>
								<?php
								printf(
									/* translators: 1: current question number, 2: total questions */
									esc_html__( 'Question %1$s of %2$s', 'reci-media-hub' ),
									'1',
									esc_html( (string) $total )
								);
								?>
							</span>
							<span class="text-slate-600 text-sm font-medium font-['SF_Pro_Display']" data-quiz-progress-pct>
								<?php echo esc_html( (string) round( 1 / $total * 100 ) ); ?>%
							</span>
						</div>
						<div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
							<div
								class="h-full bg-amber-400 rounded-full transition-all duration-500"
								data-quiz-progress-bar
								style="width: <?php echo esc_attr( (string) round( 1 / $total * 100 ) ); ?>%;"
								role="progressbar"
								aria-valuenow="1"
								aria-valuemin="1"
								aria-valuemax="<?php echo esc_attr( (string) $total ); ?>"
								aria-label="<?php esc_attr_e( 'Quiz progress', 'reci-media-hub' ); ?>"
							></div>
						</div>
					</div>

					<!-- Questions rendered as individual panels -->
					<form class="px-10 py-8 flex flex-col gap-0" action="#" method="post">
						<?php foreach ( $questions as $q_idx => $q ) :
							$q_num     = $q_idx + 1;
							$q_text    = is_string( $q ) ? $q : ( $q['question'] ?? '' );
							$q_options = is_array( $q ) ? ( $q['options'] ?? [] ) : [];
						?>
							<div
								data-quiz-question
								<?php if ( $q_idx > 0 ) : ?>class="hidden"<?php endif; ?>
							>
								<div class="pb-5 border-b border-zinc-300">
									<p class="text-slate-700 text-sm font-medium font-['SF_Pro_Display'] leading-6">
										<?php echo esc_html( $q_num . '. ' . $q_text ); ?>
									</p>

									<?php if ( ! empty( $q_options ) ) : ?>
										<fieldset class="mt-4 flex flex-col gap-0.5" aria-label="<?php esc_attr_e( 'Answer choices', 'reci-media-hub' ); ?>">
											<legend class="sr-only"><?php esc_html_e( 'Select your answer', 'reci-media-hub' ); ?></legend>
											<?php foreach ( $q_options as $option ) : ?>
												<label class="flex items-center gap-2 py-1 rounded-lg cursor-pointer group">
													<input
														type="radio"
														name="q<?php echo esc_attr( (string) $q_num ); ?>"
														value="<?php echo esc_attr( sanitize_title( $option ) ); ?>"
														class="w-5 h-5 border-2 border-zinc-300 focus:ring-amber-400 focus:ring-offset-0 cursor-pointer accent-amber-400"
													/>
													<span class="text-slate-600 text-sm font-normal font-['SF_Pro_Display'] leading-6 group-hover:text-neutral-800 transition-colors">
														<?php echo esc_html( $option ); ?>
													</span>
												</label>
											<?php endforeach; ?>
										</fieldset>
									<?php endif; ?>
								</div>

								<!-- Navigation buttons (rendered inside each question panel) -->
								<div class="flex items-center justify-between pt-6 pb-2">
									<button
										type="button"
										data-quiz-prev
										class="px-6 py-3 border border-zinc-300 rounded-lg text-neutral-700 text-sm font-medium font-['SF_Pro_Display'] hover:bg-slate-50 transition-colors disabled:opacity-40"
										<?php if ( $q_idx === 0 ) echo 'disabled'; ?>
									>
										<?php esc_html_e( 'Previous', 'reci-media-hub' ); ?>
									</button>

									<div class="flex items-center gap-2">
										<?php for ( $dot = 1; $dot <= $total; $dot++ ) : ?>
											<div class="w-2.5 h-2.5 rounded-full <?php echo $dot === $q_num ? 'bg-amber-400' : 'bg-zinc-300'; ?>"></div>
										<?php endfor; ?>
									</div>

									<?php if ( $q_idx < $total - 1 ) : ?>
										<button
											type="button"
											data-quiz-next
											class="btn btn-primary btn-md min-w-40 hover:bg-amber-500"
										>
											<?php esc_html_e( 'Next Question', 'reci-media-hub' ); ?>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
										</button>
									<?php else : ?>
										<button
											type="submit"
											class="btn btn-primary btn-md min-w-40 hover:bg-amber-500"
										>
											<?php esc_html_e( 'Submit', 'reci-media-hub' ); ?>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
										</button>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</form>
				</div>

				<!-- Quiz info strip -->
				<div class="mt-6 flex items-center justify-between text-sm text-neutral-500 font-['SF_Pro_Display']">
					<?php if ( $estimated_time ) : ?>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
								<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
							</svg>
							<span><?php echo esc_html( sprintf( __( 'Estimated time: %s', 'reci-media-hub' ), $estimated_time ) ); ?></span>
						</div>
					<?php else : ?>
						<div></div>
					<?php endif; ?>
					<a href="<?php echo esc_url( is_user_logged_in() ? home_url( '/my-account/' ) : wp_login_url( get_permalink() ) ); ?>" class="text-[#003594] hover:underline">
						<?php is_user_logged_in() ? esc_html_e( 'Save &amp; Continue Later', 'reci-media-hub' ) : esc_html_e( 'Sign in to save progress', 'reci-media-hub' ); ?>
					</a>
				</div>

			</div>
		</div>
	</div>

</div>

<?php get_footer(); ?>
