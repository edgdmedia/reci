<?php
/**
 * Template Name: Author Overlay
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="bg-neutral-900 min-h-screen">
	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-20">
		<div class="bg-neutral-800 rounded-lg p-10 border border-zinc-600">
			<div class="flex items-center gap-3 mb-5">
				<span class="w-3 h-3 bg-amber-400 rounded-sm"></span>
				<h1 class="text-white text-4xl font-medium font-['EB_Garamond']">Author Overlay</h1>
			</div>
			<p class="text-zinc-300 text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">Placeholder overlay screen for author profile details. Modal interactions and live profile data are pending.</p>
		</div>
	</section>
</main>
<?php get_footer(); ?>
