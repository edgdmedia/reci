<?php
/**
 * Matching has to be forgiving enough to catch evasion but strict enough not
 * to defame ordinary words. The false-positive cases below matter as much as
 * the true positives: a flag sends someone's writing to a stranger to read.
 */

require_once __DIR__ . '/../inc/features/community-policy.php';

$terms = [ 'badword', 'go back home' ];

// Plain hits.
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'you are a badword', $terms ), 'match: plain term' );
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'BADWORD', $terms ), 'match: case insensitive' );
reci_assert_same( [ 'go back home' ], reci_match_flagged_terms( 'they said go back home', $terms ), 'match: multi-word phrase' );

// Evasion. These are the reason normalisation exists.
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'b4dw0rd', $terms ), 'match: leetspeak digits' );
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'b-a-d-w-o-r-d', $terms ), 'match: hyphen separated' );
// A symbol standing in for a letter cannot be recovered — we cannot know
// which letter '*' replaced — so it is stripped and the word no longer
// matches. Asserted explicitly so the limitation is visible, not assumed.
reci_assert_same( [], reci_match_flagged_terms( 'b*dword', $terms ), 'match: symbol substitution is a known miss' );
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'bádwörd', $terms ), 'match: accented characters' );

// False positives. A term inside a longer ordinary word must NOT match, or
// every innocent use of that substring gets a real person flagged.
reci_assert_same( [], reci_match_flagged_terms( 'badwordsmith', $terms ), 'match: no hit inside a longer word' );
reci_assert_same( [], reci_match_flagged_terms( 'a badwordy thing', $terms ), 'match: no hit on a longer word' );
reci_assert_same( [], reci_match_flagged_terms( 'nothing here', $terms ), 'match: clean text yields nothing' );
reci_assert_same( [], reci_match_flagged_terms( '', $terms ), 'match: empty text yields nothing' );
reci_assert_same( [], reci_match_flagged_terms( 'badword', [] ), 'match: empty term list yields nothing' );

// Punctuation adjacency is a word boundary, not part of the word.
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'stop, badword!', $terms ), 'match: punctuation is a boundary' );

// Each term is reported once regardless of how often it appears.
reci_assert_same( [ 'badword' ], reci_match_flagged_terms( 'badword badword badword', $terms ), 'match: reports each term once' );

// Both terms present, reported in the configured order.
reci_assert_same(
	[ 'badword', 'go back home' ],
	reci_match_flagged_terms( 'badword — go back home', $terms ),
	'match: multiple terms in list order'
);

// Normalisation is exposed because the JS warning must agree with PHP.
reci_assert_same( 'badword', reci_normalize_for_matching( 'B4d-W0rd' ), 'normalize: folds case, digits and separators' );
reci_assert_same( 'go back home', reci_normalize_for_matching( 'Go  Back   Home' ), 'normalize: collapses runs of spaces' );
