# Git State Audit - 2026-07-23

## Current Branch

- `development`

## Divergence From origin/main

- Ahead/behind result from `git rev-list --left-right --count origin/main...development`: `0 18`
- Local `development` is `18` commits ahead of `origin/main`
- `227` files differ between `origin/main` and `development`

## Worktree Counts

- Total changed entries: `376`
- Deleted: `295`
- Modified: `78`
- Untracked: `3`

## Remote Branches

- `origin/main`
- `origin/feature/reflection-renderer`
- No remote `development` branch exists yet

## Recovery Artifacts

- `/var/folders/np/s21vqrvn0_bb55lb9mp_qcxc0000gn/T/opencode/reci-working-tree-backup.patch`
- `/var/folders/np/s21vqrvn0_bb55lb9mp_qcxc0000gn/T/opencode/reci-working-tree-status.txt`

## Push Findings So Far

- Initial GitHub rejection due to oversized file `localhost-20260320-165012-4ovndqprbb61.wpress` was addressed by local history rewrite
- Auth to GitHub is valid
- Push attempts now reach authenticated upload stage
- The current push attempt is sending a large pack (`~142 MB`) and has not completed successfully from this shell

## Missing Binary/Media Gaps

- Representative currently-detected binary/media deletion from worktree status: `reflection-gallery/sample/reflection-gallery-sample.svg`
- Current evidence suggests most remaining deleted paths are source/text files rather than binary media
- Classification:
  - `reflection-gallery/sample/reflection-gallery-sample.svg`: non-blocking archive/reference material unless the sample gallery is still part of active workflows

## Recovery Limitation

The saved patch restored text/code changes but could not fully guarantee binary asset restoration. Any remaining deleted binary/media files should be restored from a trusted source only after they are classified as runtime-critical or release-critical.

## Remaining Worktree Buckets

- dashboard/personalization work
- homepage/editorial presentation work
- reflection system/studio work
- legacy file deletions vs structural moves
- binary/media restoration gaps

## Initial Conclusion

- Local `development` appears to be the true active development line
- `origin/main` is materially behind local development
- The repository needs branch stabilization and worktree restoration before release flow from `main` can be considered safe
