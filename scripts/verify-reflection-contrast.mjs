/**
 * Reports text that cannot be read against its own background.
 *
 * Reflections carry per-style palettes, and a palette written for a light
 * background lands on a dark card as soon as one variable is missing. That is
 * invisible to unit tests and easy to miss by eye, so it is measured here.
 *
 * Usage: node scripts/verify-reflection-contrast.mjs [baseUrl] [--fail]
 * --fail exits non-zero when anything is below the threshold.
 */

import { chromium } from 'playwright';

const args = process.argv.slice(2);
const BASE = args.find((a) => a.startsWith('http')) || 'http://localhost:10003';
const FAIL = args.includes('--fail');
const MIN = 4.5; // WCAG AA for body text.

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

const slugs = (await (await page.request.get(`${BASE}/wp-json/wp/v2/reci_reflection?per_page=50&_fields=slug`)).json()).map((r) => r.slug);
let total = 0;

for (const slug of slugs) {
  await page.goto(`${BASE}/reflections/${slug}/`, { waitUntil: 'networkidle' });

  const fails = await page.evaluate((min) => {
    const lum = (c) => {
      const [r, g, b] = c.map((v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); });
      return 0.2126 * r + 0.7152 * g + 0.0722 * b;
    };
    const parse = (s) => (s.match(/\d+(\.\d+)?/g) || []).slice(0, 3).map(Number);
    // Walk up for the first painted background, the way the eye does.
    // A background image cannot be sampled this way, and guessing produced
    // false failures on every hero - text over a photograph measured against
    // the white behind it. Those are reported separately, not as failures.
    const bgOf = (el) => {
      let n = el;
      while (n) {
        const cs = getComputedStyle(n);
        if (cs.backgroundImage && cs.backgroundImage !== 'none') return null;
        const b = cs.backgroundColor;
        if (b && !/rgba\(0, 0, 0, 0\)|transparent/.test(b)) return parse(b);
        n = n.parentElement;
      }
      return [255, 255, 255];
    };

    const out = [];
    let skipped = 0;
    document.querySelectorAll('.reci-stage *').forEach((el) => {
      const text = Array.from(el.childNodes).filter((n) => n.nodeType === 3).map((n) => n.textContent.trim()).join('');
      if (text.length < 3) return;
      const cs = getComputedStyle(el);
      if (cs.display === 'none' || cs.visibility === 'hidden' || cs.opacity === '0') return;

      const bg = bgOf(el);
      if (bg === null) { skipped++; return; }

      const L1 = lum(parse(cs.color));
      const L2 = lum(bg);
      const ratio = (Math.max(L1, L2) + 0.05) / (Math.min(L1, L2) + 0.05);
      if (ratio < min) {
        out.push({ stage: el.closest('.reci-stage')?.id, text: text.slice(0, 40), color: cs.color, ratio: +ratio.toFixed(2) });
      }
    });
    return { out, skipped };
  }, MIN);

  const { out: list, skipped } = fails;
  total += list.length;
  console.log(`${slug}: ${list.length} below ${MIN}:1 (${skipped} over images, not measurable)`);
  list.slice(0, 6).forEach((f) => console.log(`    ${f.ratio}:1  ${f.stage}  ${f.color}  "${f.text}"`));
  if (list.length > 6) console.log(`    ... and ${list.length - 6} more`);
}

await browser.close();
console.log(`\n${total} low-contrast text nodes across ${slugs.length} reflections.`);
if (FAIL && total > 0) process.exit(1);
