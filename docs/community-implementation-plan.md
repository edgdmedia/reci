# Community Implementation Plan

## Purpose
This document captures:

1. what currently exists in the RECI theme
2. the agreed product direction for Community
3. the scoped implementation plan for the next phase
4. the deferred items we are intentionally not building now

The goal is to stop incremental feature drift and work from a clear, bounded plan.

## Current State

### Roles and access
- Regular signed-up users function as Members.
- Approved contributors function as Collaborators.
- Collaborator approval is stored on user meta via `_reci_collaborator_status`.
- Status values currently in use: `member`, `pending`, `approved`, `rejected`, plus `guest` as a runtime state.

### Collaborator application flow
- Dedicated page exists at `/become-a-collaborator`.
- Applications are stored as a private admin CPT: `reci_collab_app`.
- Application fields currently captured:
  - first name
  - last name
  - email
  - organization
  - role/title
  - bio
  - website
- Admin review exists in wp-admin with:
  - application list table columns
  - review metabox
  - explicit `Approve Collaborator` / `Reject Application` actions
- Approval updates the member to collaborator status and unlocks submit access.

### Collaborator identity and public profile
- Internal CPT remains `reci_author`.
- UI has been relabeled to Collaborator in key places.
- Public collaborator archive and single page already exist.
- Collaborator single page currently supports:
  - profile content
  - collaborator follow button
  - filterable listing of collaborator content

### Submit flow
- `/submit` is now collaborator-gated.
- Submit behavior currently is:
  - guest: prompt to log in / sign up / become a collaborator
  - member: prompt to become a collaborator
  - pending collaborator: under-review messaging
  - approved collaborator: full submit wizard
- Server-side submission handler also enforces collaborator status.

### Following and personalization
- Members can currently follow:
  - topics
  - spheres
  - practice focus
  - target audience
  - collaborators
- These preferences live in dashboard settings.
- Dashboard now includes a personalized feed based on:
  - followed taxonomies
  - followed collaborators

### Notifications
- In-app notification system already exists.
- Email preferences already exist.
- Current notification support includes:
  - submission approved
  - personalized content notifications
  - collaborator publish notifications
  - collaborator application status notifications

### Dashboard
- Dashboard currently functions as the private user utility layer.
- Current pages include:
  - overview
  - my content
  - submit
  - bookmarks
  - notifications
  - journal
  - comments
  - profile
  - settings
- Dashboard overview now includes the personalized feed.
- Collaborators can manage their content through `My Content`.

### Resources / documents
- A dedicated CPT currently exists as `reci_document`.
- It is already wired into:
  - collaborator attribution
  - dashboard feed
  - notifications
  - my content list
  - submit wizard content type options
- Product naming still needs to be finalized from `Documents` to `Resources` in the next phase.

### Community page
- A first Community page implementation now exists.
- It currently behaves more like a hybrid hub with:
  - orientation content
  - quick actions
  - personalized feed section
  - featured collaborators
  - documents/resources section
  - events section
- This is functional, but it does not yet match the agreed product direction.

## Agreed Product Direction

### What Community is
Community should be an entry and participation layer.

It should help people understand:
- what Community is
- what Members get
- what Collaborators do
- how to join
- how to contribute

### What Community is not
Community should not become:
- a second homepage
- a duplicate article listing
- a duplicate CPT archive surface
- a collaborator listing surface when collaborator archive already exists
- a social network

### Dashboard role
Dashboard should remain the private utility layer.

Dashboard owns:
- personalized feed
- bookmarks
- notifications
- journal
- settings
- content management
- submit access
- collaborator application state

### Collaborator model
Collaborators are publishers and profile owners, not social-network actors.

We want:
- collaborator profile editing
- collaborator content management
- collaborator discovery through archive/profile pages
- collaborator follows for readers

We do not want:
- collaborator follow-back relationships
- messaging
- networking graph mechanics
- people-centric activity streams beyond content publishing

### Engagement model
Community interaction should stay content-centered:
- see people
- see their works
- follow collaborators
- follow taxonomies
- comment on content
- manage personal feed through dashboard

## Recommended Next-Phase Scope

