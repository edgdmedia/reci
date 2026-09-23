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

/**
 * Should a match on this surface be routed to a moderator?
 *
 * A private journal entry is the writer's own space. Detection warns them and
 * stops there: putting private writing in front of staff because it contained
 * a word from a list would break the promise the Private/Shared toggle makes.
 *
 * Unknown surfaces fail closed. Flagging is what exposes writing to another
 * person, so anything we cannot classify is left alone.
 *
 * @param string             $surface One of 'private_journal', 'shared_journal', 'comment'.
 * @param array<int,string>  $matches Result of reci_match_flagged_terms().
 */
function reci_should_flag_for_review( string $surface, array $matches ): bool {
	if ( [] === $matches ) {
		return false;
	}

	return in_array( $surface, [ 'shared_journal', 'comment' ], true );
}

/**
 * Hold a matching comment for review.
 *
 * Comments currently post straight through with no gate at all, so this is new
 * behaviour. The comment is still saved — it is held, never rejected.
 */
add_filter( 'pre_comment_approved', 'reci_hold_flagged_comment', 20, 2 );
function reci_hold_flagged_comment( $approved, $commentdata ) {
	// Leave spam and errors alone; this filter only downgrades an approval.
	if ( 'spam' === $approved || is_wp_error( $approved ) ) {
		return $approved;
	}

	$matches = reci_match_flagged_terms(
		(string) ( $commentdata['comment_content'] ?? '' ),
		reci_get_abuse_terms()
	);

	if ( ! reci_should_flag_for_review( 'comment', $matches ) ) {
		return $approved;
	}

	return 0;
}

/**
 * Record why a comment was held, so the moderator sees the reason.
 */
add_action( 'comment_post', 'reci_record_comment_flags', 10, 3 );
function reci_record_comment_flags( $comment_id, $approved, $commentdata ): void {
	$matches = reci_match_flagged_terms(
		(string) ( $commentdata['comment_content'] ?? '' ),
		reci_get_abuse_terms()
	);

	if ( [] === $matches ) {
		return;
	}

	update_comment_meta( $comment_id, '_reci_flagged_terms', $matches );
}

/**
 * Hand the term list and policy to the browser.
 *
 * The list is published in the guideline by design, so shipping it to the
 * client is not a disclosure.
 */
function reci_community_policy_script_data(): array {
	return [
		'terms'      => reci_get_abuse_terms(),
		'accentMap'  => reci_accent_fold_map(),
		'policyUrl'  => '#reci-community-policy',
		'policyHtml' => reci_get_community_policy(),
		'warning'    => __( 'This may need review before it is published. Read our community guideline.', 'reci-media-hub' ),
		'linkLabel'  => __( 'Community guideline', 'reci-media-hub' ),
	];
}

/**
 * Starter text for the three community settings.
 *
 * Used to seed a site that has never had them filled in - by the demo
 * installer, and once on upgrade for existing sites. Editors override any of
 * it from RECI Settings > Community; nothing here is read at render time, so
 * an edit is never quietly reverted.
 *
 * @return array<string,string>
 */
