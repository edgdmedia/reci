# Theme Settings Audit

This audit classifies each theme setting field as one of:

- **Frontend-used**: actively affects the site experience
- **Admin/indirect**: operationally important, but not directly rendered as frontend content
- **Partial**: some usage exists, but not fully or consistently wired
- **Unclear/likely unused**: no clear active usage found in the current theme code audit

It also includes a recommended action for each field:

- **Keep**
- **Wire up**
- **Hide from UI**
- **Remove**

---

## Branding

### `branding_reci_logo`
- Status: **Frontend-used**
- Found in:
  - `template-parts/site-header.php`
  - `template-parts/site-footer.php`
- Purpose:
  - renders main RECI logo in header/footer
- Recommendation: **Keep**

### `branding_partner_logo`
- Status: **Frontend-used**
- Found in:
  - `template-parts/site-header.php`
  - `template-parts/site-footer.php`
- Purpose:
  - renders partner/Pitt logo in header/footer
- Recommendation: **Keep**

### `branding_hub_subtitle`
- Status: **Frontend-used**
- Found in:
  - `template-parts/site-header.php`
  - `template-parts/site-footer.php`
- Purpose:
  - subtitle/label such as “Media Hub”
- Recommendation: **Keep**

### `branding_primary_color`
- Status: **Admin/indirect**
- Found in:
  - `inc/admin/admin-branding.php`
- Purpose:
  - admin/dashboard branding and likely intended theme color token
- Frontend use not clearly established in active templates
- Recommendation: **Wire up** if color theming is meant to be user-configurable; otherwise **Hide from UI**

### `branding_accent_color`
- Status: **Admin/indirect**
- Found in:
  - `inc/admin/admin-branding.php`
- Purpose:
  - admin/dashboard branding and likely intended accent token
- Frontend use not clearly established in active templates
- Recommendation: **Wire up** or **Hide from UI**

---

## Key Pages

### `pages_sign_in`
- Status: **Admin/indirect**
- Purpose:
  - mapped via page routing/auth helpers
- Notes:
  - frontend auth behavior relies more on `reci_pages` option and helper functions than direct `reci_setting()` calls
- Recommendation: **Keep**

### `pages_sign_up`
- Status: **Admin/indirect**
- Purpose:
  - supports custom sign-up routing
- Recommendation: **Keep**

### `pages_become_collaborator`
- Status: **Admin/indirect**
- Purpose:
  - collaborator onboarding page mapping
- Recommendation: **Keep**

### `pages_forgot_pw`
- Status: **Admin/indirect**
- Purpose:
  - forgot-password route mapping
- Recommendation: **Keep**

### `pages_donate`
- Status: **Partial**
- Purpose:
  - donate page mapping
- Notes:
  - likely useful operationally, but direct frontend usage not consistently visible in audit
- Recommendation: **Keep**

### `pages_community`
- Status: **Admin/indirect**
- Purpose:
  - community page mapping
- Recommendation: **Keep**

### `pages_reflection`
- Status: **Admin/indirect**
- Purpose:
  - reflection page mapping
- Recommendation: **Keep**

### `pages_home`
- Status: **Admin/indirect**
- Purpose:
  - home page mapping
- Recommendation: **Keep**

---

## Social Links

### `social_facebook`
- Status: **Unclear/likely unused**
- Direct frontend usage not clearly found in the audited theme templates
- Recommendation: **Wire up** if intended for footer/header social links, otherwise **Hide from UI**

### `social_twitter`
- Status: **Unclear/likely unused**
- Recommendation: **Wire up** or **Hide from UI**

### `social_instagram`
- Status: **Unclear/likely unused**
- Recommendation: **Wire up** or **Hide from UI**

### `social_youtube`
- Status: **Unclear/likely unused**
- Recommendation: **Wire up** or **Hide from UI**

### `social_linkedin`
- Status: **Unclear/likely unused**
- Recommendation: **Wire up** or **Hide from UI**

---

## Authentication

### `auth_enable_registration`
- Status: **Partial**
- Purpose:
  - allows/disallows user self-registration behavior
- Notes:
  - auth flow definitely matters, but settings linkage should be reviewed against actual registration enforcement
- Recommendation: **Keep**

