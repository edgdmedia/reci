import { create } from 'zustand';
import type { ReflectionSystemBlueprint, ReflectionSystemComponent } from '../../types/blueprint';

export const initialBlueprint: ReflectionSystemBlueprint = {
  version: 2,
  system: 'reflections',
  chapters: [],
  settings: {
    mode: 'immersive',
    palette: '',
    stage_controller: 'default',
    menu_enabled: true,
    menu_back_url: '/reflections/',
  },
};

function uid(): string {
  return crypto.randomUUID();
}

function mergeComponentProps(component: ReflectionSystemComponent, updates: Partial<ReflectionSystemComponent>): ReflectionSystemComponent {
  return {
    ...component,
    ...updates,
    props: updates.props ? { ...component.props, ...updates.props } : component.props,
  };
}

interface BuilderState {
  blueprint: ReflectionSystemBlueprint;
  setSettings: (updates: Partial<ReflectionSystemBlueprint['settings']>) => void;
  addChapter: (family: string, variant: string, props?: Record<string, unknown>) => void;
  duplicateChapter: (id: string) => void;
  removeChapter: (id: string) => void;
  reorderChapters: (orderedIds: string[]) => void;
  updateChapter: (id: string, updates: Partial<ReflectionSystemComponent>) => void;
  loadBlueprint: (bp: ReflectionSystemBlueprint | Record<string, unknown>) => void;
  serialise: () => string;
}

function normalizeBlueprint(bp: ReflectionSystemBlueprint | Record<string, unknown>): ReflectionSystemBlueprint {
  const source = typeof bp === 'object' && bp !== null ? bp : {};
  const sourceSettings = typeof source.settings === 'object' && source.settings !== null ? source.settings : {};

  return {
    version: 2,
    system: 'reflections',
    chapters: Array.isArray(source.chapters)
      ? source.chapters.filter((chapter): chapter is ReflectionSystemComponent => typeof chapter === 'object' && chapter !== null)
      : [],
    settings: {
      ...initialBlueprint.settings,
      ...sourceSettings,
      mode: 'immersive',
    },
  };
}

function insertDuplicate<T extends ReflectionSystemComponent>(items: T[], id: string): T[] {
  const index = items.findIndex((item) => item.id === id);
  if (index === -1) return items;
  const item = items[index];
  const duplicate = { ...item, id: uid(), props: { ...item.props } } as T;
  const next = [...items];
  next.splice(index + 1, 0, duplicate);
  return next;
}

function reorder<T extends ReflectionSystemComponent>(items: T[], orderedIds: string[]): T[] {
  const map = new Map(items.map((item) => [item.id, item]));
  return orderedIds.map((id) => map.get(id)!).filter(Boolean);
}

export const useBuilderStore = create<BuilderState>((set, get) => ({
  blueprint: normalizeBlueprint(initialBlueprint),

  setSettings: (updates) =>
    set((state) => ({
      blueprint: {
        ...state.blueprint,
        settings: { ...state.blueprint.settings, ...updates },
      },
    })),

  addChapter: (family, variant, props = {}) =>
    set((state) => ({
      blueprint: {
        ...state.blueprint,
        chapters: [...state.blueprint.chapters, { id: uid(), family, variant, props }],
      },
    })),

  duplicateChapter: (id) =>
    set((state) => ({
      blueprint: { ...state.blueprint, chapters: insertDuplicate(state.blueprint.chapters, id) },
    })),

  removeChapter: (id) =>
    set((state) => ({
      blueprint: { ...state.blueprint, chapters: state.blueprint.chapters.filter((chapter) => chapter.id !== id) },
    })),

  reorderChapters: (orderedIds) =>
    set((state) => ({
      blueprint: { ...state.blueprint, chapters: reorder(state.blueprint.chapters, orderedIds) },
    })),

  updateChapter: (id, updates) =>
    set((state) => ({
      blueprint: {
        ...state.blueprint,
        chapters: state.blueprint.chapters.map((chapter) => chapter.id === id ? mergeComponentProps(chapter, updates) : chapter),
      },
    })),

  loadBlueprint: (bp) => set({ blueprint: normalizeBlueprint(bp) }),

  serialise: () => JSON.stringify(get().blueprint),
}));
