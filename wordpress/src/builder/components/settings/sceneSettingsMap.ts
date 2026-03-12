import type { ComponentType } from 'react';
import HeroSettings from './scenes/HeroSettings';
import RichTextSettings from './scenes/RichTextSettings';
import QuoteSettings from './scenes/QuoteSettings';
import GallerySettings from './scenes/GallerySettings';
import TimelineSettings from './scenes/TimelineSettings';
import DocumentsSettings from './scenes/DocumentsSettings';
import PromptListSettings from './scenes/PromptListSettings';
import ComparePanelsSettings from './scenes/ComparePanelsSettings';
import MediaEmbedSettings from './scenes/MediaEmbedSettings';
import HotspotsSettings from './scenes/HotspotsSettings';

export const SCENE_SETTINGS_MAP: Record<string, ComponentType<any>> = {
  hero: HeroSettings,
  rich_text: RichTextSettings,
  quote: QuoteSettings,
  gallery: GallerySettings,
  timeline: TimelineSettings,
  documents: DocumentsSettings,
  prompt_list: PromptListSettings,
  compare_panels: ComparePanelsSettings,
  media_embed: MediaEmbedSettings,
  hotspots: HotspotsSettings,
};
