# Community Phase 2 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refactor Community into an orientation and participation gateway, keep the personalized feed inside Dashboard only, and rename user-facing Documents language to Resources without introducing social-network features.

**Architecture:** Keep the existing member/collaborator architecture and content model intact, but realign the presentation layer to match the approved product direction. Use the current CPTs, dashboard settings, collaborator application flow, and submit gating as foundations, and make minimal, targeted changes to templates and labels rather than expanding the data model.

**Tech Stack:** WordPress theme PHP templates, custom post types, custom user meta, Vite-built React submit wizard, Tailwind utility classes, wp-admin post workflows.

---

## File Structure

### Existing files to modify
- `templates/page/template-community.php`
  - Rework the Community page into a public/member orientation surface only.
- `inc/content/content-types.php`
  - Change user-facing labels from Documents to Resources for the dedicated resource CPT.
- `templates/page/dashboard/template-dashboard-my-content.php`
  - Update collaborator content-management labels from Documents to Resources.
- `sample/recmh-submission.jsx`
  - Update submit wizard content type language from Document / Resource or Documents toward finalized Resources language.
- `templates/page/dashboard/template-dashboard.php`
  - Keep feed ownership in dashboard and ensure wording reinforces it as the only personal feed surface.
- `template-parts/dashboard/settings-form.php`
  - Clarify settings copy so follows/notifications read as content-centered, not social.
- `templates/single/single-reci_author.php`
  - Keep collaborator profile behavior but review labels/copy for content-centered community language.
- `docs/community-implementation-plan.md`
  - Update the high-level product plan if implementation reveals naming or scope adjustments.

### Existing files to verify, but likely not modify unless needed
- `inc/admin/dashboard.php`
  - Source of dashboard feed behavior and current collaborator-aware preferences.
- `inc/features/collaborators.php`
  - Already contains collaborator status and admin workflow; verify no Community page dependencies remain.
- `assets/js/submission-form.js`
  - Built asset that must be regenerated from source after submit wizard copy changes.

### No new subsystems in this phase
- No new CPTs
- No new notification channels
- No new social graph mechanics
- No new community group or messaging models

---

### Task 1: Refactor Community Into Orientation/Gateway Only

**Files:**
- Modify: `templates/page/template-community.php`
- Test: manual browser review on the Community page in a WordPress environment

- [ ] **Step 1: Replace live feed/archive sections with orientation-only sections**

Update `templates/page/template-community.php` so it removes:
- personalized feed block
- featured collaborators block
- documents/resources listing block
- upcoming events listing block

Replace them with sections focused on:
- what Community is
- what Members get
- what Collaborators do
- how to join
- how contribution works
- why Dashboard is where the personal feed lives

Use this structure in `templates/page/template-community.php`:

