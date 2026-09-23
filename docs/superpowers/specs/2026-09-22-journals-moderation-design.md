# Shared Journals, Moderation, Counters & Community Policy — Design

**Date:** 2026-09-22
**Status:** Awaiting review
**Branch:** development

## 1. Why

Five client requests, reviewed together because four of them converge on one
moderation surface:

1. Users can share reflection journals (optionally anonymously); shared entries
   are reviewed before going public; a reflection surfaces its shared entries;
   reflection owners are notified.
2. A Moderator role that manages comments and shared journals only.
3. Like and bookmark counters on posts.
4. Submission guidelines shown before the collaborator gate, not behind it.
5. A community/abuse policy with a term list, reachable while typing a comment
   or a journal entry.

## 2. What already exists

Discovery found more built than the brief assumes. Nothing here needs inventing
from scratch.

| Capability | Where | State |
|---|---|---|
| Journal storage | `wp_reci_journals` (`inc/core/database.php:24`) | Live, has `is_shared` |
| Journal REST | `inc/features/reflection-responses.php` | Create, list, `PATCH /journals/{id}/share` |
| Journal dashboard | `templates/page/dashboard/template-dashboard-journal.php` | Live, renders Shared/Private pill |
| Journal admin table | `inc/admin/class-reci-journals-list-table.php` | Live |
| Comments | native WP, forced open on `post` (`inc/content/content-types.php:278`) | Live, **no moderation gate** |
| Comment dashboard | `templates/page/dashboard/template-dashboard-comments.php` | Live |
| Notifications | `wp_reci_notifications` + `reci_create_notification()` | Live, wired to `transition_post_status` |
| Role ladder | `inc/core/roles.php`, `RECI_ROLES_VERSION = '1.0.0'` | Live, 6 levels |
| Likes / bookmarks | `reci_likes` / `reci_bookmarks` **user meta arrays** (`inc/admin/dashboard.php:192,197,374,402`) | Live, no counters |
| Theme settings | single `reci_theme_settings` option (`inc/admin/theme-settings.php`) | Live |
| Reflection prompt UI | `modules/reflection-system/templates/response-block.php` | Live, has "Your saved responses" panel |
| Overlay pattern | `modules/reflection-system/templates/menu-overlay.php` + `menus/` | Live, variant-based |

### Two defects found during discovery

- **`reci_create_journal()` shares without review.** It sets `is_shared` directly
  from the user's `reci_journal_default_privacy` meta
  (`inc/features/reflection-responses.php:115-116`). A user whose default is
  `public` has every entry published with no review whatsoever. This must route
  through `pending` like any other share.
- **Counting likes/bookmarks is O(all users).** They live in per-user meta
  arrays, so a per-post count means scanning every user's meta. Request 3 is a
  data-model fix, not a UI addition.

## 3. Decisions taken

Recorded with their rationale so they are not silently re-litigated later.

### 3.1 Journals stay separate from comments; sharing mirrors into `wp_comments`

The client asked whether journals should still be separate from comments.
**Answer: separate storage, shared moderation surface.**

`wp_reci_journals` stays the source of truth. When a user shares an entry, it is
*also* written as a `wp_comment` on the reflection post, held pending.

Rejected alternatives:

- *Merge everything into `wp_comments`.* Journals are private by default and most
  are never shared. Private writing sitting in `wp_comments` is reachable by the
  REST API, feeds, exports and every comment plugin installed now or later. Too
  much exposure for too little gain.
- *Keep fully separate with a bespoke queue.* Means building moderation UI,
  threading and capability mapping from scratch, all of which core already has.

The mirror costs a sync obligation (two rows per shared entry) and buys the
moderation queue, threaded replies, and a Moderator role that maps onto
`moderate_comments` without inventing capabilities.

### 3.2 Anonymous hides the author from the public *and* from the reflection owner

Visible only to administrators and moderators. Specifically:

| Audience | Sees the author |
|---|---|
| Public | no |
| Reflection owner (`post_author`) | **no** |
| Editor, Site Manager | **no** |
| `reci_moderator` | yes |
| `administrator` | yes |

This is gated on a new capability, `reci_view_journal_identity`, not on "is
staff" — because the reflection owner usually *is* staff.

**The landmine this avoids.** WordPress grants `moderate_comments` to `editor`
by default, and `reci_site_manager` is built from Editor's capabilities
(`inc/core/roles.php:84-85`), so it inherits the same. A reflection owner will
therefore normally reach the native comment queue, where core renders the author
from `comment_user_id`. Masking has to be active and capability-gated, not merely
"leave the name out of the notification".

**Fail closed, not open.** For anonymous entries the mirror comment stores
`comment_user_id = 0`, `comment_author = 'Anonymous'` and an empty author email.
The real identity lives **only** in `wp_reci_journals.user_id`, reachable through
the `_reci_journal_id` meta, and is *added back* for users holding
`reci_view_journal_identity`.

