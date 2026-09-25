<?php
/**
 * The hero overlay is derived, but not at the cost of what the author set.
 *
 * racial-disparities is a light style - near-black heading on #f4f4f4 - and its
 * hero wants a white wash over the background photograph. The derivation used
 * to assign overlay_rgb and overlay_opacity unconditionally, so a chapter that
 * set them directly had both replaced with black before its template was
 * reached, and the title and buttons disappeared into the overlay.
 */

if ( ! function_exists( 'home_url' ) ) {
	function home_url( string $path = '' ): string {
		return 'https://example.test' . $path;
	}
}

require_once __DIR__ . '/../modules/reflection-system/inc/class-reflection-system-render-service.php';

/**
 * Reach the private derivation directly; it is where the rules live.
 *
 * @param array<string,mixed> $props
 * @return array<string,mixed>
 */
function reci_test_hero_props( array $props ): array {
	static $method = null;

	if ( null === $method ) {
		$method = new ReflectionMethod( 'RECI_Reflection_System_Render_Service', 'normalize_component_props' );
	}

	return $method->invoke( null, 'hero', $props );
}

// ── The builder's controls win when set ──────────────────────────────────────

$props = reci_test_hero_props( [ 'overlay_color' => '#ffffff', 'overlay_intensity' => 70 ] );
reci_assert_same( '255,255,255', $props['overlay_rgb'], 'Overlay colour is converted to rgb' );
reci_assert_same( 0.7, $props['overlay_opacity'], 'Overlay intensity becomes an opacity' );

$props = reci_test_hero_props( [ 'overlay_color' => '#fff' ] );
reci_assert_same( '255,255,255', $props['overlay_rgb'], 'A three-digit hex expands' );

$props = reci_test_hero_props( [ 'overlay_color' => 'not-a-colour' ] );
reci_assert_same( '0,0,0', $props['overlay_rgb'], 'An unreadable colour falls back to black' );

$props = reci_test_hero_props( [ 'overlay_intensity' => 400 ] );
reci_assert_same( 1.0, $props['overlay_opacity'], 'Intensity is clamped at fully opaque' );

$props = reci_test_hero_props( [ 'overlay_intensity' => -20 ] );
reci_assert_same( 0.0, $props['overlay_opacity'], 'And at fully transparent' );

// ── An overlay written straight into the blueprint survives ──────────────────

$props = reci_test_hero_props( [ 'overlay_rgb' => '255,255,255', 'overlay_opacity' => 0.7 ] );
reci_assert_same( '255,255,255', $props['overlay_rgb'], 'A hand-set overlay colour is not overwritten' );
reci_assert_same( 0.7, $props['overlay_opacity'], 'A hand-set overlay opacity is not overwritten' );

// The builder's control still takes precedence over it.
$props = reci_test_hero_props( [ 'overlay_rgb' => '255,255,255', 'overlay_color' => '#111111' ] );
reci_assert_same( '17,17,17', $props['overlay_rgb'], 'The colour control overrides a hand-set rgb' );

// ── With nothing set, the variant decides ────────────────────────────────────

$props = reci_test_hero_props( [ 'title' => 'The Data Gap' ] );
reci_assert(
	! array_key_exists( 'overlay_rgb', $props ),
	'With no overlay set, the key is left for the variant default to fill'
);
reci_assert(
	! array_key_exists( 'overlay_opacity', $props ),
	'And so is the opacity - a light variant keeps its light overlay'
);

// An empty string is not a choice.
$props = reci_test_hero_props( [ 'overlay_color' => '', 'overlay_intensity' => '' ] );
reci_assert( ! array_key_exists( 'overlay_rgb', $props ), 'An empty overlay colour is treated as unset' );
reci_assert( ! array_key_exists( 'overlay_opacity', $props ), 'An empty intensity is treated as unset' );

// ── The dead derivation is gone ──────────────────────────────────────────────

$props = reci_test_hero_props( [ 'background_type' => 'image' ] );
reci_assert( ! array_key_exists( 'bg_type', $props ), 'bg_type is no longer derived; no template read it' );

// ── Alignment still maps to classes ──────────────────────────────────────────

$props = reci_test_hero_props( [ 'align_horizontal' => 'right', 'align_vertical' => 'bottom' ] );
reci_assert_same( 'items-end', $props['align_h_class'], 'Right alignment maps to items-end' );
reci_assert_same( 'justify-end', $props['align_v_class'], 'Bottom alignment maps to justify-end' );
reci_assert_same( 'text-right', $props['align_text_class'], 'And the text follows it' );

$props = reci_test_hero_props( [] );
reci_assert_same( 'items-center', $props['align_h_class'], 'Alignment defaults to centred' );
reci_assert_same( 'reci-stage', $props['section_class'], 'The stage class is always present' );
