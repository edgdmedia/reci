// ─── Primitives ────────────────────────────────────────────────────────────

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

// ─── Blueprint ─────────────────────────────────────────────────────────────

export interface ChapterInstance {
  id: string;
  family: string;
  style: string;           // style key or 'inherit'
  colorMode: 'inherit' | 'style-defaults' | 'custom';
  elementColors: Record<string, string>;
  content: Record<string, unknown>;
}

export interface BlueprintV2 {
  version: 3;
  system: 'reflection-studio';
  globalStyle: string;
  palette: ReflectionPalette;
  nav: NavConfig;
  chapters: ChapterInstance[];
}

// ─── Style tokens ──────────────────────────────────────────────────────────

export interface StyleTokens {
  bg: string;
  heading: string;
  body: string;
  accent: string;
  primary: string;
  surface: string;
  surface_text: string;
  muted: string;
  headingFont: string;
  bodyFont: string;
  baseFontSize: string;
  spacingUnit: string;
}

export interface StyleDefinition {
  id: string;
  label: string;
  description?: string;
  tokens: StyleTokens;
}

// ─── Chapter registration ──────────────────────────────────────────────────

export interface ElementDefinition {
  label: string;
  description?: string;
  colorSlot: keyof StyleTokens;
}

export type FieldType =
  | 'text' | 'textarea' | 'richtext' | 'select'
  | 'media' | 'color' | 'range' | 'url' | 'toggle'
  | 'repeater' | 'chapter-target';

export interface FieldDefinition {
  type: FieldType;
  label: string;
  description?: string;
  required?: boolean;
  options?: Record<string, string>;
  itemFields?: Record<string, FieldDefinition>;
  maxItems?: number;
  maxLength?: number;
  show_if?: Record<string, string | boolean | string[]>;
}

export interface ResolvedColors {
  [elementSlot: string]: string;
}

export interface StyleComponentProps<TContent = Record<string, unknown>> {
  content: TContent;
  resolvedColors: ResolvedColors;
  status: 'active' | 'locked' | 'completed';
  onComplete: () => void;
  chapterId: string;
}

export type StudioStyleComponent<TContent = Record<string, unknown>> = (
  props: StyleComponentProps<TContent>
) => JSX.Element | null;

export interface ChapterDefinition<TContent = Record<string, unknown>> {
  family: string;
  label: string;
  description?: string;
  elements: Record<string, ElementDefinition>;
  fields: Record<string, FieldDefinition>;
  defaultContent: TContent;
  defaultStyle: string;
  styles: Record<string, StudioStyleComponent<TContent>>;
}

// ─── Serialized registry (PHP-side + builder config) ──────────────────────

export interface SerializedChapterDefinition {
  family: string;
  label: string;
  description?: string;
  elements: Record<string, ElementDefinition>;
  fields: Record<string, FieldDefinition>;
  defaultContent: Record<string, unknown>;
  defaultStyle: string;
  availableStyles: string[];
}

export interface SerializedRegistry {
  chapters: SerializedChapterDefinition[];
  styles: StyleDefinition[];
}

export interface StudioBuilderConfig {
  registry: SerializedRegistry;
  templates: Record<string, TemplateDefinition>;
  blueprint: BlueprintV2;
  postId: number;
  saveEndpoint: string;
  nonce: string;
}

export interface TemplateDefinition {
  id: string;
  label: string;
  description?: string;
  thumbnail?: string;
  blueprint: BlueprintV2;
}

// ─── Registry (runtime, includes React components) ────────────────────────

export interface Registry {
  chapters: Map<string, ChapterDefinition>;
  styles: Map<string, StyleDefinition>;
  getChapter(family: string): ChapterDefinition | undefined;
  getStyle(id: string): StyleDefinition | undefined;
  serialize(): SerializedRegistry;
}
