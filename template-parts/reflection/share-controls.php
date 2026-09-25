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
	// Native popover: opens on click, closes on click-away or Escape, with no
	// script of our own. The title attribute stays for hover, and the text is
	// in the popover itself so assistive tech reads it either way.
	$id = 'reci-info-' . wp_unique_id();

	printf(
		'<button type="button" class="reci-info" popovertarget="%1$s" aria-label="%2$s" title="%3$s"><span aria-hidden="true">i</span></button>'
		. '<span id="%1$s" popover class="reci-info-bubble">%3$s</span>',
		esc_attr( $id ),
		esc_attr__( 'More information', 'reci-media-hub' ),
		esc_html( $text )
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
.reci-info-bubble {
	max-width: 22rem;
	margin: 0;
	padding: 0.75rem 0.9rem;
	border: 1px solid rgba(255, 255, 255, 0.18);
	border-radius: 12px;
	background: #1b1b1b;
	color: #f4f4f4;
	font-size: 0.8rem;
	line-height: 1.5;
	box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45);
}
.reci-info-bubble:not(:popover-open) { display: none; }

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
