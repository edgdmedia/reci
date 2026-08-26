# Fresh Install Validation Checklist

Use this checklist to verify whether a clean WordPress install plus the RECI setup flow reproduces the intended website.

## 1. Clean baseline

- Start from a fresh WordPress install.
- Confirm there is no reused demo content, no old pages, and no old plugin state from earlier tests.

## 2. Install expected plugins

From `Appearance -> RECI Theme Setup`, confirm plugin status and install/activate as needed.

### Required
- Classic Editor
- EWWW Image Optimizer
- Yoast SEO

### Recommended
- WP Super Cache
- Really Simple Security
- All-in-One WP Security

## 3. Activate the theme

After activation, confirm these pages exist:

- Home
- Articles
- Learn
- Framework
- Glossary
- About
- Community
- Sign In
- Sign Up
- Become a Collaborator
- Forgot Password
- Reset Password
- Verify Email
- Submit Content
- Dashboard
- Donate
- Privacy Policy
- Terms of Use
- Cookies

## 4. Confirm page assignments

In WordPress settings, verify:

- `show_on_front = page`
- `page_on_front = Home`
- `page_for_posts = Articles`

## 5. Run the setup wizard

Complete the setup wizard and confirm:

- branding saves correctly
- primary and accent colors save correctly
- social links save correctly
- footer contact details save correctly
- analytics IDs save correctly
- registration toggle saves correctly

## 6. Import demo content

Run the full demo import, not a partial import.

Confirm content appears for:

- Articles
- Podcasts
- Videos
- Events
- Reflections
- Quotes
- Assessments
- Courses
- Testimonials
- Glossary Terms
- Collaborators
- Team
- Partners
- Core Pages

## 7. Save permalinks

Go to `Settings -> Permalinks` and click `Save Changes` once.

Then confirm these routes resolve:

- `/`
- `/articles/`
- `/learn/`
- `/framework/`
- `/glossary/`
- `/community/`
- `/collaborators/`
- `/sign-in/`
- `/sign-up/`
- `/submit/`
- `/dashboard/`
- `/privacy-policy/`
- `/terms-of-use/`
- `/cookies/`

## 8. Validate homepage reconstruction

Confirm the homepage renders correctly with populated sections:

- hero / featured content
- Today at RECI
- Reflection of the Day
- Articles
- Videos
- Podcasts
- Community section
- shared fallback thumbnail when featured images are missing

## 9. Validate shell pages

Confirm these pages load and are not dead links:

- About
- Community
- Framework
- Glossary
- Donate
- Privacy Policy
- Terms of Use
- Cookies

## 10. Validate auth and community flow

Confirm:

- sign-in page loads
- sign-up page loads
- forgot-password page loads
- reset-password page loads
- dashboard loads
- become-a-collaborator page loads
- submit content gating works correctly

## 11. Validate settings wiring

Confirm the saved settings affect the frontend correctly:

- logos render in header/footer
- subtitle renders correctly
- footer tagline renders correctly
- footer email, phone, and address render correctly
- social links render correctly
- analytics IDs output when set
- archive counts respect settings
- fallback thumbnail is used consistently

## 12. Pass / fail criteria

Call the install a **pass** only if:

- all required pages exist
- front page and posts page are assigned correctly
- demo content imports successfully
- main routes resolve correctly
- homepage is populated and visually correct
- auth/community/dashboard flows work
- no critical dead links remain in the primary site shell

Call it a **fail** if any of the above are not true.

## 13. If the validation fails

Capture:

- which step failed
- which page or route failed
- whether the issue happened before or after demo import
- whether the issue happened before or after permalink save
- screenshots if the problem is visual

Use that output as the fix list for the next pass.
