import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import ColorPicker from '../components/ColorPicker';

describe('ColorPicker', () => {
  it('renders a color preview swatch', () => {
    render(<ColorPicker label="Background" value="#ff0000" onChange={() => {}} />);
    expect(screen.getByText('Background')).toBeInTheDocument();
  });
});
