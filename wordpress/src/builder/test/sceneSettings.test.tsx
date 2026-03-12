import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import { SCENE_SETTINGS_MAP } from '../components/settings/sceneSettingsMap';
import type { Scene } from '../../../types/blueprint';

const baseScene: Scene = { id: 's1', type: 'hero', title: '' };

describe('SCENE_SETTINGS_MAP', () => {
  it('has entries for all 10 scene types', () => {
    const expected = [
      'hero', 'rich_text', 'quote', 'gallery', 'timeline',
      'documents', 'prompt_list', 'compare_panels', 'media_embed', 'hotspots',
    ];
    expected.forEach((t) => expect(SCENE_SETTINGS_MAP[t]).toBeDefined());
  });

  it('HeroSettings renders title field', () => {
    const Hero = SCENE_SETTINGS_MAP['hero'];
    render(<Hero scene={baseScene} onChange={vi.fn()} />);
    expect(screen.getByLabelText(/title/i)).toBeInTheDocument();
  });

  it('QuoteSettings renders quote field', () => {
    const Quote = SCENE_SETTINGS_MAP['quote'];
    render(<Quote scene={{ ...baseScene, type: 'quote' }} onChange={vi.fn()} />);
    expect(screen.getByLabelText(/quote/i)).toBeInTheDocument();
  });

  it('MediaEmbedSettings renders video url field', () => {
    const Media = SCENE_SETTINGS_MAP['media_embed'];
    render(<Media scene={{ ...baseScene, type: 'media_embed' }} onChange={vi.fn()} />);
    expect(screen.getByLabelText(/video url/i)).toBeInTheDocument();
  });
});
