interface Props {
  label: string;
  value: string;
  onChange: (v: string) => void;
  type?: string;
  placeholder?: string;
}

export default function Field({ label, value, onChange, type = 'text', placeholder }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="mb-2">
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-gray-600">{label}</label>
      <input
        id={id}
        type={type}
        value={value}
        placeholder={placeholder}
        onChange={(e) => onChange(e.target.value)}
        className="w-full rounded border border-gray-300 px-2 py-1 text-sm"
      />
    </div>
  );
}
