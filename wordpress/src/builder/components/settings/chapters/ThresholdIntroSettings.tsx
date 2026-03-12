import Field from '../../fields/Field';
import TextareaField from '../../fields/TextareaField';
import UrlField from '../../fields/UrlField';
import type { Chapter } from '../../../../../types/blueprint';

interface Props { chapter: Chapter; onChange: (u: Partial<Chapter>) => void; }

export default function ThresholdIntroSettings({ chapter, onChange }: Props) {
  const c = chapter.content ?? {};
  return (
    <div>
      <Field label="Title" value={c.title ?? ''} onChange={(v) => onChange({ content: { ...c, title: v } })} />
      <TextareaField label="Subtitle" value={c.subtitle ?? ''} onChange={(v) => onChange({ content: { ...c, subtitle: v } })} />
      <Field label="Button Label" value={c.button_label ?? ''} onChange={(v) => onChange({ content: { ...c, button_label: v } })} />
      <UrlField label="Background Image URL" value={c.background_image_url ?? ''} onChange={(v) => onChange({ content: { ...c, background_image_url: v } })} />
    </div>
  );
}
