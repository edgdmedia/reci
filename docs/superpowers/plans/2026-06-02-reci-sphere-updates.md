# RECI Sphere Updates Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update RECI Sphere taxonomy with accurate brand colors and large content images displayed in a split-header layout on the single sphere page.

**Architecture:** Extend taxonomy metadata to store image IDs, update default configuration, and refactor the taxonomy archive template into a responsive split-column layout.

**Tech Stack:** WordPress (PHP), Tailwind CSS, WordPress Media API.

---

### Task 1: Update Sphere Defaults & Brand Colors

**Files:**
- Modify: `/Users/olalekan/Projects/reci/media-hub/wordpress/inc/taxonomies.php`

- [ ] **Step 1: Update `reci_media_hub_default_spheres` colors and add image filenames**

Update the array to match the colors and map filenames.

```php
// In inc/taxonomies.php -> reci_media_hub_default_spheres()
return [
    [
        'slug'          => 'recognizing-racial-oppression',
        'num'           => '01',
        'awareness'     => 'Recognizing Racial Oppression',
        'action'        => 'Advancing Racial Liberation',
        'color'         => '#5875FF', // Updated
        'gradient'      => 'linear-gradient(135deg, #5875FF, #8AA0FF)',
        'image_file'    => 'Sphere 1 Recognizing Racial Oppression and Advancing Racial Liberation.png',
        // ... desc and questions remain same
    ],
    [
        'slug'          => 'examining-racial-identities',
        'num'           => '02',
        'awareness'     => 'Examining Racial Identities',
        'action'        => 'Addressing Racial Biases',
        'color'         => '#E5B13B', // Updated
        'gradient'      => 'linear-gradient(135deg, #E5B13B, #F2D184)',
        'image_file'    => 'Sphere 2 Examining Racial Identities and Addressing Racial Biases.png',
    ],
    [
        'slug'          => 'embracing-racial-diversity',
        'num'           => '03',
        'awareness'     => 'Embracing Racial Diversity',
        'action'        => 'Growing Racial Literacy',
        'color'         => '#66B570', // Updated
        'gradient'      => 'linear-gradient(135deg, #66B570, #95D29C)',
        'image_file'    => 'Sphere 3 Embracing Racial Diversity and Growing Racial Literacy.png',
    ],
    [
        'slug'          => 'building-racial-empathy',
        'num'           => '04',
        'awareness'     => 'Building Racial Empathy',
        'action'        => 'Enhancing Racial Stamina',
        'color'         => '#6099B1', // Updated
        'gradient'      => 'linear-gradient(135deg, #6099B1, #8ABBCF)',
        'image_file'    => 'Sphere 4 Building Racial Empathy and Enhancing Racial Stamina.png',
    ],
    [
        'slug'          => 'acknowledging-racial-trauma',
        'num'           => '05',
        'awareness'     => 'Acknowledging Racial Trauma',
        'action'        => 'Fostering Racial Healing',
        'color'         => '#9368AC', // Updated
        'gradient'      => 'linear-gradient(135deg, #9368AC, #B896CC)',
        'image_file'    => 'Sphere 5 Acknowledging Racial Trauma and Fostering Racial Healing.png',
    ],
    [
        'slug'          => 'gauging-racial-inequities',
        'num'           => '06',
        'awareness'     => 'Gauging Racial Inequities',
        'action'        => 'Championing Racial Justice',
        'color'         => '#45938D', // Updated
        'gradient'      => 'linear-gradient(135deg, #45938D, #72B8B3)',
        'image_file'    => 'Sphere 6 Gauging Racial Inequity and Championing Racial Justice.png',
    ],
];
```

- [ ] **Step 2: Update `reci_media_hub_seed_default_spheres` to handle meta updates**

Ensure existing terms get their colors updated if they differ from the new defaults.

- [ ] **Step 3: Commit**

```bash
git add inc/taxonomies.php
git commit -m "feat: update sphere brand colors and add default image mapping"
```

---

### Task 2: Add Image Upload to Taxonomy Admin

**Files:**
- Modify: `/Users/olalekan/Projects/reci/media-hub/wordpress/inc/taxonomies.php`

- [ ] **Step 1: Enqueue Media Library assets for taxonomy screens**

