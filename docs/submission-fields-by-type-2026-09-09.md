# Submission fields × content type — 2026-09-09

Read from the running site: `reci_media_hub_submission_type_map()`,
`reci_submission_type_fields()`, `reci_media_hub_submission_narrative_fields()`,
and the two surfaces that render them — the submit wizard
(`sample/recmh-submission.jsx`) and the dashboard editor
(`template-parts/dashboard/edit-content-form.php`).

## The seven front-end types

| Type | Post type | Written here, or catalogued? |
|---|---|---|
| `blog` | `post` | usually **written here** |
| `article` | `post` | usually **elsewhere first** |
| `exhibit` | `post` | either |
| `other` | `post` | either |
| `video` | `reci_video` | hosted elsewhere (YouTube/Vimeo) |
| `podcast` | `reci_podcast` | hosted elsewhere (audio file) |
| `document` | `reci_document` | the upload **is** the content |

## The table

Legend: **✓** needed · **—** not applicable · **?** only if it came from elsewhere ·
**✗ shown but shouldn't be**

| Field | Where from | blog | article | exhibit | other | video | podcast | document |
|---|---|---|---|---|---|---|---|---|
| Title | shared | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Summary / abstract | shared | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Details (body, `wp_editor`) | editor only | ✓ | ✓ | ✓ | ✓ | ? | ? | ? |
| Evidence Basis | narrative | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Process Orientation | narrative | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Racial Equity Focus | narrative | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Spheres | taxonomy | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Practice Focus | taxonomy | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Target Audience | taxonomy | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Location | taxonomy | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Keywords → `post_tag` | shared | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Content Link** | shared | **✗** | ? | ? | ? | **✗ dup** | **✗ dup** | ? |
| File upload | shared | — | — | ? | ? | — | ? | ✓ |
| File description | shared | — | — | ? | ? | — | ? | ✓ |
| **Original Publication** | type | **✗** | ? | **✗** | **✗** | — | — | — |
| **Original URL** | type | **✗** | ? | **✗** | **✗** | — | — | — |
| **Canonical URL** | type | **✗** | ? | **✗** | **✗** | — | — | — |
| Video URL *(required)* | type | — | — | — | — | ✓ | — | — |
| Video Duration | type | — | — | — | — | ✓ | — | — |
| Audio URL *(required)* | type | — | — | — | — | — | ✓ | — |
| Audio Duration | type | — | — | — | — | — | ✓ | — |
| Episode Number | type | — | — | — | — | — | ✓ | — |
| Season Number | type | — | — | — | — | — | ✓ | — |
| Transcript URL | type | — | — | — | — | — | ✓ | — |
| Show | taxonomy | — | — | — | — | — | ✓ | — |

## What's actually wrong

**1. The reprint trio is keyed by post type, not by meaning.**
`reci_submission_type_fields()` assigns one `$source` array to `article`, `blog`,
`exhibit` and `other` — every type that maps to `post`. So a blog post written
from scratch is asked for its Original Publication, Original URL and Canonical
URL. That is the field you saw.

Provenance is not a property of the *type*. An article is usually a reprint; a
blog post usually isn't; an exhibit could be either. Keying it off the type will
be wrong for somebody whichever way it is set.

**Recommended:** ask once, and show the trio only if the answer is yes —
a "Was this published somewhere else first?" toggle on the types that map to
`post`, collapsed by default. Off for `blog`, on for `article`, remembered per
post. No field is lost; it stops being noise for original work.

**2. Content Link is rendered unconditionally in the editor.**
It is meaningless for a post written in place, and for video and podcast it
duplicates the required Video URL / Audio URL, so there are two boxes for one
fact. It belongs in the same provenance section as the trio, and should be
suppressed entirely for `video` and `podcast`.

**3. `document` has no type fields at all**, which is right — the upload is the
content — but the shared file upload is not marked required for it, so a
Resource can be submitted with nothing attached.

**4. Podcast audio can be given twice**: the shared file upload and the required
Audio URL both feed the episode. One should fill the other.

## Suggested order of work

1. Provenance toggle; move the trio and Content Link behind it. Fixes the
   reported problem and items 1 and 2 together.
2. Suppress Content Link for `video` and `podcast`.
3. Require the upload for `document`; let a podcast upload fill Audio URL.

Nothing here is applied. This is the map.
