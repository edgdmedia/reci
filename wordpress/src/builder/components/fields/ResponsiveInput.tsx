import { useState } from 'react';
import type { Responsive } from '../../../../types/blueprint';

type Device = 'desktop' | 'tablet' | 'mobile';

interface Props {
  label: string;
  value: Responsive<string>;
  onChange: (v: Responsive<string>) => void;
  placeholder?: string;
  type?: string;
}

const ICONS: Record<Device, string> = {
  desktop: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>`,
  tablet:  `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><rect x="4" y="2" width="16" height="20" rx="2"/><circle cx="12" cy="18" r="1"/></svg>`,
  mobile:  `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><rect x="6" y="2" width="12" height="20" rx="2"/><circle cx="12" cy="18" r="1"/></svg>`,
};

export default function ResponsiveInput({ label, value, onChange, placeholder, type = 'text' }: Props) {
  const [device, setDevice] = useState<Device>('desktop');
  const id = `resp-${label.toLowerCase().replace(/\s+/g, '-')}`;
  const inherited = device !== 'desktop' ? (value.desktop ?? '') : '';

  return (
    <div className="mb-2">
      <div className="mb-1 flex items-center justify-between">
        <label htmlFor={id} className="text-xs font-medium text-gray-600">{label}</label>
        <div className="flex gap-0.5 rounded border border-gray-200 p-0.5">
          {(['desktop', 'tablet', 'mobile'] as Device[]).map((d) => (
            <button
              key={d}
              type="button"
              title={d.charAt(0).toUpperCase() + d.slice(1)}
              onClick={() => setDevice(d)}
              className={`rounded px-1.5 py-0.5 transition ${device === d ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-gray-700'}`}
              dangerouslySetInnerHTML={{ __html: ICONS[d] }}
            />
          ))}
        </div>
      </div>
      <input
        id={id}
        type={type}
        value={value[device] ?? ''}
        placeholder={inherited ? `↑ ${inherited}` : placeholder}
        onChange={(e) => onChange({ ...value, [device]: e.target.value || undefined })}
        className="w-full rounded border border-gray-300 px-2 py-1 text-sm"
      />
    </div>
  );
}