### `auth_google_client_id`
- Status: **Partial / placeholder**
- Purpose:
  - intended for Google OAuth
- Notes:
  - clear storage exists, but full Google auth flow is not clearly active
- Recommendation: **Hide from UI** until implemented, or **Wire up** properly

### `auth_login_redirect`
- Status: **Partial**
- Purpose:
  - intended post-login redirect control
- Direct full usage not clearly established in audit
- Recommendation: **Wire up** or **Hide from UI**

---

## Homepage

### `hp_today_count`
- Status: **Partial / likely unused in current rendering**
- Notes:
  - current homepage appears to use query/hardcoded logic instead of this setting directly
- Recommendation: **Wire up** or **Hide from UI**

### `hp_quotes_count`
- Status: **Partial / likely unused in current rendering**
- Recommendation: **Wire up** or **Hide from UI**

### `hp_community_count`
- Status: **Partial / likely unused in current rendering**
- Recommendation: **Wire up** or **Hide from UI**

### `hp_featured_method`
- Status: **Frontend-used**
- Found in:
  - `templates/page/template-homepage.php`
- Purpose:
  - controls featured item selection behavior
- Recommendation: **Keep**

---

## About Page

### `about_c1_title`
- Status: **Frontend-used**
- Found in:
  - `templates/page/template-about.php`
- Recommendation: **Keep**

### `about_c1_copy`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c1_icon`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c2_title`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c2_copy`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c2_icon`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c3_title`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c3_copy`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `about_c3_icon`
- Status: **Frontend-used**
- Recommendation: **Keep**

---

## Footer

### `footer_tagline`
- Status: **Unclear / partially unused**
- Notes:
  - registered, but direct active usage is not obvious in the footer template audit slice
- Recommendation: **Wire up** or **Hide from UI**

### `footer_email`
- Status: **Frontend-used**
- Found in:
  - `template-parts/site-footer.php`
- Recommendation: **Keep**

### `footer_phone`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `footer_address`
- Status: **Frontend-used**
- Recommendation: **Keep**

### `footer_copyright`
- Status: **Frontend-used**
- Recommendation: **Keep**

---

## Analytics

### `analytics_ga4_id`
- Status: **Frontend-used / script injection path**
- Found in:
  - `inc/admin/theme-settings.php`
- Recommendation: **Keep**

### `analytics_gtm_id`
- Status: **Frontend-used / script injection path**
- Recommendation: **Keep**

### `analytics_pixel_id`
- Status: **Frontend-used / script injection path**
- Recommendation: **Keep**

---

## Content Defaults

### `content_articles_per_page`
- Status: **Unclear / likely not consistently wired**
- Recommendation: **Wire up** or **Hide from UI**

### `content_podcasts_per_page`
- Status: **Unclear / likely not consistently wired**
- Recommendation: **Wire up** or **Hide from UI**

### `content_videos_per_page`
- Status: **Unclear / likely not consistently wired**
- Recommendation: **Wire up** or **Hide from UI**

### `content_fallback_thumbnail`
- Status: **Unclear / likely not consistently wired**
- Recommendation: **Wire up** or **Hide from UI**

---

## Summary Recommendations

### Strong keep candidates
- Branding:
  - `branding_reci_logo`
  - `branding_partner_logo`
  - `branding_hub_subtitle`
- Key Pages
- Homepage:
  - `hp_featured_method`
- About Page fields
- Footer:
  - `footer_email`
  - `footer_phone`
  - `footer_address`
  - `footer_copyright`
- Analytics fields

### Likely keep, but review wiring
- `auth_enable_registration`
- `pages_donate`

### Best candidates to wire up or hide
- `branding_primary_color`
- `branding_accent_color`
- all Social Links fields
- `auth_google_client_id`
- `auth_login_redirect`
- `hp_today_count`
- `hp_quotes_count`
- `hp_community_count`
- `footer_tagline`
- all Content Defaults fields

### Good cleanup strategy
1. **Keep** settings that are clearly active in frontend behavior.
2. **Hide** settings that are visible in admin but not actually controlling anything yet.
3. **Wire up** only the ones that have near-term product value.
4. Avoid keeping large visible admin groups full of fields that do nothing.
