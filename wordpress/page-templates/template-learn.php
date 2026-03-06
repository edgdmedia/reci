<?php
/**
 * Template Name: Learn
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
					<h1 class="text-5xl font-medium font-['EB_Garamond'] text-neutral-800 leading-tight">Learn</h1>
				</div>
				<div class="lg:pl-10 lg:border-l border-zinc-400 max-w-xl">
					<p class="text-neutral-500 text-lg font-normal font-['SF_Pro_Display'] leading-7">
						<?php echo esc_html( 'Deepen your understanding of racial equity through our curated courses, modules, and self-paced learning resources. Education is the foundation of lasting change.' ); ?>
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Filter Bar + Course Grid -->
	<section class="w-full">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 pt-5 pb-14">

			<!-- Filter Row -->
			<div class="pb-5 border-b border-zinc-400 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10">
				<div class="flex items-center gap-5">
					<span class="text-neutral-800 text-base font-bold font-['SF_Pro_Display']"><?php echo esc_html( 'Filter by:' ); ?></span>
					<nav class="flex items-center gap-6" aria-label="<?php echo esc_attr( 'Course filter tabs' ); ?>">
						<button type="button" class="py-2 text-neutral-800 text-base font-normal font-['SF_Pro_Display'] border-b-2 border-amber-400"><?php echo esc_html( 'All' ); ?></button>
						<button type="button" class="py-2 text-neutral-500 text-base font-normal font-['SF_Pro_Display'] hover:text-neutral-800"><?php echo esc_html( 'Beginner' ); ?></button>
						<button type="button" class="py-2 text-neutral-500 text-base font-normal font-['SF_Pro_Display'] hover:text-neutral-800"><?php echo esc_html( 'Intermediate' ); ?></button>
						<button type="button" class="py-2 text-neutral-500 text-base font-normal font-['SF_Pro_Display'] hover:text-neutral-800"><?php echo esc_html( 'Advanced' ); ?></button>
					</nav>
				</div>
				<div class="w-80 px-5 py-4 bg-white rounded-lg flex items-center gap-2.5">
					<svg class="w-4 h-4 text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
					</svg>
					<span class="text-neutral-500 text-base font-light font-['SF_Pro_Display']"><?php echo esc_html( 'Search Courses' ); ?></span>
				</div>
			</div>

			<?php
			$courses = array(
				array(
					'tag'      => 'Equity',
					'title'    => 'Racial Equity Foundations',
					'desc'     => 'Actionable insight for daily practice in racial equity.',
					'lessons'  => 4,
					'level'    => 'Beginner',
					'image'    => 'https://placehold.co/400x180/003594/ffffff?text=Racial+Equity',
				),
				array(
					'tag'      => 'Ethnicity',
					'title'    => 'Systemic Barriers Related to Ethnicity',
					'desc'     => 'Actionable insight for daily practice in dismantling barriers.',
					'lessons'  => 5,
					'level'    => 'Intermediate',
					'image'    => 'https://placehold.co/400x180/1e293b/ffffff?text=Ethnicity',
				),
				array(
					'tag'      => 'Faith',
					'title'    => 'Religion and Racial Justice',
					'desc'     => 'Exploring the intersection of faith traditions and racial equity.',
					'lessons'  => 3,
					'level'    => 'Beginner',
					'image'    => 'https://placehold.co/400x180/78350f/ffffff?text=Faith',
				),
				array(
					'tag'      => 'Borders',
					'title'    => 'Immigration Status and Systemic Exclusion',
					'desc'     => 'Understanding how borders shape racial identity and opportunity.',
					'lessons'  => 6,
					'level'    => 'Intermediate',
					'image'    => 'https://placehold.co/400x180/064e3b/ffffff?text=Immigration',
				),
				array(
					'tag'      => 'Identity',
					'title'    => 'Gender Identity and Sexual Orientation',
					'desc'     => 'Intersectional approaches to identity-affirming practice.',
					'lessons'  => 4,
					'level'    => 'Advanced',
					'image'    => 'https://placehold.co/400x180/4c1d95/ffffff?text=Identity',
				),
				array(
					'tag'      => 'Language',
					'title'    => 'Language, Power, and Racial Narrative',
					'desc'     => 'How language shapes perception and perpetuates or disrupts inequity.',
					'lessons'  => 5,
					'level'    => 'Advanced',
					'image'    => 'https://placehold.co/400x180/7f1d1d/ffffff?text=Language',
				),
			);
			?>

			<!-- Row 1 -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 mb-10">
				<?php foreach ( array_slice( $courses, 0, 3 ) as $course ) : ?>
				<article class="bg-white rounded-lg outline outline-[0.5px] outline-gray-200 flex flex-col overflow-hidden">
					<img
						src="<?php echo esc_url( $course['image'] ); ?>"
						alt="<?php echo esc_attr( $course['title'] ); ?>"
						class="w-full h-44 object-cover rounded-t"
					/>
					<div class="flex flex-col flex-1 justify-between p-2.5">
						<div class="flex flex-col gap-2.5 px-2.5 pb-4">
							<span class="inline-block bg-neutral-800 text-white text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded leading-4 self-start">
								<?php echo esc_html( $course['tag'] ); ?>
							</span>
							<h2 class="text-neutral-800 text-2xl font-semibold font-['EB_Garamond'] leading-7 line-clamp-2">
								<?php echo esc_html( $course['title'] ); ?>
							</h2>
							<p class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-6">
								<?php echo esc_html( $course['desc'] ); ?>
							</p>
						</div>
						<div class="flex justify-between items-center px-2.5 py-2">
							<a href="<?php echo esc_url( '#' ); ?>" class="flex items-center gap-1 text-amber-400 text-base font-normal font-['SF_Pro_Display']">
								<?php echo esc_html( 'Start course' ); ?>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
								</svg>
							</a>
							<span class="flex items-center gap-1 text-neutral-500 text-base font-normal font-['SF_Pro_Display']">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
								</svg>
								<?php echo esc_html( $course['lessons'] . ' lessons' ); ?>
							</span>
						</div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>

			<!-- Row 2 -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
				<?php foreach ( array_slice( $courses, 3 ) as $course ) : ?>
				<article class="bg-white rounded-lg outline outline-[0.5px] outline-gray-200 flex flex-col overflow-hidden">
					<img
						src="<?php echo esc_url( $course['image'] ); ?>"
						alt="<?php echo esc_attr( $course['title'] ); ?>"
						class="w-full h-44 object-cover rounded-t"
					/>
					<div class="flex flex-col flex-1 justify-between p-2.5">
						<div class="flex flex-col gap-2.5 px-2.5 pb-4">
							<span class="inline-block bg-neutral-800 text-white text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded leading-4 self-start">
								<?php echo esc_html( $course['tag'] ); ?>
							</span>
							<h2 class="text-neutral-800 text-2xl font-semibold font-['EB_Garamond'] leading-7 line-clamp-2">
								<?php echo esc_html( $course['title'] ); ?>
							</h2>
							<p class="text-neutral-500 text-sm font-normal font-['SF_Pro_Display'] leading-6">
								<?php echo esc_html( $course['desc'] ); ?>
							</p>
						</div>
						<div class="flex justify-between items-center px-2.5 py-2">
							<a href="<?php echo esc_url( '#' ); ?>" class="flex items-center gap-1 text-amber-400 text-base font-normal font-['SF_Pro_Display']">
								<?php echo esc_html( 'Start course' ); ?>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
								</svg>
							</a>
							<span class="flex items-center gap-1 text-neutral-500 text-base font-normal font-['SF_Pro_Display']">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
								</svg>
								<?php echo esc_html( $course['lessons'] . ' lessons' ); ?>
							</span>
						</div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>

		</div>
	</section>

	<!-- Connect Elements CTA -->
	<section class="w-full bg-neutral-800">
		<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 py-24">
			<div class="flex flex-col lg:flex-row items-start gap-10">
				<img
					src="<?php echo esc_url( 'https://placehold.co/415x347/374151/ffffff?text=Connect' ); ?>"
					alt="<?php echo esc_attr( 'Connect your learning interests' ); ?>"
					class="flex-1 rounded-lg border-b-[11px] border-amber-400 object-cover"
				/>
				<div class="flex flex-col gap-10 flex-1">
					<div class="flex flex-col gap-2.5">
						<span class="inline-block bg-amber-400 text-neutral-800 text-sm font-normal font-['SF_Pro_Display'] px-2 py-1 rounded self-start leading-4">
							<?php echo esc_html( 'Connect Elements' ); ?>
						</span>
						<h2 class="text-white text-5xl lg:text-6xl font-semibold font-['EB_Garamond'] leading-tight">
							<?php echo esc_html( 'Connect your interests to improve your feed and discover more relevant content' ); ?>
						</h2>
					</div>
					<a href="<?php echo esc_url( '#' ); ?>" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-amber-400 text-neutral-800 text-base font-medium font-['SF_Pro_Display'] rounded-lg self-start">
						<?php echo esc_html( 'Connect Now' ); ?>
					</a>
				</div>
			</div>

			<hr class="border-zinc-400 my-24"/>

			<!-- Community Pulse Testimonial -->
			<div class="p-14 bg-neutral-600 rounded-lg flex flex-col lg:flex-row gap-28 relative">
				<div class="flex flex-col gap-2.5 shrink-0">
					<div class="text-white text-5xl font-medium font-['EB_Garamond'] leading-tight w-56">
						<?php echo esc_html( 'Community Pulse' ); ?>
					</div>
				</div>
				<div class="flex-1 flex flex-col gap-10 py-10">
					<blockquote class="text-white text-2xl font-normal font-['EB_Garamond'] leading-10">
						<?php echo esc_html( 'RECI has truly transformed how we approach market analysis. The insights are unparalleled!' ); ?>
					</blockquote>
					<hr class="border-zinc-400"/>
					<div class="flex justify-between items-center">
						<div class="flex items-center gap-2.5">
							<img
								src="<?php echo esc_url( 'https://placehold.co/60x60/6b7280/ffffff?text=JD' ); ?>"
								alt="<?php echo esc_attr( 'Jane Doe' ); ?>"
								class="w-14 h-14 rounded-full outline outline-4 outline-white object-cover"
							/>
							<div>
								<p class="text-white text-xl font-bold font-['SF_Pro_Display'] leading-8"><?php echo esc_html( 'Jane Doe' ); ?></p>
								<p class="text-zinc-400 text-base font-medium font-['SF_Pro_Display'] leading-6"><?php echo esc_html( 'Financial Analyst' ); ?></p>
							</div>
						</div>
						<div class="flex items-center gap-3">
							<button type="button" class="p-4 rounded-lg outline outline-[0.5px] outline-zinc-400" aria-label="<?php echo esc_attr( 'Previous testimonial' ); ?>">
								<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
								</svg>
							</button>
							<button type="button" class="p-4 rounded-lg outline outline-[0.5px] outline-zinc-400" aria-label="<?php echo esc_attr( 'Next testimonial' ); ?>">
								<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
								</svg>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
