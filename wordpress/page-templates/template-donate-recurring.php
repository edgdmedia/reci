<?php
/**
 * Template Name: Donate Recurring
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
		<h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-['EB_Garamond'] leading-tight">Recurring Donation</h1>
		<p class="max-w-3xl text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">Placeholder recurring donor form. Billing cadence and payment gateway setup will be wired in the payment phase.</p>
		<div class="w-full max-w-3xl rounded-lg bg-white border border-zinc-300 p-6 lg:p-10 flex flex-col gap-4">
			<div class="inline-flex gap-3">
				<button class="px-4 py-2 rounded bg-blue-900 text-white">Monthly</button>
				<button class="px-4 py-2 rounded border border-zinc-300 text-neutral-700">Yearly</button>
			</div>
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
				<button class="px-4 py-3 rounded border border-zinc-300 text-neutral-700">$10</button>
				<button class="px-4 py-3 rounded border border-zinc-300 text-neutral-700">$25</button>
				<button class="px-4 py-3 rounded border border-zinc-300 text-neutral-700">$50</button>
				<button class="px-4 py-3 rounded border border-zinc-300 text-neutral-700">Custom</button>
			</div>
			<button class="self-start px-7 py-3.5 bg-amber-400 rounded-lg text-zinc-800 font-medium font-['SF_Pro_Display']">Start recurring donation</button>
		</div>
	</section>
</main>
<?php get_footer(); ?>
