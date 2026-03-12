import Field from '../../fields/Field';
import TextareaField from '../../fields/TextareaField';
import UrlField from '../../fields/UrlField';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function HeroSettings({ scene, onChange }: Props) {
  return (
    <div>
      <Field label="Title" value={scene.title ?? ''} onChange={(v) => onChange({ title: v })} />
      <TextareaField label="Content" value={scene.content ?? ''} onChange={(v) => onChange({ content: v })} />
      <UrlField label="Background Image URL" value={scene.background_image_url ?? ''} onChange={(v) => onChange({ background_image_url: v })} />
      <Field label="Badge" value={scene.badge ?? ''} onChange={(v) => onChange({ badge: v })} />
    </div>
  );
}
