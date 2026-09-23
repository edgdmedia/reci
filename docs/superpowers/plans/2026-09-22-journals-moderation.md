# Shared Journals, Moderation & Community Policy — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let members share reflection journal entries (optionally anonymously) for moderated publication, add a Moderator role, put like/bookmark counters on posts, surface submission guidelines before the collaborator gate, and warn writers against abusive language without ever blocking them.

**Architecture:** `wp_reci_journals` stays the source of truth for journal entries. Sharing an entry *mirrors* it into `wp_comments` as a pending `reci_journal` comment, so WordPress's own moderation queue, threading and `moderate_comments` capability do the work instead of a bespoke queue. Anonymity is enforced by *withholding* identity from the mirror (`comment_user_id = 0`) rather than by filtering it out at display time, so a missed code path degrades to "Anonymous" instead of leaking a real person.

**Tech Stack:** WordPress theme, PHP 8.5, `$wpdb` + `dbDelta`, WP REST API, vanilla JS (no build step for the PHP-side JS), Tailwind utility classes in templates.

**Spec:** `docs/superpowers/specs/2026-09-22-journals-moderation-design.md` — read it before starting. This plan implements it and does not repeat its reasoning.

---

## Global Constraints

- **Working tree:** the theme root, `/Users/olalekan/Projects/reci/media-hub`. Branch `development`.
- **`wordpress/` is a deploy mirror, not a second project.** Do all work at the repo root. Task 13 handles mirroring. Never edit `wordpress/` directly during Tasks 1–12.
- **Every new PHP file must be registered** in the `$reci_media_hub_includes` array in `inc/init.php`. It is not autoloaded. Files load in array order, so a file must appear *after* anything it depends on at load time.
- **Every PHP file starts with** `if ( ! defined( 'ABSPATH' ) ) { exit; }` after the docblock.
- **Syntax check every PHP file you touch:** `php -l <file>`. This is the theme's existing convention.
- **Text domain is `reci-media-hub`** for every `__()`, `esc_html__()` and `_e()` call.
- **Database version constant:** `$current_ver` in `inc/core/database.php` moves from `'1.5.0'` to `'1.6.0'`. Do not skip or reuse a version.
- **Roles version constant:** `RECI_ROLES_VERSION` in `inc/core/roles.php` moves from `'1.0.0'` to `'1.1.0'`. Roles only reinstall when this changes.
- **Journal statuses are exactly these four strings:** `private`, `pending`, `approved`, `rejected`. No others.
- **The mirror comment type is exactly `reci_journal`.**
- **The identity capability is exactly `reci_view_journal_identity`.** Held by `administrator` and `reci_moderator` only — never `editor`, never `reci_site_manager`.
- **Anonymous placeholder string is exactly `Anonymous`**, wrapped in `__( 'Anonymous', 'reci-media-hub' )` at every display site.
- **Never block a save.** The abuse term list warns and flags. No code path may refuse to persist a user's writing because it matched a term.
- **Private journals are never routed to a moderator.** Detection warns the writer and stops.
- Commit after every task. Use the message given in the task's final step.

## A note on verification

This theme has **no PHP test infrastructure** — no `composer.json`, no PHPUnit. Adding a Docker/`wp-env` integration suite was considered and deliberately not taken, to avoid introducing a new toolchain mid-feature.

Instead, Task 1 builds a **dependency-free PHP test harness** (`php tests/run.php`, no Composer, no Docker). The tasks below are structured so that all decision logic — term matching, status transitions, the anonymity gate, notification text construction, REST response shaping — lives in **pure functions that take their inputs as arguments** and can be tested by that harness. WordPress-dependent wiring is thin by design.

This is a real tradeoff, and you must respect it:

- Logic that the harness can cover **must** be written as a pure function and tested first. Do not inline it into a WordPress hook.
- Integration behaviour the harness cannot reach (real capabilities, real comment queries, live REST routes) has an explicit **Manual verification** block. Run it, and record the actual result. Do not mark a task complete on `php -l` alone.
- If you cannot verify something, say so plainly in your report rather than claiming it passes.

---

## File Structure

**New files:**

| Path | Responsibility |
|---|---|
| `tests/run.php` | Test runner. Discovers and executes `tests/test-*.php`. |
| `tests/bootstrap.php` | Minimal WordPress function stubs so pure functions load without WP. |
| `tests/test-*.php` | One file per unit under test. |
| `inc/features/engagement-counters.php` | Like/bookmark count storage, bumping, recount, backfill. |
| `inc/features/community-policy.php` | Term list parsing, text normalisation, term matching, flag decisions. |
| `inc/features/journal-sharing.php` | Share/unshare, mirror comment lifecycle, status transitions. |
| `inc/features/journal-identity.php` | The anonymity gate and every display-name decision. |
| `inc/features/journal-moderation.php` | Approve/reject handling, owner and author notifications. |
| `template-parts/common/guidelines-panel.php` | Reusable guidelines/policy panel. |
| `modules/reflection-system/templates/shared-journals-overlay.php` | The "read what others shared" overlay. |
| `assets/js/community-policy.js` | Typing-time warning for journal and comment textareas. |

**Modified files:**

| Path | Change |
|---|---|
| `inc/init.php` | Register the six new PHP files. |
| `inc/core/database.php` | Schema `1.5.0` → `1.6.0`, new columns, backfills. |
| `inc/core/roles.php` | Two new caps, the `reci_moderator` role, version `1.0.0` → `1.1.0`. |
| `inc/features/reflection-responses.php` | Route the create path through `pending`; extend the share route; expose new fields. |
| `inc/admin/dashboard.php` | Bump counters inside the two existing AJAX toggles. |
| `inc/admin/theme-settings.php` | A "Community" settings section and its sanitisation. |
| `inc/admin/class-reci-journals-list-table.php` | Status column, flagged-term badges, approve/reject actions. |
| `templates/page/template-submit-content.php` | Hoist guidelines above the state machine. |
| `templates/single/single-post.php` | Guideline link beside the comment form. |
| `modules/reflection-system/templates/response-block.php` | Share controls and the shared-entries button. |

---

## Phase 1 — Independent groundwork

Tasks 1–4 depend on nothing in Phase 2 and can ship on their own.

---

### Task 1: Dependency-free PHP test harness

**Files:**
- Create: `tests/run.php`
- Create: `tests/bootstrap.php`
- Create: `tests/test-harness.php`
- Modify: `package.json` (add a `test:php` script)

**Interfaces:**
- Consumes: nothing.
- Produces: `reci_assert( bool $condition, string $message ): void`, `reci_assert_same( mixed $expected, mixed $actual, string $message ): void`, `reci_test_stub_options( array $options ): void`. Every later task's tests use these three.

- [ ] **Step 1: Write the bootstrap of WordPress stubs**

Create `tests/bootstrap.php`:

```php
<?php
/**
 * Minimal WordPress stubs so pure theme functions load outside WordPress.
 *
 * Only declare what the functions under test actually call. Each stub is
 * guarded, so if this ever runs inside a real WordPress the real one wins.
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['reci_test_options'] = [];

/**
 * Seed the fake option store used by get_option()/update_option() stubs.
 *
 * @param array<string,mixed> $options Option name => value.
 */
function reci_test_stub_options( array $options ): void {
	$GLOBALS['reci_test_options'] = $options;
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( string $name, $default = false ) {
		return $GLOBALS['reci_test_options'][ $name ] ?? $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( string $name, $value ): bool {
		$GLOBALS['reci_test_options'][ $name ] = $value;
		return true;
	}
}

if ( ! function_exists( '__' ) ) {
	function __( string $text, string $domain = '' ): string {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( string $text, string $domain = '' ): string {
		return $text;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ): int {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( string $value ): string {
		return trim( strip_tags( $value ) );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( string $value ): string {
		return trim( strip_tags( $value ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( string $value ): string {
		return strip_tags( $value );
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( string $type, $gmt = 0 ): string {
		return gmdate( 'Y-m-d H:i:s' );
	}
}
```

- [ ] **Step 2: Write the runner**

Create `tests/run.php`:

```php
<?php
/**
 * Dependency-free test runner.
 *
 * Usage: php tests/run.php
 * Exit code 0 when everything passes, 1 otherwise.
 */

require __DIR__ . '/bootstrap.php';

$GLOBALS['reci_test_results'] = [
	'pass'     => 0,
	'fail'     => 0,
	'failures' => [],
];

/**
 * Assert a condition holds.
 */
function reci_assert( bool $condition, string $message ): void {
	if ( $condition ) {
		$GLOBALS['reci_test_results']['pass']++;
		return;
	}

	$GLOBALS['reci_test_results']['fail']++;
	$GLOBALS['reci_test_results']['failures'][] = $message;
}

/**
 * Assert strict equality, reporting both sides on failure.
 */
function reci_assert_same( $expected, $actual, string $message ): void {
	reci_assert(
		$expected === $actual,
		sprintf(
			'%s — expected %s, got %s',
			$message,
			var_export( $expected, true ),
			var_export( $actual, true )
		)
	);
}

foreach ( glob( __DIR__ . '/test-*.php' ) as $test_file ) {
	require $test_file;
}

$results = $GLOBALS['reci_test_results'];

foreach ( $results['failures'] as $failure ) {
	fwrite( STDERR, "FAIL: {$failure}\n" );
}

printf( "%d passed, %d failed\n", $results['pass'], $results['fail'] );

exit( $results['fail'] > 0 ? 1 : 0 );
```

- [ ] **Step 3: Write a test that proves the harness reports failure**

Create `tests/test-harness.php`:

```php
<?php
/**
 * The harness must be able to fail. A runner that always passes is worse
 * than no runner, so prove both directions here.
 */

reci_assert_same( 'a', 'a', 'harness: identical strings are equal' );
reci_assert( true, 'harness: true is truthy' );

// Prove failure detection without failing the suite: run the comparison the
// runner uses and check it returns false.
reci_assert_same( false, ( 'a' === 'b' ), 'harness: differing strings are not equal' );

reci_test_stub_options( [ 'reci_demo' => 'seeded' ] );
reci_assert_same( 'seeded', get_option( 'reci_demo' ), 'harness: option stub reads back' );
reci_assert_same( 'fallback', get_option( 'reci_missing', 'fallback' ), 'harness: option stub falls back' );
```

- [ ] **Step 4: Run the suite and verify it passes**

Run: `php tests/run.php`
Expected: `5 passed, 0 failed`, exit code 0.

- [ ] **Step 5: Verify the runner actually fails when an assertion fails**

Temporarily append to `tests/test-harness.php`:

```php
reci_assert_same( 'x', 'y', 'harness: deliberate failure' );
```

Run: `php tests/run.php; echo "exit=$?"`
Expected: `FAIL: harness: deliberate failure — expected 'x', got 'y'` on stderr, `5 passed, 1 failed`, `exit=1`.

**Now remove those two lines again** and re-run to confirm `5 passed, 0 failed`.

- [ ] **Step 6: Add the npm script**

In `package.json`, add to `"scripts"` (keep every existing entry):

```json
"test:php": "php tests/run.php"
```

Run: `npm run test:php`
Expected: `5 passed, 0 failed`.

- [ ] **Step 7: Commit**

```bash
git add tests/ package.json
git commit -m "test: add dependency-free PHP test harness

No composer, no Docker. Discovers tests/test-*.php and runs them
against a minimal WordPress stub layer, so pure theme logic can be
tested without a WordPress install.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 2: Like and bookmark counters

Implements spec §5.E.

**Files:**
- Create: `inc/features/engagement-counters.php`
- Create: `tests/test-engagement-counters.php`
- Modify: `inc/init.php`
- Modify: `inc/admin/dashboard.php` (inside `reci_ajax_toggle_bookmark` and `reci_ajax_toggle_like`)

**Interfaces:**
- Consumes: `reci_assert_same()` from Task 1. `reci_get_user_likes( int $user_id ): array` and `reci_get_user_bookmarks( int $user_id ): array`, both already defined at `inc/admin/dashboard.php:191-199`.
- Produces:
  - `reci_tally_engagement( array $likes_by_user, array $bookmarks_by_user ): array` — pure. Returns `[ post_id => [ 'likes' => int, 'bookmarks' => int ] ]`.
  - `reci_get_like_count( int $post_id ): int`
  - `reci_get_bookmark_count( int $post_id ): int`
  - `reci_set_engagement_count( int $post_id, string $kind, int $count ): void` where `$kind` is `'like'` or `'bookmark'`
  - `reci_backfill_engagement_counts(): int` — returns the number of posts written. Task 5 calls this.

- [ ] **Step 1: Write the failing test for the pure tally**

Create `tests/test-engagement-counters.php`:

```php
<?php
/**
 * Counting is the part that can be wrong silently, so it is a pure function
 * over the two user-meta shapes the dashboard already stores.
 *
 * Likes are stored as a flat list of post IDs.
 * Bookmarks are stored as a list of [ 'post_id' => int, 'bookmarked_at' => int ].
 */

require_once __DIR__ . '/../inc/features/engagement-counters.php';

$likes_by_user = [
	7  => [ 10, 11 ],
	8  => [ 11 ],
	9  => [],
];

$bookmarks_by_user = [
	7 => [ [ 'post_id' => 11, 'bookmarked_at' => 1 ] ],
	8 => [ [ 'post_id' => 11, 'bookmarked_at' => 2 ], [ 'post_id' => 12, 'bookmarked_at' => 3 ] ],
];

$tally = reci_tally_engagement( $likes_by_user, $bookmarks_by_user );

reci_assert_same( 1, $tally[10]['likes'], 'tally: post 10 has one like' );
reci_assert_same( 0, $tally[10]['bookmarks'], 'tally: post 10 has no bookmarks' );
reci_assert_same( 2, $tally[11]['likes'], 'tally: post 11 has two likes' );
reci_assert_same( 2, $tally[11]['bookmarks'], 'tally: post 11 has two bookmarks' );
reci_assert_same( 0, $tally[12]['likes'], 'tally: post 12 has no likes' );
reci_assert_same( 1, $tally[12]['bookmarks'], 'tally: post 12 has one bookmark' );

// A user with an empty list must not create phantom entries.
reci_assert_same( false, isset( $tally[0] ), 'tally: no phantom post 0' );

