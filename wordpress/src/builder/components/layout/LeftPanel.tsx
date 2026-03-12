import { useState } from 'react';
import ReflectionSettings from '../ReflectionSettings';
import Palette from '../Palette';
import type { Blueprint, Scene, Chapter, ReflectionStyle, SceneType } from '../../../../types/blueprint';

type Tab = 'reflection' | 'add';

interface Props {
  mode: Blueprint['mode'];
  appearance: ReflectionStyle;
  onUpdateMode: (m: Blueprint['mode']) => void;
  onUpdateAppearance: (u: Partial<ReflectionStyle>) => void;
  onAddScene: (type: SceneType) => void;
  onAddChapter: (type: Chapter['type']) => void;
}

export default function LeftPanel({ mode, appearance, onUpdateMode, onUpdateAppearance, onAddScene, onAddChapter }: Props) {
  const [tab, setTab] = useState<Tab>('reflection');

  return (
    <aside className="flex w-64 shrink-0 flex-col border-r border-gray-200 bg-white overflow-hidden">
      {/* Tab bar */}
      <div className="flex border-b border-gray-200">
        {(['reflection', 'add'] as Tab[]).map((t) => (
          <button
            key={t}
            type="button"
            onClick={() => setTab(t)}
            className={`flex-1 py-3 text-xs font-semibold uppercase tracking-wide transition ${
              tab === t ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'
            }`}
          >
            {t === 'reflection' ? 'Reflection' : 'Add Block'}
          </button>
        ))}
      </div>

      {/* Tab content */}
      <div className="flex-1 overflow-y-auto">
        {tab === 'reflection' ? (
          <ReflectionSettings
            mode={mode}
            appearance={appearance}
            onUpdateMode={onUpdateMode}
            onUpdateAppearance={onUpdateAppearance}
          />
        ) : (
          <Palette mode={mode} onAddScene={onAddScene} onAddChapter={onAddChapter} />
        )}
      </div>
    </aside>
  );
}
