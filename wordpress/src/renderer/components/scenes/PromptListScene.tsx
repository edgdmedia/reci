import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function PromptListScene({ scene }: Props) {
  const items = scene.items ?? [];
  return (
    <section className="py-16" style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}>
      <div className="mx-auto max-w-3xl px-6">
        {scene.title && (
          <h2 className="mb-4 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        {scene.content && <p className="mb-10 opacity-70">{scene.content}</p>}
        <ol className="space-y-6">
          {items.map((item, i) => (
            <li key={i} className="flex gap-5">
              <span
                className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
                aria-hidden
              >
                {i + 1}
              </span>
              <p className="leading-relaxed">{item.content}</p>
            </li>
          ))}
        </ol>
      </div>
    </section>
  );
}
