# Development Branch Stabilization And Remote Sync Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stabilize the local `development` branch, restore the dirty worktree safely, and establish a pushable remote `development` branch without risking `main` or losing in-progress local work.

**Architecture:** Treat the local repository as the current development source of truth, but stop ad hoc git recovery. First restore and inventory the local worktree, then separate committed branch history from uncommitted working state, then publish a clean remote `development` branch with explicit branch tracking and a documented promotion path to `main` for releases.

**Tech Stack:** Git, GitHub remote over HTTPS, WordPress theme repository, local patch backups, shell-based repository inspection

---

## File Structure

**Create** `docs/superpowers/plans/2026-07-23-development-branch-stabilization-and-remote-sync.md`
- This execution plan.

**Create** `docs/git-state-audit-2026-07-23.md`
- Snapshot of current branch divergence, worktree counts, blocked push findings, and recovery artifacts.

**Create** `docs/development-branch-policy.md`
- Documented branch policy: `development` as primary integration branch, `main` as release-only branch.

**Create** `scripts/git-audit-development.sh`
- Repeatable audit helper for branch divergence, worktree counts, and remote status.

**Create** `scripts/git-restore-binary-gaps.sh`
- Optional helper to identify and restore missing binary assets from a trusted source once that source is defined.

---

### Task 1: Capture Current Git State Before More Recovery

**Files:**
- Create: `docs/git-state-audit-2026-07-23.md`
- Create: `scripts/git-audit-development.sh`

- [ ] **Step 1: Create a repeatable git audit script**

Create `scripts/git-audit-development.sh`.

```bash
#!/usr/bin/env bash
set -euo pipefail

echo "== Branch =="
git branch --show-current

echo "== Ahead/behind vs origin/main =="
git rev-list --left-right --count origin/main...development

echo "== Commit count ahead of origin/main =="
git log --oneline origin/main..development | wc -l

echo "== File count diff vs origin/main =="
git diff --name-only origin/main..development | wc -l

echo "== Worktree counts =="
echo -n "total: "
git status --short | wc -l
echo -n "deleted: "
git status --short | grep '^ D ' | wc -l || true
echo -n "modified: "
git status --short | grep '^ M ' | wc -l || true
echo -n "untracked: "
git status --short | grep '^?? ' | wc -l || true

echo "== Remote heads =="
git ls-remote --heads origin
```

- [ ] **Step 2: Make the audit script executable**

Run: `chmod +x scripts/git-audit-development.sh`

Expected: no output.

- [ ] **Step 3: Run the audit script and record findings**

Run: `./scripts/git-audit-development.sh`

Record the output in `docs/git-state-audit-2026-07-23.md` with sections for:
- current branch
- divergence from `origin/main`
- worktree status counts
- remote branch status
- backup artifact paths

Use this exact markdown shell:

```md
# Git State Audit — 2026-07-23

## Current Branch

## Divergence From origin/main

## Worktree Counts

## Remote Branches

## Recovery Artifacts
- `/var/folders/np/s21vqrvn0_bb55lb9mp_qcxc0000gn/T/opencode/reci-working-tree-backup.patch`
- `/var/folders/np/s21vqrvn0_bb55lb9mp_qcxc0000gn/T/opencode/reci-working-tree-status.txt`
```

- [ ] **Step 4: Commit the audit artifacts**

Run:

```bash
git add scripts/git-audit-development.sh docs/git-state-audit-2026-07-23.md
git commit -m "docs: add development branch git audit"
```

### Task 2: Restore Worktree Integrity Before Any More Push Attempts

**Files:**
- Create: `scripts/git-restore-binary-gaps.sh`
- Modify: `docs/git-state-audit-2026-07-23.md`

- [ ] **Step 1: Identify which remaining worktree changes are deletions vs intentional refactors**

Run:

```bash
git status --short > /tmp/reci-status.txt
grep '^ D ' /tmp/reci-status.txt > /tmp/reci-deleted.txt || true
grep '^ M ' /tmp/reci-status.txt > /tmp/reci-modified.txt || true
grep '^?? ' /tmp/reci-status.txt > /tmp/reci-untracked.txt || true
```

Expected: the three files exist and separate deleted/modified/untracked paths.

- [ ] **Step 2: Create a helper script for binary restoration inventory**

Create `scripts/git-restore-binary-gaps.sh`.

```bash
#!/usr/bin/env bash
set -euo pipefail

status_file="${1:-/tmp/reci-deleted.txt}"

if [[ ! -f "$status_file" ]]; then
  echo "Missing status file: $status_file" >&2
  exit 1
fi

echo "Potentially missing binary/media assets:"
grep -E '\.(png|jpg|jpeg|gif|svg|pdf|mp3|m4a|aiff|avif|wpress)$' "$status_file" || true
```

- [ ] **Step 3: Make the binary inventory script executable and run it**

Run:

```bash
chmod +x scripts/git-restore-binary-gaps.sh
./scripts/git-restore-binary-gaps.sh
```

Expected: list of deleted binary/media assets that were not recoverable from the text patch.

- [ ] **Step 4: Append missing binary findings to the audit doc**

Add this section to `docs/git-state-audit-2026-07-23.md`.

```md
## Missing Binary/Media Gaps

- List representative binary files that did not restore from the patch
- Identify whether they are required runtime assets, sample media, or archival/reference assets
- Mark each as one of:
  - required before development continues
  - required before release only
  - non-blocking archive/reference material
```

- [ ] **Step 5: Do not restore binaries from git blindly**

