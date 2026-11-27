<?php

/**
 * Site header — one sticky white bar, per Pitt's brand standards for websites.
 *
 * Left to right: Pitt shield + unit lockup | RECI logo | search, menu, Donate.
 *
 * The lockup follows the white two-line header in Pitt's starter template
 * (communications.pitt.edu → Brand Standards for Websites → Site Title/Logos):
 * the official blue shield at 135×42, a thin royal-blue rule, then the unit
 * name as HTML Roboto text — parent unit at 17px over the unit at 24px Light.
 * It is text, not an image, so search engines and screen readers can read it.
 *
 * Reflections, Sign in, Join and Submit Content live in the menu overlay.
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$assets_url = get_template_directory_uri() . "/assets/images/";

$reci_logo_id = (int) reci_setting("branding_reci_logo");
// The RECI mark renders 40-64px tall. reci_logo_img() asks for a size that
// fits and emits a srcset, rather than pulling the full-size original.
$reci_logo_html = reci_logo_img(
    $reci_logo_id,
    $assets_url . "reci-collab.png",
    "RECI Collaboratory",
    "h-8 sm:h-10 md:h-14 xl:h-16 w-auto max-w-full object-contain",
    "(min-width: 1280px) 249px, (min-width: 768px) 218px, 156px"
);

// The unit lockup. The parent unit sits above the unit itself.
$unit_parent = reci_setting("branding_unit_parent", "School of Social Work");
$unit_name   = reci_setting("branding_unit_name", "Center on Race and Social Problems");
$unit_url    = reci_setting("branding_unit_url", "https://crsp.pitt.edu");

$donate_url = "https://give.pitt.edu/campaigns/64590/donations/new?designation_id=races&a=12750048";
?>

<header id="site-header" class="sticky top-0 z-[60] w-full bg-white border-b border-zinc-400/50" role="banner">
	<div class="reci-container py-4 md:py-5 flex items-center justify-between gap-2 md:gap-6">

		<!-- Left: Pitt shield + unit lockup -->
		<div class="flex items-center shrink-0">
			<a href="https://www.pitt.edu" title="University of Pittsburgh" class="shrink-0 lg:pr-[18px] lg:mr-[19px] lg:border-r lg:border-[#003594]/75 py-3">
				<img
					src="<?php echo esc_url($assets_url . "pitt-logo-shield-blue.png"); ?>"
					alt="University of Pittsburgh"
					width="135" height="42"
					class="w-[92px] sm:w-[120px] md:w-[135px] h-auto">
			</a>
			<a href="<?php echo esc_url($unit_url); ?>" class="reci-unit-lockup hidden lg:block whitespace-nowrap font-sans text-[#003594] no-underline hover:text-[#003594]">
				<span class="block font-bold text-[13px] xl:text-[17px] leading-tight"><?php echo esc_html($unit_parent); ?></span>
				<span class="block font-light text-[17px] xl:text-[24px] leading-[1.18]"><?php echo esc_html($unit_name); ?></span>
			</a>
		</div>

		<!-- Center: RECI logo -->
		<a href="<?php echo esc_url(home_url("/")); ?>" class="flex-1 min-w-0 flex items-center justify-center" aria-label="<?php esc_attr_e("RECI Media Hub home", "reci-media-hub"); ?>">
			<?php echo $reci_logo_html; // phpcs:ignore WordPress.Security.EscapeOutput -- built by reci_logo_img(), already escaped. ?>
		</a>

		<!-- Right: search, menu, Donate -->
		<div class="flex items-center justify-end gap-0 sm:gap-3 lg:gap-4 shrink-0">

			<button
				id="search-toggle"
				type="button"
				aria-label="<?php esc_attr_e("Open search", "reci-media-hub"); ?>"
				aria-expanded="false"
				aria-controls="search-drawer"
				class="p-2.5 rounded-lg text-[#003594] hover:bg-slate-100 transition-colors flex items-center justify-center">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
				</svg>
			</button>

			<button
				id="menu-toggle"
				type="button"
				aria-label="<?php esc_attr_e("Open navigation menu", "reci-media-hub"); ?>"
				aria-expanded="false"
				aria-controls="menu-overlay"
				class="p-2.5 rounded-lg text-[#003594] hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer">
				<?php echo reci_inline_svg("assets/icons/Menu.svg", "w-6 h-6", ["aria-hidden" => "true"]); ?>
			</button>

			<a href="<?php echo esc_url($donate_url); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-md text-lg xl:text-xl px-4 py-2 hidden sm:flex shrink-0">
				<span>Donate</span>
			</a>

		</div>
	</div>

	<!-- Search drawer: drops below the bar at every screen size -->
	<div
		id="search-drawer"
		class="hidden absolute left-0 right-0 top-full bg-white border-b border-zinc-300 shadow-lg"
		role="search">
		<div class="reci-container py-4">
			<div class="reci-search-container relative max-w-3xl mx-auto">
				<form method="get" action="<?php echo esc_url(home_url("/")); ?>" class="flex items-center gap-3 bg-slate-100 rounded-lg px-4 py-3">
					<svg class="w-4 h-4 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
					<label for="search-drawer-input" class="sr-only"><?php esc_html_e("Search the site", "reci-media-hub"); ?></label>
					<input
						id="search-drawer-input"
						type="search"
						name="s"
						value="<?php echo esc_attr(get_search_query()); ?>"
						placeholder="<?php esc_attr_e("Search Articles, Videos, Podcasts…", "reci-media-hub"); ?>"
						autocomplete="off"
						class="reci-live-search flex-1 bg-transparent text-neutral-800 text-base font-light outline-none min-w-0" />
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
				<div class="reci-search-dropdown hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-zinc-200 max-h-[400px] overflow-y-auto z-50"></div>
			</div>
		</div>
	</div>
</header>

<?php get_template_part("template-parts/menu", "overlay");