```php
// In inc/taxonomies.php
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'edit-tags.php' && $hook !== 'term.php') return;
    if (get_current_screen()->taxonomy !== 'reci_sphere') return;
    wp_enqueue_media();
});
```

- [ ] **Step 2: Add `reci_sphere_content_image_id` field to `reci_media_hub_render_sphere_fields`**

Add a row with an "Upload Image" button and a preview div. Use existing JS patterns from `meta-fields.php`.

- [ ] **Step 3: Update `reci_media_hub_save_sphere_fields` to save the image ID**

- [ ] **Step 4: Commit**

```bash
git add inc/taxonomies.php
git commit -m "feat: add content image upload field to reci_sphere taxonomy"
```

---

### Task 3: Update Helpers for Image Retrieval

**Files:**
- Modify: `/Users/olalekan/Projects/reci/media-hub/wordpress/inc/sphere-helpers.php`

- [ ] **Step 1: Update `reci_get_post_spheres` to include `content_image_url`**

```php
// In inc/sphere-helpers.php
$image_id = get_term_meta($term->term_id, 'reci_sphere_content_image_id', true);
$image_url = '';
if ($image_id) {
    $image_url = wp_get_attachment_url($image_id);
} else {
    // Fallback to default asset
    $filename = $default['image_file'] ?? '';
    if ($filename) {
        $image_url = get_template_directory_uri() . '/assets/images/site/reci-spheres/' . $filename;
    }
}
$spheres[] = [
    // ... existing
    'content_image_url' => $image_url,
];
```

- [ ] **Step 2: Commit**

```bash
git add inc/sphere-helpers.php
git commit -m "feat: add content_image_url to sphere helper retrieval"
```

---

### Task 4: Refactor Sphere Archive Template Layout

**Files:**
- Modify: `/Users/olalekan/Projects/reci/media-hub/wordpress/taxonomy-reci_sphere.php`

- [ ] **Step 1: Fetch the content image URL in the template**

- [ ] **Step 2: Refactor Header Section into split columns**

Replace lines 100-121 with a grid layout.

```php
<!-- taxonomy-reci_sphere.php refactoring header section -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background: <?php echo esc_attr($sphere_gradient); ?>;"></div>
    <div class="reci-container relative py-14 lg:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-10 items-center">
            <!-- Left: Content (60%) -->
            <div class="lg:col-span-6 flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <?php if ($sphere_num): ?>
                        <span class="text-4xl lg:text-5xl font-bold font-heading leading-none" style="color: <?php echo esc_attr($sphere_color); ?>;">
                            <?php echo esc_html($sphere_num); ?>
                        </span>
                    <?php endif; ?>
                    <span class="w-4 h-4 rounded-full" style="background-color: <?php echo esc_attr($sphere_color); ?>;"></span>
                </div>
                <h1 class="text-neutral-800 text-4xl lg:text-6xl font-bold font-heading leading-tight">
                    <?php echo esc_html($page_title); ?>
                </h1>
                <?php if ($sphere_action): ?>
                    <p class="text-neutral-500 text-xl lg:text-2xl font-normal"><?php echo esc_html($sphere_action); ?></p>
                <?php endif; ?>
                <?php if ($page_subtitle): ?>
                    <p class="text-neutral-700 text-lg font-normal leading-relaxed"><?php echo esc_html($page_subtitle); ?></p>
                <?php endif; ?>
            </div>
            <!-- Right: Image (40%) -->
            <div class="lg:col-span-4 flex justify-center">
                <?php if ($sphere_image_url): ?>
                    <img src="<?php echo esc_url($sphere_image_url); ?>" alt="<?php echo esc_attr($page_title); ?>" class="w-full h-auto rounded-lg shadow-sm" />
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
```

- [ ] **Step 3: Commit**

```bash
git add taxonomy-reci_sphere.php
git commit -m "feat: refactor sphere archive header into split column layout with image"
```

---

### Task 5: Verification & Seeding

- [ ] **Step 1: Trigger the seeding function**

Use `wp eval 'reci_media_hub_seed_default_spheres();'` or visit the admin to ensure meta is updated.

- [ ] **Step 2: Manual Verification**

- Visit `/reci-sphere/recognizing-racial-oppression/`
- Check colors (Blue), Check Layout (Split), Check Image Visibility.

- [ ] **Step 3: Commit**

```bash
git commit --allow-empty -m "chore: verify RECI sphere updates"
```
