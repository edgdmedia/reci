import { useState, useEffect } from 'react';
import type { Responsive } from '../../types/blueprint';

export type Breakpoint = 'mobile' | 'tablet' | 'desktop';

function getBreakpoint(): Breakpoint {
  if (typeof window === 'undefined') return 'desktop';
  if (window.innerWidth < 768) return 'mobile';
  if (window.innerWidth < 1024) return 'tablet';
  return 'desktop';
}

export function useBreakpoint(): Breakpoint {
  const [bp, setBp] = useState<Breakpoint>(getBreakpoint);
  useEffect(() => {
    const handler = () => setBp(getBreakpoint());
    window.addEventListener('resize', handler, { passive: true });
    return () => window.removeEventListener('resize', handler);
  }, []);
  return bp;
}

/** Resolve a Responsive<T> to the best value for the current breakpoint. Falls back up the chain: mobile → tablet → desktop. */
export function resolveResponsive<T>(val: Responsive<T> | undefined, bp: Breakpoint): T | undefined {
  if (!val) return undefined;
  if (bp === 'mobile') return val.mobile ?? val.tablet ?? val.desktop;
  if (bp === 'tablet') return val.tablet ?? val.desktop;
  return val.desktop;
}
