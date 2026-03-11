import { useState } from 'react';
import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function HotspotScene({ scene }: Props) {
  const [active, setActive] = useState<number | null>(null);
  const items = scene.items ?? [];

  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-5xl px-6">
        {scene.title && (
          <h2 className="mb-8 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        <div className="relative overflow-hidden rounded-xl">
          <img
            src={scene.background_image_url}
            alt={scene.image_alt ?? scene.title ?? ''}
            className="w-full object-cover"
          />
          {items.map((item, i) => (
            <button
              key={i}
              className="absolute flex h-8 w-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full text-sm font-bold ring-2 ring-white transition-transform hover:scale-110"
              style={{
                left: `${item.x ?? 50}%`,
                top: `${item.y ?? 50}%`,
                background: 'var(--reci-accent)',
                color: 'var(--reci-bg)',
              }}
              onClick={() => setActive(active === i ? null : i)}
              aria-label={item.label ?? `Hotspot ${i + 1}`}
              aria-expanded={active === i}
            >
              {i + 1}
            </button>
          ))}
        </div>
        {active !== null && items[active] && (
          <div
            className="mt-4 rounded-xl border p-6"
            style={{ borderColor: 'var(--reci-accent)' }}
          >
            {items[active].label && (
              <h3 className="mb-2 font-semibold">{items[active].label}</h3>
            )}
            {items[active].content && (
              <p className="leading-relaxed opacity-85">{items[active].content}</p>
            )}
          </div>
        )}
      </div>
    </section>
  );
}
