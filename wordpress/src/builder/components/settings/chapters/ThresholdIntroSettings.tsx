import Field from '../../fields/Field';
import TextareaField from '../../fields/TextareaField';
import UrlField from '../../fields/UrlField';
import ElementStyleFields from '../shared/ElementStyleFields';
import ButtonStyleFields from '../shared/ButtonStyleFields';
import ChapterAnimationFields from '../shared/ChapterAnimationFields';
import type { Chapter } from '../../../../../types/blueprint';

interface Props { chapter: Chapter; onChange: (u: Partial<Chapter>) => void; }

export default function ThresholdIntroSettings({ chapter, onChange }: Props) {
  const c = chapter.content ?? {};
  const a = chapter.animation ?? {};

  function setContent(patch: Partial<typeof c>) {
    onChange({ content: { ...c, ...patch } });
  }

  return (
    <div className="space-y-1">

      {/* Background */}
      <details className="mb-3 rounded border border-gray-200" open>
        <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
          Background
        </summary>
        <div className="space-y-2 p-3">
          <UrlField
            label="Background Image URL"
            value={c.background_image_url ?? ''}
            onChange={(v) => setContent({ background_image_url: v })}
          />
        </div>
      </details>

      {/* Title */}
      <details className="mb-3 rounded border border-gray-200" open>
        <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
          Title
        </summary>
        <div className="space-y-2 p-3">
          <Field
            label="Title Text"
            value={c.title ?? ''}
            onChange={(v) => setContent({ title: v })}
          />
          <ElementStyleFields
            label="Title"
            value={c.title_style ?? {}}
            onChange={(v) => setContent({ title_style: v })}
          />
        </div>
      </details>

      {/* Subtitle */}
      <details className="mb-3 rounded border border-gray-200">
        <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
          Subtitle
        </summary>
        <div className="space-y-2 p-3">
          <TextareaField
            label="Subtitle Text"
            value={c.subtitle ?? ''}
            onChange={(v) => setContent({ subtitle: v })}
          />
          <ElementStyleFields
            label="Subtitle"
            value={c.subtitle_style ?? {}}
            onChange={(v) => setContent({ subtitle_style: v })}
          />
        </div>
      </details>

      {/* Button */}
      <details className="mb-3 rounded border border-gray-200">
        <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
          Button
        </summary>
        <div className="space-y-2 p-3">
          <Field
            label="Button Label"
            value={c.button_label ?? ''}
            onChange={(v) => setContent({ button_label: v })}
            placeholder="Begin"
          />
          <ButtonStyleFields
            value={c.button_style ?? {}}
            onChange={(v) => setContent({ button_style: v })}
          />
        </div>
      </details>

      {/* Chapter animation */}
      <ChapterAnimationFields
        value={a}
        onChange={(v) => onChange({ animation: v })}
      />

    </div>
  );
}
