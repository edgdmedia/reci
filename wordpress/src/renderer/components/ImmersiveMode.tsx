import { useEffect, useRef } from 'react';
import type { Chapter } from '../../../types/blueprint';
import { useChapterProgression } from '../hooks/useChapterProgression';
import ThresholdIntroChapter from './chapters/ThresholdIntroChapter';
import ContentStageChapter from './chapters/ContentStageChapter';
import ThresholdMessageChapter from './chapters/ThresholdMessageChapter';
import HotspotStageChapter from './chapters/HotspotStageChapter';
import ProgressiveTextChapter from './chapters/ProgressiveTextChapter';
import HorizontalPanelsChapter from './chapters/HorizontalPanelsChapter';
import ReflectionPromptChapter from './chapters/ReflectionPromptChapter';
import StepSequenceChapter from './chapters/StepSequenceChapter';
import DataCardsChapter from './chapters/DataCardsChapter';
import DragRevealChapter from './chapters/DragRevealChapter';
import WordShiftChapter from './chapters/WordShiftChapter';
import ParallaxStageChapter from './chapters/ParallaxStageChapter';

const CHAPTER_MAP = {
  threshold_intro:   ThresholdIntroChapter,
  content_stage:     ContentStageChapter,
  threshold_message: ThresholdMessageChapter,
  hotspot_stage:     HotspotStageChapter,
  progressive_text:  ProgressiveTextChapter,
  horizontal_panels: HorizontalPanelsChapter,
  reflection_prompt: ReflectionPromptChapter,
  step_sequence:     StepSequenceChapter,
  data_cards:        DataCardsChapter,
  drag_reveal:       DragRevealChapter,
  word_shift:        WordShiftChapter,
  parallax_stage:    ParallaxStageChapter,
} as const;

interface Props { chapters: Chapter[]; postId: number }

export default function ImmersiveMode({ chapters, postId }: Props) {
  const { state, completeChapter } = useChapterProgression(chapters);
  const chapterRefs = useRef<Record<string, HTMLDivElement | null>>({});

  // Scroll newly activated chapter into view
  useEffect(() => {
    const activeId = Object.entries(state).find(([, s]) => s === 'active')?.[0];
    if (activeId && chapterRefs.current[activeId]) {
      chapterRefs.current[activeId]?.scrollIntoView({ behavior: 'smooth' });
    }
  }, [state]);

  return (
    <div className="reci-immersive-mode">
      {chapters.map((chapter) => {
        const Component = CHAPTER_MAP[chapter.type as keyof typeof CHAPTER_MAP];
        if (!Component) return null;
        return (
          <div
            key={chapter.id}
            id={chapter.id}
            ref={(el) => {
              chapterRefs.current[chapter.id] = el;
            }}
          >
            <Component
              chapter={chapter}
              status={state[chapter.id] ?? 'locked'}
              onComplete={() => completeChapter(chapter.id)}
              postId={postId}
            />
          </div>
        );
      })}
    </div>
  );
}
