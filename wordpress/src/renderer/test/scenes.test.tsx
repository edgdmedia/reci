import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import type { Scene } from '../../types/blueprint';

const base: Scene = {
  id: 'test-scene',
  type: 'rich_text',
  title: 'Test Title',
  content: 'Test content body',
};

describe('HeroScene', () => {
  it('renders title', async () => {
    const { default: HeroScene } = await import('../components/scenes/HeroScene');
    render(<HeroScene scene={{ ...base, type: 'hero' }} />);
    expect(screen.getByText('Test Title')).toBeInTheDocument();
  });
});

describe('RichTextScene', () => {
  it('renders content', async () => {
    const { default: RichTextScene } = await import('../components/scenes/RichTextScene');
    render(<RichTextScene scene={base} />);
    expect(screen.getByText('Test content body')).toBeInTheDocument();
  });
});

describe('QuoteScene', () => {
  it('renders quote text and speaker', async () => {
    const { default: QuoteScene } = await import('../components/scenes/QuoteScene');
    render(<QuoteScene scene={{ ...base, type: 'quote', quote: 'A wise word', speaker: 'Jane Doe' }} />);
    expect(screen.getByText('A wise word')).toBeInTheDocument();
    expect(screen.getByText(/Jane Doe/)).toBeInTheDocument();
  });
});

describe('GalleryScene', () => {
  it('renders items', async () => {
    const { default: GalleryScene } = await import('../components/scenes/GalleryScene');
    render(<GalleryScene scene={{ ...base, type: 'gallery', items: [{ image_url: 'img.jpg', label: 'Photo 1' }] }} />);
    expect(screen.getByAltText('Photo 1')).toBeInTheDocument();
  });
});

describe('TimelineScene', () => {
  it('renders timeline items', async () => {
    const { default: TimelineScene } = await import('../components/scenes/TimelineScene');
    render(<TimelineScene scene={{ ...base, type: 'timeline', items: [{ label: '1865', title: 'Emancipation', content: 'The end of slavery' }] }} />);
    expect(screen.getByText('1865')).toBeInTheDocument();
  });
});

describe('HotspotScene', () => {
  it('renders image and hotspot buttons', async () => {
    const { default: HotspotScene } = await import('../components/scenes/HotspotScene');
    render(<HotspotScene scene={{ ...base, type: 'hotspots', background_image_url: 'map.jpg', items: [{ label: 'Point A', x: 50, y: 50, content: 'Detail text' }] }} />);
    expect(screen.getByRole('button', { name: /Point A/i })).toBeInTheDocument();
  });
});

describe('ComparePanelsScene', () => {
  it('renders two panels', async () => {
    const { default: ComparePanelsScene } = await import('../components/scenes/ComparePanelsScene');
    render(<ComparePanelsScene scene={{ ...base, type: 'compare_panels', items: [{ label: 'Before', content: 'Then' }, { label: 'After', content: 'Now' }] }} />);
    expect(screen.getByText('Before')).toBeInTheDocument();
    expect(screen.getByText('After')).toBeInTheDocument();
  });
});

describe('MediaEmbedScene', () => {
  it('renders video element', async () => {
    const { default: MediaEmbedScene } = await import('../components/scenes/MediaEmbedScene');
    const { container } = render(<MediaEmbedScene scene={{ ...base, type: 'media_embed', video_url: 'https://example.com/video.mp4' }} />);
    expect(container.querySelector('video, iframe')).toBeInTheDocument();
  });
});

describe('DocumentsScene', () => {
  it('renders document links', async () => {
    const { default: DocumentsScene } = await import('../components/scenes/DocumentsScene');
    render(<DocumentsScene scene={{ ...base, type: 'documents', items: [{ label: 'Report', url: 'report.pdf' }] }} />);
    expect(screen.getByText('Report')).toBeInTheDocument();
  });
});

describe('PromptListScene', () => {
  it('renders prompt items', async () => {
    const { default: PromptListScene } = await import('../components/scenes/PromptListScene');
    render(<PromptListScene scene={{ ...base, type: 'prompt_list', items: [{ content: 'What do you think?' }] }} />);
    expect(screen.getByText('What do you think?')).toBeInTheDocument();
  });
});