```php
<?php
/**
 * Template Name: Community (Collaboratory)
 *
 * @package reci-media-hub
 */

get_header();

$current_user_id = get_current_user_id();
$is_logged_in    = is_user_logged_in();
$is_collaborator = function_exists( 'reci_user_is_collaborator' ) && reci_user_is_collaborator( $current_user_id );

$member_cta_url = $is_logged_in ? home_url( '/dashboard/' ) : ( reci_get_auth_page_url( 'sign-up' ) ?: wp_registration_url() );
$submit_url     = home_url( '/submit/' );
$collab_url     = function_exists( 'reci_get_collaborator_page_url' ) ? reci_get_collaborator_page_url() : home_url( '/become-a-collaborator/' );
?>

<main class="layout-page">
	<?php get_template_part('template-parts/common/page-title-card', null, [
		'title'    => 'Community',
		'subtitle' => 'Learn how RECI Community works, how to join, and how to contribute.',
	]); ?>

	<section class="reci-container py-16 lg:py-20">
		<div class="grid gap-8 lg:grid-cols-[1.4fr_0.9fr] lg:items-start">
			<div class="rounded-3xl bg-neutral-900 px-8 py-10 text-white shadow-sm">
				<span class="inline-flex rounded-full bg-amber-400/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-300">Participation Layer</span>
				<h2 class="mt-5 text-4xl font-bold font-heading leading-tight">A publishing community built around people, their work, and shared learning.</h2>
				<p class="mt-5 max-w-2xl text-base leading-8 text-zinc-200">RECI Community helps readers follow ideas and collaborators, and helps approved Collaborators publish resources, media, and other work that advances racial equity consciousness.</p>
				<div class="mt-8 flex flex-wrap gap-3">
					<a href="<?php echo esc_url( $member_cta_url ); ?>" class="inline-flex items-center rounded-lg bg-amber-500 px-5 py-3 text-sm font-semibold text-neutral-900 hover:bg-amber-400"><?php echo esc_html( $is_logged_in ? 'Open Dashboard' : 'Join as a Member' ); ?></a>
					<a href="<?php echo esc_url( $collab_url ); ?>" class="inline-flex items-center rounded-lg border border-white/25 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10">Become a Collaborator</a>
					<a href="<?php echo esc_url( $submit_url ); ?>" class="inline-flex items-center rounded-lg border border-white/25 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10"><?php echo esc_html( $is_collaborator ? 'Submit Content' : 'Learn How Submission Works' ); ?></a>
				</div>
			</div>

			<div class="rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
				<h3 class="text-2xl font-bold font-heading text-neutral-900">What Community Is For</h3>
				<ul class="mt-5 space-y-4 text-sm leading-7 text-zinc-600">
					<li><strong class="text-neutral-800">Members</strong> follow collaborators and interest areas to shape their personal dashboard feed.</li>
					<li><strong class="text-neutral-800">Collaborators</strong> maintain profiles and publish articles, videos, podcasts, events, reflections, and resources.</li>
					<li><strong class="text-neutral-800">Engagement</strong> happens through content discovery, follows, bookmarks, notifications, and comments.</li>
				</ul>
			</div>
		</div>
	</section>

	<section class="reci-container pb-12">
		<div class="grid gap-6 md:grid-cols-3">
			<div class="rounded-3xl bg-white px-7 py-8 border border-zinc-200 shadow-sm">
				<h3 class="text-2xl font-bold font-heading text-neutral-900">For Members</h3>
				<p class="mt-4 text-sm leading-7 text-zinc-600">Join to follow interest areas, track collaborators, save content, manage notifications, and access your private dashboard feed.</p>
			</div>
			<div class="rounded-3xl bg-white px-7 py-8 border border-zinc-200 shadow-sm">
				<h3 class="text-2xl font-bold font-heading text-neutral-900">For Collaborators</h3>
				<p class="mt-4 text-sm leading-7 text-zinc-600">Apply separately to contribute. Approved Collaborators can manage public profiles and publish content and resources through the existing RECI workflow.</p>
			</div>
			<div class="rounded-3xl bg-amber-50 px-7 py-8 border border-amber-200 shadow-sm">
				<h3 class="text-2xl font-bold font-heading text-neutral-900">For the Platform</h3>
				<p class="mt-4 text-sm leading-7 text-zinc-700">Community is the bridge between public content and participation. The public site holds archives; the dashboard holds the personal feed.</p>
			</div>
		</div>
	</section>

	<section class="reci-container pb-12">
		<div class="rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
			<h2 class="text-3xl font-bold font-heading text-neutral-900">How It Works</h2>
			<div class="mt-6 grid gap-6 md:grid-cols-4">
				<div>
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Step 1</p>
					<h3 class="mt-2 text-xl font-bold font-heading text-neutral-900">Join</h3>
					<p class="mt-3 text-sm leading-7 text-zinc-600">Create a Member account to save content, follow interests, and access your dashboard.</p>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Step 2</p>
					<h3 class="mt-2 text-xl font-bold font-heading text-neutral-900">Follow</h3>
					<p class="mt-3 text-sm leading-7 text-zinc-600">Choose topics, spheres, audiences, and collaborators to shape the content you see.</p>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Step 3</p>
					<h3 class="mt-2 text-xl font-bold font-heading text-neutral-900">Discover</h3>
					<p class="mt-3 text-sm leading-7 text-zinc-600">Use your dashboard feed, bookmarks, and notifications to stay close to the work that matters to you.</p>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Step 4</p>
					<h3 class="mt-2 text-xl font-bold font-heading text-neutral-900">Contribute</h3>
					<p class="mt-3 text-sm leading-7 text-zinc-600">Apply to become a Collaborator if you want to publish content, build a public profile, and manage your contributions.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="reci-container pb-20">
		<div class="rounded-3xl bg-neutral-900 px-8 py-10 text-white shadow-sm">
			<h2 class="text-3xl font-bold font-heading">Where your personal feed lives</h2>
			<p class="mt-4 max-w-3xl text-sm leading-8 text-zinc-200">RECI Community explains the ecosystem. Your private dashboard is where your personalized feed, notifications, bookmarks, journal, and content-management tools live.</p>
			<div class="mt-8">
				<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="inline-flex items-center rounded-lg bg-amber-500 px-5 py-3 text-sm font-semibold text-neutral-900 hover:bg-amber-400">Go to Dashboard</a>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
```

- [ ] **Step 2: Run a syntax check on the template**

Run: `php -l "templates/page/template-community.php"`

Expected: `No syntax errors detected in templates/page/template-community.php`

- [ ] **Step 3: Manual review checklist for Community page behavior**

Verify in WordPress:
- logged-out users see join/contribute explanations
- logged-in users still get a dashboard CTA
- there is no personalized feed on Community
- there is no collaborator listing on Community
- there is no archive-style content listing on Community

