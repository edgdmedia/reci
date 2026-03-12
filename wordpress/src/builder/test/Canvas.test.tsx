import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import Canvas from '../components/Canvas';
import type { Scene, Chapter } from '../../../types/blueprint';

const scenes: Scene[] = [
  { id: 's1', type: 'hero', title: 'My Hero' },
  { id: 's2', type: 'quote' },
];

describe('Canvas (standard mode)', () => {
  it('renders all scene cards', () => {
    render(
      <Canvas
        mode="standard"
        scenes={scenes}
        chapters={[]}
        onUpdateScene={vi.fn()}
        onRemoveScene={vi.fn()}
        onReorderScenes={vi.fn()}
        onUpdateChapter={vi.fn()}
        onRemoveChapter={vi.fn()}
        onReorderChapters={vi.fn()}
      />,
    );
    expect(screen.getByText('hero')).toBeInTheDocument();
    expect(screen.getByText('quote')).toBeInTheDocument();
  });

  it('shows empty state when no scenes', () => {
    render(
      <Canvas
        mode="standard"
        scenes={[]}
        chapters={[]}
        onUpdateScene={vi.fn()}
        onRemoveScene={vi.fn()}
        onReorderScenes={vi.fn()}
        onUpdateChapter={vi.fn()}
        onRemoveChapter={vi.fn()}
        onReorderChapters={vi.fn()}
      />,
    );
    expect(screen.getByText(/no scenes yet/i)).toBeInTheDocument();
  });
});

describe('Canvas (immersive mode)', () => {
  it('renders chapter cards', () => {
    const chapters: Chapter[] = [
      { id: 'c1', type: 'threshold_intro', content: { title: 'Welcome' } },
    ];
    render(
      <Canvas
        mode="immersive"
        scenes={[]}
        chapters={chapters}
        onUpdateScene={vi.fn()}
        onRemoveScene={vi.fn()}
        onReorderScenes={vi.fn()}
        onUpdateChapter={vi.fn()}
        onRemoveChapter={vi.fn()}
        onReorderChapters={vi.fn()}
      />,
    );
    expect(screen.getByText('threshold_intro')).toBeInTheDocument();
  });

  it('shows empty state when no chapters', () => {
    render(
      <Canvas
        mode="immersive"
        scenes={[]}
        chapters={[]}
        onUpdateScene={vi.fn()}
        onRemoveScene={vi.fn()}
        onReorderScenes={vi.fn()}
        onUpdateChapter={vi.fn()}
        onRemoveChapter={vi.fn()}
        onReorderChapters={vi.fn()}
      />,
    );
    expect(screen.getByText(/no chapters yet/i)).toBeInTheDocument();
  });
});
