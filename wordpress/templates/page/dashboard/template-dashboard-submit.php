<?php
/**
 * Template Name: Dashboard — Submit Content
 *
 * Mounts the RECMH multi-step React submission experience inside the dashboard layout.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header('dashboard');
?>
<main class="layout-page">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>
		<div class="flex-1 p-6 lg:p-10">
			<h1 class="text-2xl font-bold font-heading text-zinc-800 mb-8">Submit Content</h1>
			<div id="reci-submission-root"></div>
		</div>
	</div>
</main>
<?php get_footer(); ?>
