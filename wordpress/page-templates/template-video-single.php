<?php
/**
 * Template Name: Video Single (Placeholder)
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
			<span>Video Placeholder</span>
		</div>
		<h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-['EB_Garamond'] leading-tight">Video Single Page</h1>
		<p class="max-w-3xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">Placeholder for featured video hero, duration metadata, description, and related content rail.</p>
		<div class="w-full h-[320px] lg:h-[480px] rounded-lg bg-neutral-800 flex items-center justify-center">
			<span class="text-white text-lg font-medium font-['SF_Pro_Display']">Video Player Placeholder</span>
		</div>
	</section>
</main>
<?php get_footer(); ?>
