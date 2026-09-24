<?php
/**
 * Reflection prompt form.
 *
 * The one contract every prompt style shares. Structure and behaviour live
 * here; a style contributes colour and nothing else.
 *
 * Always, in this order, in one row: Save reflection, Shared Reflections when
 * there are any, Back to Gallery. The last one used to come from the chapter's
 * own `button_label`, which is how a demo reflection ended up offering "Submit
 * Reflection" next to a save button that already submitted.
 *
 * Addressed by data attribute rather than id, because a reflection may hold
 * several prompt chapters.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reci_form = wp_parse_args(
	$args ?? [],
	[
		'style'         => 'panel',
		'prompt'        => '',
		// Colour only. Size, padding, radius and type come from the base
		// classes below so the writing box is the same box everywhere.
		'textarea_tone' => 'border-[color:var(--reflection-border)] bg-[var(--reflection-card)] reci-reflection-text',
		'button_class'  => '',
		'tone_class'    => 'reci-reflection-soft-text',
		'save_label'    => __( 'Save reflection', 'reci-media-hub' ),
		'placeholder'   => __( 'Write your response here...', 'reci-media-hub' ),
		// Centred styles need their controls centred and their writing box
		// reined in; left-aligned panel styles do not.
		'align'         => 'left',
		// A prompt is not always the last chapter. When the author points this
		// one at a following chapter, the third action carries the reader on
		// instead of dropping them back to the gallery.
		'continue_label'  => '',
		'continue_target' => '',
		'form_class'    => '',
		'success_title_class' => '',
		'success_body_class'  => '',
	]
);

$reci_textarea_base = 'block w-full rounded-[18px] border p-4 text-base leading-7 outline-none';
$reci_is_center     = 'center' === $reci_form['align'];
$reci_row_base      = 'mt-5 flex flex-wrap items-center gap-3' . ( $reci_is_center ? ' justify-center' : '' );

$reci_shared_count = function_exists( 'reci_get_shared_journal_count' )
	? reci_get_shared_journal_count( (int) get_the_ID() )
	: 0;
?>
<div
	class="reci-prompt mt-6 w-full <?php echo esc_attr( (string) $reci_form['form_class'] ); ?>"
	data-reci-prompt
	data-reflection-id="<?php echo esc_attr( (string) get_the_ID() ); ?>"
	data-prompt="<?php echo esc_attr( (string) $reci_form['prompt'] ); ?>"
>
	<div data-reci-form-body>
		<textarea
			data-reci-response
			rows="6"
			class="<?php echo esc_attr( $reci_textarea_base . ' ' . (string) $reci_form['textarea_tone'] ); ?>"
			placeholder="<?php echo esc_attr( (string) $reci_form['placeholder'] ); ?>"
		></textarea>

		<?php if ( ! is_user_logged_in() ) : ?>
			<p class="mt-3 text-sm <?php echo esc_attr( (string) $reci_form['tone_class'] ); ?>">
				<?php esc_html_e( 'Log in or create a free account to record your reflections in your private journal.', 'reci-media-hub' ); ?>
			</p>
		<?php endif; ?>

		<?php
		get_template_part(
			'template-parts/reflection/share-controls',
			null,
			[
				'tone_class' => $reci_form['tone_class'],
			'align'      => $reci_form['align'],
			]
		);
		?>

		<div class="<?php echo esc_attr( $reci_row_base ); ?>">
			<button
				type="button"
				data-reci-save
				class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?>"
			><?php echo esc_html( (string) $reci_form['save_label'] ); ?></button>

			<?php if ( $reci_shared_count > 0 ) : ?>
				<button
					type="button"
					data-stage-target="reci-shared-journals"
					class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?>"
				><?php esc_html_e( 'Shared Reflections', 'reci-media-hub' ); ?></button>
			<?php endif; ?>

			<?php
			$reci_next = (string) $reci_form['continue_target'];
			$reci_next = ( '' !== $reci_next && '#' !== $reci_next ) ? $reci_next : '';
			?>
			<?php if ( '' !== $reci_next ) : ?>
				<button
					type="button"
					data-stage-target="<?php echo esc_attr( $reci_next ); ?>"
					class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?>"
				><?php echo esc_html( (string) $reci_form['continue_label'] ?: __( 'Continue', 'reci-media-hub' ) ); ?></button>
			<?php else : ?>
				<a
					href="<?php echo esc_url( home_url( '/reflections/' ) ); ?>"
					class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?> no-underline"
				><?php esc_html_e( 'Back to Gallery', 'reci-media-hub' ); ?></a>
			<?php endif; ?>
		</div>

		<p data-reci-status class="mt-4 text-sm <?php echo esc_attr( (string) $reci_form['tone_class'] ); ?>" hidden></p>
	</div>

	<div data-reci-success class="flex-col <?php echo $reci_is_center ? 'items-center text-center' : 'items-start'; ?>" hidden>
		<svg class="m-auto mb-4 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
		<h3 class="<?php echo esc_attr( (string) $reci_form['success_title_class'] ); ?>"><?php esc_html_e( 'Reflection Saved', 'reci-media-hub' ); ?></h3>
		<p class="<?php echo esc_attr( (string) $reci_form['success_body_class'] ); ?>"><?php esc_html_e( 'Your thoughts have been securely recorded in your private journal.', 'reci-media-hub' ); ?></p>

		<div class="<?php echo esc_attr( $reci_row_base ); ?>">
			<a href="<?php echo esc_url( home_url( '/reflections/' ) ); ?>" class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?> no-underline"><?php esc_html_e( 'Back to Gallery', 'reci-media-hub' ); ?></a>
			<button type="button" data-reci-restart class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?>"><?php esc_html_e( 'Start Over', 'reci-media-hub' ); ?></button>
		</div>
	</div>
</div>