// Malformed rows must be skipped, not fatal. Real user meta accumulates junk.
$messy = reci_tally_engagement(
	[ 7 => [ 10, 'not-an-id', 0, null ] ],
	[ 7 => [ [ 'no_post_id' => 1 ], 'garbage', [ 'post_id' => 13 ] ] ]
);
reci_assert_same( 1, $messy[10]['likes'], 'tally: valid like survives junk' );
reci_assert_same( 1, $messy[13]['bookmarks'], 'tally: valid bookmark survives junk' );
reci_assert_same( false, isset( $messy[0] ), 'tally: junk does not become post 0' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — `require_once` cannot find `inc/features/engagement-counters.php`, a PHP fatal naming that path.

- [ ] **Step 3: Write the implementation**

Create `inc/features/engagement-counters.php`:

```php
<?php
/**
 * Like and bookmark counters.
 *
 * Likes and bookmarks are stored per user, in user meta. Counting them per
 * post therefore means reading every user's meta, which is far too expensive
 * to do on a page render. These denormalised post-meta counters are the cache;
 * the user meta remains the source of truth.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'RECI_TESTS_BOOTSTRAPPED' ) ) {
	// Allow the test harness to require this file directly.
	if ( ! function_exists( 'reci_assert' ) ) {
		exit;
	}
}

const RECI_LIKE_COUNT_META     = '_reci_like_count';
const RECI_BOOKMARK_COUNT_META = '_reci_bookmark_count';

/**
 * Tally likes and bookmarks per post.
 *
 * Pure: it takes the two meta shapes as arguments and touches no globals, so
 * it can be tested without WordPress.
 *
 * @param array<int,array<int,mixed>> $likes_by_user     user_id => list of post IDs.
 * @param array<int,array<int,mixed>> $bookmarks_by_user user_id => list of [ 'post_id' => int, ... ].
 *
 * @return array<int,array{likes:int,bookmarks:int}> post_id => counts.
 */
function reci_tally_engagement( array $likes_by_user, array $bookmarks_by_user ): array {
	$tally = [];

	$touch = static function ( int $post_id ) use ( &$tally ): void {
		if ( ! isset( $tally[ $post_id ] ) ) {
			$tally[ $post_id ] = [ 'likes' => 0, 'bookmarks' => 0 ];
		}
	};

	foreach ( $likes_by_user as $likes ) {
		foreach ( (array) $likes as $post_id ) {
			$post_id = (int) $post_id;
			if ( $post_id <= 0 ) {
				continue;
			}
			$touch( $post_id );
			$tally[ $post_id ]['likes']++;
		}
	}

	foreach ( $bookmarks_by_user as $bookmarks ) {
		foreach ( (array) $bookmarks as $bookmark ) {
			if ( ! is_array( $bookmark ) || ! isset( $bookmark['post_id'] ) ) {
				continue;
			}
			$post_id = (int) $bookmark['post_id'];
			if ( $post_id <= 0 ) {
				continue;
			}
			$touch( $post_id );
			$tally[ $post_id ]['bookmarks']++;
		}
	}

	return $tally;
}

/**
 * Read a post's like count.
 */
function reci_get_like_count( int $post_id ): int {
	return (int) get_post_meta( $post_id, RECI_LIKE_COUNT_META, true );
}

/**
 * Read a post's bookmark count.
 */
function reci_get_bookmark_count( int $post_id ): int {
	return (int) get_post_meta( $post_id, RECI_BOOKMARK_COUNT_META, true );
}

/**
 * Write a post's count for one kind.
 *
 * @param string $kind 'like' or 'bookmark'.
 */
function reci_set_engagement_count( int $post_id, string $kind, int $count ): void {
	$meta_key = ( 'like' === $kind ) ? RECI_LIKE_COUNT_META : RECI_BOOKMARK_COUNT_META;
	update_post_meta( $post_id, $meta_key, max( 0, $count ) );
}

/**
 * Nudge a counter by one in either direction.
 *
 * Clamped at zero: a counter that has drifted below the real figure should
 * recover on the next recount rather than render as a negative.
 *
 * @param string $kind  'like' or 'bookmark'.
 * @param int    $delta Normally 1 or -1.
 */
function reci_bump_engagement_count( int $post_id, string $kind, int $delta ): void {
	$current = ( 'like' === $kind )
		? reci_get_like_count( $post_id )
		: reci_get_bookmark_count( $post_id );

	reci_set_engagement_count( $post_id, $kind, $current + $delta );
}

/**
 * Recompute every counter from user meta.
 *
 * Called once by the 1.6.0 migration, and available afterwards for repair if
 * the cached counts ever drift from the user meta.
 *
 * @return int Number of posts written.
 */
function reci_backfill_engagement_counts(): int {
	global $wpdb;

	$likes_by_user     = [];
	$bookmarks_by_user = [];

	$rows = $wpdb->get_results(
		"SELECT user_id, meta_key, meta_value
		   FROM {$wpdb->usermeta}
		  WHERE meta_key IN ( 'reci_likes', 'reci_bookmarks' )"
	);

	foreach ( $rows as $row ) {
		$value = maybe_unserialize( $row->meta_value );
		if ( ! is_array( $value ) ) {
			continue;
		}

		if ( 'reci_likes' === $row->meta_key ) {
			$likes_by_user[ (int) $row->user_id ] = $value;
		} else {
			$bookmarks_by_user[ (int) $row->user_id ] = $value;
		}
	}

	$tally = reci_tally_engagement( $likes_by_user, $bookmarks_by_user );

	foreach ( $tally as $post_id => $counts ) {
		reci_set_engagement_count( $post_id, 'like', $counts['likes'] );
		reci_set_engagement_count( $post_id, 'bookmark', $counts['bookmarks'] );
	}

	return count( $tally );
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `15 passed, 0 failed`.

- [ ] **Step 5: Register the file**

In `inc/init.php`, add to `$reci_media_hub_includes`, immediately after `'/inc/features/listing-builder.php',`:

```php
	'/inc/features/engagement-counters.php',
```

Run: `php -l inc/init.php && php -l inc/features/engagement-counters.php`
Expected: `No syntax errors detected` for both.

- [ ] **Step 6: Bump the counters from the existing toggles**

In `inc/admin/dashboard.php`, inside `reci_ajax_toggle_bookmark`, replace this line:

```php
	update_user_meta( $user_id, 'reci_bookmarks', array_values( $bookmarks ) );
	wp_send_json_success( [ 'bookmarked' => $bookmarked ] );
```

with:

```php
	update_user_meta( $user_id, 'reci_bookmarks', array_values( $bookmarks ) );
	reci_bump_engagement_count( $post_id, 'bookmark', $bookmarked ? 1 : -1 );
	wp_send_json_success(
		[
			'bookmarked' => $bookmarked,
			'count'      => reci_get_bookmark_count( $post_id ),
		]
	);
```

Then, inside `reci_ajax_toggle_like`, replace:

```php
	update_user_meta( $user_id, 'reci_likes', array_values( $likes ) );
	wp_send_json_success( [ 'liked' => $liked ] );
```

with:

```php
	update_user_meta( $user_id, 'reci_likes', array_values( $likes ) );
	reci_bump_engagement_count( $post_id, 'like', $liked ? 1 : -1 );
	wp_send_json_success(
		[
			'liked' => $liked,
			'count' => reci_get_like_count( $post_id ),
		]
	);
```

Run: `php -l inc/admin/dashboard.php`
Expected: `No syntax errors detected`.

- [ ] **Step 7: Manual verification**

On the Local site `reci-media-hub`, logged in as any member:

1. Open a post that shows the like button. Note the count.
2. Click like. The AJAX response now carries `count`; confirm the stored meta changed:
   `wp post meta get <post_id> _reci_like_count` → should be 1 higher.
3. Click unlike. Re-run the meta read → back to the previous value.
4. Click unlike again when already at 0 → the meta must read `0`, never a negative.

Record the four observed values in your report.

- [ ] **Step 8: Commit**

```bash
git add inc/features/engagement-counters.php tests/test-engagement-counters.php inc/init.php inc/admin/dashboard.php
git commit -m "feat: add like and bookmark counters

Likes and bookmarks live in per-user meta, so a per-post count meant
scanning every user. Denormalised _reci_like_count and
_reci_bookmark_count post meta now cache the figures, bumped by the
existing AJAX toggles, with user meta still the source of truth.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 3: Community policy settings and term parsing

Implements spec §5.G (the settings surface) and §5.F's content source.

**Files:**
- Create: `inc/features/community-policy.php`
- Create: `tests/test-community-policy.php`
- Modify: `inc/init.php`
- Modify: `inc/admin/theme-settings.php`

**Interfaces:**
- Consumes: `reci_assert_same()`, `reci_test_stub_options()` from Task 1. `reci_add_field( string $key, string $label, string $type, string $page, string $section, string $description = '', array $choices = [], array $atts = [] ): void` from `inc/admin/theme-settings.php:331`.
- Produces:
  - `reci_parse_abuse_terms( string $raw ): array` — pure. Newline-separated text to a clean, lowercased, deduplicated list.
  - `reci_get_abuse_terms(): array`
  - `reci_get_community_policy(): string`
  - `reci_get_submission_guidelines(): string`
  - `reci_sync_moderation_keys( array $terms ): void`

Task 6 adds the matcher to this same file. Task 12 consumes all of it.

- [ ] **Step 1: Write the failing test**

Create `tests/test-community-policy.php`:

```php
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
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — fatal, `inc/features/community-policy.php` does not exist.

- [ ] **Step 3: Write the implementation**

Create `inc/features/community-policy.php`:

```php
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
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `25 passed, 0 failed`.

- [ ] **Step 5: Register the file**

In `inc/init.php`, add immediately after the `engagement-counters.php` line from Task 2:

```php
	'/inc/features/community-policy.php',
```

Run: `php -l inc/init.php`
Expected: `No syntax errors detected`.

- [ ] **Step 6: Add the settings section**

In `inc/admin/theme-settings.php`, inside `reci_register_settings()`, add a new section after the Email section block (after the `email_log_retention` field line):

```php
	// ── 1c. Community ─────────────────────────────────────────────────────
	add_settings_section( 'reci_community', 'Community', '__return_false', 'reci-settings-community' );

	reci_add_field( 'submission_guidelines', 'Submission Guidelines', 'textarea', 'reci-settings-community', 'reci_community', 'Shown at the top of the Submit Content page to everyone, including signed-out visitors. Basic HTML is allowed.' );
	reci_add_field( 'community_policy',      'Community Guideline',   'textarea', 'reci-settings-community', 'reci_community', 'Shown when someone clicks the guideline link beside a journal or comment box. Basic HTML is allowed.' );
	reci_add_field( 'abuse_terms',           'Flagged Terms',         'textarea', 'reci-settings-community', 'reci_community', 'One term or phrase per line. Lines starting with # are ignored. Matching never blocks a save — it warns the writer and sends shared entries and comments to the moderation queue.' );
```

- [ ] **Step 7: Add sanitisation and the moderation-key mirror**

In `inc/admin/theme-settings.php`, inside `reci_sanitize_settings()`, add `'abuse_terms'` to the existing `$textarea_fields` array:

```php
	$textarea_fields = [ 'footer_address', 'abuse_terms' ];
```

Then, immediately before the function's final `return $clean;`, add:

```php
	// The two policy bodies allow the same limited HTML as post content, so
	// sanitize_textarea_field would strip the formatting an editor just wrote.
	foreach ( [ 'submission_guidelines', 'community_policy' ] as $field ) {
		if ( isset( $input[ $field ] ) ) {
			$clean[ $field ] = wp_kses_post( $input[ $field ] );
		}
	}

	// Keep WordPress's own moderation keywords in step with our list, so the
	// comment path is held by core rather than by a parallel implementation.
	if ( isset( $clean['abuse_terms'] ) ) {
		reci_sync_moderation_keys( reci_parse_abuse_terms( $clean['abuse_terms'] ) );
	}
```

Run: `php -l inc/admin/theme-settings.php`
Expected: `No syntax errors detected`.

- [ ] **Step 8: Manual verification**

On the Local site, as an administrator:

1. Open RECI Settings. Confirm a **Community** tab or section is present with three textareas.
2. Put `Test Term` and `# ignored` on separate lines in Flagged Terms. Save.
3. Confirm it round-trips: reload the page, the textarea still shows both lines.
4. Confirm the mirror: `wp option get moderation_keys` → must output `test term` and **not** `# ignored`.
5. Confirm `wp option get disallowed_keys` is unchanged (empty, or whatever it was before).

Record the output of steps 4 and 5.

- [ ] **Step 9: Commit**

```bash
git add inc/features/community-policy.php tests/test-community-policy.php inc/init.php inc/admin/theme-settings.php
git commit -m "feat: add community policy settings and term parsing

Three new theme settings: submission guidelines, community guideline,
and a flagged-term list. The term list is mirrored into WordPress's
moderation_keys so the comment path is held by core; disallowed_keys is
deliberately left alone because it rejects outright.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 4: Guidelines before the collaborator gate

Implements spec §5.F.

**Files:**
- Create: `template-parts/common/guidelines-panel.php`
- Modify: `templates/page/template-submit-content.php`

**Interfaces:**
- Consumes: `reci_get_submission_guidelines()` and `reci_get_community_policy()` from Task 3.
- Produces: a template part taking `[ 'title' => string, 'body' => string, 'collapsible' => bool ]` via `get_template_part()`'s `$args`.

- [ ] **Step 1: Create the template part**

Create `template-parts/common/guidelines-panel.php`:

```php
<?php
/**
 * Guidelines panel.
 *
 * Used above the Submit Content flow and inside the community policy overlay.
 * Renders nothing at all when no body has been configured, so an unconfigured
 * site does not show an empty box.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panel = wp_parse_args(
	$args ?? [],
	[
		'title'       => __( 'Before you submit', 'reci-media-hub' ),
		'body'        => '',
		'collapsible' => false,
	]
);

if ( '' === trim( (string) $panel['body'] ) ) {
	return;
}
?>
<?php if ( $panel['collapsible'] ) : ?>
<details class="mb-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6" open>
	<summary class="cursor-pointer font-heading text-xl font-bold text-zinc-900">
		<?php echo esc_html( $panel['title'] ); ?>
	</summary>
	<div class="prose prose-zinc mt-4 max-w-none text-base leading-7 text-zinc-700">
		<?php echo wp_kses_post( $panel['body'] ); ?>
	</div>
</details>
<?php else : ?>
<div class="mb-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6">
	<h2 class="font-heading text-xl font-bold text-zinc-900"><?php echo esc_html( $panel['title'] ); ?></h2>
	<div class="prose prose-zinc mt-4 max-w-none text-base leading-7 text-zinc-700">
		<?php echo wp_kses_post( $panel['body'] ); ?>
	</div>
</div>
<?php endif; ?>
```

Run: `php -l template-parts/common/guidelines-panel.php`
Expected: `No syntax errors detected`.

- [ ] **Step 2: Hoist it above the state machine**

In `templates/page/template-submit-content.php`, find this existing block:

```php
			<?php reci_render_collaborator_application_notices(); ?>

			<?php if ( 'approved_collaborator' !== $submit_state ) : ?>
				<?php reci_render_submit_flow_progress( $submit_state ); ?>
			<?php endif; ?>
```

Insert the guidelines panel **before** the notices call, so it is the first thing in the section for every one of the four states:

```php
			<?php
			get_template_part(
				'template-parts/common/guidelines-panel',
				null,
				[
					'title'       => __( 'Submission Guidelines', 'reci-media-hub' ),
					'body'        => reci_get_submission_guidelines(),
					// Approved collaborators have read this before and are here
					// to work, so it starts collapsed for them alone.
					'collapsible' => ( 'approved_collaborator' === $submit_state ),
				]
			);
			?>

			<?php reci_render_collaborator_application_notices(); ?>
```

Run: `php -l templates/page/template-submit-content.php`
Expected: `No syntax errors detected`.

- [ ] **Step 3: Manual verification**

Set a Submission Guidelines body in RECI Settings first, then visit the Submit Content page in these four states and confirm the guidelines appear **above** everything else each time:

1. Signed out (guest) — guidelines above the account creation form.
2. Signed in as a `subscriber` — above the contributor profile form.
3. Signed in as a pending collaborator — above the "In Review" card.
4. Signed in as an approved collaborator (`author` or `contributor` with an approved application) — present, collapsed by default, expandable.

Then clear the setting and reload as a guest: the panel must disappear entirely rather than render an empty box.

Record which of the five checks passed.

- [ ] **Step 4: Commit**

```bash
git add template-parts/common/guidelines-panel.php templates/page/template-submit-content.php
git commit -m "feat: show submission guidelines before the collaborator gate

The guidelines were only reachable after approval. They now render above
the state machine for all four states, including signed-out visitors,
and collapse by default for approved collaborators who have read them.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

## Phase 2 — Shared journals and moderation

Tasks 5–12 form one dependency chain. Do them in order.

---

### Task 5: Schema migration to 1.6.0

Implements spec §4.1 and §5.A.

**Files:**
- Create: `tests/test-journal-status.php`
- Modify: `inc/core/database.php`
- Modify: `inc/features/journal-status.php` (created here)
- Create: `inc/features/journal-status.php`
- Modify: `inc/init.php`

**Interfaces:**
- Consumes: `reci_backfill_engagement_counts()` from Task 2.
- Produces:
  - `RECI_JOURNAL_STATUSES` — the four valid strings.
  - `reci_journal_status_from_legacy( int $is_shared ): string`
  - `reci_journal_is_valid_status( string $status ): bool`
  - `reci_journal_can_transition( string $from, string $to ): bool`

Tasks 7 and 10 depend on all four.

- [ ] **Step 1: Write the failing test**

Create `tests/test-journal-status.php`:

```php
<?php
/**
 * Status transitions decide whether private writing becomes public, so the
 * rules are a pure function with an explicit table rather than scattered ifs.
 */

require_once __DIR__ . '/../inc/features/journal-status.php';

// Legacy mapping. Rows already flagged shared were publicly visible before the
// migration, so they must land on 'approved' — migrating must never silently
// unpublish someone's entry, nor silently publish a private one.
reci_assert_same( 'approved', reci_journal_status_from_legacy( 1 ), 'legacy: is_shared 1 becomes approved' );
reci_assert_same( 'private',  reci_journal_status_from_legacy( 0 ), 'legacy: is_shared 0 becomes private' );

reci_assert_same( true,  reci_journal_is_valid_status( 'private' ),  'valid: private' );
reci_assert_same( true,  reci_journal_is_valid_status( 'pending' ),  'valid: pending' );
reci_assert_same( true,  reci_journal_is_valid_status( 'approved' ), 'valid: approved' );
reci_assert_same( true,  reci_journal_is_valid_status( 'rejected' ), 'valid: rejected' );
reci_assert_same( false, reci_journal_is_valid_status( 'shared' ),   'valid: rejects unknown status' );
reci_assert_same( false, reci_journal_is_valid_status( '' ),         'valid: rejects empty status' );

// Sharing sends an entry for review. It must never jump straight to approved.
reci_assert_same( true,  reci_journal_can_transition( 'private', 'pending' ),   'transition: share sends for review' );
reci_assert_same( false, reci_journal_can_transition( 'private', 'approved' ), 'transition: sharing cannot self-approve' );

// Moderation outcomes.
reci_assert_same( true, reci_journal_can_transition( 'pending', 'approved' ), 'transition: moderator approves' );
reci_assert_same( true, reci_journal_can_transition( 'pending', 'rejected' ), 'transition: moderator rejects' );

// The author can withdraw from any state back to private.
reci_assert_same( true, reci_journal_can_transition( 'pending', 'private' ),  'transition: withdraw from pending' );
reci_assert_same( true, reci_journal_can_transition( 'approved', 'private' ), 'transition: withdraw from approved' );
reci_assert_same( true, reci_journal_can_transition( 'rejected', 'private' ), 'transition: withdraw from rejected' );

// A rejected entry must be re-reviewed, not quietly restored.
reci_assert_same( false, reci_journal_can_transition( 'rejected', 'approved' ), 'transition: rejected cannot skip review' );

// Re-sharing a rejected entry goes back through the queue.
reci_assert_same( true, reci_journal_can_transition( 'rejected', 'pending' ), 'transition: rejected can be resubmitted' );

// A no-op is not an error, but it is not a transition either.
reci_assert_same( false, reci_journal_can_transition( 'private', 'private' ), 'transition: same-state is not a transition' );

// Unknown states never transition.
reci_assert_same( false, reci_journal_can_transition( 'bogus', 'pending' ), 'transition: unknown source rejected' );
reci_assert_same( false, reci_journal_can_transition( 'pending', 'bogus' ), 'transition: unknown target rejected' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — fatal, `inc/features/journal-status.php` does not exist.

- [ ] **Step 3: Write the implementation**

Create `inc/features/journal-status.php`:

```php
<?php
/**
 * Journal status rules.
 *
 * A journal entry's status decides whether private writing is visible to other
 * people, so the transition table is explicit and lives in one place. Nothing
 * outside this file should compare status strings directly.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * The only four statuses a journal entry may hold.
 */
const RECI_JOURNAL_STATUSES = [ 'private', 'pending', 'approved', 'rejected' ];

/**
 * Map the pre-1.6.0 `is_shared` flag onto a status.
 *
 * Rows already flagged shared were publicly visible, so they become approved.
 * Migrating must not change what anyone can see.
 */
function reci_journal_status_from_legacy( int $is_shared ): string {
	return 1 === $is_shared ? 'approved' : 'private';
}

/**
 * Is this one of the four known statuses?
 */
function reci_journal_is_valid_status( string $status ): bool {
	return in_array( $status, RECI_JOURNAL_STATUSES, true );
}

/**
 * May an entry move from one status to another?
 *
 * The table is deliberately restrictive. Two rules matter most: sharing can
 * only reach `pending`, never `approved` — nothing may self-publish; and a
 * rejected entry cannot become approved without going back through the queue.
 */
function reci_journal_can_transition( string $from, string $to ): bool {
	if ( ! reci_journal_is_valid_status( $from ) || ! reci_journal_is_valid_status( $to ) ) {
		return false;
	}

	if ( $from === $to ) {
		return false;
	}

	$allowed = [
		'private'  => [ 'pending' ],
		'pending'  => [ 'approved', 'rejected', 'private' ],
		'approved' => [ 'rejected', 'private' ],
		'rejected' => [ 'pending', 'private' ],
	];

	return in_array( $to, $allowed[ $from ], true );
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `45 passed, 0 failed`.

- [ ] **Step 5: Register the file**

In `inc/init.php`, add after the `community-policy.php` line:

```php
	'/inc/features/journal-status.php',
```

- [ ] **Step 6: Add the columns and bump the version**

In `inc/core/database.php`, change:

```php
	$current_ver   = '1.5.0';
```

to:

```php
	$current_ver   = '1.6.0';
```

Then, in the `$sql_journals` CREATE TABLE statement, add the five new columns and two keys. The statement must end up reading exactly:

```php
		$sql_journals = "CREATE TABLE $table_journals (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			reflection_id bigint(20) unsigned NOT NULL DEFAULT 0,
			prompt text NOT NULL,
			response longtext NOT NULL,
			is_shared tinyint(1) NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'private',
			is_anonymous tinyint(1) NOT NULL DEFAULT 0,
			shared_at datetime NULL DEFAULT NULL,
			comment_id bigint(20) unsigned NOT NULL DEFAULT 0,
			flagged_terms text NOT NULL,
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY reflection_id (reflection_id),
			KEY status (status),
			KEY comment_id (comment_id)
		) $charset_collate;";
```

`dbDelta` adds the columns to an existing table; it does not need a separate ALTER.

- [ ] **Step 7: Add the 1.6.0 migration block**

In `inc/core/database.php`, immediately before the closing `update_option( 'reci_db_version', $current_ver );`, add:

```php
		if ( version_compare( (string) $installed_ver, '1.6.0', '<' ) ) {
			// Entries already flagged shared were publicly visible before this
			// version. Land them on 'approved' so the migration changes what
			// the database records, not what anybody can see.
			$wpdb->query(
				"UPDATE {$table_journals}
				    SET status = 'approved', shared_at = created_at
				  WHERE is_shared = 1"
			);

			$wpdb->query(
				"UPDATE {$table_journals}
				    SET status = 'private'
				  WHERE is_shared = 0"
			);

			// Counters have never been computed, so build them once from the
			// per-user meta that has always been the source of truth.
			if ( function_exists( 'reci_backfill_engagement_counts' ) ) {
				reci_backfill_engagement_counts();
			}
		}
```

Run: `php -l inc/core/database.php && php -l inc/features/journal-status.php && php -l inc/init.php`
Expected: `No syntax errors detected` for all three.

- [ ] **Step 8: Manual verification**

This is a migration against real data. Back up first.

```bash
wp db export ~/reci-pre-1.6.0.sql
```

Seed a representative mix before migrating:

```bash
wp db query "INSERT INTO wp_reci_journals (user_id, reflection_id, prompt, response, is_shared, created_at) VALUES (1, 0, 'test shared', 'body', 1, NOW()), (1, 0, 'test private', 'body', 0, NOW());"
```

Trigger the migration by loading any wp-admin page (it runs on `admin_init`), then verify:

```bash
wp db query "SELECT id, is_shared, status, shared_at FROM wp_reci_journals ORDER BY id DESC LIMIT 2;"
```

Expected: the `is_shared = 1` row has `status = 'approved'` and a non-null `shared_at`; the `is_shared = 0` row has `status = 'private'` and a null `shared_at`.

Then confirm the version moved and that re-running is a no-op:

```bash
wp option get reci_db_version   # expect 1.6.0
```

Load another wp-admin page and re-run the SELECT. Nothing may change.

Record both SELECT outputs.

- [ ] **Step 9: Commit**

```bash
git add inc/core/database.php inc/features/journal-status.php tests/test-journal-status.php inc/init.php
git commit -m "feat: migrate journals schema to 1.6.0

Adds status, is_anonymous, shared_at, comment_id and flagged_terms to
wp_reci_journals, with an explicit transition table so nothing can
self-publish: sharing reaches 'pending' only, and a rejected entry
cannot become approved without going back through the queue.

Backfills status from the legacy is_shared flag so the migration
changes what is recorded, not what anyone can see, and builds the
engagement counters for the first time.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 6: Abuse term matcher

Implements spec §5.G (the matcher).

**Files:**
- Create: `tests/test-term-matching.php`
- Modify: `inc/features/community-policy.php`

**Interfaces:**
- Consumes: `reci_get_abuse_terms()` from Task 3.
- Produces:
  - `reci_normalize_for_matching( string $text ): string`
  - `reci_match_flagged_terms( string $text, array $terms ): array` — pure; returns the matched terms.
  - `reci_text_has_flagged_terms( string $text ): bool` — reads the configured list.

Task 12 consumes both.

- [ ] **Step 1: Write the failing test**

Create `tests/test-term-matching.php`:

```php
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
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — `Call to undefined function reci_match_flagged_terms()`.

- [ ] **Step 3: Write the implementation**

Append to `inc/features/community-policy.php`, before the end of the file:

```php
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
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `62 passed, 0 failed`.

Two failure modes to watch for, both caused by ordering inside `reci_normalize_for_matching()`:

- If `match: leetspeak digits` fails, digit substitution is running **after** the `[^a-z0-9\s]` strip. It must run before, or `b4dw0rd` keeps its digits as digits and never becomes `badword`.
- If `match: punctuation is a boundary` fails, a punctuation character is being mapped to a letter. Only digits belong in the substitution map — mapping `!` to `i` turns `badword!` into `badwordi`, which the word-boundary lookahead then refuses to match.

- [ ] **Step 5: Commit**

```bash
git add inc/features/community-policy.php tests/test-term-matching.php
git commit -m "feat: add abuse term matcher

Word-boundary matching over normalised text: case folded, accents
stripped, common leetspeak substitutions undone, separator characters
removed. A term inside a longer ordinary word deliberately does not
match, because a false positive sends someone's writing to a stranger.

Normalisation is exported so the browser-side warning can agree with
the server.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 7: Moderator role and the identity gate

Implements spec §3.2 and §5.C (the role and capability half).

**Files:**
- Create: `inc/features/journal-identity.php`
- Create: `tests/test-journal-identity.php`
- Modify: `inc/core/roles.php`
- Modify: `inc/init.php`

**Interfaces:**
- Consumes: `reci_custom_capabilities(): array` and `reci_install_roles(): void` from `inc/core/roles.php`.
- Produces:
  - `reci_journal_display_identity( bool $is_anonymous, bool $viewer_can_see_identity, string $real_name ): array` — pure. Returns `[ 'name' => string, 'is_masked' => bool ]`.
  - `reci_can_see_journal_identity( ?int $user_id = null ): bool`
  - `reci_journal_author_for_display( int $journal_id ): array` — returns `[ 'name' => string, 'is_masked' => bool, 'user_id' => int ]` where `user_id` is `0` whenever masked.

Tasks 9, 10 and 11 all call `reci_journal_author_for_display()`.

- [ ] **Step 1: Write the failing test**

Create `tests/test-journal-identity.php`:

```php
<?php
/**
 * The masking decision is the highest-consequence pure function in this
 * feature: getting it wrong exposes a real person who was promised anonymity.
 * It takes its inputs as arguments precisely so it can be exhaustively tested.
 */

require_once __DIR__ . '/../inc/features/journal-identity.php';

// Not anonymous: everyone sees the real name, whatever their capability.
$r = reci_journal_display_identity( false, false, 'Ada Obi' );
reci_assert_same( 'Ada Obi', $r['name'], 'identity: named entry shows the name to an ordinary viewer' );
reci_assert_same( false, $r['is_masked'], 'identity: named entry is not masked' );

$r = reci_journal_display_identity( false, true, 'Ada Obi' );
reci_assert_same( 'Ada Obi', $r['name'], 'identity: named entry shows the name to a privileged viewer' );
reci_assert_same( false, $r['is_masked'], 'identity: named entry is not masked for a privileged viewer' );

// Anonymous, ordinary viewer: masked. This covers the public, the reflection
// owner, editors and site managers alike — none of them hold the capability.
$r = reci_journal_display_identity( true, false, 'Ada Obi' );
reci_assert_same( 'Anonymous', $r['name'], 'identity: anonymous entry masks the name' );
reci_assert_same( true, $r['is_masked'], 'identity: anonymous entry reports itself masked' );

// Anonymous, privileged viewer: revealed.
$r = reci_journal_display_identity( true, true, 'Ada Obi' );
reci_assert_same( 'Ada Obi', $r['name'], 'identity: privileged viewer sees through anonymity' );
reci_assert_same( false, $r['is_masked'], 'identity: privileged view is not masked' );

// A missing real name must never leak an empty string into the UI, and must
// never be mistaken for "not anonymous".
$r = reci_journal_display_identity( false, false, '' );
reci_assert_same( 'Anonymous', $r['name'], 'identity: empty real name falls back to the placeholder' );
reci_assert_same( true, $r['is_masked'], 'identity: empty real name counts as masked' );

// Deleted user: same fallback, not a fatal and not a blank byline.
$r = reci_journal_display_identity( true, true, '' );
reci_assert_same( 'Anonymous', $r['name'], 'identity: privileged view of a deleted user still has a name' );
reci_assert_same( true, $r['is_masked'], 'identity: deleted user counts as masked' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — fatal, `inc/features/journal-identity.php` does not exist.

- [ ] **Step 3: Write the implementation**

Create `inc/features/journal-identity.php`:

```php
<?php
/**
 * The journal anonymity gate.
 *
 * Anonymous means hidden from readers AND from the reflection's own author —
 * who is normally staff, and who normally holds moderate_comments through the
 * editor role. So the gate is a capability, never a "is this person staff"
 * check.
 *
 * The mirror comment for an anonymous entry stores no identity at all (see
 * inc/features/journal-sharing.php). Identity is added back here, for holders
 * of reci_view_journal_identity only. A display path that forgets to call this
 * function therefore shows nothing — which is the safe direction to fail in.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Decide the byline for one entry.
 *
 * Pure: every input is an argument, so the whole decision table is testable.
 *
 * @param bool   $is_anonymous            Did the author ask for anonymity?
 * @param bool   $viewer_can_see_identity Does the viewer hold reci_view_journal_identity?
 * @param string $real_name               The author's display name, '' if unknown.
 *
 * @return array{name:string,is_masked:bool}
 */
function reci_journal_display_identity( bool $is_anonymous, bool $viewer_can_see_identity, string $real_name ): array {
	$placeholder = __( 'Anonymous', 'reci-media-hub' );
	$real_name   = trim( $real_name );

	// No name to show — a deleted account, or meta that never existed. Fall
	// back rather than rendering an empty byline.
	if ( '' === $real_name ) {
		return [
			'name'      => $placeholder,
			'is_masked' => true,
		];
	}

	if ( $is_anonymous && ! $viewer_can_see_identity ) {
		return [
			'name'      => $placeholder,
			'is_masked' => true,
		];
	}

	return [
		'name'      => $real_name,
		'is_masked' => false,
	];
}

/**
 * May this user see through anonymity?
 *
 * @param int|null $user_id Defaults to the current user.
 */
function reci_can_see_journal_identity( ?int $user_id = null ): bool {
	if ( null === $user_id ) {
		return current_user_can( 'reci_view_journal_identity' );
	}

	return user_can( $user_id, 'reci_view_journal_identity' );
}

/**
 * Resolve the byline for a journal row, for the current viewer.
 *
 * `user_id` in the return is 0 whenever the byline is masked, so a caller that
 * passes the array straight into a response cannot leak the author by
 * accident.
 *
 * @return array{name:string,is_masked:bool,user_id:int}
 */
function reci_journal_author_for_display( int $journal_id ): array {
	global $wpdb;

	$table = $wpdb->prefix . 'reci_journals';

	$row = $wpdb->get_row(
		$wpdb->prepare( "SELECT user_id, is_anonymous FROM {$table} WHERE id = %d", $journal_id )
	);

	if ( ! $row ) {
		return [
			'name'      => __( 'Anonymous', 'reci-media-hub' ),
			'is_masked' => true,
			'user_id'   => 0,
		];
	}

	$author    = get_userdata( (int) $row->user_id );
	$real_name = $author ? (string) $author->display_name : '';

	$identity = reci_journal_display_identity(
		(bool) (int) $row->is_anonymous,
		reci_can_see_journal_identity(),
		$real_name
	);

	return [
		'name'      => $identity['name'],
		'is_masked' => $identity['is_masked'],
		'user_id'   => $identity['is_masked'] ? 0 : (int) $row->user_id,
	];
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `74 passed, 0 failed`.

- [ ] **Step 5: Register the file**

In `inc/init.php`, add after the `journal-status.php` line:

```php
	'/inc/features/journal-identity.php',
```

- [ ] **Step 6: Add the two capabilities**

In `inc/core/roles.php`, extend `reci_custom_capabilities()` so it returns:

```php
	return [
		// Approve or reject collaborator applications.
		'reci_approve_collaborators',
		// Confirm a registration that has not verified by email.
		'reci_confirm_registrations',
		// Reach the wp-admin side of the site at all.
		'reci_access_admin',
		// Approve, reject and read the shared journal queue.
		'reci_moderate_journals',
		// See the real author behind an entry shared anonymously.
		'reci_view_journal_identity',
	];
```

- [ ] **Step 7: Bump the roles version**

In `inc/core/roles.php`, change:

```php
const RECI_ROLES_VERSION = '1.0.0';
```

to:

```php
const RECI_ROLES_VERSION = '1.1.0';
```

- [ ] **Step 8: Add the Moderator role**

In `inc/core/roles.php`, inside `reci_install_roles()`, immediately **before** the `// ── Levels 1–4 and 6: stock roles, adjusted ──` comment, add:

```php
	// ── Moderator: orthogonal to the ladder ──────────────────────────────
	// Not a rung. A moderator manages discussion, and nothing else: no post
	// editing, no publishing, no user management, no collaborator approvals.
	// They are one of only two roles that may see through anonymity.
	remove_role( 'reci_moderator' );
	add_role(
		'reci_moderator',
		__( 'Moderator', 'reci-media-hub' ),
		[
			'read'                       => true,
			'moderate_comments'          => true,
			'edit_comment'               => true,
			'edit_comments'              => true,
			'reci_access_admin'          => true,
			'reci_moderate_journals'     => true,
			'reci_view_journal_identity' => true,
		]
	);
```

- [ ] **Step 9: Grant the identity capability to administrators only**

Still in `reci_install_roles()`, change the `$grants` array so that `administrator` gains the two new capabilities and `editor` gains only the journal one — **editor must not receive `reci_view_journal_identity`**, because reflection owners are normally editors:

```php
	$grants = [
		'administrator' => [
			'reci_approve_collaborators',
			'reci_confirm_registrations',
			'reci_access_admin',
			'reci_moderate_journals',
			'reci_view_journal_identity',
		],
		// Deliberately no reci_view_journal_identity: an editor is usually the
		// author of the reflection being journalled against, and anonymity has
		// to hold against them.
		'editor'        => [
			'reci_approve_collaborators',
			'reci_confirm_registrations',
			'reci_access_admin',
			'reci_moderate_journals',
		],
	];
```

Note that `reci_site_manager` is built by copying the editor role's capabilities earlier in this same function, so it inherits `reci_moderate_journals` and correctly does **not** inherit `reci_view_journal_identity`.

Run: `php -l inc/core/roles.php && php -l inc/features/journal-identity.php && php -l inc/init.php`
Expected: `No syntax errors detected` for all three.

- [ ] **Step 10: Manual verification**

Roles only reinstall when the version option differs, so force it, then assert the capability matrix:

```bash
wp option delete reci_roles_version
wp eval 'do_action( "init" );'
```

Then check each role. The first two must print `1`, the last four must print nothing or `0`:

```bash
wp cap list reci_moderator | grep -E 'reci_view_journal_identity|moderate_comments|edit_posts'
wp cap list administrator  | grep reci_view_journal_identity
wp cap list editor         | grep reci_view_journal_identity
wp cap list reci_site_manager | grep reci_view_journal_identity
wp cap list author         | grep reci_view_journal_identity
```

Expected:
- `reci_moderator` — has `reci_view_journal_identity` and `moderate_comments`, and **does not** have `edit_posts`.
- `administrator` — has it.
- `editor`, `reci_site_manager`, `author` — **do not** have it.

Record all five outputs. If `editor` or `reci_site_manager` has the capability, stop: anonymity is broken against exactly the person it most needs to hold against.

- [ ] **Step 11: Commit**

```bash
git add inc/features/journal-identity.php tests/test-journal-identity.php inc/core/roles.php inc/init.php
git commit -m "feat: add Moderator role and the journal identity gate

Anonymity has to hold against the reflection's own author, who is
normally an editor and therefore normally holds moderate_comments. So
the gate is a new reci_view_journal_identity capability, granted to
administrators and the new Moderator role only - explicitly not to
editor or reci_site_manager.

The Moderator role sits orthogonal to the six-level ladder: comments and
the journal queue, no post editing, no user management.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 8: Share flow and the mirror comment

Implements spec §4.2 and §5.B, including the defect recorded in spec §2.

**Files:**
- Create: `inc/features/journal-sharing.php`
- Create: `tests/test-journal-sharing.php`
- Modify: `inc/init.php`
- Modify: `inc/features/reflection-responses.php`

**Interfaces:**
- Consumes: `reci_journal_can_transition()` from Task 5; `reci_match_flagged_terms()`, `reci_get_abuse_terms()` from Tasks 3 and 6.
- Produces:
  - `reci_journal_mirror_comment_args( array $journal, string $author_name, int $reflection_id ): array` — pure. Builds the `wp_insert_comment()` argument array.
  - `reci_share_journal( int $journal_id, bool $anonymous ): array|WP_Error`
  - `reci_unshare_journal( int $journal_id ): bool`

- [ ] **Step 1: Write the failing test**

Create `tests/test-journal-sharing.php`:

```php
<?php
/**
 * The mirror comment is where anonymity is actually enforced. For an
 * anonymous entry it must carry no identity at all — not the user id, not the
 * email — because everything downstream (the admin queue, /wp/v2/comments,
 * exports, other plugins) reads those fields directly.
 */

require_once __DIR__ . '/../inc/features/journal-sharing.php';

$journal = [
	'id'           => 42,
	'user_id'      => 7,
	'response'     => 'I froze when they said it.',
	'prompt'       => 'What did you notice?',
	'is_anonymous' => 0,
];

$args = reci_journal_mirror_comment_args( $journal, 'Ada Obi', 99 );

reci_assert_same( 'reci_journal', $args['comment_type'], 'mirror: uses the dedicated comment type' );
reci_assert_same( 99, $args['comment_post_ID'], 'mirror: attaches to the reflection' );
reci_assert_same( 0, $args['comment_approved'], 'mirror: starts unapproved, never self-publishes' );
reci_assert_same( 7, $args['user_id'], 'mirror: named entry keeps the author id' );
reci_assert_same( 'Ada Obi', $args['comment_author'], 'mirror: named entry keeps the display name' );
reci_assert_same( 'I froze when they said it.', $args['comment_content'], 'mirror: carries the response' );
reci_assert_same( 42, $args['comment_meta']['_reci_journal_id'], 'mirror: links back to the journal row' );
reci_assert_same( 0, $args['comment_meta']['_reci_anonymous'], 'mirror: records not-anonymous' );

// The anonymous case. These four assertions are the whole privacy guarantee.
$journal['is_anonymous'] = 1;
$anon = reci_journal_mirror_comment_args( $journal, 'Ada Obi', 99 );

reci_assert_same( 0, $anon['user_id'], 'mirror: ANONYMOUS ENTRY STORES NO USER ID' );
reci_assert_same( 'Anonymous', $anon['comment_author'], 'mirror: anonymous entry stores the placeholder name' );
reci_assert_same( '', $anon['comment_author_email'], 'mirror: anonymous entry stores no email' );
reci_assert_same( '', $anon['comment_author_url'], 'mirror: anonymous entry stores no url' );
reci_assert_same( 1, $anon['comment_meta']['_reci_anonymous'], 'mirror: records anonymous' );

// The real name must not survive anywhere in the argument array, including
// places a future edit might add it without thinking.
reci_assert_same(
	false,
	str_contains( wp_json_encode( $anon ), 'Ada Obi' ),
	'mirror: the real name appears nowhere in an anonymous mirror'
);

// The prompt travels so a moderator can read the entry in context.
reci_assert_same( 'What did you notice?', $anon['comment_meta']['_reci_prompt'], 'mirror: carries the prompt for context' );
```

Add this stub to `tests/bootstrap.php` (the test calls `wp_json_encode`):

```php
if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, int $options = 0, int $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — fatal, `inc/features/journal-sharing.php` does not exist.

- [ ] **Step 3: Write the implementation**

Create `inc/features/journal-sharing.php`:

```php
<?php
/**
 * Sharing a journal entry.
 *
 * The journals table stays the source of truth. Sharing mirrors the entry into
 * wp_comments so WordPress's own moderation queue, threading and
 * moderate_comments capability do the work.
 *
 * For an anonymous entry the mirror carries no identity whatsoever. The link
 * back to the author exists only through the _reci_journal_id meta, and is
 * re-established for holders of reci_view_journal_identity. A display path we
 * failed to think of therefore shows "Anonymous" rather than a real person.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Build the argument array for the mirror comment.
 *
 * Pure, so the anonymity guarantee is testable without a database.
 *
 * @param array  $journal       Row as an array: id, user_id, response, prompt, is_anonymous.
 * @param string $author_name   The author's display name.
 * @param int    $reflection_id The reflection the entry belongs to.
 */
function reci_journal_mirror_comment_args( array $journal, string $author_name, int $reflection_id ): array {
	$is_anonymous = (bool) (int) ( $journal['is_anonymous'] ?? 0 );

	return [
		'comment_post_ID'      => $reflection_id,
		'comment_type'         => 'reci_journal',
		'comment_content'      => (string) ( $journal['response'] ?? '' ),
		// Never 1. Sharing sends an entry for review; only a moderator
		// publishes it.
		'comment_approved'     => 0,
		// The privacy guarantee. An anonymous mirror carries no identity, so
		// nothing downstream can render one by accident.
		'user_id'              => $is_anonymous ? 0 : (int) ( $journal['user_id'] ?? 0 ),
		'comment_author'       => $is_anonymous ? __( 'Anonymous', 'reci-media-hub' ) : $author_name,
		'comment_author_email' => '',
		'comment_author_url'   => '',
		'comment_meta'         => [
			'_reci_journal_id' => (int) ( $journal['id'] ?? 0 ),
			'_reci_anonymous'  => $is_anonymous ? 1 : 0,
			'_reci_prompt'     => (string) ( $journal['prompt'] ?? '' ),
		],
	];
}

/**
 * Fetch one journal row as an array.
 *
 * @return array<string,mixed>|null
 */
function reci_get_journal_row( int $journal_id ): ?array {
	global $wpdb;

	$table = $wpdb->prefix . 'reci_journals';
	$row   = $wpdb->get_row(
		$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $journal_id ),
		ARRAY_A
	);

	return $row ?: null;
}

/**
 * Share an entry: send it for review and create its mirror comment.
 *
 * @return array{status:string,comment_id:int,flagged:array}|WP_Error
 */
function reci_share_journal( int $journal_id, bool $anonymous ) {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal ) {
		return new WP_Error( 'journal_missing', __( 'That entry no longer exists.', 'reci-media-hub' ), [ 'status' => 404 ] );
	}

	$from = (string) $journal['status'];

	if ( ! reci_journal_can_transition( $from, 'pending' ) ) {
		return new WP_Error(
			'journal_bad_transition',
			__( 'That entry cannot be shared from its current state.', 'reci-media-hub' ),
			[ 'status' => 409 ]
		);
	}

	// Matching never blocks the save. It records what a moderator should look
	// at, and nothing more.
	$flagged = reci_match_flagged_terms( (string) $journal['response'], reci_get_abuse_terms() );

	$journal['is_anonymous'] = $anonymous ? 1 : 0;

	$author      = get_userdata( (int) $journal['user_id'] );
	$author_name = $author ? (string) $author->display_name : '';

	$args       = reci_journal_mirror_comment_args( $journal, $author_name, (int) $journal['reflection_id'] );
	$comment_id = wp_insert_comment( wp_slash( $args ) );

	if ( ! $comment_id ) {
		return new WP_Error( 'mirror_failed', __( 'Could not share that entry.', 'reci-media-hub' ), [ 'status' => 500 ] );
	}

	if ( [] !== $flagged ) {
		update_comment_meta( $comment_id, '_reci_flagged_terms', $flagged );
	}

	$wpdb->update(
		$table,
		[
			'status'        => 'pending',
			'is_shared'     => 0,
			'is_anonymous'  => $anonymous ? 1 : 0,
			'shared_at'     => current_time( 'mysql', true ),
			'comment_id'    => $comment_id,
			'flagged_terms' => implode( "\n", $flagged ),
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d', '%d', '%s', '%d', '%s' ],
		[ '%d' ]
	);

	return [
		'status'     => 'pending',
		'comment_id' => (int) $comment_id,
		'flagged'    => $flagged,
	];
}

/**
 * Withdraw an entry: return it to private and remove its mirror.
 */
function reci_unshare_journal( int $journal_id ): bool {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal ) {
		return false;
	}

	if ( ! reci_journal_can_transition( (string) $journal['status'], 'private' ) ) {
		return false;
	}

	$comment_id = (int) $journal['comment_id'];

	if ( $comment_id ) {
		// Force delete rather than trash: a trashed comment is still readable
		// in wp-admin, and the author has just asked for this to stop being
		// visible to other people.
		wp_delete_comment( $comment_id, true );
	}

	$wpdb->update(
		$table,
		[
			'status'     => 'private',
			'is_shared'  => 0,
			'shared_at'  => null,
			'comment_id' => 0,
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d', '%s', '%d' ],
		[ '%d' ]
	);

	return true;
}

/**
 * Keep the journal row consistent if its mirror is deleted from wp-admin.
 *
 * Without this, deleting the comment would leave the entry claiming to be
 * pending or approved forever, with a comment_id pointing at nothing.
 */
add_action( 'deleted_comment', 'reci_journal_handle_deleted_mirror' );
function reci_journal_handle_deleted_mirror( $comment_id ): void {
	global $wpdb;

	$table = $wpdb->prefix . 'reci_journals';

	$wpdb->update(
		$table,
		[
			'status'     => 'private',
			'is_shared'  => 0,
			'shared_at'  => null,
			'comment_id' => 0,
		],
		[ 'comment_id' => (int) $comment_id ],
		[ '%s', '%d', '%s', '%d' ],
		[ '%d' ]
	);
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `89 passed, 0 failed`.

- [ ] **Step 5: Register the file**

In `inc/init.php`, add after the `journal-identity.php` line:

```php
	'/inc/features/journal-sharing.php',
```

- [ ] **Step 6: Fix the create-path defect**

In `inc/features/reflection-responses.php`, inside `reci_create_journal()`, replace these two lines:

```php
		$default_privacy = get_user_meta($user_id, 'reci_journal_default_privacy', true);
		$is_shared       = ($default_privacy === 'public') ? 1 : 0;
```

with:

```php
		// A user whose default privacy is "public" previously had every entry
		// published outright, with no review at all. Entries are now always
		// created private, and a public default routes through the same share
		// path as an explicit share — landing in the moderation queue.
		$default_privacy = get_user_meta($user_id, 'reci_journal_default_privacy', true);
		$share_by_default = ('public' === $default_privacy);
		$is_shared       = 0;
```

Then replace the insert's `'is_shared' => $is_shared,` line with:

```php
				'is_shared'     => 0,
				'status'        => 'private',
```

and add `'%s'` to the format array so it reads:

```php
			['%d', '%d', '%s', '%s', '%d', '%s', '%s']
```

Finally, replace the success return:

```php
		return new WP_REST_Response(['id' => $wpdb->insert_id, 'message' => __('Response saved.', 'reci-media-hub')], 201);
```

with:

```php
		$journal_id = (int) $wpdb->insert_id;

		if ($share_by_default) {
			reci_share_journal($journal_id, false);
		}

		return new WP_REST_Response(
			[
				'id'      => $journal_id,
				'status'  => $share_by_default ? 'pending' : 'private',
				'message' => $share_by_default
					? __('Response saved and sent for review.', 'reci-media-hub')
					: __('Response saved.', 'reci-media-hub'),
			],
			201
		);
```

- [ ] **Step 7: Extend the share route**

In `inc/features/reflection-responses.php`, replace the whole body of `reci_update_journal_share()` with:

```php
	function reci_update_journal_share(WP_REST_Request $request) {
		$journal_id = (int) $request->get_param('id');
		$shared     = (bool) $request->get_param('shared');
		$anonymous  = (bool) $request->get_param('anonymous');

		if (! $shared) {
			$ok = reci_unshare_journal($journal_id);

			return $ok
				? new WP_REST_Response(['shared' => false, 'status' => 'private'], 200)
				: new WP_Error('unshare_failed', __('Could not withdraw that entry.', 'reci-media-hub'), ['status' => 409]);
		}

		$result = reci_share_journal($journal_id, $anonymous);

		if (is_wp_error($result)) {
			return $result;
		}

		return new WP_REST_Response(
			[
				'shared'    => true,
				'status'    => $result['status'],
				'anonymous' => $anonymous,
				'flagged'   => $result['flagged'],
			],
			200
		);
	}
```

- [ ] **Step 8: Expose the new fields on the list route**

In `reci_get_journals()`, extend the `$items[] = [ ... ]` array with:

```php
				'status'        => (string) $row->status,
				'is_anonymous'  => (bool) (int) $row->is_anonymous,
				'flagged_terms' => array_values( array_filter( explode( "\n", (string) $row->flagged_terms ) ) ),
```

Run: `php -l inc/features/journal-sharing.php && php -l inc/features/reflection-responses.php && php -l inc/init.php`
Expected: `No syntax errors detected` for all three.

- [ ] **Step 9: Manual verification**

As a member on the Local site, in a reflection with a prompt chapter:

1. Save a journal entry. Confirm `wp db query "SELECT id,status,is_shared FROM wp_reci_journals ORDER BY id DESC LIMIT 1;"` shows `private` and `0`.
2. Set the user's default to public: `wp user meta update <id> reci_journal_default_privacy public`. Save another entry. It must land **`pending`**, not approved, and not `is_shared = 1`. This is the defect fix — record the row.
3. Share an entry anonymously through the REST route. Then read the mirror:
   `wp db query "SELECT comment_ID,user_id,comment_author,comment_approved,comment_type FROM wp_comments ORDER BY comment_ID DESC LIMIT 1;"`
   Expected: `user_id = 0`, `comment_author = Anonymous`, `comment_approved = 0`, `comment_type = reci_journal`.
4. Withdraw it. The comment must be gone entirely, and the journal row back to `private` with `comment_id = 0`.
5. Share again, then delete the comment from wp-admin. The journal row must return to `private` by itself.

Record the outputs of steps 2, 3 and 5.

- [ ] **Step 10: Commit**

```bash
git add inc/features/journal-sharing.php tests/test-journal-sharing.php inc/init.php inc/features/reflection-responses.php tests/bootstrap.php
git commit -m "feat: add journal share flow with pending review

Sharing mirrors an entry into wp_comments as an unapproved reci_journal
comment. An anonymous mirror carries no identity at all - no user id, no
email - so anything downstream that reads those fields cannot render a
real person by accident.

Fixes the defect where a user whose default privacy was 'public' had
every entry published outright with no review; those now route through
the same pending queue as an explicit share.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 9: Display and REST hardening

Implements spec §5.D's REST notes and the "side door" defence in §5.C.

**Files:**
- Create: `tests/test-journal-rest-shaping.php`
- Modify: `inc/features/journal-identity.php`

**Interfaces:**
- Consumes: `reci_journal_display_identity()` from Task 7.
- Produces: `reci_strip_comment_identity( array $data ): array` — pure. Removes every author field from a prepared comment payload.

- [ ] **Step 1: Write the failing test**

Create `tests/test-journal-rest-shaping.php`:

```php
<?php
/**
 * /wp/v2/comments is a side door: it would serve the mirror comments straight
 * out of core with their author fields attached. Stripping is a pure function
 * so the field list is explicit and reviewable.
 */

require_once __DIR__ . '/../inc/features/journal-identity.php';

$payload = [
	'id'           => 5,
	'post'         => 99,
	'content'      => [ 'rendered' => 'I froze.' ],
	'author'       => 7,
	'author_name'  => 'Ada Obi',
	'author_url'   => 'https://example.com',
	'author_email' => 'ada@example.com',
	'author_ip'    => '203.0.113.9',
	'author_avatar_urls' => [ '96' => 'https://example.com/a.png' ],
	'meta'         => [ 'anything' => 1 ],
];

$clean = reci_strip_comment_identity( $payload );

reci_assert_same( 0, $clean['author'], 'strip: author id zeroed' );
reci_assert_same( 'Anonymous', $clean['author_name'], 'strip: author name replaced' );
reci_assert_same( '', $clean['author_url'], 'strip: author url cleared' );
reci_assert_same( false, isset( $clean['author_email'] ), 'strip: author email removed entirely' );
reci_assert_same( false, isset( $clean['author_ip'] ), 'strip: author ip removed entirely' );
reci_assert_same( [], $clean['author_avatar_urls'], 'strip: avatars cleared, since an avatar identifies a person' );

// The entry itself must survive: this hides who wrote it, not what they wrote.
reci_assert_same( 'I froze.', $clean['content']['rendered'], 'strip: the writing is preserved' );
reci_assert_same( 5, $clean['id'], 'strip: the id is preserved' );
reci_assert_same( 99, $clean['post'], 'strip: the reflection link is preserved' );

// A payload missing some fields must not fatal.
$sparse = reci_strip_comment_identity( [ 'id' => 1 ] );
reci_assert_same( 1, $sparse['id'], 'strip: sparse payload survives' );
reci_assert_same( 0, $sparse['author'], 'strip: sparse payload still gets a zeroed author' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — `Call to undefined function reci_strip_comment_identity()`.

- [ ] **Step 3: Write the implementation**

Append to `inc/features/journal-identity.php`:

```php
/**
 * Remove every author-identifying field from a prepared comment payload.
 *
 * Pure, so the field list is explicit and can be reviewed against what the
 * REST controller actually emits.
 */
function reci_strip_comment_identity( array $data ): array {
	$data['author']             = 0;
	$data['author_name']        = __( 'Anonymous', 'reci-media-hub' );
	$data['author_url']         = '';
	$data['author_avatar_urls'] = [];

	// These two have no anonymised form — an email or an IP address is
	// identifying however it is rendered — so they are dropped outright.
	unset( $data['author_email'], $data['author_ip'] );

	return $data;
}

/**
 * Close the /wp/v2/comments side door.
 *
 * Without this, core would serve a mirror comment's author fields directly,
 * bypassing every masking decision made elsewhere.
 */
add_filter( 'rest_prepare_comment', 'reci_rest_mask_journal_comment', 10, 3 );
function reci_rest_mask_journal_comment( $response, $comment, $request ) {
	if ( 'reci_journal' !== $comment->comment_type ) {
		return $response;
	}

	if ( (int) get_comment_meta( $comment->comment_ID, '_reci_anonymous', true ) !== 1 ) {
		return $response;
	}

	if ( reci_can_see_journal_identity() ) {
		return $response;
	}

	$response->set_data( reci_strip_comment_identity( (array) $response->get_data() ) );

	return $response;
}

/**
 * Mask the byline wherever WordPress renders a comment author.
 *
 * The mirror stores no identity for an anonymous entry, so this is a second
 * line of defence rather than the primary one — it matters for the cases where
 * something has reconstructed a name from elsewhere.
 */
add_filter( 'get_comment_author', 'reci_mask_journal_comment_author', 10, 3 );
function reci_mask_journal_comment_author( $author, $comment_id, $comment ) {
	if ( ! $comment || 'reci_journal' !== $comment->comment_type ) {
		return $author;
	}

	if ( (int) get_comment_meta( $comment_id, '_reci_anonymous', true ) !== 1 ) {
		return $author;
	}

	if ( reci_can_see_journal_identity() ) {
		return $author;
	}

	return __( 'Anonymous', 'reci-media-hub' );
}

/**
 * An anonymous entry must not carry a profile link.
 */
add_filter( 'get_comment_author_url', 'reci_mask_journal_comment_author_url', 10, 3 );
function reci_mask_journal_comment_author_url( $url, $comment_id, $comment ) {
	if ( ! $comment || 'reci_journal' !== $comment->comment_type ) {
		return $url;
	}

	if ( (int) get_comment_meta( $comment_id, '_reci_anonymous', true ) !== 1 ) {
		return $url;
	}

	return reci_can_see_journal_identity() ? $url : '';
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `100 passed, 0 failed`.

- [ ] **Step 5: Manual verification**

With at least one approved anonymous shared entry in place (share one, then approve it from wp-admin):

1. **Signed out**, request the comment through core's route:
   `curl -s "http://<local-site>/wp-json/wp/v2/comments?type=reci_journal" | python3 -m json.tool`
   Expected: `author` is `0`, `author_name` is `Anonymous`, and **no** `author_email` or `author_ip` key appears anywhere.
2. Repeat **signed in as an `editor`**. Same result — an editor must not see through anonymity.
3. Repeat **signed in as a `reci_moderator`**. The real author fields are now present.
4. In wp-admin's Comments screen as an `editor`, confirm the entry's author column reads `Anonymous` with no user profile link.

Record the `author` and `author_name` values observed in steps 1–3.

- [ ] **Step 6: Commit**

```bash
git add inc/features/journal-identity.php tests/test-journal-rest-shaping.php
git commit -m "fix: close the REST side door on anonymous journal entries

/wp/v2/comments would have served mirror comments with their author
fields attached, bypassing every masking decision made elsewhere.
Anonymous entries are now stripped for anyone without
reci_view_journal_identity, with author_email and author_ip dropped
outright since neither has an anonymised form.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 10: Moderation transitions and notifications

Implements spec §5.C (the queue and notification half) and §3.3.

**Files:**
- Create: `inc/features/journal-moderation.php`
- Create: `tests/test-journal-notifications.php`
- Modify: `inc/init.php`
- Modify: `inc/admin/class-reci-journals-list-table.php`

**Interfaces:**
- Consumes: `reci_journal_can_transition()` (Task 5), `reci_journal_display_identity()` (Task 7), `reci_create_notification( int $user_id, string $type, string $title, string $message, string $target_url = '', int $related_post_id = 0 ): int` from `inc/features/notifications.php:18`.
- Produces:
  - `reci_journal_owner_notification_text( bool $is_anonymous, string $author_name, string $reflection_title ): array` — pure. Returns `[ 'title' => string, 'message' => string ]`.
  - `reci_approve_journal( int $journal_id ): bool`
  - `reci_reject_journal( int $journal_id ): bool`

- [ ] **Step 1: Write the failing test**

Create `tests/test-journal-notifications.php`:

```php
<?php
/**
 * Notification rows are STORED text, and the reflection owner reads those
 * rows. Masking at render time would be too late — the name must never be
 * written in the first place.
 */

require_once __DIR__ . '/../inc/features/journal-moderation.php';

// Named entry: the owner may see who wrote it.
$named = reci_journal_owner_notification_text( false, 'Ada Obi', 'We Humans' );
reci_assert_same( true, str_contains( $named['message'], 'Ada Obi' ), 'notify: named entry names the author' );
reci_assert_same( true, str_contains( $named['message'], 'We Humans' ), 'notify: names the reflection' );

// Anonymous entry: the owner must NOT see who wrote it. This is the assertion
// that enforces "hidden from the reflection owner".
$anon = reci_journal_owner_notification_text( true, 'Ada Obi', 'We Humans' );
reci_assert_same( false, str_contains( $anon['message'], 'Ada Obi' ), 'notify: ANONYMOUS ENTRY NEVER STORES THE NAME' );
reci_assert_same( false, str_contains( $anon['title'], 'Ada Obi' ), 'notify: the title never stores the name either' );
reci_assert_same( true, str_contains( $anon['message'], 'Anonymous' ), 'notify: anonymous entry says so' );
reci_assert_same( true, str_contains( $anon['message'], 'We Humans' ), 'notify: still names the reflection' );

// A deleted author must not produce an empty byline mid-sentence.
$gone = reci_journal_owner_notification_text( false, '', 'We Humans' );
reci_assert_same( true, str_contains( $gone['message'], 'Anonymous' ), 'notify: missing name falls back to the placeholder' );

// Titles stay short enough for the notification list.
reci_assert_same( true, strlen( $anon['title'] ) <= 255, 'notify: title fits the column' );
reci_assert_same( true, strlen( $named['title'] ) <= 255, 'notify: named title fits the column' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — fatal, `inc/features/journal-moderation.php` does not exist.

- [ ] **Step 3: Write the implementation**

Create `inc/features/journal-moderation.php`:

```php
<?php
/**
 * Moderating shared journal entries.
 *
 * Approving the mirror comment is what publishes an entry. The reflection's
 * owner is notified then, and not on share: the moderation queue absorbs the
 * unreviewed text and the noise of entries that are later rejected.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) && ! function_exists( 'reci_assert' ) ) {
	exit;
}

/**
 * Build the owner's notification copy.
 *
 * Pure, and deliberately so: reci_create_notification() writes this text into
 * wp_reci_notifications, where the reflection owner reads it. For an anonymous
 * entry the author's name must never enter that row at all — masking it at
 * render time would already be too late.
 *
 * @return array{title:string,message:string}
 */
function reci_journal_owner_notification_text( bool $is_anonymous, string $author_name, string $reflection_title ): array {
	$identity = reci_journal_display_identity( $is_anonymous, false, $author_name );
	$who      = $identity['name'];

	return [
		'title'   => __( 'New shared reflection', 'reci-media-hub' ),
		'message' => sprintf(
			/* translators: 1: author name or "Anonymous", 2: reflection title */
			__( '%1$s shared a reflection on "%2$s".', 'reci-media-hub' ),
			$who,
			$reflection_title
		),
	];
}

/**
 * Publish an entry and tell the reflection's owner.
 */
function reci_approve_journal( int $journal_id ): bool {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal || ! reci_journal_can_transition( (string) $journal['status'], 'approved' ) ) {
		return false;
	}

	$wpdb->update(
		$table,
		[
			'status'    => 'approved',
			'is_shared' => 1,
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d' ],
		[ '%d' ]
	);

	reci_clear_shared_journal_count( (int) $journal['reflection_id'] );

	$reflection = get_post( (int) $journal['reflection_id'] );

	if ( ! $reflection ) {
		return true;
	}

	$author      = get_userdata( (int) $journal['user_id'] );
	$author_name = $author ? (string) $author->display_name : '';

	$copy = reci_journal_owner_notification_text(
		(bool) (int) $journal['is_anonymous'],
		$author_name,
		(string) $reflection->post_title
	);

	reci_create_notification(
		(int) $reflection->post_author,
		'shared_journal_approved',
		$copy['title'],
		$copy['message'],
		get_permalink( $reflection ),
		(int) $reflection->ID
	);

	return true;
}

/**
 * Reject an entry and tell its author. The reflection owner is not told.
 */
function reci_reject_journal( int $journal_id ): bool {
	global $wpdb;

	$table   = $wpdb->prefix . 'reci_journals';
	$journal = reci_get_journal_row( $journal_id );

	if ( ! $journal || ! reci_journal_can_transition( (string) $journal['status'], 'rejected' ) ) {
		return false;
	}

	$wpdb->update(
		$table,
		[
			'status'    => 'rejected',
			'is_shared' => 0,
		],
		[ 'id' => $journal_id ],
		[ '%s', '%d' ],
		[ '%d' ]
	);

	reci_clear_shared_journal_count( (int) $journal['reflection_id'] );

	reci_create_notification(
		(int) $journal['user_id'],
		'shared_journal_rejected',
		__( 'Your shared reflection was not published', 'reci-media-hub' ),
		__( 'A moderator reviewed your shared reflection and did not publish it. It is still in your journal, and still yours.', 'reci-media-hub' ),
		home_url( '/dashboard/journal/' ),
		0
	);

	return true;
}

/**
 * The cached count of approved entries for one reflection.
 */
function reci_get_shared_journal_count( int $reflection_id ): int {
	$cache_key = 'reci_shared_journals_' . $reflection_id;
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return (int) $cached;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'reci_journals';

	$count = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(id) FROM {$table} WHERE reflection_id = %d AND status = 'approved'",
			$reflection_id
		)
	);

	set_transient( $cache_key, $count, HOUR_IN_SECONDS );

	return $count;
}

/**
 * Bust the count cache. Called on every status change.
 */
function reci_clear_shared_journal_count( int $reflection_id ): void {
	delete_transient( 'reci_shared_journals_' . $reflection_id );
}

/**
 * Approving the mirror comment is what publishes the entry.
 *
 * Moderators work in the native comment queue, so the journal row follows the
 * comment rather than the other way around.
 */
add_action( 'transition_comment_status', 'reci_journal_follow_comment_status', 10, 3 );
function reci_journal_follow_comment_status( $new_status, $old_status, $comment ): void {
	if ( 'reci_journal' !== $comment->comment_type ) {
		return;
	}

	$journal_id = (int) get_comment_meta( $comment->comment_ID, '_reci_journal_id', true );

	if ( ! $journal_id ) {
		return;
	}

	if ( 'approved' === $new_status ) {
		reci_approve_journal( $journal_id );
		return;
	}

	// Spam, trash and unapproved all mean "not published".
	if ( 'approved' === $old_status ) {
		reci_reject_journal( $journal_id );
	}
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `109 passed, 0 failed`.

- [ ] **Step 5: Register the file**

In `inc/init.php`, add after the `journal-sharing.php` line:

```php
	'/inc/features/journal-moderation.php',
```

It must come after `notifications.php` in the array, which it already does.

- [ ] **Step 6: Add status and flag columns to the journals table**

In `inc/admin/class-reci-journals-list-table.php`, find the `get_columns()` method and add two entries to the returned array:

```php
			'status'        => __( 'Status', 'reci-media-hub' ),
			'flagged_terms' => __( 'Flagged', 'reci-media-hub' ),
```

Then add these two column renderers as methods on the class:

```php
	/**
	 * Render the status column.
	 */
	public function column_status( $item ): string {
		$labels = [
			'private'  => __( 'Private', 'reci-media-hub' ),
			'pending'  => __( 'Pending review', 'reci-media-hub' ),
			'approved' => __( 'Published', 'reci-media-hub' ),
			'rejected' => __( 'Not published', 'reci-media-hub' ),
		];

		$status = (string) ( $item->status ?? 'private' );
		$label  = $labels[ $status ] ?? $status;

		$anonymous = (int) ( $item->is_anonymous ?? 0 )
			? ' <em>' . esc_html__( '(anonymous)', 'reci-media-hub' ) . '</em>'
			: '';

		return esc_html( $label ) . $anonymous;
	}

	/**
	 * Render the flagged-term column, so a moderator sees why an entry
	 * surfaced rather than having to guess.
	 */
	public function column_flagged_terms( $item ): string {
		$terms = array_values( array_filter( explode( "\n", (string) ( $item->flagged_terms ?? '' ) ) ) );

		if ( [] === $terms ) {
			return '—';
		}

		$badges = array_map(
			static function ( string $term ): string {
				return '<span class="reci-flag-badge">' . esc_html( $term ) . '</span>';
			},
			$terms
		);

		return implode( ' ', $badges );
	}
```

Run: `php -l inc/features/journal-moderation.php && php -l inc/admin/class-reci-journals-list-table.php && php -l inc/init.php`
Expected: `No syntax errors detected` for all three.

- [ ] **Step 7: Manual verification**

1. Share an entry **non-anonymously** against a reflection whose `post_author` is a different user. Approve the mirror comment in wp-admin.
   - Confirm the journal row is now `approved`.
   - Confirm the owner has a notification: `wp db query "SELECT user_id,type,message FROM wp_reci_notifications ORDER BY id DESC LIMIT 1;"` — the message should name the author and the reflection.
2. Share an entry **anonymously**, approve it, and read the notification row again.
   - The stored `message` must contain `Anonymous` and **must not** contain the author's display name. Paste the actual row into your report.
3. Unapprove the comment. The journal row must return to `rejected`, and the author (not the owner) gets a notification.
4. Confirm the owner is **not** notified on share — only on approval. Share a third entry and check no new row appears for the owner until you approve it.

- [ ] **Step 8: Commit**

```bash
git add inc/features/journal-moderation.php tests/test-journal-notifications.php inc/init.php inc/admin/class-reci-journals-list-table.php
git commit -m "feat: wire journal moderation to the comment queue

Approving the mirror comment publishes the entry and notifies the
reflection's owner; rejecting notifies the author instead. Owners are
told on approval rather than on share, so unreviewed text never reaches
their inbox.

Notification copy is built by a pure function that never writes an
anonymous author's name into the stored row - the owner reads those
rows directly, so masking at render time would be too late.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 11: Shared entries endpoint and overlay

Implements spec §5.D.

**Files:**
- Create: `modules/reflection-system/templates/shared-journals-overlay.php`
- Create: `tests/test-shared-journals-payload.php`
- Modify: `inc/features/journal-sharing.php`
- Modify: `modules/reflection-system/templates/response-block.php`

**Interfaces:**
- Consumes: `reci_get_shared_journal_count()` (Task 10), `reci_journal_display_identity()` (Task 7).
- Produces:
  - `reci_shape_shared_journal( object $row, string $author_name ): array` — pure. The public payload for one approved entry.
  - REST route `GET /reci/v1/reflections/(?P<id>\d+)/shared-journals`.

- [ ] **Step 1: Write the failing test**

Create `tests/test-shared-journals-payload.php`:

```php
<?php
/**
 * This endpoint is a PUBLIC reading surface. It must never carry identity for
 * an anonymous entry — not even for a moderator, who has the admin queue for
 * that. Shaping is pure so the emitted field list is explicit.
 */

require_once __DIR__ . '/../inc/features/journal-sharing.php';

$row = (object) [
	'id'            => 3,
	'user_id'       => 7,
	'response'      => 'I froze when they said it.',
	'prompt'        => 'What did you notice?',
	'is_anonymous'  => 0,
	'created_at'    => '2026-09-01 10:00:00',
	'reflection_id' => 99,
	'flagged_terms' => 'badword',
];

$named = reci_shape_shared_journal( $row, 'Ada Obi' );

reci_assert_same( 3, $named['id'], 'payload: carries the entry id' );
reci_assert_same( 'Ada Obi', $named['author_name'], 'payload: named entry shows the name' );
reci_assert_same( 'I froze when they said it.', $named['response'], 'payload: carries the writing' );
reci_assert_same( 'What did you notice?', $named['prompt'], 'payload: carries the prompt' );
reci_assert_same( false, isset( $named['user_id'] ), 'payload: never carries a user id, even when named' );
reci_assert_same( false, isset( $named['flagged_terms'] ), 'payload: never carries moderation metadata' );

$row->is_anonymous = 1;
$anon = reci_shape_shared_journal( $row, 'Ada Obi' );

reci_assert_same( 'Anonymous', $anon['author_name'], 'payload: anonymous entry is masked' );
reci_assert_same( true, $anon['is_anonymous'], 'payload: flags itself anonymous so the UI can label it' );
reci_assert_same(
	false,
	str_contains( wp_json_encode( $anon ), 'Ada Obi' ),
	'payload: THE REAL NAME APPEARS NOWHERE IN AN ANONYMOUS PAYLOAD'
);
reci_assert_same( 'I froze when they said it.', $anon['response'], 'payload: anonymous entry still carries the writing' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — `Call to undefined function reci_shape_shared_journal()`.

- [ ] **Step 3: Write the shaper and the route**

Append to `inc/features/journal-sharing.php`:

```php
/**
 * Shape one approved entry for the public reading surface.
 *
 * Pure. Note what is absent: no user id and no moderation metadata, for any
 * caller at any capability. A moderator who needs identity uses the admin
 * queue; this endpoint is what unauthenticated readers receive, and it should
 * not vary by who is asking.
 */
function reci_shape_shared_journal( object $row, string $author_name ): array {
	$is_anonymous = (bool) (int) $row->is_anonymous;

	$identity = reci_journal_display_identity( $is_anonymous, false, $author_name );

	return [
		'id'           => (int) $row->id,
		'author_name'  => $identity['name'],
		'is_anonymous' => $is_anonymous,
		'prompt'       => (string) $row->prompt,
		'response'     => (string) $row->response,
		'created_at'   => gmdate( 'Y-m-d\TH:i:sP', strtotime( (string) $row->created_at ) ),
	];
}

add_action( 'rest_api_init', 'reci_register_shared_journal_routes' );
function reci_register_shared_journal_routes(): void {
	register_rest_route(
		'reci/v1',
		'/reflections/(?P<id>\d+)/shared-journals',
		[
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'reci_get_shared_journals',
			// Approved entries are published writing, readable by anyone who
			// can read the reflection itself.
			'permission_callback' => '__return_true',
		]
	);
}

/**
 * List the approved shared entries for one reflection.
 */
function reci_get_shared_journals( WP_REST_Request $request ): WP_REST_Response {
	global $wpdb;

	$reflection_id = absint( (string) $request->get_param( 'id' ) );
	$page          = max( 1, absint( (string) $request->get_param( 'page' ) ?: '1' ) );
	$per_page      = min( 50, max( 1, absint( (string) $request->get_param( 'per_page' ) ?: '20' ) ) );
	$offset        = ( $page - 1 ) * $per_page;

	$table = $wpdb->prefix . 'reci_journals';

	$total = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(id) FROM {$table} WHERE reflection_id = %d AND status = 'approved'",
			$reflection_id
		)
	);

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table}
			  WHERE reflection_id = %d AND status = 'approved'
			  ORDER BY shared_at DESC, id DESC
			  LIMIT %d OFFSET %d",
			$reflection_id,
			$per_page,
			$offset
		)
	);

	$items = [];

	foreach ( $rows as $row ) {
		$author      = get_userdata( (int) $row->user_id );
		$author_name = $author ? (string) $author->display_name : '';

		$items[] = reci_shape_shared_journal( $row, $author_name );
	}

	return new WP_REST_Response(
		[
			'items'       => $items,
			'total'       => $total,
			'page'        => $page,
			'per_page'    => $per_page,
			'total_pages' => (int) ceil( $total / $per_page ),
		],
		200
	);
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `119 passed, 0 failed`.

