<?php
/**
 * Reflection prompt form.
 *
 * The single contract every prompt style shares: one textarea, one save
 * button, the share toggles, the shared-reflections link and one status line.
 * Each style keeps its own look by passing its own classes; none of them owns
 * the markup or the behaviour any more.
 *
 * Everything is addressed by data attribute rather than id, because a
 * reflection may hold several prompt chapters and duplicate ids left every
 * copy after the first inert.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reci_form = wp_parse_args(
	$args ?? [],
	[
		'style'            => 'panel',
		'prompt'           => '',
		'textarea_class'   => '',
		'textarea_rows'    => '',
		'button_class'     => '',
		'row_class'        => 'mt-4 flex flex-wrap items-center gap-4',
		'status_class'     => 'mt-4 text-sm',
		'note_class'       => 'mt-4 text-sm',
		'save_label'       => __( 'Save reflection', 'reci-media-hub' ),
		'placeholder'      => __( 'Write your response here...', 'reci-media-hub' ),
		// 'inline' leaves the reader where they are; 'success' swaps the form
		// for the style's own success panel.
		'after_save'       => 'inline',
		'continue_label'   => '',
		'continue_href'    => '',
		'continue_target'  => '',
		'continue_class'   => '',
	]
);
?>
<div
	class="reci-prompt"
	data-reci-prompt
	data-reflection-id="<?php echo esc_attr( (string) get_the_ID() ); ?>"
	data-prompt="<?php echo esc_attr( (string) $reci_form['prompt'] ); ?>"
	data-after-save="<?php echo esc_attr( (string) $reci_form['after_save'] ); ?>"
>
	<textarea
		data-reci-response
		class="<?php echo esc_attr( (string) $reci_form['textarea_class'] ); ?>"
		<?php if ( '' !== (string) $reci_form['textarea_rows'] ) : ?>rows="<?php echo esc_attr( (string) $reci_form['textarea_rows'] ); ?>"<?php endif; ?>
		placeholder="<?php echo esc_attr( (string) $reci_form['placeholder'] ); ?>"
	></textarea>

	<?php if ( ! is_user_logged_in() ) : ?>
		<p class="<?php echo esc_attr( (string) $reci_form['note_class'] ); ?>">
			<?php esc_html_e( 'Log in or create a free account to record your reflections in your private journal.', 'reci-media-hub' ); ?>
		</p>
	<?php endif; ?>

	<?php
	get_template_part(
		'template-parts/reflection/share-controls',
		null,
		[
			'style' => $reci_form['style'],
			'show'  => 'toggles',
		]
	);
	?>

	<div class="<?php echo esc_attr( (string) $reci_form['row_class'] ); ?>">
		<button
			type="button"
			data-reci-save
			class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?>"
		><?php echo esc_html( (string) $reci_form['save_label'] ); ?></button>

		<?php
		get_template_part(
			'template-parts/reflection/share-controls',
			null,
			[
				'style'        => $reci_form['style'],
				'show'         => 'read',
				'button_class' => $reci_form['button_class'],
			]
		);
		?>

		<?php if ( '' !== (string) $reci_form['continue_label'] ) : ?>
			<button
				type="button"
				class="<?php echo esc_attr( (string) ( $reci_form['continue_class'] ?: $reci_form['button_class'] ) ); ?>"
				<?php if ( '' !== (string) $reci_form['continue_target'] ) : ?>
					data-stage-target="<?php echo esc_attr( (string) $reci_form['continue_target'] ); ?>"
				<?php endif; ?>
				<?php if ( '' !== (string) $reci_form['continue_href'] ) : ?>
					data-reci-continue-href="<?php echo esc_attr( esc_url( (string) $reci_form['continue_href'] ) ); ?>"
				<?php endif; ?>
			><?php echo esc_html( (string) $reci_form['continue_label'] ); ?></button>
		<?php endif; ?>
	</div>

	<p data-reci-status class="<?php echo esc_attr( (string) $reci_form['status_class'] ); ?>" hidden></p>
</div>
