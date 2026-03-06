<?php
/**
 * Template Name: General Post Page
 *
 * @package reci-media-hub
 */

get_header();

// Placeholder post data – all hardcoded, no WP_Query.
$post_title    = 'Understanding Systemic Racism: A Comprehensive Overview';
$post_category = 'Equity';
$post_date     = 'January 8, 2026';
$post_author   = 'Dr. Amara Williams';
$author_title  = 'Senior Fellow, Racial Equity Studies';
$read_time     = '12 min read';
$hero_image    = 'https://placehold.co/1200x500/003594/ffffff?text=Feature+Image';
$author_avatar = 'https://placehold.co/80x80/6b7280/ffffff?text=AW';

$toc_items = array(
	array( 'id' => 'introduction', 'label' => 'Introduction' ),
	array( 'id' => 'defining-systemic-racism', 'label' => 'Defining Systemic Racism' ),
	array( 'id' => 'historical-context', 'label' => 'Historical Context' ),
	array( 'id' => 'manifestations-today', 'label' => 'Manifestations Today' ),
	array( 'id' => 'pathways-to-change', 'label' => 'Pathways to Change' ),
	array( 'id' => 'further-reading', 'label' => 'Further Reading' ),
);
?>

<main class="bg-slate-100 min-h-screen">

	<!-- Breadcrumb -->
	<div class="w-full border-b border-zinc-200 bg-white">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-3">
			<nav class="flex items-center gap-2 text-sm font-['SF_Pro_Display']" aria-label="<?php echo esc_attr( 'Breadcrumb' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-neutral-500 hover:text-neutral-800"><?php echo esc_html( 'Home' ); ?></a>
				<span class="text-neutral-400" aria-hidden="true">/</span>
				<a href="<?php echo esc_url( '#' ); ?>" class="text-neutral-500 hover:text-neutral-800"><?php echo esc_html( 'Articles' ); ?></a>
				<span class="text-neutral-400" aria-hidden="true">/</span>
				<span class="text-neutral-800 font-medium"><?php echo esc_html( $post_title ); ?></span>
			</nav>
		</div>
	</div>

	<!-- Hero -->
	<section class="w-full bg-neutral-800 border-b border-zinc-400">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">
			<div class="max-w-3xl flex flex-col gap-5">
				<div class="flex items-center gap-2">
					<span class="inline-block bg-amber-400 text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded leading-4">
						<?php echo esc_html( $post_category ); ?>
					</span>
					<span class="w-2 h-2 bg-amber-400 rounded-sm inline-block"></span>
					<span class="text-neutral-400 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html( $post_date ); ?></span>
					<span class="w-2 h-2 bg-amber-400 rounded-sm inline-block"></span>
					<span class="text-neutral-400 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html( $read_time ); ?></span>
				</div>
				<h1 class="text-white text-4xl lg:text-5xl font-semibold font-['EB_Garamond'] leading-tight">
					<?php echo esc_html( $post_title ); ?>
				</h1>
				<p class="text-gray-300 text-lg font-normal font-['SF_Pro_Display'] leading-7">
					<?php echo esc_html( 'A deep dive into the structures, history, and modern-day expressions of systemic racism, and what communities and institutions can do to dismantle them.' ); ?>
				</p>
			</div>
		</div>
	</section>

	<!-- Featured Image -->
	<div class="w-full">
		<img
			src="<?php echo esc_url( $hero_image ); ?>"
			alt="<?php echo esc_attr( $post_title ); ?>"
			class="w-full h-[400px] lg:h-[500px] object-cover"
		/>
	</div>

	<!-- Body: Sidebar TOC + Content -->
	<div class="w-full">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-16">
			<div class="flex flex-col lg:flex-row items-start gap-14">

				<!-- Table of Contents (sticky sidebar) -->
				<aside class="lg:w-72 shrink-0 lg:sticky lg:top-8" aria-label="<?php echo esc_attr( 'Table of contents' ); ?>">
					<div class="bg-white rounded-lg p-8 outline outline-[0.5px] outline-gray-200">
						<div class="flex items-center gap-3 mb-5">
							<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block"></span>
							<h2 class="text-neutral-800 text-xl font-bold font-['EB_Garamond']"><?php echo esc_html( 'Contents' ); ?></h2>
						</div>
						<nav>
							<ol class="flex flex-col gap-1">
								<?php foreach ( $toc_items as $i => $item ) : ?>
								<li>
									<a
										href="<?php echo esc_url( '#' . $item['id'] ); ?>"
										class="flex items-center gap-3 py-2 text-neutral-600 text-base font-normal font-['SF_Pro_Display'] hover:text-[#003594] group"
									>
										<span class="text-neutral-400 text-sm shrink-0"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
										<?php echo esc_html( $item['label'] ); ?>
									</a>
								</li>
								<?php endforeach; ?>
							</ol>
						</nav>
					</div>
					<!-- Share block -->
					<div class="bg-white rounded-lg p-8 outline outline-[0.5px] outline-gray-200 mt-5">
						<h3 class="text-neutral-800 text-base font-bold font-['SF_Pro_Display'] mb-4"><?php echo esc_html( 'Share this article' ); ?></h3>
						<div class="flex items-center gap-3">
							<a href="<?php echo esc_url( '#' ); ?>" class="p-2.5 bg-slate-100 rounded-lg text-neutral-600 hover:bg-[#003594] hover:text-white transition-colors" aria-label="<?php echo esc_attr( 'Share on Twitter/X' ); ?>">
								<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.261 5.632L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
							</a>
							<a href="<?php echo esc_url( '#' ); ?>" class="p-2.5 bg-slate-100 rounded-lg text-neutral-600 hover:bg-[#003594] hover:text-white transition-colors" aria-label="<?php echo esc_attr( 'Share on LinkedIn' ); ?>">
								<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
							</a>
							<a href="<?php echo esc_url( '#' ); ?>" class="p-2.5 bg-slate-100 rounded-lg text-neutral-600 hover:bg-amber-400 hover:text-neutral-800 transition-colors" aria-label="<?php echo esc_attr( 'Copy link' ); ?>">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2m-6 12h8a2 2 0 0 1 2-2v-8a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v8a2 2 0 0 1 2 2z"/></svg>
							</a>
						</div>
					</div>
				</aside>

				<!-- Article Body -->
				<article class="flex-1 min-w-0">

					<div id="introduction" class="mb-12">
						<h2 class="text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-9 mb-5"><?php echo esc_html( 'Introduction' ); ?></h2>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8 mb-4">
							<?php echo esc_html( 'Systemic racism—also referred to as institutional or structural racism—describes the ways in which historical, cultural, institutional, and interpersonal dynamics perpetuate a racial hierarchy that consistently privileges white people and disadvantages people of color.' ); ?>
						</p>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8">
							<?php echo esc_html( 'Unlike individual prejudice, systemic racism does not require individual intent. It operates through policies, practices, and norms that produce racially disparate outcomes, even when the actors within those systems hold no overt bias.' ); ?>
						</p>
					</div>

					<div id="defining-systemic-racism" class="mb-12">
						<h2 class="text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-9 mb-5"><?php echo esc_html( 'Defining Systemic Racism' ); ?></h2>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8 mb-4">
							<?php echo esc_html( 'Scholars define systemic racism as the compounding of biases across interacting institutions and social systems, resulting in racial inequities in wealth, education, criminal justice, housing, health care, and political representation.' ); ?>
						</p>
						<div class="bg-white rounded-lg p-8 border-l-4 border-amber-400 my-8">
							<blockquote class="text-neutral-700 text-xl font-normal font-['EB_Garamond'] leading-9 italic">
								<?php echo esc_html( '"Systemic racism is what gives individual acts of racism their power. It is the water in which we all swim—visible to some, invisible to others, but shaping the life chances of everyone."' ); ?>
							</blockquote>
							<cite class="block mt-4 text-neutral-500 text-base font-medium font-['SF_Pro_Display'] not-italic">
								<?php echo esc_html( '— Dr. Patricia Hill Collins, Sociologist' ); ?>
							</cite>
						</div>
					</div>

					<div id="historical-context" class="mb-12">
						<h2 class="text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-9 mb-5"><?php echo esc_html( 'Historical Context' ); ?></h2>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8 mb-4">
							<?php echo esc_html( "America's racial hierarchy was codified in law from the earliest colonial period. The transatlantic slave trade, Jim Crow segregation, redlining, and mass incarceration are not isolated historical events—they form a continuous thread of policy that has shaped the disparities we observe today." ); ?>
						</p>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8">
							<?php echo esc_html( 'Understanding this lineage is essential to understanding why race-neutral policies often fail to produce race-neutral outcomes. The playing field was not leveled when legal segregation ended; the structural disadvantages accumulated over centuries remained intact.' ); ?>
						</p>
					</div>

					<div id="manifestations-today" class="mb-12">
						<h2 class="text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-9 mb-5"><?php echo esc_html( 'Manifestations Today' ); ?></h2>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8 mb-6">
							<?php echo esc_html( 'Systemic racism manifests across every major social institution. Key examples include:' ); ?>
						</p>
						<?php
						$manifestations = array(
							array(
								'area'  => 'Housing',
								'desc'  => 'Persistent racial wealth gaps driven by discriminatory lending practices and neighborhood disinvestment.',
							),
							array(
								'area'  => 'Education',
								'desc'  => 'School funding tied to property taxes concentrates resources in wealthy (predominantly white) districts.',
							),
							array(
								'area'  => 'Criminal Justice',
								'desc'  => 'Black Americans are incarcerated at more than five times the rate of white Americans.',
							),
							array(
								'area'  => 'Health Care',
								'desc'  => 'Racial disparities in maternal mortality, chronic disease rates, and access to quality care are well-documented.',
							),
						);
						?>
						<div class="flex flex-col gap-4">
							<?php foreach ( $manifestations as $m ) : ?>
							<div class="flex items-start gap-3 bg-white rounded-lg p-6 outline outline-[0.5px] outline-gray-200">
								<span class="w-3 h-3 bg-amber-400 rounded-sm inline-block shrink-0 mt-1.5"></span>
								<div>
									<p class="text-neutral-800 text-lg font-semibold font-['EB_Garamond'] leading-7 mb-1"><?php echo esc_html( $m['area'] ); ?></p>
									<p class="text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6"><?php echo esc_html( $m['desc'] ); ?></p>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div id="pathways-to-change" class="mb-12">
						<h2 class="text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-9 mb-5"><?php echo esc_html( 'Pathways to Change' ); ?></h2>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8 mb-4">
							<?php echo esc_html( 'Dismantling systemic racism requires action at every level—individual, organizational, and policy. Researchers and practitioners emphasize a combination of anti-racist education, institutional audits, reparative policy, and community-led solutions.' ); ?>
						</p>
						<p class="text-neutral-600 text-lg font-normal font-['SF_Pro_Display'] leading-8">
							<?php echo esc_html( 'RECI provides resources and courses designed to support this work at each of these levels. Explore our Learn section for practical tools you can use today.' ); ?>
						</p>
					</div>

					<div id="further-reading" class="mb-12">
						<h2 class="text-neutral-800 text-3xl font-semibold font-['EB_Garamond'] leading-9 mb-5"><?php echo esc_html( 'Further Reading' ); ?></h2>
						<ul class="flex flex-col gap-3">
							<?php
							$reading = array(
								'How to Be an Antiracist — Ibram X. Kendi',
								'The New Jim Crow — Michelle Alexander',
								'Stamped from the Beginning — Ibram X. Kendi',
								'White Fragility — Robin DiAngelo',
							);
							foreach ( $reading as $book ) :
							?>
							<li class="flex items-center gap-3 text-neutral-600 text-base font-normal font-['SF_Pro_Display'] leading-6">
								<span class="w-2 h-2 bg-amber-400 rounded-sm inline-block shrink-0"></span>
								<?php echo esc_html( $book ); ?>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<!-- Author Card -->
					<div class="mt-16 pt-10 border-t border-zinc-200">
						<div class="bg-white rounded-lg p-8 outline outline-[0.5px] outline-gray-200 flex flex-col sm:flex-row items-start gap-6">
							<img
								src="<?php echo esc_url( $author_avatar ); ?>"
								alt="<?php echo esc_attr( $post_author ); ?>"
								class="w-20 h-20 rounded-full object-cover outline outline-4 outline-amber-400 shrink-0"
							/>
							<div class="flex flex-col gap-2">
								<div class="flex items-center gap-2">
									<span class="w-2 h-2 bg-amber-400 rounded-sm inline-block"></span>
									<span class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display']"><?php echo esc_html( 'Written by' ); ?></span>
								</div>
								<h3 class="text-neutral-800 text-2xl font-bold font-['EB_Garamond'] leading-7"><?php echo esc_html( $post_author ); ?></h3>
								<p class="text-neutral-500 text-base font-medium font-['SF_Pro_Display']"><?php echo esc_html( $author_title ); ?></p>
								<p class="text-neutral-500 text-base font-normal font-['SF_Pro_Display'] leading-6 mt-2">
									<?php echo esc_html( 'Dr. Williams is a nationally recognized scholar and speaker whose work focuses on racial equity in education, public policy, and organizational practice.' ); ?>
								</p>
							</div>
						</div>
					</div>

				</article>
			</div>
		</div>
	</div>

</main>

<?php get_footer(); ?>
