# Client Distribution Notes

## Must Ship In Theme Zip
- `assets/`
- `demo-content/` (until remote demo content is enabled)
- `inc/`
- `modules/`
- `template-parts/`
- `templates/`
- root theme bootstrap files (`style.css`, `functions.php`, etc.)

## Must Not Ship
- `.git/`
- `.github/`
- `node_modules/`
- `dist/`
- `scripts/`
- local-only copies like `wordpress/` and `reci-wordpress/`
- archival/reference media folders not needed at runtime

## Client Install Flow
1. Install the theme zip in WordPress.
2. Activate the theme.
3. Open the RECI Theme Setup screen.
4. Verify required plugins.
5. Import starter/demo content.
6. Configure logo, colors, and other baseline branding.

## Maintainer Release Flow
1. Work on `development`.
2. Merge `development` into `main`.
3. Packaging workflow runs on `main`.
4. Deploy workflow runs on `main` for your own server.
5. Client theme update metadata can point to the packaged release artifact.

## Theme Update Notice Requirements
- A remote update manifest URL.
- A downloadable zip URL for the latest client theme release.
- Semantic version comparison against the installed theme version.
