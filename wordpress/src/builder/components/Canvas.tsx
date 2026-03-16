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
import type { ReflectionSystemComponent } from '../../types/blueprint';
import ChapterCard from './ChapterCard';

interface Props {
  title?: string;
  emptyMessage?: string;
  components: ReflectionSystemComponent[];
  onUpdateComponent: (id: string, updates: Partial<ReflectionSystemComponent>) => void;
  onDuplicateComponent: (id: string) => void;
  onRemoveComponent: (id: string) => void;
  onReorderComponents: (orderedIds: string[]) => void;
}

export default function Canvas({ title, emptyMessage = 'Nothing here yet.', components, onUpdateComponent, onDuplicateComponent, onRemoveComponent, onReorderComponents }: Props) {
  const sensors = useSensors(useSensor(PointerSensor, { activationConstraint: { distance: 8 } }));

  function handleDragEnd(event: DragEndEvent) {
    const { active, over } = event;
    if (!over || active.id === over.id) return;
    const oldIdx = components.findIndex((component) => component.id === active.id);
    const newIdx = components.findIndex((component) => component.id === over.id);
    onReorderComponents(arrayMove(components, oldIdx, newIdx).map((component) => component.id));
  }

  return (
    <section className="flex min-h-0 flex-1 flex-col">
      {title ? <div className="border-b border-gray-200 px-4 py-3"><h2 className="text-sm font-semibold text-gray-700">{title}</h2></div> : null}
      <div className="flex-1 overflow-y-auto p-4">
        <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
          <SortableContext items={components.map((component) => component.id)} strategy={verticalListSortingStrategy}>
            {components.map((component) => (
              <ChapterCard
                key={component.id}
                chapter={component}
                onUpdate={(updates) => onUpdateComponent(component.id, updates)}
                onDuplicate={() => onDuplicateComponent(component.id)}
                onRemove={() => onRemoveComponent(component.id)}
              />
            ))}
          </SortableContext>
        </DndContext>
        {components.length === 0 && <p className="text-center text-sm text-gray-400">{emptyMessage}</p>}
      </div>
    </section>
  );
}
