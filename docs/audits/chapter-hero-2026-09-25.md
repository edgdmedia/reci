# Chapter audit: `hero`

2026-09-25. Four layers: elements, editability, behaviour across styles,
reconciliation with the demos.

## 1. Elements

Loader `modules/reflection-system/templates/hero.php` delegates to
`templates/heroes/<variant>.php`. Seven variants are offered; eight files
exist (`default.php` is a delegator to `documentary`).

| Element | documentary | narrative | testimonial | analytical | immersive-dark | protest-march | protest-march-dark |
|---|---|---|---|---|---|---|---|
| eyebrow | y | y | y | y | y | y | y |
| title | y | y | y | y | y | y | y |
| title_accent | - | - | - | - | y | - | - |
| subtitle | y | y | y | **-** | y | y | y |
| body | y | y | y | y | y | y | y |
| caption | y | y | y | **-** | y | y | **-** |
| foreground_image | - | - | - | - | y | - | - |
| background image | y | y | y | y | y | y | y |
| actions | y | y | y | y | y | y | y |
| alignment | y | y | y | y | partial | y | **-** |

`analytical` drops subtitle and caption; `protest-march-dark` drops caption
and all alignment. Same family, different element sets - which is the thing
we keep saying must not happen.

## 2. Editability

Declared fields: eyebrow, title, title_accent, subtitle, body,
foreground_image, caption, use_background_image, background_image,
overlay_intensity, overlay_color, align_horizontal, align_vertical, actions,
plus the shared menu and transition fields.

**`id` is not a declared field.** Every hero in every demo carries one, and
`continue_target` and the menu anchor both point at it. Before the
preservation guard landed, opening a hero in the builder and saving dropped
it, breaking every link into that chapter.

Derived at render time by `normalize_component_props()`, correctly absent
from the field list: `section_class`, `overlay_rgb`, `overlay_opacity`,
`align_h_class`, `align_v_class`, `align_text_class`.

`bg_type` is derived from `background_type` and read by no template. Dead.

## 3. Behaviour

### The overlay derivation discards what the author set

`normalize_component_props()` assigns `overlay_rgb` and `overlay_opacity`
unconditionally, from `overlay_color` and `overlay_intensity`. A chapter that
sets `overlay_rgb`/`overlay_opacity` directly has both silently replaced
before its template's `wp_parse_args()` defaults are ever consulted - so the
per-variant defaults in `analytical.php` (white) are dead code too.

### `analytical-light` is not a hero variant

`analytical-light` and `analytical-dark` belong to **data-cards**. The
racial-disparities style template declares a *hero* with
`'variant' => 'analytical-light'`, which no hero file provides, so it falls
back through `default.php` to **documentary**.

## 4. Reconciliation: why racial-disparities is unreadable

The reported "title and button are not visible" is these three compounding:

1. The style is light: bg `#f4f4f4`, heading `#111111`, accent `#2A4494`.
2. Its hero asks for `analytical-light`, gets `documentary`.
3. Its hand-set white overlay (`overlay_rgb: 255,255,255`,
   `overlay_opacity: 0.70`) is overwritten with black at 0.72, because the
   chapter sets no `overlay_color`.

Near-black heading and near-black bordered buttons, over a black overlay
running 0.62 to 0.79. Nothing to see.

## Fixes applied

1. `id` added to hero's fields.
2. The overlay derivation now defers: the builder's controls win, failing
   that an overlay written into the blueprint stands, failing both the key is
   left unset so the variant's own default applies. `overlay_opacity` is
   always a float.
3. `analytical` split into `analytical-light` and `analytical-dark`. The
   plain variant is off the picker; `analytical.php` remains as an alias to
   the light card so blueprints already naming it keep rendering rather than
   falling through to documentary. racial-disparities' `base_variant` now
   points at `analytical-light`, which also resolves correctly for
   data-cards.
4. `analytical` gained subtitle and caption; `protest-march-dark` gained
   caption and alignment. All eight variants now render the same six
   elements plus alignment.
5. The dead `bg_type` derivation is gone.

### Also fixed, found while verifying

`immersive-dark` declared all three alignment classes and applied only
`align_text_class` - and `.chapter-intro`/`.intro-content` hardcoded the
centring in CSS at the same specificity as the Tailwind utilities, so even
that one was decided by stylesheet order. The section now carries the
horizontal and vertical classes, and the CSS centring moved into `:where()`,
which has no specificity: the controls win, and the legacy
`chapter-threshold-intro` template - which emits no such classes - stays
centred.

### Left alone

The duplicated `'align_text_class' => 'text-left'` default appears in several
hero files. Harmless, and removing it everywhere is churn better done with
the family that owns each file.

Tests: `tests/test-hero-overlay.php`, 19 assertions.
