import { useEffect } from 'react';
import { useBuilderStore } from '../store/builderStore';
import Canvas from './Canvas';
import LeftPanel from './layout/LeftPanel';
import PreviewPane from './PreviewPane';

export default function App() {
  const { blueprint, setSettings, addChapter, duplicateChapter, removeChapter, reorderChapters, updateChapter, serialise } = useBuilderStore();
  const { chapters = [], settings } = blueprint;

  useEffect(() => {
    const el = document.getElementById('reci-builder-blueprint') as HTMLInputElement | null;
    if (el) {
      el.value = serialise();
    }
  }, [blueprint, serialise]);

  return (
    <div className="flex h-full">
      <LeftPanel settings={settings} onUpdateSettings={setSettings} onAddChapter={addChapter} />
      <main className="flex min-w-0 flex-1 overflow-hidden">
        <section className="flex min-w-0 flex-1 flex-col overflow-hidden border-r border-gray-200">
          <Canvas
            title="Chapters"
            emptyMessage="No chapters yet. Add one from the palette."
            components={chapters}
            onUpdateComponent={updateChapter}
            onDuplicateComponent={duplicateChapter}
            onRemoveComponent={removeChapter}
            onReorderComponents={reorderChapters}
          />
        </section>
        <PreviewPane blueprint={blueprint} />
      </main>
    </div>
  );
}