- [ ] **Step 5: Create the overlay template**

Create `modules/reflection-system/templates/shared-journals-overlay.php`:

```php
<?php
/**
 * Shared journal entries overlay.
 *
 * Pooled per reflection: one list for the whole piece, not one per prompt.
 * Rendered collapsed and populated over REST on first open, so a reflection
 * with hundreds of entries does not pay for them on page load.
 *
 * @package reci-media-hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$overlay = wp_parse_args(
	$args ?? [],
	[
		'reflection_id' => 0,
		'count'         => 0,
	]
);

if ( $overlay['count'] < 1 ) {
	return;
}
?>
<div
	id="reci-shared-journals"
	class="fixed inset-0 z-50 hidden overflow-y-auto"
	style="background: var(--reflection-overlay);"
	data-reflection-id="<?php echo esc_attr( (string) $overlay['reflection_id'] ); ?>"
	role="dialog"
	aria-modal="true"
	aria-labelledby="reci-shared-journals-title"
>
	<div class="mx-auto w-full max-w-[900px] px-5 py-16 sm:px-6">
		<div class="flex items-start justify-between gap-6">
			<h2 id="reci-shared-journals-title" class="font-['Playfair_Display'] text-4xl font-semibold reci-reflection-text">
				<?php esc_html_e( 'Voices from this reflection', 'reci-media-hub' ); ?>
			</h2>
			<button
				type="button"
				id="reci-shared-journals-close"
				class="rounded-full border border-[color:var(--reflection-border)] px-5 py-2 font-['Oswald'] text-sm uppercase tracking-[0.1em] reci-reflection-text"
			>
				<?php esc_html_e( 'Close', 'reci-media-hub' ); ?>
			</button>
		</div>

		<p class="mt-3 text-base leading-8 reci-reflection-soft-text">
			<?php esc_html_e( 'Reflections other people chose to share. Some are anonymous.', 'reci-media-hub' ); ?>
		</p>

		<div id="reci-shared-journals-list" class="mt-8 grid gap-4"></div>

		<div id="reci-shared-journals-status" class="mt-6 text-sm reci-reflection-soft-text"></div>
	</div>
</div>
```

