import { describe, it, expect } from 'vitest';
import { render } from '@testing-library/react';
import { useRef } from 'react';
import { useStyleContract } from '../hooks/useStyleContract';

function TestComponent() {
  const ref = useRef<HTMLDivElement>(null);
  useStyleContract(ref, { backgroundColor: '#abcdef', textColor: '#111111', spacing: 'spacious' });
  return <div ref={ref} data-testid="root" />;
}

describe('useStyleContract', () => {
  it('injects CSS custom properties onto the element', () => {
    const { getByTestId } = render(<TestComponent />);
    const el = getByTestId('root');
    expect(el.style.getPropertyValue('--reci-bg')).toBe('#abcdef');
    expect(el.style.getPropertyValue('--reci-text')).toBe('#111111');
    expect(el.style.getPropertyValue('--reci-spacing-unit')).toBe('2rem');
  });
});
