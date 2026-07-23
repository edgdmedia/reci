interface Props { label: string; value: string; onChange(v: string): void; }
export default function UrlField({ label, value, onChange }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field">
      <label htmlFor={id} className="rs-field__label">{label}</label>
      <input id={id} type="url" value={value} onChange={e => onChange(e.target.value)} className="rs-field__input" />
    </div>
  );
}
