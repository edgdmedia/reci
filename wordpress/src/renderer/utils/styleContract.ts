import type { ReflectionStyle } from '../../types/blueprint';
import { normaliseSpacing, SPACING_CSS } from './spacingCompat';

interface ThemeTokens {
  bg: string;
  text: string;
  accent: string;
}

const THEME_MAP: Record<string, ThemeTokens> = {
  'immersive-dark': { bg: '#0a0a0a',  text: '#f5f0e8', accent: '#f59e0b' },
  'light':          { bg: '#ffffff',  text: '#171717', accent: '#f59e0b' },
  'archival':       { bg: '#f5f0e6',  text: '#292524', accent: '#78716c' },
  'editorial':      { bg: '#f8f8f8',  text: '#111111', accent: '#111111' },
  'spotlight':      { bg: '#18181b',  text: '#ffffff', accent: '#60a5fa' },
};

const DEFAULT_TOKENS = THEME_MAP['immersive-dark'];

export type CSSVars = Record<string, string>;

export function resolveStyleContract(style: ReflectionStyle): CSSVars {
  const token = THEME_MAP[style.theme ?? ''] ?? DEFAULT_TOKENS;
  const spacing = normaliseSpacing(style.spacing);

  return {
    '--reci-bg':           style.backgroundColor ?? token.bg,
    '--reci-text':         style.textColor        ?? token.text,
    '--reci-accent':       style.accentColor      ?? token.accent,
    '--reci-font-base':    style.baseFontSize ? `${style.baseFontSize}px` : '18px',
    '--reci-heading-font': style.headingFont
      ? `'${style.headingFont}', serif`
      : "'EB Garamond', serif",
    '--reci-spacing-unit': SPACING_CSS[spacing],
  };
}
