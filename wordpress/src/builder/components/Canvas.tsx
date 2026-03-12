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
  selectedId: string | null;
  onSelect: (id: string) => void;
  onUpdateScene: (id: string, updates: Partial<Scene>) => void;
  onRemoveScene: (id: string) => void;
  onReorderScenes: (orderedIds: string[]) => void;
  onUpdateChapter: (id: string, updates: Partial<Chapter>) => void;
  onRemoveChapter: (id: string) => void;
  onReorderChapters: (orderedIds: string[]) => void;
}

export default function Canvas({
  mode, scenes, chapters, selectedId, onSelect,
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
                selected={selectedId === chapter.id}
                onSelect={() => onSelect(chapter.id)}
                onUpdate={(u) => onUpdateChapter(chapter.id, u)}
                onRemove={() => onRemoveChapter(chapter.id)}
              />
            ))}
          </SortableContext>
        </DndContext>
        {chapters.length === 0 && (
          <p className="text-center text-sm text-gray-400">No chapters yet. Add from the palette.</p>
        )}
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
              selected={selectedId === scene.id}
              onSelect={() => onSelect(scene.id)}
              onUpdate={(u) => onUpdateScene(scene.id, u)}
              onRemove={() => onRemoveScene(scene.id)}
            />
          ))}
        </SortableContext>
      </DndContext>
      {scenes.length === 0 && (
        <p className="text-center text-sm text-gray-400">No scenes yet. Add from the palette.</p>
      )}
    </div>
  );
}
