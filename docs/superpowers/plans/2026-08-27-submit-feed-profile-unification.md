# Submit, Feed, and Profile Unification Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Unify RECI contribution flows so `/submit/` is the single canonical submission entry point, add a dedicated `/dashboard/feed/` page, and consolidate collaborator/profile fields across collaborator onboarding, submit, and dashboard profile editing.

**Architecture:** Keep the current WordPress template-routing model, but centralize contributor identity fields into shared helper functions and route all submission entry points to one public page. Preserve the dashboard as a private utility layer by adding a dedicated feed page and simplifying dashboard home into overview-only navigation. Use the existing collaborator status model to dynamically change the `/submit/` experience for guests, members, pending collaborators, and approved collaborators. `/submit/` should feel like one continuous contribution flow to the user, while backend persistence stays split into safe stages such as account creation, collaborator application save, and content submission save/queue.

**Tech Stack:** WordPress PHP templates, custom page templates, custom post types, user meta, post meta, existing RECI dashboard routing, existing collaborator application flow, existing React-mounted submit experience.

---

## File Structure

- Modify: `inc/features/submissions.php`
  - Make `/submit/` the canonical flow, centralize content type definitions, and add shared submit-state helpers that support a staged-but-continuous contribution flow.
- Modify: `inc/features/collaborators.php`
  - Expose shared collaborator field definitions and separate profile fields from application-only fields.
- Modify: `templates/page/template-submit-content.php`
  - Render one dynamic state-aware submit page for guests, non-collaborator members, pending collaborators, and approved collaborators.
- Modify: `templates/page/dashboard/template-dashboard-submit.php`
  - Stop acting as a separate submit experience and redirect or hand off to `/submit/`.
- Modify: `templates/page/template-become-a-collaborator.php`
  - Reuse shared collaborator field definitions and keep onboarding aligned with submit/profile fields.
- Modify: `template-parts/dashboard/profile-form.php`
  - Expand profile editing to use the same canonical collaborator/profile fields.
- Modify: `templates/page/dashboard/template-dashboard.php`
  - Reduce the current dashboard home to overview-only and move full feed focus out.
- Create: `templates/page/dashboard/template-dashboard-feed.php`
  - Full dedicated dashboard feed page.
- Modify: `template-parts/dashboard/sidebar.php`
  - Add `Feed` navigation item and remove dashboard-submit duplication if needed.
- Modify: `demo-content/pages.php`
  - Register a dashboard child page for `feed`.
- Modify: `inc/core/theme-activation.php`
  - Ensure `dashboard/feed` page exists on activation.
- Modify: `inc/admin/demo-content.php`
  - Ensure demo page import creates `dashboard/feed`.

---

## Clarified Submit Principle

`/submit/` must behave as **one continuous contribution flow** for the user, but it does **not** need to be implemented as one monolithic backend request.

The correct model is:

- one canonical route
- one coherent UI flow
- shared profile/collaborator fields
- separate backend save stages when needed
- resume-safe behavior when a step fails

For example:

1. a guest opens `/submit/`
2. the system creates an account if needed
3. the system collects and saves collaborator/profile fields
4. the system creates or updates the collaborator application
5. the system then unlocks or continues the content submission stage

The user should experience this as one guided path, but the backend should stay resilient and staged.

---

### Task 1: Create Shared Collaborator/Profile Field Definitions

**Files:**
- Modify: `inc/features/collaborators.php`
- Modify: `template-parts/dashboard/profile-form.php`
- Modify: `templates/page/template-become-a-collaborator.php`

- [x] **Step 1: Add a canonical field-definition helper in `inc/features/collaborators.php`**

Add helpers that define shared profile fields separately from application-only fields:

