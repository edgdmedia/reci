interface Props { label: string; value: string; onChange(v: string): void; description?: string; rows?: number; }
export default function TextareaField({ label, value, onChange, description, rows = 4 }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field">
      <label htmlFor={id} className="rs-field__label">{label}</label>
      {description && <p className="rs-field__desc">{description}</p>}
      <textarea id={id} value={value} rows={rows} onChange={e => onChange(e.target.value)} className="rs-field__textarea" />
    </div>
  );
}
