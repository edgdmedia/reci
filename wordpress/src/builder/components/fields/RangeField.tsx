interface Props {
  label: string;
  value: number;
  min: number;
  max: number;
  onChange: (v: number) => void;
}

export default function RangeField({ label, value, min, max, onChange }: Props) {
  return (
    <div className="mb-2">
      <label className="mb-1 block text-xs font-medium text-gray-600">
        {label}: <strong>{value}</strong>
      </label>
      <input
        type="range"
        min={min}
        max={max}
        value={value}
        onChange={(e) => onChange(Number(e.target.value))}
        className="w-full"
      />
    </div>
  );
}
