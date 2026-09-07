# Client Theme Distribution Setup And Updates Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Turn the RECI theme into a client-installable product with a lightweight packaged zip, guided setup wizard, remote demo content installation, and custom theme update notices.

**Architecture:** Keep the packaged theme lightweight by shipping only runtime code, setup/import logic, and update-check logic. Move heavy demo payloads and release artifacts to remote-hosted endpoints, then let the theme fetch them on demand through an admin setup wizard and a remote update manifest.

**Tech Stack:** WordPress PHP theme, admin pages, WP-CLI-compatible plugin install flows, GitHub Releases or raw GitHub-hosted JSON/assets, custom WordPress theme update filters, GitHub Actions for packaging

---

## File Structure

**Modify** `scripts/package-theme.sh`
- Finalize packaging exclusions for lightweight client distribution.

**Modify** `.github/workflows/package-theme.yml`
- Optionally publish release artifacts or expose version metadata for updates.

**Create** `inc/admin/theme-setup-client.php`
- Client-focused setup wizard controller, separate from internal/server setup flows.

**Create** `inc/features/remote-demo-content.php`
- Remote manifest fetch, validation, and import orchestration.

**Create** `inc/features/theme-updates.php`
- Custom update-check logic for the theme.

**Modify** `inc/init.php`
- Load the new setup, remote demo, and update feature files.

**Create** `docs/client-distribution.md`
- Human-readable install/setup/update documentation for clients and maintainers.

**Create** `docs/demo-content-manifest.schema.json`
- Reference schema for the remote demo manifest.

**Create** `scripts/build-demo-manifest.sh`
- Helper to generate/update remote demo content metadata.

---

### Task 1: Finalize Lightweight Packaging Boundaries

**Files:**
- Modify: `scripts/package-theme.sh`
- Create: `docs/client-distribution.md`

- [ ] **Step 1: Review current package inclusions against client runtime needs**

Document in `docs/client-distribution.md` the folders that must remain in the client package.

```md
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
```

- [ ] **Step 2: Add a dedicated exclusion section for remote-demo mode**

Extend `scripts/package-theme.sh` comments to mark what can be removed once remote demo content is enabled.

```bash
# Optional future exclusions once remote demo content is live:
# - demo-content/
# - large bundled media not required at runtime
```

- [ ] **Step 3: Re-run local packaging to verify the zip still builds**

Run:

```bash
./scripts/package-theme.sh
```

Expected: a zip appears in `dist/` with the current committed version.

- [ ] **Step 4: Commit packaging-boundary docs/comments**

Run:

```bash
git add scripts/package-theme.sh docs/client-distribution.md
git commit -m "docs: define client packaging boundaries"
```

### Task 2: Add A Client Setup Wizard For First-Time Theme Activation

**Files:**
- Create: `inc/admin/theme-setup-client.php`
- Modify: `inc/init.php`

- [ ] **Step 1: Create a lightweight client setup page registration**

Create `inc/admin/theme-setup-client.php`.

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'reci_register_client_setup_page' );

function reci_register_client_setup_page(): void {
	add_submenu_page(
		'themes.php',
		__( 'RECI Theme Setup', 'reci-media-hub' ),
		__( 'RECI Theme Setup', 'reci-media-hub' ),
		'manage_options',
		'reci-client-setup',
		'reci_render_client_setup_page'
	);
}
```

- [ ] **Step 2: Render the setup wizard shell with explicit steps**

In the same file, add a simple shell that lists:
- requirements
- plugin installation
- demo content import
- branding
- finish

```php
function reci_render_client_setup_page(): void {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'RECI Theme Setup', 'reci-media-hub' ); ?></h1>
		<p><?php esc_html_e( 'Use this guided setup to install required plugins, import demo content, and configure the theme.', 'reci-media-hub' ); ?></p>
	</div>
	<?php
}
```

- [ ] **Step 3: Load the new setup controller from `inc/init.php`**

Add:

```php
'/inc/admin/theme-setup-client.php',
```

- [ ] **Step 4: Verify syntax for the setup controller**

Run:

```bash
php -l inc/admin/theme-setup-client.php
php -l inc/init.php
```

Expected: `No syntax errors detected`.

### Task 3: Add Required Plugin Checks And Guided Installation

**Files:**
- Modify: `inc/admin/theme-setup-client.php`

- [ ] **Step 1: Define the required plugin list centrally**

Add this helper:

```php
function reci_required_plugins(): array {
	return [
		'classic-editor' => 'Classic Editor',
		'ewww-image-optimizer' => 'EWWW Image Optimizer',
		'really-simple-ssl' => 'Really Simple Security',
		'all-in-one-wp-security-and-firewall' => 'All-in-One WP Security',
		'wordpress-seo' => 'Yoast SEO',
		'wp-super-cache' => 'WP Super Cache',
	];
}
```

- [ ] **Step 2: Add installed/active status detection**

```php
function reci_plugin_status_map(): array {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	$statuses = [];
	foreach ( reci_required_plugins() as $slug => $label ) {
		$plugin_file = null;
		foreach ( array_keys( get_plugins() ) as $file ) {
			if ( str_starts_with( $file, $slug . '/' ) ) {
				$plugin_file = $file;
				break;
			}
		}
		$statuses[ $slug ] = [
			'label' => $label,
			'installed' => null !== $plugin_file,
			'active' => $plugin_file ? is_plugin_active( $plugin_file ) : false,
			'file' => $plugin_file,
		];
	}
	return $statuses;
}
```

- [ ] **Step 3: Render plugin checklist in the setup page**

Add a list to `reci_render_client_setup_page()` that shows plugin status and instructions.

- [ ] **Step 4: Defer automated plugin installation unless explicitly approved**

For v1, show the checklist and status only. Do not silently install plugins yet.

Expected: first-time client setup remains understandable and low-risk.

### Task 4: Move Demo Content To A Remote Manifest Model

**Files:**
- Create: `inc/features/remote-demo-content.php`
- Create: `docs/demo-content-manifest.schema.json`
- Create: `scripts/build-demo-manifest.sh`
- Modify: `inc/init.php`

- [ ] **Step 1: Define the remote demo manifest schema**

Create `docs/demo-content-manifest.schema.json` with top-level keys:
- `version`
- `content_sets`
- `assets`
- `pages`
- `posts`
- `taxonomies`

- [ ] **Step 2: Create the PHP remote manifest fetcher**

Create `inc/features/remote-demo-content.php`.

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reci_remote_demo_manifest_url(): string {
	return (string) apply_filters( 'reci_remote_demo_manifest_url', '' );
}

function reci_fetch_remote_demo_manifest(): array {
	$url = reci_remote_demo_manifest_url();
	if ( '' === $url ) {
		return [];
	}

	$response = wp_remote_get( $url, [ 'timeout' => 20 ] );
	if ( is_wp_error( $response ) ) {
		return [];
	}

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	return is_array( $data ) ? $data : [];
}
```

