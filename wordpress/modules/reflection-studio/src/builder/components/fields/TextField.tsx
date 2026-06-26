interface Props { label: string; value: string; onChange(v: string): void; description?: string; maxLength?: number; disabled?: boolean; }
export default function TextField({ label, value, onChange, description, maxLength, disabled }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field">
      <label htmlFor={id} className="rs-field__label">{label}</label>
      {description && <p className="rs-field__desc">{description}</p>}
      <input id={id} type="text" value={value} maxLength={maxLength} disabled={disabled}
        onChange={e => onChange(e.target.value)} className="rs-field__input" />
    </div>
  );
}