```php
function reci_collaborator_profile_field_definitions(): array {
	return [
		'reci_firstname' => [ 'label' => 'First Name', 'type' => 'text', 'required' => true ],
		'reci_lastname' => [ 'label' => 'Last Name', 'type' => 'text', 'required' => true ],
		'user_email' => [ 'label' => 'Email', 'type' => 'email', 'required' => true ],
		'reci_affiliated_with_pitt' => [ 'label' => 'Affiliated with Pitt', 'type' => 'select', 'required' => true, 'options' => [ 'Yes', 'No' ] ],
		'reci_pitt_affiliation' => [ 'label' => 'Pitt Affiliation', 'type' => 'text', 'required' => false ],
		'submission_organization' => [ 'label' => 'Affiliation / Organization', 'type' => 'text', 'required' => true ],
		'reci_department' => [ 'label' => 'Department (School / Organization)', 'type' => 'text', 'required' => true ],
		'submission_role' => [ 'label' => 'Role / Title', 'type' => 'text', 'required' => true ],
		'submission_bio' => [ 'label' => 'Personal Bio (150 words or less)', 'type' => 'textarea', 'required' => true ],
		'submission_website' => [ 'label' => 'Professional Website', 'type' => 'url', 'required' => false ],
		'reci_social_handles' => [ 'label' => 'Social Media Handles', 'type' => 'text', 'required' => false ],
	];
}

function reci_collaborator_application_only_field_definitions(): array {
	return [
		'reci_profile_picture' => [ 'label' => 'Profile Picture (Professional headshot)', 'type' => 'file', 'required' => true ],
		'reci_cv_upload' => [ 'label' => 'Attach CV', 'type' => 'file', 'required' => false ],
		'reci_membership_objective' => [ 'label' => 'Main Objective for Membership', 'type' => 'textarea', 'required' => true ],
	];
}
```

- [x] **Step 2: Add user-meta read/write helpers for shared profile fields**

In `inc/features/collaborators.php`, add helpers that map shared field names to user meta values:

```php
function reci_get_user_collaborator_profile_data( int $user_id ): array {
	$user = get_user_by( 'id', $user_id );
	if ( ! $user instanceof WP_User ) {
		return [];
	}

	return [
		'reci_firstname' => (string) get_user_meta( $user_id, 'first_name', true ),
		'reci_lastname' => (string) get_user_meta( $user_id, 'last_name', true ),
		'user_email' => (string) $user->user_email,
		'reci_affiliated_with_pitt' => (string) get_user_meta( $user_id, 'reci_affiliated_with_pitt', true ),
		'reci_pitt_affiliation' => (string) get_user_meta( $user_id, 'reci_pitt_affiliation', true ),
		'submission_organization' => (string) get_user_meta( $user_id, 'organization', true ),
		'reci_department' => (string) get_user_meta( $user_id, 'reci_department', true ),
		'submission_role' => (string) get_user_meta( $user_id, 'user_title', true ),
		'submission_bio' => (string) get_user_meta( $user_id, 'description', true ),
		'submission_website' => (string) $user->user_url,
		'reci_social_handles' => (string) get_user_meta( $user_id, 'reci_social_handles', true ),
	];
}
```

- [x] **Step 3: Update dashboard profile form to use the shared field helpers**

In `template-parts/dashboard/profile-form.php`, replace the currently separate field list with the shared profile data helper and add fields for:

- Pitt affiliation flag
- Pitt affiliation text
- department
- social handles

Keep profile editing scoped to profile fields, not application-only uploads/objective.

- [x] **Step 4: Update collaborator onboarding page to use the same field model**

In `templates/page/template-become-a-collaborator.php`, render the shared profile fields first, then append application-only fields.

- [x] **Step 5: Run syntax verification**

Run:

```bash
php -l inc/features/collaborators.php
php -l template-parts/dashboard/profile-form.php
php -l templates/page/template-become-a-collaborator.php
```

Expected: `No syntax errors detected` for all files.

---

### Task 2: Make `/submit/` the Canonical Submission Entry Point

