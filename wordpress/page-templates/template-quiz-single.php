<?php
/**
 * Template Name: Quiz Single (Placeholder)
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="min-h-screen bg-slate-100">
	<section class="reci-container py-12 lg:py-20 flex flex-col gap-8">
		<div class="inline-flex items-center gap-2 text-sm text-neutral-500 font-['SF_Pro_Display']">
			<span class="w-2 h-2 bg-amber-400 rounded-sm"></span>
			<span>Assessment Placeholder</span>
		</div>
		<h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-['EB_Garamond'] leading-tight">Quiz Single Page</h1>
		<p class="max-w-3xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">Static placeholder for quiz instructions and question flow. Interactive scoring logic will be wired in the assessment phase.</p>
		<div class="w-full max-w-3xl rounded-lg bg-white border border-zinc-300 p-6 lg:p-10 flex flex-col gap-6">
			<div class="text-neutral-800 text-xl font-semibold font-['EB_Garamond']">Sample Question</div>
			<div class="space-y-3">
				<label class="flex items-center gap-3"><input type="radio" disabled /> <span>Option A</span></label>
				<label class="flex items-center gap-3"><input type="radio" disabled /> <span>Option B</span></label>
				<label class="flex items-center gap-3"><input type="radio" disabled /> <span>Option C</span></label>
			</div>
			<button class="self-start px-7 py-3.5 bg-blue-900 rounded-lg text-white font-medium font-['SF_Pro_Display']">Next question</button>
		</div>
	</section>
</main>
<?php get_footer(); ?>
