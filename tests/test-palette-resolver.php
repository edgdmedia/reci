<?php
/**
 * A style names five colours. The builder's panel edits eight. The templates
 * read fifteen.
 *
 * The seven nobody sets - cards, borders, the hotspot ring, and the two text
 * tiers - were declared in three stylesheets and undefined everywhere else, so
 * for voices-of-resistance, march-toward-justice and racial-disparities all 93
 * uses of var(--reflection-card) and friends resolved to nothing. The resolver
 * derives them from the background instead.
 */

require_once __DIR__ . '/../modules/reflection-system/inc/reflection-system-registry.php';

// ── Reading a colour's brightness ────────────────────────────────────────────

reci_assert( reci_reflection_color_is_dark( '#111111' ), 'Near-black reads as dark' );
reci_assert( reci_reflection_color_is_dark( '#000' ), 'Shorthand black reads as dark' );
reci_assert( ! reci_reflection_color_is_dark( '#f4f4f4' ), 'The light style background reads as light' );
reci_assert( ! reci_reflection_color_is_dark( '#ffffff' ), 'White reads as light' );
reci_assert( reci_reflection_color_is_dark( 'rgb(0,0,0)' ), 'An unreadable colour is assumed dark' );

// ── A dark style gets light tints ────────────────────────────────────────────

$dark = reci_reflection_resolve_palette( [
	'color_bg'      => '#111111',
	'color_heading' => '#e0e0e0',
	'color_body'    => '#a0a0a0',
	'color_accent'  => '#FFB81C',
] );

reci_assert_same( 'rgba(255,255,255,0.04)', $dark['color_card'], 'A dark background gets a white card tint' );
reci_assert_same( 'rgba(255,255,255,0.18)', $dark['color_border'], 'And a white border tint' );
reci_assert_same( 'rgba(255,184,28,0.35)', $dark['color_hotspot_ring'], 'The hotspot ring is the accent, held back' );

// ── A light style gets dark tints ────────────────────────────────────────────

$light = reci_reflection_resolve_palette( [
	'color_bg'      => '#f4f4f4',
	'color_heading' => '#111111',
	'color_body'    => '#3f3f46',
	'color_accent'  => '#2A4494',
] );

reci_assert_same( 'rgba(17,17,17,0.03)', $light['color_card'], 'A light background gets a dark card tint' );
reci_assert_same( 'rgba(17,17,17,0.14)', $light['color_border'], 'And a dark border tint' );
reci_assert_same( 'rgba(42,68,148,0.35)', $light['color_hotspot_ring'], 'The ring follows the accent either way' );

// This is the bug in one line: the two styles must not share a card colour.
reci_assert(
	$dark['color_card'] !== $light['color_card'],
	'A light style and a dark style do not get the same card colour'
);

// ── Text tiers follow the colours the style did name ─────────────────────────

reci_assert_same( '#111111', $light['color_text'], 'Text follows the heading colour' );
reci_assert_same( '#111111', $light['color_surface_text'], 'Text on a surface follows it too' );
reci_assert_same( '#3f3f46', $light['color_soft_text'], 'Soft text follows the body colour' );
reci_assert_same( '#3f3f46', $light['color_muted'], 'And so does muted text' );

// ── A surface with no colour of its own sits on the background ───────────────

reci_assert_same( '#f4f4f4', $light['color_surface'], 'Surface falls back to the background, not a dark default' );

$named = reci_reflection_resolve_palette( [ 'color_bg' => '#f4f4f4', 'color_surface' => '#ffffff' ] );
reci_assert_same( '#ffffff', $named['color_surface'], 'A named surface is kept' );

// ── What the author set always wins ──────────────────────────────────────────

$overridden = reci_reflection_resolve_palette( [
	'color_bg'     => '#111111',
	'color_card'   => '#ff0000',
	'color_border' => '#00ff00',
] );
reci_assert_same( '#ff0000', $overridden['color_card'], 'An explicit card colour is not derived over' );
reci_assert_same( '#00ff00', $overridden['color_border'], 'Nor an explicit border' );

// An empty string is not a choice, so it is filled.
$blank = reci_reflection_resolve_palette( [ 'color_bg' => '#111111', 'color_card' => '  ' ] );
reci_assert_same( 'rgba(255,255,255,0.04)', $blank['color_card'], 'A blank colour is treated as unset' );

// ── With no background, nothing is invented ──────────────────────────────────

$empty = reci_reflection_resolve_palette( [] );
reci_assert_same( [], $empty, 'With nothing to derive from, the stylesheets still decide' );

$accent_only = reci_reflection_resolve_palette( [ 'color_accent' => '#FFB81C' ] );
reci_assert(
	! array_key_exists( 'color_card', $accent_only ),
	'An accent alone does not conjure a card colour'
);

// ── The CSS it produces ──────────────────────────────────────────────────────

$stated = reci_reflection_palette_css( [ 'color_bg' => '#111111', 'color_accent' => '#FFB81C' ] );
reci_assert(
	str_contains( $stated, '--reflection-bg: #111111;' ),
	'A stated colour goes inline, where it beats the stylesheets'
);
reci_assert(
	! str_contains( $stated, '--reflection-card:' ),
	'A derived colour does not, so it cannot overrule a stylesheet'
);
reci_assert(
	str_contains( $stated, '--reflection-surface: #111111;' ),
	'Surface is the exception: it has to beat the dark default under a light palette'
);

$defaults = reci_reflection_palette_defaults_css( [ 'color_bg' => '#111111', 'color_accent' => '#FFB81C' ] );
reci_assert(
	str_contains( $defaults, '--reflection-card: rgba(255,255,255,0.04);' ),
	'A derived colour goes in the defaults, for a :where() rule to carry'
);
reci_assert(
	! str_contains( $defaults, '--reflection-bg:' ),
	'A stated colour is not repeated there'
);
reci_assert(
	! str_contains( $defaults, '--reflection-surface:' ),
	'Nor is surface, which was already written inline'
);

// Between them the two cover the palette once each, never twice.
foreach ( [ '--reflection-bg', '--reflection-card', '--reflection-border' ] as $property ) {
	reci_assert(
		str_contains( $stated, $property . ':' ) !== str_contains( $defaults, $property . ':' ),
		sprintf( '%s is written exactly once, in one place or the other', $property )
	);
}

reci_assert_same( '', reci_reflection_palette_css( [] ), 'An empty palette writes nothing' );
reci_assert_same( '', reci_reflection_palette_defaults_css( [] ), 'And derives nothing' );

// A style that already names everything needs no defaults at all.
$full = array_fill_keys( array_keys( reci_reflection_palette_keys() ), '#123456' );
reci_assert_same(
	'',
	reci_reflection_palette_defaults_css( $full ),
	'A style that names every colour gets no derived defaults'
);

// Every key the resolver knows maps to a property, so none is silently dropped.
$all = array_fill_keys( array_keys( reci_reflection_palette_keys() ), '#123456' );
$css = reci_reflection_palette_css( $all );
foreach ( reci_reflection_palette_keys() as $key => $property ) {
	reci_assert(
		str_contains( $css, $property . ': #123456;' ),
		sprintf( '%s is written out as %s', $key, $property )
	);
}
