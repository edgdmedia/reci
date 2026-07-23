<?php
/**
 * Template Name: Dashboard — Profile
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
			<h1 class="text-2xl font-bold font-heading text-zinc-800 mb-8">Profile</h1>
			<?php get_template_part( 'template-parts/dashboard/profile-form' ); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
