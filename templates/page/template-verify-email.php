<?php
/**
 * Template Name: Verify Email
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="min-h-screen bg-slate-100 flex items-center justify-center px-4 py-20">
	<div class="w-full max-w-xl bg-white rounded-lg p-10 flex flex-col items-center text-center gap-6">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . rawurlencode( 'reci-collab.png' ) ); ?>" alt="<?php echo esc_attr__( 'RECI Logo', 'reci-media-hub' ); ?>" class="h-9 w-auto" />
		<h1 class="text-neutral-800 text-4xl font-bold font-heading">Verify Your Email</h1>
		<p class="text-neutral-600 text-base font-normal leading-6 ">Placeholder verification page. Final token validation and resend actions will be wired in the auth phase.</p>
		<div class="flex items-center gap-3">
			<a href="#" class="px-7 py-3.5 bg-[#003594] rounded-lg text-white text-base font-medium">Resend Link</a>
			<a href="<?php echo esc_url( home_url( '/sign-in/' ) ); ?>" class="px-7 py-3.5 border border-zinc-400 rounded-lg text-neutral-700 text-base font-medium">Sign In</a>
		</div>
	</div>
</main>
<?php get_footer(); ?>
