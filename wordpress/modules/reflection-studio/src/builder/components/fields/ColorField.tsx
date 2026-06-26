interface Props { label: string; value: string; onChange(v: string): void; }
export default function ColorField({ label, value, onChange }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field rs-field--color">
      <label htmlFor={id} className="rs-field__label">{label}</label>
      <input id={id} type="color" value={value || '#000000'} onChange={e => onChange(e.target.value)} className="rs-field__color" />
    </div>
  );
}
