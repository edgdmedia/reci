import TextareaField from '../../fields/TextareaField';
import Field from '../../fields/Field';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function QuoteSettings({ scene, onChange }: Props) {
  return (
    <div>
      <TextareaField label="Quote" value={scene.quote ?? ''} onChange={(v) => onChange({ quote: v })} />
      <Field label="Speaker" value={scene.speaker ?? ''} onChange={(v) => onChange({ speaker: v })} />
      <Field label="Role" value={scene.role ?? ''} onChange={(v) => onChange({ role: v })} />
    </div>
  );
}
