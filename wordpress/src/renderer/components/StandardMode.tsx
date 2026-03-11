import type { Scene } from '../../../types/blueprint';
import HeroScene from './scenes/HeroScene';
import RichTextScene from './scenes/RichTextScene';
import QuoteScene from './scenes/QuoteScene';
import GalleryScene from './scenes/GalleryScene';
import TimelineScene from './scenes/TimelineScene';
import HotspotScene from './scenes/HotspotScene';
import ComparePanelsScene from './scenes/ComparePanelsScene';
import MediaEmbedScene from './scenes/MediaEmbedScene';
import DocumentsScene from './scenes/DocumentsScene';
import PromptListScene from './scenes/PromptListScene';

const SCENE_MAP = {
  hero:           HeroScene,
  rich_text:      RichTextScene,
  quote:          QuoteScene,
  gallery:        GalleryScene,
  timeline:       TimelineScene,
  hotspots:       HotspotScene,
  compare_panels: ComparePanelsScene,
  media_embed:    MediaEmbedScene,
  documents:      DocumentsScene,
  prompt_list:    PromptListScene,
} as const;

interface Props { scenes: Scene[] }

export default function StandardMode({ scenes }: Props) {
  return (
    <div className="reci-standard-mode">
      {scenes.map((scene) => {
        const Component = SCENE_MAP[scene.type as keyof typeof SCENE_MAP];
        if (!Component) return null;
        return (
          <div
            key={scene.id}
            id={scene.id}
            style={
              scene.backgroundColor
                ? ({ '--reci-bg': scene.backgroundColor } as React.CSSProperties)
                : undefined
            }
          >
            <Component scene={scene} />
          </div>
        );
      })}
    </div>
  );
}
