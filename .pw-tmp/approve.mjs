import pkg from '/Users/olalekan/Projects/reci/media-hub/node_modules/playwright/index.js';
const { chromium } = pkg;
const BASE='http://localhost:10003';
const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport:{width:1440,height:1000} });
const page = await ctx.newPage();
await page.goto(`${BASE}/sign-in/`, { waitUntil:'domcontentloaded' });
await page.fill('input[name="log"]','reci_editor@example.test');
await page.fill('input[name="pwd"]','Probe!Pass2026x');
await Promise.all([page.waitForNavigation({waitUntil:'domcontentloaded',timeout:20000}).catch(()=>{}),
  page.click('form:has(input[name="reci_sign_in_nonce"]) button[type="submit"]')]);

await page.goto(`${BASE}/wp-admin/edit.php?post_type=reci_collab_app`, { waitUntil:'domcontentloaded' });
console.log('applications list reachable:', page.url().includes('reci_collab_app'));
const rows = await page.$$eval('#the-list tr', ns=>ns.map(n=>n.innerText.replace(/\s+/g,' ').trim().slice(0,70)));
console.log('rows:', rows.join(' || '));
const approve = page.locator('a', { hasText: /^Approve$/ }).first();
console.log('Approve row action present:', await approve.count() > 0);

if (await approve.count() > 0) {
  const href = await approve.getAttribute('href');
  await page.goto(href, { waitUntil:'domcontentloaded' });
  console.log('after approve ->', page.url().replace(BASE,''));
  const notice = await page.locator('.notice p').first().innerText().catch(()=>'(none)');
  console.log('notice:', notice);
}
await browser.close();
