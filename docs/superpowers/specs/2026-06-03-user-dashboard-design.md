# User Dashboard — Design Spec

## Overview

A clean, focused dashboard for logged-in WordPress users (subscribers, authors, contributors) replacing the default front-end experience. Editors and Administrators continue using wp-admin.

**Roles:**
- **Subscribers** — all reader features (bookmarks, journal, comments, profile, settings)
- **Authors** — all subscriber features + submission management + submit content
- **Contributors** — same as authors (create content, submit for review)

---

## 1. CPT: `reci_journal` (rename from `reci_reflection_response`)

### Registration

- Rename existing `reci_reflection_response` to `reci_journal`
- Post type args: `public => false`, `show_ui => true`, `show_in_menu => 'edit.php?post_type=reci_reflection'`, `supports => ['title', 'editor', 'author']`
- Labels: "Journals" / "Journal"

### Meta Fields

| Key | Type | Description |
|-----|------|-------------|
| `_reci_reflection_id` | int | Linked reflection post ID (0 if free-form) |
| `_reci_reflection_prompt` | string | The prompt text at time of writing |
| `_reci_shared` | bool | Opt-in to make public (default: false) |

### REST API

Existing routes `reci/v1/reflection-responses` renamed to `reci/v1/journals`:
- `GET /reci/v1/journals` — list user's journals (filterable by `reflection_id`)
- `POST /reci/v1/journals` — create journal entry
- `PATCH /reci/v1/journals/:id` — update journal (privacy toggle, content edit)

### DB Migration

On theme update / activation:
1. Query all `reci_reflection_response` posts
2. Change `post_type` to `reci_journal`
3. Update any references

---

## 2. File Structure

```
page-templates/
  dashboard/
    template-dashboard.php              ← /dashboard/
    template-dashboard-my-content.php   ← /dashboard/my-content/ (author)
    template-dashboard-submit.php       ← /dashboard/submit/ (mount React app)
    template-dashboard-bookmarks.php    ← /dashboard/bookmarks/
    template-dashboard-journal.php      ← /dashboard/journal/
    template-dashboard-comments.php     ← /dashboard/comments/
    template-dashboard-profile.php      ← /dashboard/profile/
    template-dashboard-settings.php     ← /dashboard/settings/

template-parts/
  dashboard/
    sidebar.php
    sidebar-nav.php
    overview-cards.php
    journal-list-item.php
    bookmarks-grid-item.php
    submission-table-row.php
    comment-item.php
    profile-form.php
    settings-form.php

inc/
  dashboard.php                         ← rewrite rules, role checks, AJAX handlers
```

---

## 3. Rewrite Rules (`inc/dashboard.php`)

```php
// Register dashboard page rewrite rules
add_action('init', 'reci_dashboard_rewrite_rules');
function reci_dashboard_rewrite_rules() {
    $pages = [
        'my-content'   => 'template-dashboard-my-content.php',
        'submit'       => 'template-dashboard-submit.php',
        'bookmarks'    => 'template-dashboard-bookmarks.php',
        'journal'      => 'template-dashboard-journal.php',
        'comments'     => 'template-dashboard-comments.php',
        'profile'      => 'template-dashboard-profile.php',
        'settings'     => 'template-dashboard-settings.php',
    ];
    foreach ($pages as $slug => $template) {
        add_rewrite_rule('^dashboard/' . $slug . '/?$', 'index.php?pagename=dashboard&dashboard_page=' . $slug, 'top');
    }
    add_rewrite_rule('^dashboard/?$', 'index.php?pagename=dashboard', 'top');
}

// Redirect non-logged-in users
add_action('template_redirect', 'reci_dashboard_auth_check');
function reci_dashboard_auth_check() {
    if (get_query_var('pagename') === 'dashboard' && !is_user_logged_in()) {
        wp_safe_redirect(wp_login_url(get_permalink()));
        exit;
    }
}

// Author-only section guard
add_action('template_redirect', 'reci_dashboard_author_guard');
function reci_dashboard_author_guard() {
    $author_pages = ['my-content', 'submit'];
    if (in_array(get_query_var('dashboard_page'), $author_pages, true)
        && !current_user_can('edit_posts')) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
    }
}
```

---

## 4. Sidebar Navigation