- [ ] **Step 4: Commit**

```bash
git add templates/page/template-community.php
git commit -m "refactor: simplify community page"
```

### Task 2: Rename Documents to Resources in User-Facing UI

**Files:**
- Modify: `inc/content/content-types.php`
- Modify: `templates/page/dashboard/template-dashboard-my-content.php`
- Modify: `sample/recmh-submission.jsx`
- Test: `assets/js/submission-form.js` after rebuild

- [ ] **Step 1: Update CPT labels from Document(s) to Resource(s)**

In `inc/content/content-types.php`, change the `reci_document` configuration from:

```php
'reci_document'   => [
	'singular'      => 'Document',
	'plural'        => 'Documents',
	'slug'          => 'documents',
	'menu_icon'     => 'dashicons-media-document',
	'menu_position' => 30,
],
```

to:

```php
'reci_document'   => [
	'singular'      => 'Resource',
	'plural'        => 'Resources',
	'slug'          => 'documents',
	'menu_icon'     => 'dashicons-media-document',
	'menu_position' => 30,
],
```

Keep the internal post type and slug unchanged in this phase.

- [ ] **Step 2: Update collaborator My Content labels**

In `templates/page/dashboard/template-dashboard-my-content.php`, change the filter label map from:

```php
'reci_document' => 'Documents',
```

to:

```php
'reci_document' => 'Resources',
```

- [ ] **Step 3: Update submit wizard fallback content type language**

In `sample/recmh-submission.jsx`, replace the fallback content type entry:

```jsx
{ id: "document", label: "Document / Resource", icon: "▣", desc: "Reports, PDFs, curricula, policy briefs, toolkits, and linked documents that contributors want to share as standalone resources.", examples: "Research papers, reports, curricula, policy briefs, toolkits", wordRange: "Varies by format" },
```

with:

```jsx
{ id: "document", label: "Resource", icon: "▣", desc: "Reports, PDFs, curricula, policy briefs, toolkits, and linked materials that contributors want to share as practical standalone resources.", examples: "Research papers, reports, curricula, policy briefs, toolkits", wordRange: "Varies by format" },
```

Also update any visible references in the guidelines copy from `documents/resources` or `documents` to `resources` when the copy is referring to the dedicated CPT.

- [ ] **Step 4: Rebuild the submit wizard asset**

Run: `npx vite build --config "vite.config.js"`

Expected:
- `assets/js/submission-form.js` is regenerated
- build completes for the main theme entry

If the full `npm run build` still fails inside `modules/reflection-system`, do not expand scope. The requirement here is only to regenerate the submit bundle.

- [ ] **Step 5: Run syntax and asset checks**

Run:

```bash
php -l "inc/content/content-types.php"
php -l "templates/page/dashboard/template-dashboard-my-content.php"
```

Expected:
- both PHP files pass syntax check

Then verify in the rebuilt bundle by searching:

Run: `rg "Resource" "assets/js/submission-form.js"`

Expected:
- visible Resource label appears in the compiled asset

- [ ] **Step 6: Commit**

```bash
git add inc/content/content-types.php templates/page/dashboard/template-dashboard-my-content.php sample/recmh-submission.jsx assets/js/submission-form.js
git commit -m "refactor: rename documents to resources"
```

### Task 3: Tighten Dashboard and Settings Language Around the Personal Feed

**Files:**
- Modify: `templates/page/dashboard/template-dashboard.php`
- Modify: `template-parts/dashboard/settings-form.php`
- Test: manual dashboard copy review in WordPress

- [ ] **Step 1: Strengthen dashboard feed framing**

In `templates/page/dashboard/template-dashboard.php`, keep the feed block but make the empty/help text explicit that this is the private personal feed surface.

Change:

```php
<p class="text-sm text-zinc-500">Choose interests and collaborators in Settings to get a personalized feed.</p>
```

to:

```php
<p class="text-sm text-zinc-500">Choose interests and collaborators in Settings to build your private dashboard feed.</p>
```

If helpful, also adjust the section description or CTA text so the dashboard clearly owns the feed.

- [ ] **Step 2: Clarify settings copy so follows are content-centered**

In `template-parts/dashboard/settings-form.php`, update these strings:

Change:

```php
<p class="text-sm text-zinc-600 mb-4">Choose the topics, lenses, and collaborators you want to see more of in your dashboard.</p>
```

to:

```php
<p class="text-sm text-zinc-600 mb-4">Choose the topics, lenses, and collaborators you want shaping your dashboard feed.</p>
```

Change notification copy:

```php
<span class="text-sm text-zinc-700">Email me when collaborators I follow publish</span>
```

to:

```php
<span class="text-sm text-zinc-700">Email me when collaborators I follow publish new content or resources</span>
```

These changes keep the model grounded in content publication, not social activity.

- [ ] **Step 3: Run syntax checks**

