# Reflection Studio v2 — Design Spec
**Date:** 2026-06-26  
**Status:** Approved for implementation  
**Module:** `modules/reflection-studio/`  
**Replaces:** Nothing — runs side-by-side with `modules/reflection-system/` (v1)

---

## 1. Goals

Build a new, clean reflection builder + renderer system ("Reflection Studio v2") alongside the existing system without breaking any live content. The old system continues running. A post flag determines which renderer serves the content. Posts migrate one at a time.

### Core design goals
- **Chapter = content container.** A chapter declares its elements (heading, body, background, items, etc.) and its content fields. It does not own its visual style.
- **Style = design expression.** A style for a chapter implements the visual design for that chapter's elements — including layout structure, not just colours.
- **Template = composed view.** A template combines a global style + a curated set of chapters with pre-filled content. It is a starting-point blueprint, fully editable after loading.
- **Developer extensibility.** Adding a new chapter or style means adding one file/directory and one registry import. No changes to the builder UI, no PHP template changes.
- **Three-level colour system.** Reflection → Style defaults → Per-element override. Every element a chapter exposes is individually colour-editable.

---

## 2. Module Directory Structure

```
modules/reflection-studio/
  src/
    types/
      blueprint.ts          # v2 blueprint schema + all interfaces
    chapters/               # one directory per chapter family
      hotspot-stage/
        definition.ts       # TypeScript content type + ChapterDefinition object
        styles/
          default.tsx       # default style component (JSX + interactions)
          immersive-dark.tsx
          documentary.tsx
        hooks/              # optional: shared interaction logic across styles
          useHotspotInteraction.ts
        index.ts            # exports ChapterDefinition with styles wired in
      progressive-text/
        ...                 # same pattern
      [other families]/
    styles/                 # global style definitions (shared design language)
      immersive-dark/
        tokens.ts           # color tokens, font tokens, spacing
        guideline.md        # design rules for implementors (colors, fonts, layout intent)
        index.ts            # exports StyleDefinition
      documentary/
        ...
      [other styles]/
    registry/
      chapters.ts           # imports + exports all ChapterDefinitions
      styles.ts             # imports + exports all StyleDefinitions
      index.ts              # assembles full registry, exports useRegistry()
    builder/
      components/
        App.tsx             # root: left panel + preview pane
        panel/
          LeftPanel.tsx     # tabbed panel (Settings / Chapters / Edit)
          tabs/
            SettingsTab.tsx     # global style, palette, nav, template picker
            ChaptersTab.tsx     # chapter list (stacked, drag/drop, add/remove)
            EditTab.tsx         # settings for currently selected chapter
          ChapterRow.tsx        # single chapter row in Chapters tab
          DynamicFieldRenderer.tsx  # renders fields from definition.fields schema
          ColorEditor.tsx       # 3-level colour editor (§6)
          TemplateGallery.tsx   # modal: template picker
        preview/
          PreviewPane.tsx       # iframe wrapper + responsive size controls
          ResponsiveToolbar.tsx # Desktop / Tablet / Mobile toggle
      store/
        builderStore.ts     # Zustand store
      main.tsx
    renderer/
      components/
        App.tsx
        ChapterRenderer.tsx # resolves family → style component, injects tokens
      hooks/
        useColorResolution.ts  # resolves 3-level colour for each element (§6)
        useProgression.ts      # chapter unlock/scroll logic
        useGSAP.ts             # GSAP instance management
        useScrollTrigger.ts    # GSAP ScrollTrigger wrapper
        useIntersection.ts     # Intersection Observer helper
      main.tsx
  inc/
    class-studio-bootstrap.php    # require_once all PHP files
    class-studio-meta-box.php     # registers "Reflection Studio v2" meta box on posts
    class-studio-renderer.php     # enqueues renderer bundle on front end
    class-studio-api.php          # REST: GET blueprint, POST save, POST preview
  assets/
    js/
      studio-builder.js     # vite build output
      studio-renderer.js    # vite build output
  vite.config.js
  package.json
  tsconfig.json
  DEVELOPER-GUIDE.md        # how to add a new chapter / new style
```

