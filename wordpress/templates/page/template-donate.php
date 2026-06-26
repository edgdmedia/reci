<?php

/**
 * Template Name: Donate
 *
 * Donation page: page-title hero bar, left sidebar navigation / info cards,
 * and a right-side donation card with amount presets, frequency toggle,
 * and a donor details form.
 *
 * All content is static placeholder — no WP_Query calls.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$amount_presets = ['$50', '$100', '$250', '$500', '$1,000'];
$raw_mode       = isset($_GET['mode']) ? sanitize_key(wp_unslash($_GET['mode'])) : 'one-time';
$donation_mode  = in_array($raw_mode, ['one-time', 'recurring'], true) ? $raw_mode : 'one-time';
$is_recurring   = ('recurring' === $donation_mode);
$mode_active_classes   = "flex-1 px-4 py-2 bg-amber-400 rounded-lg flex justify-center items-center gap-2 overflow-hidden text-sm font-medium text-neutral-800 leading-6 ";
$mode_inactive_classes = "flex-1 px-4 py-2 rounded-lg outline outline-[0.5px] outline-neutral-500 flex justify-center items-center gap-2 overflow-hidden text-sm font-medium text-neutral-600 leading-5  hover:bg-slate-50 transition-colors";

$donate_page_url = get_permalink();
if (! $donate_page_url) {
	$donate_page_url = home_url('/donate/');
}

$one_time_url  = add_query_arg('mode', 'one-time', $donate_page_url) . '#donation-form';
$recurring_url = add_query_arg('mode', 'recurring', $donate_page_url) . '#donation-form';

$benefits_url   = home_url('/donate/benefits/');
$faq_url        = home_url('/donate/faq/');
$other_ways_url = home_url('/donate/other-ways/');

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
	     MAIN CONTENT — sidebar + donation card
	     ========================================================= -->
	<section class=" mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-10 pb-24">
		<div class="flex flex-col lg:flex-row gap-14 items-start">

			<!-- -------------------------------------------------------
			     LEFT SIDEBAR
			     ------------------------------------------------------- -->
			<aside class="w-full lg:w-96 flex flex-col gap-10 shrink-0">

				<!-- Section navigation card -->
				<nav id="donate-section-nav" class="w-full p-10 bg-[#003594] rounded-lg flex flex-col gap-10 overflow-hidden" aria-label="Donation page navigation">

					<!-- Active: Donate -->
					<a id="donate-nav-donate" href="#donation-form" class="flex flex-col gap-3 transition-opacity">
						<div class="flex items-center gap-2">
							<div id="donate-nav-donate-dot" class="w-2 h-2 bg-amber-400 rounded-sm shrink-0"></div>
							<span id="donate-nav-donate-text" class="text-white text-2xl font-bold font-heading leading-7">
								<?php echo esc_html__('Donate', 'reci-media-hub'); ?>
							</span>
						</div>
						<div id="donate-nav-donate-line" class="w-full h-0.5 bg-white transition-colors"></div>
					</a>


					<!-- Inactive: Benefits -->
					<a href="<?php echo esc_url($benefits_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-heading leading-7">
							<?php echo esc_html__('Donor Benefits', 'reci-media-hub'); ?>
						</span>
					</a>

					<!-- Inactive: FAQ -->
					<a href="<?php echo esc_url($faq_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-heading leading-7">
							<?php echo esc_html__('Frequently Asked Questions', 'reci-media-hub'); ?>
						</span>
					</a>

					<!-- Inactive: Other ways -->
					<a href="<?php echo esc_url($other_ways_url); ?>" class="flex items-center gap-2 opacity-60 transition-opacity hover:opacity-100">
						<div class="w-2 h-2 bg-slate-400 rounded-sm shrink-0"></div>
						<span class="text-slate-300 text-2xl font-bold font-heading leading-7">
							<?php echo esc_html__('Other Ways to Give', 'reci-media-hub'); ?>
						</span>
					</a>


				</nav><!-- /nav card -->

				<!-- Questions about giving? -->
				<div class="w-full p-10 bg-white rounded-lg flex flex-col gap-5">
					<div class="p-4 bg-[#003594] rounded-full inline-flex justify-center items-center self-start">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0-10a2 2 0 00-2 2v3m4-3a2 2 0 012 2v3m-6 4h6M5 9a7 7 0 1114 0v5a2 2 0 01-2 2H7a2 2 0 01-2-2V9z" />
						</svg>
					</div>
					<div class="flex flex-col gap-3">
						<h2 class="text-2xl font-bold font-heading text-neutral-800 leading-7">
							<?php echo esc_html__('Questions about Giving?', 'reci-media-hub'); ?>
						</h2>
						<p class="text-base font-medium text-neutral-600 leading-6 ">
							<?php echo esc_html__('Email: ', 'reci-media-hub'); ?>
							<a
								href="mailto:info@RECI.com"
								class="underline hover:text-[#003594] transition-colors">
								<?php echo esc_html('info@RECI.com'); ?>
							</a>
						</p>
					</div>
				</div>

				<!-- Mailing a cheque? -->
				<div class="w-full p-10 bg-neutral-800 rounded-lg flex flex-col gap-5">
					<div class="p-4 bg-neutral-600 rounded-full inline-flex justify-center items-center self-start">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
						</svg>
					</div>
					<div class="flex flex-col gap-3">
						<h2 class="text-2xl font-bold font-heading text-white leading-7">
							<?php echo esc_html__('Mailing a Cheque?', 'reci-media-hub'); ?>
						</h2>
						<p class="text-base font-medium text-gray-200 leading-6 ">
							<?php echo esc_html__('Kindly follow this instruction', 'reci-media-hub'); ?><br />
							<a href="<?php echo esc_url($faq_url); ?>" class="underline hover:text-amber-400 transition-colors">
								<?php echo esc_html__('Donation FAQ', 'reci-media-hub'); ?>
							</a>
						</p>
					</div>
				</div>

			</aside><!-- /LEFT SIDEBAR -->

			<!-- -------------------------------------------------------
			     DONATION CARD
			     ------------------------------------------------------- -->
			<div class="flex-1 flex flex-col gap-10">

				<div id="donation-form" class="w-full p-10 bg-white rounded-lg flex flex-col gap-14 overflow-hidden">

					<!-- Card header -->
					<div class="flex flex-col gap-10">

						<div class="flex flex-col gap-3">
							<h2 class="text-3xl font-bold font-heading text-neutral-800 leading-9">
								<?php echo esc_html__('Help us build a more just world.', 'reci-media-hub'); ?>
							</h2>
							<p class="text-base font-normal text-neutral-600 leading-6 ">
								<?php echo esc_html__('Your generosity makes our resources, community workshops, and critical conversations possible.', 'reci-media-hub'); ?>
							</p>
						</div>

						<div class="w-full h-px bg-zinc-400/50"></div>

						<!-- Donation Type toggle -->
						<div class="flex flex-col gap-3">
							<h3 class="text-xl font-medium text-neutral-800 leading-8 ">
								<?php echo esc_html__('Donation Type', 'reci-media-hub'); ?>
							</h3>
							<div class="flex gap-3" role="group" aria-label="Donation type">
								<a
									href="<?php echo esc_url($one_time_url); ?>"
									id="donation-mode-one-time"
									data-donate-mode-link="one-time"
									class="<?php echo esc_attr($is_recurring ? $mode_inactive_classes : $mode_active_classes); ?>"
									aria-pressed="<?php echo $is_recurring ? 'false' : 'true'; ?>">
									<?php echo esc_html__('One-Time', 'reci-media-hub'); ?>
								</a>
								<a
									href="<?php echo esc_url($recurring_url); ?>"
									id="donation-mode-recurring"
									data-donate-mode-link="recurring"
									class="<?php echo esc_attr($is_recurring ? $mode_active_classes : $mode_inactive_classes); ?>"
									aria-pressed="<?php echo $is_recurring ? 'true' : 'false'; ?>">
									<?php echo esc_html__('Recurring', 'reci-media-hub'); ?>
								</a>
							</div>
						</div>

						<!-- Frequency selector (Recurring only) -->
						<div id="donation-frequency-row" class="flex flex-col gap-3 hidden">
							<h3 class="text-xl font-medium text-neutral-800 leading-8 ">
								<?php echo esc_html__('Frequency', 'reci-media-hub'); ?>
							</h3>
							<div class="flex gap-3" role="group" aria-label="Donation frequency">
								<button
									type="button"
									id="donate-freq-monthly"
									data-donate-freq="monthly"
									class="flex-1 px-4 py-2 bg-amber-400 rounded-lg flex justify-center items-center text-sm font-medium text-neutral-800 leading-6 "
									aria-pressed="true">
									<?php echo esc_html__('Monthly', 'reci-media-hub'); ?>
								</button>
								<button
									type="button"
									id="donate-freq-quarterly"
									data-donate-freq="quarterly"
									class="flex-1 px-4 py-2 rounded-lg outline outline-[0.5px] outline-neutral-500 flex justify-center items-center text-sm font-medium text-neutral-600 leading-5  hover:bg-slate-50 transition-colors"
									aria-pressed="false">
									<?php echo esc_html__('Quarterly', 'reci-media-hub'); ?>
								</button>
								<button
									type="button"
									id="donate-freq-annually"
									data-donate-freq="annually"
									class="flex-1 px-4 py-2 rounded-lg outline outline-[0.5px] outline-neutral-500 flex justify-center items-center text-sm font-medium text-neutral-600 leading-5  hover:bg-slate-50 transition-colors"
									aria-pressed="false">
									<?php echo esc_html__('Annually', 'reci-media-hub'); ?>
								</button>
							</div>
							<input type="hidden" name="reci_donation_frequency" id="donate-frequency-input" value="monthly" />
						</div>

						<div class="w-full h-px bg-zinc-400/50"></div>

						<!-- Amount selector -->
						<div class="flex flex-col gap-3">

							<div class="flex justify-between items-center">
								<h3 id="donation-amount-label" class="text-xl font-medium text-neutral-800 leading-8 ">
									<?php echo esc_html($is_recurring ? __('Monthly Amount', 'reci-media-hub') : __('Amount', 'reci-media-hub')); ?>
								</h3>
								<div class="flex items-center gap-1">
									<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-neutral-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4" />
									</svg>
									<a href="<?php echo esc_url($benefits_url); ?>" class="text-sm font-medium text-neutral-600 underline leading-6 hover:text-[#003594] transition-colors">
										<?php echo esc_html__('Read Donors Benefit', 'reci-media-hub'); ?>
									</a>
								</div>
							</div>

							<!-- Preset amount buttons -->
							<div class="flex flex-wrap gap-3" role="group" aria-label="Donation amount presets">
								<?php
								$first = true;
								foreach ($amount_presets as $preset) :
									if ($first) :
								?>
										<button
											type="button"
											type="button"
											data-donate-preset
											aria-pressed="true">
											<?php echo esc_html($preset); ?>
										</button>
									<?php
										$first = false;
									else :
									?>
										<button
											type="button"
											type="button"
											data-donate-preset
											aria-pressed="false">
											<?php echo esc_html($preset); ?>
										</button>
								<?php
									endif;
								endforeach;
								?>

								<!-- Others / custom amount -->
								<button
									type="button"
									id="donate-preset-other"
									id="donate-preset-other"
									data-donate-preset
									aria-pressed="false">
									<?php echo esc_html__('Others', 'reci-media-hub'); ?>
								</button>

							</div><!-- /presets -->

							<!-- Custom amount input (shown when "Other" is selected) -->
							<div class="flex flex-col gap-2">
								<label
									for="reci-custom-amount"
									class="text-sm font-medium text-neutral-800 leading-6">
									<?php echo esc_html__('Custom Amount (USD)', 'reci-media-hub'); ?>
								</label>
								<div class="relative">
									<span class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-600 text-sm font-medium">$</span>
									<input
										type="number"
										id="reci-custom-amount"
										name="reci_custom_amount"
										min="1"
										step="1"
										placeholder="<?php echo esc_attr__('0.00', 'reci-media-hub'); ?>"
										class="w-full pl-8 pr-4 py-5 rounded-lg outline outline-[0.5px] outline-zinc-400 text-sm font-normal text-neutral-800 placeholder-zinc-400 bg-white focus:outline-[#003594] focus:outline-2 transition-all" />
								</div>
							</div>

						</div><!-- /Amount selector -->

					</div><!-- /Card header -->

					<!-- Donor details form -->
					<form
						method="post"
						action="#"
						class="flex flex-col gap-5">
						<?php wp_nonce_field('reci_donate', 'reci_donate_nonce'); ?>
						<input type="hidden" name="reci_donation_mode" value="<?php echo esc_attr($donation_mode); ?>" />

						<h3 class="text-xl font-medium text-neutral-800 leading-8 ">
							<?php echo esc_html__('Your Details', 'reci-media-hub'); ?>
						</h3>

						<!-- Name + Email -->
						<div class="flex flex-col sm:flex-row gap-5">
							<div class="flex-1 flex flex-col gap-2">
								<label
									for="reci-donor-name"
									class="text-sm font-medium text-neutral-800 leading-6">
									<?php echo esc_html__('Full Name', 'reci-media-hub'); ?>
								</label>
								<input
									type="text"
									id="reci-donor-name"
									name="reci_donor_name"
									placeholder="<?php echo esc_attr__('Jane Doe', 'reci-media-hub'); ?>"
									autocomplete="name"
									class="w-full px-4 py-5 rounded-lg outline outline-[0.5px] outline-zinc-400 text-sm font-normal text-neutral-800 placeholder-zinc-400 bg-white focus:outline-[#003594] focus:outline-2 transition-all" />
							</div>
							<div class="flex-1 flex flex-col gap-2">
								<label
									for="reci-donor-email"
									class="text-sm font-medium text-neutral-800 leading-6">
									<?php echo esc_html__('Email', 'reci-media-hub'); ?>
								</label>
								<input
									type="email"
									id="reci-donor-email"
									name="reci_donor_email"
									placeholder="<?php echo esc_attr__('Johndoe@email.com', 'reci-media-hub'); ?>"
									autocomplete="email"
									class="w-full px-4 py-5 rounded-lg outline outline-[0.5px] outline-zinc-400 text-sm font-normal text-neutral-800 placeholder-zinc-400 bg-white focus:outline-[#003594] focus:outline-2 transition-all" />
							</div>
						</div>

						<!-- Review notice -->
						<div class="flex items-start gap-[5px]">
							<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-neutral-800 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z" />
							</svg>
							<p class="flex-1 text-sm font-normal text-neutral-600 leading-6">
								<?php echo esc_html__('Please take a moment to review your submission on the next page before clicking submit.', 'reci-media-hub'); ?>
							</p>
						</div>

						<!-- Donate Now button -->
						<button
							type="submit"
							class="btn btn-primary btn-md btn-block">
							<span id="donation-submit-label"><?php echo esc_html($is_recurring ? __('Start Monthly Donation', 'reci-media-hub') : __('Donate Now', 'reci-media-hub')); ?></span>
						</button>

					</form><!-- /donor details form -->

				</div><!-- /donation card -->

			</div><!-- /flex-1 -->

		</div><!-- /flex layout -->
	</section>

</div><!-- /bg-slate-100 -->


<style>
	/* Amount preset buttons */
	button[data-donate-preset][aria-pressed="true"] {
		background-color: #fbbf24;
		/* amber-400 */
		color: #1c1917;
		/* neutral-800 */
		outline: none;
	}

	button[data-donate-preset][aria-pressed="false"] {
		background-color: transparent;
		color: #737373;
		/* neutral-500 */
		outline: 0.5px solid #737373;
	}

	/* Frequency buttons */
	button[data-donate-freq][aria-pressed="true"] {
		background-color: #fbbf24;
		color: #1c1917;
		outline: none;
	}

	button[data-donate-freq][aria-pressed="false"] {
		background-color: transparent;
		color: #737373;
		outline: 0.5px solid #737373;
	}
