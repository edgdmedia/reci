import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function ProgressiveTextChapter({ chapter, status, onComplete }: ChapterProps) {
  const [revealed, setRevealed] = useState(0);
  if (status === 'locked') return null;
  const { content } = chapter;
  const items = content?.items ?? [];
  const isLast = revealed >= items.length - 1;

  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center px-8"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="w-full max-w-3xl">
        {content?.title && (
          <h2 className="mb-10 text-4xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {content.title}
          </h2>
        )}
        <div className="space-y-6">
          {items.slice(0, revealed + 1).map((item, i) => (
            <p key={i} className="text-xl leading-relaxed transition-opacity duration-500">
              {item.content}
            </p>
          ))}
        </div>
        <div className="mt-10">
          {!isLast ? (
            <button
              onClick={() => setRevealed((r) => r + 1)}
              className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
              style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
            >
              {content?.button_label ?? 'Reveal'}
            </button>
          ) : (
            <button
              onClick={onComplete}
              className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
              style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
            >
              Continue
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