- [ ] **Step 3: Load the remote demo feature from `inc/init.php`**

Add:

```php
'/inc/features/remote-demo-content.php',
```

- [ ] **Step 4: Create a manifest helper script for maintainers**

Create `scripts/build-demo-manifest.sh` with a placeholder scaffold that documents where the remote manifest should be generated/published.

- [ ] **Step 5: Keep local `demo-content/` in package until remote import is proven**

Expected: no premature removal of working local demo content.

### Task 5: Connect Setup Wizard To Remote Demo Import

**Files:**
- Modify: `inc/admin/theme-setup-client.php`
- Modify: `inc/features/remote-demo-content.php`

- [ ] **Step 1: Add a remote demo manifest availability section to setup UI**

Show whether a manifest was found and which content sets are available.

- [ ] **Step 2: Add an import action stub for v1**

Add a button that, for now, validates manifest retrieval and shows content availability rather than doing a full destructive import automatically.

- [ ] **Step 3: Make local importer the fallback path**

If no remote manifest exists, setup wizard should say:
- remote demo unavailable
- local bundled demo content still available

Expected: setup remains usable while remote content rollout is incremental.

### Task 6: Add Custom Theme Update Checks

**Files:**
- Create: `inc/features/theme-updates.php`
- Modify: `inc/init.php`

- [ ] **Step 1: Create a remote update metadata URL helper**

Create `inc/features/theme-updates.php` with:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function reci_theme_update_manifest_url(): string {
	return (string) apply_filters( 'reci_theme_update_manifest_url', '' );
}
```

- [ ] **Step 2: Hook into WordPress theme update transient**

Add a minimal update check using `pre_set_site_transient_update_themes`.

- [ ] **Step 3: Compare installed version to remote version**

Use `wp_get_theme()->get( 'Version' )` and inject a response into the update transient when remote version is higher.

- [ ] **Step 4: Load the updater feature from `inc/init.php`**

Add:

```php
'/inc/features/theme-updates.php',
```

### Task 7: Publish Release Artifacts From Main

**Files:**
- Modify: `.github/workflows/package-theme.yml`

- [ ] **Step 1: Keep packaging on `main` only**

Do not trigger on `development`.

- [ ] **Step 2: Upload zip artifact with versioned naming**

Verify it is already using `dist/*.zip`; keep that behavior.

- [ ] **Step 3: Add a comment block documenting future release-manifest publication**

Document that this workflow can later publish:
- update manifest JSON
- GitHub Release asset zip

Expected: future updater can point at a predictable release source.

### Task 8: Final Documentation And Handoff

**Files:**
- Modify: `docs/client-distribution.md`

- [ ] **Step 1: Document install flow for client**

Add:
- install theme zip
- activate theme
- run setup wizard
- install required plugins
- choose demo/starter content import

- [ ] **Step 2: Document release flow for maintainers**

Add:
- work on `development`
- merge to `main`
- package workflow runs
- deploy workflow runs on your server
- client update manifest/release zip is updated

- [ ] **Step 3: Document update-notice prerequisites**

Add:
- remote manifest location
- release zip hosting location
- required version fields

- [ ] **Step 4: Commit plan implementation docs and scaffolding**

Run:

```bash
git add docs/client-distribution.md docs/demo-content-manifest.schema.json scripts/build-demo-manifest.sh inc/admin/theme-setup-client.php inc/features/remote-demo-content.php inc/features/theme-updates.php inc/init.php .github/workflows/package-theme.yml scripts/package-theme.sh
git commit -m "feat: scaffold client distribution and updates"
```

## Self-Review

**Spec coverage:**
- Lightweight client theme: Tasks 1 and 4.
- Setup wizard install flow: Tasks 2, 3, and 5.
- Remote demo content: Tasks 4 and 5.
- Theme update notices: Task 6.
- Main-based packaging/release model: Task 7.

**Placeholder scan:**
- No `TODO`/`TBD` placeholders remain in the execution steps.

**Type consistency:**
- `development` as working branch and `main` as release branch are used consistently.
- Remote manifest terminology is used consistently across demo and update flows.