Run: `php -l modules/reflection-system/templates/shared-journals-overlay.php`
Expected: `No syntax errors detected`.

- [ ] **Step 6: Add the button to the response block**

In `modules/reflection-system/templates/response-block.php`, find the closing of the "Your saved responses" panel — the block ending:

```php
				<div id="responseList" class="mt-4 grid gap-4"></div>
			</div>
```

Replace it with:

```php
				<div id="responseList" class="mt-4 grid gap-4"></div>

				<?php
				$reci_reflection_id = get_the_ID();
				$reci_shared_count  = function_exists( 'reci_get_shared_journal_count' )
					? reci_get_shared_journal_count( (int) $reci_reflection_id )
					: 0;
				?>
				<?php if ( $reci_shared_count > 0 ) : ?>
					<button
						type="button"
						id="reci-open-shared-journals"
						class="mt-6 inline-flex items-center justify-center rounded-full border border-[color:var(--reflection-border)] px-6 py-3 font-['Oswald'] text-sm uppercase tracking-[0.1em] reci-reflection-text"
					>
						<?php
						printf(
							/* translators: %d: number of shared reflections */
							esc_html( _n( 'Read %d shared reflection', 'Read %d shared reflections', $reci_shared_count, 'reci-media-hub' ) ),
							(int) $reci_shared_count
						);
						?>
					</button>
				<?php endif; ?>
			</div>
```

