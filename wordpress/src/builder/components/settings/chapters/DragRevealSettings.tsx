import Field from '../../fields/Field';
import TextareaField from '../../fields/TextareaField';
import type { Chapter } from '../../../../../types/blueprint';

interface Props { chapter: Chapter; onChange: (u: Partial<Chapter>) => void; }

export default function DragRevealSettings({ chapter, onChange }: Props) {
  const c = chapter.content ?? {};
  return (
    <div>
      <Field label="Title" value={c.title ?? ''} onChange={(v) => onChange({ content: { ...c, title: v } })} />
      <TextareaField label="Content" value={c.content ?? ''} onChange={(v) => onChange({ content: { ...c, content: v } })} />
    </div>
  );
}
