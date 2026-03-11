import type { ChapterProps } from '../../../../types/blueprint';

export default function ContentStageChapter({ chapter, status, onComplete }: ChapterProps) {
  if (status === 'locked') return null;
  const { content } = chapter;
  const bg = content?.background_image_url;
  return (
    <div
      className="relative flex min-h-screen flex-col items-center justify-center"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      {bg && (
        <div
          className="absolute inset-0 bg-cover bg-center opacity-40"
          style={{ backgroundImage: `url(${bg})` }}
          aria-hidden
        />
      )}
      <div className="relative z-10 mx-auto max-w-3xl px-8">
        {content?.title && (
          <h2 className="mb-6 text-4xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {content.title}
          </h2>
        )}
        {content?.content && (
          <div
            className="mb-10 leading-relaxed opacity-85"
            dangerouslySetInnerHTML={{ __html: content.content }}
          />
        )}
        <button
          onClick={onComplete}
          className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          {content?.button_label ?? 'Continue'}
        </button>
      </div>
    </div>
  );
}
