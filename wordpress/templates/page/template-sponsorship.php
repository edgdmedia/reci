<?php

/**
 * Template Name: Sponsorship
 *
 * @package reci-media-hub
 */

get_header();
?>

<main class="layout-page">

	<!-- Page Header -->
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => "Be a Catalyst for Equity",
		'subtitle' => "Join us in equipping individuals and communities with the tools to dismantle systemic racism. Your sponsorship fuels real conversations and lasting impact.",
	]); ?>

	<!-- Hero Image -->
	<div class="reci-container-full">
		<img
			src="/wp-content/uploads/2026/03/sponsorship.png"
			alt="<?php echo esc_attr('Partner with RECI'); ?>"
			class="w-full h-96 object-cover" />
	</div>

	<!-- Why Partner Section -->
	<div class="reci-container lg:px-12 xl:px-20 py-24">
		<div class="flex flex-col lg:flex-row items-start gap-14">
			<div
				class="w-full hidden lg:inline-flex lg:w-1/2 rounded-lg self-stretch min-h-[520px] bg-cover bg-center bg-no-repeat"
				style="background-image:url('<?php echo esc_url('/wp-content/uploads/2026/03/sponsorship2.png'); ?>')">
			</div>
			<div class="w-full lg:w-1/2 flex flex-col gap-5">
				<div class="flex flex-col gap-3">
					<h2 class="text-neutral-800 text-5xl font-bold font-heading leading-tight">
						<?php echo esc_html('Why Partner with RECI?'); ?>
					</h2>
					<p class="text-neutral-600 text-lg font-normal leading-7">
						<?php echo esc_html("In today's world, commitment to Diversity, Equity, and Inclusion (DEI) is essential. A partnership with RECI demonstrates that your organization is serious about walking the walk."); ?>
					</p>
				</div>
				<p class="text-neutral-800 text-lg font-bold leading-7">
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
						<p class="text-neutral-600 text-lg font-normal leading-7">
							<strong class="text-neutral-800 font-medium"><?php echo esc_html($benefit['title']); ?></strong>
							<?php echo esc_html(' ' . $benefit['desc']); ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<!-- Impact Numbers -->
	<section class="reci-container bg-neutral-800 py-20">
		<div class="flex flex-col lg:flex-row justify-between items-center gap-10 mb-20">
			<div class="flex flex-col gap-3">
				<div class="flex items-center gap-3">
					<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
					<h2 class="text-white text-5xl font-bold font-heading leading-tight">
						<?php echo esc_html('Our Impact by the Numbers'); ?>
					</h2>
				</div>
				<p class="text-gray-200 text-lg font-normal leading-7">
					<?php echo esc_html('Your support fuels measurable growth. Here is what our community has accomplished together.'); ?>
				</p>
			</div>
			<a href="<?php echo esc_url('#contact'); ?>" class="lg:inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-amber-400 text-zinc-800 text-base font-medium rounded-lg shrink-0 hidden">
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
						<span class="text-white text-3xl font-bold leading-9"><?php echo esc_html($stat['number']); ?></span>
					</div>
					<p class="text-gray-200 text-base font-normal leading-6"><?php echo esc_html($stat['desc']); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Sponsorship Tiers -->
	<?php
	$benefit_rows = array(
		array('benefit' => 'Recognition on the RECI website', 'advocate' => 'Included', 'partner' => 'Priority placement', 'lead' => 'Featured placement'),
		array('benefit' => 'Newsletter recognition', 'advocate' => 'Quarterly', 'partner' => 'Monthly', 'lead' => 'Dedicated feature'),
		array('benefit' => 'Social media acknowledgment', 'advocate' => 'Quarterly', 'partner' => 'Monthly', 'lead' => 'Campaign spotlight'),
		array('benefit' => 'Co-branded content opportunities', 'advocate' => '—', 'partner' => '1 activation', 'lead' => 'Multi-format series'),
		array('benefit' => 'Event and webinar visibility', 'advocate' => '—', 'partner' => 'Select events', 'lead' => 'Premier placement'),
		array('benefit' => 'Annual impact report recognition', 'advocate' => '—', 'partner' => 'Included', 'lead' => 'Featured story'),
		array('benefit' => 'Dedicated partnership support', 'advocate' => '—', 'partner' => 'Shared support', 'lead' => 'Dedicated manager'),
	);
	$partner_names = array('Partner logo', 'Partner logo', 'Partner logo', 'Partner logo', 'Partner logo');
	$community_slides = array(
		array(
			'quote'        => 'RECI has truly transformed how we approach equity education. The insights are practical, grounded, and immediately useful across our teams.',
			'author_name'  => 'Community Partner',
			'author_role'  => 'RECI sponsor',
			'author_image' => 'https://placehold.co/60x60',
			'author_alt'   => 'Community Partner',
		),
		array(
			'quote'        => 'Partnering with RECI helped us move from intention to action with a clearer strategy for engaging our staff and community.',
			'author_name'  => 'Program Lead',
			'author_role'  => 'Sponsorship collaborator',
			'author_image' => 'https://placehold.co/60x60',
			'author_alt'   => 'Program Lead',
		),
		array(
			'quote'        => 'The sponsorship relationship gave us a practical way to support equity work that feels measurable, community-rooted, and sustainable.',
			'author_name'  => 'Community Sponsor',
			'author_role'  => 'Funding partner',
			'author_image' => 'https://placehold.co/60x60',
			'author_alt'   => 'Community Sponsor',
		),
	);
	?>
	<div class="reci-container py-24 flex flex-col gap-24">
		<section class="flex flex-col gap-10">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
				<h2 class="text-neutral-800 text-5xl font-bold font-heading leading-tight">
					<?php echo esc_html('Sponsorship Levels & Benefits'); ?>
				</h2>
			</div>

			<div class="overflow-x-auto rounded-lg border border-zinc-300 bg-white">
				<div class="min-w-[920px]">
					<div class="grid grid-cols-[minmax(240px,1.3fr)_1fr_1fr_1fr] bg-neutral-800 text-white">
						<div class="px-6 py-5 text-lg font-bold font-heading"><?php echo esc_html('Benefit'); ?></div>
						<div class="px-6 py-5 text-center text-lg font-bold font-heading"><?php echo esc_html('Advocate'); ?></div>
						<div class="px-6 py-5 text-center text-lg font-bold font-heading"><?php echo esc_html('Partner'); ?></div>
						<div class="px-6 py-5 text-center text-lg font-bold font-heading"><?php echo esc_html('Lead Benefactor'); ?></div>
					</div>
					<?php foreach ($benefit_rows as $index => $row) : ?>
						<div class="grid grid-cols-[minmax(240px,1.3fr)_1fr_1fr_1fr] <?php echo 0 === $index ? '' : 'border-t border-zinc-300'; ?>">
							<div class="px-6 py-6 text-neutral-800 text-base font-medium leading-6 bg-white"><?php echo esc_html($row['benefit']); ?></div>
							<div class="px-6 py-6 text-center text-neutral-600 text-base font-normal leading-6">
								<div class="inline-flex items-center justify-center">
									<?php if ('—' !== $row['advocate']) : ?>
										<?php echo reci_inline_svg('assets/icons/checkbox-marked-circle.svg', 'w-5 h-5 text-reci-blue flex-shrink-0', ['aria-hidden' => 'true']); ?>
									<?php else : ?>
										<span aria-hidden="true">&nbsp;</span>
									<?php endif; ?>
								</div>
							</div>
							<div class="px-6 py-6 text-center text-neutral-600 text-base font-normal leading-6">
								<div class="inline-flex items-center justify-center">
									<?php if ('—' !== $row['partner']) : ?>
										<?php echo reci_inline_svg('assets/icons/checkbox-marked-circle.svg', 'w-5 h-5 text-reci-blue flex-shrink-0', ['aria-hidden' => 'true']); ?>
									<?php else : ?>
										<span aria-hidden="true">&nbsp;</span>
									<?php endif; ?>
								</div>
							</div>
							<div class="px-6 py-6 text-center text-neutral-600 text-base font-normal leading-6">
								<div class="inline-flex items-center justify-center">
									<?php if ('—' !== $row['lead']) : ?>
										<?php echo reci_inline_svg('assets/icons/checkbox-marked-circle.svg', 'w-5 h-5 text-reci-blue flex-shrink-0', ['aria-hidden' => 'true']); ?>
									<?php else : ?>
										<span aria-hidden="true">&nbsp;</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="flex flex-col items-center gap-14 text-center">
			<div class="flex items-center gap-3">
				<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
				<h2 class="text-neutral-800 text-5xl font-bold font-heading leading-tight">
					<?php echo esc_html('We Are Grateful for Our Valued Partners'); ?>
				</h2>
			</div>
			<div class="w-full grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-8 xl:gap-14 items-center">
				<?php foreach ($partner_names as $partner_name) : ?>
					<div class="h-20 rounded-lg border border-zinc-300 bg-white flex items-center justify-center">
						<span class="text-neutral-600 text-lg font-medium text-center"><?php echo esc_html($partner_name); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</div>

	<section id="contact" class="reci-container-full bg-neutral-800 border-t border-zinc-400/50 border-b border-zinc-400/50">
		<div class="reci-container py-24 flex flex-col gap-16">
			<div class="flex flex-col lg:flex-row gap-10 lg:gap-16 lg:items-start">
				<div class="w-full lg:max-w-[556px] flex flex-col gap-5">
					<h2 class="text-white text-5xl lg:text-[62px] font-bold font-heading leading-[1.15]">
						<?php echo esc_html('Ready to Make a Tangible Impact?'); ?>
					</h2>
					<p class="text-gray-200 text-lg font-normal leading-7 ">
						<?php echo esc_html("We would be honored to discuss how a partnership with RECI can align with your organization's goals and values. Let's start a conversation."); ?>
					</p>
				</div>
				<div class="w-full flex flex-col gap-10">
					<p class="text-gray-200 text-lg font-normal leading-7  max-w-2xl">
						<?php echo esc_html('Whether you are exploring a first partnership or scaling your commitment, our team can build a sponsorship plan that reflects your mission and community goals.'); ?>
					</p>
					<div class="flex flex-wrap items-center gap-4">
						<a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary btn-md">
							<?php echo esc_html('Contact us'); ?>
						</a>
						<a href="https://give.pitt.edu/campaigns/64590/donations/new?designation_id=races&a=12750048" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-md bg-reci-blue text-white border-0 hover:bg-blue-800">
							<?php echo esc_html('Make a donation'); ?>
						</a>
					</div>
				</div>
			</div>

			<div class="h-px bg-zinc-400/60"></div>

			<?php get_template_part('template-parts/common/community-section', null, [
				'title'         => 'Community Pulse',
				'slides'        => $community_slides,
				'wrapper_class' => 'p-8 lg:p-14 gap-10 lg:gap-20',
				'content_class' => 'w-full lg:max-w-[240px]',
				'quote_icon_class' => 'hidden lg:block pt-10 pr-8',
			]); ?>
		</div>
	</section>

</main>

<?php get_footer(); ?>
