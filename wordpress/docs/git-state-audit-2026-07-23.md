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

## Initial Conclusion

- Local `development` appears to be the true active development line
- `origin/main` is materially behind local development
- The repository needs branch stabilization and worktree restoration before release flow from `main` can be considered safe
