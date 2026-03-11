import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function StepSequenceChapter({ chapter, status, onComplete }: ChapterProps) {
  const [step, setStep] = useState(0);
  if (status === 'locked') return null;
  const { content } = chapter;
  const items = content?.items ?? [];
  const isLast = step >= items.length - 1;

  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center px-8"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="w-full max-w-2xl">
        <div className="mb-10 flex gap-3">
          {items.map((_, i) => (
            <div
              key={i}
              className="h-1 flex-1 rounded-full transition-all duration-300"
              style={{
                background:
                  i <= step
                    ? 'var(--reci-accent)'
                    : 'color-mix(in srgb, var(--reci-text) 20%, transparent)',
              }}
            />
          ))}
        </div>
        {items[step] && (
          <>
            {items[step].label && (
              <p className="mb-2 text-xs font-semibold uppercase tracking-widest opacity-50">
                {items[step].label}
              </p>
            )}
            {items[step].title && (
              <h2
                className="mb-6 text-3xl font-bold"
                style={{ fontFamily: 'var(--reci-heading-font)' }}
              >
                {items[step].title}
              </h2>
            )}
            {items[step].content && (
              <p className="leading-relaxed opacity-80">{items[step].content}</p>
            )}
          </>
        )}
        <div className="mt-10">
          {!isLast ? (
            <button
              onClick={() => setStep((s) => s + 1)}
              className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
              style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
            >
              Next Step
            </button>
          ) : (
            <button
              onClick={onComplete}
              className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
              style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
            >
              {content?.button_label ?? 'Continue'}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