The alternative — keep `comment_user_id` set and filter every display path — was
rejected. It leaks by default: any core path, REST route, export or plugin we
failed to filter exposes the author. Zeroing the column means a missed path shows
"Anonymous", which is the safe direction to be wrong in.

This costs nothing the author cares about: their dashboard reads
`wp_reci_journals` by `user_id`, not `wp_comments`.

**Still state it plainly in the UI copy.** "Anonymous" here means hidden from
readers and from the reflection's author — not from site administrators. A user
who reads it as "nobody can ever know" and is later moderated has been misled.

### 3.3 Reflection owners are notified on approval, not on share

The client said "submitted and shared", but with review in between, notifying on
share puts unmoderated text into an owner's inbox and generates noise for entries
later rejected. Moderators get the pending alert; the owner gets one clean
notification once an entry is approved.

**Flag to client:** they may expect this to be instant.

### 3.4 Shared entries are pooled per reflection

One list per reflection, not one per prompt. The entry point is a button that is
hidden when the count is zero.

### 3.5 The abuse term list warns and flags; it never blocks

RECI is a racial-equity site. Journal entries and comments will legitimately
quote slurs, name language used against people, and discuss abusive speech as
subject matter. A hard keyword block would reject exactly the testimony the
reflections are built to elicit, would lose people's writing, and is trivially
evaded with spacing or substitution.

Matching a term shows an inline notice linking to the guideline, allows the save,
and records the matched terms for the moderator.

## 4. Data model

### 4.1 `wp_reci_journals` additions

`reci_db_version` moves `1.5.0` → `1.6.0` in `inc/core/database.php`.

```sql
status        varchar(20)          NOT NULL DEFAULT 'private',
is_anonymous  tinyint(1)           NOT NULL DEFAULT 0,
shared_at     datetime                 NULL DEFAULT NULL,
comment_id    bigint(20) unsigned  NOT NULL DEFAULT 0,
flagged_terms text                 NOT NULL,
KEY status (status),
KEY comment_id (comment_id)
```

Lifecycle: `private → pending → approved | rejected`. A user may withdraw an
entry from any state, returning it to `private`.

`is_shared` is retained and kept in sync (`is_shared = status === 'approved'`) so
existing readers — the dashboard template and the admin list table — keep working
through the transition rather than breaking on deploy.

**Backfill:** existing rows with `is_shared = 1` become `status = 'approved'`
with `shared_at = created_at`. They were already public; the migration must not
silently unpublish them.

### 4.2 The mirror comment

| Field | Value |
|---|---|
| `comment_type` | `reci_journal` |
| `comment_post_ID` | the reflection's post ID |
| `comment_approved` | `0` on share |
| `comment_user_id` | the author — or **`0`** when anonymous (see §3.2) |
| `comment_author` | display name, or `Anonymous` |
| `comment_author_email` | the author's email, or **empty** when anonymous |
| `comment_content` | the journal response |
| meta `_reci_journal_id` | row id in `wp_reci_journals` |
| meta `_reci_anonymous` | `1` when anonymous |
| meta `_reci_flagged_terms` | matched terms, if any |
| meta `_reci_prompt` | the prompt the entry answered |

`wp_reci_journals.comment_id` holds the reverse link. Deleting either side must
clean up the other — a `deleted_comment` hook resets the journal to `private`.

## 5. Workstreams

### A. Schema and migration

`inc/core/database.php`. Add the columns, bump to `1.6.0`, backfill `status` from
`is_shared`, and backfill the counters from §E in the same pass.

### B. Share flow

`inc/features/reflection-responses.php`.

- `PATCH /journals/{id}/share` accepts `{ shared: bool, anonymous: bool }`.
- Sharing: set `status = 'pending'`, `shared_at`, `is_anonymous`; run the term
  matcher (§G); insert the mirror comment; store `comment_id`. When
  `is_anonymous`, the mirror is written with `comment_user_id = 0`,
  `comment_author = 'Anonymous'` and an empty author email (§3.2) — the link back
  to the real author exists only through `_reci_journal_id`.
- Toggling anonymity on an already-shared entry rewrites the mirror comment's
  author fields accordingly. Turning anonymity **on** must also purge the
  previously stored name from any notification row already written for it.
- Unsharing: trash the mirror comment, reset to `private`, clear `comment_id`.
- Fix `reci_create_journal()` so a `public` default privacy creates the entry and
  *then* runs the share path, landing it in `pending` — never straight to public.
- `GET /journals` gains `status`, `is_anonymous` and `flagged_terms` in its
  payload so the dashboard can render state.

The existing ownership check on the share route is correct and stays.

