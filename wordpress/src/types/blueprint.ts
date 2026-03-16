export interface ReflectionSystemFieldDefinition {
  type: 'text' | 'textarea' | 'select' | 'media' | 'repeater' | 'wysiwyg' | 'url';
  label: string;
  required?: boolean;
  options?: Record<string, string>;
  itemFields?: Record<string, ReflectionSystemFieldDefinition>;
}

export interface ReflectionSystemDefinition {
  label: string;
  kind: 'chapter' | 'element';
  loader: string;
  default_variant: string;
  variants: Record<string, string>;
  fields: Record<string, ReflectionSystemFieldDefinition>;
}

export interface ReflectionSystemRegistry {
  [family: string]: ReflectionSystemDefinition;
}

export interface ReflectionSystemComponent {
  id: string;
  family: string;
  variant: string;
  props: Record<string, unknown>;
}

export interface ReflectionSystemSettings {
  mode: 'immersive';
  palette?: string;
  stage_controller?: string;
  menu_enabled?: boolean;
  menu_back_url?: string;
}

export interface ReflectionSystemBlueprint {
  version: 2;
  system: 'reflections';
  chapters: ReflectionSystemComponent[];
  settings: ReflectionSystemSettings;
}

export interface BuilderConfig {
  registry: ReflectionSystemRegistry;
  defaultBlueprint: ReflectionSystemBlueprint;
  currentBlueprint: ReflectionSystemBlueprint;
  postId: number;
  previewNonce: string;
  previewEndpoint: string;
}
