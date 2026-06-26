<?php

/**
 * Template Name: Quiz Single Result
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>
<main class="bg-slate-100 min-h-screen">
	<section class="bg-neutral-800 border-b border-zinc-600">
		<div class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16 flex items-center gap-3">
			<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
			<h1 class="text-white text-5xl font-bold font-heading">Racial Anxiety Quiz Result</h1>
		</div>
	</section>
	<section class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14">
		<div class="bg-white rounded-lg border-b-4 border-amber-400 p-10 flex flex-col gap-5">
			<p class="text-neutral-800 text-3xl font-bold font-heading">Your Reflection Profile: Growth in Progress</p>
			<p class="text-neutral-600 text-base font-normal leading-6 ">Static placeholder result page. Final scoring logic and recommendation mapping will be connected after data wiring.</p>
			<div class="w-full h-2 bg-zinc-200 rounded-full">
				<div class="h-2 bg-amber-400 rounded-full w-2/3"></div>
			</div>
			<div class="flex items-center gap-3">
				<a href="#" class="px-7 py-3.5 bg-[#003594] rounded-lg text-white text-base font-medium">Retake Quiz</a>
				<a href="<?php echo esc_url(home_url('/learn/')); ?>" class="px-7 py-3.5 border border-zinc-400 rounded-lg text-neutral-700 text-base font-medium">Explore Learning Resources</a>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>