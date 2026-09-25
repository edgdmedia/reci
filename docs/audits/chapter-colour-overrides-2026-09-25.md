# Audit: the colour override system

2026-09-25. Not a chapter family - the layer every family sits on.

## What reads, what writes

| | Count | Which |
|---|---|---|
| A style declares | 5 | primary, bg, heading, body, accent |
| The builder's panel edits | 8 | + surface, surface_text, muted |
| The templates read | 15 | + text, soft_text, card, card_strong, border, border_soft, hotspot_ring |

The colour panel is hardcoded JSX in the builder bundle, not driven by
registry fields - which is why no family declares a `color_*` field and the
override still worked. It is the one editable value in the system whose
source of truth is not the registry.

## Findings

### The seven nobody sets were undefined for half the styles

Only three stylesheets declare cards, borders and the hotspot ring, scoped
to `body.reci-style-documentary`, `-immersive-dark` and `-breaking-chains`.
Four styles are offered. For voices-of-resistance, march-toward-justice and
racial-disparities those seven custom properties were never defined, so all
**93 bare uses** of `var(--reflection-card)` and friends resolved to nothing:
no card, no border, no ring.

Two of the four styles are light - march-toward-justice at `#e6e6e6` and
racial-disparities at `#f4f4f4` - so even a universal dark default would
have been wrong for them. That was the reason a previous `:root` base was
removed rather than extended.

### The `__color_*` fallbacks were dead

Eight reads of `$props['__color_bg']` and the like. Nothing in the theme,
the demos or the builder bundle ever wrote that spelling.

### `$style` was never initialised

The per-chapter colour block appended to an undefined variable.

## What changed

A single resolver, `reci_reflection_resolve_palette()`, fills the colours
nobody named: cards and borders as tints of the foreground over the
background, the hotspot ring as the accent held back to 0.35, and the two
text tiers following the heading and body the style did name. Whether the
tint is white or near-black follows the background's luminance, so a light
style gets dark tints and a dark style light ones.

The derived values match the three existing stylesheets where those
stylesheets already agreed, so nothing that worked changes.

Stated and derived are kept apart, which is the point:

- **Stated** - what a style, a global setting or a chapter override names -
  goes inline, where it beats everything.
- **Derived** goes into a `:where(.reci-reflection-page)` rule, which carries
  no specificity, so a style's own stylesheet and any inline colour both win.
  It fills gaps and never overrules.

`color_surface` is the exception: it stays inline, because a light style with
no surface of its own has to beat the theme's dark default, which used to put
light-palette text on a dark card at 1.02:1.

A chapter that overrides colours also emits the derived values inline, since
its background may be lighter or darker than the page's and its cards have
to follow it.

Dead `__color_*` reads removed. `$style` initialised.

Tests: `tests/test-palette-resolver.php`, 41 assertions.

## Still open

The colour panel remains hardcoded in a bundle with no buildable source, so
the seven derived colours cannot be exposed as controls from PHP. They are
now correct by default and settable by hand in a blueprint, which is the
most that can be done without rebuilding the builder.
