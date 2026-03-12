import Field from '../../fields/Field';
import TextareaField from '../../fields/TextareaField';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function RichTextSettings({ scene, onChange }: Props) {
  return (
    <div>
      <Field label="Title" value={scene.title ?? ''} onChange={(v) => onChange({ title: v })} />
      <TextareaField label="Content" value={scene.content ?? ''} onChange={(v) => onChange({ content: v })} />
    </div>
  );
}
