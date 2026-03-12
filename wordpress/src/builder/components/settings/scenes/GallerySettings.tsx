import Field from '../../fields/Field';
import ItemListField from '../../fields/ItemListField';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function GallerySettings({ scene, onChange }: Props) {
  return (
    <div>
      <Field label="Title" value={scene.title ?? ''} onChange={(v) => onChange({ title: v })} />
      <ItemListField label="Images" value={scene.items ?? []} onChange={(v) => onChange({ items: v })} />
    </div>
  );
}