**Important:** a reflection may contain more than one `reflection-prompt` chapter. Render the overlay itself **once only**, guarded by a static flag, so multiple prompt chapters do not emit duplicate `id="reci-shared-journals"` elements. Add this immediately after the closing `</section>` at the end of `response-block.php`:

```php
<?php
// The overlay is pooled per reflection, so it is emitted once however many
// prompt chapters the reflection contains. A duplicate id would break the
// close button on every copy after the first.
static $reci_shared_overlay_rendered = false;

if ( ! $reci_shared_overlay_rendered && ! empty( $reci_shared_count ) ) {
	$reci_shared_overlay_rendered = true;

	get_template_part(
		'modules/reflection-system/templates/shared-journals-overlay',
		null,
		[
			'reflection_id' => (int) $reci_reflection_id,
			'count'         => (int) $reci_shared_count,
		]
	);
}
?>
```

Run: `php -l modules/reflection-system/templates/response-block.php`
Expected: `No syntax errors detected`.

- [ ] **Step 7: Manual verification**

1. With **zero** approved entries on a reflection, open it. No button may appear, and `#reci-shared-journals` must not be in the DOM.
2. Approve one entry. Reload. The button appears reading `Read 1 shared reflection` (singular).
3. Approve a second. Reload. It reads `Read 2 shared reflections` (plural). If the count is stale, the transient was not busted — check `reci_clear_shared_journal_count()` is being called.
4. Hit the endpoint signed out:
   `curl -s "http://<local-site>/wp-json/reci/v1/reflections/<id>/shared-journals" | python3 -m json.tool`
   Confirm: no `user_id` key on any item, and an anonymous item's `author_name` is `Anonymous`.