### C. Moderation and the Moderator role

**Role** (`inc/core/roles.php`, bump `RECI_ROLES_VERSION` to `1.1.0`):

`reci_moderator`, sitting *orthogonal* to the six-level ladder rather than as a
rung on it.

```
read, moderate_comments, edit_comment, edit_comments,
reci_access_admin, reci_moderate_journals, reci_view_journal_identity
```

Deliberately **no** `edit_posts`, `publish_posts`, `list_users` or
`reci_approve_collaborators`. A moderator manages discussion, not content and not
people. `reci_moderate_journals` and `reci_view_journal_identity` join
`reci_custom_capabilities()`.

**`reci_view_journal_identity` is granted to `administrator` and
`reci_moderator` only** — explicitly *not* to `editor` or `reci_site_manager`,
both of which already hold `moderate_comments` and would otherwise see through
anonymity. They can still approve and reject; they just see "Anonymous".

**Identity resolution.** One helper, `reci_journal_author_for_display( int
$journal_id ): array`, returns either the real author or the anonymous
placeholder based on `current_user_can( 'reci_view_journal_identity' )`. Every
surface calls it — the moderator queue, the journals list table, the
shared-journals overlay. Because the comment row carries no identity for
anonymous entries (§3.2), a surface that forgets to call it degrades to
"Anonymous" rather than leaking.

**Notifications carry no identity.** `reci_create_notification()` writes its
title and message into `wp_reci_notifications` as stored text, and the reflection
owner reads those rows. For an anonymous entry the stored copy must never contain
the author's name — masking at render time would be too late.

**Queue.** Mirror comments appear in the native comment screen filtered by
`comment_type = reci_journal`. `inc/admin/class-reci-journals-list-table.php`
gains a status column, matched-term badges, and approve/reject row actions.

**Approval.** A `comment_approved_reci_journal` (and `wp_set_comment_status`)
handler sets `journals.status = 'approved'`, syncs `is_shared = 1`, then calls
`reci_create_notification()` against the reflection's `post_author`. Rejection
sets `status = 'rejected'` and notifies the author, not the owner.

Notification type: `shared_journal_approved`.

### D. Surfacing shared entries

- New route `GET /reci/v1/reflections/{id}/shared-journals` — approved only,
  paginated, author name replaced by `Anonymous` where `is_anonymous = 1`.
  Must never return `user_id`, `comment_user_id` or an avatar URL for an
  anonymous row, to any caller — including one holding
  `reci_view_journal_identity`. This is a public reading surface; identity
  belongs in the moderation surfaces, not here.
- Core's own comment REST route needs the same treatment: a
  `rest_prepare_comment` filter that strips author fields from `reci_journal`
  comments, so `/wp/v2/comments` cannot be used as a side door.
- `modules/reflection-system/templates/response-block.php` gains a sibling panel
  beside "Your saved responses": **Read N shared reflections**, hidden when
  `N = 0`.
- The overlay reuses the `menu-overlay.php` variant pattern rather than
  introducing a second overlay mechanism.
- The shipped style templates carry **at most one** `reflection-prompt` chapter
  each — `voices-of-resistance`, `march-toward-justice` and `breaking-chains` have
  one, `racial-disparities` has none. The builder lets an author add any number,
  so the button must still handle the multi-prompt case: render it **once**, on
  the last prompt chapter, so the pooled count does not repeat down the page.
- A reflection with **no** prompt chapter can still have shared entries if prompts
  are later removed. Those entries stay reachable from the author's dashboard and
  the moderator queue; they simply have no in-reflection entry point.
- Count comes from a cached `COUNT(*)` on `status = 'approved'`, busted on
  approve/reject/withdraw.

### E. Like and bookmark counters

- `_reci_like_count` and `_reci_bookmark_count` post meta, written in the existing
  toggle handlers (`inc/admin/dashboard.php:374` and `:402`).
- One-time backfill in the `1.6.0` migration, scanning `reci_likes` /
  `reci_bookmarks` user meta once.
- User meta arrays remain the source of truth; the post meta is a derived cache.
  A WP-CLI or admin-triggered recount guards against drift.
- Counters render wherever the like/bookmark buttons already appear.

### F. Guidelines before the gate

`templates/page/template-submit-content.php`. Hoist a guidelines block above the
`$submit_state` branch so all four states — guest, member, pending collaborator,
approved collaborator — see it first. Content from `reci_theme_settings`
(§G shares this settings surface).

### G. Community policy and term list

**Settings** (`inc/admin/theme-settings.php`, into `reci_theme_settings`):

- `submission_guidelines` — body for §F.
- `community_policy` — the abuse/community guideline body.
- `abuse_terms` — newline-separated term list.

