<?php
/**
 * Community policy: the guideline text and the abuse term list.
 *
 * The term list never blocks a save. It warns the writer and, on the surfaces
 * that are published to other people, flags the entry for a human to read.
 * This site's subject matter means people will legitimately quote the language
 * used against them, and that testimony must survive.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Parse the raw textarea value into a clean term list.
 *
 * Pure. One term per line; blank lines and lines starting with `#` are
 * dropped; everything is lowercased and deduplicated so matching can be a
 * straight comparison later.
 *
 * @return array<int,string>
 */
function reci_parse_abuse_terms( string $raw ): array {
	$raw   = str_replace( "\r\n", "\n", $raw );
	$raw   = str_replace( "\r", "\n", $raw );
	$lines = explode( "\n", $raw );

	$terms = [];

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line || str_starts_with( $line, '#' ) ) {
			continue;
		}

		$terms[] = mb_strtolower( $line, 'UTF-8' );
	}

	return array_values( array_unique( $terms ) );
}

/**
 * Read one key out of the theme settings array.
 */
function reci_policy_setting( string $key ): string {
	$settings = get_option( 'reci_theme_settings', [] );

	if ( ! is_array( $settings ) || ! isset( $settings[ $key ] ) ) {
		return '';
	}

	return (string) $settings[ $key ];
}

/**
 * The configured abuse term list.
 *
 * @return array<int,string>
 */
function reci_get_abuse_terms(): array {
	return reci_parse_abuse_terms( reci_policy_setting( 'abuse_terms' ) );
}

/**
 * The community/abuse guideline body.
 */
function reci_get_community_policy(): string {
	return reci_policy_setting( 'community_policy' );
}

/**
 * The submission guideline body.
 */
function reci_get_submission_guidelines(): string {
	return reci_policy_setting( 'submission_guidelines' );
}

/**
 * Mirror the term list into WordPress's own moderation keyword option.
 *
 * `moderation_keys` holds a comment for review. `disallowed_keys` would reject
 * it outright, which this feature must never do, so that option is left alone.
 *
 * @param array<int,string> $terms
 */
function reci_sync_moderation_keys( array $terms ): void {
	update_option( 'moderation_keys', implode( "\n", $terms ) );
}

/**
 * Accent folding map.
 *
 * Latin-1 and the common Latin Extended-A characters, which is everything this
 * site's languages need. Kept as data so the browser-side copy in
 * assets/js/community-policy.js can mirror it exactly.
 *
 * @return array<string,string>
 */
function reci_accent_fold_map(): array {
	return [
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ā' => 'a', 'ă' => 'a', 'ą' => 'a',
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ę' => 'e', 'ě' => 'e',
		'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ĩ' => 'i', 'ī' => 'i', 'į' => 'i',
		'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o',
		'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u',
		'ý' => 'y', 'ÿ' => 'y',
		'ñ' => 'n', 'ń' => 'n', 'ň' => 'n',
		'ç' => 'c', 'ć' => 'c', 'č' => 'c',
		'ś' => 's', 'š' => 's', 'ş' => 's',
		'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
		'ł' => 'l', 'ĺ' => 'l', 'ľ' => 'l',
		'ř' => 'r', 'ŕ' => 'r',
		'ť' => 't', 'ţ' => 't',
		'ď' => 'd', 'đ' => 'd',
		'ğ' => 'g', 'ģ' => 'g',
		'æ' => 'ae', 'œ' => 'oe', 'ß' => 'ss',
	];
}

/**
 * Fold text into the form matching happens in.
 *
 * Lowercases, strips accents, undoes the common digit and symbol
 * substitutions, removes characters used to break a word up, and collapses
 * whitespace. Exposed rather than private because `assets/js/community-policy.js`
 * has to normalise identically — if the two drift, the warning a writer sees
 * stops predicting what the server flags.
 */
function reci_normalize_for_matching( string $text ): string {
	$text = mb_strtolower( $text, 'UTF-8' );

	// Accents: é → e, ö → o. An explicit map rather than
	// iconv( 'ASCII//TRANSLIT' ), whose output differs between glibc and the
	// BSD iconv on macOS — the same input must normalise identically on a
	// developer's Mac and on the Linux host.
	$text = strtr( $text, reci_accent_fold_map() );

	// Digits commonly substituted for letters to slip a term past a plain
	// string match. Deliberately digits only: mapping '!' or '@' to a letter
	// would corrupt ordinary punctuation, so that "badword!" stopped matching
	// 'badword' because it had become "badwordi".
	$text = strtr(
		$text,
		[
			'4' => 'a',
			'3' => 'e',
			'1' => 'i',
			'0' => 'o',
			'5' => 's',
			'7' => 't',
		]
	);

	// Characters inserted between letters to break the word up. Spaces are
	// kept, because multi-word phrases are legitimate terms.
	$text = preg_replace( '/[^a-z0-9\s]+/', '', $text );

	// Collapse runs of whitespace so "go  back   home" still matches.
	$text = preg_replace( '/\s+/', ' ', (string) $text );

	return trim( (string) $text );
}

/**
 * Which of the given terms appear in the text?
 *
 * Pure. Word-boundary matched, so a term inside a longer ordinary word does
 * not count — flagging sends a person's writing to a stranger, and a false
 * positive has a real cost.
 *
 * @param array<int,string> $terms Already lowercased, from reci_parse_abuse_terms().
 *
 * @return array<int,string> The matched terms, in the order they were configured.
 */
function reci_match_flagged_terms( string $text, array $terms ): array {
	if ( '' === trim( $text ) || [] === $terms ) {
		return [];
	}

	$haystack = reci_normalize_for_matching( $text );

	if ( '' === $haystack ) {
		return [];
	}

	$matched = [];

	foreach ( $terms as $term ) {
		$needle = reci_normalize_for_matching( $term );

		if ( '' === $needle ) {
			continue;
		}

		$pattern = '/(?<![a-z0-9])' . preg_quote( $needle, '/' ) . '(?![a-z0-9])/u';

		if ( 1 === preg_match( $pattern, $haystack ) ) {
			$matched[] = $term;
		}
	}

	return $matched;
}

/**
 * Does this text hit the configured term list?
 */
function reci_text_has_flagged_terms( string $text ): bool {
	return [] !== reci_match_flagged_terms( $text, reci_get_abuse_terms() );
}
