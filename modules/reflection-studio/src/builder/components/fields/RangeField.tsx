interface Props { label: string; value: number; min?: number; max?: number; onChange(v: number): void; }
export default function RangeField({ label, value, min = 0, max = 100, onChange }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="rs-field rs-field--range">
      <label htmlFor={id} className="rs-field__label">{label} <span>{value}</span></label>
      <input id={id} type="range" min={min} max={max} value={value} onChange={e => onChange(Number(e.target.value))} />
    </div>
  );
}
