import { useReducer } from 'react';
import type { Chapter } from '../../types/blueprint';

type ChapterStatus = 'locked' | 'active' | 'completed';
type ProgressionState = Record<string, ChapterStatus>;

function buildInitialState(chapters: Chapter[]): ProgressionState {
  return Object.fromEntries(
    chapters.map((ch) => [ch.id, (ch.state?.initial ?? 'locked') as ChapterStatus]),
  );
}

type Action = { type: 'COMPLETE'; id: string; target?: string };

function reducer(state: ProgressionState, action: Action): ProgressionState {
  if (action.type === 'COMPLETE') {
    const next: ProgressionState = { ...state, [action.id]: 'completed' };
    if (action.target && next[action.target] === 'locked') {
      next[action.target] = 'active';
    }
    return next;
  }
  return state;
}

export interface ChapterProgression {
  state: ProgressionState;
  completeChapter: (id: string) => void;
}

export function useChapterProgression(chapters: Chapter[]): ChapterProgression {
  const [state, dispatch] = useReducer(reducer, chapters, buildInitialState);

  function completeChapter(id: string) {
    const chapter = chapters.find((c) => c.id === id);
    const target = chapter?.state?.completion?.target;
    dispatch({ type: 'COMPLETE', id, target });
  }

  return { state, completeChapter };
}
