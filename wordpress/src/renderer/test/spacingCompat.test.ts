import { describe, it, expect } from 'vitest';
import { normaliseSpacing } from '../utils/spacingCompat';

describe('normaliseSpacing', () => {
  it('maps legacy compact to tight', () => {
    expect(normaliseSpacing('compact')).toBe('tight');
  });
  it('passes through comfortable unchanged', () => {
    expect(normaliseSpacing('comfortable')).toBe('comfortable');
  });
  it('passes through spacious unchanged', () => {
    expect(normaliseSpacing('spacious')).toBe('spacious');
  });
  it('passes through tight unchanged', () => {
    expect(normaliseSpacing('tight')).toBe('tight');
  });
  it('returns comfortable for unknown values', () => {
    expect(normaliseSpacing('')).toBe('comfortable');
    expect(normaliseSpacing(undefined)).toBe('comfortable');
  });
});
