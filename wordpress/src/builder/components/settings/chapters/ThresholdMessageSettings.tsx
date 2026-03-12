import Field from '../../fields/Field';
import TextareaField from '../../fields/TextareaField';
import type { Chapter } from '../../../../../types/blueprint';

interface Props { chapter: Chapter; onChange: (u: Partial<Chapter>) => void; }

export default function ThresholdMessageSettings({ chapter, onChange }: Props) {
  const c = chapter.content ?? {};
  return (
    <div>
      <Field label="Title" value={c.title ?? ''} onChange={(v) => onChange({ content: { ...c, title: v } })} />
      <TextareaField label="Content" value={c.content ?? ''} onChange={(v) => onChange({ content: { ...c, content: v } })} />
      <Field label="Button Label" value={c.button_label ?? ''} onChange={(v) => onChange({ content: { ...c, button_label: v } })} />
    </div>
  );
}
