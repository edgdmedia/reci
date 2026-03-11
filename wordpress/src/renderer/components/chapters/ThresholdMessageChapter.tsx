import type { ChapterProps } from '../../../../types/blueprint';

export default function ThresholdMessageChapter({ chapter, status, onComplete }: ChapterProps) {
  if (status === 'locked') return null;
  const { content } = chapter;
  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center px-8 text-center"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="max-w-2xl">
        {content?.title && (
          <h2 className="mb-6 text-4xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {content.title}
          </h2>
        )}
        {content?.content && (
          <p className="mb-10 text-lg leading-relaxed opacity-80">{content.content}</p>
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
