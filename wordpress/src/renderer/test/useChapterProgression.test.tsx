import { describe, it, expect } from 'vitest';
import { renderHook, act } from '@testing-library/react';
import { useChapterProgression } from '../hooks/useChapterProgression';
import type { Chapter } from '../../types/blueprint';

const chapters: Chapter[] = [
  {
    id: 'c1',
    type: 'threshold_intro',
    state: { initial: 'active', completion: { trigger: 'button', target: 'c2' } },
  },
  {
    id: 'c2',
    type: 'content_stage',
    state: { initial: 'locked', completion: { trigger: 'button', target: 'c3' } },
  },
  {
    id: 'c3',
    type: 'threshold_message',
    state: { initial: 'locked' },
  },
];

describe('useChapterProgression', () => {
  it('initialises first active chapter as active, rest as locked', () => {
    const { result } = renderHook(() => useChapterProgression(chapters));
    expect(result.current.state['c1']).toBe('active');
    expect(result.current.state['c2']).toBe('locked');
    expect(result.current.state['c3']).toBe('locked');
  });

  it('completeChapter unlocks the target chapter', () => {
    const { result } = renderHook(() => useChapterProgression(chapters));
    act(() => result.current.completeChapter('c1'));
    expect(result.current.state['c1']).toBe('completed');
    expect(result.current.state['c2']).toBe('active');
  });

  it('completing final chapter does not throw', () => {
    const { result } = renderHook(() => useChapterProgression(chapters));
    act(() => result.current.completeChapter('c1'));
    act(() => result.current.completeChapter('c2'));
    act(() => result.current.completeChapter('c3'));
    expect(result.current.state['c3']).toBe('completed');
  });
});
