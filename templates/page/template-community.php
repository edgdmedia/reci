<?php
/**
 * Template Name: Community (Collaboratory)
 *
 * @package reci-media-hub
 */

get_header();
?>

<main class="layout-page">

	<!-- Page Header -->
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => "Racial Equity Collaboratory",
		'subtitle' => "Building Bridges, Advancing Change",
	]); ?>

	<!-- Overview Section -->
	<section class="reci-container pt-20 pb-16">
		<div class="max-w-6xl mx-auto flex flex-col gap-6 text-center">
			<div class="flex flex-col gap-5 text-neutral-600 text-lg font-normal leading-8 text-left sm:text-center">
				<p>
					<?php echo esc_html('The Racial Equity Collaboratory at the University of Pittsburgh is a pivotal resource dedicated to advancing social justice, racial equity, and transformative research. Serving as a centralized hub, the Collaboratory curates, amplifies, and connects racial equity-focused research and projects, facilitating meaningful partnerships between University faculty, staff, students, and external community organizations. By fostering interdisciplinary collaboration, the Collaboratory enables scholars and community partners to co-create impactful solutions, expand racial equity-focused education, and contribute to social transformation.'); ?>
				</p>
				<p>
					<?php echo esc_html('Our work extends beyond academia, forming robust connections between researchers, scholars, community partners, and advocates within and beyond the University. Through these partnerships, the Collaboratory not only addresses critical race-related issues but also enriches public understanding and policy, catalyzing change toward a more equitable society.'); ?>
				</p>
			</div>
		</div>
	</section>

	<!-- Explore Section -->
	<section class="reci-container-full bg-neutral-100 py-20 border-t border-b border-zinc-200">
		<div class="reci-container flex flex-col gap-12">
			<div class="text-center">
				<h2 class="text-neutral-800 text-4xl font-bold font-heading">
					<?php echo esc_html('Explore our Collaboratory'); ?>
				</h2>
			</div>
			
			<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
				<?php
				$explore_links = [
					[
						'label' => 'Research our Collaborators',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
						'url'   => 'https://www.crsp.pitt.edu/racial-equity-collaboratory/searchable-collaboratory-database',
					],
					[
						'label' => 'Become a Member',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
						'url'   => 'https://www.crsp.pitt.edu/racial-equity-collaboratory/racial-equity-collaboratory-sign',
					],
					[
						'label' => 'View our Media Library',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>',
						'url'   => 'https://www.crsp.pitt.edu/racial-equity-collaboratory/media-library',
					],
					[
						'label' => 'Announcements',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>',
						'url'   => 'https://www.crsp.pitt.edu/announcements',
					],
					[
						'label' => 'Events',
						'icon'  => '<svg class="w-8 h-8 mb-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
						'url'   => 'https://calendar.pitt.edu/department/center_on_race_and_social_problems',
					],
				];
				foreach ($explore_links as $link) :
				?>
					<a href="<?php echo esc_url($link['url']); ?>" class="flex flex-col items-center justify-center p-6 bg-white rounded-lg border border-zinc-200 shadow-sm hover:border-amber-400 hover:shadow-md transition-all text-center group">
						<?php echo $link['icon']; ?>
						<span class="text-neutral-800 text-lg font-bold group-hover:text-amber-500 transition-colors"><?php echo esc_html($link['label']); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Vision / Mission / Goal -->
	<section class="reci-container py-24">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<?php
			$pillars = [
				[
					'title' => 'Vision',
					'desc'  => 'To be a leading model in fostering interdisciplinary partnerships that generate lasting impact and innovation for racial justice and equity.',
				],
				[
					'title' => 'Mission',
					'desc'  => 'To drive racial equity and justice by uniting research, education, and community-based action across disciplines and generations.',
				],
				[
					'title' => 'Goal',
					'desc'  => 'To create a sustainable, collaborative platform that empowers individuals and groups to develop, share, and apply knowledge for lasting racial equity and social justice.',
				],
			];
			foreach ($pillars as $pillar) :
			?>
				<div class="p-8 bg-neutral-800 rounded-xl flex flex-col gap-6 text-white border-b-4 border-amber-400 transform transition-transform hover:-translate-y-1">
					<div class="flex items-center gap-3">
						<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
						<h3 class="text-3xl font-bold text-white font-heading"><?php echo esc_html($pillar['title']); ?></h3>
					</div>
					<p class="text-gray-300 text-lg font-normal leading-relaxed">
						<?php echo esc_html($pillar['desc']); ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

</main>

<?php get_footer(); ?>
