import Field from './Field';
import SelectField from './SelectField';
import TextareaField from './TextareaField';
import MediaField from './MediaField';
import type { ReflectionSystemFieldDefinition } from '../../../types/blueprint';

interface Props {
  label: string;
  value: unknown;
  onChange: (next: unknown[]) => void;
  itemFields?: Record<string, ReflectionSystemFieldDefinition>;
}

function coerceItems(value: unknown): Record<string, unknown>[] {
  if (!Array.isArray(value)) return [];
  return value.filter((item) => item && typeof item === 'object') as Record<string, unknown>[];
}

export default function RepeaterField({ label, value, onChange, itemFields = {} }: Props) {
  const items = coerceItems(value);
  const entries = Object.entries(itemFields);

  function updateItem(index: number, key: string, nextValue: unknown) {
    const next = items.map((item, itemIndex) => itemIndex === index ? { ...item, [key]: nextValue } : item);
    onChange(next);
  }

  function addItem() {
    const seed = Object.fromEntries(entries.map(([key, field]) => [key, field.type === 'repeater' ? [] : '']));
    onChange([...items, seed]);
  }

  function removeItem(index: number) {
    onChange(items.filter((_, itemIndex) => itemIndex !== index));
  }

  function renderSubField(index: number, key: string, field: ReflectionSystemFieldDefinition, itemValue: unknown) {
    if (field.type === 'textarea' || field.type === 'wysiwyg') {
      return <TextareaField key={key} label={field.label} value={typeof itemValue === 'string' ? itemValue : ''} onChange={(next) => updateItem(index, key, next)} rows={4} />;
    }
    if (field.type === 'select') {
      const options = Object.entries(field.options ?? {}).map(([optionValue, optionLabel]) => ({ value: optionValue, label: optionLabel }));
      return <SelectField key={key} label={field.label} value={typeof itemValue === 'string' ? itemValue : ''} options={options} onChange={(next) => updateItem(index, key, next)} />;
    }
    if (field.type === 'media') {
      return <MediaField key={key} label={field.label} value={typeof itemValue === 'string' ? itemValue : ''} onChange={(next) => updateItem(index, key, next)} />;
    }
    if (field.type === 'repeater') {
      return <RepeaterField key={key} label={field.label} value={itemValue} itemFields={field.itemFields} onChange={(next) => updateItem(index, key, next)} />;
    }
    return <Field key={key} label={field.label} value={typeof itemValue === 'string' ? itemValue : ''} onChange={(next) => updateItem(index, key, next)} />;
  }

  return (
    <div className="mb-3 rounded-lg border border-gray-200 bg-white p-3">
      <div className="mb-3 flex items-center justify-between gap-3">
        <label className="block text-xs font-medium text-gray-600">{label}</label>
        <button type="button" onClick={addItem} className="rounded border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100">Add item</button>
      </div>
      <div className="space-y-3">
        {items.map((item, index) => (
          <div key={index} className="rounded-md border border-gray-200 bg-gray-50 p-3">
            <div className="mb-2 flex items-center justify-between">
              <span className="text-xs font-semibold uppercase tracking-wide text-gray-500">Item {index + 1}</span>
              <button type="button" onClick={() => removeItem(index)} className="text-xs text-red-500 hover:text-red-700">Remove</button>
            </div>
            <div className="space-y-2">
              {entries.length ? entries.map(([key, field]) => renderSubField(index, key, field, item[key])) : (
                <TextareaField label={`${label} JSON`} value={JSON.stringify(item, null, 2)} onChange={() => {}} rows={4} />
              )}
            </div>
          </div>
        ))}
        {items.length === 0 && <p className="text-xs text-gray-400">No items yet.</p>}
      </div>
    </div>
  );
}
