import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function TimelineScene({ scene }: Props) {
  const items = scene.items ?? [];
  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-3xl px-6">
        {scene.title && (
          <h2 className="mb-12 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        <ol className="relative border-l-2" style={{ borderColor: 'var(--reci-accent)' }}>
          {items.map((item, i) => (
            <li key={i} className="mb-10 ml-8">
              <span
                className="absolute -left-[9px] flex h-4 w-4 rounded-full"
                style={{ background: 'var(--reci-accent)' }}
                aria-hidden
              />
              {item.label && (
                <time className="mb-1 block text-xs font-semibold uppercase tracking-widest opacity-60">
                  {item.label}
                </time>
              )}
              {item.title && (
                <h3 className="mb-2 text-lg font-semibold">{item.title}</h3>
              )}
              {item.content && (
                <p className="leading-relaxed opacity-80">{item.content}</p>
              )}
            </li>
          ))}
        </ol>
      </div>
    </section>
  );
}
