import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import Palette from '../components/Palette';

describe('Palette', () => {
  it('renders all 10 standard scene types', () => {
    render(<Palette onAddScene={() => {}} onAddChapter={() => {}} />);
    expect(screen.getByText('Hero')).toBeInTheDocument();
    expect(screen.getByText('Rich Text')).toBeInTheDocument();
    expect(screen.getByText('Gallery')).toBeInTheDocument();
  });

  it('renders all 12 immersive chapter types', () => {
    render(<Palette onAddScene={() => {}} onAddChapter={() => {}} />);
    expect(screen.getByText('Threshold Intro')).toBeInTheDocument();
    expect(screen.getByText('Parallax Stage')).toBeInTheDocument();
  });

  it('shows Standard and Immersive group labels', () => {
    render(<Palette onAddScene={() => {}} onAddChapter={() => {}} />);
    expect(screen.getByText('Standard Scenes')).toBeInTheDocument();
    expect(screen.getByText('Immersive Chapters')).toBeInTheDocument();
  });
});