**Files:**
- Modify: `templates/page/template-submit-content.php`
- Modify: `templates/page/dashboard/template-dashboard-submit.php`
- Modify: `inc/features/submissions.php`

- [x] **Step 1: Add a submit-state helper in `inc/features/submissions.php`**

Add a helper that returns one of:
- `guest`
- `member_needs_collaborator`
- `pending_collaborator`
- `approved_collaborator`

```php
function reci_get_submit_experience_state(): string {
	if ( ! is_user_logged_in() ) {
		return 'guest';
	}

	$collaborator_status = function_exists( 'reci_get_collaborator_status' ) ? reci_get_collaborator_status() : 'guest';
	if ( 'approved' === $collaborator_status ) {
		return 'approved_collaborator';
	}
	if ( 'pending' === $collaborator_status ) {
		return 'pending_collaborator';
	}

	return 'member_needs_collaborator';
}
```

- [x] **Step 2: Replace the current collaborator-only gate on `/submit/` with one continuous contribution flow**

In `templates/page/template-submit-content.php`, replace the current binary:
- collaborator => show submit app
- everyone else => generic gate

with explicit state rendering:

- guest: account creation + collaborator/profile completion + contribution flow
- member_needs_collaborator: collaborator/profile completion + contribution flow
- pending_collaborator: review notice and hold state
- approved_collaborator: direct submit app

Important: keep the UX continuous, but keep backend writes staged. Do not force account creation, collaborator application creation, and content persistence into one brittle all-or-nothing request.

- [x] **Step 3: Stop treating `/dashboard/submit/` as its own distinct flow**

In `templates/page/dashboard/template-dashboard-submit.php`, replace the full page body with a redirect to `/submit/`:

```php
wp_safe_redirect( home_url( '/submit/' ) );
exit;
```

This keeps one canonical submit route.

- [x] **Step 3a: Keep submit persistence staged and explicit**

Implementation rule:

- create account separately when needed
- save collaborator/profile/application data separately when needed
- only accept or unlock final content submission when the user reaches an allowed state
- if non-approved collaborators cannot complete final submission yet, communicate that explicitly instead of faking a fully successful content publish step

- [x] **Step 4: Ensure submit content types stay aligned with the live supported types**

In `inc/features/submissions.php`, review `reci_media_hub_submission_type_map()` and `reci_media_hub_submission_type_definitions()` so they match the real supported types and the dashboard content surfaces. Remove stale or misleading options only if they are not actually supported downstream.

- [x] **Step 5: Run syntax verification**

Run:

```bash
php -l inc/features/submissions.php
php -l templates/page/template-submit-content.php
php -l templates/page/dashboard/template-dashboard-submit.php
```

Expected: `No syntax errors detected` for all files.

---

### Task 3: Add Dedicated Dashboard Feed Page

**Files:**
- Create: `templates/page/dashboard/template-dashboard-feed.php`
- Modify: `template-parts/dashboard/sidebar.php`
- Modify: `templates/page/dashboard/template-dashboard.php`
- Modify: `demo-content/pages.php`
- Modify: `inc/core/theme-activation.php`
- Modify: `inc/admin/demo-content.php`

- [x] **Step 1: Create the feed page template**

Create `templates/page/dashboard/template-dashboard-feed.php` using the current personalized feed logic from dashboard home, but with a fuller feed layout and stronger page identity.

- [x] **Step 2: Add dashboard sidebar navigation for Feed**

In `template-parts/dashboard/sidebar.php`, add:

```php
<li>
	<a href="<?php echo esc_url( home_url( '/dashboard/feed/' ) ); ?>"
	   class="... <?php echo $current_page === 'feed' ? 'bg-amber-50 text-amber-800' : 'text-zinc-700 hover:bg-zinc-100'; ?>">
		Feed
	</a>
</li>
```

- [x] **Step 3: Reduce dashboard home to overview-only**

In `templates/page/dashboard/template-dashboard.php`, replace the current `Your Feed` card with a smaller overview card that links to the new dedicated feed page.

