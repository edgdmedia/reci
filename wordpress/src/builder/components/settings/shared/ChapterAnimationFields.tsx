import SelectField from '../../fields/SelectField';
import Field from '../../fields/Field';
import type { ChapterAnimation } from '../../../../../types/blueprint';

interface Props {
  value: ChapterAnimation;
  onChange: (v: ChapterAnimation) => void;
}

export default function ChapterAnimationFields({ value: a, onChange }: Props) {
  function set(patch: Partial<ChapterAnimation>) { onChange({ ...a, ...patch }); }

  return (
    <details className="mb-3 rounded border border-gray-200">
      <summary className="cursor-pointer select-none bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100">
        Chapter Entry Animation
      </summary>
      <div className="space-y-2 p-3">
        <SelectField
          label="Type"
          value={a.type ?? 'fade'}
          options={[
            { value: 'none',       label: 'None'        },
            { value: 'fade',       label: 'Fade'        },
            { value: 'slide-up',   label: 'Slide Up'    },
            { value: 'slide-left', label: 'Slide Left'  },
            { value: 'zoom',       label: 'Zoom'        },
            { value: 'curtain',    label: 'Curtain'     },
          ]}
          onChange={(v) => set({ type: v as ChapterAnimation['type'] })}
        />
        {a.type !== 'none' && (
          <>
            <Field
              label="Duration"
              value={a.duration ?? ''}
              onChange={(v) => set({ duration: v || undefined })}
              placeholder="e.g. 0.6s"
            />
            <SelectField
              label="Easing"
              value={a.easing ?? 'ease-out'}
              options={[
                { value: 'ease',         label: 'Ease'         },
                { value: 'ease-in',      label: 'Ease In'      },
                { value: 'ease-out',     label: 'Ease Out'     },
                { value: 'ease-in-out',  label: 'Ease In-Out'  },
                { value: 'linear',       label: 'Linear'       },
              ]}
              onChange={(v) => set({ easing: v as ChapterAnimation['easing'] })}
            />
          </>
        )}
      </div>
    </details>
  );
}