---

## 3. Blueprint v2 Schema

```typescript
// src/types/blueprint.ts

/** Top-level document stored in post meta `_reflection_studio_blueprint` */
export interface BlueprintV2 {
  version: 3;
  system: 'reflection-studio';
  globalStyle: string;          // 'immersive-dark' | 'documentary' | etc.
  palette: ReflectionPalette;   // reflection-level colour overrides
  nav: NavConfig;
  chapters: ChapterInstance[];
}

/** The reflection-level colour palette (Level 1 of colour system) */
export interface ReflectionPalette {
  bg?: string;
  heading?: string;
  body?: string;
  accent?: string;
  primary?: string;
  surface?: string;
  surface_text?: string;
  muted?: string;
}

export interface NavConfig {
  enabled: boolean;
  back_label?: string;
  back_url?: string;
  audio_label?: string;
  audio_url?: string;
  menu_enabled?: boolean;
}

/** A single chapter instance inside a blueprint */
export interface ChapterInstance {
  id: string;                   // uuid, stable across edits
  family: string;               // e.g. 'hotspot-stage'
  style: string | 'inherit';    // style key or 'inherit' → resolves to globalStyle
  colorMode: 'inherit' | 'style-defaults' | 'custom';
  // 'inherit'       → use reflection palette
  // 'style-defaults' → use this style's default token values
  // 'custom'        → use elementColors below
  elementColors: Record<string, string>; // keyed by element slot name
  content: Record<string, unknown>;      // typed per family, validated by definition
}

/**
 * Serialized registry passed from PHP to the builder SPA via window.RECIStudioBuilderConfig.
 * PHP generates this from the TypeScript registry at build time (JSON export step).
 * Contains only data — no React components (those are bundled in the JS).
 */
export interface SerializedRegistry {
  chapters: SerializedChapterDefinition[];
  styles: StyleDefinition[];
}

export interface SerializedChapterDefinition {
  family: string;
  label: string;
  description?: string;
  elements: Record<string, ElementDefinition>;
  fields: Record<string, FieldDefinition>;
  defaultContent: Record<string, unknown>;
  defaultStyle: string;
  availableStyles: string[];  // list of style keys implemented for this family
}

/** Used by builder host page to pass config to the SPA */
export interface StudioBuilderConfig {
  registry: SerializedRegistry;
  templates: Record<string, TemplateDefinition>;
  blueprint: BlueprintV2;
  postId: number;
  saveEndpoint: string;
  nonce: string;
}
```

---

## 4. Chapter Registration Contract

Every chapter family exports one `ChapterDefinition` object from its `index.ts`.  
**No custom settings panel is written per chapter.** The builder renders all fields  
dynamically from `definition.fields` using `DynamicFieldRenderer`. This means  
adding a new chapter requires zero changes to the builder UI code.

### 4a. Type interfaces