Run:

```bash
php -l "templates/page/dashboard/template-dashboard.php"
php -l "template-parts/dashboard/settings-form.php"
```

Expected:
- both files pass syntax check

- [ ] **Step 4: Manual review checklist**

Verify in WordPress:
- dashboard clearly reads as the personal feed home
- settings language feels content-centered
- collaborator-follow notification language does not imply messaging or social networking

- [ ] **Step 5: Commit**

```bash
git add templates/page/dashboard/template-dashboard.php template-parts/dashboard/settings-form.php
git commit -m "refactor: clarify dashboard feed language"
```

### Task 4: Audit Collaborator Language in Public and Private Surfaces

**Files:**
- Modify: `templates/single/single-reci_author.php`
- Modify: `templates/page/dashboard/template-dashboard-my-content.php`
- Test: manual review of collaborator profile and collaborator dashboard pages

- [ ] **Step 1: Review collaborator public profile copy**

In `templates/single/single-reci_author.php`, keep the follow button and public listing behavior, but ensure the copy reinforces a publishing-community model.

If the button label is currently:

```php
__( 'Follow Collaborator', 'reci-media-hub' )
```

keep it.

If you add any helper text, use language like:

```php
<p class="text-sm text-zinc-600">Follow this collaborator to keep up with their published work in your dashboard feed.</p>
```

Do not add networking, connection, or messaging language.

- [ ] **Step 2: Review collaborator private content management copy**

In `templates/page/dashboard/template-dashboard-my-content.php`, adjust the empty-state copy if needed so it reflects collaborator publishing.

Change:

```php
<p class="text-zinc-500">No content found. Submit your first piece to get started.</p>
```

to:

```php
<p class="text-zinc-500">No contributions found yet. Submit your first piece to get started.</p>
```

This keeps the dashboard aligned with collaborator identity without introducing a larger profile-management system in this phase.

- [ ] **Step 3: Run syntax checks**

Run:

```bash
php -l "templates/single/single-reci_author.php"
php -l "templates/page/dashboard/template-dashboard-my-content.php"
```

Expected:
- both files pass syntax check

- [ ] **Step 4: Manual review checklist**

Verify in WordPress:
- collaborator profile reads like a public publisher identity
- my content page reads like a collaborator management space
- no new social-network language has been introduced

- [ ] **Step 5: Commit**

```bash
git add templates/single/single-reci_author.php templates/page/dashboard/template-dashboard-my-content.php
git commit -m "refactor: align collaborator copy"
```

### Task 5: Update the High-Level Product Plan and Validate Journeys

**Files:**
- Modify: `docs/community-implementation-plan.md`
- Test: manual journey validation checklist

- [ ] **Step 1: Reflect any final naming or scope changes in the plan**

If implementation decisions in Tasks 1-4 change naming or page behavior, update `docs/community-implementation-plan.md` so it remains the source of truth.

Expected updates are limited to:
- Community page role
- Resources naming
- dashboard feed ownership language

- [ ] **Step 2: Validate the key user journeys manually**

Verify these flows in WordPress:

1. Visitor -> Member
2. Member -> Collaborator application
3. Member -> dashboard feed driven by followed interests/collaborators
4. Collaborator -> submit -> manage content

Checklist:
- `/community/` explains the system without acting like another archive
- `/dashboard/` remains the only personal feed surface
- `/submit/` remains properly collaborator-gated
- collaborator profile remains a public publisher profile
- resource language is visible where expected

- [ ] **Step 3: Commit**

```bash
git add docs/community-implementation-plan.md
git commit -m "docs: finalize community phase 2 plan"
```

## Self-Review

### Spec coverage
- Current state documented: yes
- Product direction documented: yes
- Community becomes orientation/gateway: covered in Task 1
- Feed remains dashboard-only: covered in Tasks 1 and 3
- Documents renamed to Resources in UI: covered in Task 2
- Collaborator content-centered model retained: covered in Task 4
- Deferred social/network features preserved: covered in plan doc and reinforced in Tasks 3 and 4

### Placeholder scan
- No `TBD`, `TODO`, or deferred implementation placeholders appear inside tasks.
- All tasks include exact file paths.
- Code-modifying steps include concrete code or exact replacement instructions.
- Verification steps include exact commands.

### Type consistency
- Internal post type remains `reci_document` throughout the plan.
- Internal collaborator profile CPT remains `reci_author` throughout the plan.
- Community remains a page/template concern, not a new subsystem.

## Execution Handoff

**Plan complete and saved to `docs/superpowers/plans/2026-08-20-community-phase-2.md`. Two execution options:**

**1. Subagent-Driven (recommended)** - I dispatch a fresh subagent per task, review between tasks, fast iteration

**2. Inline Execution** - Execute tasks in this session using executing-plans, batch execution with checkpoints

**Which approach?**
