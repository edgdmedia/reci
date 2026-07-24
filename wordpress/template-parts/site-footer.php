<?php

/**
 * Site footer.
 *
 * Mirrors the homepage reference footer section.
 *
 * @package reci-media-hub
 */

if (!defined("ABSPATH")) {
    exit();
}

$assets_url = get_template_directory_uri() . "/assets/images/";

// Settings-driven values with hardcoded fallbacks.
$footer_email = reci_setting("footer_email", "mediahub@reci.pitt.edu");
$footer_phone = reci_setting("footer_phone", "+14126480000");
$footer_address = reci_setting(
    "footer_address",
    "4200 Fifth Avenue\nPittsburgh, PA 15260",
);
$footer_copyright = reci_setting("footer_copyright");

$reci_logo_id = (int) reci_setting("branding_reci_logo");
$partner_logo_id = (int) reci_setting("branding_partner_logo");
$reci_logo_url = $reci_logo_id
    ? wp_get_attachment_image_url($reci_logo_id, "full")
    : $assets_url . "reci-collab.png";
$partner_logo_url = $partner_logo_id
    ? wp_get_attachment_image_url($partner_logo_id, "full")
    : $assets_url . "pitt-logo.png";
$hub_subtitle = reci_setting("branding_hub_subtitle", "Media Hub");
?>
<footer class="w-full bg-blue-900">
	<div class="reci-container pt-12 lg:pt-24 flex flex-col gap-8 lg:gap-10">

		<!-- Logo -->
		<div class="flex items-center">
			<a href="<?php echo esc_url(
       home_url("/"),
   ); ?>" class="flex flex-wrap items-center gap-x-5 gap-y-3">
				<img
					src="<?php echo esc_url($partner_logo_url); ?>"
					alt="<?php echo esc_attr(get_bloginfo("name")); ?>"
					class="h-16 w-auto" />
				<div class="hidden sm:block w-px h-7 bg-zinc-400/50 origin-center mx-1"></div>
			</a>
		</div>

		<!-- Nav columns + newsletter -->
		<div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16 xl:gap-20">

			<!-- Nav columns -->
			<div class="col-span-1 lg:col-span-3 grid grid-cols-4 justify-between gap-8 lg:gap-12 xl:gap-16">

				<!-- RECI content -->
				<div class="col-span-2 md:col-span-1 gap-3">
					<span class="text-slate-300 text-2xl font-bold font-heading leading-8 ">RECI</span>
					<nav class="flex flex-col gap-1" aria-label="RECI">
						<a href="<?php echo esc_url(
          (get_option("page_for_posts") ? get_post_type_archive_link("post") : home_url("/articles/")),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Articles</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          get_post_type_archive_link("reci_podcast") ?: home_url("/podcasts/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Podcasts</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          get_post_type_archive_link("reci_video") ?: home_url("/videos/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Videos</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          home_url("/framework/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Framework</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          home_url("/glossary/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Glossary</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					</nav>
				</div>

				<!-- Quick Links -->
				<div class="col-span-2 md:col-span-1 gap-3">
					<span class="text-slate-300 text-2xl font-bold font-heading leading-8 ">Quick Links</span>
					<nav class="flex flex-col gap-1" aria-label="Quick Links">
						<a href="<?php echo esc_url(
          home_url("/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Home</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
	          get_post_type_archive_link("reci_course") ?: home_url("/courses/"),
	      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Learn</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          home_url("/framework/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Framework</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          get_post_type_archive_link("reci_reflection") ?:
          home_url("/reflections/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Reflect</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(
          home_url("/community/"),
      ); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-md font-normal">Community</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					</nav>
				</div>

				<!-- Contact (address, email, phone) -->
				<div class="col-span-2 md:col-span-2 gap-3">
					<span class="text-slate-300 text-2xl font-bold font-heading leading-8 ">Contact</span>
					<div class=" flex flex-col gap-1 text-white text-md font-normal leading-6 ">
						<p class="leading-6">
							<?php echo nl2br(esc_html($footer_address)); ?>
						</p>
						<?php if ($footer_email): ?>
							<a href="mailto:<?php echo esc_attr(
           $footer_email,
       ); ?>" class="max-w-full break-words hover:text-amber-400 transition-colors"><?php echo esc_html(
    $footer_email,
); ?></a>
						<?php endif; ?>
						<?php if ($footer_phone): ?>
							<a href="tel:<?php echo esc_attr(
           preg_replace("/[^+\d]/", "", $footer_phone),
       ); ?>" class="max-w-full break-words hover:text-amber-400 transition-colors"><?php echo esc_html(
    $footer_phone,
); ?></a>
						<?php endif; ?>
					</div>
				</div>

			</div><!-- /.nav columns -->

			<!-- Newsletter -->
			<div class="col-span-1 md:col-span-2 gap-6 lg:flex-[1.7]">
				<div class="flex flex-col gap-3">
					<h2 class="text-white text-3xl font-bold font-heading leading-10">Connect with RECI</h2>
				</div>
				<form method="post" action="<?php echo esc_url(
        admin_url("admin-post.php"),
    ); ?>" class="flex flex-col gap-3">
					<?php wp_nonce_field("reci_newsletter_subscribe", "reci_newsletter_nonce"); ?>
					<input type="hidden" name="action" value="reci_newsletter_subscribe" />

					<div class="py-2 border-b border-indigo-300 flex items-center gap-2">
						<input
							type="email"
							name="email"
							placeholder="Your email"
							required
							class="flex-1 bg-transparent text-slate-300 text-base font-normal leading-6 outline-none" />
						<button
							type="submit"
							class="flex items-center gap-1 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
								<path d="M1 8h14M9 2l6 6-6 6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<span class="text-white text-base font-normal leading-6">Submit</span>
						</button>
					</div>

					<p class="text-white text-sm font-normal leading-5">
						Subscribe to our newsletter for weekly insights, updates, and exclusive contents
					</p>
				</form>

				<div class="pt-8">
					<span class="text-slate-300 text-lg font-bold font-heading">Follow RECI</span>
				<div class="flex items-center gap-3 pt-4">
					<a href="https://www.facebook.com/PittCRSP" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="w-10 h-10 border border-indigo-300 rounded-full flex items-center justify-center hover:bg-white hover:border-white transition-colors group">
						<?php echo reci_inline_svg("assets/icons/facebook.svg", "w-5 h-5 text-indigo-300 group-hover:text-blue-900", ["aria-hidden" => "true"]); ?>
					</a>
					<a href="https://x.com/FPittCRSP" target="_blank" rel="noopener noreferrer" aria-label="Twitter / X" class="w-10 h-10 border border-indigo-300 rounded-full flex items-center justify-center hover:bg-white hover:border-white transition-colors group">
						<?php echo reci_inline_svg("assets/icons/twitter.svg", "w-5 h-5 text-indigo-300 group-hover:text-blue-900", ["aria-hidden" => "true"]); ?>
					</a>
					<a href="https://www.instagram.com/pittcrsp/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="w-10 h-10 border border-indigo-300 rounded-full flex items-center justify-center hover:bg-white hover:border-white transition-colors group">
						<?php echo reci_inline_svg("assets/icons/instagram.svg", "w-5 h-5 text-indigo-300 group-hover:text-blue-900", ["aria-hidden" => "true"]); ?>
					</a>
					<a href="https://www.youtube.com/channel/UCpH5lubAtNU0WsSIQjjHgcg" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="w-10 h-10 border border-indigo-300 rounded-full flex items-center justify-center hover:bg-white hover:border-white transition-colors group">
						<svg class="w-5 h-5 text-indigo-300 group-hover:text-red-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
					</a>
				</div>
				</div>
			</div>

		</div><!-- /.flex.flex-col.xl:flex-row -->

		<!-- Legal bar -->
		<div class="pt-10 pb-14 border-t border-indigo-300 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

			<p class="text-white text-sm font-normal leading-6">
				<?php if ($footer_copyright) {
        echo esc_html($footer_copyright);
    } else {
        echo "&copy; " . esc_html(date("Y")) . " RECI. All rights reserved.";
    } ?>
			</p>

			<nav class="flex flex-wrap items-center gap-5" aria-label="Legal">
				<?php
    $privacy_url = get_privacy_policy_url();
    if ($privacy_url): ?>
					<a href="<?php echo esc_url(
         $privacy_url,
     ); ?>" class="py-2 relative group inline-flex items-center">
						<span class="text-white text-base font-normal">Privacy Policy</span>
						<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
					</a>
				<?php else: ?>
					<a href="<?php echo esc_url(
         home_url("/privacy-policy/"),
     ); ?>" class="py-2 relative group inline-flex items-center">
						<span class="text-white text-base font-normal">Privacy Policy</span>
						<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
					</a>
				<?php endif;
    ?>
				<a href="<?php echo esc_url(
        home_url("/terms-of-use/"),
    ); ?>" class="py-2 relative group inline-flex items-center">
					<span class="text-white text-base font-normal">Terms of Use</span>
					<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
				</a>
				<a href="<?php echo esc_url(
        home_url("/cookies/"),
    ); ?>" class="py-2 relative group inline-flex items-center">
					<span class="text-white text-base font-normal">Cookies</span>
					<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
				</a>
			</nav>

		</div>

	</div>
</footer>
