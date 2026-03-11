import { describe, it, expect, vi } from 'vitest';
import { renderHook } from '@testing-library/react';
import { useGSAP } from '../hooks/useGSAP';

describe('useGSAP', () => {
  it('returns a ref object', () => {
    const { result } = renderHook(() => useGSAP(() => {}, []));
    expect(result.current).toHaveProperty('current');
  });

  it('does not throw when element is null', () => {
    expect(() => renderHook(() => useGSAP(() => {}, []))).not.toThrow();
  });

  it('does not call callback when prefers-reduced-motion is set', () => {
    vi.stubGlobal('matchMedia', (q: string) => ({
      matches: q.includes('reduce'),
      addEventListener: vi.fn(),
      removeEventListener: vi.fn(),
    }));
    const cb = vi.fn();
    renderHook(() => useGSAP(cb, []));
    expect(cb).not.toHaveBeenCalled();
    vi.unstubAllGlobals();
  });
});
