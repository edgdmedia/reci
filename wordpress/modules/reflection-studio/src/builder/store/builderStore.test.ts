import { act, renderHook } from '@testing-library/react';
import { useBuilderStore, makeInitialBlueprint } from './builderStore';

describe('builderStore', () => {
  beforeEach(() => {
    useBuilderStore.setState({ blueprint: makeInitialBlueprint(), selectedChapterId: null });
  });

  it('addChapter inserts a chapter with unique id', () => {
    const { result } = renderHook(() => useBuilderStore());
    act(() => result.current.addChapter('hotspot-stage', 'default', {}));
    expect(result.current.blueprint.chapters).toHaveLength(1);
    expect(result.current.blueprint.chapters[0].family).toBe('hotspot-stage');
    expect(result.current.blueprint.chapters[0].id).toBeTruthy();
  });

  it('removeChapter deletes by id', () => {
    const { result } = renderHook(() => useBuilderStore());
    act(() => result.current.addChapter('hotspot-stage', 'default', {}));
    const id = result.current.blueprint.chapters[0].id;
    act(() => result.current.removeChapter(id));
    expect(result.current.blueprint.chapters).toHaveLength(0);
  });

  it('updateChapterContent merges props', () => {
    const { result } = renderHook(() => useBuilderStore());
    act(() => result.current.addChapter('hotspot-stage', 'default', { title: 'old' }));
    const id = result.current.blueprint.chapters[0].id;
    act(() => result.current.updateChapterContent(id, { title: 'new' }));
    expect(result.current.blueprint.chapters[0].content.title).toBe('new');
  });

  it('serialise returns valid JSON', () => {
    const { result } = renderHook(() => useBuilderStore());
    const json = result.current.serialise();
    expect(() => JSON.parse(json)).not.toThrow();
  });
});
