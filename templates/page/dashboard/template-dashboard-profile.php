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
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>
		<div class="flex-1 p-6 lg:p-10">
			<?php
			get_template_part(
				'template-parts/dashboard/page-header',
				null,
				[ 'title' => 'Account Profile', 'subtitle' => 'Your contributor details, shared with collaborator onboarding and the submit flow.' ]
			);
			?>
			<?php get_template_part( 'template-parts/dashboard/profile-form' ); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
