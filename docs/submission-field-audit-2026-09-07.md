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

## Table 3 — Every post type's fields vs. what the submission form collects

Meta from `inc/content/meta-fields.php`. **Cat.** = catered for by the
submission form.

### Shared by every submitted type

| Post-type field | Submission field that fills it | Cat. |
|---|---|:--:|
| `post_title` | `title` | yes |
| `post_excerpt` | `abstract` | yes |
| `post_content` | the 7 concatenated blocks | partly — see Table 1 |
| `post_author` | logged-in user | yes |
| featured image | — | **no** |
| `reci_sphere` | `selectedSpheres` | yes |
| `reci_target_audience` | `targetAudience` | yes |
| `reci_practice_focus` | `practiceType` | written, but unregistered — Table 2 |
| `post_tag` | `keywords` goes to `post_content` instead | **no** |
| `reci_location` | `location` collected, never posted | **no** |
| `_reci_submission_content_link` | `contentLink` | yes |
| `_reci_submission_file_id` / `_url` | file upload | yes |
| `_reci_submission_file_description` | `fileDescription` | yes |

### `post` — blog, article, exhibit, other

| Post-type field | Submission field | Cat. |
|---|---|:--:|
| `_post_read_time_label` | — | **no** |
| `_post_featured_rank` | — | **no** |
| `_post_source_name` | — | **no** |
| `_post_source_url` | — | **no** |
| `_post_canonical_url` | — | **no** (`contentLink` is close but stored elsewhere) |

### `reci_podcast`

| Post-type field | Submission field | Cat. |
|---|---|:--:|
| `_reci_podcast_audio_url` | — | **no** |
| `_reci_podcast_video_url` | — | **no** |
| `_reci_podcast_duration_label` | — | **no** |
| `_reci_podcast_duration_secs` | — | **no** |
| `_reci_podcast_episode_number` | — | **no** |
| `_reci_podcast_season_number` | — | **no** |
| `_reci_podcast_transcript_url` | — | **no** |
| `_reci_podcast_spotify_url` | — | **no** |
| `_reci_podcast_apple_url` | — | **no** |
| `reci_show` taxonomy | — | **no** |

**0 of 10.** A submitted podcast has no audio.

### `reci_video`

| Post-type field | Submission field | Cat. |
|---|---|:--:|
| `_reci_video_url` | — | **no** |
| `_reci_video_platform` | — | **no** |
| `_reci_video_external_id` | — | **no** |
| `_reci_video_duration_label` | — | **no** |
| `_reci_video_duration_secs` | — | **no** |

**0 of 5.** A submitted video has no video.

### `reci_assessment`

*Correction to my earlier note: this type does have a full field set — I said it
had none, and that was wrong.*

| Post-type field | Submission field | Cat. |
|---|---|:--:|
| `_reci_assessment_type` | — | **no** |
| `_reci_assessment_questions` | — | **no** |
| `_reci_assessment_result_ranges` | — | **no** |
| `_reci_assessment_intro` | — | **no** |
| `_reci_assessment_instructions` | — | **no** |
| `_reci_assessment_estimated_time` | — | **no** |
| `_reci_assessment_completion_title` | — | **no** |
| `_reci_assessment_completion_message` | — | **no** |

**0 of 8.** A submitted assessment has no questions, so it cannot be taken.
This is a repeating question-builder — realistically staff work, not something
to put in the submission wizard.

### `reci_document`

| Post-type field | Submission field | Cat. |
|---|---|:--:|
| *no metabox and no `_reci_document_*` meta exists* | — | n/a |

The type is submittable and the file upload is its real payload, so this is the
one type the form already serves. But none of its three taxonomies are
registered for it (Table 2).

### `reci_course` and `reci_event` — not submittable

Both have complete field sets and neither appears in
`reci_media_hub_submission_type_map()`, so nothing can reach them through the
form at all.

| `reci_course` | `reci_event` |
|---|---|
| `_reci_course_start_date` | `_reci_event_start_date` / `_end_date` |
| `_reci_course_duration_weeks` | `_reci_event_start_time` / `_end_time` |
| `_reci_course_lessons` | `_reci_event_timezone` |
| `_reci_course_level` | `_reci_event_is_virtual` |
| `_reci_course_format` | `_reci_event_location_name` / `_location_address` |
| `_reci_course_fee_label` | `_reci_event_registration_url` |
| `_reci_course_enrollment_url` | `_reci_event_cta_label` |

### `reci_reflection` — not submittable

`_reci_reflection_blueprint` and friends are built by the reflection builder, not
by a form. Correctly excluded.

---

## Table 4 — The other direction: submission fields with no post-type home

| Submission field | Wants to be | Currently |
|---|---|---|
| `evidenceBasis` | new meta | `post_content` text only |
| `processOrientation` | new meta | `post_content` text only |
| `equityFocus` | new meta | `post_content` text only |
| `keywords` | `post_tag` | `post_content` text only |
| `location` | `reci_location` | collected, never posted |
| `bio`, `role`, `organization`, `website` | the author's `reci_author` profile | `_reci_submission_*` meta on the post, duplicating the profile |

---

## Scorecard

| Type | Type-specific fields | Collected | Gap |
|---|---:|---:|---|
| `post` | 5 | 0 | all |
| `reci_podcast` | 10 | 0 | all |
| `reci_video` | 5 | 0 | all |
| `reci_assessment` | 8 | 0 | all |
| `reci_document` | 0 | n/a | none |
| `reci_course` | 7 | — | not submittable |
| `reci_event` | 10 | — | not submittable |

Only `reci_document` is fully served, and only because it has no fields of its
own. Every other submittable type arrives empty and is completed by hand in
wp-admin.

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
