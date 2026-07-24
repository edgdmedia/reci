<?php
/**
 * 404 template.
 *
 * @package reci-media-hub
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>
<main class="min-h-[80vh] flex flex-col justify-center items-center px-6 py-20 bg-slate-50">
	<div class="max-w-xl text-center flex flex-col items-center gap-6">
		<h1 class="text-8xl md:text-9xl font-bold font-heading text-[#003594]">
			404
		</h1>
		<div class="flex flex-col gap-3">
			<h2 class="text-3xl font-bold text-neutral-800">
				<?php esc_html_e( 'Page Not Found', 'reci-media-hub' ); ?>
			</h2>
			<p class="text-lg text-neutral-600">
				<?php esc_html_e( "Sorry, the page you are looking for doesn't exist or has been moved.", 'reci-media-hub' ); ?>
			</p>
		</div>
		<div class="pt-6 flex flex-col sm:flex-row gap-4 items-center justify-center w-full">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-secondary btn-md w-full sm:w-auto text-center">
				<?php esc_html_e( 'Back to Home', 'reci-media-hub' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn btn-outline btn-md w-full sm:w-auto text-center border-neutral-300 text-neutral-700 hover:bg-neutral-100 transition-colors px-6 py-3 rounded-lg font-medium">
				<?php esc_html_e( 'Go to Dashboard', 'reci-media-hub' ); ?>
			</a>
		</div>
	</div>
</main>
<?php
get_footer();

