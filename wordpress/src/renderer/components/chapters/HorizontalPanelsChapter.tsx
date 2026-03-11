import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function HorizontalPanelsChapter({ chapter, status, onComplete }: ChapterProps) {
  const [index, setIndex] = useState(0);
  if (status === 'locked') return null;
  const { content } = chapter;
  const items = content?.items ?? [];
  const isLast = index >= items.length - 1;

  return (
    <div
      className="flex min-h-screen flex-col overflow-hidden"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="flex flex-1 overflow-hidden">
        {items.map((item, i) => (
          <div
            key={i}
            className="flex flex-shrink-0 flex-col items-center justify-center p-12 transition-all duration-500"
            style={{
              width: '100%',
              transform: `translateX(-${index * 100}%)`,
              minHeight: '100vh',
            }}
          >
            {item.image_url && (
              <img
                src={item.image_url}
                alt={item.title ?? ''}
                className="mb-8 max-h-64 rounded-xl object-cover"
              />
            )}
            {item.title && (
              <h2
                className="mb-4 text-center text-3xl font-bold"
                style={{ fontFamily: 'var(--reci-heading-font)' }}
              >
                {item.title}
              </h2>
            )}
            {item.content && (
              <p className="max-w-xl text-center leading-relaxed opacity-80">{item.content}</p>
            )}
          </div>
        ))}
      </div>
      <div className="flex items-center justify-between px-12 pb-10">
        <button
          onClick={() => setIndex((i) => Math.max(0, i - 1))}
          disabled={index === 0}
          className="rounded-full px-6 py-2 text-sm disabled:opacity-30"
          style={{ border: '1px solid var(--reci-accent)', color: 'var(--reci-accent)' }}
        >
          ← Back
        </button>
        <span className="text-sm opacity-50">
          {index + 1} / {items.length}
        </span>
        {!isLast ? (
          <button
            onClick={() => setIndex((i) => i + 1)}
            className="rounded-full px-6 py-2 text-sm"
            style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
          >
            Next →
          </button>
        ) : (
          <button
            onClick={onComplete}
            className="rounded-full px-6 py-2 text-sm"
            style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
          >
            {content?.button_label ?? 'Continue'}
          </button>
        )}
      </div>
    </div>
  );
}
