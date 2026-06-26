interface Props { label: string; value: boolean; onChange(v: boolean): void; }
export default function ToggleField({ label, value, onChange }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field rs-field--toggle">
      <label htmlFor={id} className="rs-field__label">
        <input id={id} type="checkbox" checked={value} onChange={e => onChange(e.target.checked)} />
        {label}
      </label>
    </div>
  );
}
