import { useGSAP } from '../../hooks/useGSAP';
import type { ChapterProps } from '../../../../types/blueprint';

export default function ParallaxStageChapter({ chapter, status, onComplete }: ChapterProps) {
  if (status === 'locked') return null;
  const { content } = chapter;

  const ref = useGSAP((el) => {
    const layers = el.querySelectorAll<HTMLElement>('[data-depth]');
    el.addEventListener('mousemove', (e: MouseEvent) => {
      const rect = el.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
      const y = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
      layers.forEach((layer) => {
        const depth = parseFloat(layer.dataset.depth ?? '1');
        layer.style.transform = `translate(${x * depth * 20}px, ${y * depth * 20}px)`;
      });
    });
  }, []);

  const items = content?.items ?? [];

  return (
    <div
      ref={ref as React.RefObject<HTMLDivElement>}
      className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      {items.map((item, i) => (
        <div
          key={i}
          data-depth={((i + 1) * 0.5).toString()}
          className="pointer-events-none absolute inset-0 transition-transform duration-75 ease-out"
          style={{ willChange: 'transform' }}
        >
          {item.image_url && (
            <img
              src={item.image_url}
              alt=""
              className="h-full w-full object-cover opacity-60"
              aria-hidden
            />
          )}
        </div>
      ))}
      <div className="relative z-10 max-w-3xl px-8 text-center">
        {content?.title && (
          <h2 className="mb-6 text-5xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {content.title}
          </h2>
        )}
        {content?.content && <p className="mb-10 text-lg opacity-80">{content.content}</p>}
        <button
          onClick={onComplete}
          className="rounded-full px-8 py-3 text-sm font-semibold uppercase tracking-widest"
          style={{ background: 'var(--reci-accent)', color: 'var(--reci-bg)' }}
        >
          {content?.button_label ?? 'Continue'}
        </button>
      </div>
    </div>
  );
}