```typescript
// src/types/blueprint.ts (continued)

export interface ElementDefinition {
  label: string;
  description?: string;
  /** Maps this element to a colour slot in StyleTokens, e.g. 'heading', 'bg', 'accent' */
  colorSlot: keyof StyleTokens;
}

export type FieldType =
  | 'text' | 'textarea' | 'richtext'
  | 'select' | 'media' | 'color' | 'range' | 'url'
  | 'toggle'              // boolean on/off
  | 'repeater'            // array of sub-fields
  | 'chapter-target';     // dropdown of chapter IDs in the current blueprint

export interface FieldDefinition {
  type: FieldType;
  label: string;
  description?: string;
  required?: boolean;
  options?: Record<string, string>;            // for 'select'
  itemFields?: Record<string, FieldDefinition>; // for 'repeater'
  maxItems?: number;
  maxLength?: number;
  /** Conditionally show this field. Key is another field name, value is the
   *  required value(s) for this field to appear. */
  show_if?: Record<string, string | boolean | string[]>;
}

export interface ChapterDefinition<TContent = Record<string, unknown>> {
  family: string;        // kebab-case unique ID, e.g. 'hotspot-stage'
  label: string;         // shown in chapter picker and chapter list
  description?: string;  // shown in chapter picker tooltip

  /** Visual elements this chapter exposes — drives the colour editor panel */
  elements: Record<string, ElementDefinition>;

  /** Content field schema — drives DynamicFieldRenderer in the Edit tab */
  fields: Record<string, FieldDefinition>;

  /** Used when the user first adds this chapter to a blueprint */
  defaultContent: TContent;

  /** Which style renders if chapter.style === 'inherit' and no globalStyle is set */
  defaultStyle: string;

  /**
   * Style components keyed by style ID.
   * Renderer falls back to 'default' if the requested style is not listed.
   * NOTE: Omitted from SerializedChapterDefinition (PHP-side); bundled in JS only.
   */
  styles: Record<string, StudioStyleComponent<TContent>>;
}

/** A style component for a chapter. Owns its own JSX, layout, and interactions. */
export type StudioStyleComponent<TContent = Record<string, unknown>> = (
  props: StyleComponentProps<TContent>
) => JSX.Element | null;

export interface StyleComponentProps<TContent = Record<string, unknown>> {
  content: TContent;
  /** Fully resolved colours for every element slot this chapter declared */
  resolvedColors: ResolvedColors;
  status: 'active' | 'locked' | 'completed';
  onComplete: () => void;
  chapterId: string;
}

export interface ResolvedColors {
  [elementSlot: string]: string;
}
```

### 4b. Complete chapter example — `hotspot-stage`

Every file in the chapter directory follows this pattern exactly.

```typescript
// src/chapters/hotspot-stage/definition.ts

import type { ChapterDefinition, FieldDefinition, ElementDefinition } from '../../types/blueprint';

/** Typed content shape for this chapter */
export interface HotspotStageContent {
  background_image: string;
  instruction: string;
  hotspots: Array<{
    key: string;
    left: string;   // CSS percentage string, e.g. '35%'
    top: string;
    title: string;
    body: string;
    icon?: string;  // optional media URL
  }>;
  transition_mode: 'button' | 'scroll' | 'auto';
  continue_label?: string;
  continue_target?: string;
  include_in_menu: boolean;
  menu_label?: string;
}

const elements: Record<string, ElementDefinition> = {
  background:      { label: 'Background',      colorSlot: 'bg' },
  instruction:     { label: 'Instruction text', colorSlot: 'muted' },
  hotspot_icon:    { label: 'Hotspot icon',     colorSlot: 'accent' },
  hotspot_title:   { label: 'Hotspot title',    colorSlot: 'heading' },
  hotspot_body:    { label: 'Hotspot body',     colorSlot: 'body' },
  hotspot_surface: { label: 'Hotspot card bg',  colorSlot: 'surface' },
  hotspot_text:    { label: 'Hotspot card text',colorSlot: 'surface_text' },
  overlay:         { label: 'Overlay',          colorSlot: 'bg' },
};

const fields: Record<string, FieldDefinition> = {
  background_image: { type: 'media',    label: 'Background Image', required: true },
  instruction:      { type: 'text',     label: 'Instruction text', maxLength: 80 },
  hotspots: {
    type: 'repeater',
    label: 'Hotspots',
    maxItems: 8,
    itemFields: {
      key:   { type: 'text',     label: 'ID key (unique, no spaces)' },
      left:  { type: 'text',     label: 'Position left (%)',  description: 'e.g. 35%' },
      top:   { type: 'text',     label: 'Position top (%)',   description: 'e.g. 60%' },
      title: { type: 'text',     label: 'Title' },
      body:  { type: 'textarea', label: 'Body' },
      icon:  { type: 'media',    label: 'Icon image (optional)' },
    },
  },
  transition_mode: {
    type: 'select',
    label: 'Transition mode',
    options: { button: 'Button (manual)', scroll: 'Scroll (auto)', auto: 'Auto (no interaction)' },
  },
  continue_label:  { type: 'text',           label: 'Continue button label', show_if: { transition_mode: 'button' } },
  continue_target: { type: 'chapter-target', label: 'Continue target',       show_if: { transition_mode: 'button' } },
  include_in_menu: { type: 'toggle',  label: 'Include in menu' },
  menu_label:      { type: 'text',    label: 'Menu label', show_if: { include_in_menu: true } },
};

export const defaultContent: HotspotStageContent = {
  background_image: '',
  instruction: 'Tap the icons to explore',
  hotspots: [],
  transition_mode: 'scroll',
  include_in_menu: false,
};
```

