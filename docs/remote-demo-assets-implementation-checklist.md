# Remote Demo Assets Implementation Checklist

This checklist turns the remote demo assets plan into concrete repo work.

## Objective

Move heavy demo media, especially images, out of the theme bundle and into a remote GitHub-hosted distribution path while preserving a reliable setup/import experience.

## Phase 1: Prepare the manifest path

### Add or confirm manifest source
- Decide where the manifest lives:
  - GitHub Releases metadata path, or
  - raw GitHub-hosted JSON manifest

### Files to inspect/update
- `inc/admin/theme-setup-client.php`
- `inc/admin/demo-content.php`
- any existing functions such as:
  - `reci_fetch_remote_demo_manifest()`
  - `reci_remote_demo_content_sets()`

### Tasks
- define one canonical manifest URL source
- add manifest version handling
- add theme compatibility checks
- add graceful failure messaging when manifest is unavailable

## Phase 2: Separate local-required assets from demo-only assets

### Audit current local demo asset usage
- inspect `demo-content/images/`
- identify:
  - assets required for core rendering
  - assets used only by demo import

### Likely keep local
- logos
- favicon
- screenshot
- fallback thumbnail
- shell-required visuals

### Likely move remote first
- reflection gallery images
- article demo images
- event demo images
- course demo images
- podcast demo images
- video demo images
- quiz demo images

### Files to inspect/update
- `demo-content/images/`
- `inc/core/theme-setup.php`
- `templates/page/template-homepage.php`
- any helpers depending on demo image paths

## Phase 3: Add remote asset download + sideload support

### Files to update
- `inc/admin/demo-content.php`

### Add functions for
- downloading remote asset files
- unpacking remote zip bundles if using archives
- sideloading media into WordPress
- storing imported asset registry entries
- skipping already imported assets

### Likely function areas
- asset registry functions already present in `demo-content.php`
- import queue processing functions
- image group handling functions

### Tasks
- extend importer so a content group can come from remote
- support both local and remote asset sources during rollout
- keep import idempotent
- make failures recoverable

## Phase 4: Support remote image groups in import jobs

### Files to update
- `inc/admin/demo-content.php`

### Tasks
- allow manifest-declared groups to map to importer steps
- support remote group metadata such as:
  - URL
  - label
  - expected type
  - optional checksum
- persist group completion state

### Output expectation
- selecting a remote content set should enqueue image/media download steps before content insertion steps that depend on them

## Phase 5: Keep local content datasets, remote media first

### Keep local for phase 1
- `demo-content/pages.php`
- `demo-content/articles.php`
- `demo-content/events.php`
- `demo-content/podcasts.php`
- `demo-content/videos.php`
- `demo-content/courses.php`
- `demo-content/reflections.php`
- `demo-content/testimonials.php`
- `demo-content/glossary.php`
- `demo-content/resources.php`

### Why
- lowest-risk migration
- smaller scope
- avoids rewriting content ingestion and only moves heavy media first

## Phase 6: Improve setup UX around remote import

### Files to update
- `inc/admin/theme-setup-client.php`
- `inc/admin/theme-setup-wizard.php`

### Tasks
- make it clear whether a content set is remote-backed
- show manifest availability state
- show remote import failures clearly
- allow retry for failed remote groups
- distinguish baseline theme setup from optional rich demo import

## Phase 7: Add validation and compatibility checks

### Files to update
- `inc/admin/theme-setup-client.php`
- `inc/admin/demo-content.php`

### Tasks
- validate manifest version
- validate theme compatibility range
- reject incompatible manifest/content set versions cleanly
- surface actionable admin messages

## Phase 8: Prepare GitHub distribution structure

### External work
- create release bundle layout for remote demo assets

### Suggested structure
- `reflection-images.zip`
- `article-images.zip`
- `event-images.zip`
- `course-images.zip`
- `podcast-images.zip`
- `video-images.zip`
- `quiz-images.zip`
- `manifest.json`

### Requirements
- stable URLs
- version tags
- documented release process

## Phase 9: Fresh install QA

### Run two test modes

#### Test A: Remote available
- clean WP install
- activate theme
- install/activate expected plugins
- run setup
- import full remote-backed demo set
- confirm site reconstructs correctly

#### Test B: Remote unavailable
- simulate manifest or asset fetch failure
- confirm theme shell still works
- confirm setup does not fatal
- confirm admin gets clear retry guidance

## Phase 10: Optional later phases

### Later, if needed
- move larger content datasets remote too
- support multiple site presets
- support checksum verification
- support background/retry job recovery improvements

## File-level starting points

### Highest priority files
- `inc/admin/demo-content.php`
- `inc/admin/theme-setup-client.php`
- `inc/admin/theme-setup-wizard.php`
- `inc/core/theme-setup.php`

### Content/data files to review but not necessarily change first
- `demo-content/pages.php`
- `demo-content/articles.php`
- `demo-content/events.php`
- `demo-content/podcasts.php`
- `demo-content/videos.php`
- `demo-content/courses.php`
- `demo-content/reflections.php`

## Recommended implementation order

1. Manifest contract
2. Remote asset downloader/sideloader
3. Remote image group support in importer
4. Setup UI improvements
5. GitHub bundle publishing
6. Clean install QA

## Definition of done for phase 1

Phase 1 is done when:

- theme zip is lighter because bulk demo images are removed
- setup can fetch a remote manifest
- setup can import a remote-backed demo media set
- importer remains retry-safe
- site shell still works without remote demo success
- clean install passes with remote available
