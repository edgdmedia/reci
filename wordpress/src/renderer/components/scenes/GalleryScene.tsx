import { useState } from 'react';
import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function GalleryScene({ scene }: Props) {
  const [lightbox, setLightbox] = useState<number | null>(null);
  const items = scene.items ?? [];

  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-6xl px-6">
        {scene.title && (
          <h2 className="mb-10 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        <div className="grid grid-cols-2 gap-3 md:grid-cols-3">
          {items.map((item, i) => (
            <button
              key={i}
              className="group overflow-hidden rounded focus:outline-none"
              onClick={() => setLightbox(i)}
              aria-label={`View ${item.label ?? `image ${i + 1}`}`}
            >
              <img
                src={item.image_url}
                alt={item.label ?? ''}
                className="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-105"
              />
            </button>
          ))}
        </div>
      </div>
      {lightbox !== null && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
          role="dialog"
          aria-modal
          onClick={() => setLightbox(null)}
        >
          <img
            src={items[lightbox]?.image_url}
            alt={items[lightbox]?.label ?? ''}
            className="max-h-[90vh] max-w-[90vw] rounded object-contain"
          />
          <button
            className="absolute right-6 top-6 text-white opacity-70 hover:opacity-100"
            onClick={() => setLightbox(null)}
            aria-label="Close lightbox"
          >✕</button>
        </div>
      )}
    </section>
  );
}