```typescript
// src/chapters/hotspot-stage/index.ts

import type { ChapterDefinition } from '../../types/blueprint';
import { elements, fields, defaultContent } from './definition';
import type { HotspotStageContent } from './definition';
import HotspotStageDefault       from './styles/default';
import HotspotStageImmersiveDark from './styles/immersive-dark';
import HotspotStageDocumentary   from './styles/documentary';

const HotspotStage: ChapterDefinition<HotspotStageContent> = {
  family:        'hotspot-stage',
  label:         'Hotspot Stage',
  description:   'A full-bleed image with interactive hotspot markers.',
  elements,
  fields,
  defaultContent,
  defaultStyle:  'default',
  styles: {
    'default':        HotspotStageDefault,
    'immersive-dark': HotspotStageImmersiveDark,
    'documentary':    HotspotStageDocumentary,
  },
};

export default HotspotStage;
```

```tsx
// src/chapters/hotspot-stage/styles/immersive-dark.tsx
// Owns: JSX structure, layout, all interactions, animations.

import { useRef } from 'react';
import type { StyleComponentProps } from '../../../types/blueprint';
import type { HotspotStageContent } from '../definition';
import { useHotspotInteraction } from '../hooks/useHotspotInteraction';
import { useScrollTrigger }      from '../../../renderer/hooks/useScrollTrigger';

export default function HotspotStageImmersiveDark({
  content, resolvedColors, status, onComplete, chapterId,
}: StyleComponentProps<HotspotStageContent>) {
  const containerRef = useRef<HTMLDivElement>(null);
  const { activeKey, activate } = useHotspotInteraction(content.hotspots);
  useScrollTrigger(containerRef, onComplete, content.transition_mode);

  return (
    <div
      ref={containerRef}
      id={chapterId}
      className="rs-hotspot-stage rs-hotspot-stage--immersive-dark"
      style={{ '--rs-bg': resolvedColors.background, '--rs-accent': resolvedColors.hotspot_icon } as React.CSSProperties}
    >
      <img src={content.background_image} className="rs-hotspot-stage__bg" alt="" />
      <p className="rs-hotspot-stage__instruction" style={{ color: resolvedColors.instruction }}>
        {content.instruction}
      </p>
      {content.hotspots.map((spot) => (
        <button
          key={spot.key}
          className={`rs-hotspot-stage__pin ${activeKey === spot.key ? 'is-active' : ''}`}
          style={{ left: spot.left, top: spot.top, background: resolvedColors.hotspot_icon }}
          onClick={() => activate(spot.key)}
          aria-expanded={activeKey === spot.key}
        >
          {spot.icon && <img src={spot.icon} alt="" />}
        </button>
      ))}
      {activeKey && (() => {
        const spot = content.hotspots.find(h => h.key === activeKey)!;
        return (
          <div
            className="rs-hotspot-stage__card"
            style={{ background: resolvedColors.hotspot_surface, color: resolvedColors.hotspot_text }}
          >
            <h3 style={{ color: resolvedColors.hotspot_title }}>{spot.title}</h3>
            <p  style={{ color: resolvedColors.hotspot_body  }}>{spot.body}</p>
          </div>
        );
      })()}
    </div>
  );
}
```

### 4c. DynamicFieldRenderer

`DynamicFieldRenderer` in the builder reads `definition.fields` and renders the  
correct input for each field type. No per-chapter settings panel is needed.  
All field types (`text`, `textarea`, `richtext`, `select`, `media`, `color`,  
`range`, `url`, `toggle`, `repeater`, `chapter-target`) have a corresponding  
field component in `src/builder/components/panel/fields/`.

A chapter may optionally export a `customSettingsSection?: StudioSettingsComponent`  
from its `index.ts` if it needs UI that cannot be expressed as a field schema  
(e.g. an interactive position picker for hotspot coordinates). This replaces  
the field renderer only for that chapter's extra section — standard fields  
still render from the schema.

