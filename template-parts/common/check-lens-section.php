<?php
/**
 * Reusable "Check your lens" section.
 *
 * Args:
 * - enabled (bool): Whether to render the section. Default true.
 * - title (string): Section heading.
 * - view_all_label (string): View-all button label.
 * - view_all_url (string): View-all button URL.
 * - cards (array): Lens cards passed to lens-quiz-row.
 */

if (!defined('ABSPATH')) {
	exit;
}

$section_args = wp_parse_args(
	is_array($args ?? null) ? $args : [],
	[
		'enabled'        => true,
		'title'          => __('Check your lens', 'reci-media-hub'),
		'view_all_label' => __('View all', 'reci-media-hub'),
		'view_all_url'   => get_post_type_archive_link('reci_assessment') ?: home_url('/quizzes/'),
		'cards'          => [],
	]
);

if (empty($section_args['enabled'])) {
	return;
}
?>

<div class="reci-container-full bg-white p-12 lg:p-24">
	<div class="reci-container self-stretch rounded-lg bg-slate-100 py-10 lg:py-14 flex flex-col justify-start items-start gap-8 lg:gap-10">
		<div class="self-stretch flex flex-row justify-between items-center gap-4">
			<div data-layer="Check your lens" class="CheckYourLens text-neutral-800 reci-section-title font-medium">
				<?php echo esc_html((string) $section_args['title']); ?>
			</div>
			<a href="<?php echo esc_url((string) $section_args['view_all_url']); ?>" class="btn btn-outline-primary btn-sm md:btn-md">
				<?php echo esc_html((string) $section_args['view_all_label']); ?>
			</a>
		</div>
		<?php get_template_part('template-parts/listings/lens-quiz-row', null, ['cards' => $section_args['cards']]); ?>
	</div>
</div>
