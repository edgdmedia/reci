import { useState } from 'react';
import { useBuilderStore } from '../store/builderStore';
import Canvas from './Canvas';
import LeftPanel from './layout/LeftPanel';
import { SCENE_SETTINGS_MAP } from './settings/sceneSettingsMap';
import { CHAPTER_SETTINGS_MAP } from './settings/chapterSettingsMap';
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

  const [selectedId, setSelectedId] = useState<string | null>(null);

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
          selectedId={selectedId}
          onSelect={setSelectedId}
          onUpdateScene={handleUpdateScene}
          onRemoveScene={handleRemoveScene}
          onReorderScenes={handleReorderScenes}
          onUpdateChapter={handleUpdateChapter}
          onRemoveChapter={handleRemoveChapter}
          onReorderChapters={handleReorderChapters}
        />
      </main>

      {/* Right: selected block settings */}
      <aside className="w-72 shrink-0 overflow-y-auto border-l border-gray-200 bg-white">
        <div className="border-b border-gray-200 px-4 py-3">
          <h2 className="text-sm font-semibold text-gray-700">Block Settings</h2>
        </div>
        {(() => {
          if (!selectedId) {
            return (
              <div className="flex h-32 items-center justify-center text-xs text-gray-400">
                Select a block to edit its settings
              </div>
            );
          }
          const scene = scenes.find((s) => s.id === selectedId);
          if (scene) {
            const SettingsForm = SCENE_SETTINGS_MAP[scene.type];
            return SettingsForm ? (
              <SettingsForm scene={scene} onChange={(u: Parameters<typeof handleUpdateScene>[1]) => handleUpdateScene(scene.id, u)} />
            ) : (
              <div className="p-4 text-xs text-gray-400">No settings available for this block type.</div>
            );
          }
          const chapter = chapters.find((c) => c.id === selectedId);
          if (chapter) {
            const SettingsForm = CHAPTER_SETTINGS_MAP[chapter.type];
            return SettingsForm ? (
              <SettingsForm chapter={chapter} onChange={(u: Parameters<typeof handleUpdateChapter>[1]) => handleUpdateChapter(chapter.id, u)} />
            ) : (
              <div className="p-4 text-xs text-gray-400">No settings available for this block type.</div>
            );
          }
          return (
            <div className="flex h-32 items-center justify-center text-xs text-gray-400">
              Block not found
            </div>
          );
        })()}
      </aside>
    </div>
  );
}
