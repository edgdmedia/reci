import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

function isYouTube(url: string): boolean {
  return /youtube\.com|youtu\.be/.test(url);
}

function isVimeo(url: string): boolean {
  return /vimeo\.com/.test(url);
}

export default function MediaEmbedScene({ scene }: Props) {
  const url = scene.video_url ?? scene.audio_url ?? '';
  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-4xl px-6">
        {scene.title && (
          <h2 className="mb-8 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        {scene.video_url && (isYouTube(url) || isVimeo(url)) ? (
          <div className="relative aspect-video overflow-hidden rounded-xl">
            <iframe
              src={url}
              className="absolute inset-0 h-full w-full"
              allow="autoplay; fullscreen"
              allowFullScreen
              title={scene.title ?? 'Video'}
            />
          </div>
        ) : scene.video_url ? (
          <video controls className="w-full rounded-xl" src={scene.video_url}>
            Your browser does not support the video element.
          </video>
        ) : scene.audio_url ? (
          <audio controls className="w-full" src={scene.audio_url}>
            Your browser does not support the audio element.
          </audio>
        ) : null}
        {scene.content && (
          <p className="mt-6 leading-relaxed opacity-80">{scene.content}</p>
        )}
      </div>
    </section>
  );
}