function reci_default_community_settings(): array {
	return [
		'submission_guidelines' => '<p>RECI curates content that is evidence-based and process-oriented, focused on practices, policies, programs, initiatives, frameworks, and resources that effectively advance racial equity. We seek contributions that inform individuals, communities, and organizations&mdash;and ultimately inspire them to share what they have learned.</p><h3>Evidence-Based Standard</h3><p>Submissions should be grounded in evidence. This includes peer-reviewed research, documented outcomes, evaluation data, practitioner knowledge, community-based participatory research, or other credible evidentiary foundations. Claims should be supported, and sources cited where possible.</p><h3>Process Orientation</h3><p>We value content that emphasizes process and developmental growth over static facts or one-time interventions. Content should guide readers, viewers and listeners through a journey of understanding&mdash;modeling the kind of ongoing consciousness development RECI champions.</p><h3>RECI Sphere Alignment</h3><p>All content should align with at least one of RECI&rsquo;s six spheres of consciousness development. Each sphere has both an awareness dimension and an action dimension, reflecting the journey from recognition to transformation.</p><h3>Content Standards</h3><p>All submissions should be original or properly attributed. Content should be respectful, constructive, and grounded in a commitment to advancing racial equity. We welcome diverse perspectives and encourage submissions from contributors of all backgrounds and career stages.</p><h3>Editorial Process</h3><p>All submissions are reviewed by the RECI editorial team for quality, evidence standards, process orientation, and alignment with the RECI framework. We may suggest revisions to strengthen your contribution. Expect a response within 10&ndash;15 business days.</p><h3>Accepted Formats</h3><p>Magazine articles (800&ndash;3,000 words), blog posts (400&ndash;1,200 words), video (3&ndash;30 min), podcasts (15&ndash;60 min), resources, virtual exhibits, assessments and tools, infographics, curricula, and other creative formats that advance racial equity consciousness.</p>',
		'community_policy'      => '<p>These guidelines cover reflections you choose to share and comments you leave anywhere on the hub. They exist so this stays a place where people can speak honestly about race without being harmed for doing so.</p><h3>What belongs here</h3><ul><li>Your own experience, in your own words&mdash;including the hard, unresolved or unflattering parts.</li><li>Naming racism you have witnessed, experienced or taken part in, and quoting the language that was used against you or others. Testimony about harmful speech is not the same as harmful speech, and it is welcome here.</li><li>Disagreement, including with RECI, offered in good faith.</li></ul><h3>What does not</h3><ul><li>Slurs, abuse or demeaning language aimed at a person or group taking part here.</li><li>Threats, harassment, or encouraging anyone to harm themselves or others.</li><li>Revealing someone else&rsquo;s identity or private details, including another contributor&rsquo;s.</li><li>Speech whose purpose is to advance racism rather than to examine it.</li></ul><h3>How this is reviewed</h3><p>Nothing you write is ever blocked or deleted as you type. Shared reflections and comments are read by a moderator before they appear publicly. If something is not published it stays in your journal, and it stays yours.</p><p>Some words are flagged so a moderator reads them in context. A flag is not an accusation: testimony that quotes a slur will be flagged and, in almost every case, published.</p><h3>Sharing anonymously</h3><p>You can share a reflection anonymously. Your name is then hidden from everyone reading the hub, including other members. The RECI team who moderate and run the site can still see it, so that abuse can be acted on.</p>',
		'abuse_terms'           => "# One term or phrase per line. Lines beginning with # are ignored.
# Matching never blocks a save: it warns the writer, and sends shared
# reflections and comments to the moderation queue for a human to read.
#
# These are phrases of directed abuse rather than a list of slurs. On this
# site people quote the language used against them, and a slur list would
# flag almost every piece of real testimony - burying actual abuse in false
# positives. Add the specific terms your moderators need.
kill yourself
kys
go back to your country
go back where you came from
your kind
subhuman
vermin
race traitor
white trash
deserve what you get
should be deported",
	];
}

/**
 * Fill any community setting that has never been given a value.
 *
 * Runs once per site. Existing values are never touched, and clearing a field
 * afterwards stays cleared - the flag means this will not run again.
 */
add_action( 'admin_init', 'reci_seed_community_defaults' );
function reci_seed_community_defaults(): void {
	if ( get_option( 'reci_community_defaults_seeded' ) ) {
		return;
	}

	$settings = get_option( 'reci_theme_settings', [] );
	$settings = is_array( $settings ) ? $settings : [];
	$changed  = false;

	foreach ( reci_default_community_settings() as $key => $value ) {
		if ( empty( $settings[ $key ] ) ) {
			$settings[ $key ] = $value;
			$changed          = true;
		}
	}

	if ( $changed ) {
		update_option( 'reci_theme_settings', $settings );
		reci_sync_moderation_keys( reci_parse_abuse_terms( (string) $settings['abuse_terms'] ) );
	}

	update_option( 'reci_community_defaults_seeded', 1 );
}
