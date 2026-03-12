import Field from '../../fields/Field';
import MediaField from '../../fields/MediaField';
import type { Scene } from '../../../../../types/blueprint';

interface Props { scene: Scene; onChange: (u: Partial<Scene>) => void; }

export default function MediaEmbedSettings({ scene, onChange }: Props) {
  return (
    <div>
      <Field label="Title" value={scene.title ?? ''} onChange={(v) => onChange({ title: v })} />
      <MediaField label="Video URL" value={scene.video_url ?? ''} onChange={(v) => onChange({ video_url: v })} type="video" />
      <MediaField label="Audio URL" value={scene.audio_url ?? ''} onChange={(v) => onChange({ audio_url: v })} type="audio" />
    </div>
  );
}
