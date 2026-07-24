import { createContext, useContext } from 'react';
import type {
  ChapterDefinition, StyleDefinition, Registry, SerializedRegistry,
} from '@/types/blueprint';

export function buildRegistry(
  chapters: ChapterDefinition[],
  styles: StyleDefinition[],
): Registry {
  const chapterMap = new Map(chapters.map(c => [c.family, c]));
  const styleMap   = new Map(styles.map(s => [s.id, s]));

  return {
    chapters: chapterMap,
    styles:   styleMap,
    getChapter: (family) => chapterMap.get(family),
    getStyle:   (id)     => styleMap.get(id),
    serialize(): SerializedRegistry {
      return {
        chapters: chapters.map(c => ({
          family:          c.family,
          label:           c.label,
          description:     c.description,
          elements:        c.elements,
          fields:          c.fields,
          defaultContent:  c.defaultContent as Record<string, unknown>,
          defaultStyle:    c.defaultStyle,
          availableStyles: Object.keys(c.styles),
        })),
        styles: styles.map(s => ({ ...s })),
      };
    },
  };
}

const RegistryContext = createContext<Registry | null>(null);
export const RegistryProvider = RegistryContext.Provider;

export function useRegistry(): Registry {
  const ctx = useContext(RegistryContext);
  if (!ctx) throw new Error('useRegistry must be used inside RegistryProvider');
  return ctx;
}
