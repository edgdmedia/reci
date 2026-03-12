// src/builder/test/builderStore.test.ts
import { describe, it, expect, beforeEach } from 'vitest';
import { useBuilderStore, initialBlueprint } from '../store/builderStore';

beforeEach(() => {
  useBuilderStore.setState({ blueprint: { ...initialBlueprint, scenes: [], chapters: [] } });
});

describe('mode', () => {
  it('defaults to standard', () => {
    expect(useBuilderStore.getState().blueprint.mode).toBe('standard');
  });

  it('setMode switches to immersive', () => {
    useBuilderStore.getState().setMode('immersive');
    expect(useBuilderStore.getState().blueprint.mode).toBe('immersive');
  });
});

describe('scenes', () => {
  it('addScene appends a scene with a unique id', () => {
    useBuilderStore.getState().addScene('hero');
    const { scenes } = useBuilderStore.getState().blueprint;
    expect(scenes).toHaveLength(1);
    expect(scenes![0].type).toBe('hero');
    expect(scenes![0].id).toBeTruthy();
  });

  it('addScene twice produces two distinct ids', () => {
    useBuilderStore.getState().addScene('hero');
    useBuilderStore.getState().addScene('quote');
    const ids = useBuilderStore.getState().blueprint.scenes!.map((s) => s.id);
    expect(new Set(ids).size).toBe(2);
  });

  it('removeScene removes by id', () => {
    useBuilderStore.getState().addScene('hero');
    const id = useBuilderStore.getState().blueprint.scenes![0].id;
    useBuilderStore.getState().removeScene(id);
    expect(useBuilderStore.getState().blueprint.scenes).toHaveLength(0);
  });

  it('updateScene merges partial updates', () => {
    useBuilderStore.getState().addScene('hero');
    const id = useBuilderStore.getState().blueprint.scenes![0].id;
    useBuilderStore.getState().updateScene(id, { title: 'Hello' });
    expect(useBuilderStore.getState().blueprint.scenes![0].title).toBe('Hello');
  });

  it('reorderScenes reorders by new id array', () => {
    useBuilderStore.getState().addScene('hero');
    useBuilderStore.getState().addScene('quote');
    const [a, b] = useBuilderStore.getState().blueprint.scenes!.map((s) => s.id);
    useBuilderStore.getState().reorderScenes([b, a]);
    const types = useBuilderStore.getState().blueprint.scenes!.map((s) => s.type);
    expect(types).toEqual(['quote', 'hero']);
  });
});

describe('chapters', () => {
  it('addChapter appends a chapter', () => {
    useBuilderStore.getState().addChapter('threshold_intro');
    const { chapters } = useBuilderStore.getState().blueprint;
    expect(chapters).toHaveLength(1);
    expect(chapters![0].type).toBe('threshold_intro');
  });

  it('removeChapter removes by id', () => {
    useBuilderStore.getState().addChapter('threshold_intro');
    const id = useBuilderStore.getState().blueprint.chapters![0].id;
    useBuilderStore.getState().removeChapter(id);
    expect(useBuilderStore.getState().blueprint.chapters).toHaveLength(0);
  });

  it('reorderChapters reorders by new id array', () => {
    useBuilderStore.getState().addChapter('threshold_intro');
    useBuilderStore.getState().addChapter('content_stage');
    const [a, b] = useBuilderStore.getState().blueprint.chapters!.map((c) => c.id);
    useBuilderStore.getState().reorderChapters([b, a]);
    const types = useBuilderStore.getState().blueprint.chapters!.map((c) => c.type);
    expect(types).toEqual(['content_stage', 'threshold_intro']);
  });

  it('updateChapter merges updates', () => {
    useBuilderStore.getState().addChapter('content_stage');
    const id = useBuilderStore.getState().blueprint.chapters![0].id;
    useBuilderStore.getState().updateChapter(id, { content: { title: 'Deep' } });
    expect(useBuilderStore.getState().blueprint.chapters![0].content?.title).toBe('Deep');
  });

  it('updateChapter preserves pre-existing content sub-fields', () => {
    useBuilderStore.getState().addChapter('content_stage');
    const id = useBuilderStore.getState().blueprint.chapters![0].id;
    // Set initial content with multiple sub-fields
    useBuilderStore.getState().updateChapter(id, { content: { title: 'Original', body: 'Body text', audio_url: '/audio.mp3' } });
    // Partial update — only title changes
    useBuilderStore.getState().updateChapter(id, { content: { ...useBuilderStore.getState().blueprint.chapters![0].content, title: 'Updated' } });
    const ch = useBuilderStore.getState().blueprint.chapters![0];
    expect(ch.content?.title).toBe('Updated');
    expect(ch.content?.body).toBe('Body text');
    expect(ch.content?.audio_url).toBe('/audio.mp3');
  });
});

describe('loadBlueprint', () => {
  it('populates mode, appearance, scenes, and chapters from a full blueprint', () => {
    const bp = {
      mode: 'immersive' as const,
      appearance: { backgroundColor: '#111' },
      scenes: [],
      chapters: [{ id: 'ch1', type: 'threshold_intro' as const, state: { initial: 'active' as const } }],
    };
    useBuilderStore.getState().loadBlueprint(bp);
    const state = useBuilderStore.getState().blueprint;
    expect(state.mode).toBe('immersive');
    expect(state.appearance?.backgroundColor).toBe('#111');
    expect(state.chapters).toHaveLength(1);
    expect(state.chapters![0].id).toBe('ch1');
  });
});

describe('appearance', () => {
  it('updateAppearance merges fields', () => {
    useBuilderStore.getState().updateAppearance({ backgroundColor: '#ff0000' });
    expect(useBuilderStore.getState().blueprint.appearance?.backgroundColor).toBe('#ff0000');
  });
});

describe('serialise', () => {
  it('returns valid Blueprint JSON string', () => {
    useBuilderStore.getState().addScene('hero');
    const json = useBuilderStore.getState().serialise();
    const parsed = JSON.parse(json);
    expect(parsed.mode).toBe('standard');
    expect(parsed.scenes).toHaveLength(1);
  });
});
