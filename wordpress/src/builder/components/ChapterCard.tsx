import { useMemo, useState } from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import Field from './fields/Field';
import MediaField from './fields/MediaField';
import RepeaterField from './fields/RepeaterField';
import SelectField from './fields/SelectField';
import TextareaField from './fields/TextareaField';
import type { BuilderConfig, ReflectionSystemComponent, ReflectionSystemFieldDefinition } from '../../types/blueprint';

declare global {
  interface Window {
    RECIReflectionBuilderConfig?: BuilderConfig;
  }
}

interface Props {
  chapter: ReflectionSystemComponent;
  onUpdate: (updates: Partial<ReflectionSystemComponent>) => void;
  onDuplicate: () => void;
  onRemove: () => void;
}

function chapterSummary(chapter: ReflectionSystemComponent): string {
  const title = typeof chapter.props?.title === 'string' ? chapter.props.title : '';
  const eyebrow = typeof chapter.props?.eyebrow === 'string' ? chapter.props.eyebrow : '';
  const repeaters = Object.entries(chapter.props ?? {}).find(([, value]) => Array.isArray(value));
  if (title) return title;
  if (eyebrow) return eyebrow;
  if (repeaters) return `${repeaters[0]}: ${(repeaters[1] as unknown[]).length} item(s)`;
  return 'No content summary yet';
}

export default function ChapterCard({ chapter, onUpdate, onDuplicate, onRemove }: Props) {
  const [open, setOpen] = useState(false);
  const { attributes, listeners, setNodeRef, transform, transition } = useSortable({ id: chapter.id });
  const registry = window.RECIReflectionBuilderConfig?.registry ?? {};
  const definition = registry[chapter.family];
  const variantOptions = definition ? Object.entries(definition.variants).map(([value, label]) => ({ value, label })) : [];
  const fieldEntries = useMemo(() => Object.entries(definition?.fields ?? {}), [definition]);

  function updateProp(key: string, value: unknown) {
    onUpdate({ props: { [key]: value } });
  }

  function renderField(key: string, field: ReflectionSystemFieldDefinition) {
    const value = chapter.props?.[key];
    if (field.type === 'textarea' || field.type === 'wysiwyg') {
      return (
        <TextareaField
          key={key}
          label={field.label}
          value={typeof value === 'string' ? value : ''}
          onChange={(next) => updateProp(key, next)}
          rows={field.type === 'wysiwyg' ? 6 : 4}
        />
      );
    }
    if (field.type === 'select') {
      const options = Object.entries(field.options ?? {}).map(([optionValue, optionLabel]) => ({ value: optionValue, label: optionLabel }));
      return (
        <SelectField
          key={key}
          label={field.label}
          value={typeof value === 'string' ? value : ''}
          options={options}
          onChange={(next) => updateProp(key, next)}
        />
      );
    }
    if (field.type === 'repeater') {
      return (
        <RepeaterField
          key={key}
          label={field.label}
          value={value}
          itemFields={field.itemFields}
          onChange={(next) => updateProp(key, next)}
        />
      );
    }
    if (field.type === 'media') {
      return (
        <MediaField
          key={key}
          label={field.label}
          value={typeof value === 'string' ? value : ''}
          onChange={(next) => updateProp(key, next)}
        />
      );
    }
    return (
      <Field
        key={key}
        label={field.label}
        value={typeof value === 'string' ? value : ''}
        onChange={(next) => updateProp(key, next)}
      />
    );
  }

  return (
    <div
      ref={setNodeRef}
      style={{ transform: CSS.Transform.toString(transform), transition }}
      className="mb-3 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
    >
      <div
        onClick={() => setOpen((value) => !value)}
        className={`cursor-pointer p-3 ${open ? 'bg-blue-50' : 'hover:bg-gray-50'}`}
        style={open ? { borderLeft: '3px solid #2563eb' } : { borderLeft: '3px solid transparent' }}
      >
        <div className="flex items-start gap-2">
          <span {...attributes} {...listeners} onClick={(event) => event.stopPropagation()} className="mt-0.5 cursor-grab text-gray-400 hover:text-gray-600" aria-label="Drag">⠿</span>
          <div className="min-w-0 flex-1">
            <div className="flex items-center gap-2">
              <span className="rounded bg-blue-100 px-1.5 py-0.5 text-[11px] font-medium text-blue-700">{chapter.family}</span>
              <span className="text-xs text-gray-500">{definition?.variants?.[chapter.variant] ?? chapter.variant}</span>
            </div>
            <div className="mt-1 text-sm font-medium text-gray-800">{chapterSummary(chapter)}</div>
            <div className="mt-1 text-xs text-gray-400">{chapter.id}</div>
          </div>
          <div className="flex items-center gap-2" onClick={(event) => event.stopPropagation()}>
            <button type="button" onClick={onDuplicate} className="text-xs text-gray-500 hover:text-gray-800">Duplicate</button>
            <button type="button" onClick={onRemove} className="text-xs text-red-400 hover:text-red-600" aria-label="Remove">✕</button>
            <span className="mr-1 text-xs text-gray-400">{open ? '▲' : '▼'}</span>
          </div>
        </div>
      </div>
      {open && (
        <div className="space-y-3 border-t border-gray-100 bg-gray-50 p-3">
          <Field label="Chapter ID" value={chapter.id} onChange={() => {}} disabled />
          <Field label="Family" value={chapter.family} onChange={() => {}} disabled />
          <SelectField
            label="Style"
            value={chapter.variant}
            options={variantOptions}
            onChange={(value) => onUpdate({ variant: value })}
          />
          {fieldEntries.map(([key, field]) => renderField(key, field))}
        </div>
      )}
    </div>
  );
}
