# WordPress Admin Instructions (PHP Template Workflow)

## 1) Activate and Assign Home

1. Go to `Appearance -> Themes` and activate `Reci Media Hub`.
2. Create pages `Home` and `Blog` (if they do not already exist).
3. Go to `Settings -> Reading`.
4. Set `Your homepage displays` to `A static page`.
5. Set `Homepage` to `Home` and `Posts page` to `Blog`.

## 2) Use the Figma Page Template

1. Open `Pages -> Home -> Edit`.
2. In the right sidebar, under `Template`, choose `Figma - Hero V3`.
3. Update the page.
4. View the page on the frontend.

This template uses raw code from:

- `figma/herov3.php`

## 3) Front Page Behavior

`front-page.php` is set to load `figma/herov3.php` directly, so the homepage keeps the exported Figma structure with minimal transformation.

## 4) Build Additional Pure PHP Templates

When you want another Figma page:

1. Add a new file in `figma/` (for example `figma/about-v1.php`).
2. Duplicate `page-templates/template-figma-herov3.php`.
3. Change:
   - `Template Name`
   - included file path
4. In WP admin, assign that template to the target page.

## 5) Refresh Permalinks

1. Go to `Settings -> Permalinks`.
2. Click `Save Changes` once.

## 6) Dynamic Content Types (Still Available)

Registered post types:

- `Reci Articles`
- `Reci Videos`
- `Reci Podcasts`

Taxonomy:

- `Reci Topics`

## 7) Quick QA

1. Confirm `Home` page renders the Figma layout.
2. Confirm the `Template` dropdown is visible on page edit screens.
3. Confirm archives resolve for custom post types.
