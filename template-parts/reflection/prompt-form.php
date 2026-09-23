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
		'continue_label'   => '',
		'continue_href'    => '',
		'continue_target'  => '',
		'continue_class'   => '',
		// The confirmation belongs to the contract, not to individual styles:
		// three of them used to show a panel and two showed nothing, so the
		// same action told you different things depending where you stood.
		// Visibility is the hidden attribute's job. A Tailwind `hidden` class
		// here would set display:none from CSS, which clearing the attribute
		// cannot undo - the form would vanish and nothing would replace it.
		'success_class'    => 'mt-6 flex-col items-start gap-2',
		'success_title'    => __( 'Reflection Saved', 'reci-media-hub' ),
		'success_body'     => __( 'Your thoughts have been securely recorded in your private journal.', 'reci-media-hub' ),
		'success_title_class' => '',
		'success_body_class'  => '',
		'success_row_class'   => 'mt-6 flex flex-col sm:flex-row gap-4',
		'restart_label'    => __( 'Start Over', 'reci-media-hub' ),
		'restart_class'    => '',
		'gallery_label'    => __( 'Return to Gallery', 'reci-media-hub' ),
	]
);
?>
<div
	class="reci-prompt"
	data-reci-prompt
	data-reflection-id="<?php echo esc_attr( (string) get_the_ID() ); ?>"
	data-prompt="<?php echo esc_attr( (string) $reci_form['prompt'] ); ?>"
>
	<div data-reci-form-body>
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

	<div data-reci-success class="<?php echo esc_attr( (string) $reci_form['success_class'] ); ?>" hidden>
		<svg class="m-auto mb-4 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
		<h3 class="<?php echo esc_attr( (string) $reci_form['success_title_class'] ); ?>"><?php echo esc_html( (string) $reci_form['success_title'] ); ?></h3>
		<p class="<?php echo esc_attr( (string) $reci_form['success_body_class'] ); ?>"><?php echo esc_html( (string) $reci_form['success_body'] ); ?></p>
		<div class="<?php echo esc_attr( (string) $reci_form['success_row_class'] ); ?>">
			<a href="<?php echo esc_url( home_url( '/reflections/' ) ); ?>" class="<?php echo esc_attr( (string) $reci_form['button_class'] ); ?> no-underline"><?php echo esc_html( (string) $reci_form['gallery_label'] ); ?></a>
			<button type="button" data-reci-restart class="<?php echo esc_attr( (string) ( $reci_form['restart_class'] ?: $reci_form['button_class'] ) ); ?>"><?php echo esc_html( (string) $reci_form['restart_label'] ); ?></button>
		</div>
	</div>
</div>
