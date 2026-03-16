<?php

/**
 * Site header — two-bar navigation.
 *
 * Bar 1 (blue): logo, sign-in/join, donate.
 * Bar 2 (white): RECI logo, search, reflection gallery, hamburger.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$icons_url    = get_template_directory_uri() . '/figma/assets/';
$assets_url   = get_template_directory_uri() . '/assets/images/';
$is_logged_in = is_user_logged_in();

// Settings-driven values with hardcoded fallbacks.
$partner_logo_id  = (int) reci_setting( 'branding_partner_logo' );
$reci_logo_id     = (int) reci_setting( 'branding_reci_logo' );
$partner_logo_url = $partner_logo_id ? wp_get_attachment_image_url( $partner_logo_id, 'full' ) : $assets_url . 'pitt-logo.png';
$reci_logo_url    = $reci_logo_id    ? wp_get_attachment_image_url( $reci_logo_id, 'full' )    : $assets_url . rawurlencode( 'RECI Logo - Version 2 1.png' );
$hub_subtitle     = reci_setting( 'branding_hub_subtitle', 'Media Hub' );
?>
<header id="site-header" class="relative z-40">

	<!-- ── Navbar 2: top blue bar ──────────────────────────────── -->
	<div class="w-full bg-[#003594]">
		<div class="reci-container py-4 flex flex-row flex-nowrap justify-between items-start sm:items-center gap-4">

			<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center">
				<img
					src="<?php echo esc_url( $partner_logo_url ); ?>"
					alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
					class="h-10 md:h-12 w-auto" />
			</a>

			<div class="w-auto flex justify-end items-center gap-6 lg:gap-14">

				<div class="flex items-center gap-5">
					<?php if ($is_logged_in) : ?>
						<a href="<?php echo esc_url(get_edit_profile_url()); ?>" class="py-2 relative group flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">My Account</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="py-2 relative group flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Sign out</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="py-2 relative group flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Sign in</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(home_url('/sign-up/')); ?>" class="py-2 relative group flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Join RECI</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					<?php endif; ?>
				</div>

				<a
					href="<?php echo esc_url(home_url('/donate/')); ?>"
					class="btn btn-primary btn-md hidden md:flex">
					<span>Donate</span>
				</a>

			</div>
		</div>
	</div>

	<!-- ── Navbar: white secondary bar ────────────────────────── -->
	<div class="w-full bg-white border-b border-zinc-400/50">
		<div class="reci-container py-3.5 flex flex-row flex-nowrap justify-between items-center gap-4">

			<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-5 w-2/3">
				<img
					src="<?php echo esc_url( $reci_logo_url ); ?>"
					alt="RECI"
					class="h-10 md:h-12 w-1/2 md:w-44 object-contain" />
				<div class="w-px h-7 bg-zinc-400/50"></div>
				<div class="text-neutral-800 text-sm md:text-lg font-medium font-['SF_Pro_Display']"><?php echo esc_html( $hub_subtitle ); ?></div>
			</a>

			<div class="w-1/2 lg:flex-1 flex flex-row flex-nowrap justify-end items-stretch sm:items-center gap-4 lg:gap-10">

				<!-- Search: desktop inline bar -->
				<form
					role="search"
					method="get"
					action="<?php echo esc_url( home_url( '/' ) ); ?>"
					class="hidden md:flex w-80 px-5 py-3.5 bg-slate-100 rounded-lg items-center gap-2.5">
					<img
						src="<?php echo esc_url( $icons_url . 'search-icon.svg' ); ?>"
						alt=""
						class="w-4 h-4 flex-shrink-0 opacity-50"
						aria-hidden="true" />
					<input
						type="search"
						name="s"
						value="<?php echo esc_attr( get_search_query() ); ?>"
						placeholder="<?php esc_attr_e( 'Search Articles, Videos, Podcasts…', 'reci-media-hub' ); ?>"
						class="flex-1 bg-transparent text-neutral-500 text-base font-light font-['SF_Pro_Display'] outline-none min-w-0" />
				</form>

				<!-- Search: mobile icon (opens drawer) -->
				<button
					id="search-toggle"
					type="button"
					aria-label="<?php esc_attr_e( 'Open search', 'reci-media-hub' ); ?>"
					aria-expanded="false"
					aria-controls="search-drawer"
					class="md:hidden p-2 rounded-lg bg-slate-100 flex items-center justify-center">
					<img
						src="<?php echo esc_url( $icons_url . 'search-icon.svg' ); ?>"
						alt=""
						class="w-5 h-5 flex-shrink-0"
						aria-hidden="true" />
				</button>

				<div class="hidden sm:block w-px h-7 bg-zinc-400/50 flex-shrink-0"></div>

				<!-- Reflections -->
				<a
					href="<?php echo esc_url(get_post_type_archive_link('reci_reflection') ?: home_url('/reflections/')); ?>"
					class="py-2 relative group items-center gap-1 flex-shrink-0 hidden sm:flex">
					<img
						src="<?php echo esc_url($icons_url . 'lightbulb-on.svg'); ?>"
						alt=""
						class="w-4 h-4 flex-shrink-0"
						aria-hidden="true" />
					<span class="text-neutral-800 text-base font-normal font-['SF_Pro_Display']">Reflections</span>
					<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
				</a>

				<div class="hidden sm:block w-px h-7 bg-zinc-400/50 flex-shrink-0"></div>

				<!-- Hamburger -->
				<button
					id="menu-toggle"
					aria-label="Open navigation menu"
					aria-expanded="false"
					aria-controls="menu-overlay"
					class="p-1.5 flex items-center gap-2.5 cursor-pointer">
					<img
						src="<?php echo esc_url($icons_url . 'Menu.svg'); ?>"
						alt=""
						class="w-5 h-5"
						aria-hidden="true" />
				</button>

			</div>
		</div>
	</div>

	<!-- Mobile search dropdown (absolutely positioned, floats over content) -->
	<div
		id="search-drawer"
		class="md:hidden hidden absolute left-0 right-0 top-full bg-white border-b border-zinc-300 shadow-lg z-50"
		role="search">
		<div class="reci-container py-3">
			<form
				method="get"
				action="<?php echo esc_url( home_url( '/' ) ); ?>"
				class="flex items-center gap-3 bg-slate-100 rounded-lg px-4 py-3">
				<img
					src="<?php echo esc_url( $icons_url . 'search-icon.svg' ); ?>"
					alt=""
					class="w-4 h-4 flex-shrink-0 opacity-50"
					aria-hidden="true" />
				<input
					id="search-drawer-input"
					type="search"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Search Articles, Videos, Podcasts…', 'reci-media-hub' ); ?>"
					class="flex-1 bg-transparent text-neutral-800 text-base font-light font-['SF_Pro_Display'] outline-none min-w-0" />
				<button
					type="button"
					id="search-close"
					aria-label="<?php esc_attr_e( 'Close search', 'reci-media-hub' ); ?>"
					class="p-1 text-neutral-500 hover:text-neutral-800 transition-colors flex-shrink-0">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</form>
		</div>
	</div>

</header>

<?php get_template_part('template-parts/menu', 'overlay'); ?>
