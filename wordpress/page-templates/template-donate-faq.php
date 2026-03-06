<?php
/**
 * Template Name: Donate - FAQ
 *
 * Frequently Asked Questions page for the Donate section.
 * Placeholder — real content deferred.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Sibling page URLs
$donate_url     = home_url( '/donate/' );
$benefits_url   = home_url( '/donate-benefits/' );
$faq_url        = get_permalink() ?: home_url( '/donate-faq/' );
$other_ways_url = home_url( '/donate-other-ways/' );

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
					<?php echo esc_html__( 'Donate', 'reci-media-hub' ); ?>
				</h1>
			</div>
			<p class="text-lg font-normal text-white/70 max-w-xl leading-7 tracking-tight sm:pl-10 sm:border-l sm:border-white/30">
				<?php echo esc_html__( 'Your contribution directly supports RECI\'s mission to advance racial equity through education, media, and community engagement.', 'reci-media-hub' ); ?>
			</p>
		</div>
	</section>

	<!-- =========================================================
	     MAIN CONTENT — sidebar + FAQ
	     ========================================================= -->
	<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10 pb-24">
		<div class="flex flex-col lg:flex-row gap-14 items-start">

			<!-- LEFT SIDEBAR -->
			<aside class="w-full lg:w-96 flex flex-col gap-10 shrink-0">

				<nav class="w-full p-10 bg-[#003594] rounded-lg flex flex-col gap-10 overflow-hidden" aria-label="Donation page navigation">

					<a href="<?php echo esc_url( $donate_url ); ?>" class="flex flex-col gap-3 opacity-60 transition-opacity hover:opacity-100">
						<div class="flex items-center gap-2">
							<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
							<span class="text-slate-300 text-2xl font-bold font-['EB_Garamond'] leading-7">
								<?php echo esc_html__( 'Donate', 'reci-media-hub' ); ?>
							</span>
						</div>
					</a>

					<a href="<?php echo esc_url( $benefits_url ); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-['EB_Garamond'] leading-7">
							<?php echo esc_html__( 'Donor Benefits', 'reci-media-hub' ); ?>
						</span>
					</a>

					<!-- Active -->
					<div class="flex flex-col gap-3">
						<div class="flex items-center gap-2">
							<div class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
							<span class="text-white text-2xl font-bold font-['EB_Garamond'] leading-7">
								<?php echo esc_html__( 'Frequently Asked Questions', 'reci-media-hub' ); ?>
							</span>
						</div>
						<div class="w-full h-0.5 bg-white"></div>
					</div>

					<a href="<?php echo esc_url( $other_ways_url ); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-['EB_Garamond'] leading-7">
							<?php echo esc_html__( 'Other Ways to Give', 'reci-media-hub' ); ?>
						</span>
					</a>

				</nav>

				<!-- Questions card -->
				<div class="w-full p-10 bg-white rounded-lg flex flex-col gap-5">
					<div class="p-4 bg-[#003594] rounded-full inline-flex justify-center items-center self-start">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
					<div class="flex flex-col gap-3">
						<h2 class="text-2xl font-bold font-['EB_Garamond'] text-neutral-800 leading-7">
							<?php echo esc_html__( 'Questions about Giving?', 'reci-media-hub' ); ?>
						</h2>
						<p class="text-base font-medium font-['SF_Pro_Display'] text-neutral-500 leading-6 tracking-tight">
							Email: <a href="mailto:mediahub@reci.pitt.edu" class="underline hover:text-[#003594] transition-colors">mediahub@reci.pitt.edu</a>
						</p>
					</div>
				</div>

			</aside>

			<!-- RIGHT — FAQ content -->
			<div class="flex-1 flex flex-col gap-0 bg-white rounded-lg overflow-hidden border border-zinc-200">

				<div class="px-10 pt-10 pb-6 flex flex-col gap-3">
					<div class="flex items-center gap-2">
						<div class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
						<h2 class="text-4xl font-bold font-['EB_Garamond'] text-neutral-800 leading-[1.05]">
							<?php echo esc_html__( 'Frequently Asked Questions', 'reci-media-hub' ); ?>
						</h2>
					</div>
				</div>

				<div class="mx-10 h-px bg-zinc-300"></div>

				<?php
				$faqs = [
					[
						'q' => 'How Can I make a donation to RECI?',
						'a' => 'You can donate online using the donation form — select your amount and frequency, enter your details, and click "Donate Now". We accept all major credit and debit cards, PayPal, and cheques by mail.',
					],
					[
						'q' => 'Where do I mail my donation cheque?',
						'a' => 'Make cheques payable to "RECI" and mail to: 4200 Fifth Avenue, Pittsburgh, PA 15260. Please include your name and email address so we can send you a receipt.',
					],
					[
						'q' => 'Can I set up a recurring donation?',
						'a' => 'Absolutely. Select "Recurring" on the donation form to schedule a monthly, quarterly, or annual contribution. You can modify or cancel your recurring gift at any time by contacting us at mediahub@reci.pitt.edu.',
					],
					[
						'q' => 'Will I receive updates on how my donation is used?',
						'a' => 'Yes. All donors receive our monthly impact newsletter with updates on programmes, content, and community initiatives your contribution helps make possible.',
					],
					[
						'q' => 'What are RECI donor benefits?',
						'a' => 'Donor benefits vary by giving level. Benefits include early content access, community event invitations, name recognition in our annual report, exclusive webinars, and more. Visit the Donor Benefits page for the full breakdown.',
					],
				];
				foreach ( $faqs as $faq ) :
				?>
					<div class="px-10 py-6 flex flex-col gap-3 border-t border-zinc-200 first:border-t-0">
						<h3 class="text-xl font-semibold font-['EB_Garamond'] text-neutral-800 leading-7">
							<?php echo esc_html( $faq['q'] ); ?>
						</h3>
						<p class="text-base font-normal font-['SF_Pro_Display'] text-neutral-500 leading-6 tracking-tight">
							<?php echo esc_html( $faq['a'] ); ?>
						</p>
					</div>
				<?php endforeach; ?>

				<div class="px-10 py-8 border-t border-zinc-200">
					<a href="<?php echo esc_url( $donate_url ); ?>" class="inline-flex items-center gap-2 px-7 py-3.5 bg-amber-400 rounded-lg text-base font-medium font-['SF_Pro_Display'] text-neutral-800 leading-6 hover:bg-amber-300 transition-colors">
						<?php echo esc_html__( 'Make a Donation', 'reci-media-hub' ); ?>
					</a>
				</div>

			</div><!-- /right -->

		</div>
	</section>

</div>

<?php get_footer(); ?>
