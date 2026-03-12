interface Option { value: string; label: string }

interface Props {
  label: string;
  value: string;
  onChange: (v: string) => void;
  options: Option[];
}

export default function SelectField({ label, value, onChange, options }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="mb-2">
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-gray-600">{label}</label>
      <select
        id={id}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="w-full rounded border border-gray-300 px-2 py-1 text-sm"
      >
        {options.map((opt) => (
          <option key={opt.value} value={opt.value}>{opt.label}</option>
        ))}
      </select>
    </div>
  );
}
