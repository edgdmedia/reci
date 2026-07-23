import { create } from 'zustand';
import type { BlueprintV2, ChapterInstance } from '@/types/blueprint';

export function makeInitialBlueprint(): BlueprintV2 {
  return {
    version: 3,
    system: 'reflection-studio',
    globalStyle: 'default',
    palette: {},
    nav: { enabled: true, menu_enabled: true },
    chapters: [],
  };
}

function uid(): string { return crypto.randomUUID(); }

interface BuilderState {
  blueprint: BlueprintV2;
  selectedChapterId: string | null;
  previewReloadKey: number;

  setSelectedChapter(id: string | null): void;
  setSettings(updates: Partial<Omit<BlueprintV2, 'chapters' | 'version' | 'system'>>): void;
  addChapter(family: string, style: string, content: Record<string, unknown>): void;
  duplicateChapter(id: string): void;
  removeChapter(id: string): void;
  reorderChapters(orderedIds: string[]): void;
  updateChapter(id: string, updates: Partial<Omit<ChapterInstance, 'id' | 'family'>>): void;
  updateChapterContent(id: string, contentUpdates: Record<string, unknown>): void;
  loadBlueprint(bp: BlueprintV2): void;
  serialise(): string;
}

export const useBuilderStore = create<BuilderState>((set, get) => ({
  blueprint: makeInitialBlueprint(),
  selectedChapterId: null,
  previewReloadKey: 0,

  setSelectedChapter: (id) => set({ selectedChapterId: id }),

  setSettings: (updates) =>
    set((s) => ({
      blueprint: { ...s.blueprint, ...updates },
      previewReloadKey: s.previewReloadKey + 1,
    })),

  addChapter: (family, style, content) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        chapters: [
          ...s.blueprint.chapters,
          { id: uid(), family, style, colorMode: 'inherit', elementColors: {}, content },
        ],
      },
      previewReloadKey: s.previewReloadKey + 1,
    })),

  duplicateChapter: (id) =>
    set((s) => {
      const idx = s.blueprint.chapters.findIndex(c => c.id === id);
      if (idx === -1) return s;
      const original = s.blueprint.chapters[idx];
      const dup: ChapterInstance = { ...original, id: uid(), content: { ...original.content } };
      const next = [...s.blueprint.chapters];
      next.splice(idx + 1, 0, dup);
      return { blueprint: { ...s.blueprint, chapters: next }, previewReloadKey: s.previewReloadKey + 1 };
    }),

  removeChapter: (id) =>
    set((s) => ({
      blueprint: { ...s.blueprint, chapters: s.blueprint.chapters.filter(c => c.id !== id) },
      previewReloadKey: s.previewReloadKey + 1,
    })),

  reorderChapters: (orderedIds) =>
    set((s) => {
      const map = new Map(s.blueprint.chapters.map(c => [c.id, c]));
      return {
        blueprint: { ...s.blueprint, chapters: orderedIds.map(id => map.get(id)!).filter(Boolean) },
        previewReloadKey: s.previewReloadKey + 1,
      };
    }),

  updateChapter: (id, updates) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        chapters: s.blueprint.chapters.map(c => c.id === id ? { ...c, ...updates } : c),
      },
    })),

  updateChapterContent: (id, contentUpdates) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        chapters: s.blueprint.chapters.map(c =>
          c.id === id ? { ...c, content: { ...c.content, ...contentUpdates } } : c
        ),
      },
    })),

  loadBlueprint: (bp) =>
    set({ blueprint: bp, previewReloadKey: get().previewReloadKey + 1 }),

  serialise: () => JSON.stringify(get().blueprint),
}));
