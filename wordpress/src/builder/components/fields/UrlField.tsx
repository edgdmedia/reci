import Field from './Field';

interface Props { label: string; value: string; onChange: (v: string) => void }

export default function UrlField({ label, value, onChange }: Props) {
  return <Field label={label} value={value} onChange={onChange} type="url" />;
}
