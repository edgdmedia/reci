# Release Automation

## Overview

The theme uses GitHub Releases for distribution. When you push a version tag, GitHub Actions automatically builds the zip and creates a release. Client sites check the GitHub Releases API and show a dashboard notice when an update is available.

## How to Release

### 1. Merge to main
```bash
git checkout main
git merge development
git push origin main
```

### 2. Bump version + tag + push
```bash
BUMP_VERSION=1 bash scripts/package-theme.sh
git add style.css
git commit -m "chore: bump version to 0.4.6"
git tag v0.4.6
git push origin main --tags
```

### 3. Done
GitHub Actions automatically:
- Packages the theme zip
- Creates a GitHub Release with the zip attached

## Client Update Flow

1. Client's WP dashboard checks `https://api.github.com/repos/edgdmedia/reci/releases/latest`
2. If remote version > installed version → shows admin notice
3. Client clicks "Download from GitHub" → downloads zip
4. Client uploads via Appearance → Themes → Add New → Upload Theme

## Files

| File | Purpose |
|------|---------|
| `.github/workflows/release.yml` | Builds zip and creates GitHub Release on tag push |
| `inc/features/theme-updates.php` | Checks GitHub API and shows dashboard notices |
| `scripts/package-theme.sh` | Packages theme into zip (used by workflow) |

## API Endpoint

```
https://api.github.com/repos/edgdmedia/reci/releases/latest
```

Returns latest release with:
- `tag_name` — version tag (e.g. `v0.4.5`)
- `assets[].browser_download_url` — zip download URL
- `html_url` — release page URL
- `body` — changelog/release notes

## Troubleshooting

### Release didn't create
- Check tag format: must be `v0.0.0` (e.g. `v0.4.6`)
- Check workflow runs: `gh run list --workflow=release.yml`

### Dashboard notice not showing
- Check API is accessible: `curl https://api.github.com/repos/edgdmedia/reci/releases/latest`
- Check installed version in WP → Appearance → Themes → Reci Media Hub → Details

### Version mismatch
- Ensure `style.css` version matches the tag
- The workflow validates version matches tag before creating release
