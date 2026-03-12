// src/builder/store/builderStore.ts
import { create } from 'zustand';
import type { Blueprint, Scene, Chapter, ReflectionStyle, SceneType } from '../../types/blueprint';

export const initialBlueprint: Blueprint = {
  mode: 'standard',
  appearance: {},
  scenes: [],
  chapters: [],
};

function uid(): string {
  return crypto.randomUUID();
}

interface BuilderState {
  blueprint: Blueprint;
  setMode: (mode: 'standard' | 'immersive') => void;
  addScene: (type: SceneType) => void;
  removeScene: (id: string) => void;
  reorderScenes: (orderedIds: string[]) => void;
  updateScene: (id: string, updates: Partial<Scene>) => void;
  addChapter: (type: Chapter['type']) => void;
  removeChapter: (id: string) => void;
  reorderChapters: (orderedIds: string[]) => void;
  updateChapter: (id: string, updates: Partial<Chapter>) => void;
  updateAppearance: (updates: Partial<ReflectionStyle>) => void;
  loadBlueprint: (bp: Blueprint) => void;
  serialise: () => string;
}

export const useBuilderStore = create<BuilderState>((set, get) => ({
  blueprint: { ...initialBlueprint },

  setMode: (mode) =>
    set((s) => ({ blueprint: { ...s.blueprint, mode } })),

  addScene: (type) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        scenes: [...(s.blueprint.scenes ?? []), { id: uid(), type } as Scene],
      },
    })),

  removeScene: (id) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        scenes: (s.blueprint.scenes ?? []).filter((sc) => sc.id !== id),
      },
    })),

  reorderScenes: (orderedIds) =>
    set((s) => {
      const map = new Map((s.blueprint.scenes ?? []).map((sc) => [sc.id, sc]));
      return {
        blueprint: {
          ...s.blueprint,
          scenes: orderedIds.map((id) => map.get(id)!).filter(Boolean),
        },
      };
    }),

  updateScene: (id, updates) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        scenes: (s.blueprint.scenes ?? []).map((sc) =>
          sc.id === id ? { ...sc, ...updates } : sc
        ),
      },
    })),

  addChapter: (type) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        chapters: [
          ...(s.blueprint.chapters ?? []),
          {
            id: uid(),
            type,
            state: { initial: (s.blueprint.chapters ?? []).length === 0 ? 'active' : 'locked' },
          } as Chapter,
        ],
      },
    })),

  removeChapter: (id) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        chapters: (s.blueprint.chapters ?? []).filter((ch) => ch.id !== id),
      },
    })),

  reorderChapters: (orderedIds) =>
    set((s) => {
      const map = new Map((s.blueprint.chapters ?? []).map((ch) => [ch.id, ch]));
      return {
        blueprint: {
          ...s.blueprint,
          chapters: orderedIds.map((id) => map.get(id)!).filter(Boolean),
        },
      };
    }),

  updateChapter: (id, updates) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        chapters: (s.blueprint.chapters ?? []).map((ch) =>
          ch.id === id ? { ...ch, ...updates } : ch
        ),
      },
    })),

  updateAppearance: (updates) =>
    set((s) => ({
      blueprint: {
        ...s.blueprint,
        appearance: { ...s.blueprint.appearance, ...updates },
      },
    })),

  loadBlueprint: (bp) => set({ blueprint: bp }),

  serialise: () => JSON.stringify(get().blueprint),
}));
