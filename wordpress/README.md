# Reci Media Hub WordPress Theme (PHP Templates)

This repository is now configured for a PHP template workflow (classic/hybrid), so pages can be built from raw HTML/PHP with minimal transformation from Figma exports.

## Included
- PHP theme templates: `front-page.php`, `page.php`, `single.php`, `archive.php`, `index.php`, `404.php`
- Selectable page templates in `page-templates/`
- Raw Figma export files in `figma/` (currently `figma/herov3.php`)
- Theme setup and post type registration in `inc/`
- Global CSS in `style.css`

## Workflow for each Figma frame
1. Export HTML from Figma and place it in `figma/*.php`.
2. Create a page template in `page-templates/` that includes that file.
3. Assign the template to a WordPress page from the page editor.
4. Keep edits mostly in CSS and light PHP wiring for dynamic data.

See `WORDPRESS-INSTRUCTIONS.md` for WordPress admin steps.
