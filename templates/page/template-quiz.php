<?php

/**
 * Template Name: Quizzes
 *
 * Displays the Quizzes & Learning page with a grid of quiz cards.
 * All content is hardcoded placeholder.
 */

if (! defined('ABSPATH')) {
	exit;
}

$quiz_cards = [
	[
		'title'       => 'Racial Anxiety Quiz',
		'description' => 'Assess your comfort levels in cross-racial interactions and identify areas where racial anxiety might be present.',
		'time'        => '10 mins',
		'difficulty'  => 'Beginner',
		'url'         => '#',
	],
	[
		'title'       => 'Privilege Checklist',
		'description' => 'Explore unearned advantages based on social identities and reflect on systemic inequities.',
		'time'        => '15 mins',
		'difficulty'  => 'Intermediate',
		'url'         => '#',
	],
	[
		'title'       => 'Implicit Bias Survey',
		'description' => 'Uncover subconscious associations affecting your perceptions, decisions, and behaviors.',
		'time'        => '12 mins',
		'difficulty'  => 'Intermediate',
		'url'         => '#',
	],
	[
		'title'       => 'Equity Consciousness Quiz',
		'description' => 'Measure your current level of awareness and understanding of racial equity principles.',
		'time'        => '20 mins',
		'difficulty'  => 'Advanced',
		'url'         => '#',
	],
	[
		'title'       => 'Allyship Readiness Check',
		'description' => 'Evaluate how prepared you are to actively support and advocate for marginalized communities.',
		'time'        => '8 mins',
		'difficulty'  => 'Beginner',
		'url'         => '#',
	],
	[
		'title'       => 'Cultural Competency Benchmark',
		'description' => 'Benchmark your ability to effectively interact with people across cultures and backgrounds.',
		'time'        => '25 mins',
		'difficulty'  => 'Advanced',
		'url'         => '#',
	],
];

$difficulty_colors = [
	'Beginner'     => 'bg-emerald-100 text-emerald-700',
	'Intermediate' => 'bg-amber-100 text-amber-700',
	'Advanced'     => 'bg-red-100 text-red-700',
];

get_header();
?>

