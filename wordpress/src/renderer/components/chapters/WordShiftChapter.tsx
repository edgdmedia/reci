import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

function parseWordShift(text: string): Array<{ original: string; shift: string | null }> {
  const parts = text.split(/({{[^}]+}})/g);
  return parts.map((part) => {
    const match = part.match(/^{{(.+?)\|(.+?)}}$/);
    if (match) return { original: match[1], shift: match[2] };
    return { original: part, shift: null };
  });
}

export default function WordShiftChapter({ chapter, status, onComplete }: ChapterProps) {
  const [hovered, setHovered] = useState<Set<number>>(new Set());
  if (status === 'locked') return null;
  const { content } = chapter;
  const tokens = parseWordShift(content?.content ?? '');

  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center px-8"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="max-w-3xl">
        {content?.title && (
          <h2
            className="mb-10 text-4xl font-bold"
            style={{ fontFamily: 'var(--reci-heading-font)' }}
          >
            {content.title}
          </h2>
        )}
        <p className="text-xl leading-loose">
          {tokens.map((token, i) =>
            token.shift ? (
              <span
                key={i}
                className="cursor-pointer rounded px-1 transition-colors"
                style={{
                  color: hovered.has(i) ? 'var(--reci-bg)' : 'var(--reci-accent)',
                  background: hovered.has(i) ? 'var(--reci-accent)' : 'transparent',
                  textDecoration: 'underline dotted',
                }}
                onMouseEnter={() => setHovered((s) => new Set([...s, i]))}
                onMouseLeave={() =>
                  setHovered((s) => {
                    const n = new Set(s);
                    n.delete(i);
                    return n;
                  })
                }
                aria-label={`${token.original} — shifted meaning: ${token.shift}`}
              >
                {hovered.has(i) ? token.shift : token.original}
              </span>
            ) : (
              <span key={i}>{token.original}</span>
            )
          )}
        </p>
        <button
          onClick={onComplete}
          className="mt-12 rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          {content?.button_label ?? 'Continue'}
        </button>
      </div>
    </div>
  );
}
