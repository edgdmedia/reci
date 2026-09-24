<?php
/**
 * Share controls for a reflection prompt.
 *
 * The two toggles sit on one row. Their explanations are a hover/focus tooltip
 * on a small info button rather than a paragraph under each label, which is
 * what made them stack and eat the height of the form.
 *
 * Data attributes, not ids: a reflection may hold several prompt chapters.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Sharing needs an account. The styles already tell a signed-out visitor to
// log in, and reading what others shared needs no account at all.
if ( ! is_user_logged_in() ) {
	return;
}

$reci_share = wp_parse_args(
	$args ?? [],
	[
		'tone_class' => 'reci-reflection-soft-text',
		'align'      => 'left',
	]
);

$reci_info = static function ( string $text ): void {
	printf(
		'<button type="button" class="reci-info" aria-label="%1$s" title="%2$s"><span aria-hidden="true">i</span><span class="reci-sr-only">%2$s</span></button>',
		esc_attr__( 'More information', 'reci-media-hub' ),
		esc_attr( $text )
	);
};
?>
<div class="reci-share-controls mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm <?php echo 'center' === $reci_share['align'] ? 'justify-center ' : ''; ?><?php echo esc_attr( (string) $reci_share['tone_class'] ); ?>" data-reci-share-controls>
	<label class="inline-flex items-center gap-2">
		<input type="checkbox" data-reci-share />
		<span><?php esc_html_e( 'Share this reflection', 'reci-media-hub' ); ?></span>
		<?php $reci_info( __( 'A moderator reads it before it appears. You can withdraw it at any time.', 'reci-media-hub' ) ); ?>
	</label>

	<label class="inline-flex items-center gap-2" data-reci-anon-wrap hidden>
		<input type="checkbox" data-reci-anonymous />
		<span><?php esc_html_e( 'Share anonymously', 'reci-media-hub' ); ?></span>
		<?php $reci_info( __( 'Your name is hidden from other readers. The RECI team can still see it, so that abuse can be acted on.', 'reci-media-hub' ) ); ?>
	</label>
</div>

<style>
/* Small enough to sit inside a label without breaking the row, legible on
   both the light card styles and the dark immersive ones. */
.reci-info {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 1.1rem;
	height: 1.1rem;
	border: 1px solid currentColor;
	border-radius: 999px;
	font-size: 0.7rem;
	font-style: italic;
	line-height: 1;
	opacity: 0.7;
	cursor: help;
	background: none;
	color: inherit;
	padding: 0;
}
.reci-info:hover,
.reci-info:focus-visible { opacity: 1; }

/* Defined here rather than borrowed from the theme: reflection pages do not
   load the stylesheet that carries screen-reader-text, so the text meant for
   assistive tech was simply printed on the page. */
.reci-sr-only {
	position: absolute;
	width: 1px;
	height: 1px;
	padding: 0;
	margin: -1px;
	overflow: hidden;
	clip: rect(0, 0, 0, 0);
	white-space: nowrap;
	border: 0;
}
</style>
