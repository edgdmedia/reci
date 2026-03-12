import Field from '../../fields/Field';
import UrlField from '../../fields/UrlField';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function MediaEmbedSettings({ scene, onChange }: Props) {
  return (
    <div>
      <Field label="Title" value={scene.title ?? ''} onChange={(v) => onChange({ title: v })} />
      <UrlField label="Video URL" value={scene.video_url ?? ''} onChange={(v) => onChange({ video_url: v })} />
      <UrlField label="Audio URL" value={scene.audio_url ?? ''} onChange={(v) => onChange({ audio_url: v })} />
    </div>
  );
}
