interface Props {
  label: string;
  value: string;
  onChange: (v: string) => void;
  rows?: number;
}

export default function TextareaField({ label, value, onChange, rows = 3 }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="mb-2">
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-gray-600">{label}</label>
      <textarea
        id={id}
        rows={rows}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="w-full rounded border border-gray-300 px-2 py-1 text-sm"
      />
    </div>
  );
}
