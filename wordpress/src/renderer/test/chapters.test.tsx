import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import type { Chapter } from '../../types/blueprint';

const base: Chapter = {
  id: 'ch1',
  type: 'content_stage',
  content: { title: 'Chapter Title', content: 'Chapter body text', button_label: 'Continue' },
  state: { completion: { trigger: 'button' } },
};

const onComplete = vi.fn();

describe('ThresholdIntroChapter', () => {
  it('renders title and continue button', async () => {
    const { default: C } = await import('../components/chapters/ThresholdIntroChapter');
    render(<C chapter={{ ...base, type: 'threshold_intro' }} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Chapter Title')).toBeInTheDocument();
    expect(screen.getByRole('button')).toBeInTheDocument();
  });
});

describe('ContentStageChapter', () => {
  it('renders content and continue button', async () => {
    const { default: C } = await import('../components/chapters/ContentStageChapter');
    render(<C chapter={base} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Chapter body text')).toBeInTheDocument();
  });
});

describe('ThresholdMessageChapter', () => {
  it('renders message and button', async () => {
    const { default: C } = await import('../components/chapters/ThresholdMessageChapter');
    render(<C chapter={{ ...base, type: 'threshold_message' }} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Chapter Title')).toBeInTheDocument();
  });
});

describe('HotspotStageChapter', () => {
  it('renders hotspot buttons', async () => {
    const { default: C } = await import('../components/chapters/HotspotStageChapter');
    const ch: Chapter = { ...base, type: 'hotspot_stage', content: { ...base.content, items: [{ label: 'Spot A', x: 30, y: 40, content: 'Detail' }] } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByRole('button', { name: /Spot A/i })).toBeInTheDocument();
  });
});

describe('ProgressiveTextChapter', () => {
  it('renders reveal button', async () => {
    const { default: C } = await import('../components/chapters/ProgressiveTextChapter');
    const ch: Chapter = { ...base, type: 'progressive_text', content: { items: [{ content: 'Para 1' }, { content: 'Para 2' }], button_label: 'Reveal' } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByRole('button', { name: /Reveal/i })).toBeInTheDocument();
  });
});

describe('HorizontalPanelsChapter', () => {
  it('renders panels', async () => {
    const { default: C } = await import('../components/chapters/HorizontalPanelsChapter');
    const ch: Chapter = { ...base, type: 'horizontal_panels', content: { items: [{ title: 'Panel 1', content: 'Pane body' }] } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Panel 1')).toBeInTheDocument();
  });
});

describe('ReflectionPromptChapter', () => {
  it('renders textarea and submit button', async () => {
    const { default: C } = await import('../components/chapters/ReflectionPromptChapter');
    render(<C chapter={{ ...base, type: 'reflection_prompt' }} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByRole('textbox')).toBeInTheDocument();
  });
});

describe('StepSequenceChapter', () => {
  it('renders steps', async () => {
    const { default: C } = await import('../components/chapters/StepSequenceChapter');
    const ch: Chapter = { ...base, type: 'step_sequence', content: { items: [{ label: 'Step 1', title: 'Begin', content: 'Do this' }] } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Begin')).toBeInTheDocument();
  });
});

describe('DataCardsChapter', () => {
  it('renders cards', async () => {
    const { default: C } = await import('../components/chapters/DataCardsChapter');
    const ch: Chapter = { ...base, type: 'data_cards', content: { items: [{ label: 'Card A', stat: '42', unit: '%' }] } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('42')).toBeInTheDocument();
  });
});

describe('DragRevealChapter', () => {
  it('renders drag instruction', async () => {
    const { default: C } = await import('../components/chapters/DragRevealChapter');
    const ch: Chapter = { ...base, type: 'drag_reveal', content: { placeholder: 'Drag to reveal', content: 'Hidden text' } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Drag to reveal')).toBeInTheDocument();
  });
});

describe('WordShiftChapter', () => {
  it('renders body text', async () => {
    const { default: C } = await import('../components/chapters/WordShiftChapter');
    const ch: Chapter = { ...base, type: 'word_shift', content: { content: 'Hover over {{biased|aware}} language' } };
    render(<C chapter={ch} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText(/biased|aware/)).toBeInTheDocument();
  });
});

describe('ParallaxStageChapter', () => {
  it('renders title and button', async () => {
    const { default: C } = await import('../components/chapters/ParallaxStageChapter');
    render(<C chapter={{ ...base, type: 'parallax_stage' }} status="active" onComplete={onComplete} postId={1} />);
    expect(screen.getByText('Chapter Title')).toBeInTheDocument();
  });
});