On save, `abuse_terms` is mirrored into WP's native `moderation_keys` option so
the comment path inherits core's `wp_allow_comment()` check instead of a parallel
implementation. `disallowed_keys` is left untouched — that option hard-rejects,
which §3.5 rules out.

**Matcher.** One function, `reci_match_flagged_terms( string $text ): array`.
Word-boundary, case-insensitive, with accent folding and basic leetspeak
normalisation. Authoritative server-side. The same list is localised to JS for
the typing-time warning; this is not a leak, because the guideline publishes the
list by design.

**Behaviour by surface:**

| Surface | Warns while typing | Routed to moderators |
|---|---|---|
| Private journal | yes | **no** |
| Shared journal | yes | yes, on share |
| Comment | yes | yes — held, `comment_approved = 0` |

**UI.** A permanent "Community guideline" link beside the textarea in both
`response-block.php` and the comment form in `templates/single/single-post.php`,
satisfying "you can click it to see the guideline". The warning appears only on a
match. The policy opens in a modal, falling back to a normal page.

**Behaviour change to call out:** comments currently post straight through with no
gate at all. Matched comments being held for review is new.

## 6. Decisions confirmed with the client

All four resolved with the client on 2026-09-22:

1. **Private journals are never routed to moderators.** Confirmed. Detection
   warns the writer and stops there.
2. **Owner notification waits for approval** (§3.3), not instant. Confirmed.
3. **Anonymity hides the author from the public *and* the reflection owner**,
   revealing only to administrators and moderators (§3.2). Confirmed, and
   widened from the original draft — moderators were added because tracking
   repeat abuse is the role's purpose.
4. **Moderators cannot edit posts or manage users.** Confirmed.

Remaining item for the client, not blocking:

- **UI copy for "anonymous"** must say it is hidden from readers and from the
  reflection's author, but not from site administrators. Needs wording sign-off.

## 7. Sequencing

```
F  ──────────────┐  (independent, small)
E  ──────────────┤  (independent, small)
G-settings ──────┤  shares the settings surface with F
                 │
A ─► B ─► C ─► D │  (one chain)
          └─► G-flagging
```

F, E and the G settings surface can ship first and independently. A→B→C→D is a
single chain. G's flagging behaviour depends on C's queue; G's typing-time UI
does not.

## 8. Testing

- **Migration:** a `1.5.0` database with a mix of shared and private rows lands on
  `1.6.0` with no row changing visibility. Re-running is a no-op.
- **The defect in §2:** a user with `reci_journal_default_privacy = public`
  creates an entry and it lands `pending`, not public.
- **Anonymity, per audience:** for one anonymous entry, assert the author is
  masked for a logged-out visitor, for the reflection's own `post_author`, for an
  `editor` and for a `reci_site_manager` — and resolved for a `reci_moderator`
  and an `administrator`.
- **Anonymity, per surface:** the shared-journals endpoint, `/wp/v2/comments`,
  the native comment queue, the journals list table, and the stored
  notification row all mask an anonymous author. The stored notification text
  must not contain the name even before rendering.
- **Fail-closed:** with the display helper deliberately bypassed, an anonymous
  entry still renders "Anonymous" because `comment_user_id` is `0`.
- **Round trip:** share → pending → approve → owner notified once → appears in
  the overlay → withdraw → disappears, comment trashed.
- **Capability boundaries:** `reci_moderator` can reach the comment queue and
  cannot reach post editing, user management or collaborator approvals.
- **Counters:** like, unlike, re-like converges; backfill matches a live recount.
- **Matcher:** accent and leetspeak variants match; a term inside a longer
  ordinary word does not.
- PHP syntax checked with `php -l`, per theme convention.

## 9. Out of scope

- Replacing the user-meta storage for likes/bookmarks (counters only).
- Threaded replies on shared journals — the mirror makes them possible later, but
  no UI is built now.
- Digest notifications for owners of popular reflections (§3.3 considered and
  deferred).
- Automated abuse detection beyond term matching.

## 10. Incidental finding, not in scope

`modules/reflection-system/inc/reflection-system-registry.php` declares
`'breaking-chains'` **twice** as a sibling key in the same styles array, at lines
164 and 250. PHP silently keeps the last one, so the definition at 164 is dead
code — it never renders, and `php -l` will not flag it. The live template is the
one at 250 (which is the one carrying a `reflection-prompt` chapter).

Worth a separate fix; it does not block this work, but anyone editing the 164
block will watch their changes do nothing.

## 11. Repo note

The tree is duplicated: root and `wordpress/` (275 vs 125 tracked PHP files),
both moved in the same commit `093136a` with no sync script in `scripts/`.
`wordpress/` reads as a packaged deploy mirror. **Development happens at root**;
confirm the mirroring step before release. `docs/data-storage-map.md` should be
updated with the new columns.
