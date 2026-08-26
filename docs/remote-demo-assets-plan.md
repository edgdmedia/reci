# Remote Demo Assets Plan

## Goal

Reduce the shipped theme size by moving heavy demo media, especially images, out of the theme bundle while keeping the site functional and setup-friendly.

## Desired outcome

- smaller theme package
- lighter updates
- demo media versioned separately from theme code
- setup wizard can still import a full starter site
- theme remains usable even if remote demo import fails

## Content split

### Keep local in the theme
- assets required for normal theme rendering
- logos used by the shell
- favicon and screenshot
- fallback thumbnail/default images
- any small assets required so key templates do not break before setup

### Move remote
- bulk demo images for articles
- bulk demo images for podcasts
- bulk demo images for videos
- bulk demo images for events
- bulk demo images for courses
- bulk demo images for quizzes
- bulk demo images for reflections
- large optional starter-content media packs

## Recommended distribution model

Use GitHub Releases assets for remote demo bundles.

Why:
- versioned
- stable URLs per release
- cleaner separation from source code
- easier rollback
- better for packing many demo assets into known bundles

Alternative:
- dedicated GitHub repo folder/branch with raw files

This is workable, but less structured for release/version compatibility.

## Manifest design

The theme should fetch a remote manifest before starting import.

### Manifest should contain
- manifest version
- demo content version
- compatible theme version range
- content set definitions
- asset group definitions
- download URLs
- optional checksums
- optional asset sizes

### Example structure

```json
{
  "manifest_version": 1,
  "demo_version": "2026-08-26",
  "theme_compatibility": {
    "min": "0.4.8",
    "max": "0.5.x"
  },
  "content_sets": [
    {
      "id": "full-site",
      "label": "Full Site Demo",
      "groups": [
        "images-reflections",
        "images-articles",
        "images-events",
        "images-courses",
        "images-podcasts",
        "images-videos",
        "images-quizzes",
        "pages",
        "content-core"
      ]
    }
  ],
  "groups": {
    "images-reflections": {
      "label": "Reflection Images",
      "type": "release-asset",
      "url": "https://github.com/.../reflection-images.zip"
    }
  }
}
```

## Importer behavior

### Setup flow
1. Theme setup fetches manifest
2. User selects content set
3. Importer resolves required groups
4. Importer downloads assets
5. Importer sideloads media into WordPress
6. Importer creates or updates demo posts/pages
7. Importer stores progress and results

### Must-have behaviors
- idempotent imports
- retry-safe media sideloading
- skip already imported content/assets
- activity log during import
- partial failure reporting
- resumable import job where possible

## Failure strategy

The theme must remain usable even if remote demo import fails.

### If manifest fetch fails
- do not break setup
- show a clear error
- allow retry
- keep local theme pages/settings usable

### If asset download fails
- log failed group/file
- continue where reasonable
- surface incomplete import state clearly

### If remote demo is unavailable
- site shell should still work
- setup wizard should still complete baseline configuration
- only demo richness should be blocked

## Local fallback policy

Keep local assets for:
- shell branding
- fallback thumbnail
- placeholder/failsafe visuals required for rendering

Do not rely on remote assets for:
- header/footer essentials
- required default media that prevents broken layouts

## Versioning policy

Each manifest should declare compatibility with theme versions.

This avoids:
- importing outdated demo structures into newer templates
- mismatched content expectations
- broken image or page references after theme evolution

## Suggested implementation phases

### Phase 1
- move bulk demo images remote
- keep content datasets local
- add manifest fetch and remote image download support

### Phase 2
- bundle remote demo images by content group
- add checksum validation
- add import resume/retry improvements

### Phase 3
- optionally move larger starter-content datasets remote too
- support multiple demo site presets

## What to change in this repo

### Code
- extend remote manifest support already referenced in setup
- teach importer to download/sideload remote bundles or files
- preserve current local import path as fallback during rollout

### Content organization
- separate theme-required assets from demo-only media
- prepare release bundle structure for remote assets

### Setup UX
- make remote content set selection explicit
- show when demo import is remote-based
- clearly distinguish baseline setup from optional rich demo import

## Validation checklist for rollout

- clean install with internet access succeeds
- clean install with remote demo unavailable still yields usable theme shell
- partial remote import failures are clearly reported
- repeated imports do not duplicate content
- theme updates do not require re-downloading unchanged demo content unnecessarily

## Recommendation

Start with images only.

That gives the biggest package-size win with the lowest architectural risk.

Keep:
- local content structure
- local setup logic
- local fallback assets

Move:
- only heavy demo media first

Then validate clean-install behavior before moving anything more ambitious.
