import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function HotspotStageChapter({ chapter, status, onComplete }: ChapterProps) {
  const [visited, setVisited] = useState<Set<number>>(new Set());
  const [detail, setDetail] = useState<number | null>(null);
  if (status === 'locked') return null;
  const { content, state } = chapter;
  const items = content?.items ?? [];
  const required = state?.completion?.min_required ?? items.length;
  const canComplete = visited.size >= required;

  function visit(i: number) {
    setVisited((prev) => new Set([...prev, i]));
    setDetail(i);
  }

  return (
    <div
      className="relative flex min-h-screen items-center justify-center overflow-hidden"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      {content?.background_image_url && (
        <img
          src={content.background_image_url}
          alt=""
          className="absolute inset-0 h-full w-full object-cover opacity-60"
          aria-hidden
        />
      )}
      <div className="absolute inset-0">
        {items.map((item, i) => (
          <button
            key={i}
            className="absolute flex h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full text-sm font-bold ring-2 ring-white transition-transform hover:scale-110"
            style={{
              left: `${item.x ?? 50}%`,
              top: `${item.y ?? 50}%`,
              background: visited.has(i) ? '#4ade80' : 'var(--reci-accent)',
              color: 'var(--reci-bg)',
            }}
            onClick={() => visit(i)}
            aria-label={item.label ?? `Hotspot ${i + 1}`}
            aria-expanded={detail === i}
          >
            {visited.has(i) ? '✓' : i + 1}
          </button>
        ))}
      </div>
      {detail !== null && items[detail] && (
        <div
          className="absolute bottom-8 left-1/2 z-20 w-full max-w-lg -translate-x-1/2 rounded-xl p-6 shadow-2xl"
          style={{ background: 'var(--reci-bg)' }}
        >
          {items[detail].label && <h3 className="mb-2 font-semibold">{items[detail].label}</h3>}
          {items[detail].content && (
            <p className="leading-relaxed opacity-85">{items[detail].content}</p>
          )}
          <button onClick={() => setDetail(null)} className="mt-4 text-xs opacity-50 hover:opacity-80">
            Close
          </button>
        </div>
      )}
      {canComplete && (
        <button
          onClick={onComplete}
          className="absolute bottom-8 right-8 z-30 rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          {content?.button_label ?? 'Continue'}
        </button>
      )}
    </div>
  );
}
