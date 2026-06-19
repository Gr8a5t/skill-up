const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ args: ['--no-sandbox'] });
  const page = await browser.newPage();
  
  page.on('console', msg => console.log('PAGE LOG:', msg.text()));
  page.on('pageerror', error => console.log('PAGE ERROR:', error.message));

  await page.goto('http://127.0.0.1:8001/courses/react-beginners/learn', { waitUntil: 'networkidle0' }).catch(e => console.log('Nav error:', e.message));
  
  console.log('--- HTML SNAPSHOT (SCRIPT TAGS) ---');
  const scripts = await page.evaluate(() => {
      return Array.from(document.querySelectorAll('script')).map(s => s.src || (s.innerText ? s.innerText.substring(0, 100).replace(/\n/g, ' ') + '...' : 'empty inline'));
  });
  console.log(scripts);
  
  console.log('--- TESTING BUTTON CLICK ---');
  try {
      const result = await page.evaluate(() => {
          const btn = document.getElementById('open-workspace-btn');
          if(!btn) return 'BUTTON NOT FOUND';
          if(!btn.onclick) return 'BUTTON HAS NO ONCLICK HANDLER BOUND';
          return 'BUTTON HAS ONCLICK HANDLER!';
      });
      console.log('Button Check:', result);
      
      const monacoCheck = await page.evaluate(() => {
          return typeof initWorkspaceUI;
      });
      console.log('typeof initWorkspaceUI:', monacoCheck);
  } catch (e) {
      console.log('Click error:', e.message);
  }

  await browser.close();
})();
