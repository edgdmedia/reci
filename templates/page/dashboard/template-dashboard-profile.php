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
				[
					'title'    => 'Account Profile',
					'subtitle' => ( function_exists( 'reci_user_is_collaborator' ) && reci_user_is_collaborator( get_current_user_id() ) )
						? 'Your contributor details, shared with your public profile and the submit flow.'
						: 'Your account details.',
				]
			);
			?>
			<?php get_template_part( 'template-parts/dashboard/profile-form' ); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
