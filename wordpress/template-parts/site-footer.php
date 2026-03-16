<?php

/**
 * Site footer.
 *
 * Mirrors figma/homepage-body.php footer section.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

$assets_url = get_template_directory_uri() . '/assets/images/';

// Settings-driven values with hardcoded fallbacks.
$footer_email     = reci_setting( 'footer_email',   'mediahub@reci.pitt.edu' );
$footer_phone     = reci_setting( 'footer_phone',   '+14126480000' );
$footer_address   = reci_setting( 'footer_address', "4200 Fifth Avenue\nPittsburgh, PA 15260" );
$footer_copyright = reci_setting( 'footer_copyright' );

$reci_logo_id      = (int) reci_setting( 'branding_reci_logo' );
$partner_logo_id   = (int) reci_setting( 'branding_partner_logo' );
$reci_logo_url     = $reci_logo_id ? wp_get_attachment_image_url( $reci_logo_id, 'full' ) : $assets_url . rawurlencode( 'RECI Logo - Version 2 1.png' );
$partner_logo_url  = $partner_logo_id ? wp_get_attachment_image_url( $partner_logo_id, 'full' ) : $assets_url . 'pitt-logo.png';
$hub_subtitle      = reci_setting( 'branding_hub_subtitle', 'Media Hub' );
?>
<footer class="w-full bg-blue-900">
	<div class="reci-container pt-12 lg:pt-24 flex flex-col gap-8 lg:gap-10">

		<!-- Logo -->
		<div class="flex items-center gap-5">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-5">
				<img
					src="<?php echo esc_url( $partner_logo_url ); ?>"
					alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
					class="h-12 w-auto" />
				<div class="w-px h-7 bg-zinc-400/50 origin-center mx-1"></div>
				<span class="text-white text-lg font-medium font-['SF_Pro_Display']"><?php echo esc_html( $hub_subtitle ); ?></span>
			</a>
		</div>

		<!-- Nav columns + newsletter -->
		<div class="flex flex-col xl:flex-row gap-10 xl:gap-20">

			<!-- Nav columns -->
			<div class="flex flex-wrap gap-10 xl:gap-20">

				<!-- RECI content -->
				<div class="flex flex-col gap-3">
					<span class="text-slate-400 text-xl font-bold font-['EB_Garamond'] leading-8 tracking-tight">RECI</span>
					<nav class="flex flex-col gap-1">
						<a href="<?php echo esc_url(get_post_type_archive_link('reci_article') ?: home_url('/articles/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Articles</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(get_post_type_archive_link('reci_podcast') ?: home_url('/podcasts/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Podcasts</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(get_post_type_archive_link('reci_video') ?: home_url('/videos/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Videos</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					</nav>
				</div>

				<!-- Quick Links -->
				<div class="flex flex-col gap-3">
					<span class="text-slate-400 text-xl font-bold font-['EB_Garamond'] leading-8 tracking-tight">Quick Links</span>
					<nav class="flex flex-col gap-1">
						<a href="<?php echo esc_url(home_url('/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Home</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(home_url('/learn/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Learn</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(get_post_type_archive_link('reci_reflection') ?: home_url('/reflections/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Reflect</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
						<a href="<?php echo esc_url(home_url('/community/')); ?>" class="py-2 relative group inline-flex items-center gap-1">
							<span class="text-white text-base font-normal font-['SF_Pro_Display']">Community</span>
							<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
						</a>
					</nav>
				</div>

				<!-- Address -->
				<div class="flex flex-col gap-3">
					<span class="text-slate-400 text-xl font-bold font-['EB_Garamond'] leading-8 tracking-tight">Address</span>
					<p class="w-48 text-white text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
						<?php echo nl2br( esc_html( $footer_address ) ); ?>
					</p>
				</div>

				<!-- Contact -->
				<div class="flex flex-col gap-3">
					<span class="text-slate-400 text-xl font-bold font-['EB_Garamond'] leading-8 tracking-tight">Contact</span>
					<div class="w-48 flex flex-col gap-1 text-white text-base font-normal font-['SF_Pro_Display'] leading-6 tracking-tight">
						<?php if ( $footer_email ) : ?>
							<a href="mailto:<?php echo esc_attr( $footer_email ); ?>" class="hover:text-amber-400 transition-colors"><?php echo esc_html( $footer_email ); ?></a>
						<?php endif; ?>
						<?php if ( $footer_phone ) : ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $footer_phone ) ); ?>" class="hover:text-amber-400 transition-colors"><?php echo esc_html( $footer_phone ); ?></a>
						<?php endif; ?>
					</div>
				</div>

			</div><!-- /.flex.flex-wrap -->

			<!-- Newsletter -->
			<div class="flex-1 flex flex-col gap-5">
				<div class="flex flex-col gap-3">
					<h2 class="text-white text-3xl font-bold font-['EB_Garamond'] leading-10">Connect with RECI</h2>
				</div>
				<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="flex flex-col gap-3">
					<?php wp_nonce_field('reci_newsletter_subscribe', 'reci_newsletter_nonce'); ?>
					<input type="hidden" name="action" value="reci_newsletter_subscribe" />

					<div class="py-2 border-b border-indigo-300 flex items-center gap-2">
						<input
							type="email"
							name="email"
							placeholder="Your email"
							required
							class="flex-1 bg-transparent text-slate-400 text-base font-normal font-['SF_Pro_Display'] leading-6 outline-none" />
						<button
							type="submit"
							class="flex items-center gap-1 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
								<path d="M1 8h14M9 2l6 6-6 6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<span class="text-white text-base font-normal font-['SF_Pro_Display'] leading-6">Submit</span>
						</button>
					</div>

					<p class="text-white text-xs font-normal font-['SF_Pro_Display'] leading-5">
						Subscribe to our newsletter for weekly insights, updates, and exclusive contents
					</p>
				</form>
			</div>

		</div><!-- /.flex.flex-col.xl:flex-row -->

		<!-- Legal bar -->
		<div class="pt-10 pb-14 border-t border-indigo-300 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

			<p class="text-white text-sm font-normal font-['SF_Pro_Display'] leading-6">
				<?php
				if ( $footer_copyright ) {
					echo esc_html( $footer_copyright );
				} else {
					echo '&copy; ' . esc_html( date( 'Y' ) ) . ' RECI. All rights reserved.';
				}
				?>
			</p>

			<nav class="flex flex-wrap items-center gap-5" aria-label="Legal">
				<?php
				$privacy_url = get_privacy_policy_url();
				if ($privacy_url) :
				?>
					<a href="<?php echo esc_url($privacy_url); ?>" class="py-2 relative group inline-flex items-center">
						<span class="text-white text-base font-normal font-['SF_Pro_Display']">Privacy Policy</span>
						<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="py-2 relative group inline-flex items-center">
						<span class="text-white text-base font-normal font-['SF_Pro_Display']">Privacy Policy</span>
						<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url(home_url('/terms-of-use/')); ?>" class="py-2 relative group inline-flex items-center">
					<span class="text-white text-base font-normal font-['SF_Pro_Display']">Terms of Use</span>
					<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
				</a>
				<a href="<?php echo esc_url(home_url('/cookies/')); ?>" class="py-2 relative group inline-flex items-center">
					<span class="text-white text-base font-normal font-['SF_Pro_Display']">Cookies</span>
					<span class="absolute bottom-0 left-0 w-full h-px bg-amber-400 opacity-0 group-hover:opacity-100 transition-opacity"></span>
				</a>
			</nav>

		</div>

	</div>
</footer>