Rendered by `template-parts/dashboard/sidebar.php`.

### Desktop Layout (left sidebar, ~240px)
- Narrow vertical sidebar with nav links + user info at bottom
- Active state highlighting

### Order

```
Dashboard          ← /dashboard/
────────────────────
My Content         ← /dashboard/my-content/  [author only]
────────────────────
Bookmarks          ← /dashboard/bookmarks/
Journal            ← /dashboard/journal/
Comments           ← /dashboard/comments/
────────────────────
Profile            ← /dashboard/profile/
Settings           ← /dashboard/settings/
────────────────────
[user avatar]
[user display name]
[user email]
────────────────────
← Back to Site     ← links to homepage
```

### Mobile
- Sidebar collapses into a hamburger/drawer overlay
- Same link structure, stack vertically

### Header Icons
- Profile icon → `/dashboard/profile/`
- Settings gear icon → `/dashboard/settings/`
- Submit button (author only) → `/dashboard/submit/`
- "Back to Site" link → homepage

---

## 5. Pages — Detail

### 5.1 Overview (`/dashboard/`)

Widget cards in responsive grid (2 cols desktop, 1 col mobile):

| Widget | Content | Source |
|--------|---------|--------|
| Recent Bookmarks | Last 5 bookmarks (thumbnail + title) | `get_user_meta($uid, 'reci_bookmarks', true)` |
| Recent Journal | Last 3 entries (reflection title, date) | `WP_Query post_type=reci_journal&author=$uid` |
| Recent Comments | Last 5 comments (post title, date) | `get_comments(['user_id' => $uid, 'number' => 5])` |
| Pending Submissions | Author only: count + link | `WP_Query post_author=$uid&post_status=pending` |

### 5.2 My Content (`/dashboard/my-content/`) — Author Only

Table of user-authored posts across all CPTs:

| Column | Content |
|--------|---------|
| Title | Post title (linked to edit if editor, view otherwise) |
| Type | Article / Podcast / Video / Event / etc. |
| Status | Published / Pending Review / Draft |
| Date | Last modified |
| Actions | Edit / View / Delete |

Filter by type and status. Search by title.

Submit button on this page and in the header → `/dashboard/submit/`.

### 5.3 Submit Content (`/dashboard/submit/`) — Author Only

Mounts existing React submission app at `<div id="reci-submission-root">`. Same implementation as `template-submit-content.php` but inside the dashboard layout.

### 5.4 Bookmarks (`/dashboard/bookmarks/`)

Grid of bookmarked posts. Each card:
- Thumbnail
- Title (linked)
- Post type badge
- Bookmark icon (filled) to remove
- Date bookmarked

**Bookmark toggle implementation:**
- `user_meta` key `reci_bookmarks` → array of post IDs, each stored as `['post_id' => int, 'bookmarked_at' => timestamp]`
- AJAX endpoint: `POST /reci/v1/bookmarks/toggle` with `post_id` param
- Frontend: Bookmark icon (heart/bookmark) added to:
  - Archive listing cards (all post type templates)
  - Single post pages (below title or in meta area)
  - Via `the_content` filter or template edit

```php
// AJAX handler
add_action('wp_ajax_reci_toggle_bookmark', 'reci_ajax_toggle_bookmark');
function reci_ajax_toggle_bookmark() {
    $post_id = absint($_POST['post_id']);
    $user_id = get_current_user_id();
    $bookmarks = get_user_meta($user_id, 'reci_bookmarks', true) ?: [];
    $found = false;
    foreach ($bookmarks as $k => $b) {
        if ($b['post_id'] === $post_id) { unset($bookmarks[$k]); $found = true; break; }
    }
    if (!$found) { $bookmarks[] = ['post_id' => $post_id, 'bookmarked_at' => time()]; }
    update_user_meta($user_id, 'reci_bookmarks', array_values($bookmarks));
    wp_send_json_success(['bookmarked' => !$found]);
}
```

### 5.5 Journal (`/dashboard/journal/`)

List of `reci_journal` entries for current user. Each entry shows:
- Reflection title (linked if exists)
- Prompt text
- Response preview (trimmed to 150 chars, expandable)
- Date written
- Privacy badge: "Private" (default) or "Shared"
- Toggle to share/unshare
- Delete option

