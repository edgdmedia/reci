<?php
/**
 * Template Name: Submit Content
 *
 * Mounts the RECMH multi-step React submission experience.
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<main class="layout-page">
	<?php
	get_template_part(
		'template-parts/common/page-title-card',
		null,
		[
			'title'    => 'Submit Content',
			'subtitle' => 'Share evidence-based, process-oriented work that advances racial equity consciousness.',
		]
	);
	?>
	<section class="reci-container-full bg-slate-100">
		<div class="reci-container py-12 lg:py-14">
			<div id="reci-submission-root"></div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
