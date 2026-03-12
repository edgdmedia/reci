import type { SceneItem } from '../../../../types/blueprint';

interface Props {
  label: string;
  value: SceneItem[];
  onChange: (items: SceneItem[]) => void;
  fields?: Array<keyof SceneItem>;
}

const DEFAULT_FIELDS: Array<keyof SceneItem> = ['label', 'content'];

export default function ItemListField({ label, value = [], onChange, fields = DEFAULT_FIELDS }: Props) {
  const items = value;
  function add() {
    onChange([...items, {}]);
  }

  function remove(i: number) {
    onChange(items.filter((_, idx) => idx !== i));
  }

  function update(i: number, field: keyof SceneItem, value: string | number) {
    onChange(items.map((item, idx) => idx === i ? { ...item, [field]: value } : item));
  }

  return (
    <div className="mb-3">
      <p className="mb-1 text-xs font-semibold text-gray-600">{label}</p>
      {items.map((item, i) => (
        <div key={i} className="mb-2 rounded border border-gray-200 p-2">
          {fields.map((field) => (
            <div key={field} className="mb-1">
              <label className="mb-0.5 block text-xs text-gray-500 capitalize">{String(field).replace(/_/g, ' ')}</label>
              {field === 'content' ? (
                <textarea
                  rows={2}
                  value={String(item[field] ?? '')}
                  onChange={(e) => update(i, field, e.target.value)}
                  className="w-full rounded border border-gray-300 px-2 py-1 text-xs"
                />
              ) : (
                <input
                  type={field === 'x' || field === 'y' ? 'number' : 'text'}
                  value={String(item[field] ?? '')}
                  onChange={(e) => update(i, field, field === 'x' || field === 'y' ? Number(e.target.value) : e.target.value)}
                  className="w-full rounded border border-gray-300 px-2 py-1 text-xs"
                />
              )}
            </div>
          ))}
          <button
            type="button"
            onClick={() => remove(i)}
            className="mt-1 text-xs text-red-500 hover:text-red-700"
          >
            Remove
          </button>
        </div>
      ))}
      <button
        type="button"
        onClick={add}
        className="rounded bg-gray-100 px-3 py-1 text-xs hover:bg-gray-200"
      >
        + Add {label}
      </button>
    </div>
  );
}
