<?php
/**
 * Template Name: Podcast Single (Placeholder)
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
			<span>Podcast Placeholder</span>
		</div>
		<h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-['EB_Garamond'] leading-tight">Podcast Single Page</h1>
		<p class="max-w-3xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">This static placeholder will later support episode metadata, audio player, transcript, and related episodes.</p>
		<div class="w-full rounded-lg bg-white p-6 lg:p-10 border border-zinc-300 flex flex-col gap-4">
			<div class="h-16 rounded bg-zinc-200"></div>
			<div class="h-2 rounded bg-zinc-300"></div>
			<div class="h-2 w-3/4 rounded bg-zinc-300"></div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
