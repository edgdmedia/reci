type SpacingToken = 'tight' | 'comfortable' | 'spacious';

const SPACING_MAP: Record<string, SpacingToken> = {
  compact:     'tight',
  tight:       'tight',
  comfortable: 'comfortable',
  spacious:    'spacious',
};

export function normaliseSpacing(value: string | undefined): SpacingToken {
  return SPACING_MAP[value ?? ''] ?? 'comfortable';
}

export const SPACING_CSS: Record<SpacingToken, string> = {
  tight:       '1rem',
  comfortable: '1.5rem',
  spacious:    '2rem',
};
