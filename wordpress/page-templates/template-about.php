<?php
/**
 * Template Name: About
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="bg-slate-100 min-h-screen">
	<section class="w-full border-b border-zinc-400">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
				<h1 class="text-neutral-800 text-5xl font-medium font-['EB_Garamond'] leading-tight">About Us</h1>
			</div>
			<p class="max-w-xl lg:pl-10 lg:border-l border-zinc-400 text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7 tracking-tight">
				Placeholder version of the About page. Final copy, media, and team data will be connected in the data-wiring phase.
			</p>
		</div>
	</section>

	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 grid grid-cols-1 lg:grid-cols-3 gap-6">
		<div class="p-10 bg-blue-900 rounded-lg">
			<h2 class="text-white text-3xl font-bold font-['EB_Garamond'] mb-3">Our Mission</h2>
			<p class="text-gray-200 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">We equip individuals and communities with practical tools for racial equity learning and action.</p>
		</div>
		<div class="p-10 bg-blue-900 rounded-lg">
			<h2 class="text-white text-3xl font-bold font-['EB_Garamond'] mb-3">Our Vision</h2>
			<p class="text-gray-200 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">A future where race no longer predicts outcomes, access, or safety.</p>
		</div>
		<div class="p-10 bg-blue-900 rounded-lg">
			<h2 class="text-white text-3xl font-bold font-['EB_Garamond'] mb-3">Our Goal</h2>
			<p class="text-gray-200 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">Build an accessible media and learning hub that supports sustained growth from awareness to action.</p>
		</div>
	</section>
</main>
<?php get_footer(); ?>
