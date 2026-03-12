import Field from '../../fields/Field';
import MediaField from '../../fields/MediaField';
import ItemListField from '../../fields/ItemListField';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function HotspotsSettings({ scene, onChange }: Props) {
  return (
    <div>
      <Field label="Title" value={scene.title ?? ''} onChange={(v) => onChange({ title: v })} />
      <MediaField label="Background Image URL" value={scene.background_image_url ?? ''} onChange={(v) => onChange({ background_image_url: v })} />
      <ItemListField label="Hotspots" value={scene.items ?? []} onChange={(v) => onChange({ items: v })} />
    </div>
  );
}
