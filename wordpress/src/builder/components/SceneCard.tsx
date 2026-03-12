import { useState } from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import type { Scene, SceneType } from '../../../types/blueprint';
import { SCENE_SETTINGS_MAP } from './settings/sceneSettingsMap';

interface Props {
  scene: Scene;
  onUpdate: (updates: Partial<Scene>) => void;
  onRemove: () => void;
}

export default function SceneCard({ scene, onUpdate, onRemove }: Props) {
  const [open, setOpen] = useState(false);
  const { attributes, listeners, setNodeRef, transform, transition } = useSortable({ id: scene.id });
  const SettingsForm = SCENE_SETTINGS_MAP[scene.type];

  return (
    <div
      ref={setNodeRef}
      style={{ transform: CSS.Transform.toString(transform), transition }}
      className="mb-2 rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden"
    >
      <div
        onClick={() => setOpen((o) => !o)}
        className={`flex items-center gap-2 cursor-pointer p-3 ${open ? 'bg-blue-50' : 'hover:bg-gray-50'}`}
        style={open ? { borderLeft: '3px solid #2563eb' } : { borderLeft: '3px solid transparent' }}
      >
        <span {...attributes} {...listeners} onClick={(e) => e.stopPropagation()} className="cursor-grab text-gray-400 hover:text-gray-600" aria-label="Drag">⠿</span>
        <span className="flex-1 text-sm font-medium text-gray-700">
          <span className="rounded bg-blue-100 px-1.5 py-0.5 text-xs text-blue-700">{scene.type as SceneType}</span>
          {scene.title && <span className="ml-2 text-gray-500">{scene.title}</span>}
        </span>
        <span className="mr-1 text-xs text-gray-400">{open ? '▲' : '▼'}</span>
        <button type="button" onClick={(e) => { e.stopPropagation(); onRemove(); }} className="text-xs text-red-400 hover:text-red-600" aria-label="Remove">✕</button>
      </div>
      {open && (
        <div className="border-t border-gray-100 bg-gray-50 p-3">
          {SettingsForm ? (
            <SettingsForm scene={scene} onChange={onUpdate} />
          ) : (
            <p className="text-xs text-gray-400">No settings for this block type.</p>
          )}
        </div>
      )}
    </div>
  );
}
