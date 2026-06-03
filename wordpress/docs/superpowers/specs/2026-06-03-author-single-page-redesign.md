# Author Single Page Redesign

## Summary

Restructure `single-reci_author.php` into three stacked sections: compact header (avatar + name + title), an About section (bio + full profile text + avatar), and the existing posts listing (search + 3-col grid + pagination). The archive list card drops the excerpt — name and title only.

## Layout

```
┌──────────────────────────────────────┐
│  [avatar]  Ron Idoko, M.Ed.          │  ← header (no bio)
│            Founding Director, RECI…  │
├──────────────────────────────────────┤
│  About the Author                    │  ← About section
│  ────────────────────────            │
│  [avatar]  Bio paragraph…            │
│            Full profile content      │
│            (the multi-paragraph      │
│             body text)               │
├──────────────────────────────────────┤
│  Filter by:    [search]              │  ← existing filter + listing
│  ┌─────┐ ┌─────┐ ┌─────┐            │
│  │Post │ │Post │ │Post │            │
│  └─────┘ └─────┘ └─────┘            │
│          [pagination]                │
└──────────────────────────────────────┘
```

## Sections

### 1. Header strip
- **Left:** avatar (img or fallback initials circle, `w-20 h-20 rounded-full`), name (h1), title (p)
- **Right:** empty / nothing
- Bio is **removed** from this area

### 2. About section
- A new section between the header strip and the filter bar
- Amber dot + heading "About the Author"
- Horizontal rule
- Flex row: avatar (`w-28 h-28 rounded-full`) on left, excerpt (bio) + full `the_content()` on right
- Follows the same pattern as the "About the author" card in `single-reci_video.php`

### 3. Posts listing
- Unchanged from current implementation
- Search filter bar + `reci_media_hub_render_listing()` with 3-col grid + pagination

## Archive list card

- Remove excerpt/description from `archive-reci_author.php` grid items
- Keep: avatar, name, title only

## Files Changed

| File | Change |
|------|--------|
| `single-reci_author.php` | Restructure into header / About / posts; add About section with avatar + bio + content |
| `archive-reci_author.php` | Remove excerpt from card |
| `inc/demo-content.php` | Already done — Ron Idoko seed (separate work) |

## Visual reference

About section style follows the author card in `single-reci_video.php` — amber dot heading, flex row with avatar left and text right.
