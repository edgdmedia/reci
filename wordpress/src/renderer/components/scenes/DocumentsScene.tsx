import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function DocumentsScene({ scene }: Props) {
  const items = scene.items ?? [];
  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-3xl px-6">
        {scene.title && (
          <h2 className="mb-8 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        <ul className="space-y-4">
          {items.map((item, i) => (
            <li key={i}>
              <a
                href={item.url ?? '#'}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center gap-4 rounded-lg border p-4 transition hover:opacity-80"
                style={{ borderColor: 'color-mix(in srgb, var(--reci-text) 20%, transparent)' }}
              >
                <span className="text-2xl" aria-hidden>📄</span>
                <span>
                  {item.label && <strong className="block">{item.label}</strong>}
                  {item.content && <span className="text-sm opacity-60">{item.content}</span>}
                </span>
              </a>
            </li>
          ))}
        </ul>
      </div>
    </section>
  );
}