<div class="bg-slate-100 min-h-screen">

	<!-- Hero / Page Title -->
	<div class="border-b border-zinc-300">
		<div class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14">
			<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
				<div class="flex items-center gap-3">
					<div class="w-3 h-3 bg-amber-400 rounded-sm flex-shrink-0"></div>
					<h1 class="text-neutral-800 text-5xl font-bold font-heading leading-tight">
						<?php esc_html_e('Check Your Lens', 'reci-media-hub'); ?>
					</h1>
				</div>
				<div class="lg:pl-10 lg:border-l lg:border-zinc-400">
					<p class="text-neutral-600 text-lg font-normal leading-7  max-w-lg">
						<?php esc_html_e('Explore our collection of self-reflection quizzes designed to help you understand your own perspectives, biases, and readiness to engage with racial equity.', 'reci-media-hub'); ?>
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Filter Bar -->
	<div class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 pt-6 pb-5 border-b border-zinc-300">
		<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
			<div class="flex items-center gap-3">
				<div class="w-72 px-5 py-4 bg-white rounded-lg flex items-center gap-2.5">
					<svg class="w-4 h-4 text-neutral-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
					</svg>
					<span class="text-neutral-600 text-base font-light">
						<?php esc_html_e('Search Quizzes', 'reci-media-hub'); ?>
					</span>
				</div>
			</div>
			<div class="flex items-center gap-4">
				<span class="text-neutral-800 text-sm font-medium">
					<?php esc_html_e('Difficulty:', 'reci-media-hub'); ?>
				</span>
				<div class="flex items-center gap-2">
					<button type="button" class="px-3 py-1.5 bg-[#003594] text-white text-sm rounded">
						<?php esc_html_e('All', 'reci-media-hub'); ?>
					</button>
					<button type="button" class="px-3 py-1.5 bg-white border border-zinc-300 text-neutral-700 text-sm rounded hover:bg-slate-50">
						<?php esc_html_e('Beginner', 'reci-media-hub'); ?>
					</button>
					<button type="button" class="px-3 py-1.5 bg-white border border-zinc-300 text-neutral-700 text-sm rounded hover:bg-slate-50">
						<?php esc_html_e('Intermediate', 'reci-media-hub'); ?>
					</button>
					<button type="button" class="px-3 py-1.5 bg-white border border-zinc-300 text-neutral-700 text-sm rounded hover:bg-slate-50">
						<?php esc_html_e('Advanced', 'reci-media-hub'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Assessment Cards Grid -->
	<div class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10 pb-16">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
			<?php foreach ($quiz_cards as $quiz) : ?>
				<div class="bg-neutral-800 rounded-lg px-10 pt-20 pb-10 flex flex-col justify-between gap-8 min-h-[320px]">
					<div class="flex flex-col gap-5">
						<!-- Icon -->
						<div class="p-2.5 bg-neutral-600 rounded-lg inline-flex items-center justify-center w-fit">
							<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" fill="#F59E0B" />
							</svg>
						</div>

						<!-- Text content -->
						<div class="flex flex-col gap-2.5">
							<h2 class="text-white text-2xl font-bold font-heading leading-7">
								<?php echo esc_html($quiz['title']); ?>
							</h2>
							<p class="text-gray-200 text-sm font-normal leading-6">
								<?php echo esc_html($quiz['description']); ?>
							</p>
						</div>

						<!-- Meta: time + difficulty -->
						<div class="flex items-center gap-4">
							<div class="flex items-center gap-1.5 text-zinc-400 text-xs font-normal">
								<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<circle cx="12" cy="12" r="10" />
									<polyline points="12 6 12 12 16 14" />
								</svg>
								<span><?php echo esc_html($quiz['time']); ?></span>
							</div>
							<span class="<?php echo esc_attr($difficulty_colors[$quiz['difficulty']] ?? 'bg-slate-100 text-slate-600'); ?> text-xs font-medium px-2 py-0.5 rounded">
								<?php echo esc_html($quiz['difficulty']); ?>
							</span>
						</div>
					</div>

					<!-- CTA -->
					<a
						href="<?php echo esc_url($quiz['url']); ?>"
						class="btn btn-primary btn-md w-fit hover:bg-amber-500">
						<?php esc_html_e('Start Quiz', 'reci-media-hub'); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- Connect CTA Banner -->
	<div class="bg-neutral-800">
		<div class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-20">
			<div class="flex flex-col lg:flex-row items-start gap-10">
				<img
					src="https://placehold.co/415x347"
					alt=""
					class="flex-shrink-0 rounded-lg border-b-[11px] border-amber-400 w-full lg:w-auto lg:max-w-xs"
					aria-hidden="true" />
				<div class="flex flex-col gap-8">
					<div class="flex flex-col gap-3">
						<span class="px-2 py-1 bg-amber-400 rounded text-neutral-800 text-sm font-normal leading-4 inline-block w-fit">
							<?php esc_html_e('Connect Elements', 'reci-media-hub'); ?>
						</span>
						<h2 class="text-white text-5xl lg:text-6xl font-bold font-heading leading-tight max-w-2xl">
							<?php esc_html_e('Connect your interests to improve your feed and discover more relevant content', 'reci-media-hub'); ?>
						</h2>
					</div>
					<a
						href="#"
						class="min-w-44 px-7 py-3.5 bg-amber-400 rounded-lg text-neutral-800 text-base font-medium leading-6 inline-flex justify-center items-center w-fit hover:bg-amber-500 transition-colors">
						<?php esc_html_e('Connect Now', 'reci-media-hub'); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

</div>

<?php get_footer(); ?>