---

## 5. Style Registration Contract

Every global style exports one `StyleDefinition` from its `index.ts`.  
A style sets a design language: tokens + which layout it prefers.  
Each chapter independently implements that style in its own `styles/<name>.tsx`.

```typescript
// src/types/blueprint.ts (continued)

export interface StyleTokens {
  // Colour tokens (CSS custom properties)
  bg: string;
  heading: string;
  body: string;
  accent: string;
  primary: string;
  surface: string;
  surface_text: string;
  muted: string;
  // Typography tokens
  headingFont: string;     // e.g. "'EB Garamond', serif"
  bodyFont: string;        // e.g. "'Inter', sans-serif"
  baseFontSize: string;    // e.g. '18px'
  // Spacing
  spacingUnit: string;     // e.g. '1.5rem'
}

export interface StyleDefinition {
  id: string;              // kebab-case, e.g. 'immersive-dark'
  label: string;           // e.g. 'Immersive Dark'
  description?: string;
  tokens: StyleTokens;     // the default values for this style's design language
}
```

---

## 6. Three-Level Colour System

Every chapter instance resolves its element colours through three layers, evaluated in order:

```
Level 1 — Reflection palette (BlueprintV2.palette)
  The page-wide colour overrides set by the user on the reflection.

Level 2 — Style defaults (StyleDefinition.tokens)
  The default colours of the chapter's active style.

Level 3 — Chapter element overrides (ChapterInstance.elementColors)
  Per-element overrides set by the user on that specific chapter.
```

**Resolution logic (`colorMode` on the chapter):**

| `colorMode`      | Base source      | Per-element overrides |
|------------------|------------------|-----------------------|
| `inherit`        | Reflection palette (Level 1) | Applied on top |
| `style-defaults` | Style tokens (Level 2) | Applied on top |
| `custom`         | Style tokens (Level 2) | Applied on top (full control) |

`useColorResolution(chapter, globalStyle, reflectionPalette)` returns `ResolvedColors` — a flat map of `elementSlot → hexColour` passed directly to the style component as `resolvedColors`.

**In the builder:** When a user expands a chapter's colour settings, they see:
1. A radio group: "Inherit from reflection / Use style defaults / Custom"
2. A grid of colour pickers — one per element slot declared by the chapter definition.
   Each shows the currently resolved value with the source label ("from reflection", "style default", "your override").

---

## 7. Registry Assembly

```typescript
// src/registry/chapters.ts
import HotspotStage from '../chapters/hotspot-stage';
import ProgressiveText from '../chapters/progressive-text';
// ... one import per family

export const ALL_CHAPTERS: ChapterDefinition[] = [
  HotspotStage,
  ProgressiveText,
  // ...
];

// src/registry/styles.ts
import ImmersiveDark from '../styles/immersive-dark';
import Documentary from '../styles/documentary';
// ...

export const ALL_STYLES: StyleDefinition[] = [
  ImmersiveDark,
  Documentary,
  // ...
];

// src/registry/index.ts
export function buildRegistry(): Registry { ... }
export function useRegistry(): Registry { ... }  // React context hook
```

Adding a new chapter = create the directory, add one import line to `chapters.ts`.  
Adding a new style = create the directory, add one import line to `styles.ts`.

---

## 8. Templates

A template is a complete starting-point `BlueprintV2`, stored as JSON in `src/templates/`.

```typescript
export interface TemplateDefinition {
  id: string;
  label: string;
  description?: string;
  thumbnail?: string;         // URL to a preview image
  blueprint: BlueprintV2;     // complete starting blueprint
}
```

Loading a template in the builder:
1. Shows a preview thumbnail and chapter list in the Template Gallery modal.
2. On confirm: replaces the entire builder canvas with the template blueprint (with undo warning).
3. Or: "Apply style only" — copies `globalStyle` + `palette` without touching chapters.

---

## 9. Builder UI

**Layout: Elementor-style.** Left panel is fixed-width with tabs. Preview fills the remaining width. No right panel — everything lives in the left panel tabs.

