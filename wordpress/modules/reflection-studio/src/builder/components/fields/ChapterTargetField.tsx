import { useBuilderStore } from '@/builder/store/builderStore';
interface Props { label: string; value: string; onChange(v: string): void; }
export default function ChapterTargetField({ label, value, onChange }: Props) {
  const chapters = useBuilderStore(s => s.blueprint.chapters);
  const options = chapters.map(c => ({
    value: c.id,
    label: String(c.content?.title ?? c.content?.eyebrow ?? c.family),
  }));
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field">
      <label htmlFor={id} className="rs-field__label">{label}</label>
      <select id={id} value={value} onChange={e => onChange(e.target.value)} className="rs-field__select">
        <option value="">— Select chapter —</option>
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
    </div>
  );
}
