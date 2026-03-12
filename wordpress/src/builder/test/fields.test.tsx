import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import Field from '../components/fields/Field';
import TextareaField from '../components/fields/TextareaField';
import SelectField from '../components/fields/SelectField';
import RangeField from '../components/fields/RangeField';
import ItemListField from '../components/fields/ItemListField';

describe('Field', () => {
  it('renders label and input', () => {
    render(<Field label="Title" value="hello" onChange={() => {}} />);
    expect(screen.getByLabelText('Title')).toBeInTheDocument();
  });
});

describe('TextareaField', () => {
  it('renders textarea', () => {
    render(<TextareaField label="Content" value="text" onChange={() => {}} />);
    expect(screen.getByRole('textbox')).toBeInTheDocument();
  });
});

describe('SelectField', () => {
  it('renders select with options', () => {
    render(<SelectField label="Mode" value="a" onChange={() => {}} options={[{ value: 'a', label: 'A' }, { value: 'b', label: 'B' }]} />);
    expect(screen.getByRole('combobox')).toBeInTheDocument();
  });
});

describe('RangeField', () => {
  it('renders range input', () => {
    render(<RangeField label="Size" value={18} min={14} max={26} onChange={() => {}} />);
    expect(screen.getByRole('slider')).toBeInTheDocument();
  });
});

describe('ItemListField', () => {
  it('renders add button', () => {
    render(<ItemListField label="Items" items={[]} onChange={() => {}} fields={['content']} />);
    expect(screen.getByRole('button', { name: /add/i })).toBeInTheDocument();
  });
});