</style>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const modeActiveClasses = "<?php echo esc_js($mode_active_classes); ?>".split(' ');
		const modeInactiveClasses = "<?php echo esc_js($mode_inactive_classes); ?>".split(' ');
		const oneTimeLink = document.getElementById('donation-mode-one-time');
		const recurringLink = document.getElementById('donation-mode-recurring');
		const amountLabel = document.getElementById('donation-amount-label');
		const submitLabel = document.getElementById('donation-submit-label');
		const modeInput = document.querySelector('input[name="reci_donation_mode"]');
		const donationFormSection = document.getElementById('donation-form');
		const frequencyRow = document.getElementById('donation-frequency-row');
		const frequencyInput = document.getElementById('donate-frequency-input');

		if (!oneTimeLink || !recurringLink) {
			return;
		}

		function applyButtonClasses(link, isActive) {
			link.classList.remove(...modeActiveClasses, ...modeInactiveClasses);
			link.classList.add(...(isActive ? modeActiveClasses : modeInactiveClasses));
			link.setAttribute('aria-pressed', isActive ? 'true' : 'false');
		}

		function setMode(mode, options) {
			const settings = Object.assign({
				updateUrl: true,
				smoothScroll: false
			}, options || {});
			const resolvedMode = mode === 'recurring' ? 'recurring' : 'one-time';
			const isRecurring = resolvedMode === 'recurring';

			applyButtonClasses(oneTimeLink, !isRecurring);
			applyButtonClasses(recurringLink, isRecurring);

			if (amountLabel) {
				amountLabel.textContent = isRecurring ? 'Monthly Amount' : 'Amount';
			}
			if (submitLabel) {
				submitLabel.textContent = isRecurring ? 'Start Monthly Donation' : 'Donate Now';
			}
			if (modeInput) {
				modeInput.value = resolvedMode;
			}
			if (frequencyRow) {
				frequencyRow.classList.toggle('hidden', !isRecurring);
			}

			if (settings.updateUrl) {
				const url = new URL(window.location.href);
				url.searchParams.set('mode', resolvedMode);
				history.replaceState({}, '', url.toString());
			}
			if (settings.smoothScroll && donationFormSection) {
				donationFormSection.scrollIntoView({
					behavior: 'smooth',
					block: 'start'
				});
			}
		}

		// Initialise from URL param
		const initialMode = new URLSearchParams(window.location.search).get('mode');
		setMode(initialMode === 'recurring' ? 'recurring' : 'one-time', {
			updateUrl: false,
			smoothScroll: false
		});

		oneTimeLink.addEventListener('click', function(event) {
			event.preventDefault();
			setMode('one-time', {
				updateUrl: true,
				smoothScroll: true
			});
		});

		recurringLink.addEventListener('click', function(event) {
			event.preventDefault();
			setMode('recurring', {
				updateUrl: true,
				smoothScroll: true
			});
		});

		// ── Amount preset buttons ─────────────────────────────────────
		const presetButtons = document.querySelectorAll('[data-donate-preset]');
		const customAmountWrap = document.getElementById('reci-custom-amount') ?
			document.getElementById('reci-custom-amount').closest('.flex.flex-col.gap-2') :
			null;
		if (customAmountWrap) {
			customAmountWrap.style.display = 'none';
		}

		presetButtons.forEach(function(btn) {
			btn.addEventListener('click', function() {
				presetButtons.forEach(function(b) {
					b.setAttribute('aria-pressed', 'false');
				});
				btn.setAttribute('aria-pressed', 'true');
				const isOther = btn.id === 'donate-preset-other';
				if (customAmountWrap) {
					customAmountWrap.style.display = isOther ? '' : 'none';
				}
			});
		});

		// ── Frequency buttons ─────────────────────────────────────────
		const freqButtons = document.querySelectorAll('[data-donate-freq]');
		freqButtons.forEach(function(btn) {
			btn.addEventListener('click', function() {
				freqButtons.forEach(function(b) {
					b.setAttribute('aria-pressed', 'false');
				});
				btn.setAttribute('aria-pressed', 'true');
				const freq = btn.getAttribute('data-donate-freq');
				const freqCap = freq.charAt(0).toUpperCase() + freq.slice(1);
				if (frequencyInput) {
					frequencyInput.value = freq;
				}
				if (amountLabel) {
					amountLabel.textContent = freqCap + ' Amount';
				}
				if (submitLabel) {
					submitLabel.textContent = 'Start ' + freqCap + ' Donation';
				}
			});
		});
	});
</script>

<?php get_footer(); ?>