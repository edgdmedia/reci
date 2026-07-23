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
