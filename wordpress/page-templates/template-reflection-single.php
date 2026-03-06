<?php
/**
 * Template Name: Reflection Single (Placeholder)
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
			<span>Reflection Placeholder</span>
		</div>
		<h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-['EB_Garamond'] leading-tight">Reflection Single Page</h1>
		<p class="max-w-3xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">Static placeholder for reflection content, quote blocks, and engagement actions.</p>
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
			<div class="h-64 rounded-lg bg-zinc-200"></div>
			<div class="h-64 rounded-lg bg-zinc-200"></div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
