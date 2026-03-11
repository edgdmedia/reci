import type { Scene } from '../../../../types/blueprint';

interface Props { scene: Scene }

export default function RichTextScene({ scene }: Props) {
  return (
    <section
      className="py-16"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)' }}
    >
      <div className="mx-auto max-w-3xl px-6">
        {scene.title && (
          <h2 className="mb-8 text-3xl font-bold" style={{ fontFamily: 'var(--reci-heading-font)' }}>
            {scene.title}
          </h2>
        )}
        {scene.content && (
          <div
            className="prose prose-lg max-w-none leading-relaxed"
            style={{ color: 'var(--reci-text)' }}
            dangerouslySetInnerHTML={{ __html: scene.content }}
          />
        )}
      </div>
    </section>
  );
}
