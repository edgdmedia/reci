import Field from './fields/Field';
import SelectField from './fields/SelectField';
import type { ReflectionSystemSettings } from '../../types/blueprint';

interface Props {
  settings: ReflectionSystemSettings;
  onUpdateSettings: (updates: Partial<ReflectionSystemSettings>) => void;
}

export default function ReflectionSettings({ settings, onUpdateSettings }: Props) {
  return (
    <div className="space-y-4 p-4">
      <Field label="System" value="reflections" onChange={() => {}} disabled />
      <SelectField
        label="Mode"
        value={settings.mode}
        options={[{ value: 'immersive', label: 'Immersive' }]}
        onChange={() => onUpdateSettings({ mode: 'immersive' })}
      />
      <Field
        label="Palette"
        value={settings.palette ?? ''}
        onChange={(value) => onUpdateSettings({ palette: value })}
        placeholder="Optional palette key"
      />
      <Field
        label="Stage Controller"
        value={settings.stage_controller ?? 'default'}
        onChange={(value) => onUpdateSettings({ stage_controller: value || 'default' })}
        placeholder="default"
      />
      <SelectField
        label="Include menu overlay"
        value={settings.menu_enabled === false ? '0' : '1'}
        options={[{ value: '1', label: 'Yes' }, { value: '0', label: 'No' }]}
        onChange={(value) => onUpdateSettings({ menu_enabled: value === '1' })}
      />
      <Field
        label="Menu back URL"
        value={settings.menu_back_url ?? ''}
        onChange={(value) => onUpdateSettings({ menu_back_url: value })}
        placeholder="/reflections/"
      />
    </div>
  );
}
