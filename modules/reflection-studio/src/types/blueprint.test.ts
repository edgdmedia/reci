import type { ChapterInstance, StyleTokens } from './blueprint';

// Compile-time shape tests — if these assignments type-check, the interfaces are correct.
const chapter: ChapterInstance = {
  id: 'abc',
  family: 'hotspot-stage',
  style: 'inherit',
  colorMode: 'inherit',
  elementColors: {},
  content: {},
};

const tokens: StyleTokens = {
  bg: '#000', heading: '#fff', body: '#ccc', accent: '#f90',
  primary: '#00f', surface: '#111', surface_text: '#eee', muted: '#888',
  headingFont: "'EB Garamond', serif",
  bodyFont: "'Inter', sans-serif",
  baseFontSize: '18px',
  spacingUnit: '1.5rem',
};

describe('blueprint types', () => {
  it('chapter has required fields', () => {
    expect(chapter.family).toBe('hotspot-stage');
    expect(chapter.colorMode).toBe('inherit');
  });
  it('StyleTokens has all colour slots', () => {
    const slots: (keyof StyleTokens)[] = ['bg','heading','body','accent','primary','surface','surface_text','muted'];
    slots.forEach(s => expect(tokens[s]).toBeDefined());
  });
});
