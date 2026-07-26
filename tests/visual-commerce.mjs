import { createRequire } from 'node:module';
const require = createRequire(import.meta.url);
const { chromium } = require('playwright');

const viewports = [
  { name: 'desktop', width: 1440, height: 1100 },
  { name: 'mobile', width: 390, height: 844 },
];

const browser = await chromium.launch({ headless: true });
for (const viewport of viewports) {
  const page = await browser.newPage({ viewport });
  await page.goto('https://usegeo.blog/#scanner', { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.addStyleTag({ path: 'wordpress/theme/usegeo/assets/css/homepage.css' });
  await page.evaluate(() => {
    const result = document.querySelector('[data-usegeo-scanner-result]');
    const empty = document.querySelector('[data-usegeo-scanner-empty]');
    if (empty) empty.hidden = true;
    if (result) result.hidden = false;
    const gate = document.querySelector('.usegeo-report-gate');
    if (!gate) throw new Error('report gate missing');
    gate.outerHTML = `<div class="usegeo-report-gate" data-usegeo-report-gate>
      <div class="usegeo-report-gate__copy"><span class="usegeo-report-gate__kicker">Your next move</span><h3>Turn this snapshot into a focused 30-day action plan.</h3><p class="usegeo-report-gate__intro">Get the free result by email, or unlock the full implementation report instantly. No sales call and no subscription.</p><ul><li>Every evidence-backed finding and recommendation</li><li>Your three lowest-scoring priorities in order</li><li>A printable report your team can execute</li></ul></div>
      <form class="usegeo-report-gate__form"><div class="usegeo-report-gate__form-head"><span>Homepage GEO Action Report</span><strong>$29</strong><small>One website · instant delivery</small></div><label><span>Where should we send your result?</span><input type="email" placeholder="name@company.com"></label><div class="usegeo-report-gate__actions"><button type="button" class="usegeo-report-gate__secondary">Email free snapshot</button><button type="button" class="usegeo-report-gate__button">Unlock full report · $29</button></div><p class="usegeo-report-gate__status"></p><p class="usegeo-report-gate__disclosure">Secure checkout by Stripe. Your information is used only to deliver this report.</p></form>
    </div>`;
  });
  const gate = page.locator('.usegeo-report-gate');
  await gate.scrollIntoViewIfNeeded();
  const metrics = await gate.evaluate((element) => ({
    scrollWidth: element.scrollWidth,
    clientWidth: element.clientWidth,
    scrollHeight: element.scrollHeight,
    buttons: Array.from(element.querySelectorAll('button')).map((button) => ({ width: button.clientWidth, scrollWidth: button.scrollWidth })),
  }));
  if (metrics.scrollWidth > metrics.clientWidth + 1 || metrics.buttons.some((button) => button.scrollWidth > button.width + 1)) {
    throw new Error(`${viewport.name} commerce module overflows: ${JSON.stringify(metrics)}`);
  }
  await gate.screenshot({ path: `tests/visual-commerce-${viewport.name}.png` });
  console.log(`PASS ${viewport.name}: ${JSON.stringify(metrics)}`);
  await page.close();
}
await browser.close();