5. Hit the same endpoint **as a `reci_moderator`**. The response must be identical — this surface does not vary by capability.

Record the output of steps 4 and 5.

- [ ] **Step 8: Commit**

```bash
git add inc/features/journal-sharing.php tests/test-shared-journals-payload.php modules/reflection-system/templates/shared-journals-overlay.php modules/reflection-system/templates/response-block.php
git commit -m "feat: surface shared reflections per reflection

Adds GET /reci/v1/reflections/{id}/shared-journals and an overlay opened
from the prompt chapter, hidden when the count is zero. The payload
carries no user id and no moderation metadata for any caller at any
capability - a moderator who needs identity uses the admin queue.

The overlay is emitted once per page however many prompt chapters a
reflection contains, so the ids stay unique.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 12: Typing-time warning and comment flagging

Implements the rest of spec §5.G.

**Files:**
- Create: `assets/js/community-policy.js`
- Create: `tests/test-flag-routing.php`
- Modify: `inc/features/community-policy.php`
- Modify: `inc/core/theme-setup.php`
- Modify: `templates/single/single-post.php`

**Interfaces:**
- Consumes: `reci_match_flagged_terms()`, `reci_normalize_for_matching()`, `reci_accent_fold_map()`, `reci_get_abuse_terms()`, `reci_get_community_policy()` (Tasks 3 and 6).
- Produces: `reci_should_flag_for_review( string $surface, array $matches ): bool` — pure.

- [ ] **Step 1: Write the failing test**

Create `tests/test-flag-routing.php`:

```php
<?php
/**
 * Where a match routes is the privacy-critical half of the policy feature.
 * A private journal entry is the writer's own space: it may warn them, but it
 * must never put their writing in front of a moderator.
 */