```
┌───────────────────────────────────────────────────────────────────┐
│ WP Admin bar                                                      │
│ [← Back]  Reflection Studio v2  [💾 Save]  [Switch to v1]        │
├─────────────────────┬─────────────────────────────────────────────┤
│  Left Panel (320px) │  Preview Pane (fills remaining width)       │
│                     │                                             │
│ ┌─────────────────┐ │  ┌─ Responsive toolbar ──────────────────┐ │
│ │ Settings│Chapters│ │  │  [🖥 Desktop] [📱 Tablet] [📱 Mobile] │ │
│ │         │ Edit   │ │  └───────────────────────────────────────┘ │
│ └─────────────────┘ │                                             │
│                     │  ┌─ iframe ───────────────────────────────┐ │
│  [active tab body]  │  │                                        │ │
│                     │  │  studio-renderer.js renders here       │ │
│                     │  │  with current blueprint JSON           │ │
│                     │  │                                        │ │
│                     │  │  Responsive widths:                    │ │
│                     │  │  Desktop → 100% of pane width          │ │
│                     │  │  Tablet  → 768px centred               │ │
│                     │  │  Mobile  → 390px centred               │ │
│                     │  └────────────────────────────────────────┘ │
└─────────────────────┴─────────────────────────────────────────────┘
```

### Settings tab
- Template Gallery button → full-screen modal with template cards (thumbnail + name + chapter count). Confirm replaces canvas. "Apply style only" option preserves chapters.
- Global Style picker — dropdown of all registered styles
- Reflection Palette — colour overrides at reflection level (§6 Level 1)
- Nav config — back link, audio toggle, menu enabled

### Chapters tab
- Stacked list of all chapters in the blueprint. Each row shows:
  - Drag handle (reorder via drag-and-drop)
  - Family tag (e.g. `hotspot-stage`)
  - Style badge (e.g. `immersive-dark` or `inherit`)
  - Content summary (first title or item count found in content)
  - Duplicate / Delete icons
- Clicking a chapter row → **automatically switches to Edit tab** with that chapter loaded
- "+ Add Chapter" button at the bottom → picker modal listing all registered families (name + description)

### Edit tab
Opened automatically when a chapter is selected in the Chapters tab. Shows:

1. **Chapter info** — family label (read-only), chapter ID (read-only)
2. **Style selector** — dropdown: "Inherit from reflection" + all registered styles. Shows a small colour swatch preview for each option.
3. **Colour mode** — radio: Inherit from reflection / Use style defaults / Custom (§6)
4. **Element colours** — grid of colour pickers, one per element slot from `definition.elements`. Each picker shows the currently resolved value and its source ("from reflection", "style default", "your override").
5. **Content fields** — rendered dynamically from `definition.fields` by `DynamicFieldRenderer`. Field order follows the order keys are declared in the definition.
6. **Transition / menu fields** — part of `definition.fields`; rendered like any other field.

### Preview Pane
- `<iframe>` loads `studio-renderer.js` with `window.RECIStudioConfig = { blueprint, postId }`
- **Chapter change** (edit tab field update): `postMessage({ type: 'update-chapter', chapterId, chapter })` → renderer hot-swaps that chapter's DOM subtree without reloading
- **Structural change** (add/remove/reorder): full iframe reload via `previewReloadKey` increment
- **Responsive mode**: Desktop renders at 100% iframe width. Tablet/Mobile render the iframe at a fixed pixel width centred in the pane, simulating the viewport. The iframe itself does not change — only its CSS `width` is constrained.

---

## 10. Interaction & Behaviour JS

All interaction logic (click handlers, hover states, GSAP animations, scroll triggers,
keyboard behaviour) lives **inside the style component file**. There are no separate
global JS files per reflection. The component owns its own behaviour.

### Where code lives

