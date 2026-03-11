import { useLayoutEffect } from 'react';
import type { RefObject } from 'react';
import type { ReflectionStyle } from '../../types/blueprint';
import { resolveStyleContract } from '../utils/styleContract';

/**
 * Injects CSS custom properties derived from the reflection's style contract
 * onto the given element before first paint.
 */
export function useStyleContract(
  ref: RefObject<HTMLElement>,
  style: ReflectionStyle,
): void {
  useLayoutEffect(() => {
    const el = ref.current;
    if (!el) return;
    const vars = resolveStyleContract(style);
    Object.entries(vars).forEach(([prop, value]) => {
      el.style.setProperty(prop, value);
    });
  }, [ref, style]);
}
