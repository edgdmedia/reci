import { useEffect, useRef } from 'react';
import type { ChapterProps, ElementStyle, ButtonStyle, ChapterAnimation } from '../../../../types/blueprint';
import { useBreakpoint, resolveResponsive } from '../../utils/responsive';

function chapterAnimStyle(a?: ChapterAnimation): React.CSSProperties {
  if (!a || a.type === 'none' || !a.type) return {};
  return {
    animation: `reci-chapter-${a.type} ${a.duration ?? '0.6s'} ${a.easing ?? 'ease-out'} both`,
  };
}

export default function ThresholdIntroChapter({ chapter, status, onComplete }: ChapterProps) {
  if (status === 'locked') return null;

  const bp = useBreakpoint();

  function elementStyles(s?: ElementStyle): React.CSSProperties {
    if (!s) return {};
    return {
      fontFamily: s.fontFamily,
      fontSize:   resolveResponsive(s.fontSize, bp),
      color:      s.color,
      textAlign:  resolveResponsive(s.textAlign, bp),
      animation:  s.animation && s.animation !== 'none'
        ? `reci-${s.animation} ${s.animationDuration ?? '0.8s'} ${s.animationDelay ?? ''} both`
        : undefined,
    };
  }

  function buttonStyles(s?: ButtonStyle): React.CSSProperties {
    if (!s) return {};
    return {
      fontFamily:      s.fontFamily,
      fontSize:        resolveResponsive(s.fontSize, bp),
      paddingLeft:     resolveResponsive(s.paddingX, bp),
      paddingRight:    resolveResponsive(s.paddingX, bp),
      paddingTop:      resolveResponsive(s.paddingY, bp),
      paddingBottom:   resolveResponsive(s.paddingY, bp),
      borderRadius:    s.borderRadius,
      backgroundColor: s.backgroundColor,
      color:           s.textColor,
    };
  }

  const { content, animation } = chapter;
  const bg  = content?.background_image_url;
  const bs  = content?.button_style ?? {};

  const btnRef = useRef<HTMLButtonElement>(null);

  // Hover colours via JS since we can't use Tailwind arbitrary values dynamically
  useEffect(() => {
    const btn = btnRef.current;
    if (!btn || !bs.hoverBackgroundColor) return;
    const enter = () => {
      if (bs.hoverBackgroundColor) btn.style.backgroundColor = bs.hoverBackgroundColor;
      if (bs.hoverTextColor)       btn.style.color           = bs.hoverTextColor;
    };
    const leave = () => {
      btn.style.backgroundColor = bs.backgroundColor ?? '';
      btn.style.color           = bs.textColor ?? '';
    };
    btn.addEventListener('mouseenter', enter);
    btn.addEventListener('mouseleave', leave);
    return () => { btn.removeEventListener('mouseenter', enter); btn.removeEventListener('mouseleave', leave); };
  }, [bs.hoverBackgroundColor, bs.hoverTextColor, bs.backgroundColor, bs.textColor]);

  function handleClick() {
    if (bs.action === 'url' && bs.actionUrl) {
      window.open(bs.actionUrl, '_blank');
    } else {
      onComplete();
    }
  }

  return (
    <div
      className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden text-center"
      style={{ background: 'var(--reci-bg)', color: 'var(--reci-text)', ...chapterAnimStyle(animation) }}
    >
      {bg && (
        <div
          className="absolute inset-0 bg-cover bg-center opacity-30"
          style={{ backgroundImage: `url(${bg})` }}
          aria-hidden
        />
      )}
      <div className="relative z-10 max-w-3xl px-8">
        {content?.title && (
          <h1
            className="mb-6 text-5xl font-bold leading-tight lg:text-7xl"
            style={{
              fontFamily: 'var(--reci-heading-font)',
              ...elementStyles(content.title_style),
            }}
          >
            {content.title}
          </h1>
        )}
        {content?.subtitle && (
          <p
            className="mb-12 text-lg opacity-70 lg:text-xl"
            style={elementStyles(content.subtitle_style)}
          >
            {content.subtitle}
          </p>
        )}
        <button
          ref={btnRef}
          onClick={handleClick}
          className="rounded-full px-10 py-4 text-sm font-semibold uppercase tracking-widest transition"
          style={{
            background: 'var(--reci-accent)',
            color: 'var(--reci-bg)',
            ...buttonStyles(bs),
          }}
        >
          {content?.button_label ?? 'Begin'}
        </button>
      </div>
    </div>
  );
}
