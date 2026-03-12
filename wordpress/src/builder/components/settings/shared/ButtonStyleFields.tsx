import Field from '../../fields/Field';
import SelectField from '../../fields/SelectField';
import ColorPicker from '../../ColorPicker';
import ResponsiveInput from '../../fields/ResponsiveInput';
import type { ButtonStyle } from '../../../../../types/blueprint';

interface Props {
  value: ButtonStyle;
  onChange: (v: ButtonStyle) => void;
}

export default function ButtonStyleFields({ value: s, onChange }: Props) {
  function set(patch: Partial<ButtonStyle>) { onChange({ ...s, ...patch }); }

  return (
    <details className="mb-3 rounded border border-gray-200">
      <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
        Button Style
      </summary>
      <div className="space-y-2 p-3">
        <Field
          label="Font Family"
          value={s.fontFamily ?? ''}
          onChange={(v) => set({ fontFamily: v || undefined })}
          placeholder="e.g. inherit"
        />
        <ResponsiveInput
          label="Font Size"
          value={s.fontSize ?? {}}
          onChange={(v) => set({ fontSize: v })}
          placeholder="e.g. 0.875rem"
        />
        <ResponsiveInput
          label="Padding X"
          value={s.paddingX ?? {}}
          onChange={(v) => set({ paddingX: v })}
          placeholder="e.g. 2.5rem"
        />
        <ResponsiveInput
          label="Padding Y"
          value={s.paddingY ?? {}}
          onChange={(v) => set({ paddingY: v })}
          placeholder="e.g. 1rem"
        />
        <Field
          label="Border Radius"
          value={s.borderRadius ?? ''}
          onChange={(v) => set({ borderRadius: v || undefined })}
          placeholder="e.g. 9999px, 4px"
        />
        <div>
          <label className="mb-1 block text-xs font-medium text-gray-600">Background</label>
          <ColorPicker color={s.backgroundColor ?? '#ffffff'} onChange={(v) => set({ backgroundColor: v })} />
        </div>
        <div>
          <label className="mb-1 block text-xs font-medium text-gray-600">Text Color</label>
          <ColorPicker color={s.textColor ?? '#000000'} onChange={(v) => set({ textColor: v })} />
        </div>
        <div>
          <label className="mb-1 block text-xs font-medium text-gray-600">Hover Background</label>
          <ColorPicker color={s.hoverBackgroundColor ?? '#000000'} onChange={(v) => set({ hoverBackgroundColor: v })} />
        </div>
        <div>
          <label className="mb-1 block text-xs font-medium text-gray-600">Hover Text Color</label>
          <ColorPicker color={s.hoverTextColor ?? '#ffffff'} onChange={(v) => set({ hoverTextColor: v })} />
        </div>
        <SelectField
          label="Action"
          value={s.action ?? 'next_chapter'}
          options={[
            { value: 'next_chapter', label: 'Proceed to next chapter' },
            { value: 'url',          label: 'Open URL'                },
          ]}
          onChange={(v) => set({ action: v as ButtonStyle['action'] })}
        />
        {s.action === 'url' && (
          <Field
            label="URL"
            value={s.actionUrl ?? ''}
            onChange={(v) => set({ actionUrl: v || undefined })}
            placeholder="https://..."
          />
        )}
      </div>
    </details>
  );
}
