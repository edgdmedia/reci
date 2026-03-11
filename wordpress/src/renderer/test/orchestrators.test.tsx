import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import StandardMode from '../components/StandardMode';
import ImmersiveMode from '../components/ImmersiveMode';
import type { Scene, Chapter } from '../../types/blueprint';

describe('StandardMode', () => {
  it('renders a hero scene without throwing', () => {
    const scenes: Scene[] = [{ id: 's1', type: 'hero', title: 'Hello Hero' }];
    render(<StandardMode scenes={scenes} />);
    expect(screen.getByText('Hello Hero')).toBeInTheDocument();
  });

  it('renders nothing for unknown scene type', () => {
    const scenes = [{ id: 's2', type: 'unknown_type' as Scene['type'] }];
    const { container } = render(<StandardMode scenes={scenes} />);
    expect(container.firstChild).toBeEmptyDOMElement();
  });
});

describe('ImmersiveMode', () => {
  it('renders the first active chapter', () => {
    const chapters: Chapter[] = [
      {
        id: 'c1',
        type: 'threshold_intro',
        state: { initial: 'active', completion: { trigger: 'button' } },
        content: { title: 'Welcome', button_label: 'Begin' },
      },
      {
        id: 'c2',
        type: 'content_stage',
        state: { initial: 'locked' },
        content: { title: 'Locked Chapter' },
      },
    ];
    render(<ImmersiveMode chapters={chapters} postId={1} />);
    expect(screen.getByText('Welcome')).toBeInTheDocument();
    expect(screen.queryByText('Locked Chapter')).not.toBeInTheDocument();
  });
});