require_once __DIR__ . '/../inc/features/community-policy.php';

$matches = [ 'badword' ];

// The rule that matters most.
reci_assert_same( false, reci_should_flag_for_review( 'private_journal', $matches ), 'routing: A PRIVATE JOURNAL IS NEVER FLAGGED' );

// Surfaces that publish to other people do route for review.
reci_assert_same( true, reci_should_flag_for_review( 'shared_journal', $matches ), 'routing: a shared journal is flagged' );
reci_assert_same( true, reci_should_flag_for_review( 'comment', $matches ), 'routing: a comment is flagged' );

// No match, nothing to route, on any surface.
reci_assert_same( false, reci_should_flag_for_review( 'shared_journal', [] ), 'routing: no match means no flag' );
reci_assert_same( false, reci_should_flag_for_review( 'comment', [] ), 'routing: no match means no flag on comments' );
reci_assert_same( false, reci_should_flag_for_review( 'private_journal', [] ), 'routing: no match means no flag when private' );

// An unrecognised surface must fail closed — do not flag writing we cannot
// classify, because flagging is what exposes it to a stranger.
reci_assert_same( false, reci_should_flag_for_review( 'something_new', $matches ), 'routing: unknown surface fails closed' );
reci_assert_same( false, reci_should_flag_for_review( '', $matches ), 'routing: empty surface fails closed' );
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php tests/run.php`
Expected: FAIL — `Call to undefined function reci_should_flag_for_review()`.

- [ ] **Step 3: Write the routing rule**

Append to `inc/features/community-policy.php`:

```php
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
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php tests/run.php`
Expected: `127 passed, 0 failed`.

- [ ] **Step 5: Write the browser-side warning**

Create `assets/js/community-policy.js`:

```js
/**
 * Typing-time community guideline warning.
 *
 * Mirrors the PHP normaliser in inc/features/community-policy.php. If the two
 * drift, the warning a writer sees stops predicting what the server flags, so
 * any change to one belongs in both.
 *
 * This only ever warns. It never disables a control and never blocks a save.
 */
