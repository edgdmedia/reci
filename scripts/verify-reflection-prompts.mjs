/**
 * Checks the reflection prompt contract in a real browser.
 *
 * Unit tests cover the decision logic; they cannot see a class that sets
 * display:none, an id that appears twice, or a panel that swaps to nothing.
 * Every bug this file exists to catch was one that shipped.
 *
 * Usage: node scripts/verify-reflection-prompts.mjs [baseUrl]
 * Default base: http://localhost:10003
 */

import { chromium } from 'playwright';

const BASE = process.argv[2] || process.env.RECI_BASE_URL || 'http://localhost:10003';

const browser = await chromium.launch();
const page = await browser.newPage();
const problems = [];

async function reflectionSlugs() {
  const res = await page.request.get(`${BASE}/wp-json/wp/v2/reci_reflection?per_page=50&_fields=slug`);
  if (!res.ok()) throw new Error(`Cannot list reflections: HTTP ${res.status()}`);
  return (await res.json()).map((r) => r.slug);
}

for (const slug of await reflectionSlugs()) {
  const url = `${BASE}/reflections/${slug}/`;
  const res = await page.goto(url, { waitUntil: 'domcontentloaded' });

  if (!res || !res.ok()) {
    problems.push(`${slug}: HTTP ${res && res.status()}`);
    continue;
  }

  const report = await page.evaluate(() => {
    const dupes = (sel) => {
      const ids = Array.from(document.querySelectorAll(sel)).map((el) => el.id).filter(Boolean);
      return ids.filter((id, i) => ids.indexOf(id) !== i);
    };

    const forms = Array.from(document.querySelectorAll('[data-reci-prompt]')).map((f) => {
      const ta = f.querySelector('[data-reci-response]');
      const save = f.querySelector('[data-reci-save]');
      const success = f.querySelector('[data-reci-success]');
      const body = f.querySelector('[data-reci-form-body]');

      // The panel must actually appear once the attribute is cleared. A
      // Tailwind `hidden` class here beats the attribute and leaves the reader
      // staring at nothing.
      let shown = null;
      if (success) {
        success.hidden = false;
        shown = getComputedStyle(success).display;
        success.hidden = true;
      }

      return {
        textareaStyled: ta ? ta.className.trim().length > 0 : false,
        saveVisible: save ? getComputedStyle(save).display !== 'none' : false,
        saveText: save ? save.textContent.trim() : '',
        hasBody: !!body,
        hasSuccess: !!success,
        successHiddenAtRest: success ? success.hidden === true : null,
        successDisplayWhenShown: shown,
      };
    });

    const sharedButton = document.querySelector('[data-stage-target="reci-shared-journals"]');
    const sharedChapter = document.getElementById('reci-shared-journals');

    return { forms, duplicateIds: dupes('[id]'), hasSharedButton: !!sharedButton, hasSharedChapter: !!sharedChapter };
  });

  if (report.duplicateIds.length) {
    problems.push(`${slug}: duplicate ids ${[...new Set(report.duplicateIds)].join(', ')}`);
  }

  // A button that opens a chapter which is not on the page goes nowhere.
  if (report.hasSharedButton && !report.hasSharedChapter) {
    problems.push(`${slug}: shared-reflections button with no chapter to open`);
  }

  report.forms.forEach((f, i) => {
    const where = `${slug} prompt#${i + 1}`;
    if (!f.textareaStyled) problems.push(`${where}: textarea has no classes`);
    if (!f.saveVisible) problems.push(`${where}: no visible save button`);
    if (!f.hasBody) problems.push(`${where}: missing form body wrapper`);
    if (!f.hasSuccess) problems.push(`${where}: missing confirmation panel`);
    if (f.successHiddenAtRest === false) problems.push(`${where}: confirmation showing before saving`);
    if (f.successDisplayWhenShown === 'none') problems.push(`${where}: confirmation stays hidden when shown`);
  });

  console.log(`${slug}: ${report.forms.length} prompt form(s)${report.hasSharedChapter ? ', shared chapter present' : ''}`);
}

await browser.close();

if (problems.length) {
  console.error(`\n${problems.length} problem(s):`);
  problems.forEach((p) => console.error(`  - ${p}`));
  process.exit(1);
}

console.log('\nAll reflection prompts satisfy the contract.');
