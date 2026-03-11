import { describe, it, expect } from 'vitest';
import { resolveStyleContract } from '../utils/styleContract';
import type { ReflectionStyle } from '../../types/blueprint';

describe('resolveStyleContract', () => {
  it('returns CSS vars from explicit fields', () => {
    const style: ReflectionStyle = {
      backgroundColor: '#123456',
      textColor: '#ffffff',
      accentColor: '#ff0000',
      baseFontSize: 20,
      headingFont: 'Inter',
      spacing: 'spacious',
    };
    const vars = resolveStyleContract(style);
    expect(vars['--reci-bg']).toBe('#123456');
    expect(vars['--reci-text']).toBe('#ffffff');
    expect(vars['--reci-accent']).toBe('#ff0000');
    expect(vars['--reci-font-base']).toBe('20px');
    expect(vars['--reci-heading-font']).toBe("'Inter', serif");
    expect(vars['--reci-spacing-unit']).toBe('2rem');
  });

  it('falls back to theme token when no explicit colours', () => {
    const vars = resolveStyleContract({ theme: 'light' });
    expect(vars['--reci-bg']).toBe('#ffffff');
    expect(vars['--reci-text']).toBe('#171717');
    expect(vars['--reci-accent']).toBe('#f59e0b');
  });

  it('defaults to immersive-dark when no style given', () => {
    const vars = resolveStyleContract({});
    expect(vars['--reci-bg']).toBe('#0a0a0a');
    expect(vars['--reci-text']).toBe('#f5f0e8');
    expect(vars['--reci-font-base']).toBe('18px');
  });

  it('maps tight spacing to 1rem', () => {
    const vars = resolveStyleContract({ spacing: 'tight' });
    expect(vars['--reci-spacing-unit']).toBe('1rem');
  });

  it('explicit colour overrides theme token', () => {
    const vars = resolveStyleContract({ theme: 'light', backgroundColor: '#aabbcc' });
    expect(vars['--reci-bg']).toBe('#aabbcc');
    expect(vars['--reci-text']).toBe('#171717');
  });
});