| Type of logic | Where it goes |
|---|---|
| Click/hover handlers, local state | Inside the style component (`styles/immersive-dark.tsx`) |
| Complex interaction logic reused across styles of the same chapter | Chapter-level hook: `chapters/hotspot-stage/hooks/useHotspotInteraction.ts` |
| Scroll triggers, GSAP animations | Style component imports from shared renderer hooks |
| Chapter progression (unlock/advance) | `renderer/hooks/useProgression.ts` — shared across all chapters |
| Shared animation primitives | `renderer/hooks/useGSAP.ts`, `useScrollTrigger.ts`, `useIntersection.ts` |

### Shared renderer hooks

```typescript
// renderer/hooks/useScrollTrigger.ts
// Fires onComplete when the element scrolls into/out of view based on transition_mode.
export function useScrollTrigger(
  ref: RefObject<HTMLElement>,
  onComplete: () => void,
  mode: 'button' | 'scroll' | 'auto',
): void

// renderer/hooks/useGSAP.ts
// Returns a scoped GSAP context that cleans up on unmount.
export function useGSAP(
  ref: RefObject<HTMLElement>,
  setup: (gsap: GSAP) => void,
  deps?: DependencyList,
): void

// renderer/hooks/useIntersection.ts
// Fires callback when element enters/exits the viewport.
export function useIntersection(
  ref: RefObject<HTMLElement>,
  onEnter: () => void,
  options?: IntersectionObserverInit,
): void
```

### Chapter-level hook example

```typescript
// chapters/hotspot-stage/hooks/useHotspotInteraction.ts
export function useHotspotInteraction(hotspots: Hotspot[]) {
  const [activeKey, setActiveKey] = useState<string | null>(null);
  const activate = (key: string) =>
    setActiveKey(prev => prev === key ? null : key);
  return { activeKey, activate };
}
```

The style component imports from its own `hooks/` folder — not from the renderer.  
This keeps the chapter fully self-contained and testable in isolation.

---

## 11. Rendering Pipeline

```
BlueprintV2 JSON
  └─► ChapterRenderer (per chapter)
        ├── Look up ChapterDefinition by chapter.family
        ├── Resolve effective style: chapter.style === 'inherit' → blueprint.globalStyle
        ├── Look up StyleComponent: definition.styles[effectiveStyle] ?? definition.styles['default']
        ├── Call useColorResolution() → ResolvedColors
        ├── Inject CSS custom properties on wrapper <div>
        └── Render <StyleComponent content={chapter.content} resolvedColors={...} ... />
```

The renderer does NOT know about PHP templates. It is a pure React app.  
PHP role: serve a shell WordPress page, enqueue `studio-renderer.js`, inject:
```php
window.RECIStudioConfig = {
  blueprint: <json from post meta>,
  postId: <int>,
};
```

---

## 11. PHP Integration

**`class-studio-meta-box.php`**
- Adds a "Reflection Studio (v2)" meta box to the `reci_reflection` post type
- Shows the builder iframe (or inline React mount point)
- "Switch to v2" button sets `_reflection_version = 'v2'`
- "Switch back to v1" button sets it back (non-destructive; v1 meta is never deleted)

**`class-studio-renderer.php`**
- On `template_redirect` for `reci_reflection` posts:
  - If `_reflection_version === 'v2'` → enqueue `studio-renderer.js`, inject config, render shell template
  - Otherwise → let existing v1 system handle it

**`class-studio-api.php`**
REST namespace: `reci/v2`
- `GET  /reci/v2/blueprint/{postId}` → returns current blueprint JSON
- `POST /reci/v2/blueprint/{postId}` → saves blueprint to `_reflection_studio_blueprint`

Note: There is no server-side preview render endpoint. The builder preview pane is an
iframe loading `studio-renderer.js` (the React renderer) with the current blueprint
injected as `window.RECIStudioConfig`. Chapter edits are hot-swapped client-side via
`postMessage({ type: 'update-chapter', chapterId, chapter })` — no round-trip to PHP.

**Post meta keys:**
- `_reflection_version` — `'v1'` (default) | `'v2'`
- `_reflection_studio_blueprint` — BlueprintV2 JSON (v2 only)
- `_reflection_blueprint` — existing v1 key, untouched

---

## 14. Developer Guide Summary

### Adding a new chapter family

