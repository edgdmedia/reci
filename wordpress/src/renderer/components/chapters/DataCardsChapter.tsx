import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function DataCardsChapter({ chapter, status, onComplete }: ChapterProps) {
  const [flipped, setFlipped] = useState<Set<number>>(new Set());
  if (status === 'locked') return null;
  const { content, state } = chapter;
  const items = content?.items ?? [];
  const required = state?.completion?.min_required ?? 1;

  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center px-8 py-16"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      {content?.title && (
        <h2 className="mb-10 text-4xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
          {content.title}
        </h2>
      )}
      <div className="grid grid-cols-2 gap-6 md:grid-cols-3">
        {items.map((item, i) => (
          <button
            key={i}
            className="aspect-square rounded-xl p-6 text-center transition-transform hover:scale-105"
            style={{
              background: flipped.has(i)
                ? 'var(--reci-accent)'
                : 'color-mix(in srgb, var(--reci-bg) 80%, var(--reci-text))',
              color: flipped.has(i) ? 'var(--reci-bg)' : 'var(--reci-text)',
            }}
            onClick={() => setFlipped((prev) => new Set([...prev, i]))}
          >
            {flipped.has(i) ? (
              <p className="text-sm leading-snug">{item.content}</p>
            ) : (
              <>
                {item.stat && (
                  <p className="text-5xl font-bold">
                    {item.stat}
                    <span className="text-xl">{item.unit}</span>
                  </p>
                )}
                {item.label && <p className="mt-2 text-sm opacity-70">{item.label}</p>}
              </>
            )}
          </button>
        ))}
      </div>
      {flipped.size >= required && (
        <button
          onClick={onComplete}
          className="mt-12 rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          Continue
        </button>
      )}
    </div>
  );
}
