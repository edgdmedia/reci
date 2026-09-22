<?php
/**
 * Guidelines panel.
 *
 * Used above the Submit Content flow and inside the community policy overlay.
 * Renders nothing at all when no body has been configured, so an unconfigured
 * site does not show an empty box.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panel = wp_parse_args(
	$args ?? [],
	[
		'title'       => __( 'Before you submit', 'reci-media-hub' ),
		'body'        => '',
		'collapsible' => false,
	]
);

if ( '' === trim( (string) $panel['body'] ) ) {
	return;
}
?>
<?php if ( $panel['collapsible'] ) : ?>
<details class="mb-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6" open>
	<summary class="cursor-pointer font-heading text-xl font-bold text-zinc-900">
		<?php echo esc_html( $panel['title'] ); ?>
	</summary>
	<div class="prose prose-zinc mt-4 max-w-none text-base leading-7 text-zinc-700">
		<?php echo wp_kses_post( $panel['body'] ); ?>
	</div>
</details>
<?php else : ?>
<div class="mb-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
	<h2 class="font-heading text-xl font-bold text-zinc-900"><?php echo esc_html( $panel['title'] ); ?></h2>
	<div class="prose prose-zinc mt-4 max-w-none text-base leading-7 text-zinc-700">
		<?php echo wp_kses_post( $panel['body'] ); ?>
	</div>
</div>
<?php endif; ?>
