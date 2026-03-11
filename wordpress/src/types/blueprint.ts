// Canonical data contract. Builder writes this; renderer reads it.
// PHP service classes normalise and pass through unchanged.

export interface ReflectionStyle {
  theme?: 'immersive-dark' | 'light' | 'archival' | 'editorial' | 'spotlight';
  backgroundColor?: string;
  textColor?: string;
  accentColor?: string;
  baseFontSize?: number;       // px, 14–26
  headingFont?: string;
  spacing?: 'tight' | 'comfortable' | 'spacious';
  columns?: 'auto' | '1' | '2' | '3' | '4';
  text_width?: 'default' | 'narrow' | 'prose' | 'wide' | 'full';
}

export interface SceneItem {
  label?: string;
  title?: string;
  content?: string;
  image_url?: string;
  url?: string;
  stat?: string;
  unit?: string;
  icon?: string;
  shift?: string;
  x?: number;
  y?: number;
  problem?: string;
  solution?: string;
}

export type SceneType =
  | 'hero' | 'rich_text' | 'quote' | 'gallery' | 'timeline'
  | 'documents' | 'prompt_list' | 'compare_panels' | 'media_embed' | 'hotspots';

export interface Scene extends ReflectionStyle {
  id: string;
  type: SceneType;
  title?: string;
  content?: string;
  quote?: string;
  speaker?: string;
  role?: string;
  badge?: string;
  background_image_url?: string;
  image_alt?: string;
  video_url?: string;
  audio_url?: string;
  items?: SceneItem[];
}

export interface ChapterContent {
  title?: string;
  subtitle?: string;
  content?: string;
  button_label?: string;
  placeholder?: string;
  background_image_url?: string;
  audio_url?: string;
  video_url?: string;
  items?: SceneItem[];
}

export type CompletionTrigger =
  | 'button' | 'all_hotspots' | 'manual_continue'
  | 'last_panel' | 'all_steps' | 'drag_break' | 'submission';

export interface Chapter {
  id: string;
  type:
    | 'threshold_intro' | 'hotspot_stage' | 'progressive_text'
    | 'threshold_message' | 'horizontal_panels' | 'reflection_prompt'
    | 'content_stage' | 'step_sequence' | 'data_cards' | 'drag_reveal'
    | 'word_shift' | 'parallax_stage';
  label?: string;
  title?: string;
  presentation?: ReflectionStyle;
  state?: {
    initial?: 'active' | 'locked';
    completion?: {
      trigger?: CompletionTrigger;
      target?: string;
      min_required?: number;
    };
  };
  content?: ChapterContent;
}

export interface Blueprint {
  mode: 'standard' | 'immersive';
  template?: 'narrative' | 'documentary' | 'testimonial' | 'analytical';
  appearance?: ReflectionStyle;
  scenes?: Scene[];
  chapters?: Chapter[];
}

export interface ChapterProps {
  chapter: Chapter;
  status: 'locked' | 'active' | 'completed';
  onComplete: () => void;
  postId: number;
}
