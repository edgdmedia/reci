import type { BuilderConfig } from '../../types/blueprint';

declare global {
  interface Window {
    RECIReflectionBuilderConfig?: BuilderConfig;
  }
}

interface Props {
  onAddChapter: (family: string, variant: string, props?: Record<string, unknown>) => void;
}

export default function Palette({ onAddChapter }: Props) {
  const registry = window.RECIReflectionBuilderConfig?.registry ?? {};
  const chapterEntries = Object.entries(registry).filter(([, definition]) => definition.kind === 'chapter');

  return (
    <aside className="flex flex-col gap-6 overflow-y-auto p-4">
      <div>
        <p className="mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Chapter Families</p>
        <ul className="space-y-2">
          {chapterEntries.map(([family, definition]) => (
            <li key={family}>
              <button
                type="button"
                onClick={() => onAddChapter(family, definition.default_variant, {})}
                className="w-full rounded-lg border border-gray-200 px-3 py-2 text-left text-sm hover:bg-blue-50 hover:text-blue-700"
              >
                <span className="block font-medium text-gray-800">{definition.label}</span>
                <span className="mt-1 block text-xs text-gray-500">Default style: {definition.variants[definition.default_variant]}</span>
              </button>
            </li>
          ))}
        </ul>
      </div>
    </aside>
  );
}
