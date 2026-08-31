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
			<div class="mb-8 max-w-3xl">
				<p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">Account</p>
				<h1 class="mt-3 font-heading text-3xl font-bold text-zinc-900">Account Profile</h1>
				<p class="mt-3 text-base leading-7 text-zinc-600">Update your account details here. These are the canonical contributor fields shared with collaborator onboarding and the RECI submit flow.</p>
			</div>
			<?php get_template_part( 'template-parts/dashboard/profile-form' ); ?>
		</div>
	</div>
</main>
<?php get_footer(); ?>
