const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new' });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  await page.goto('http://localhost:8000/auto-login-for-screenshot', { waitUntil: 'networkidle2' });
  
  await new Promise(r => setTimeout(r, 2000));

  await page.screenshot({ path: 'public/images/guide/track-transporters.png' });

  await browser.close();
  console.log('Dashboard screenshot taken successfully!');
})();
