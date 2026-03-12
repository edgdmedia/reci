import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import ReflectionSettings from '../components/ReflectionSettings';

describe('ReflectionSettings', () => {
  const defaults = {
    mode: 'standard' as const,
    appearance: {},
    onUpdateMode: vi.fn(),
    onUpdateAppearance: vi.fn(),
  };

  it('renders mode selector', () => {
    render(<ReflectionSettings {...defaults} />);
    expect(screen.getByLabelText(/mode/i)).toBeInTheDocument();
  });

  it('renders theme selector', () => {
    render(<ReflectionSettings {...defaults} />);
    expect(screen.getByLabelText(/theme/i)).toBeInTheDocument();
  });

  it('calls onUpdateMode when mode changes', () => {
    const onUpdateMode = vi.fn();
    render(<ReflectionSettings {...defaults} onUpdateMode={onUpdateMode} />);
    fireEvent.change(screen.getByLabelText(/mode/i), { target: { value: 'immersive' } });
    expect(onUpdateMode).toHaveBeenCalledWith('immersive');
  });

  it('calls onUpdateAppearance when spacing changes', () => {
    const onUpdateAppearance = vi.fn();
    render(<ReflectionSettings {...defaults} onUpdateAppearance={onUpdateAppearance} />);
    fireEvent.change(screen.getByLabelText(/spacing/i), { target: { value: 'tight' } });
    expect(onUpdateAppearance).toHaveBeenCalledWith({ spacing: 'tight' });
  });

  it('renders heading font field', () => {
    render(<ReflectionSettings {...defaults} />);
    expect(screen.getByLabelText(/heading font/i)).toBeInTheDocument();
  });
});
