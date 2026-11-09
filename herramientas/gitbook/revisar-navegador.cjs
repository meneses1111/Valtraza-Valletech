'use strict';

const fs = require('node:fs');
const http = require('node:http');
const path = require('node:path');
const puppeteer = require(path.join(process.env.GITBOOK_BROWSER_MODULES, 'puppeteer-core'));
const root = path.resolve(__dirname, '../..');
const book = path.join(root, '_book');
const destination = process.env.GITBOOK_QA_OUTPUT || path.join(root, '_verificacion_gitbook');
const types = { '.html': 'text/html; charset=utf-8', '.css': 'text/css', '.js': 'application/javascript', '.json': 'application/json', '.svg': 'image/svg+xml', '.png': 'image/png', '.ico': 'image/x-icon', '.woff': 'font/woff', '.woff2': 'font/woff2', '.ttf': 'font/ttf' };

const server = http.createServer((request, response) => {
    let pathname;
    try { pathname = decodeURIComponent(new URL(request.url, 'http://localhost').pathname); }
    catch { response.writeHead(400); response.end(); return; }
    let target = path.resolve(book, '.' + pathname);
    if (!target.startsWith(book + path.sep) && target !== book) { response.writeHead(403); response.end(); return; }
    if (fs.existsSync(target) && fs.statSync(target).isDirectory()) target = path.join(target, 'index.html');
    if (!fs.existsSync(target) || !fs.statSync(target).isFile()) { response.writeHead(404); response.end(); return; }
    response.setHeader('Content-Type', types[path.extname(target)] || 'application/octet-stream');
    fs.createReadStream(target).pipe(response);
});

async function run() {
    fs.mkdirSync(destination, { recursive: true });
    const summary = fs.readFileSync(path.join(root, 'docs/SUMMARY.md'), 'utf8');
    const pages = [...summary.matchAll(/\]\(([^)]+\.md)\)/g)].map(match => match[1].replace(/README\.md$/, 'index.html').replace(/\.md$/, '.html'));
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const origin = `http://127.0.0.1:${server.address().port}`;
    const browser = await puppeteer.launch({ executablePath: process.env.GITBOOK_CHROME, headless: true, args: ['--no-sandbox', '--disable-dev-shm-usage'] });
    const results = [];
    try {
        for (const size of [{ width: 1366, height: 768 }, { width: 1920, height: 1080 }]) {
            const page = await browser.newPage();
            await page.setViewport(size);
            const errors = [];
            page.on('pageerror', error => errors.push(String(error)));
            for (const relative of pages) {
                const response = await page.goto(origin + '/' + relative, { waitUntil: 'networkidle0', timeout: 20000 });
                const content = await page.$eval('.markdown-section', element => element.innerText.trim());
                const width = await page.evaluate(() => ({ scroll: document.documentElement.scrollWidth, visible: window.innerWidth }));
                const images = await page.$$eval('.markdown-section img', elements => elements.every(image => image.complete && image.naturalWidth > 0));
                if (!response || response.status() !== 200 || content.length < 80 || width.scroll > width.visible + 1 || !images) {
                    throw new Error(`P�gina incompleta o desbordada: ${relative} a ${size.width}x${size.height}`);
                }
                results.push({ pagina: relative, resolucion: `${size.width}x${size.height}`, http: response.status(), contenido: true, sin_desbordamiento_horizontal: true, imagenes: images });
                if (size.width === 1366 && ['index.html', 'arquitectura/index.html', 'base-de-datos/modelo.html'].includes(relative)) {
                    await page.screenshot({ path: path.join(destination, relative.replaceAll('/', '-') + '.png'), fullPage: true });
                }
            }
            await page.goto(origin + '/index.html', { waitUntil: 'networkidle0' });
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle0' }),
                page.evaluate(() => {
                    const navigation = [...document.querySelectorAll('.summary a')].find(link => link.textContent.includes('Instalaci�n y operaci�n'));
                    if (!navigation) throw new Error('No aparece Instalaci�n en el �ndice del tema.');
                    navigation.click();
                }),
            ]);
            if (!page.url().includes('/instalacion/')) throw new Error('El �ndice no navega a Instalaci�n.');
            if (errors.length) throw new Error('Errores JavaScript: ' + errors.join('; '));
            await page.close();
        }
        fs.writeFileSync(path.join(destination, 'resultado.json'), JSON.stringify({ gitbook: '3.2.2', paginas: pages.length, comprobaciones: results.length, indice_navegable: true, resultados: results }, null, 2));
        console.log(`GitBook: ${pages.length} p�ginas, ${results.length} comprobaciones en Chrome y navegaci�n del �ndice correctas.`);
    } finally {
        await browser.close();
    }
}

run().catch(error => { console.error(error); process.exitCode = 1; }).finally(() => server.close());
