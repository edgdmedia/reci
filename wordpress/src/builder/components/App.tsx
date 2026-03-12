import { useBuilderStore } from '../store/builderStore';
import Palette from './Palette';
import Canvas from './Canvas';
import ReflectionSettings from './ReflectionSettings';
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
      {/* Left: settings panel */}
      <aside className="w-64 shrink-0 overflow-y-auto border-r border-gray-200 bg-gray-50">
        <div className="border-b border-gray-200 px-4 py-3">
          <h2 className="text-sm font-semibold text-gray-700">Reflection Settings</h2>
        </div>
        <ReflectionSettings
          mode={mode}
          appearance={appearance}
          onUpdateMode={handleUpdateMode}
          onUpdateAppearance={handleUpdateAppearance}
        />
      </aside>

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

      {/* Right: palette */}
      <aside className="w-56 shrink-0 overflow-y-auto border-l border-gray-200 bg-gray-50">
        <div className="border-b border-gray-200 px-4 py-3">
          <h2 className="text-sm font-semibold text-gray-700">Add Block</h2>
        </div>
        <Palette mode={mode} onAddScene={handleAddScene} onAddChapter={handleAddChapter} />
      </aside>
    </div>
  );
}
