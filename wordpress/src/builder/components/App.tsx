import { useBuilderStore } from '../store/builderStore';
import Canvas from './Canvas';
import LeftPanel from './layout/LeftPanel';
import type { SceneType } from '../../../types/blueprint';
import type { Chapter } from '../../../types/blueprint';

export default function App() {
  const {
    blueprint,
    setMode,
    addScene,
    removeScene,
    reorderScenes,
    updateScene,
    addChapter,
    removeChapter,
    reorderChapters,
    updateChapter,
    updateAppearance,
    serialise,
  } = useBuilderStore();

  const { mode, appearance = {}, scenes = [], chapters = [] } = blueprint;

  function syncHidden() {
    const el = document.getElementById('reci-builder-blueprint') as HTMLInputElement | null;
    if (el) el.value = serialise();
  }

  function handleAddScene(type: SceneType) {
    addScene(type);
    setTimeout(syncHidden, 0);
  }

  function handleAddChapter(type: Chapter['type']) {
    addChapter(type);
    setTimeout(syncHidden, 0);
  }

  function handleUpdateScene(id: string, updates: Parameters<typeof updateScene>[1]) {
    updateScene(id, updates);
    setTimeout(syncHidden, 0);
  }

  function handleRemoveScene(id: string) {
    removeScene(id);
    setTimeout(syncHidden, 0);
  }

  function handleReorderScenes(orderedIds: string[]) {
    reorderScenes(orderedIds);
    setTimeout(syncHidden, 0);
  }

  function handleUpdateChapter(id: string, updates: Parameters<typeof updateChapter>[1]) {
    updateChapter(id, updates);
    setTimeout(syncHidden, 0);
  }

  function handleRemoveChapter(id: string) {
    removeChapter(id);
    setTimeout(syncHidden, 0);
  }

  function handleReorderChapters(orderedIds: string[]) {
    reorderChapters(orderedIds);
    setTimeout(syncHidden, 0);
  }

  function handleUpdateMode(newMode: 'standard' | 'immersive') {
    setMode(newMode);
    setTimeout(syncHidden, 0);
  }

  function handleUpdateAppearance(updates: Parameters<typeof updateAppearance>[0]) {
    updateAppearance(updates);
    setTimeout(syncHidden, 0);
  }

  return (
    <div className="flex h-full">
      {/* Left: tabbed settings panel */}
      <LeftPanel
        mode={mode}
        appearance={appearance}
        onUpdateMode={handleUpdateMode}
        onUpdateAppearance={handleUpdateAppearance}
        onAddScene={handleAddScene}
        onAddChapter={handleAddChapter}
      />

      {/* Center: canvas */}
      <main className="flex flex-1 flex-col overflow-hidden">
        <div className="border-b border-gray-200 px-4 py-3">
          <h2 className="text-sm font-semibold text-gray-700">
            {mode === 'immersive' ? 'Chapters' : 'Scenes'}
          </h2>
        </div>
        <Canvas
          mode={mode}
          scenes={scenes}
          chapters={chapters}
          onUpdateScene={handleUpdateScene}
          onRemoveScene={handleRemoveScene}
          onReorderScenes={handleReorderScenes}
          onUpdateChapter={handleUpdateChapter}
          onRemoveChapter={handleRemoveChapter}
          onReorderChapters={handleReorderChapters}
        />
      </main>
    </div>
  );
}
