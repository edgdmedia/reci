import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function HeroScene({ scene }: Props) {
  const bg = scene.background_image_url;
  return (
    <section
      className="relative flex min-h-[80vh] items-end overflow-hidden"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      {bg && (
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{ backgroundImage: `url(${bg})` }}
          aria-hidden
        />
      )}
      <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" aria-hidden />
      <div className="relative z-10 mx-auto w-full max-w-5xl px-6 pb-16 pt-24">
        {scene.badge && (
          <span
            className="mb-4 inline-block rounded px-2 py-1 text-xs font-medium uppercase tracking-widest"
            style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
          >
            {scene.badge}
          </span>
        )}
        {scene.title && (
          <h1
            className="mb-6 font-bold leading-tight"
            style={{
              fontFamily: 'var(--reci-heading-font)',
              fontSize: 'clamp(2rem, 5vw, calc(var(--reci-font-base, 18px) * 4))',
            }}
          >
            {scene.title}
          </h1>
        )}
        {scene.quote && (
          <blockquote
            className="mb-4 border-l-4 pl-6 text-xl italic opacity-90"
            style={{ borderColor: 'var(--reci-accent)' }}
          >
            {scene.quote}
          </blockquote>
        )}
        {scene.speaker && (
          <p className="text-sm font-medium uppercase tracking-widest opacity-70">
            {scene.speaker}{scene.role ? ` — ${scene.role}` : ''}
          </p>
        )}
      </div>
    </section>
  );
}
