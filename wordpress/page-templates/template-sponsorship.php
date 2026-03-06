<?php

/**
 * Template Name: Sponsorship
 *
 * @package reci-media-hub
 */

get_header();
?>

<main class="bg-slate-100 min-h-screen">

	<!-- Page Header -->
	<section class="w-full border-b border-zinc-400">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-14">
			<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
				<div class="flex items-center gap-3">
					<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
					<h1 class="text-5xl font-medium font-['EB_Garamond'] text-neutral-800 leading-tight"><?php echo esc_html('Be a Catalyst for Equity'); ?></h1>
				</div>
				<div class="lg:pl-10 lg:border-l border-zinc-400 max-w-xl">
					<p class="text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">
						<?php echo esc_html('Join us in equipping individuals and communities with the tools to dismantle systemic racism. Your sponsorship fuels real conversations and lasting impact.'); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Hero Image -->
	<div class="w-full">
		<img
			src="http://localhost:10003/wp-content/uploads/2026/03/sponsorship.png"
			alt="<?php echo esc_attr('Partner with RECI'); ?>"
			class="w-full h-96 object-cover" />
	</div>

	<!-- Why Partner Section -->
	<section class="w-full">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-24">
			<div class="flex flex-col lg:flex-row items-start gap-14">
				<div class="w-full lg:w-1/2">
					<img
						src="<?php echo esc_url('http://localhost:10003/wp-content/uploads/2026/03/sponsorship2.png'); ?>"
						alt="<?php echo esc_attr('RECI Impact'); ?>"
						class="flex-1 rounded-lg object-cover self-stretch" />
				</div>

				<div class="w-full lg:w-1/2 flex flex-col gap-5">
					<div class="flex flex-col gap-3">
						<h2 class="text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-tight">
							<?php echo esc_html('Why Partner with RECI?'); ?>
						</h2>
						<p class="text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">
							<?php echo esc_html("In today's world, commitment to Diversity, Equity, and Inclusion (DEI) is essential. A partnership with RECI demonstrates that your organization is serious about walking the walk."); ?>
						</p>
					</div>
					<p class="text-neutral-800 text-lg font-bold font-['SF_Pro_Display'] leading-7">
						<?php echo esc_html('When you sponsor RECI, you gain:'); ?>
					</p>
					<?php
					$benefits = array(
						array(
							'title' => 'Authentic Alignment:',
							'desc'  => 'Associate your brand with a trusted, grassroots initiative dedicated to meaningful racial equity education.',
						),
						array(
							'title' => 'Visibility with a Key Audience:',
							'desc'  => 'Reach our growing community of educators, activists, HR leaders, social workers, and everyday advocates who are actively seeking resources and solutions.',
						),
						array(
							'title' => 'Thought Leadership Opportunities:',
							'desc'  => 'Position your organization at the forefront of the equity conversation through co-branded content and events.',
						),
						array(
							'title' => 'Tangible Impact:',
							'desc'  => 'Move beyond internal statements and directly fund programs that create real-world change.',
						),
					);
					foreach ($benefits as $benefit) :
					?>
						<div class="flex items-start gap-3">
							<span class="pt-2.5 shrink-0">
								<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
							</span>
							<p class="text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">
								<strong class="text-neutral-800 font-medium"><?php echo esc_html($benefit['title']); ?></strong>
								<?php echo esc_html(' ' . $benefit['desc']); ?>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Impact Numbers -->
	<section class="w-full bg-neutral-800">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-20">
			<div class="flex flex-col lg:flex-row justify-between items-end gap-10 mb-20">
				<div class="flex flex-col gap-3">
					<div class="flex items-center gap-3">
						<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
						<h2 class="text-white text-5xl font-bold font-['EB_Garamond'] leading-tight">
							<?php echo esc_html('Our Impact by the Numbers'); ?>
						</h2>
					</div>
					<p class="text-gray-200 text-lg font-normal font-['SF_Pro_Display'] leading-7">
						<?php echo esc_html('Your support fuels measurable growth. Here is what our community has accomplished together.'); ?>
					</p>
				</div>
				<a href="<?php echo esc_url('#contact'); ?>" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-amber-400 text-zinc-800 text-base font-medium font-['SF_Pro_Display'] rounded-lg shrink-0">
					<?php echo esc_html('Sponsor us'); ?>
				</a>
			</div>

			<?php
			$stats = array(
				array('number' => '50,000+', 'desc' => 'Monthly visitors to our resource library and quizzes'),
				array('number' => '10,000+', 'desc' => 'Individuals reached through our Racial Anxiety Quiz and self-reflection tools'),
				array('number' => '500+', 'desc' => 'Educators and facilitators using our curriculum in classrooms and workplaces'),
				array('number' => '4.8/5 stars', 'desc' => 'Average rating from users who report increased confidence in discussing race'),
			);
			?>
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
				<?php foreach ($stats as $stat) : ?>
					<div class="p-5 bg-neutral-600 rounded-lg border-b-4 border-amber-400 flex flex-col justify-between gap-8 min-h-56">
						<div class="flex items-center gap-3">
							<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
							<span class="text-white text-3xl font-bold font-['SF_Pro_Display'] leading-9"><?php echo esc_html($stat['number']); ?></span>
						</div>
						<p class="text-gray-200 text-base font-normal font-['SF_Pro_Display'] leading-6"><?php echo esc_html($stat['desc']); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Sponsorship Tiers -->
	<section class="w-full">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-24">
			<div class="flex items-center gap-3 mb-10">
				<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
				<h2 class="text-neutral-800 text-5xl font-bold font-['EB_Garamond'] leading-tight">
					<?php echo esc_html('Sponsorship Levels & Benefits'); ?>
				</h2>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">

				<!-- Advocate Tier -->
				<div class="bg-white rounded-lg p-10 flex flex-col gap-6 outline outline-[0.5px] outline-gray-200">
					<div class="flex flex-col gap-2">
						<span class="inline-block bg-slate-100 text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded self-start leading-4">
							<?php echo esc_html('Advocate'); ?>
						</span>
						<p class="text-neutral-800 text-3xl font-bold font-['EB_Garamond']"><?php echo esc_html('Under $1,000'); ?></p>
						<p class="text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6">
							<?php echo esc_html('Perfect for small businesses and individuals who want to show their commitment to racial equity.'); ?>
						</p>
					</div>
					<hr class="border-zinc-200" />
					<ul class="flex flex-col gap-3">
						<?php
						$advocate_benefits = array(
							'Logo on RECI Website (Sponsors page)',
							'Social media mention (quarterly)',
							'Certificate of partnership',
							'Access to RECI community newsletter',
						);
						foreach ($advocate_benefits as $b) :
						?>
							<li class="flex items-start gap-3 text-neutral-600 text-base font-normal font-['SF_Pro_Display'] leading-6">
								<svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								<?php echo esc_html($b); ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<a href="<?php echo esc_url('#contact'); ?>" class="mt-auto inline-flex justify-center items-center px-7 py-3.5 bg-[#003594] text-white text-base font-medium font-['SF_Pro_Display'] rounded-lg">
						<?php echo esc_html('Become an Advocate'); ?>
					</a>
				</div>

				<!-- Partner Tier -->
				<div class="bg-[#003594] rounded-lg p-10 flex flex-col gap-6">
					<div class="flex flex-col gap-2">
						<span class="inline-block bg-amber-400 text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded self-start leading-4">
							<?php echo esc_html('Partner'); ?>
						</span>
						<p class="text-white text-3xl font-bold font-['EB_Garamond']"><?php echo esc_html('$1,000 – $4,999'); ?></p>
						<p class="text-slate-300 text-base font-normal font-['SF_Pro_Display'] leading-6">
							<?php echo esc_html('For organizations ready to build a visible, meaningful partnership with our community.'); ?>
						</p>
					</div>
					<hr class="border-blue-700" />
					<ul class="flex flex-col gap-3">
						<?php
						$partner_benefits = array(
							'All Advocate benefits',
							'Social media shout-out (monthly)',
							'Recognition in monthly newsletter',
							'Guest blog post opportunity (with review)',
							'Invitation to RECI community webinar',
						);
						foreach ($partner_benefits as $b) :
						?>
							<li class="flex items-start gap-3 text-slate-200 text-base font-normal font-['SF_Pro_Display'] leading-6">
								<svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								<?php echo esc_html($b); ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<a href="<?php echo esc_url('#contact'); ?>" class="mt-auto inline-flex justify-center items-center px-7 py-3.5 bg-amber-400 text-zinc-800 text-base font-medium font-['SF_Pro_Display'] rounded-lg">
						<?php echo esc_html('Become a Partner'); ?>
					</a>
				</div>

				<!-- Lead Benefactor Tier -->
				<div class="bg-neutral-800 rounded-lg p-10 flex flex-col gap-6">
					<div class="flex flex-col gap-2">
						<span class="inline-block bg-amber-400 text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded self-start leading-4">
							<?php echo esc_html('Lead Benefactor'); ?>
						</span>
						<p class="text-white text-3xl font-bold font-['EB_Garamond']"><?php echo esc_html('$5,000+'); ?></p>
						<p class="text-gray-300 text-base font-normal font-['SF_Pro_Display'] leading-6">
							<?php echo esc_html('For leaders who want to shape the future of racial equity education at scale.'); ?>
						</p>
					</div>
					<hr class="border-zinc-600" />
					<ul class="flex flex-col gap-3">
						<?php
						$champion_benefits = array(
							'All Partner benefits',
							'Invitation to private webinar with RECI Founder',
							'Co-branded content creation opportunities',
							'Premium logo placement across all platforms',
							'Annual impact report with your brand featured',
							'Dedicated partnership manager',
						);
						foreach ($champion_benefits as $b) :
						?>
							<li class="flex items-start gap-3 text-gray-200 text-base font-normal font-['SF_Pro_Display'] leading-6">
								<svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								<?php echo esc_html($b); ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<a href="<?php echo esc_url('#contact'); ?>" class="mt-auto inline-flex justify-center items-center px-7 py-3.5 bg-amber-400 text-zinc-800 text-base font-medium font-['SF_Pro_Display'] rounded-lg">
						<?php echo esc_html('Become a Lead Benefactor'); ?>
					</a>
				</div>

			</div>

			<!-- Contact / Interest Form -->
			<div id="contact" class="bg-white rounded-lg p-10 lg:p-14">
				<div class="max-w-2xl">
					<div class="flex items-center gap-3 mb-3">
						<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
						<h3 class="text-neutral-800 text-3xl font-bold font-['EB_Garamond']">
							<?php echo esc_html('Express Your Interest'); ?>
						</h3>
					</div>
					<p class="text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6 mb-10">
						<?php echo esc_html("Ready to partner with us? Fill out the form below and a member of our team will be in touch within 2 business days."); ?>
					</p>
					<form class="flex flex-col gap-6" action="<?php echo esc_url('#'); ?>" method="post">
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
							<div class="flex flex-col gap-2">
								<label for="sp-org-name" class="text-neutral-800 text-base font-medium font-['SF_Pro_Display']">
									<?php echo esc_html('Organization Name'); ?>
								</label>
								<input
									type="text"
									id="sp-org-name"
									name="org_name"
									placeholder="<?php echo esc_attr('Your organization'); ?>"
									class="px-5 py-4 bg-slate-100 rounded-lg text-neutral-800 text-base font-normal font-['SF_Pro_Display'] outline-none border border-zinc-200 focus:border-[#003594]" />
							</div>
							<div class="flex flex-col gap-2">
								<label for="sp-contact-name" class="text-neutral-800 text-base font-medium font-['SF_Pro_Display']">
									<?php echo esc_html('Contact Name'); ?>
								</label>
								<input
									type="text"
									id="sp-contact-name"
									name="contact_name"
									placeholder="<?php echo esc_attr('Your name'); ?>"
									class="px-5 py-4 bg-slate-100 rounded-lg text-neutral-800 text-base font-normal font-['SF_Pro_Display'] outline-none border border-zinc-200 focus:border-[#003594]" />
							</div>
						</div>
						<div class="flex flex-col gap-2">
							<label for="sp-email" class="text-neutral-800 text-base font-medium font-['SF_Pro_Display']">
								<?php echo esc_html('Email Address'); ?>
							</label>
							<input
								type="email"
								id="sp-email"
								name="email"
								placeholder="<?php echo esc_attr('you@organization.com'); ?>"
								class="px-5 py-4 bg-slate-100 rounded-lg text-neutral-800 text-base font-normal font-['SF_Pro_Display'] outline-none border border-zinc-200 focus:border-[#003594]" />
						</div>
						<div class="flex flex-col gap-2">
							<label for="sp-tier" class="text-neutral-800 text-base font-medium font-['SF_Pro_Display']">
								<?php echo esc_html('Sponsorship Level of Interest'); ?>
							</label>
							<select
								id="sp-tier"
								name="tier"
								class="px-5 py-4 bg-slate-100 rounded-lg text-neutral-800 text-base font-normal font-['SF_Pro_Display'] outline-none border border-zinc-200 focus:border-[#003594]">
								<option value=""><?php echo esc_html('Select a level'); ?></option>
								<option value="advocate"><?php echo esc_html('Advocate (Under $1,000)'); ?></option>
								<option value="partner"><?php echo esc_html('Partner ($1,000 – $4,999)'); ?></option>
								<option value="champion"><?php echo esc_html('Lead Benefactor ($5,000+)'); ?></option>
								<option value="custom"><?php echo esc_html('Custom / Not sure yet'); ?></option>
							</select>
						</div>
						<div class="flex flex-col gap-2">
							<label for="sp-message" class="text-neutral-800 text-base font-medium font-['SF_Pro_Display']">
								<?php echo esc_html('Message (optional)'); ?>
							</label>
							<textarea
								id="sp-message"
								name="message"
								rows="4"
								placeholder="<?php echo esc_attr('Tell us about your organization and why you want to partner with RECI.'); ?>"
								class="px-5 py-4 bg-slate-100 rounded-lg text-neutral-800 text-base font-normal font-['SF_Pro_Display'] outline-none border border-zinc-200 focus:border-[#003594] resize-none"></textarea>
						</div>
						<button
							type="submit"
							class="inline-flex justify-center items-center px-7 py-3.5 bg-[#003594] text-white text-base font-medium font-['SF_Pro_Display'] rounded-lg self-start">
							<?php echo esc_html('Submit Interest'); ?>
						</button>
					</form>
				</div>
			</div>

		</div>
	</section>

</main>

<?php get_footer(); ?>