# RECI Sphere Updates Design Spec

**Date:** 2026-06-02
**Status:** Approved

## Goal
Update RECI Spheres to include large content images and accurate brand colors on their respective single taxonomy pages. The images contain detailed text and should be displayed prominently in a split-header layout.

## Brand Colors (Extracted from Images)
- **Sphere 1 (Recognizing):** `#5875FF` (Vibrant Blue)
- **Sphere 2 (Examining):** `#E5B13B` (Yellow/Gold)
- **Sphere 3 (Embracing):** `#66B570` (Green)
- **Sphere 4 (Building):** `#6099B1` (Teal/Blue)
- **Sphere 5 (Acknowledging):** `#9368AC` (Purple)
- **Sphere 6 (Gauging):** `#45938D` (Dark Teal)

## Design Details

### 1. Taxonomy Admin (`inc/taxonomies.php`)
- **Custom Meta Field:** `reci_sphere_content_image_id` (int) to store attachment ID.
- **Admin UI:** Add a "Sphere Content Image" upload field to the Add/Edit screens for the `reci_sphere` taxonomy.
- **Seeding:** Update `reci_media_hub_seed_default_spheres()` to automatically link images in `assets/images/site/reci-spheres/` using the following mapping:
    - Sphere 1: `Sphere 1 Recognizing Racial Oppression and Advancing Racial Liberation.png`
    - Sphere 2: `Sphere 2 Examining Racial Identities and Addressing Racial Biases.png`
    - Sphere 3: `Sphere 3 Embracing Racial Diversity and Growing Racial Literacy.png`
    - Sphere 4: `Sphere 4 Building Racial Empathy and Enhancing Racial Stamina.png`
    - Sphere 5: `Sphere 5 Acknowledging Racial Trauma and Fostering Racial Healing.png`
    - Sphere 6: `Sphere 6 Gauging Racial Inequity and Championing Racial Justice.png`

### 2. Single Page Layout (`taxonomy-reci_sphere.php`)
- **Split Header:** Refactor the top section into a two-column grid.
    - **Left (60%):** Number, Title, Action, Description.
    - **Right (40%):** Sphere Content Image.
- **Responsiveness:** Stack on mobile (Text top, Image bottom).
- **Styling:** Maintain existing background gradients but update them to use the new brand colors.

### 3. Data Integration (`inc/sphere-helpers.php`)
- Update `reci_get_submission_spheres()` and related helpers to include the `content_image_url`.
- Ensure fallback logic: If no ID is in meta, look for the default file in the assets folder.

## Success Criteria
- [ ] Brand colors on all spheres updated to match images.
- [ ] Content image visible on each single RECI Sphere page.
- [ ] Header layout is split (Text Left, Image Right) on desktop.
- [ ] Upload button exists in WordPress Admin for `reci_sphere` taxonomy.
- [ ] All existing badges/dots reflect the new colors.
