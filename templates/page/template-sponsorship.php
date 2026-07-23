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
		'subtitle' => "Your sponsorship keeps RECI's resources open, our cohorts accessible, and our reach growing — locally and around the world.",
	]); ?>

	<!-- Sponsorship Intro & Blurbs Section -->
	<div class="reci-container lg:px-12 xl:px-20 py-24">
		<div class="flex flex-col gap-12">
			<!-- Intro -->
			<div class="flex flex-col gap-6 max-w-4xl mx-auto text-center">
				<p class="text-neutral-600 text-xl font-normal leading-8">
					<?php echo esc_html("Systemic problems require systemic solutions, and lasting change requires sustained investment. Sponsors and supporters make it possible for us to keep learning resources free and open, offer cohort seats to people who couldn't otherwise attend, advance the research behind our framework, and carry this work into new communities. When you fund RECI, you're not underwriting a single program — you're helping build the capacity of a whole community to recognize racism and act toward equity."); ?>
				</p>
			</div>

			<!-- What your support makes possible -->
			<div class="flex flex-col gap-6 mt-8">
				<h2 class="text-neutral-800 text-3xl font-bold font-heading text-center mb-6">
					<?php echo esc_html('What your support makes possible'); ?>
				</h2>
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					<?php
					$support_blurbs = array(
						array(
							'title' => 'Open, free resources',
							'desc'  => 'Articles, videos, podcasts, and self-assessments that anyone can access, anywhere.',
							'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
						),
						array(
							'title' => 'Accessible cohorts',
							'desc'  => 'Scholarship seats so cost is never the reason someone can\'t do the work.',
							'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>',
						),
						array(
							'title' => 'Research-backed practice',
							'desc'  => 'Support for the studies that test how our curriculum shapes understanding and behavior.',
							'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
						),
						array(
							'title' => 'Community partnerships',
							'desc'  => 'Collaborations with local organizations that root this work in lived experience.',
							'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
						),
						array(
							'title' => 'Global reach',
							'desc'  => 'Carrying the framework to new cities, campuses, and countries.',
							'icon'  => '<svg class="w-8 h-8 text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
						),
					);
					foreach ($support_blurbs as $blurb) :
					?>
						<div class="p-6 bg-white rounded-lg border border-zinc-300 flex flex-col gap-3 shadow-sm hover:border-amber-400 transition-colors">
							<?php echo $blurb['icon']; ?>
							<h3 class="text-neutral-800 text-xl font-bold font-heading"><?php echo esc_html($blurb['title']); ?></h3>
							<p class="text-neutral-600 text-base font-normal leading-relaxed"><?php echo esc_html($blurb['desc']); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- CTA Section -->
	<section id="contact" class="reci-container-full bg-neutral-800 border-t border-zinc-400/50 border-b border-zinc-400/50">
		<div class="reci-container py-24 flex flex-col gap-10 text-center items-center">
			<div class="max-w-3xl flex flex-col gap-6">
				<p class="text-gray-200 text-2xl font-medium leading-9 font-heading">
					<?php echo esc_html("Every sponsor is recognized as part of the community making this work possible — with visibility across our platform and at events, scaled to your level of support."); ?>
				</p>
				<p class="text-amber-400 text-lg font-normal leading-7">
					<?php echo esc_html("The RECI Collaboratory is part of the University of Pittsburgh's Center on Race and Social Problems (CRSP), within the School of Social Work."); ?>
				</p>
			</div>
			<div class="flex justify-center mt-6">
				<a href="https://give.pitt.edu/campaigns/64590/donations/new?designation_id=races&a=12750048" target="_blank" rel="noopener noreferrer" class="btn btn-primary bg-amber-400 text-zinc-800 hover:bg-amber-500 border-0 px-8 py-4 text-lg">
					<?php echo esc_html('Donate Now'); ?>
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
