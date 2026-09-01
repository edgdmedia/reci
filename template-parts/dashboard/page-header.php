<?php
/**
 * Dashboard page header.
 *
 * Mirrors the public site's page-title-card: amber square marker, condensed
 * heading face, neutral ink. The dashboard sits inside a sidebar layout, so the
 * heading steps down to 4xl below large screens and matches the site's 5xl above.
 *
 * Args:
 * - title    (string) Page heading.
 * - subtitle (string) Supporting line beneath it.
 * - action   (string) Optional pre-escaped HTML for a right-aligned action.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = wp_parse_args(
	$args ?? [],
	[
		'title'    => '',
		'subtitle' => '',
		'action'   => '',
	]
);

if ( '' === $args['title'] ) {
	return;
}
?>

<div class="mb-10 flex flex-col gap-5 border-b border-zinc-300 pb-8 lg:flex-row lg:items-end lg:justify-between">
	<div class="max-w-3xl">
		<div class="flex items-center gap-3">
			<span class="w-3 h-3 bg-amber-400 rounded-sm shrink-0"></span>
			<h1 class="text-neutral-800 text-4xl lg:text-5xl font-bold font-heading leading-[1.05]"><?php echo esc_html( (string) $args['title'] ); ?></h1>
		</div>
		<?php if ( '' !== $args['subtitle'] ) : ?>
			<p class="mt-4 text-neutral-700 text-lg font-normal leading-7"><?php echo esc_html( (string) $args['subtitle'] ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( '' !== $args['action'] ) : ?>
		<div class="shrink-0"><?php echo wp_kses_post( (string) $args['action'] ); ?></div>
	<?php endif; ?>
</div>