Run no destructive restore command here. Instead, pause and classify the missing assets first.

Expected: no files overwritten from old history without intentional review.

### Task 3: Separate Canonical Development History From Release History

**Files:**
- Create: `docs/development-branch-policy.md`

- [ ] **Step 1: Record the branch policy so future git actions are consistent**

Create `docs/development-branch-policy.md`.

```md
# Development Branch Policy

## Branch Roles
- `development`: primary shared integration branch for ongoing work
- `main`: stable release branch only
- feature branches: short-lived branches merged into `development`

## Promotion Flow
1. Work lands on feature branch
2. Feature branch merges into `development`
3. `development` is verified
4. `development` merges into `main`
5. Theme release/package is created from `main`

## Rules
- Do not package production releases from feature branches
- Do not push unreviewed recovery or migration history to `main`
- Use `development` for day-to-day pushes once remote tracking is established
```

- [ ] **Step 2: Commit the branch policy doc**

Run:

```bash
git add docs/development-branch-policy.md
git commit -m "docs: define development branch policy"
```

### Task 4: Build A Pushable Remote Development Branch Intentionally

**Files:**
- No file edits required until the branch source decision is finalized.

- [ ] **Step 1: Decide whether local `development` or `origin/main` is canonical**

Use the audit results to answer:
- Is the local `development` branch the true active product state?
- Are the `18` commits ahead of `origin/main` all intended and valuable?

Expected: explicit yes/no answer written in the audit doc.

- [ ] **Step 2: If local development is canonical, push it with a long-running monitored command**

Run:

```bash
git push origin development:development
```

Expected:
- remote branch `origin/development` is created
- no `.wpress` large-file rejection occurs

- [ ] **Step 3: If the push still hangs, capture exact transfer diagnostics**

Run:

```bash
GIT_TRACE=1 GIT_CURL_VERBOSE=1 git push origin development:development 2>&1 | tee /tmp/reci-development-push.log
```

Expected:
- `/tmp/reci-development-push.log` contains the exact HTTP and pack upload sequence

- [ ] **Step 4: If push payload is still too large, create a reduced remote-sync branch**

Run:

```bash
git checkout -b development-sync-candidate origin/main
```

Expected: a fresh branch from remote main exists for selective migration.

- [ ] **Step 5: Cherry-pick only the commits that are confirmed product-critical**

Start with the most recent clearly intentional commits first.

```bash
git cherry-pick bed7c62 19aaf71
```

If they fail, stop and document the structural mismatch rather than forcing conflict resolution blindly.

Expected:
- either clean cherry-picks
- or a documented conflict report proving manual porting is required

### Task 5: Re-establish Clean Branch Tracking Once Remote Development Exists

**Files:**
- No source file changes required.

- [ ] **Step 1: Confirm remote `development` exists**

Run:

```bash
git ls-remote --heads origin development
```

Expected: one line for `refs/heads/development`.

- [ ] **Step 2: Set local `development` to track remote `development`**

Run:

```bash
git branch --set-upstream-to=origin/development development
```

Expected: local `development` now tracks `origin/development` instead of `origin/main`.

- [ ] **Step 3: Verify branch tracking state**

Run:

```bash
git branch -vv
```

Expected: `development` shows `[origin/development]`.

### Task 6: Decide How To Restore Full Local Worktree After Remote Sync

**Files:**
- Modify: `docs/git-state-audit-2026-07-23.md`

- [ ] **Step 1: Record the current recovery limitation**

Add this note to the audit doc.

```md
## Recovery Limitation

The saved patch restored text/code changes but could not fully restore binary assets.
Any remaining deleted binary/media files must be restored from a trusted source outside the text patch.
```

- [ ] **Step 2: Classify the remaining 374 worktree changes into buckets**

Add these buckets to the audit doc:

```md
## Remaining Worktree Buckets
- dashboard/personalization work
- homepage/editorial presentation work
- reflection system/studio work
- legacy file deletions vs structural moves
- binary/media restoration gaps
```

- [ ] **Step 3: Do not auto-commit the remaining dirty worktree**

Expected: no bulk `git add .` or mass commit.

### Task 7: Final Verification And Handoff

**Files:**
- Modify: `docs/git-state-audit-2026-07-23.md`

- [ ] **Step 1: Re-run the git audit script at the end**

Run: `./scripts/git-audit-development.sh`

Expected: updated branch and remote state after stabilization work.

- [ ] **Step 2: Update the audit doc with final outcomes**

Add:

```md
## Final Outcome
- whether remote `development` was created
- whether local `development` tracks remote `development`
- whether the worktree is fully restored or only partially restored
- what still remains before release-from-main is safe
```

- [ ] **Step 3: Commit the stabilization docs and scripts**

Run:

```bash
git add docs/git-state-audit-2026-07-23.md docs/development-branch-policy.md scripts/git-audit-development.sh scripts/git-restore-binary-gaps.sh
git commit -m "docs: add development stabilization plan artifacts"
```

## Self-Review

**Spec coverage:**
- Local branch stabilization: covered in Tasks 1, 2, and 6.
- Remote sync strategy: covered in Tasks 4 and 5.
- Branch workflow hardening: covered in Task 3.
- Evidence-based final state reporting: covered in Task 7.

**Placeholder scan:**
- No `TODO`/`TBD` placeholders remain in the action steps.

**Type consistency:**
- Branch names `development`, `main`, and `origin/development` are used consistently.
- Recovery artifact paths are consistent with the earlier backup step.
