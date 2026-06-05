const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  console.log('Taking Register screenshot...');
  await page.goto('http://127.0.0.1:8000/register', { waitUntil: 'networkidle2' });
  await page.screenshot({ path: 'public/images/guide/register.png' });

  console.log('Taking Dashboard (Submit Deals) screenshot...');
  // Since we are not logged in, we might get redirected from dashboard.
  // We can take a screenshot of the main marketplace instead for 'Submit Deals'
  // Or we can mock the session. Let's just capture the marketplace for now.
  await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle2' });
  await page.screenshot({ path: 'public/images/guide/submit-deals.png' });

  console.log('Taking Chat screenshot...');
  // Chat might not be accessible without login, so we'll just capture a placeholder or the services page
  await page.goto('http://127.0.0.1:8000/contact', { waitUntil: 'networkidle2' });
  await page.screenshot({ path: 'public/images/guide/chat.png' });

  console.log('Taking Transporters screenshot...');
  await page.goto('http://127.0.0.1:8000/network', { waitUntil: 'networkidle2' });
  await page.screenshot({ path: 'public/images/guide/track-transporters.png' });

  await browser.close();
  console.log('Done!');
})();
