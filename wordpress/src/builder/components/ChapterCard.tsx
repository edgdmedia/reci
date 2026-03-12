import { useState } from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import type { Chapter } from '../../../types/blueprint';
import { CHAPTER_SETTINGS_MAP } from './settings/chapterSettingsMap';

interface Props {
  chapter: Chapter;
  onUpdate: (updates: Partial<Chapter>) => void;
  onRemove: () => void;
}

export default function ChapterCard({ chapter, onUpdate, onRemove }: Props) {
  const [expanded, setExpanded] = useState(false);
  const { attributes, listeners, setNodeRef, transform, transition } = useSortable({ id: chapter.id });

  const SettingsForm = CHAPTER_SETTINGS_MAP[chapter.type];

  return (
    <div
      ref={setNodeRef}
      style={{ transform: CSS.Transform.toString(transform), transition }}
      className="mb-2 rounded-lg border border-gray-200 bg-white shadow-sm"
    >
      <div className="flex items-center gap-2 p-3">
        <span
          {...attributes}
          {...listeners}
          className="cursor-grab text-gray-400 hover:text-gray-600"
          aria-label="Drag to reorder"
        >
          ⠿
        </span>
        <button
          type="button"
          onClick={() => setExpanded((e) => !e)}
          className="flex-1 text-left text-sm font-medium text-gray-700"
        >
          <span className="rounded bg-purple-100 px-1.5 py-0.5 text-xs text-purple-700">{chapter.type}</span>
          {chapter.content?.title && <span className="ml-2 text-gray-500">{chapter.content.title}</span>}
        </button>
        <button
          type="button"
          onClick={onRemove}
          className="text-xs text-red-400 hover:text-red-600"
          aria-label="Remove chapter"
        >
          ✕
        </button>
      </div>
      {expanded && SettingsForm && (
        <div className="border-t border-gray-100 p-3">
          <SettingsForm chapter={chapter} onChange={onUpdate} />
        </div>
      )}
    </div>
  );
}
