<?php

/**
 * Template Name: Donate - Other Ways
 *
 * Other Ways to Give page for the Donate section.
 * Placeholder — real content deferred.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

// Sibling page URLs
$donate_url     = home_url('/donate/');
$benefits_url   = home_url('/donate/benefits/');
$faq_url        = home_url('/donate/faq/');
$other_ways_url = get_permalink() ?: home_url('/donate/other-ways/');

get_header();
?>

<div class="bg-slate-100 min-h-screen font-['SF_Pro_Display']">

	<!-- =========================================================
	     PAGE HERO
	     ========================================================= -->
	<section class="bg-[#003594]">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5 border-b border-white/20">
			<div class="flex items-center gap-3">
				<div class="w-3 h-3 bg-amber-400 rounded-sm shrink-0"></div>
				<h1 class="text-5xl font-medium font-['EB_Garamond'] leading-[1.05] text-white">
					<?php echo esc_html__('Donate', 'reci-media-hub'); ?>
				</h1>
			</div>
			<p class="text-lg font-normal text-white/70 max-w-xl leading-7 tracking-tight sm:pl-10 sm:border-l sm:border-white/30">
				<?php echo esc_html__('There are many ways to support RECI\'s mission. Explore the options below and find the giving method that works best for you.', 'reci-media-hub'); ?>
			</p>
		</div>
	</section>

	<!-- =========================================================
	     MAIN CONTENT — sidebar + Other Ways
	     ========================================================= -->
	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10 pb-24">
		<div class="flex flex-col lg:flex-row gap-14 items-start">

			<!-- LEFT SIDEBAR -->
			<aside class="w-full lg:w-96 flex flex-col gap-10 shrink-0">

				<nav class="w-full p-10 bg-[#003594] rounded-lg flex flex-col gap-10 overflow-hidden" aria-label="Donation page navigation">

					<a href="<?php echo esc_url($donate_url); ?>" class="flex flex-col gap-3 opacity-60 transition-opacity hover:opacity-100">
						<div class="flex items-center gap-2">
							<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
							<span class="text-slate-300 text-2xl font-bold font-['EB_Garamond'] leading-7">
								<?php echo esc_html__('Donate', 'reci-media-hub'); ?>
							</span>
						</div>
					</a>

					<a href="<?php echo esc_url($benefits_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-['EB_Garamond'] leading-7">
							<?php echo esc_html__('Donor Benefits', 'reci-media-hub'); ?>
						</span>
					</a>

					<a href="<?php echo esc_url($faq_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-['EB_Garamond'] leading-7">
							<?php echo esc_html__('Frequently Asked Questions', 'reci-media-hub'); ?>
						</span>
					</a>

					<!-- Active -->
					<div class="flex flex-col gap-3">
						<div class="flex items-center gap-2">
							<div class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
							<span class="text-white text-2xl font-bold font-['EB_Garamond'] leading-7">
								<?php echo esc_html__('Other Ways to Give', 'reci-media-hub'); ?>
							</span>
						</div>
						<div class="w-full h-0.5 bg-white"></div>
					</div>

				</nav>

				<!-- Mailing a Cheque card -->
				<div class="w-full p-10 bg-neutral-800 rounded-lg flex flex-col gap-5">
					<div class="p-4 bg-neutral-600 rounded-full inline-flex justify-center items-center self-start">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
						</svg>
					</div>
					<div class="flex flex-col gap-3">
						<h2 class="text-2xl font-bold font-['EB_Garamond'] text-white leading-7">
							<?php echo esc_html__('Mailing a Cheque?', 'reci-media-hub'); ?>
						</h2>
						<p class="text-base font-medium font-['SF_Pro_Display'] text-gray-200 leading-6 tracking-tight">
							<?php echo esc_html__('Kindly follow this instruction:', 'reci-media-hub'); ?><br />
							<a href="<?php echo esc_url($faq_url); ?>" class="underline hover:text-amber-400 transition-colors">
								<?php echo esc_html__('Donation FAQ', 'reci-media-hub'); ?>
							</a>
						</p>
					</div>
				</div>

			</aside>

			<!-- RIGHT — Other Ways content -->
			<div class="flex-1 flex flex-col gap-8">

				<div class="flex items-center gap-2">
					<div class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
					<h2 class="text-4xl font-bold font-['EB_Garamond'] text-neutral-800 leading-[1.05]">
						<?php echo esc_html__('Other Ways to Give', 'reci-media-hub'); ?>
					</h2>
				</div>

				<?php
				$other_ways = [
					[
						'title' => 'Stock &amp; Securities',
						'body'  => 'Donating appreciated stocks, bonds, or mutual funds directly to RECI can be more tax-efficient than giving cash. Contact your broker and have them transfer shares directly to our account. We will acknowledge the gift at full fair-market value on the date of transfer.',
					],
					[
						'title' => 'Planned Gifts',
						'body'  => 'Leave a lasting legacy for racial equity by including RECI in your will, trust, or beneficiary designations. A planned gift — no matter the size — ensures our mission continues for generations. Contact us at info@reci.pitt.edu to learn more or to notify us of your intent.',
					],
					[
						'title' => 'Wired Transfers / ACH',
						'body'  => 'For large gifts or institutional donors, wire transfers and ACH bank transfers offer a secure, low-cost way to give. Please contact us at mediahub@reci.pitt.edu to receive our banking details and to ensure your gift is properly acknowledged.',
					],
					[
						'title' => 'Endowment Gifts',
						'body'  => 'An endowment gift creates a permanent fund whose investment returns support RECI programmes in perpetuity. Minimum endowment gifts start at $25,000. Named endowment opportunities are available and recognise your family or organisation in our work forever.',
					],
					[
						'title' => 'Donor Advised Funds',
						'body'  => 'Recommend a grant to RECI through your Donor Advised Fund (DAF) at Fidelity Charitable, Schwab Charitable, or any community foundation. Our legal name is "RECI at the University of Pittsburgh" and our EIN is available on request.',
					],
				];
				?>
				<div class="flex flex-col divide-y divide-zinc-200 border border-zinc-200 rounded-lg bg-white overflow-hidden">
					<?php foreach ($other_ways as $i => $way) : ?>
						<details class="group" <?php echo $i === 0 ? 'open' : ''; ?>>
							<summary class="flex items-center justify-between gap-4 px-8 py-6 cursor-pointer list-none select-none hover:bg-slate-50 transition-colors">
								<h3 class="text-xl font-semibold font-['EB_Garamond'] text-neutral-800 leading-7">
									<?php echo wp_kses_post($way['title']); ?>
								</h3>
								<svg class="w-5 h-5 text-neutral-500 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
								</svg>
							</summary>
							<div class="px-8 pb-8">
								<p class="text-base font-normal font-['SF_Pro_Display'] text-neutral-500 leading-7 tracking-tight max-w-3xl">
									<?php echo esc_html($way['body']); ?>
								</p>
							</div>
						</details>
					<?php endforeach; ?>
				</div>

				<div>
					<a href="<?php echo esc_url($donate_url); ?>" class="inline-flex items-center gap-2 px-7 py-3.5 bg-amber-400 rounded-lg text-base font-medium font-['SF_Pro_Display'] text-neutral-800 leading-6 hover:bg-amber-300 transition-colors">
						<?php echo esc_html__('Make a Donation', 'reci-media-hub'); ?>
					</a>
				</div>

			</div><!-- /right -->

		</div>
	</section>

</div>

<?php get_footer(); ?>