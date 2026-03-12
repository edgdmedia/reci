import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import { CHAPTER_SETTINGS_MAP } from '../components/settings/chapterSettingsMap';
import type { Chapter } from '../../../types/blueprint';

const baseChapter: Chapter = { id: 'c1', type: 'threshold_intro' };

describe('CHAPTER_SETTINGS_MAP', () => {
  it('has entries for all 12 chapter types', () => {
    const expected = [
      'threshold_intro', 'hotspot_stage', 'progressive_text',
      'threshold_message', 'horizontal_panels', 'reflection_prompt',
      'content_stage', 'step_sequence', 'data_cards',
      'drag_reveal', 'word_shift', 'parallax_stage',
    ];
    expected.forEach((t) => expect(CHAPTER_SETTINGS_MAP[t]).toBeDefined());
  });

  it('ThresholdIntroSettings renders title and button label', () => {
    const Comp = CHAPTER_SETTINGS_MAP['threshold_intro'];
    render(<Comp chapter={baseChapter} onChange={vi.fn()} />);
    expect(screen.getByLabelText('Title Text')).toBeInTheDocument();
    expect(screen.getByLabelText('Button Label')).toBeInTheDocument();
  });

  it('ReflectionPromptSettings renders placeholder field', () => {
    const Comp = CHAPTER_SETTINGS_MAP['reflection_prompt'];
    render(<Comp chapter={{ ...baseChapter, type: 'reflection_prompt' }} onChange={vi.fn()} />);
    expect(screen.getByLabelText(/placeholder/i)).toBeInTheDocument();
  });

  it('HotspotStageSettings renders background image url', () => {
    const Comp = CHAPTER_SETTINGS_MAP['hotspot_stage'];
    render(<Comp chapter={{ ...baseChapter, type: 'hotspot_stage' }} onChange={vi.fn()} />);
    expect(screen.getByLabelText(/background image url/i)).toBeInTheDocument();
  });
});
