# Where the data lives — 2026-09-09

Read from the running database, not from code.

## Custom tables

| Table | Rows | Holds |
|---|---:|---|
| `wp_reci_assessment_submissions` | 0 | Quiz/assessment results |
| `wp_reci_email_log` | 70 | Every wp_mail() attempt: recipient, subject, heading, transport, status, error |
| `wp_reci_journals` | 0 | Reflection journal entries |
| `wp_reci_notifications` | 71 | In-app bell notifications per user |

## Collaborator application (`reci_collab_app`)

Core row in `wp_posts`; everything else in `wp_postmeta`.

| Meta key | Example | Notes |
|---|---|---|
| `_reci_collaborator_user_id` | 59 | |
| `_reci_submission_first_name` | Olalekan | |
| `_reci_submission_last_name` | Owonikoko | |
| `_reci_submission_email` | olalekan@stanforteedge.com | |
| `_reci_collaborator_affiliated_with_pitt` | No | |
| `_reci_collaborator_pitt_affiliation` | D | |
| `_reci_collaborator_department` | dd | |
| `_reci_submission_organization` | ddd | |
| `_reci_submission_role` | reee | |
| `_reci_submission_bio` | sffs sdsdssd ssdsdsd | |
| `_reci_submission_website` |  | |
| `_reci_collaborator_social_handles` |  | |
| `_reci_collaborator_membership_objective` | sfds fssfs | |
| `_reci_collaborator_affiliation_term` | Alumni | |
| `_reci_collaborator_expertise_terms` | ["Behavioral Health","Economics","Education" | |
| `_reci_collaborator_application_status` | rejected | |
| `_thumbnail_id` | 1542 | |
| `_reci_collaborator_profile_image_id` | 1542 | |
| `_reci_announced_published` | 2026-09-09 11:57:32 | |
| `_reci_notified_approved` | 2026-09-09 11:57:32 | |

## User meta written by the theme

- `_reci_collaborator_status`
- `_reci_is_verified`
- `reci_affiliated_with_pitt`
- `reci_affiliation_term`
- `reci_department`
- `reci_expertise_terms`
- `reci_followed_collaborators`
- `reci_followed_practice_focus`
- `reci_followed_spheres`
- `reci_followed_target_audience`
- `reci_followed_topics`
- `reci_journal_default_privacy`
- `reci_notify_collaborator_application_status`
- `reci_notify_comment_reply`
- `reci_notify_followed_collaborators`
- `reci_notify_personalized_content`
- `reci_notify_submission_approved`
- `reci_notify_submission_rejected`
- `reci_notify_weekly_digest`
- `reci_pitt_affiliation`
- `reci_social_handles`

## Options

- `_transient_reci_pending_submission_count` (1 bytes)
- `_transient_timeout_reci_pending_submission_count` (10 bytes)
- `reci_affiliation_children` (6 bytes)
- `reci_dashboard_routes_version` (32 bytes)
- `reci_db_version` (5 bytes)
- `reci_demo_installed` (1 bytes)
- `reci_demo_slugs` (9426 bytes)
- `reci_expertise_children` (6 bytes)
- `reci_location_children` (6 bytes)
- `reci_media_hub_sdgs_seeded` (1 bytes)
- `reci_media_hub_shows_seeded` (1 bytes)
- `reci_media_hub_spheres_seeded` (1 bytes)
- `reci_media_hub_taxonomy_terms_seeded` (1 bytes)
- `reci_pages` (685 bytes)
- `reci_practice_focus_children` (6 bytes)
- `reci_rewrite_version` (15 bytes)
- `reci_roles_version` (5 bytes)
- `reci_show_children` (6 bytes)
- `reci_sphere_children` (6 bytes)
- `reci_theme_settings` (1252 bytes)
- `reci-default-logo` (2 bytes)
- `reci-default-partner-logo` (2 bytes)
- `theme_mods_reci-media-hub` (271 bytes)
