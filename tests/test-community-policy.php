<?php
/**
 * The term list is typed by a human into a textarea, so it arrives messy:
 * blank lines, stray whitespace, mixed case, accidental duplicates, and
 * comment lines. Parsing is pure and therefore testable.
 */

require_once __DIR__ . '/../inc/features/community-policy.php';

$raw = "Slur One\n\n  slur two  \nSLUR ONE\n# a comment line\nslur three\n";

$terms = reci_parse_abuse_terms( $raw );

reci_assert_same( [ 'slur one', 'slur two', 'slur three' ], $terms, 'parse: cleans, lowercases, dedupes, drops comments and blanks' );
reci_assert_same( [], reci_parse_abuse_terms( '' ), 'parse: empty input yields empty list' );
reci_assert_same( [], reci_parse_abuse_terms( "\n\n   \n" ), 'parse: whitespace-only input yields empty list' );
reci_assert_same( [], reci_parse_abuse_terms( "# only a comment\n" ), 'parse: comment-only input yields empty list' );

// Carriage returns: Windows browsers post \r\n and would otherwise leave a
// trailing \r glued to every term, so no term would ever match.
reci_assert_same( [ 'alpha', 'beta' ], reci_parse_abuse_terms( "alpha\r\nbeta\r\n" ), 'parse: strips carriage returns' );

// A multi-word phrase is a legitimate entry and must survive intact.
reci_assert_same( [ 'go back home' ], reci_parse_abuse_terms( 'Go Back Home' ), 'parse: keeps multi-word phrases' );

// Getters read the stored option through the parser.
reci_test_stub_options( [ 'reci_theme_settings' => [ 'abuse_terms' => "One\nTwo" ] ] );
reci_assert_same( [ 'one', 'two' ], reci_get_abuse_terms(), 'getter: reads and parses the stored option' );

reci_test_stub_options( [] );
reci_assert_same( [], reci_get_abuse_terms(), 'getter: missing option yields empty list' );
reci_assert_same( '', reci_get_community_policy(), 'getter: missing policy yields empty string' );
reci_assert_same( '', reci_get_submission_guidelines(), 'getter: missing guidelines yields empty string' );

// --- Starter community settings -----------------------------------------
//
// These seed a site that has never had the fields filled in. An empty one
// would leave the guidelines invisible and the term list inert, which is
// exactly the state that made the feature look missing.
$defaults = reci_default_community_settings();

foreach ( [ 'submission_guidelines', 'community_policy', 'abuse_terms' ] as $key ) {
	reci_assert_same( true, isset( $defaults[ $key ] ), "defaults: {$key} is present" );
	reci_assert_same( true, '' !== trim( (string) ( $defaults[ $key ] ?? '' ) ), "defaults: {$key} is not empty" );
}

$default_terms = reci_parse_abuse_terms( $defaults['abuse_terms'] );
reci_assert_same( true, count( $default_terms ) > 0, 'defaults: the starter term list parses to at least one term' );

// Every starter term must actually match itself, or it is dead weight in the
// list and a moderator would never see it fire.
foreach ( $default_terms as $term ) {
	reci_assert_same(
		[ $term ],
		reci_match_flagged_terms( 'before ' . $term . ' after', $default_terms ),
		'defaults: starter term "' . $term . '" matches itself and nothing else'
	);
}

// Ordinary testimony must not trip the starter list.
reci_assert_same(
	[],
	reci_match_flagged_terms( 'I reflected on my own experience of bias at work today.', $default_terms ),
	'defaults: ordinary reflection is not flagged'
);
