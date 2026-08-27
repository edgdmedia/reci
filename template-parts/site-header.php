<?php

/**
 * Site header — CENTERED Only.
 *
 * 2 rows:
 * - Row 1: Pitt logo + Sign in/Donate
 * - Row 2: [Search] - [RECI Logo] - [Reflections + Hamburger]
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$assets_url = get_template_directory_uri() . "/assets/images/";
$is_logged_in = is_user_logged_in();

$partner_logo_id = (int) reci_setting("branding_partner_logo");
$reci_logo_id = (int) reci_setting("branding_reci_logo");
$partner_logo_url = $partner_logo_id ? wp_get_attachment_image_url($partner_logo_id, "full") : '';
if (! $partner_logo_url) {
    $partner_logo_url = $assets_url . "pitt-logo.png";
}
$reci_logo_url = $reci_logo_id ? wp_get_attachment_image_url($reci_logo_id, "full") : '';
if (! $reci_logo_url) {
    $reci_logo_url = $assets_url . "reci-collab.png";
}
$hub_subtitle = reci_setting("branding_hub_subtitle", "Media Hub");
?>

<header id="site-header" class="relative z-40">

	<!-- Row 1: Pitt logo + Sign in/Donate -->
	<div class="w-full bg-reci-blue">
		<div class="reci-container py-4 flex flex-row justify-between items-start sm:items-center gap-4">

			<a href="https://crsp.pitt.edu" class="flex items-center">
				<img
					src="<?php echo esc_url($partner_logo_url); ?>"
					alt="<?php echo esc_attr(get_bloginfo("name")); ?>"
					class="h-10 md:h-12 w-auto" />
			</a>

			<div class="w-auto flex justify-end items-center gap-6 lg:gap-14">

			<div class="flex items-center gap-5">
					<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>" class="py-2 relative group flex items-center gap-1">
						<span class="text-white text-lg font-normal">Submit Content</span>
						<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
					</a>

				<?php if ($is_logged_in): ?>
					<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="py-2 relative group flex items-center gap-1" aria-label="Dashboard">
						<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
						<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
					</a>
					<a href="<?php echo esc_url(
          wp_logout_url(home_url("/")),
      ); ?>" class="py-2 relative group flex items-center gap-1">
							<span class="text-white text-lg font-normal hidden sm:inline-flex">Sign out</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					<?php else: ?>
						<a href="<?php echo esc_url( home_url( '/sign-in/' ) ); ?>" class="py-2 relative group flex items-center gap-1">
							<span class="text-white text-lg font-normal">Sign in/Join</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					<?php endif; ?>
				</div>

				<a href="https://give.pitt.edu/campaigns/64590/donations/new?designation_id=races&a=12750048" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-md text-2xl p-2 hidden sm:flex">
					<span>Donate</span>
				</a>

			</div>
		</div>
	</div>

</header>

<!-- Row 2: [Search] - [RECI Logo] - [Reflections + Hamburger] (sticky) -->
<div class="sticky top-0 z-[60] w-full bg-white border-b border-zinc-400/50">
				<div class="reci-container py-3.5 flex flex-row flex-nowrap justify-between items-center gap-4">

					<!-- Left: Search -->
					<div class="hidden lg:flex relative w-2/7">
						<div class="reci-search-container">
						<form role="search" method="get" action="<?php echo esc_url(
           home_url("/"),
       ); ?>" class="flex w-full md:w-[280px] px-5 py-3.5 bg-slate-100 rounded-lg items-center gap-2.5">
							<svg class="w-4 h-4 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
								<input
									type="search"
									name="s"
									value="<?php echo esc_attr(get_search_query()); ?>"
									placeholder="<?php esc_attr_e(
             "Search Articles, Videos, Podcasts…",
             "reci-media-hub",
         ); ?>"
									class="reci-live-search flex-1 bg-transparent text-neutral-600 text-sm font-light outline-none min-w-0 italic" />
							</form>
							<div class="reci-search-dropdown hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-zinc-200 max-h-[400px] overflow-y-auto z-50"></div>
						</div>
					</div>

					<!-- Mobile search icon -->
					<button
						id="search-toggle"
						type="button"
						aria-label="<?php esc_attr_e("Open search", "reci-media-hub"); ?>"
						aria-expanded="false"
						aria-controls="search-drawer"
						class="lg:hidden p-3 rounded-lg bg-slate-100 flex items-center justify-center">
						<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</button>

					<!-- Center: RECI Logo -->
					<a href="<?php echo esc_url(
         home_url("/"),
     ); ?>" class="flex items-center gap-2 w-3/7 justify-center">
						<img
							src="<?php echo esc_url($reci_logo_url); ?>"
							alt="RECI"
							class="h-12 md:h-20 w-auto object-contain" />
					</a>

					<!-- Right: Reflections + Hamburger -->
					<div class="w-2/7 flex flex-row flex-nowrap justify-end items-stretch sm:items-center gap-4 lg:gap-10">

						<!-- Reflections -->
						<a
							href="<?php echo esc_url(
           get_post_type_archive_link("reci_reflection") ?:
           home_url("/reflections/"),
       ); ?>"
							class="py-2 relative group items-center gap-1 flex-shrink-0 hidden md:flex">
						<?php echo reci_inline_svg(
			"assets/icons/lightbulb-on.svg",
			"w-4 h-4 flex-shrink-0",
			["aria-hidden" => "true"],
		); ?>
							<span class="text-neutral-800 text-[22px] font-normal">Reflections</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>

						<div class="hidden sm:block w-px h-7 bg-zinc-400/50 flex-shrink-0"></div>

						<!-- Hamburger -->
						<button
							id="menu-toggle"
							aria-label="<?php esc_attr_e("Open navigation menu", "reci-media-hub"); ?>"
							aria-expanded="false"
							aria-controls="menu-overlay"
							class="p-1.5 flex items-center gap-2.5 cursor-pointer">
							<?php echo reci_inline_svg("assets/icons/Menu.svg", "w-5 h-5", [
           "aria-hidden" => "true",
       ]); ?>
						</button>

					</div>
				</div>
			</div>

			<!-- Mobile search drawer -->
			<div
				id="search-drawer"
				class="md:hidden hidden absolute left-0 right-0 top-full bg-white border-b border-zinc-300 shadow-lg z-50"
				role="search">
				<div class="reci-container py-3">
					<form method="get" action="<?php echo esc_url(
         home_url("/"),
     ); ?>" class="flex items-center gap-3 bg-slate-100 rounded-lg px-4 py-3">
						<svg class="w-4 h-4 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						<input
							type="search"
							name="s"
							value="<?php echo esc_attr(get_search_query()); ?>"
							placeholder="<?php esc_attr_e(
          "Search Articles, Videos, Podcasts…",
          "reci-media-hub",
      ); ?>"
							class="flex-1 bg-transparent text-neutral-800 text-base font-light outline-none min-w-0" />
						<button
							type="button"
							id="search-close"
							aria-label="<?php esc_attr_e("Close search", "reci-media-hub"); ?>"
							class="p-1 text-neutral-600 hover:text-neutral-800 transition-colors flex-shrink-0">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</form>
		</div>
	</div>

<?php get_template_part("template-parts/menu", "overlay"); 
