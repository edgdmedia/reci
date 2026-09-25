<?php
/**
 * The builder composes a chapter's props from the fields the registry declares
 * for its family. A prop that is rendered but never declared is absent from the
 * posted JSON, so without a guard the first save would drop it - silently.
 *
 * That is how We Humans lost its reflection intro and its six cards when its
 * style was swapped and swapped back.
 */

// Stub the registry lookup before loading the registry, so these tests do not
// need a real WordPress. The guarded definitions in the registry defer to it.
if ( ! function_exists( 'reci_reflection_system_component_definition' ) ) {
	function reci_reflection_system_component_definition( string $family ): ?array {
		$families = [
			// Mirrors the shape of a real family: some props are fields, some
			// are rendered only.
			'reflection-prompt' => [
				'fields' => [
					'id'     => [ 'type' => 'text' ],
					'title'  => [ 'type' => 'text' ],
					'prompt' => [ 'type' => 'textarea' ],
				],
			],
			'hero' => [
				'fields' => [
					'id'    => [ 'type' => 'text' ],
					'title' => [ 'type' => 'text' ],
				],
			],
		];

		return $families[ $family ] ?? null;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( int $post_id, string $key, bool $single = false ) {
		return $GLOBALS['reci_test_post_meta'][ $post_id ][ $key ] ?? '';
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( string $path = '' ): string {
		return 'https://example.test' . $path;
	}
}

require_once __DIR__ . '/../modules/reflection-system/inc/reflection-system-registry.php';

/**
 * Build a blueprint around a single chapter.
 */
function reci_test_blueprint( array $chapters ): array {
	return [
		'version'  => 2,
		'system'   => 'reflections',
		'chapters' => $chapters,
		'settings' => [],
	];
}

// ── An undeclared prop survives a save that omits it ──────────────────────────

$stored = reci_test_blueprint( [
	[
		'id'      => 'wh-reflect',
		'family'  => 'reflection-prompt',
		'variant' => 'journal',
		'props'   => [
			'id'     => 'wh-reflect',
			'title'  => 'Reflection',
			'prompt' => 'How are you called to address racism?',
			'intro'  => 'The following questions are offered as a starting point.',
			'cards'  => [
				[ 'title' => 'Familiarity' ],
				[ 'title' => 'Surprise' ],
				[ 'title' => 'Today' ],
				[ 'title' => 'Forgetting' ],
				[ 'title' => 'The Future' ],
				[ 'title' => 'Action' ],
			],
		],
	],
] );

// What a builder that only knows the declared fields would post back.
$incoming = reci_test_blueprint( [
	[
		'id'      => 'wh-reflect',
		'family'  => 'reflection-prompt',
		'variant' => 'minimal',
		'props'   => [
			'id'     => 'wh-reflect',
			'title'  => 'Reflection',
			'prompt' => 'How are you called to address racism?',
		],
	],
] );

$merged = reci_reflection_system_preserve_unknown_props( $incoming, $stored );
$props  = $merged['chapters'][0]['props'];

reci_assert_same(
	'The following questions are offered as a starting point.',
	$props['intro'] ?? null,
	'An undeclared intro survives a save that omits it'
);
reci_assert_same( 6, count( $props['cards'] ?? [] ), 'All six undeclared cards survive' );
reci_assert_same( 'minimal', $merged['chapters'][0]['variant'], 'The swapped variant is still applied' );

// ── A declared field stays authoritative ──────────────────────────────────────

$cleared = reci_test_blueprint( [
	[
		'id'      => 'wh-reflect',
		'family'  => 'reflection-prompt',
		'variant' => 'journal',
		'props'   => [ 'id' => 'wh-reflect', 'title' => '', 'prompt' => 'Still here' ],
	],
] );

$merged = reci_reflection_system_preserve_unknown_props( $cleared, $stored );
reci_assert_same( '', $merged['chapters'][0]['props']['title'], 'Clearing a declared field really clears it' );

// A declared field the builder omits entirely is also not resurrected, because
// the guard only ever restores props outside the field set.
$dropped = reci_test_blueprint( [
	[
		'id'      => 'wh-reflect',
		'family'  => 'reflection-prompt',
		'variant' => 'journal',
		'props'   => [ 'id' => 'wh-reflect', 'prompt' => 'Still here' ],
	],
] );

$merged = reci_reflection_system_preserve_unknown_props( $dropped, $stored );
reci_assert(
	! array_key_exists( 'title', $merged['chapters'][0]['props'] ),
	'A declared field is never restored from what was stored'
);

// ── Colour overrides survive, since no family declares them ───────────────────

$with_colors = reci_test_blueprint( [
	[
		'id'     => 'march-hero',
		'family' => 'hero',
		'props'  => [
			'id'              => 'march-hero',
			'title'           => 'March',
			'override_colors' => '1',
			'color_bg'        => '#8a0e0e',
			'color_accent'    => '#FFB81C',
		],
	],
] );

$posted = reci_test_blueprint( [
	[
		'id'     => 'march-hero',
		'family' => 'hero',
		'props'  => [ 'id' => 'march-hero', 'title' => 'March' ],
	],
] );

$merged = reci_reflection_system_preserve_unknown_props( $posted, $with_colors );
reci_assert_same( '#8a0e0e', $merged['chapters'][0]['props']['color_bg'] ?? null, 'A chapter colour override survives a save' );
reci_assert_same( '1', $merged['chapters'][0]['props']['override_colors'] ?? null, 'The override flag survives with it' );

// ── A replaced chapter does not inherit the old one's props ───────────────────

$replaced = reci_test_blueprint( [
	[
		'id'     => 'wh-reflect',
		'family' => 'hero',
		'props'  => [ 'id' => 'wh-reflect', 'title' => 'Now a hero' ],
	],
] );

$merged = reci_reflection_system_preserve_unknown_props( $replaced, $stored );
reci_assert(
	! array_key_exists( 'cards', $merged['chapters'][0]['props'] ),
	'Swapping a chapter to another family does not resurrect the old props'
);

// ── Reordering keeps each chapter's own props ─────────────────────────────────

$two_stored = reci_test_blueprint( [
	[ 'id' => 'a', 'family' => 'hero', 'props' => [ 'id' => 'a', 'title' => 'A', 'overlay_rgb' => '0,0,0' ] ],
	[ 'id' => 'b', 'family' => 'hero', 'props' => [ 'id' => 'b', 'title' => 'B', 'overlay_rgb' => '255,255,255' ] ],
] );

$reordered = reci_test_blueprint( [
	[ 'id' => 'b', 'family' => 'hero', 'props' => [ 'id' => 'b', 'title' => 'B' ] ],
	[ 'id' => 'a', 'family' => 'hero', 'props' => [ 'id' => 'a', 'title' => 'A' ] ],
] );

$merged = reci_reflection_system_preserve_unknown_props( $reordered, $two_stored );
reci_assert_same( '255,255,255', $merged['chapters'][0]['props']['overlay_rgb'], 'A reordered chapter keeps its own undeclared props' );
reci_assert_same( '0,0,0', $merged['chapters'][1]['props']['overlay_rgb'], 'And so does the one it swapped places with' );

// ── A brand new chapter is left alone ─────────────────────────────────────────

$added = reci_test_blueprint( [
	[ 'id' => 'fresh', 'family' => 'hero', 'props' => [ 'id' => 'fresh', 'title' => 'Fresh' ] ],
] );

$merged = reci_reflection_system_preserve_unknown_props( $added, $stored );
reci_assert_same( [ 'id' => 'fresh', 'title' => 'Fresh' ], $merged['chapters'][0]['props'], 'A new chapter gains nothing from the old blueprint' );

// ── The full save path reads what the post holds ──────────────────────────────

$GLOBALS['reci_test_post_meta'] = [
	42 => [ '_reci_reflection_blueprint' => wp_json_encode( $stored ) ],
];

$saved = reci_reflection_system_merge_saved_blueprint( $incoming, 42 );
reci_assert_same( 6, count( $saved['chapters'][0]['props']['cards'] ?? [] ), 'The save path restores undeclared props from stored meta' );

$saved = reci_reflection_system_merge_saved_blueprint( $incoming, 999 );
reci_assert(
	! array_key_exists( 'cards', $saved['chapters'][0]['props'] ),
	'A post with no stored blueprint saves exactly what was posted'
);
