import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import DynamicFieldRenderer from './DynamicFieldRenderer';
import type { FieldDefinition } from '@/types/blueprint';

const fields: Record<string, FieldDefinition> = {
  title:       { type: 'text',     label: 'Title' },
  body:        { type: 'textarea', label: 'Body' },
  mode:        { type: 'select',   label: 'Mode', options: { a: 'Option A', b: 'Option B' } },
  enabled:     { type: 'toggle',   label: 'Enabled' },
  conditional: { type: 'text',     label: 'Conditional', show_if: { enabled: true } },
};

describe('DynamicFieldRenderer', () => {
  it('renders a text field', () => {
    render(<DynamicFieldRenderer fields={{ title: fields.title }} content={{ title: 'hello' }} onChange={() => {}} />);
    expect(screen.getByLabelText('Title')).toHaveValue('hello');
  });

  it('calls onChange with field key and new value', async () => {
    const onChange = vi.fn();
    render(<DynamicFieldRenderer fields={{ title: fields.title }} content={{ title: '' }} onChange={onChange} />);
    await userEvent.type(screen.getByLabelText('Title'), 'x');
    expect(onChange).toHaveBeenCalledWith('title', expect.any(String));
  });

  it('hides conditional field when show_if condition is false', () => {
    render(
      <DynamicFieldRenderer
        fields={{ enabled: fields.enabled, conditional: fields.conditional }}
        content={{ enabled: false }}
        onChange={() => {}}
      />
    );
    expect(screen.queryByLabelText('Conditional')).not.toBeInTheDocument();
  });

  it('shows conditional field when show_if condition is true', () => {
    render(
      <DynamicFieldRenderer
        fields={{ enabled: fields.enabled, conditional: fields.conditional }}
        content={{ enabled: true, conditional: '' }}
        onChange={() => {}}
      />
    );
    expect(screen.getByLabelText('Conditional')).toBeInTheDocument();
  });
});
