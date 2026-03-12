import { useRef } from 'react';

// WordPress media frame shape (subset we use)
interface WpMediaFrame {
  open: () => void;
  on: (event: string, cb: () => void) => void;
  state: () => { get: (key: string) => { first: () => { toJSON: () => { url: string } } } };
}

declare global {
  interface Window {
    wp?: { media: (opts: object) => WpMediaFrame };
  }
}

interface Props {
  label: string;
  value: string;
  onChange: (v: string) => void;
  type?: 'image' | 'audio' | 'video';
}

export default function MediaField({ label, value, onChange, type = 'image' }: Props) {
  const frameRef = useRef<WpMediaFrame | null>(null);
  const id = `media-field-${label.toLowerCase().replace(/\s+/g, '-')}`;

  const titles: Record<string, string> = {
    image: 'Select Image',
    audio: 'Select Audio',
    video: 'Select Video',
  };
  const libraryTypes: Record<string, string> = {
    image: 'image',
    audio: 'audio',
    video: 'video',
  };

  function openPicker() {
    if (!window.wp?.media) return;

    if (!frameRef.current) {
      frameRef.current = window.wp.media({
        title: titles[type],
        button: { text: `Use this ${type}` },
        multiple: false,
        library: { type: libraryTypes[type] },
      });
      frameRef.current.on('select', () => {
        const attachment = frameRef.current!.state().get('selection').first().toJSON();
        onChange(attachment.url);
      });
    }

    frameRef.current.open();
  }

  const isImage = type === 'image';

  return (
    <div>
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-gray-600">
        {label}
      </label>
      <div className="flex gap-1">
        <input
          id={id}
          type="url"
          value={value}
          onChange={(e) => onChange(e.target.value)}
          className="min-w-0 flex-1 rounded border border-gray-300 px-2 py-1 text-xs focus:border-blue-500 focus:outline-none"
          placeholder="https://..."
        />
        {window.wp?.media && (
          <button
            type="button"
            onClick={openPicker}
            title={`Select from media library`}
            className="shrink-0 rounded border border-gray-300 bg-gray-50 px-2 py-1 text-xs text-gray-600 hover:bg-gray-100 hover:text-gray-900"
          >
            &#128247;
          </button>
        )}
      </div>
      {isImage && value && (
        <div className="mt-1 overflow-hidden rounded border border-gray-200" style={{ maxHeight: '80px' }}>
          <img src={value} alt="" className="h-20 w-full object-cover" />
        </div>
      )}
    </div>
  );
}
