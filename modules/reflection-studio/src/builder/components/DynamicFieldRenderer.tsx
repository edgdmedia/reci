import type { FieldDefinition } from '@/types/blueprint';
import TextField from './fields/TextField';
import TextareaField from './fields/TextareaField';
import SelectField from './fields/SelectField';
import ToggleField from './fields/ToggleField';
import ColorField from './fields/ColorField';
import RangeField from './fields/RangeField';
import UrlField from './fields/UrlField';
import MediaField from './fields/MediaField';
import RepeaterField from './fields/RepeaterField';
import ChapterTargetField from './fields/ChapterTargetField';

interface Props {
  fields: Record<string, FieldDefinition>;
  content: Record<string, unknown>;
  onChange(key: string, value: unknown): void;
}

function isVisible(field: FieldDefinition, content: Record<string, unknown>): boolean {
  if (!field.show_if) return true;
  return Object.entries(field.show_if).every(([key, expected]) => {
    const actual = content[key];
    if (Array.isArray(expected)) return expected.includes(actual as string);
    return actual === expected;
  });
}

export default function DynamicFieldRenderer({ fields, content, onChange }: Props) {
  return (
    <div className="rs-fields">
      {Object.entries(fields).map(([key, field]) => {
        if (!isVisible(field, content)) return null;
        const value = content[key];

        switch (field.type) {
          case 'text':
            return <TextField key={key} label={field.label} value={String(value ?? '')} description={field.description} maxLength={field.maxLength} onChange={v => onChange(key, v)} />;
          case 'textarea':
          case 'richtext':
            return <TextareaField key={key} label={field.label} value={String(value ?? '')} description={field.description} onChange={v => onChange(key, v)} />;
          case 'select':
            return <SelectField key={key} label={field.label} value={String(value ?? '')} options={field.options ?? {}} description={field.description} onChange={v => onChange(key, v)} />;
          case 'toggle':
            return <ToggleField key={key} label={field.label} value={Boolean(value)} onChange={v => onChange(key, v)} />;
          case 'color':
            return <ColorField key={key} label={field.label} value={String(value ?? '')} onChange={v => onChange(key, v)} />;
          case 'range':
            return <RangeField key={key} label={field.label} value={Number(value ?? 0)} onChange={v => onChange(key, v)} />;
          case 'url':
            return <UrlField key={key} label={field.label} value={String(value ?? '')} onChange={v => onChange(key, v)} />;
          case 'media':
            return <MediaField key={key} label={field.label} value={String(value ?? '')} onChange={v => onChange(key, v)} />;
          case 'repeater':
            return <RepeaterField key={key} label={field.label} value={value} itemFields={field.itemFields} maxItems={field.maxItems} onChange={v => onChange(key, v)} />;
          case 'chapter-target':
            return <ChapterTargetField key={key} label={field.label} value={String(value ?? '')} onChange={v => onChange(key, v)} />;
          default:
            return null;
        }
      })}
    </div>
  );
}
