import ColorPicker from './ColorPicker';
import SelectField from './fields/SelectField';
import RangeField from './fields/RangeField';
import Field from './fields/Field';
import type { ReflectionStyle } from '../../../types/blueprint';

interface Props {
  mode: 'standard' | 'immersive';
  appearance: ReflectionStyle;
  onUpdateMode: (mode: 'standard' | 'immersive') => void;
  onUpdateAppearance: (updates: Partial<ReflectionStyle>) => void;
}

const THEME_OPTIONS = [
  { value: '', label: 'None (custom)' },
  { value: 'immersive-dark', label: 'Immersive Dark' },
  { value: 'light', label: 'Light' },
  { value: 'archival', label: 'Archival' },
  { value: 'editorial', label: 'Editorial' },
  { value: 'spotlight', label: 'Spotlight' },
];

const SPACING_OPTIONS = [
  { value: 'comfortable', label: 'Comfortable' },
  { value: 'tight', label: 'Tight' },
  { value: 'spacious', label: 'Spacious' },
];

export default function ReflectionSettings({ mode, appearance, onUpdateMode, onUpdateAppearance }: Props) {
  return (
    <div className="space-y-4 p-4">
      <SelectField
        label="Mode"
        value={mode}
        options={[
          { value: 'standard', label: 'Standard' },
          { value: 'immersive', label: 'Immersive' },
        ]}
        onChange={(v) => onUpdateMode(v as 'standard' | 'immersive')}
      />

      <SelectField
        label="Theme"
        value={appearance.theme ?? ''}
        options={THEME_OPTIONS}
        onChange={(v) => onUpdateAppearance({ theme: v as ReflectionStyle['theme'] || undefined })}
      />

      <div>
        <label className="mb-1 block text-xs font-medium text-gray-600">Background Color</label>
        <ColorPicker
          color={appearance.backgroundColor ?? '#ffffff'}
          onChange={(c) => onUpdateAppearance({ backgroundColor: c })}
        />
      </div>

      <div>
        <label className="mb-1 block text-xs font-medium text-gray-600">Text Color</label>
        <ColorPicker
          color={appearance.textColor ?? '#000000'}
          onChange={(c) => onUpdateAppearance({ textColor: c })}
        />
      </div>

      <div>
        <label className="mb-1 block text-xs font-medium text-gray-600">Accent Color</label>
        <ColorPicker
          color={appearance.accentColor ?? '#3b82f6'}
          onChange={(c) => onUpdateAppearance({ accentColor: c })}
        />
      </div>

      <Field
        label="Heading Font"
        value={appearance.headingFont ?? ''}
        onChange={(v) => onUpdateAppearance({ headingFont: v || undefined })}
        placeholder="e.g. Georgia, serif"
      />

      <RangeField
        label="Base Font Size (px)"
        value={appearance.baseFontSize ?? 16}
        min={14}
        max={26}
        onChange={(v) => onUpdateAppearance({ baseFontSize: v })}
      />

      <SelectField
        label="Spacing"
        value={appearance.spacing ?? 'comfortable'}
        options={SPACING_OPTIONS}
        onChange={(v) => onUpdateAppearance({ spacing: v as ReflectionStyle['spacing'] })}
      />
    </div>
  );
}
