import type { ChapterProps } from '../../../../types/blueprint';

export default function ThresholdIntroChapter({ chapter, status, onComplete }: ChapterProps) {
  if (status === 'locked') return null;
  const { content } = chapter;
  const bg = content?.background_image_url;
  return (
    <div
      className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden text-center"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      {bg && (
        <div
          className="absolute inset-0 bg-cover bg-center opacity-30"
          style={{ backgroundImage: `url(${bg})` }}
          aria-hidden
        />
      )}
      <div className="relative z-10 max-w-3xl px-8">
        {content?.title && (
          <h1
            className="mb-6 text-5xl font-bold leading-tight lg:text-7xl"
            style={{ fontFamily: 'var(--reci-heading-font)' }}
          >
            {content.title}
          </h1>
        )}
        {content?.subtitle && (
          <p className="mb-12 text-lg opacity-70 lg:text-xl">{content.subtitle}</p>
        )}
        <button
          onClick={onComplete}
          className="rounded-full px-10 py-4 text-sm font-semibold uppercase tracking-widest transition hover:opacity-80"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          {content?.button_label ?? 'Begin'}
        </button>
      </div>
    </div>
  );
}
