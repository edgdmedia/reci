<?php
/**
 * Template Name: Dashboard — Settings
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
				[ 'title' => 'Settings', 'subtitle' => 'Feed preferences, notifications, and journal defaults for your private RECI experience.' ]
			);
			?>
			<?php get_template_part( 'template-parts/dashboard/settings-form' ); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