( function () {
	'use strict';

	var data = window.reciCommunityPolicy || {};
	var terms = data.terms || [];
	var accentMap = data.accentMap || {};

	if ( ! terms.length ) {
		return;
	}

	function normalize( text ) {
		var out = String( text ).toLowerCase();

		out = out.replace( /./g, function ( ch ) {
			return Object.prototype.hasOwnProperty.call( accentMap, ch ) ? accentMap[ ch ] : ch;
		} );

		// Digits only, matching the PHP map. Mapping punctuation to letters
		// would corrupt ordinary punctuation.
		out = out.replace( /[43105 7]/g, function ( ch ) {
			var digits = { '4': 'a', '3': 'e', '1': 'i', '0': 'o', '5': 's', '7': 't' };
			return Object.prototype.hasOwnProperty.call( digits, ch ) ? digits[ ch ] : ch;
		} );

		out = out.replace( /[^a-z0-9\s]+/g, '' );
		out = out.replace( /\s+/g, ' ' );

		return out.trim();
	}

	function matches( text ) {
		var haystack = normalize( text );

		if ( ! haystack ) {
			return [];
		}

		return terms.filter( function ( term ) {
			var needle = normalize( term );

			if ( ! needle ) {
				return false;
			}

			var pattern = new RegExp(
				'(?:^|[^a-z0-9])' + needle.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) + '(?:[^a-z0-9]|$)'
			);

			return pattern.test( haystack );
		} );
	}

	function buildNotice() {
		var notice = document.createElement( 'div' );
		notice.className = 'reci-policy-notice';
		notice.hidden = true;
		notice.setAttribute( 'role', 'status' );
		notice.textContent = data.warning || '';
		return notice;
	}

	function buildLink() {
		var link = document.createElement( 'a' );
		link.className = 'reci-policy-link';
		link.href = data.policyUrl || '#';
		link.textContent = data.linkLabel || 'Community guideline';
		return link;
	}

	function attach( textarea ) {
		if ( textarea.dataset.reciPolicyBound ) {
			return;
		}
		textarea.dataset.reciPolicyBound = '1';

		var notice = buildNotice();
		var link = buildLink();

		textarea.insertAdjacentElement( 'afterend', notice );
		textarea.insertAdjacentElement( 'afterend', link );

		var timer = null;

		textarea.addEventListener( 'input', function () {
			window.clearTimeout( timer );

			timer = window.setTimeout( function () {
				notice.hidden = matches( textarea.value ).length === 0;
			}, 250 );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		[ '#reflectionResponse', '#comment' ].forEach( function ( selector ) {
			document.querySelectorAll( selector ).forEach( attach );
		} );
	} );
}() );
```

- [ ] **Step 6: Enqueue it**

In `inc/core/theme-setup.php`, inside the function hooked to `wp_enqueue_scripts`, add:

```php
	wp_enqueue_script(
		'reci-community-policy',
		get_template_directory_uri() . '/assets/js/community-policy.js',
		[],
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script( 'reci-community-policy', 'reciCommunityPolicy', reci_community_policy_script_data() );
```

- [ ] **Step 7: Add the guideline link beside the comment form**

In `templates/single/single-post.php`, immediately before the `comment_form([` call at line 248, add:

```php
						<?php
						get_template_part(
							'template-parts/common/guidelines-panel',
							null,
							[
								'title'       => __( 'Community guideline', 'reci-media-hub' ),
								'body'        => reci_get_community_policy(),
								'collapsible' => true,
							]
						);
						?>
```

Run: `php -l inc/features/community-policy.php && php -l inc/core/theme-setup.php && php -l templates/single/single-post.php && node --check assets/js/community-policy.js`
Expected: `No syntax errors detected` for the three PHP files, and no output from `node --check`.

- [ ] **Step 8: Manual verification**

With `badword` configured in Flagged Terms:

1. On a post, type `this is a badword` into the comment box. The warning appears under the textarea; the Submit button stays **enabled**.
2. Submit it. The comment saves and is held: `wp comment list --status=hold --format=table` shows it. Confirm `wp comment meta get <id> _reci_flagged_terms` lists `badword`.
3. Submit a clean comment. It is **not** held — confirm the previous behaviour is unchanged for text that does not match.
4. In a reflection, type `badword` into the journal textarea. The warning appears. Save it **without** sharing.
   - Confirm the entry is `private` and **no** moderator-visible record exists: `wp db query "SELECT status,flagged_terms FROM wp_reci_journals ORDER BY id DESC LIMIT 1;"` — `flagged_terms` must be empty for a private entry.
5. Now share that entry. `flagged_terms` is populated and the mirror comment carries `_reci_flagged_terms`.

Step 4 is the privacy guarantee. Record its output verbatim.

- [ ] **Step 9: Commit**

```bash
git add assets/js/community-policy.js tests/test-flag-routing.php inc/features/community-policy.php inc/core/theme-setup.php templates/single/single-post.php
git commit -m "feat: add typing-time guideline warning and comment flagging

A matched term warns the writer inline and never disables a control -
this site's subject matter means people legitimately quote the language
used against them, and that testimony must survive.

Private journal entries warn but are never routed to a moderator;
shared entries and comments are held for review. Unknown surfaces fail
closed, since flagging is what exposes writing to another person.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 13: Share controls at submit time and on the dashboard

Implements the client's "an extra option while submitting, and on their dashboard to share, and to share anonymously". Task 8 built the REST route; nothing yet calls it from the UI.

**Files:**
- Create: `assets/js/journal-sharing.js`
- Modify: `modules/reflection-system/templates/response-block.php`
- Modify: `templates/page/dashboard/template-dashboard-journal.php`
- Modify: `inc/core/theme-setup.php`

**Interfaces:**
- Consumes: `PATCH /reci/v1/journals/{id}/share` with `{ shared: bool, anonymous: bool }` from Task 8; `reci_journal_status_label( string $status ): string` produced below.
- Produces: `reci_journal_status_label( string $status ): string` — the four user-facing labels, used by both the dashboard and the admin table.

- [ ] **Step 1: Add the shared status label helper**

Append to `inc/features/journal-status.php`:

```php
/**
 * The user-facing label for a status.
 *
 * The dashboard previously showed a two-state Shared/Private pill driven by
 * `is_shared`, which would render a pending entry as "Private" and leave the
 * author wondering whether their share went through.
 */
function reci_journal_status_label( string $status ): string {
	$labels = [
		'private'  => __( 'Private', 'reci-media-hub' ),
		'pending'  => __( 'Pending review', 'reci-media-hub' ),
		'approved' => __( 'Shared', 'reci-media-hub' ),
		'rejected' => __( 'Not published', 'reci-media-hub' ),
	];

	return $labels[ $status ] ?? $labels['private'];
}
```

- [ ] **Step 2: Add the assertions**

Append to `tests/test-journal-status.php`:

```php
reci_assert_same( 'Private', reci_journal_status_label( 'private' ), 'label: private' );
reci_assert_same( 'Pending review', reci_journal_status_label( 'pending' ), 'label: pending is distinct from private' );
reci_assert_same( 'Shared', reci_journal_status_label( 'approved' ), 'label: approved reads as shared' );
reci_assert_same( 'Not published', reci_journal_status_label( 'rejected' ), 'label: rejected' );
reci_assert_same( 'Private', reci_journal_status_label( 'nonsense' ), 'label: unknown status falls back to private' );
```

Run: `php tests/run.php`
Expected: `132 passed, 0 failed`.

- [ ] **Step 3: Add the submit-time toggles**

In `modules/reflection-system/templates/response-block.php`, replace this block:

```php
					<div class="mt-4 flex flex-wrap gap-4">
						<button class="inline-flex items-center justify-center rounded-full bg-[#d4a63f] px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.1em] text-[var(--reflection-accent-contrast)]" type="button" id="saveResponseBtn">Save reflection</button>
					</div>
```

with:

```php
					<div class="mt-4 grid gap-3">
						<label class="flex items-start gap-3 text-sm reci-reflection-soft-text">
							<input type="checkbox" id="reflectionShare" class="mt-1" />
							<span>
								<?php esc_html_e( 'Share this reflection with others', 'reci-media-hub' ); ?>
								<span class="block text-xs opacity-80"><?php esc_html_e( 'A moderator reads it before it appears. You can withdraw it at any time.', 'reci-media-hub' ); ?></span>
							</span>
						</label>
						<label class="flex items-start gap-3 text-sm reci-reflection-soft-text" id="reflectionAnonWrap" hidden>
							<input type="checkbox" id="reflectionAnonymous" class="mt-1" />
							<span>
								<?php esc_html_e( 'Share anonymously', 'reci-media-hub' ); ?>
								<span class="block text-xs opacity-80"><?php esc_html_e( 'Your name is hidden from readers and from this reflection\'s author. Site administrators can still see it.', 'reci-media-hub' ); ?></span>
							</span>
						</label>
					</div>
					<div class="mt-4 flex flex-wrap gap-4">
						<button class="inline-flex items-center justify-center rounded-full bg-[#d4a63f] px-6 py-4 font-['Oswald'] text-sm uppercase tracking-[0.1em] text-[var(--reflection-accent-contrast)]" type="button" id="saveResponseBtn">Save reflection</button>
					</div>
```

The anonymity copy is deliberately explicit that administrators can still see the author. Do not soften it.

- [ ] **Step 4: Write the sharing script**

Create `assets/js/journal-sharing.js`:

```js
/**
 * Share controls for journal entries.
 *
 * Reveals the anonymity option only when sharing is chosen, and drives the
 * PATCH /reci/v1/journals/{id}/share route from both the reflection page and
 * the dashboard list.
 */
( function () {
	'use strict';

	var settings = window.reciJournalSharing || {};

	function patchShare( journalId, shared, anonymous ) {
		return window.fetch( settings.root + 'reci/v1/journals/' + journalId + '/share', {
			method: 'PATCH',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': settings.nonce || ''
			},
			body: JSON.stringify( { shared: shared, anonymous: anonymous } )
		} ).then( function ( response ) {
			if ( ! response.ok ) {
				throw new Error( 'share request failed: ' + response.status );
			}
			return response.json();
		} );
	}

	// Reflection page: reveal the anonymity option only when sharing.
	var shareBox = document.getElementById( 'reflectionShare' );
	var anonWrap = document.getElementById( 'reflectionAnonWrap' );

	if ( shareBox && anonWrap ) {
		shareBox.addEventListener( 'change', function () {
			anonWrap.hidden = ! shareBox.checked;

			if ( ! shareBox.checked ) {
				var anonBox = document.getElementById( 'reflectionAnonymous' );
				if ( anonBox ) {
					anonBox.checked = false;
				}
			}
		} );
	}

	// Dashboard list: one control per row.
	document.querySelectorAll( '[data-reci-journal-share]' ).forEach( function ( control ) {
		control.addEventListener( 'change', function () {
			var row = control.closest( '[data-journal-id]' );
			var journalId = row ? row.getAttribute( 'data-journal-id' ) : null;
			var anonBox = row ? row.querySelector( '[data-reci-journal-anonymous]' ) : null;
			var status = row ? row.querySelector( '[data-reci-journal-status]' ) : null;

			if ( ! journalId ) {
				return;
			}

			control.disabled = true;

			patchShare( journalId, control.checked, anonBox ? anonBox.checked : false )
				.then( function ( result ) {
					if ( status ) {
						status.textContent = result.status === 'pending'
							? ( settings.pendingLabel || 'Pending review' )
							: ( settings.privateLabel || 'Private' );
					}
					if ( anonBox ) {
						anonBox.disabled = ! control.checked;
					}
				} )
				.catch( function () {
					// Put the control back where it was, so the UI never claims
					// a share that did not happen.
					control.checked = ! control.checked;
					if ( status ) {
						status.textContent = settings.errorLabel || 'Could not update. Try again.';
					}
				} )
				.finally( function () {
					control.disabled = false;
				} );
		} );
	} );
}() );
```

- [ ] **Step 5: Add the dashboard controls**

In `templates/page/dashboard/template-dashboard-journal.php`, first change the query so the new columns come back — it already does `SELECT *`, so no change is needed there.

Replace this block:

```php
						<div class="flex flex-col items-end gap-2 shrink-0">
							<span class="text-xs px-2 py-0.5 rounded-full <?php echo $shared ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-600'; ?>">
								<?php echo $shared ? 'Shared' : 'Private'; ?>
							</span>
```

with:

```php
						<div class="flex flex-col items-end gap-2 shrink-0">
							<?php
							$status       = (string) ( $journal->status ?? 'private' );
							$is_anonymous = (bool) (int) ( $journal->is_anonymous ?? 0 );
							$pill_class   = [
								'private'  => 'bg-zinc-100 text-zinc-600',
								'pending'  => 'bg-amber-100 text-amber-800',
								'approved' => 'bg-green-100 text-green-700',
								'rejected' => 'bg-rose-100 text-rose-700',
							][ $status ] ?? 'bg-zinc-100 text-zinc-600';
							?>
							<span data-reci-journal-status class="text-xs px-2 py-0.5 rounded-full <?php echo esc_attr( $pill_class ); ?>">
								<?php echo esc_html( reci_journal_status_label( $status ) ); ?>
							</span>

							<label class="flex items-center gap-2 text-xs text-zinc-600">
								<input
									type="checkbox"
									data-reci-journal-share
									<?php checked( in_array( $status, [ 'pending', 'approved' ], true ) ); ?>
								/>
								<?php esc_html_e( 'Share', 'reci-media-hub' ); ?>
							</label>

							<label class="flex items-center gap-2 text-xs text-zinc-600">
								<input
									type="checkbox"
									data-reci-journal-anonymous
									<?php checked( $is_anonymous ); ?>
									<?php disabled( 'private' === $status ); ?>
								/>
								<?php esc_html_e( 'Anonymously', 'reci-media-hub' ); ?>
							</label>
```

Then add the row id. Find the opening of the entry card:

```php
				<div class="bg-white border border-zinc-200 rounded-xl p-5">
```

and replace it with:

```php
				<div class="bg-white border border-zinc-200 rounded-xl p-5" data-journal-id="<?php echo esc_attr( (string) $journal->id ); ?>">
```

- [ ] **Step 6: Enqueue the script**

In `inc/core/theme-setup.php`, beside the `reci-community-policy` enqueue added in Task 12, add:

```php
	wp_enqueue_script(
		'reci-journal-sharing',
		get_template_directory_uri() . '/assets/js/journal-sharing.js',
		[],
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script(
		'reci-journal-sharing',
		'reciJournalSharing',
		[
			'root'         => esc_url_raw( rest_url() ),
			'nonce'        => wp_create_nonce( 'wp_rest' ),
			'pendingLabel' => reci_journal_status_label( 'pending' ),
			'privateLabel' => reci_journal_status_label( 'private' ),
			'errorLabel'   => __( 'Could not update. Try again.', 'reci-media-hub' ),
		]
	);
```

Run: `php -l inc/features/journal-status.php && php -l inc/core/theme-setup.php && php -l templates/page/dashboard/template-dashboard-journal.php && php -l modules/reflection-system/templates/response-block.php && node --check assets/js/journal-sharing.js`
Expected: `No syntax errors detected` for the four PHP files, no output from `node --check`.

- [ ] **Step 7: Manual verification**

1. On a reflection, the "Share this reflection" checkbox is present and the anonymity option is **hidden** until it is ticked.
2. Tick share, tick anonymous, save. The entry lands `pending` with `is_anonymous = 1`.
3. On the dashboard Journal page, that entry's pill reads **Pending review** — not "Private", which is what the old two-state pill would have shown.
4. Untick Share on the dashboard. The pill returns to **Private**, the mirror comment is deleted, and the anonymity checkbox becomes disabled.
5. Re-tick it. Back to **Pending review**.
6. Stop the local server (or block the request) and toggle again. The checkbox must snap back to its previous position and show the error label, never leaving the UI claiming a share that did not happen.

Record the results of steps 3, 4 and 6.

- [ ] **Step 8: Commit**

```bash
git add assets/js/journal-sharing.js inc/features/journal-status.php tests/test-journal-status.php inc/core/theme-setup.php templates/page/dashboard/template-dashboard-journal.php modules/reflection-system/templates/response-block.php
git commit -m "feat: add share and anonymity controls at submit time and on the dashboard

Task 8 added the share route; nothing called it from the UI. Both the
reflection prompt and the dashboard journal list now drive it, with the
anonymity option revealed only when sharing is chosen.

The dashboard pill now reads from status rather than the old two-state
is_shared flag, so a pending entry no longer renders as "Private" and
leave its author wondering whether the share went through.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

### Task 14: Mirror to the deploy tree and full verification

Implements spec §11.

**Files:**
- Modify: everything under `wordpress/` that Tasks 1–13 changed at root.

- [ ] **Step 1: Confirm the mirror is currently in sync**

```bash
cd /Users/olalekan/Projects/reci/media-hub
git status --short
```

Expected: clean. If not, stop and report — do not mirror a dirty tree.

- [ ] **Step 2: List what changed across the whole feature**

```bash
git diff --name-only df1cedf..HEAD -- . ':(exclude)wordpress' ':(exclude)docs'
```

Record this list. Every path on it that also exists under `wordpress/` needs mirroring.

- [ ] **Step 3: Mirror each changed file**

For each path `P` from Step 2 where `wordpress/P` exists in git:

```bash
cp "P" "wordpress/P"
```

New files that belong in the deployed theme (`inc/features/*.php`, `assets/js/community-policy.js`, `assets/js/journal-sharing.js`, the template parts, the overlay template) must be copied even though `wordpress/P` does not exist yet. Create the parent directory first if needed.

**Do not mirror** `tests/`, `docs/`, or `package.json` — those are development-only and the packaged theme should not carry them.

- [ ] **Step 4: Verify the mirror**

```bash
for f in $(git diff --name-only df1cedf..HEAD -- . ':(exclude)wordpress' ':(exclude)docs' ':(exclude)tests' ':(exclude)package.json'); do
  if [ -f "wordpress/$f" ]; then
    diff -q "$f" "wordpress/$f" || echo "OUT OF SYNC: $f"
  else
    echo "MISSING IN MIRROR: $f"
  fi
done
```

Expected: no output. Any line printed is a file that would ship stale or missing.

- [ ] **Step 5: Run the full suite one last time**

```bash
php tests/run.php
for f in $(git diff --name-only df1cedf..HEAD -- '*.php'); do php -l "$f" >/dev/null || echo "SYNTAX ERROR: $f"; done
npm test
```

Expected: `132 passed, 0 failed`; no syntax errors; vitest unchanged from before this work.

- [ ] **Step 6: Final end-to-end walkthrough**

Do this as a single unbroken sequence on the Local site and record each result:

1. As a member, write a journal entry containing a flagged term. Warning shows, save succeeds, entry is `private`, `flagged_terms` empty.
2. Share it anonymously. Entry is `pending`; `flagged_terms` populated; mirror comment exists with `user_id = 0`.
3. As the **reflection's owner** (an editor), open wp-admin Comments. The entry reads `Anonymous`, with no profile link. **No notification has been sent to them yet.**
4. As a `reci_moderator`, open the same queue. The real author is visible. Approve it.
5. The owner now has exactly one notification, and its stored `message` says `Anonymous`, not the author's name.
6. Open the reflection signed out. The button reads `Read 1 shared reflection`; the overlay lists the entry as `Anonymous`.
7. `curl` the shared-journals endpoint signed out. No `user_id` field anywhere.
8. As the author, withdraw the entry. The button disappears, the mirror comment is gone, the row is `private`.

- [ ] **Step 7: Commit**

```bash
git add wordpress/
git commit -m "chore: mirror journals and moderation work to the deploy tree

Development happens at the repo root; wordpress/ is the packaged copy.
Tests, docs and package.json are deliberately not mirrored.

Co-Authored-By: Claude Opus 5 <noreply@anthropic.com>"
```

---

## Done means

- `php tests/run.php` reports `132 passed, 0 failed`.
- `npm test` is unchanged from before this work.
- Every changed PHP file passes `php -l`.
- The Step 6 walkthrough above completed, with results recorded.
- `wordpress/` is in sync for every shipped file.

If any manual verification could not be run, say which and why. Do not report the feature complete on the test suite alone — the suite covers the decision logic, not the WordPress wiring around it.
