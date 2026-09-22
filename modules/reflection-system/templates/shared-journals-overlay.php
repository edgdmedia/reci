<?php
/**
 * Shared journal entries overlay.
 *
 * Pooled per reflection: one list for the whole piece, not one per prompt.
 * Rendered collapsed and populated over REST on first open, so a reflection
 * with hundreds of entries does not pay for them on page load.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$overlay = wp_parse_args(
	$args ?? [],
	[
		'reflection_id' => 0,
		'count'         => 0,
	]
);

if ( $overlay['count'] < 1 ) {
	return;
}
?>
<div
	id="reci-shared-journals"
	class="fixed inset-0 z-50 hidden overflow-y-auto"
	style="background: var(--reflection-overlay);"
	data-reflection-id="<?php echo esc_attr( (string) $overlay['reflection_id'] ); ?>"
	role="dialog"
	aria-modal="true"
	aria-labelledby="reci-shared-journals-title"
>
	<div class="mx-auto w-full max-w-[900px] px-5 py-16 sm:px-6">
		<div class="flex items-start justify-between gap-6">
			<h2 id="reci-shared-journals-title" class="font-['Playfair_Display'] text-4xl font-semibold reci-reflection-text">
				<?php esc_html_e( 'Voices from this reflection', 'reci-media-hub' ); ?>
			</h2>
			<button
				type="button"
				id="reci-shared-journals-close"
				class="rounded-full border border-[color:var(--reflection-border)] px-5 py-2 font-['Oswald'] text-sm uppercase tracking-[0.1em] reci-reflection-text"
			>
				<?php esc_html_e( 'Close', 'reci-media-hub' ); ?>
			</button>
		</div>

		<p class="mt-3 text-base leading-8 reci-reflection-soft-text">
			<?php esc_html_e( 'Reflections other people chose to share. Some are anonymous.', 'reci-media-hub' ); ?>
		</p>

		<div id="reci-shared-journals-list" class="mt-8 grid gap-4"></div>

		<div id="reci-shared-journals-status" class="mt-6 text-sm reci-reflection-soft-text"></div>
	</div>
</div>