1. Create `src/chapters/your-chapter-name/`
2. Write `definition.ts`:
   - Export a typed `YourChapterContent` interface
   - Export `elements` — named visual elements with `colorSlot` mappings
   - Export `fields` — the content field schema (`DynamicFieldRenderer` uses this; no settings panel needed)
   - Export `defaultContent`
3. Write `styles/default.tsx` — the default style component (JSX + interactions + animations)
   - Import shared hooks from `renderer/hooks/` as needed (useScrollTrigger, useGSAP, etc.)
   - If interaction logic is complex or shared across styles, extract to `hooks/useYourInteraction.ts`
4. Write `index.ts` — export `ChapterDefinition<YourChapterContent>` with styles wired in
5. Add one import line to `src/registry/chapters.ts`
6. Done. Builder discovers it automatically. No settings panel file needed.

**Optional:** If a field needs UI beyond what `DynamicFieldRenderer` supports (e.g. a visual position picker), export `customSettingsSection` from `index.ts`. Standard fields still render from schema; custom section appends below.

### Adding a new global style

1. Read `src/styles/immersive-dark/guideline.md` — understand the design language conventions
2. Create `src/styles/your-style-name/`
3. Write `tokens.ts` — fill in all `StyleTokens` fields (colors, fonts, spacing)
4. Write `guideline.md` — describe the design intent so future developers implement it consistently
5. Write `index.ts` — export `StyleDefinition`
6. Add one import line to `src/registry/styles.ts`
7. For each chapter family: add `src/chapters/<family>/styles/your-style-name.tsx`
   - Chapters without your style implemented fall back to `default.tsx`
   - The chapter's `elements` and `fields` are already defined — just implement the visual design
8. Done.

---

## 13. Coexistence + Migration

| Concern | Approach |
|---|---|
| Existing reflections | Unaffected. `_reflection_version` defaults to `v1`. |
| Old module | `modules/reflection-system/` — not modified. |
| Switching a post | Admin clicks "Switch to v2" in the new meta box. Sets flag. V1 meta preserved. |
| Switching back | Admin clicks "Switch back to v1". Flag reset. V2 blueprint preserved. |
| Both meta boxes visible | The active builder is expanded; the inactive one is collapsed with a badge showing which version is live. Only the active version renders on the front end. |
| New posts | Start in v1 by default until the team decides to make v2 the default. |

---

## 15. Key Decisions Made

| Decision | Choice | Reason |
|---|---|---|
| Front-end rendering engine | React only | Reflections are interactive experiences; PHP can't serve the animation/progression layer |
| PHP role | Shell only (enqueue + inject config) | Single rendering pipeline, no sync burden |
| Chapter naming | `family` (kebab-case) | Consistent with v1 direction, avoids `type` confusion |
| Style-per-chapter storage | Chapter owns its style components in `styles/` subdir | Chapter is the unit of ownership; styles are expressions of it |
| Layout differences between styles | Each style component owns its own JSX + interaction logic | Full structural freedom; no layout-modes abstraction needed |
| Registry | TypeScript-native, PHP reads serialized JSON export | Single source of truth; no PHP field duplication |
| Builder settings panel | Dynamic from `definition.fields` — no per-chapter settings panel file | Adding a chapter requires zero builder UI code changes |
| Interaction/animation JS | Lives inside the style component file | Component owns structure + behaviour; no separate global JS files |
| Shared interaction hooks | `renderer/hooks/` for cross-chapter patterns; `chapter/hooks/` for chapter-specific logic | Reusable without coupling chapters to each other |
| Builder UI layout | Elementor-style: tabbed left panel (Settings/Chapters/Edit) + wide preview | Preview needs maximum width; editing happens in the panel |
| Responsive preview | Constrain iframe CSS width (768px tablet, 390px mobile) | iframe content unchanged; only viewport simulation changes |
| Colour system | 3-level resolution: reflection → style defaults → element overrides | Matches user mental model; every element independently editable |
| Template application | Replaces full canvas (with confirmation) OR style-only apply | Non-destructive style-only option preserves user content |
| Version coexistence | Post meta flag `_reflection_version`; both builders visible, active one expanded | Zero risk to live content; reversible per post |