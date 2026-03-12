import type { ComponentType } from 'react';
import ThresholdIntroSettings from './chapters/ThresholdIntroSettings';
import HotspotStageSettings from './chapters/HotspotStageSettings';
import ProgressiveTextSettings from './chapters/ProgressiveTextSettings';
import ThresholdMessageSettings from './chapters/ThresholdMessageSettings';
import HorizontalPanelsSettings from './chapters/HorizontalPanelsSettings';
import ReflectionPromptSettings from './chapters/ReflectionPromptSettings';
import ContentStageSettings from './chapters/ContentStageSettings';
import StepSequenceSettings from './chapters/StepSequenceSettings';
import DataCardsSettings from './chapters/DataCardsSettings';
import DragRevealSettings from './chapters/DragRevealSettings';
import WordShiftSettings from './chapters/WordShiftSettings';
import ParallaxStageSettings from './chapters/ParallaxStageSettings';

export const CHAPTER_SETTINGS_MAP: Record<string, ComponentType<any>> = {
  threshold_intro: ThresholdIntroSettings,
  hotspot_stage: HotspotStageSettings,
  progressive_text: ProgressiveTextSettings,
  threshold_message: ThresholdMessageSettings,
  horizontal_panels: HorizontalPanelsSettings,
  reflection_prompt: ReflectionPromptSettings,
  content_stage: ContentStageSettings,
  step_sequence: StepSequenceSettings,
  data_cards: DataCardsSettings,
  drag_reveal: DragRevealSettings,
  word_shift: WordShiftSettings,
  parallax_stage: ParallaxStageSettings,
};
