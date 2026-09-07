<?php
/**
 * Template Name: Dashboard — Notifications
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user   = wp_get_current_user();
$notifications = reci_get_user_notifications( $current_user->ID, 50, false );

get_header( 'dashboard' );
?>
<main class="layout-page bg-slate-50">
	<div class="flex flex-col lg:flex-row min-h-screen">
		<?php get_template_part( 'template-parts/dashboard/sidebar' ); ?>
		<div class="flex-1 p-6 lg:p-10">
			<?php
			get_template_part(
				'template-parts/dashboard/page-header',
				null,
				[ 'title' => 'Notifications', 'subtitle' => 'Approvals, replies, and new work from the people you follow.' ]
			);
			?>
			<?php get_template_part( 'template-parts/dashboard/notifications-list', null, [ 'items' => $notifications, 'empty_message' => 'You have no notifications yet.' ] ); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
