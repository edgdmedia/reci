interface Props { label: string; value: string; options: Record<string, string>; onChange(v: string): void; description?: string; }
export default function SelectField({ label, value, options, onChange, description }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field">
      <label htmlFor={id} className="rs-field__label">{label}</label>
      {description && <p className="rs-field__desc">{description}</p>}
      <select id={id} value={value} onChange={e => onChange(e.target.value)} className="rs-field__select">
        {Object.entries(options).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
      </select>
    </div>
  );
}
