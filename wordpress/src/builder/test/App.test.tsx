import { describe, it, expect, beforeEach } from 'vitest';
import { render, screen } from '@testing-library/react';
import App from '../components/App';
import { useBuilderStore } from '../store/builderStore';
import { initialBlueprint } from '../store/builderStore';

beforeEach(() => {
  useBuilderStore.getState().loadBlueprint({ ...initialBlueprint, scenes: [], chapters: [] });
});

describe('App', () => {
  it('renders Reflection Settings heading', () => {
    render(<App />);
    expect(screen.getByText('Reflection Settings')).toBeInTheDocument();
  });

  it('renders Add Block heading', () => {
    render(<App />);
    expect(screen.getByText('Add Block')).toBeInTheDocument();
  });

  it('renders Scenes heading in standard mode', () => {
    render(<App />);
    expect(screen.getByText('Scenes')).toBeInTheDocument();
  });

  it('renders empty state message when no scenes', () => {
    render(<App />);
    expect(screen.getByText(/no scenes yet/i)).toBeInTheDocument();
  });
});
