<?php
/**
 * Lead-in for a reflection prompt chapter.
 *
 * Eyebrow, title, intro and cards — the content above the writing box. Two
 * styles rendered all four and three rendered almost none, which is why the
 * same chapter read completely differently depending on the style an author
 * picked.
 *
 * Every element is omitted when empty, so a stark style stays stark until
 * someone gives it something to show, and no style is crippled.
 *
 * Each style passes its own classes; the structure is the same everywhere.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reci_intro = wp_parse_args(
	$args ?? [],
	[
		'eyebrow'          => '',
		'title'            => '',
		'intro'            => '',
		'cards'            => [],
		'eyebrow_class'    => '',
		'title_class'      => '',
		'intro_class'      => '',
		'cards_class'      => 'mt-6 grid gap-4 md:grid-cols-2',
		'card_class'       => '',
		'card_title_class' => '',
		'card_body_class'  => '',
	]
);

$reci_cards = array_filter(
	(array) $reci_intro['cards'],
	static function ( $card ): bool {
		return is_array( $card ) && ( '' !== trim( (string) ( $card['title'] ?? '' ) ) || '' !== trim( (string) ( $card['body'] ?? '' ) ) );
	}
);

if ( '' === trim( (string) $reci_intro['eyebrow'] )
	&& '' === trim( (string) $reci_intro['title'] )
	&& '' === trim( (string) $reci_intro['intro'] )
	&& [] === $reci_cards ) {
	return;
}
?>
<?php if ( '' !== trim( (string) $reci_intro['eyebrow'] ) ) : ?>
	<div class="<?php echo esc_attr( (string) $reci_intro['eyebrow_class'] ); ?>"><?php echo esc_html( (string) $reci_intro['eyebrow'] ); ?></div>
<?php endif; ?>

<?php if ( '' !== trim( (string) $reci_intro['title'] ) ) : ?>
	<h2 class="<?php echo esc_attr( (string) $reci_intro['title_class'] ); ?>"><?php echo esc_html( (string) $reci_intro['title'] ); ?></h2>
<?php endif; ?>

<?php if ( '' !== trim( (string) $reci_intro['intro'] ) ) : ?>
	<p class="<?php echo esc_attr( (string) $reci_intro['intro_class'] ); ?>"><?php echo reci_reflection_format_text( (string) $reci_intro['intro'] ); ?></p>
<?php endif; ?>

<?php if ( [] !== $reci_cards ) : ?>
	<div class="<?php echo esc_attr( (string) $reci_intro['cards_class'] ); ?>">
		<?php foreach ( $reci_cards as $reci_card ) : ?>
			<article class="<?php echo esc_attr( (string) $reci_intro['card_class'] ); ?>">
				<?php if ( '' !== trim( (string) ( $reci_card['title'] ?? '' ) ) ) : ?>
					<h3 class="<?php echo esc_attr( (string) $reci_intro['card_title_class'] ); ?>"><?php echo esc_html( (string) $reci_card['title'] ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) ( $reci_card['body'] ?? '' ) ) ) : ?>
					<p class="<?php echo esc_attr( (string) $reci_intro['card_body_class'] ); ?>"><?php echo reci_reflection_format_text( (string) $reci_card['body'] ); ?></p>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
