import { useState } from 'react';
import { HexColorPicker } from 'react-colorful';

interface Props {
  label: string;
  value: string;
  onChange: (v: string) => void;
}

export default function ColorPicker({ label, value, onChange }: Props) {
  const [open, setOpen] = useState(false);

  return (
    <div className="mb-2">
      <label className="mb-1 block text-xs font-medium text-gray-600">{label}</label>
      <div className="flex items-center gap-2">
        <button
          type="button"
          className="h-7 w-7 rounded border border-gray-300"
          style={{ background: value || '#ffffff' }}
          onClick={() => setOpen((o) => !o)}
          aria-label={`Pick ${label} color`}
        />
        <input
          type="text"
          value={value}
          onChange={(e) => onChange(e.target.value)}
          className="w-28 rounded border border-gray-300 px-2 py-1 text-xs font-mono"
          placeholder="#000000"
        />
      </div>
      {open && (
        <div className="mt-2">
          <HexColorPicker color={value} onChange={onChange} />
          <button
            type="button"
            onClick={() => setOpen(false)}
            className="mt-1 text-xs text-gray-500 hover:text-gray-800"
          >
            Close
          </button>
        </div>
      )}
    </div>
  );
}
