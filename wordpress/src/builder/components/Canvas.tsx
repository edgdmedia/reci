import {
  DndContext,
  closestCenter,
  PointerSensor,
  useSensor,
  useSensors,
  type DragEndEvent,
} from '@dnd-kit/core';
import {
  SortableContext,
  verticalListSortingStrategy,
  arrayMove,
} from '@dnd-kit/sortable';
import type { Scene, Chapter } from '../../../types/blueprint';
import SceneCard from './SceneCard';
import ChapterCard from './ChapterCard';

interface Props {
  mode: 'standard' | 'immersive';
  scenes: Scene[];
  chapters: Chapter[];
  onUpdateScene: (id: string, updates: Partial<Scene>) => void;
  onRemoveScene: (id: string) => void;
  onReorderScenes: (orderedIds: string[]) => void;
  onUpdateChapter: (id: string, updates: Partial<Chapter>) => void;
  onRemoveChapter: (id: string) => void;
  onReorderChapters: (orderedIds: string[]) => void;
}

export default function Canvas({
  mode, scenes, chapters,
  onUpdateScene, onRemoveScene, onReorderScenes,
  onUpdateChapter, onRemoveChapter, onReorderChapters,
}: Props) {
  const sensors = useSensors(useSensor(PointerSensor));

  function handleSceneDragEnd(event: DragEndEvent) {
    const { active, over } = event;
    if (!over || active.id === over.id) return;
    const oldIdx = scenes.findIndex((s) => s.id === active.id);
    const newIdx = scenes.findIndex((s) => s.id === over.id);
    onReorderScenes(arrayMove(scenes, oldIdx, newIdx).map((s) => s.id));
  }

  function handleChapterDragEnd(event: DragEndEvent) {
    const { active, over } = event;
    if (!over || active.id === over.id) return;
    const oldIdx = chapters.findIndex((c) => c.id === active.id);
    const newIdx = chapters.findIndex((c) => c.id === over.id);
    onReorderChapters(arrayMove(chapters, oldIdx, newIdx).map((c) => c.id));
  }

  if (mode === 'immersive') {
    return (
      <div className="flex-1 overflow-y-auto p-4">
        <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleChapterDragEnd}>
          <SortableContext items={chapters.map((c) => c.id)} strategy={verticalListSortingStrategy}>
            {chapters.map((chapter) => (
              <ChapterCard
                key={chapter.id}
                chapter={chapter}
                onUpdate={(u) => onUpdateChapter(chapter.id, u)}
                onRemove={() => onRemoveChapter(chapter.id)}
              />
            ))}
          </SortableContext>
        </DndContext>
        {chapters.length === 0 && (
          <p className="text-center text-sm text-gray-400">No chapters yet. Add from the palette.</p>
        )}
        {/* Preview button */}
        <div className="mt-4 flex justify-end">
          <button
            type="button"
            className="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700"
            onClick={async () => {
              const postIdEl = document.getElementById('reci-builder-root');
              const postId = postIdEl ? Number(postIdEl.dataset.postId) : 0;
              const nonce = postIdEl?.dataset.previewNonce ?? '';
              const blueprint = JSON.parse((document.getElementById('reci-builder-blueprint') as HTMLInputElement)?.value ?? '{}');

              const res = await fetch('/wp-json/reci/v1/reflection-preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                body: JSON.stringify({ post_id: postId, blueprint }),
              });

              if (res.ok) {
                const { preview_url } = await res.json();
                window.open(preview_url, '_blank');
              }
            }}
          >
            Preview in new tab
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="flex-1 overflow-y-auto p-4">
      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleSceneDragEnd}>
        <SortableContext items={scenes.map((s) => s.id)} strategy={verticalListSortingStrategy}>
          {scenes.map((scene) => (
            <SceneCard
              key={scene.id}
              scene={scene}
              onUpdate={(u) => onUpdateScene(scene.id, u)}
              onRemove={() => onRemoveScene(scene.id)}
            />
          ))}
        </SortableContext>
      </DndContext>
      {scenes.length === 0 && (
        <p className="text-center text-sm text-gray-400">No scenes yet. Add from the palette.</p>
      )}
      {/* Preview button */}
      <div className="mt-4 flex justify-end">
        <button
          type="button"
          className="rounded bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700"
          onClick={async () => {
            const postIdEl = document.getElementById('reci-builder-root');
            const postId = postIdEl ? Number(postIdEl.dataset.postId) : 0;
            const nonce = postIdEl?.dataset.previewNonce ?? '';
            const blueprint = JSON.parse((document.getElementById('reci-builder-blueprint') as HTMLInputElement)?.value ?? '{}');

            const res = await fetch('/wp-json/reci/v1/reflection-preview', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
              body: JSON.stringify({ post_id: postId, blueprint }),
            });

            if (res.ok) {
              const { preview_url } = await res.json();
              window.open(preview_url, '_blank');
            }
          }}
        >
          Preview in new tab
        </button>
      </div>
    </div>
  );
}