### Phase 2 goal
Align the Community and dashboard experience with the agreed product model without introducing new social or networking mechanics.

### In scope

#### 1. Reframe Community page as orientation/gateway only
Update `/community/` so that it becomes:
- a public-facing explanation page
- a member/contributor pathway page
- a dashboard entry point for logged-in users

It should include:
- hero
- explanation of Member vs Collaborator roles
- join/contribute CTAs
- how it works section
- resource/value explanation
- dashboard CTA for logged-in users

It should not include:
- personalized feed
- featured collaborators listing
- content listings duplicating archives
- event listing duplicating event archive

#### 2. Keep personalized feed only in dashboard
Dashboard remains the only place where the personal feed lives.

Requirements:
- reinforce the feed as a private member utility
- keep feed shaped by followed taxonomies and followed collaborators
- avoid duplicating it in Community

#### 3. Finalize Collaborator experience
Make collaborator workflows coherent and complete.

Requirements:
- collaborator application remains separate from member signup
- collaborator review flow remains admin-driven
- collaborator single page remains public identity + published works
- collaborators can manage profile and content through existing structures

#### 4. Rename Documents to Resources in product/UI language
Treat this CPT as the resource library layer.

Requirements:
- rename visible UI copy from Documents to Resources
- keep the type distinct from general media/content types
- resource archive and single pages should eventually reflect the naming

#### 5. Review content management surface for collaborators
Ensure the collaborator dashboard experience is sufficient.

Requirements:
- `My Content` includes resource items
- submit flow includes resource selection
- profile/content management language reflects collaborator role where appropriate

#### 6. Clarify notifications and settings copy
No large new notification features, just cleanup and clarity.

Requirements:
- make collaborator-follow notifications understandable
- keep email opt-in balanced
- keep notification system content-centered

## Out of Scope for This Phase

These items should be explicitly deferred.

### Social / network mechanics
- collaborator-to-collaborator follow-back relationships
- direct messaging
- mutual/friend-style relationships
- networking graph features
- people-based activity streams beyond published content

### Group / mentorship mechanics
- mentorship matching
- groups, circles, cohorts, or private community spaces
- community rooms or discussion hubs outside content comments

### Advanced member identity
- public member profiles
- extended member profile systems
- member activity pages

### External integrations
- Localist sync
- CRSP external data sync/import

### Advanced automation
- weekly digest automation
- more complex notification orchestration

### Expanded resource architecture
- resource subtype taxonomy overhaul
- advanced resource library filtering/redesign

## Proposed Implementation Order

### Step 1
Refactor `/community/` to remove feed/archive duplication and become an orientation/gateway page.

### Step 2
Rename visible `Documents` copy to `Resources` across user-facing Community/dashboard surfaces.

### Step 3
Review collaborator dashboard/profile/content language so it consistently reflects the collaborator role.

### Step 4
Do a focused cleanup pass on settings and notification copy.

### Step 5
Validate end-to-end journeys:
- visitor -> member
- member -> collaborator application
- member -> personalized dashboard feed
- collaborator -> submit -> manage content

## Open Questions to Resolve Before More Code

### 1. Resources naming
Should the internal CPT remain `reci_document` while all UI copy becomes `Resources`, or do we want a broader structural rename later?

Recommendation:
- keep internal CPT as-is for now
- rename UI/product language only in the next phase

### 2. Community logged-in behavior
For logged-in users, should `/community/` show only a dashboard CTA, or also lightweight account-aware messaging?

Recommendation:
- show account-aware CTA/state
- do not show the feed itself there

### 3. Collaborator profile editing
Should collaborator profile editing be handled directly in dashboard now, or stay mostly admin-managed until later?

Recommendation:
- leave full profile editing mostly out of this phase unless there is already a clean path
- keep scope focused on role clarity and navigation

## Success Criteria for the Next Phase

The next phase is successful if:
- Community clearly explains the ecosystem without duplicating archives
- Dashboard is clearly the home of the personal feed
- Collaborator workflows are understandable end-to-end
- Resources are positioned as a distinct practical/reference library
- We avoid drifting into social network mechanics
