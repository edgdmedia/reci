import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function QuoteScene({ scene }: Props) {
  return (
    <section
      className="py-20"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="mx-auto max-w-3xl px-6 text-center">
        <div className="mb-6 text-5xl leading-none opacity-30" style={{ color: 'var(--reci-accent)' }} aria-hidden>&#8220;</div>
        {scene.quote && (
          <blockquote
            className="text-2xl font-medium italic leading-snug sm:text-3xl"
            style={{ fontFamily: 'var(--reci-heading-font)' }}
          >
            {scene.quote}
          </blockquote>
        )}
        {scene.speaker && (
          <p className="mt-8 text-sm font-semibold uppercase tracking-widest opacity-60">
            {scene.speaker}{scene.role ? `, ${scene.role}` : ''}
          </p>
        )}
      </div>
    </section>
  );
}