Pagination. Filterable by reflection post.

### 5.6 Comments (`/dashboard/comments/`)

List of user's comments across all post types. Each:
- Post title (linked)
- Comment excerpt
- Date
- Status badge: Approved / Pending / Rejected

Click to view full comment. Reply link opens the post comment section.

### 5.7 Profile (`/dashboard/profile/`)

Form fields:
- First name, Last name
- Display name (dropdown of options)
- Email
- Bio / About (textarea)
- Profile photo upload (native WP avatar)

If user has an `reci_author` profile:
- Author title
- Author bio (longer)
- Social links (if applicable)

Password change section (separate form):
- Current password
- New password
- Confirm new password

### 5.8 Settings (`/dashboard/settings/`)

| Setting | Type | Default |
|---------|------|---------|
| Journal privacy | Radio: Private / Public | Private |
| Email — submission approved | Toggle | On (author) |
| Email — submission rejected | Toggle | On (author) |
| Email — comment reply | Toggle | Off |
| Email — weekly digest | Toggle | Off |
| Account deletion | Button + confirmation | — |
| Data export | Button (download all user data as JSON) | — |

---

## 6. Bookmark Toggle Frontend

### Single Post Pages

A bookmark icon (outline/filled SVG) added after the post title or in the meta bar. Uses `data-post-id` attribute. On click: AJAX toggle, update icon state.

### Archive Listing Cards

Same icon added to each card. Position: top-right corner of thumbnail or after title.

### Implementation

```php
// Add bookmark button to content
add_filter('the_content', 'reci_add_bookmark_button', 100);
function reci_add_bookmark_button($content) {
    if (!is_singular() || !is_user_logged_in()) return $content;
    $post_id = get_the_ID();
    $bookmarked = reci_is_bookmarked($post_id);
    $icon = $bookmarked ? '★' : '☆';
    $button = '<button class="reci-bookmark-btn" data-post-id="' . $post_id . '" data-bookmarked="' . ($bookmarked ? '1' : '0') . '">' . $icon . ' Save</button>';
    return $content . $button;
}
```

Alternatively, add via template edits in each single/layout template for better positioning.

---

## 7. Reflection Signup Modal

When a non-logged-in user focuses the reflection prompt `<textarea>`:

1. A modal overlay appears
2. Two tabs: Sign In | Sign Up
3. Sign Up: email + password + display name
4. Sign In: email + password
5. On success: modal closes, textarea text preserved, save triggers
6. Option to "Continue without saving" (dismiss, no save)

Implementation:
- Modal HTML in `chapter-reflection-prompt.php`
- CSS for modal styling
- JS event handler on `focus` of `textarea.reci-reflection-prompt__input`
- AJAX login/registration via `wp_ajax` / `wp_ajax_nopriv`

---

## 8. Implementation Order

| Phase | Items |
|-------|-------|
| **1** | Rename `reci_reflection_response` → `reci_journal` with migration |
| **2** | `inc/dashboard.php`: rewrite rules, role checks, redirects |
| **3** | Sidebar partial + layout CSS |
| **4** | Profile page (form + password change) |
| **5** | Settings page |
| **6** | Bookmarks: AJAX handler + toggle button on content pages |
| **7** | Bookmarks dashboard page |
| **8** | Comments dashboard page |
| **9** | Journal dashboard page (uses existing REST API, add share toggle) |
| **10** | Reflection signup modal |
| **11** | My Content (author) |
| **12** | Submit (mount React app in dashboard) |
| **13** | Header icons (profile, settings, back to site) |

---

## 9. Edge Cases & Notes

- **User deletes account:** Cascade delete journals, remove bookmarks meta, preserve public comments as "anonymous"
- **Reflection deleted:** Journal entries linked to it keep the prompt text snapshot, show "[Reflection removed]" as title link
- **Bookmarks on unpublished content:** Don't show bookmark toggle on drafts/pending. If bookmarked post becomes unpublished, show "(unavailable)" in bookmarks list
- **Password change:** Uses `wp_set_password()` — user must re-login after
- **Email changes:** Send verification to new email before updating
- **Data export:** `wp_users` data + `reci_journal` entries + bookmarks + comments, output as JSON file
- **Account deletion:** Uses `wp_delete_user()` or reassigns content to admin