- [x] **Step 4: Register the dashboard feed page in activation/demo page creation**

Add `feed` to:
- `demo-content/pages.php`
- `inc/core/theme-activation.php`
- any page-import logic in `inc/admin/demo-content.php`

- [x] **Step 5: Run syntax verification**

Run:

```bash
php -l templates/page/dashboard/template-dashboard-feed.php
php -l template-parts/dashboard/sidebar.php
php -l templates/page/dashboard/template-dashboard.php
php -l inc/core/theme-activation.php
php -l inc/admin/demo-content.php
```

Expected: `No syntax errors detected` for all files.

---

### Task 4: Align Dashboard Visual Language with Main Site

**Files:**
- Modify: `template-parts/dashboard/sidebar.php`
- Modify: `templates/page/dashboard/template-dashboard.php`
- Modify: `templates/page/dashboard/template-dashboard-feed.php`
- Modify: `templates/page/dashboard/template-dashboard-profile.php`
- Modify: `templates/page/dashboard/template-dashboard-settings.php`

- [x] **Step 1: Tighten dashboard shell styling**

Adjust dashboard surfaces toward the public-site style system:
- stronger heading hierarchy
- more consistent spacing
- less app-like neutral chrome
- keep site-standard buttons

- [x] **Step 2: Normalize dashboard card styling**

Use the same rounded/border/shadow language across feed, overview, profile, and settings.

- [x] **Step 3: Make feed page the primary content destination visually**

The feed page should feel like the natural “content home” for signed-in members, while dashboard home remains summary/overview.

- [x] **Step 4: Run syntax verification**

Run:

```bash
php -l template-parts/dashboard/sidebar.php
php -l templates/page/dashboard/template-dashboard.php
php -l templates/page/dashboard/template-dashboard-feed.php
php -l templates/page/dashboard/template-dashboard-profile.php
php -l templates/page/dashboard/template-dashboard-settings.php
```

Expected: `No syntax errors detected` for all files.

---

### Task 5: Final Verification and Release Prep

**Files:**
- Modify only if verification reveals issues.

- [ ] **Step 1: Verify canonical submit path behavior**

Manual checks on a WP install:
- guest opens `/submit/`
- logged-in non-collaborator opens `/submit/`
- pending collaborator opens `/submit/`
- approved collaborator opens `/submit/`

Expected:
- state-aware flow
- no separate dashboard-only submission behavior

- [ ] **Step 2: Verify dashboard feed route**

Manual check:
- open `/dashboard/feed/`
- confirm sidebar highlights Feed
- confirm personalized content loads

- [ ] **Step 3: Verify profile/collaborator field consistency**

Manual check:
- compare fields on:
  - `/become-a-collaborator/`
  - `/submit/`
  - `/dashboard/profile/`

Expected:
- shared identity/profile fields match
- application-only fields only appear where appropriate

- [x] **Step 4: Commit in focused slices**

Suggested commits:

```bash
git commit -m "feat: unify collaborator profile fields"
git commit -m "feat: make submit flow canonical"
git commit -m "feat: add dashboard feed page"
git commit -m "style: align dashboard with site UI"
```

---

## Self-Review

- Spec coverage: covers canonical `/submit/`, dynamic onboarding states, shared collaborator/profile fields, dedicated dashboard feed page, and dashboard visual alignment.
- Placeholder scan: no TODO/TBD placeholders are left in task instructions.
- Type consistency: shared field model, submit states, and dashboard feed route are consistently named throughout the plan.

## Execution Handoff

Plan complete and saved to `docs/superpowers/plans/2026-08-27-submit-feed-profile-unification.md`. Two execution options:

**1. Subagent-Driven (recommended)** - I dispatch a fresh subagent per task, review between tasks, fast iteration

**2. Inline Execution** - Execute tasks in this session using executing-plans, batch execution with checkpoints

Which approach?
