import Field from '../../fields/Field';
import SelectField from '../../fields/SelectField';
import ColorPicker from '../../ColorPicker';
import ResponsiveInput from '../../fields/ResponsiveInput';
import ResponsiveSelect from '../../fields/ResponsiveSelect';
import type { ElementStyle, AnimationType, TextAlign, Responsive } from '../../../../../types/blueprint';

interface Props {
  label: string;
  value: ElementStyle;
  onChange: (v: ElementStyle) => void;
}

const ANIMATIONS: { value: AnimationType | ''; label: string }[] = [
  { value: '',            label: 'None'        },
  { value: 'fade-in',    label: 'Fade In'      },
  { value: 'slide-up',   label: 'Slide Up'     },
  { value: 'slide-down', label: 'Slide Down'   },
  { value: 'slide-left', label: 'Slide Left'   },
  { value: 'slide-right',label: 'Slide Right'  },
  { value: 'zoom-in',    label: 'Zoom In'      },
];

const ALIGNS: { value: TextAlign; label: string }[] = [
  { value: 'left',   label: 'Left'   },
  { value: 'center', label: 'Center' },
  { value: 'right',  label: 'Right'  },
];

export default function ElementStyleFields({ label, value: s, onChange }: Props) {
  function set(patch: Partial<ElementStyle>) { onChange({ ...s, ...patch }); }

  return (
    <details className="mb-3 rounded border border-gray-200">
      <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
        {label} Style
      </summary>
      <div className="space-y-2 p-3">
        <Field
          label="Font Family"
          value={s.fontFamily ?? ''}
          onChange={(v) => set({ fontFamily: v || undefined })}
          placeholder="e.g. Georgia, serif"
        />
        <ResponsiveInput
          label="Font Size"
          value={s.fontSize ?? {}}
          onChange={(v) => set({ fontSize: v })}
          placeholder="e.g. 3rem"
        />
        <div>
          <label className="mb-1 block text-xs font-medium text-gray-600">Color</label>
          <ColorPicker
            color={s.color ?? '#ffffff'}
            onChange={(v) => set({ color: v })}
          />
        </div>
        <ResponsiveSelect
          label="Alignment"
          value={s.textAlign as Responsive<string> ?? {}}
          options={ALIGNS}
          onChange={(v) => set({ textAlign: v as Responsive<TextAlign> })}
        />
        <SelectField
          label="Animation"
          value={s.animation ?? ''}
          options={ANIMATIONS}
          onChange={(v) => set({ animation: (v as AnimationType) || undefined })}
        />
        {s.animation && s.animation !== 'none' && (
          <>
            <Field
              label="Duration"
              value={s.animationDuration ?? ''}
              onChange={(v) => set({ animationDuration: v || undefined })}
              placeholder="e.g. 0.8s"
            />
            <Field
              label="Delay"
              value={s.animationDelay ?? ''}
              onChange={(v) => set({ animationDelay: v || undefined })}
              placeholder="e.g. 0.3s"
            />
          </>
        )}
      </div>
    </details>
  );
}
