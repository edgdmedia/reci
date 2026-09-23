<?php
/**
 * Submission guidelines panel.
 *
 * The light-mode counterpart of the `guide-panel` in the collaborator
 * submission app: same structure and the same reading rhythm - a titled card,
 * gold section headings in the heading face, quiet body copy, and a flagged
 * "Questions?" note at the end - translated from the app's dark surface onto
 * the site's light one.
 *
 * The section headings come from the stored guideline body as `h3`s, so an
 * editor writes plain content in RECI Settings and the design is applied here
 * rather than pasted into the field.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reci_guidelines = trim( (string) reci_get_submission_guidelines() );

if ( '' === $reci_guidelines ) {
	// Nothing for the public, but anyone who can fix it is told where.
	if ( current_user_can( 'manage_options' ) ) {
		printf(
			'<div class="mb-6 rounded-2xl border border-dashed border-amber-300 bg-amber-50 px-5 py-4 text-sm text-amber-900">%s <a class="underline" href="%s">%s</a></div>',
			esc_html__( 'The submission guidelines have no content yet, so visitors see nothing here.', 'reci-media-hub' ),
			esc_url( admin_url( 'admin.php?page=reci-settings&tab=community' ) ),
			esc_html__( 'Add them under RECI Settings → Community.', 'reci-media-hub' )
		);
	}

	return;
}
?>
<div class="reci-guide-panel mb-8 max-w-4xl overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">
	<div class="flex items-center justify-between border-b border-zinc-200 px-6 py-6 sm:px-8">
		<h2 class="font-heading text-[22px] font-bold leading-none text-reci-ink">
			<?php esc_html_e( 'Submission Guidelines', 'reci-media-hub' ); ?>
		</h2>
	</div>

	<div class="reci-guide-panel__body px-6 py-6 sm:px-8">
		<?php echo wp_kses_post( $reci_guidelines ); ?>

		<div class="mt-8 border-l-[3px] border-reci-yellow bg-zinc-50 px-6 py-5">
			<div class="mb-2 font-sans text-[11px] font-bold uppercase tracking-[1.5px] text-amber-700">
				<?php esc_html_e( 'Questions?', 'reci-media-hub' ); ?>
			</div>
			<p class="text-[13px] leading-relaxed text-zinc-600">
				<?php esc_html_e( 'Contact the RECI editorial team for guidance on your submission. We are happy to help you determine the best format and sphere alignment for your content.', 'reci-media-hub' ); ?>
			</p>
		</div>
	</div>
</div>

<style>
/* Scoped to the panel: the body is editor-written HTML, so it is styled here
   rather than requiring classes in the settings field. */
.reci-guide-panel__body > h3 {
	font-family: 'Alternate Gothic ATF', 'Arial Narrow', Arial, sans-serif;
	font-size: 16px;
	letter-spacing: 0.02em;
	color: #B45309;
	margin: 24px 0 8px;
}
.reci-guide-panel__body > h3:first-child {
	margin-top: 0;
}
.reci-guide-panel__body > p {
	font-size: 13.5px;
	line-height: 1.7;
	color: #52525B;
	margin: 0 0 4px;
}
</style>
