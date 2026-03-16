interface Props {
  label: string;
  value: string;
  onChange: (v: string) => void;
  type?: string;
  placeholder?: string;
  disabled?: boolean;
}

export default function Field({ label, value, onChange, type = 'text', placeholder, disabled = false }: Props) {
  const id = `field-${label.toLowerCase().replace(/\s+/g, '-')}`;
  return (
    <div className="mb-2">
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-gray-600">{label}</label>
      <input
        id={id}
        type={type}
        value={value}
        placeholder={placeholder}
        disabled={disabled}
        onChange={(e) => onChange(e.target.value)}
        className="w-full rounded border border-gray-300 px-2 py-1 text-sm disabled:bg-gray-100 disabled:text-gray-500"
      />
    </div>
  );
}
