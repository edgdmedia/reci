import type { FieldDefinition } from '@/types/blueprint';
import DynamicFieldRenderer from '../DynamicFieldRenderer';
interface Props {
  label: string;
  value: unknown;
  itemFields?: Record<string, FieldDefinition>;
  maxItems?: number;
  onChange(v: unknown[]): void;
}
export default function RepeaterField({ label, value, itemFields, maxItems, onChange }: Props) {
  const items = Array.isArray(value) ? (value as Record<string, unknown>[]) : [];
  function addItem() { onChange([...items, {}]); }
  function removeItem(i: number) { onChange(items.filter((_, idx) => idx !== i)); }
  function updateItem(i: number, key: string, val: unknown) {
    const next = items.map((item, idx) => idx === i ? { ...item, [key]: val } : item);
    onChange(next);
  }
  return (
    <div className="rs-repeater">
      <div className="rs-repeater__label">{label}</div>
      {items.map((item, i) => (
        <div key={i} className="rs-repeater__item">
          {itemFields && (
            <DynamicFieldRenderer
              fields={itemFields}
              content={item}
              onChange={(k, v) => updateItem(i, k, v)}
            />
          )}
          <button type="button" onClick={() => removeItem(i)} className="rs-repeater__remove">Remove</button>
        </div>
      ))}
      {(!maxItems || items.length < maxItems) && (
        <button type="button" onClick={addItem} className="rs-repeater__add">+ Add item</button>
      )}
    </div>
  );
}
