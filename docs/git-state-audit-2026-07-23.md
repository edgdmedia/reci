# Git State Audit - 2026-07-23

## Current Branch

- `development`

## Divergence From origin/main

- Ahead/behind result from `git rev-list --left-right --count origin/main...development` at initial audit time: `0 18`
- Local `development` is now `20` commits ahead of `origin/main` after adding git audit and branch policy commits
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

### Monitored Push Evidence

- Authenticated receive-pack request reaches GitHub successfully
- Observed request body size from `/tmp/reci-development-push.log`: `141986038` bytes
- No fast server rejection was observed after the oversized `.wpress` cleanup
- Current environment/tool timeout remains the practical blocker to confirming remote branch creation

## Development Commit Stack Ahead Of origin/main

1. `69eba12` feat: implement banded quiz feedback and polish event archive/single pages
2. `fc15028` feat: add render-chapter REST endpoint for preview updates
3. `a0ae288` feat: add postMessage listener to preview iframe for live updates
4. `5abde69` feat: postMessage-based live preview updates
5. `aef9aad` fix: add data-chapter-id for DOM targeting, register render-chapter endpoint, add debug logging
6. `456ad35` feat: update RECI spheres with brand colors and content images
7. `89ddd9b` refactor: combine awareness and action into single sphere name to match brand graphics
8. `99345ae` feat: split sphere name into short (name) and full (awareness) versions
9. `3fdd255` fix: use short name for sphere badges in listings
10. `f0d1c6f` docs: author single page redesign spec
11. `5c1f0d6` docs: reflection studio v2 design spec
12. `bdc9a52` feat(studio): scaffold reflection-studio module with Vite + TS + Vitest
13. `7df5953` feat(studio): add blueprint v2 TypeScript types
14. `34d9842` feat(studio): add registry system with buildRegistry + useRegistry
15. `e418f26` feat(studio): add builder Zustand store
16. `c503ad4` feat(studio): add field components and DynamicFieldRenderer
17. `bed7c62` feat: add dashboard notifications foundation
18. `19aaf71` feat: add dashboard personalization controls
19. `eb3b78b` docs: add development branch git audit
20. `ce59759` docs: define development branch policy

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
- Local `development` should be treated as canonical for active integration work
