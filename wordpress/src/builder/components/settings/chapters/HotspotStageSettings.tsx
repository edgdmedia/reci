import Field from '../../fields/Field';
import MediaField from '../../fields/MediaField';
import ItemListField from '../../fields/ItemListField';
import type { Chapter } from '../../../../../types/blueprint';

interface Props { chapter: Chapter; onChange: (u: Partial<Chapter>) => void; }

export default function HotspotStageSettings({ chapter, onChange }: Props) {
  const c = chapter.content ?? {};
  return (
    <div>
      <Field label="Title" value={c.title ?? ''} onChange={(v) => onChange({ content: { ...c, title: v } })} />
      <MediaField label="Background Image URL" value={c.background_image_url ?? ''} onChange={(v) => onChange({ content: { ...c, background_image_url: v } })} />
      <ItemListField label="Hotspots" value={c.items ?? []} onChange={(v) => onChange({ content: { ...c, items: v } })} />
    </div>
  );
}
