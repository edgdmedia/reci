# Submission field audit — 2026-09-07

Why your `post_content` looks like that, what each submission field does with its
value, and what every post type expects that the form never asks for.

---

## Table 1 — Where each submission field actually lands

The form is `sample/recmh-submission.jsx`; the handler is
`reci_media_hub_handle_submission()` in `inc/features/submissions.php`.

| Form field | POST key | Real destination | Also written into `post_content`? | Verdict |
|---|---|---|---|---|
| `title` | `submission_title` | `post_title` | no | correct |
| `abstract` | `submission_summary` | `post_excerpt` | no | correct |
| `contentType` | `submission_content_type` | `post_type` + `_reci_submission_content_type` | no | correct |
| `contentLink` | `submission_content_link` | `_reci_submission_content_link` | no | correct |
| `selectedSpheres` | `reci_sphere_terms[]` | `reci_sphere` terms | no | correct |
| `targetAudience` | `reci_target_audience_terms[]` | `reci_target_audience` terms | **yes** | duplicated |
| `practiceType` | `reci_practice_focus_terms[]` | `reci_practice_focus` terms | **yes** | duplicated **and broken** — see Table 2 |
| `fileDescription` | `submission_file_description` | `_reci_submission_file_description` | **yes** | duplicated |
| `evidenceBasis` | — | **none** | yes | orphaned |
| `processOrientation` | — | **none** | yes | orphaned |
| `equityFocus` | — | **none** | yes | orphaned |
| `keywords` | — | **none** | yes | orphaned — should be `post_tag` |
| `location` | — | **never posted** | no | dead: `_reci_submission_location` exists and is always empty |
| `firstName` … `website` | `submission_*` | `_reci_submission_*` meta | no | correct |

**Reading your paste.** Seven labelled blocks are concatenated in the browser
(`recmh-submission.jsx`, the `submission_details` append) and posted as one
string. Three of those seven — Practice / Focus Area, Target Audience, File
Upload Description — already have a proper home and are being written twice.
Four have nowhere else to go. `File Upload Description:` is empty in your paste
because you did not attach a file, but the label is emitted unconditionally.

---

## Table 2 — Taxonomy registration vs. what the form writes

`inc/content/taxonomies.php`

`$submission_post_types` resolves to:
`post`, `reci_podcast`, `reci_video`, `reci_event`, `reci_reflection`,
`reci_assessment`, `reci_course` — **note `reci_document` is absent.**

| Taxonomy | Registered for | Form writes it to | Status |
|---|---|---|---|
| `reci_sphere` | `$submission_post_types` | all submissions | fine except `reci_document` |
| `reci_target_audience` | `$submission_post_types` | all submissions | fine except `reci_document` |
| `reci_practice_focus` | **`['reci_author']` only** | all submissions | **bug — every type** |
| `reci_location` | `$post_types` | never | unused by the form |
| `post_tag` | all content types | never | free slot for `keywords` |

`document` is in the submission type map, so a document *can* be submitted — but
`reci_document` is in neither `$submission_taxonomy_post_types` nor the
`reci_practice_focus` list, so **none** of its three taxonomies are registered
for it. Every sphere, audience and practice term on a submitted document is
written and invisible.

`reci_practice_focus` is registered only against `reci_author`. The form collects
it and `wp_set_object_terms()` writes the row, so the relationship exists in the
database — but the taxonomy is not registered for `post`, `reci_video`,
`reci_podcast`, `reci_document` or `reci_assessment`. It therefore does not
appear in `get_object_taxonomies()` for those types, gets no admin column or
metabox, and term archives will not list these posts. The value is stored and
invisible.

That is the concrete version of the problem you described: the value went to a
taxonomy that cannot show it, *and* to `post_content` where you can see it.

---

## Table 3 — What each post type expects vs. what the form collects

Meta from `inc/content/meta-fields.php`. "Collected" means the submission form
asks for it.

| Post type | Type-specific fields the CPT defines | Collected by the form? |
|---|---|---|
| `post` (blog, article, exhibit, other) | `_post_canonical_url`, `_post_source_name`, `_post_source_url`, `_post_read_time_label`, `_post_featured_rank` | **none** |
| `reci_podcast` | `_reci_podcast_audio_url`, `_apple_url`, `_spotify_url`, `_video_url`, `_transcript_url`, `_episode_number`, `_season_number`, `_duration_label`, `_duration_secs` | **none** |
| `reci_video` | `_reci_video_url`, `_platform`, `_external_id`, `_duration_label`, `_duration_secs` | **none** |
| `reci_document` | *no metabox defined at all* | n/a |
| `reci_assessment` | *no metabox defined at all* | n/a |
| `reci_course` | `_reci_course_start_date`, `_duration_weeks`, `_lessons`, `_level`, `_format`, `_fee_label`, `_enrollment_url` | **none** — and `course` is not in the type map, so it cannot be submitted |
| `reci_event` | `_reci_event_start_date`, `_end_date`, `_start_time`, `_end_time`, `_timezone`, `_is_virtual`, `_location_name`, `_location_address`, `_registration_url`, `_cta_label` | **none** — and `event` is not in the type map either |

Every podcast submitted through the form arrives with no audio URL, no episode
number and no duration. Every video arrives with no video URL — only the generic
`_reci_submission_content_link`. Staff have to open wp-admin and fill all of it
in by hand, which is exactly the wp-admin dependency you are trying to remove.

`reci_document` and `reci_assessment` are in the submission type map but have no
field definitions anywhere, so there is nothing to collect even in principle.

---

## Recommendation

**1. Stop building `post_content` in the browser.** The four orphaned narrative
fields are real content — post them as their own keys and let the server compose
the body, so each value also survives as meta and can be edited individually
later.

| Field | Proposed home |
|---|---|
| `evidenceBasis` | `_reci_submission_evidence_basis` |
| `processOrientation` | `_reci_submission_process_orientation` |
| `equityFocus` | `_reci_submission_equity_focus` |
| `keywords` | `post_tag` terms |

**2. Fix `reci_practice_focus` registration** — add the submission post types.
One-line change, and it makes existing stored terms visible retroactively.

**3. Drop the three duplicated blocks** from the composed body. They have homes.

**4. Make the form's later steps depend on the chosen content type.** Step 1
already picks the type; steps 2–3 should then ask for that type's own fields —
audio URL and episode number for a podcast, video URL and platform for a video,
dates and venue for an event. This is the "dynamic fields" you asked about, and
it is what closes the wp-admin gap for staff.

**5. Decide on `course` and `event`.** Both have full field definitions and
neither can be submitted. Either add them to the type map or drop their entries
from the front-end type list.

**6. `location` is dead** — the form holds it in state and never posts it.
Either wire it to `reci_location` or remove it.

### Order I would do them in

1, 2 and 3 are small, self-contained, and fix data that is being lost or hidden
right now. 4 is the large one: it needs a per-type field schema in PHP, that
schema exposed to the React app, conditional rendering in steps 2–3, and a
handler that writes each type's meta. 5 and 6 are decisions, not work.

**Caveat:** none of this is verified against a running site — Local's database
has been down all session. Every claim above is read from the source, and the
`reci_practice_focus` registration is the one I would most want to confirm
against a real install before acting on it.
