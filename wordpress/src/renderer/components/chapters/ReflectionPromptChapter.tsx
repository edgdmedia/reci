import { useState } from 'react';
import type { ChapterProps } from '../../../../types/blueprint';

export default function ReflectionPromptChapter({ chapter, status, onComplete, postId }: ChapterProps) {
  const storageKey = `reci_prompt_${postId}_${chapter.id}`;
  const [value, setValue] = useState(() => localStorage.getItem(storageKey) ?? '');
  if (status === 'locked') return null;
  const { content } = chapter;

  function handleSubmit() {
    localStorage.setItem(storageKey, value);
    onComplete();
  }

  return (
    <div
      className="flex min-h-screen flex-col items-center justify-center px-8"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="w-full max-w-2xl">
        {content?.title && (
          <h2 className="mb-4 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {content.title}
          </h2>
        )}
        {content?.subtitle && <p className="mb-8 opacity-70">{content.subtitle}</p>}
        <textarea
          rows={6}
          value={value}
          onChange={(e) => setValue(e.target.value)}
          placeholder={content?.placeholder ?? 'Write your reflection here…'}
          className="w-full rounded-xl p-4 text-base leading-relaxed outline-none"
          style={{
            background: 'color-mix(in srgb, var(--reci-bg) 80%, var(--reci-text))',
            color: 'var(--reci-text)',
            border: '1px solid color-mix(in srgb, var(--reci-text) 30%, transparent)',
          }}
        />
        <button
          onClick={handleSubmit}
          disabled={!value.trim()}
          className="mt-6 rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest disabled:opacity-40"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          {content?.button_label ?? 'Submit'}
        </button>
      </div>
    </div>
  );
}
