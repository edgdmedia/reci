<?php

/**
 * Template Name: Donate - Donor Benefits
 *
 * Donor Benefits table page for the Donate section.
 * Placeholder — real content deferred.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

// Sibling page URLs
$donate_url     = home_url('/donate/');
$benefits_url   = get_permalink() ?: home_url('/donate-benefits/');
$faq_url        = home_url('/donate-faq/');
$other_ways_url = home_url('/donate-other-ways/');

get_header();
$page_title = "Donate";
$page_subtitle = "Your contribution directly supports RECI\'s mission to advance racial equity through education, media, and community engagement. Every dollar helps us build a more just world.";
?>

<div class="layout-page">

	<!-- =========================================================
	     PAGE HERO — title bar
	     ========================================================= -->
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => $page_title,
		'subtitle' => $page_subtitle,
	]); ?>

	<!-- =========================================================
	     BANNER IMAGE (placeholder)
	     ========================================================= -->
	<div class="reci-container-full ">
		<img
			src="/wp-content/uploads/2026/03/donate-banner.png?text=Support+RECI"
			alt="<?php echo esc_attr__('Support RECI — racial equity in action', 'reci-media-hub'); ?>"
			class="w-full h-[450px] object-cover" />
	</div>

	<!-- =========================================================
	     MAIN CONTENT — sidebar + Benefits table
	     ========================================================= -->
	<section class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10 pb-24">
		<div class="flex flex-col lg:flex-row gap-14 items-start">

			<!-- LEFT SIDEBAR -->
			<aside class="w-full lg:w-96 flex flex-col gap-10 shrink-0">

				<nav class="w-full p-10 bg-[#003594] rounded-lg flex flex-col gap-10 overflow-hidden" aria-label="Donation page navigation">

					<a href="<?php echo esc_url($donate_url); ?>" class="flex flex-col gap-3 opacity-60 transition-opacity hover:opacity-100">
						<div class="flex items-center gap-2">
							<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
							<span class="text-slate-300 text-2xl font-bold font-heading leading-7">
								<?php echo esc_html__('Donate', 'reci-media-hub'); ?>
							</span>
						</div>
					</a>

					<!-- Active -->
					<div class="flex flex-col gap-3">
						<div class="flex items-center gap-2">
							<div class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
							<span class="text-white text-2xl font-bold font-heading leading-7">
								<?php echo esc_html__('Donor Benefits', 'reci-media-hub'); ?>
							</span>
						</div>
						<div class="w-full h-0.5 bg-white"></div>
					</div>

					<a href="<?php echo esc_url($faq_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-heading leading-7">
							<?php echo esc_html__('Frequently Asked Questions', 'reci-media-hub'); ?>
						</span>
					</a>

					<a href="<?php echo esc_url($other_ways_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-heading leading-7">
							<?php echo esc_html__('Other Ways to Give', 'reci-media-hub'); ?>
						</span>
					</a>

				</nav>

				<!-- CTA card -->
				<div class="w-full p-10 bg-[#003594] rounded-lg flex flex-col gap-5">
					<div class="flex flex-col gap-3">
						<h2 class="text-2xl font-bold font-heading text-white leading-7">
							<?php echo esc_html__('Ready to Give?', 'reci-media-hub'); ?>
						</h2>
						<p class="text-base font-normal text-blue-200 leading-6 ">
							<?php echo esc_html__('Choose your giving level and start making a difference today.', 'reci-media-hub'); ?>
						</p>
					</div>
					<a href="<?php echo esc_url($donate_url); ?>" class="self-start px-7 py-3.5 bg-amber-400 rounded-lg text-sm font-medium text-neutral-800 leading-6 hover:bg-amber-300 transition-colors">
						<?php echo esc_html__('Donate Now', 'reci-media-hub'); ?>
					</a>
				</div>

			</aside>

			<!-- RIGHT — Benefits table -->
			<div class="flex-1 flex flex-col gap-8">

				<div class="flex items-center gap-2">
					<div class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
					<h2 class="text-4xl font-bold font-heading text-neutral-800 leading-[1.05]">
						<?php echo esc_html__('What do you receive when you donate?', 'reci-media-hub'); ?>
					</h2>
				</div>

				<div class="w-full overflow-x-auto rounded-lg border border-zinc-200">
					<table class="w-full text-sm border-collapse min-w-[640px]">
						<thead>
							<tr class="bg-neutral-800 text-white">
								<th class="px-6 py-4 text-left font-semibold leading-6 w-1/3">Donors Benefit</th>
								<th class="px-4 py-4 text-center font-semibold leading-6">$50</th>
								<th class="px-4 py-4 text-center font-semibold leading-6">$100</th>
								<th class="px-4 py-4 text-center font-semibold leading-6">$250</th>
								<th class="px-4 py-4 text-center font-semibold leading-6">$500</th>
								<th class="px-4 py-4 text-center font-semibold leading-6">$1,000</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$benefit_rows = [
								['label' => 'Monthly newsletter',              'tiers' => [true,  true,  true,  true,  true]],
								['label' => 'Early content access',            'tiers' => [false, true,  true,  true,  true]],
								['label' => 'Community event invitations',     'tiers' => [false, false, true,  true,  true]],
								['label' => 'Name in annual report',           'tiers' => [false, false, true,  true,  true]],
								['label' => 'Exclusive donor webinars',        'tiers' => [false, false, false, true,  true]],
								['label' => 'Personalised impact report',      'tiers' => [false, false, false, true,  true]],
								['label' => 'Recognition plaque / certificate', 'tiers' => [false, false, false, false, true]],
								['label' => 'Dedicated content partnership',   'tiers' => [false, false, false, false, true]],
							];
							foreach ($benefit_rows as $row_idx => $row) :
								$row_bg = $row_idx % 2 === 0 ? 'bg-white' : 'bg-slate-50';
							?>
								<tr class="<?php echo esc_attr($row_bg); ?> border-t border-zinc-100">
									<td class="px-6 py-4 text-neutral-700 font-normal leading-6"><?php echo esc_html($row['label']); ?></td>
									<?php foreach ($row['tiers'] as $has) : ?>
										<td class="px-4 py-4 text-center">
											<?php if ($has) : ?>
												<svg class="w-5 h-5 text-emerald-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Included">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
												</svg>
											<?php else : ?>
												<span class="block w-4 h-px bg-zinc-300 mx-auto" aria-label="Not included"></span>
											<?php endif; ?>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="p-6 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800 leading-6">
					<?php echo esc_html__('Benefits are cumulative — higher giving levels include all benefits from lower tiers. All recurring donors receive benefits based on their monthly giving level.', 'reci-media-hub'); ?>
				</div>

				<div>
					<a href="<?php echo esc_url($donate_url); ?>" class="inline-flex items-center gap-2 px-7 py-3.5 bg-amber-400 rounded-lg text-base font-medium text-neutral-800 leading-6 hover:bg-amber-300 transition-colors">
						<?php echo esc_html__('Make a Donation', 'reci-media-hub'); ?>
					</a>
				</div>

			</div><!-- /right -->

		</div>
	</section>

</div>

<?php get_footer(); ?>