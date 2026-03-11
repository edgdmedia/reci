import { useRef, useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function DragRevealChapter({ chapter, status, onComplete }: ChapterProps) {
  const [revealed, setRevealed] = useState(false);
  const [dragging, setDragging] = useState(false);
  const startX = useRef(0);
  if (status === 'locked') return null;
  const { content } = chapter;

  function handleDragStart(e: React.MouseEvent | React.TouchEvent) {
    setDragging(true);
    startX.current = 'touches' in e ? e.touches[0].clientX : e.clientX;
  }

  function handleDragEnd(e: React.MouseEvent | React.TouchEvent) {
    if (!dragging) return;
    setDragging(false);
    const endX = 'changedTouches' in e ? e.changedTouches[0].clientX : e.clientX;
    if (Math.abs(endX - startX.current) > 80) setRevealed(true);
  }

  return (
    <div
      className="flex min-h-screen select-none flex-col items-center justify-center px-8"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="w-full max-w-2xl">
        {!revealed ? (
          <>
            <p className="mb-8 text-center text-lg opacity-60">
              {content?.placeholder ?? 'Drag to reveal'}
            </p>
            <div
              className="cursor-grab rounded-xl p-10 text-center text-2xl font-medium active:cursor-grabbing"
              style={{ border: '2px dashed var(--reci-accent)' }}
              onMouseDown={handleDragStart}
              onMouseUp={handleDragEnd}
              onTouchStart={handleDragStart}
              onTouchEnd={handleDragEnd}
              role="button"
              aria-label="Drag to reveal"
            >
              ← Drag →
            </div>
          </>
        ) : (
          <>
            {content?.title && (
              <h2
                className="mb-6 text-3xl font-bold"
                style={{ fontFamily: 'var(--reci-heading-font)' }}
              >
                {content.title}
              </h2>
            )}
            {content?.content && (
              <p className="mb-10 leading-relaxed opacity-85">{content.content}</p>
            )}
            <button
              onClick={onComplete}
              className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
              style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
            >
              Continue
            </button>
          </>
        )}
      </div>
    </div>
  );
}
