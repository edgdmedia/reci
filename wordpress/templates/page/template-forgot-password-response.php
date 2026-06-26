<?php
/**
 * Template Name: Forgot Password Response
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
		<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . rawurlencode( 'RECI Logo - Version 2 1.png' ) ); ?>" alt="<?php echo esc_attr__( 'RECI Logo', 'reci-media-hub' ); ?>" class="h-9 w-auto" />
		<h1 class="text-neutral-800 text-4xl font-bold font-heading">Check Your Email</h1>
		<p class="text-neutral-600 text-base font-normal leading-6 ">This is a placeholder success state. We have sent a reset link if the account exists.</p>
		<a href="<?php echo esc_url( home_url( '/sign-in/' ) ); ?>" class="px-7 py-3.5 bg-[#003594] rounded-lg text-white text-base font-medium">Back to Sign In</a>
	</div>
</main>
<?php get_footer(); ?>
