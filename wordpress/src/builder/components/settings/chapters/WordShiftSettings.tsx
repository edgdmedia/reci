import Field from '../../fields/Field';
import ItemListField from '../../fields/ItemListField';
import type { Chapter } from '../../../../../types/blueprint';

interface Props { chapter: Chapter; onChange: (u: Partial<Chapter>) => void; }

export default function WordShiftSettings({ chapter, onChange }: Props) {
  const c = chapter.content ?? {};
  return (
    <div>
      <Field label="Title" value={c.title ?? ''} onChange={(v) => onChange({ content: { ...c, title: v } })} />
      <ItemListField label="Words" value={c.items ?? []} onChange={(v) => onChange({ content: { ...c, items: v } })} />
    </div>
  );
}
