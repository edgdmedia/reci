<?php

/**
 * Full-screen navigation overlay (hamburger menu).
 *
 * Toggled open/closed by assets/js/theme.js.
 * 2-column grid on medium+ screens, single column on mobile.
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$assets_url = get_template_directory_uri() . "/assets/images/";

// ── Dynamic data ─────────────────────────────────────────────────────────────

$categories = get_categories(["hide_empty" => false]);

$content_type_links = [
    "Articles" =>
        (get_option("page_for_posts") ? get_post_type_archive_link("post") : home_url("/articles/")),
    "Videos" =>
        get_post_type_archive_link("reci_video") ?: home_url("/videos/"),
    "Podcasts" =>
        get_post_type_archive_link("reci_podcast") ?: home_url("/podcasts/"),
    "Events"   =>
        get_post_type_archive_link("reci_event") ?: home_url("/events/"),
		    "Quizzes"   =>
        get_post_type_archive_link("reci_assessment") ?: home_url("/quizzes/"),
];

$authors = array_slice(reci_media_hub_get_author_profile_options(true), 0, 10);

?>

<div
	id="menu-overlay"
	class="hidden"
	role="dialog"
	aria-modal="true"
	aria-label="Navigation menu"
	aria-hidden="true">

	<!-- Backdrop -->
	<div class="fixed inset-0 z-[70] bg-neutral-900/50" aria-hidden="true" onclick="document.getElementById('menu-close').click()"></div>

	<!-- Sidebar panel -->
	<div class="fixed right-0 top-0 z-[80] h-full w-[85vw] sm:w-[60vw] lg:w-[55vw] xl:w-[50vw] max-w-3xl bg-white overflow-y-auto border-l border-zinc-200 shadow-xl">
	<div class="w-full border-b border-zinc-200">
		<div class="px-4 sm:px-6 h-24 flex justify-between items-center">

			<a href="<?php echo esc_url(home_url("/")); ?>" class="flex items-center gap-5">
				<img
					src="<?php echo esc_url(
         $assets_url . "reci-collab.png",
     ); ?>"
					alt="<?php echo esc_attr(get_bloginfo("name")); ?>"
					class="h-10 md:h-14 w-auto" />
				
			</a>

			<button
				id="menu-close"
				aria-label="Close navigation menu"
				class="w-8 h-8 flex items-center justify-center cursor-pointer flex-shrink-0">
				<svg width="8" height="8" viewBox="0 0 32 32" class="w-8 h-8" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M25.3337 8.54663L23.4537 6.66663L16.0003 14.12L8.54699 6.66663L6.66699 8.54663L14.1203 16L6.66699 23.4533L8.54699 25.3333L16.0003 17.88L23.4537 25.3333L25.3337 23.4533L17.8803 16L25.3337 8.54663Z" fill="#525252" />
				</svg>
			</button>

		</div>
	</div>

	<!-- Mobile: single column. md+: 2 columns (col 1: Categories/Quiz/Donate/Social, col 2: RECI/Resources) -->
	<div class="px-4 sm:px-6 grid grid-cols-1 md:grid-cols-2 md:gap-x-10">

		<!-- ── Column 1: Categories, Quiz, Social ───────────────── -->
		<div>

			<!-- Categories -->
			<div class="pt-10 pb-2 border-b border-zinc-200 flex items-center gap-2">
				<?php echo reci_inline_svg("assets/icons/Tag.svg", "w-2 h-2 flex-shrink-0", ["aria-hidden" => "true"]); ?>
				<span class="text-neutral-800 text-lg font-bold">Categories</span>
			</div>

			<nav aria-label="Content categories" class="pb-5 pt-3">
				<?php foreach ($categories as $cat): ?>
					<a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" class="block pr-5 py-1 rounded-xl group">
						<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors"><?php echo esc_html($cat->name); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>

			<!-- Donate -->
			<div class="pt-6 pb-2 border-b border-zinc-200 flex items-center gap-2">
				<?php echo reci_inline_svg("assets/icons/Tag.svg", "w-2 h-2 flex-shrink-0", ["aria-hidden" => "true"]); ?>
				<span class="text-neutral-800 text-lg font-bold">Donate</span>
			</div>

			<div class="py-5 flex flex-col gap-5">
				<p class="text-neutral-700 text-xl font-normal leading-7 ">
					Support RECI's mission toward racial equity consciousness.
				</p>
				<a
					href="https://give.pitt.edu/campaigns/64590/donations/new?designation_id=races&a=12750048"
					target="_blank" rel="noopener noreferrer"
					class="self-start px-5 py-2.5 bg-amber-400 rounded-lg flex items-center gap-2 hover:bg-amber-300 transition-colors">
					<span class="text-zinc-800 text-sm font-medium leading-5">Donate</span>
				</a>
			</div>

		</div><!-- .col-1 -->

		<!-- ── Column 2: RECI, Resources ────────────────── -->
		<div>

			<!-- RECI -->
			<div class="pt-10 pb-2 border-b border-zinc-200 flex items-center gap-2">
				<?php echo reci_inline_svg("assets/icons/Tag.svg", "w-2 h-2 flex-shrink-0", ["aria-hidden" => "true"]); ?>
				<span class="text-neutral-800 text-lg font-bold">RECI</span>
			</div>

			<nav aria-label="RECI" class="pb-5 pt-3">
				<a href="<?php echo esc_url(home_url('/about/')); ?>" class="block pr-5 py-1 rounded-xl group">
					<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">About</span>
				</a>
				<a href="<?php echo esc_url(home_url('/framework/')); ?>" class="block pr-5 py-1 rounded-xl group">
					<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Framework</span>
				</a>
				<a href="<?php echo esc_url(home_url('/sponsorship/')); ?>" class="block pr-5 py-1 rounded-xl group">
					<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Sponsorship</span>
				</a>
			<a href="<?php echo esc_url(home_url('/community/')); ?>" class="block pr-5 py-1 rounded-xl group">
				<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Community</span>
			</a>
			<a href="<?php echo esc_url(home_url('/glossary/')); ?>" class="block pr-5 py-1 rounded-xl group">
				<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Glossary</span>
			</a>
		</nav>

			<!-- Resources -->
			<div class="pt-6 pb-2 border-b border-zinc-200 flex items-center gap-2">
				<?php echo reci_inline_svg("assets/icons/Tag.svg", "w-2 h-2 flex-shrink-0", ["aria-hidden" => "true"]); ?>
				<span class="text-neutral-800 text-lg font-bold">Resources</span>
			</div>

			<nav aria-label="Resources" class="pb-5 pt-3">
				<?php foreach ($content_type_links as $label => $url): ?>
					<?php if (in_array($label, ['Articles', 'Videos', 'Podcasts', 'Events', 'Quizzes'], true)): ?>
						<a href="<?php echo esc_url($url); ?>" class="block pr-5 py-1 rounded-xl group">
							<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors"><?php echo esc_html($label); ?></span>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
				<a href="<?php echo esc_url(get_post_type_archive_link('reci_reflection') ?: home_url('/reflections/')); ?>" class="block pr-5 py-1 rounded-xl group">
					<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Reflection Gallery</span>
				</a>
				<a href="<?php echo esc_url(get_post_type_archive_link('reci_course') ?: home_url('/courses/')); ?>" class="block pr-5 py-1 rounded-xl group">
					<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Learn</span>
				</a>
				<a href="<?php echo esc_url(get_post_type_archive_link('reci_author') ?: home_url('/collaborators/')); ?>" class="block pr-5 py-1 rounded-xl group">
					<span class="py-2 text-neutral-800 text-[24px] font-medium group-hover:text-amber-400 transition-colors">Collaborators</span>
				</a>
			</nav>

			<!-- Social Media -->
			<div class="pt-6 pb-2 border-b border-zinc-200 flex items-center gap-2">
				<?php echo reci_inline_svg("assets/icons/Tag.svg", "w-2 h-2 flex-shrink-0", ["aria-hidden" => "true"]); ?>
				<span class="text-neutral-800 text-lg font-bold">Follow RECI</span>
			</div>

			<div class="py-5">
				<div class="flex items-center gap-2">
					<a href="https://www.facebook.com/PittCRSP" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<?php echo reci_inline_svg("assets/icons/facebook.svg", "w-5 h-5", ["aria-hidden" => "true"]); ?>
					</a>
					<a href="https://x.com/FPittCRSP" target="_blank" rel="noopener noreferrer" aria-label="Twitter / X" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<?php echo reci_inline_svg("assets/icons/twitter.svg", "w-5 h-5", ["aria-hidden" => "true"]); ?>
					</a>
					<a href="https://www.instagram.com/pittcrsp/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<?php echo reci_inline_svg("assets/icons/instagram.svg", "w-5 h-5", ["aria-hidden" => "true"]); ?>
					</a>
					<a href="https://www.youtube.com/channel/UCpH5lubAtNU0WsSIQjjHgcg" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-amber-400 transition-colors">
						<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
					</a>
				</div>
			</div>

		</div><!-- .col-2 -->

	</div><!-- .grid -->

</div><!-- .sidebar-panel -->

</div><!-- #menu-overlay -->
