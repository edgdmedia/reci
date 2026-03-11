import { useState } from 'react';
import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function ComparePanelsScene({ scene }: Props) {
  const items = scene.items ?? [];
  const [active, setActive] = useState(0);
  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-5xl px-6">
        {scene.title && (
          <h2 className="mb-8 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        <div className="mb-6 flex gap-2">
          {items.map((item, i) => (
            <button
              key={i}
              className="rounded px-4 py-2 text-sm font-medium transition"
              style={{
                background: active === i ? 'var(--reci-accent)' : 'transparent',
                color: active === i ? 'var(--reci-bg)' : 'var(--reci-text)',
                border: '1px solid var(--reci-accent)',
              }}
              onClick={() => setActive(i)}
            >
              {item.label}
            </button>
          ))}
        </div>
        {items[active] && (
          <div className="rounded-xl p-8" style={{ background: 'color-mix(in srgb, var(--reci-bg) 85%, var(--reci-text))' }}>
            {items[active].image_url && (
              <img src={items[active].image_url} alt={items[active].label ?? ''} className="mb-6 w-full rounded-lg object-cover" />
            )}
            {items[active].content && (
              <p className="leading-relaxed">{items[active].content}</p>
            )}
          </div>
        )}
      </div>
    </section>
  );
}
