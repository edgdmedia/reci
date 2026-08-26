<?php
/**
 * Reusable community engagement section:
 * - Join community ("Connect Elements")
 * - Community Pulse quote slider
 *
 * Args:
 * - enabled (bool)
 * - show_join (bool)
 * - show_pulse (bool)
 * - join_image_url (string)
 * - join_image_alt (string)
 * - join_tag (string)
 * - join_title (string)
 * - join_cta_label (string)
 * - join_cta_url (string)
 * - pulse_title (string)
 * - pulse_slides (array)
 */

if (!defined('ABSPATH')) {
	exit;
}

$section_args = wp_parse_args(
	is_array($args ?? null) ? $args : [],
	[
		'enabled'        => true,
		'show_join'      => true,
		'show_pulse'     => true,
		'join_image_url' => get_template_directory_uri() . '/assets/images/connect-now3.png',
		'join_image_alt' => __('Connect elements', 'reci-media-hub'),
		'join_tag'       => __('Connect Elements', 'reci-media-hub'),
		'join_title'     => __('Explore how RECI Community works, how to join, and how to contribute', 'reci-media-hub'),
		'join_cta_label' => __('Explore Community', 'reci-media-hub'),
		'join_cta_url'   => home_url('/community/'),
		'pulse_title'    => __('Community Pulse', 'reci-media-hub'),
		'pulse_slides'   => [],
	]
);

if (empty($section_args['enabled'])) {
	return;
}
?>

<div class="reci-container-full py-12 lg:py-24 bg-neutral-800">
	<div class="reci-container flex flex-col justify-start items-start gap-12 lg:gap-24">
		<?php if (!empty($section_args['show_join'])) : ?>
			<div class="Content self-stretch flex flex-col lg:flex-row justify-start items-start gap-6 lg:gap-10">
				<img
					data-layer="Image"
					class="Image flex-1 self-stretch p-2.5 rounded-lg border-b-[11px] border-amber-400"
					src="<?php echo esc_url((string) $section_args['join_image_url']); ?>"
					alt="<?php echo esc_attr((string) $section_args['join_image_alt']); ?>"
				/>
				<div class="Content inline-flex flex-col justify-start items-start gap-10">
					<div data-layer="Tag" class="Tag flex flex-col justify-start items-start gap-2.5">
						<div data-layer="Tag" class="Tag px-2 py-1 bg-amber-400 rounded inline-flex justify-center items-center gap-2.5">
							<div data-layer="Connect Elements" class="ConnectElements justify-start text-neutral-800 text-sm font-normal leading-4">
								<?php echo esc_html((string) $section_args['join_tag']); ?>
							</div>
						</div>
						<div data-layer="Title" class="w-full lg:max-w-[785px] justify-start text-white text-4xl lg:text-6xl font-bold font-heading leading-tight lg:leading-[74.40px]">
							<?php echo esc_html((string) $section_args['join_title']); ?>
						</div>
					</div>
					<a href="<?php echo esc_url((string) $section_args['join_cta_url']); ?>" class="btn btn-primary btn-md">
						<?php echo esc_html((string) $section_args['join_cta_label']); ?>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($section_args['show_join']) && !empty($section_args['show_pulse'])) : ?>
			<div data-layer="Vector 4" class="Vector4 divider divider-zinc"></div>
		<?php endif; ?>

		<?php if (!empty($section_args['show_pulse'])) : ?>
			<?php get_template_part('template-parts/common/community-section', null, [
				'title'  => (string) $section_args['pulse_title'],
				'slides' => $section_args['pulse_slides'],
			]); ?>
		<?php endif; ?>
	</div>
</div>
