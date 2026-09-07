# Submission fields × post types — 2026-09-07

Read from source: `sample/recmh-submission.jsx` (the form),
`reci_media_hub_handle_submission()` in `inc/features/submissions.php` (the
handler), `inc/content/meta-fields.php` (post-type fields),
`inc/content/taxonomies.php` (registration).

**Not verified at runtime.** Local's database has been down all session, so
nothing below was observed in a browser or database — it is all read from code.
The three "never posted" findings are the strongest, because they are absences
in the form's `FormData` and need no runtime to confirm.

---

## The five submittable post types

`reci_media_hub_submission_type_map()`:

| Front-end choice | Post type |
|---|---|
| blog, article, exhibit, other | `post` |
| podcast | `reci_podcast` |
| video | `reci_video` |
| document | `reci_document` |
| assessment | `reci_assessment` |

`course`, `event` and `reflection` are **not** in the map — they cannot be
submitted at all, though `course` and `event` have full field sets.

---

## Legend

| Mark | Meaning |
|---|---|
| **OK** | Value reaches a real, queryable, displayable home |
| **TEXT** | Lands only inside the `post_content` blob |
| **HIDDEN** | Written to the database, but the taxonomy is not registered for that type, so nothing can display it |
| **DEAD** | The form never sends it — the field exists but does nothing |
| **DUP** | Has a proper home *and* is copied into the `post_content` blob |
| **–** | Does not apply to that type |

---

## The table

| # | Submission field | `post` | `reci_podcast` | `reci_video` | `reci_document` | `reci_assessment` | Where it lands today | Recommended |
|---|---|---|---|---|---|---|---|---|
| 1 | `contentType` | OK | OK | OK | OK | OK | `post_type` + `_reci_submission_content_type` | Keep |
| 2 | `title` | OK | OK | OK | OK | OK | `post_title` | Keep |
| 3 | `abstract` | OK | OK | OK | OK | OK | `post_excerpt` | Keep |
| 4 | `selectedSpheres` | OK | OK | OK | **HIDDEN** | OK | `reci_sphere` terms | Register `reci_sphere` for `reci_document` |
| 5 | `targetAudience` | DUP | DUP | DUP | **HIDDEN** | DUP | `reci_target_audience` terms **+ blob** | Drop from blob; register for `reci_document` |
| 6 | `practiceType` | **HIDDEN** | **HIDDEN** | **HIDDEN** | **HIDDEN** | **HIDDEN** | `reci_practice_focus` terms **+ blob** | Register the taxonomy for all five types; drop from blob |
| 7 | `contentLink` | OK | OK | OK | OK | – | `_reci_submission_content_link` | Split per type — see below |
| 8 | `submissionFile` | OK | OK | OK | OK | – | `_reci_submission_file_id` / `_url` | For `reci_document` this **is** the content; for podcast it should also fill `_reci_podcast_audio_url` |
| 9 | `fileDescription` | DUP | DUP | DUP | DUP | – | `_reci_submission_file_description` **+ blob** | Drop from blob |
| 10 | `evidenceBasis` | TEXT | TEXT | TEXT | TEXT | TEXT | blob only | Keep as prose in the body — but compose the body server-side |
| 11 | `processOrientation` | TEXT | TEXT | TEXT | TEXT | TEXT | blob only | Same |
| 12 | `equityFocus` | TEXT | TEXT | TEXT | TEXT | TEXT | blob only | Same |
| 13 | `keywords` | TEXT | TEXT | TEXT | TEXT | TEXT | blob only | Route to `post_tag` — registered and sitting empty |
| 14 | `location` | **DEAD** | **DEAD** | **DEAD** | **DEAD** | **DEAD** | nowhere | `reci_location` is registered and unused — wire it or delete the field |
| 15 | `agreeTerms` | **DEAD** | **DEAD** | **DEAD** | **DEAD** | **DEAD** | nowhere | Gates the button, records nothing. Store the consent |
| 16 | `agreeReview` | **DEAD** | **DEAD** | **DEAD** | **DEAD** | **DEAD** | nowhere | Same |
| 17 | `firstName` | OK | OK | OK | OK | OK | `_reci_submission_first_name` | Keep — audit trail of who submitted |
| 18 | `lastName` | OK | OK | OK | OK | OK | `_reci_submission_last_name` | Keep |
| 19 | `email` | OK | OK | OK | OK | OK | `_reci_submission_email` | Keep |
| 20 | `organization` | OK | OK | OK | OK | OK | `_reci_submission_organization` | Duplicates the `reci_author` profile — read from the profile instead |
| 21 | `role` | OK | OK | OK | OK | OK | `_reci_submission_role` | Same |
| 22 | `bio` | OK | OK | OK | OK | OK | `_reci_submission_bio` | Same |
| 23 | `website` | OK | OK | OK | OK | OK | `_reci_submission_website` | Same |

