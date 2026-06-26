import { buildRegistry } from './index';
import type { ChapterDefinition, StyleDefinition, StudioStyleComponent } from '@/types/blueprint';

const stubStyle: StudioStyleComponent = () => null;

const testChapter: ChapterDefinition = {
  family: 'test-chapter',
  label: 'Test Chapter',
  elements: { bg: { label: 'Background', colorSlot: 'bg' } },
  fields: { title: { type: 'text', label: 'Title' } },
  defaultContent: { title: '' },
  defaultStyle: 'default',
  styles: { default: stubStyle },
};

const testStyle: StyleDefinition = {
  id: 'test-style',
  label: 'Test Style',
  tokens: {
    bg: '#000', heading: '#fff', body: '#ccc', accent: '#f90',
    primary: '#00f', surface: '#111', surface_text: '#eee', muted: '#888',
    headingFont: 'serif', bodyFont: 'sans-serif',
    baseFontSize: '18px', spacingUnit: '1rem',
  },
};

describe('buildRegistry', () => {
  it('looks up a chapter by family', () => {
    const reg = buildRegistry([testChapter], [testStyle]);
    expect(reg.getChapter('test-chapter')).toBe(testChapter);
  });

  it('looks up a style by id', () => {
    const reg = buildRegistry([testChapter], [testStyle]);
    expect(reg.getStyle('test-style')).toBe(testStyle);
  });

  it('returns undefined for unknown family', () => {
    const reg = buildRegistry([testChapter], [testStyle]);
    expect(reg.getChapter('nonexistent')).toBeUndefined();
  });

  it('serializes correctly', () => {
    const reg = buildRegistry([testChapter], [testStyle]);
    const serial = reg.serialize();
    expect(serial.chapters).toHaveLength(1);
    expect(serial.chapters[0].availableStyles).toEqual(['default']);
    expect(serial.styles).toHaveLength(1);
  });
});
