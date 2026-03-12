import type { SceneType, Chapter } from '../../../types/blueprint';

const STANDARD_SCENES: Array<{ type: SceneType; label: string }> = [
  { type: 'hero',           label: 'Hero' },
  { type: 'rich_text',      label: 'Rich Text' },
  { type: 'quote',          label: 'Quote' },
  { type: 'gallery',        label: 'Gallery' },
  { type: 'timeline',       label: 'Timeline' },
  { type: 'hotspots',       label: 'Hotspot Image' },
  { type: 'compare_panels', label: 'Compare Panels' },
  { type: 'media_embed',    label: 'Media Embed' },
  { type: 'documents',      label: 'Documents' },
  { type: 'prompt_list',    label: 'Prompt List' },
];

const IMMERSIVE_CHAPTERS: Array<{ type: Chapter['type']; label: string }> = [
  { type: 'threshold_intro',   label: 'Threshold Intro' },
  { type: 'content_stage',     label: 'Content Stage' },
  { type: 'threshold_message', label: 'Threshold Message' },
  { type: 'hotspot_stage',     label: 'Hotspot Stage' },
  { type: 'progressive_text',  label: 'Progressive Text' },
  { type: 'horizontal_panels', label: 'Horizontal Panels' },
  { type: 'reflection_prompt', label: 'Reflection Prompt' },
  { type: 'step_sequence',     label: 'Step Sequence' },
  { type: 'data_cards',        label: 'Data Cards' },
  { type: 'drag_reveal',       label: 'Drag Reveal' },
  { type: 'word_shift',        label: 'Word Shift' },
  { type: 'parallax_stage',    label: 'Parallax Stage' },
];

interface Props {
  onAddScene: (type: SceneType) => void;
  onAddChapter: (type: Chapter['type']) => void;
}

export default function Palette({ onAddScene, onAddChapter }: Props) {
  return (
    <aside className="flex flex-col gap-6 overflow-y-auto p-4">
      <div>
        <p className="mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Standard Scenes</p>
        <ul className="space-y-1">
          {STANDARD_SCENES.map(({ type, label }) => (
            <li key={type}>
              <button
                type="button"
                onClick={() => onAddScene(type)}
                className="w-full rounded px-3 py-1.5 text-left text-sm hover:bg-blue-50 hover:text-blue-700"
              >
                {label}
              </button>
            </li>
          ))}
        </ul>
      </div>
      <div>
        <p className="mb-2 text-xs font-bold uppercase tracking-widest text-gray-500">Immersive Chapters</p>
        <ul className="space-y-1">
          {IMMERSIVE_CHAPTERS.map(({ type, label }) => (
            <li key={type}>
              <button
                type="button"
                onClick={() => onAddChapter(type)}
                className="w-full rounded px-3 py-1.5 text-left text-sm hover:bg-purple-50 hover:text-purple-700"
              >
                {label}
              </button>
            </li>
          ))}
        </ul>
      </div>
    </aside>
  );
}