### Count

| Status | Fields |
|---|---:|
| OK | 11 |
| DUP — works, but also duplicated into the blob | 3 |
| TEXT — blob only | 4 |
| HIDDEN | 1 for every type, plus 2 more on `reci_document` |
| DEAD | 3 |

**Three fields do nothing at all**, and they are the surprise: `location`,
`agreeTerms`, `agreeReview`. The two agreement checkboxes gate the submit button
in the browser and are never sent, so there is no record anywhere that a
contributor accepted the terms.

There is a fourth dead path, on the server side: the handler reads
`$_POST['submission_author_opt_in']` and, if true, creates an author profile
(`inc/features/submissions.php` ~line 784). The form never sends that key, so
that whole branch has never run.

---

## The other direction — post-type fields with no submission field

| Post type | Its own fields | Collected by the form |
|---|---|---:|
| `post` | `_post_canonical_url`, `_post_source_name`, `_post_source_url`, `_post_read_time_label`, `_post_featured_rank` | **0 of 5** |
| `reci_podcast` | `_audio_url`, `_video_url`, `_duration_label`, `_duration_secs`, `_episode_number`, `_season_number`, `_transcript_url`, `_spotify_url`, `_apple_url`, `reci_show` | **0 of 10** |
| `reci_video` | `_reci_video_url`, `_platform`, `_external_id`, `_duration_label`, `_duration_secs` | **0 of 5** |
| `reci_document` | none | n/a — fully served |
| `reci_assessment` | `_type`, `_questions`, `_result_ranges`, `_intro`, `_instructions`, `_estimated_time`, `_completion_title`, `_completion_message` | **0 of 8** |

Missing for every type: **featured image**. The form never collects one.

---

## What each type actually needs

**`reci_document` — works today.** No fields of its own; the upload is the
content. The only fix it needs is taxonomy registration.

**`post` — nearly works.** `contentLink` is the one real gap: for an article
reposted from elsewhere it should fill `_post_canonical_url` and
`_post_source_url`, not a generic submission meta key. `_post_read_time_label`
and `_post_featured_rank` are editorial judgements — leave them to staff.

**`reci_podcast` — does not work.** No audio, so a submitted podcast cannot
play. Needs audio URL and duration at minimum; episode and season next.

**`reci_video` — does not work.** No video URL, so nothing plays. Platform and
external ID can be derived from the URL rather than asked for.

**`reci_assessment` — should probably come out of the form.** Eight fields
including a repeating question builder with types, scales, choices and result
ranges. That is not a wizard step. Submitting an assessment today produces a
quiz with no questions, which cannot be taken.

---

## Recommended, in three tiers

**Tier 1 — fix what is silently broken.** Small, self-contained, no form redesign.
- Register `reci_practice_focus` for the five submittable types
- Register `reci_sphere` and `reci_target_audience` for `reci_document`
- Route `keywords` to `post_tag`
- Post and store the two agreement checkboxes
- Compose `post_content` server-side instead of in the browser, and drop the
  three duplicated blocks

**Tier 2 — make the form type-aware.** The real work.
- Podcast: audio URL, duration, episode, season, transcript
- Video: video URL, duration
- Post: source name, source URL, canonical URL
- All types: featured image
- Remove `assessment` from the front-end type list

**Tier 3 — decisions, not code.**
- `location`: wire to `reci_location`, or delete the field
- `course` and `event` have complete field sets and cannot be submitted — add
  them to the map, or accept that they are staff-only
- Contributor fields duplicate the `reci_author` profile on every submission and
  drift the moment the profile changes — read from the profile instead
- The dead `submission_author_opt_in` branch: wire it or delete it

Nothing here is applied. This is the map, not the fix.
