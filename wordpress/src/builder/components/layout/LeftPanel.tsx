import { useState } from 'react';
import ReflectionSettings from '../ReflectionSettings';
import Palette from '../Palette';
import type { ReflectionSystemSettings } from '../../../types/blueprint';

type Tab = 'reflection' | 'add';

interface Props {
  settings: ReflectionSystemSettings;
  onUpdateSettings: (updates: Partial<ReflectionSystemSettings>) => void;
  onAddChapter: (family: string, variant: string, props?: Record<string, unknown>) => void;
}

export default function LeftPanel({ settings, onUpdateSettings, onAddChapter }: Props) {
  const [tab, setTab] = useState<Tab>('reflection');

  return (
    <aside className="flex w-72 shrink-0 flex-col overflow-hidden border-r border-gray-200 bg-white">
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
            {t === 'reflection' ? 'Reflection' : 'Add'}
          </button>
        ))}
      </div>
      <div className="flex-1 overflow-y-auto">
        {tab === 'reflection' ? (
          <ReflectionSettings settings={settings} onUpdateSettings={onUpdateSettings} />
        ) : (
          <Palette onAddChapter={onAddChapter} />
        )}
      </div>
    </aside>
  );
}
