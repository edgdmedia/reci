# Collaborator Import Notes

Source directory:
- `https://crsp.pitt.edu/racial-equity-collaboratory/searchable-collaboratory-database`

Observed live filters:
- `Research or Work Focus`
- `Affiliation`

Theme mapping:
- `Research or Work Focus` -> `reci_practice_focus`
- `Affiliation` -> `reci_affiliation`

Observed list behavior:
- 4 paginated listing pages
- approx. 109 collaborator profile URLs
- list cards expose:
  - profile URL
  - name
  - email

Observed profile-page field shapes:
- page title / collaborator name
- personal biography block
- one or more research/work focus values
- apparent role/title near the email block
- affiliation values may need to be inferred or scraped from dedicated labels where present
- website may need field-level filtering because generic site links can appear before personal links

Implementation direction:
- scrape collaborator list in batches
- scrape profile detail pages in batches
- normalize into local dataset for `reci_author`
- assign `reci_practice_focus` and `reci_affiliation` terms on import
