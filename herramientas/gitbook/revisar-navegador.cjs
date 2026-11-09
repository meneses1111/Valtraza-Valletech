'use strict';

const fs = require('node:fs');
const path = require('node:path');
const { crearServidor } = require('./vista-previa.cjs');
const puppeteer = require(path.join(process.env.GITBOOK_BROWSER_MODULES, 'puppeteer-core'));
const root = path.resolve(__dirname, '../..');
const book = path.join(root, '_book');
const destination = process.env.GITBOOK_QA_OUTPUT || path.join(root, '_verificacion_gitbook');
const basePath = process.env.GITBOOK_BASE_PATH || '';
const server = crearServidor(book, { basePath, redirectIndex: process.env.GITBOOK_INDEX_REDIRECT !== 'false' });

async function run() {
    fs.mkdirSync(destination, { recursive: true });
    const summary = fs.readFileSync(path.join(root, 'docs/SUMMARY.md'), 'utf8');
    const pages = [...summary.matchAll(/\]\(([^)]+\.md)\)/g)].map(match => match[1].replace(/README\.md$/, 'index.html').replace(/\.md$/, '.html'));
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const origin = `http://127.0.0.1:${server.address().port}${basePath}`;
    const browser = await puppeteer.launch({ executablePath: process.env.GITBOOK_CHROME, headless: true, args: ['--no-sandbox', '--disable-dev-shm-usage'] });
    const results = [];
    try {
        for (const size of [{ width: 1366, height: 768 }, { width: 1920, height: 1080 }]) {
            const page = await browser.newPage();
            await page.setViewport(size);
            const errors = [];
            page.on('pageerror', error => {
                const detail = { pagina: page.url(), mensaje: String(error), stack: error.stack };
                errors.push(detail);
                console.error(JSON.stringify(detail));
            });
            for (const relative of pages) {
                const response = await page.goto(origin + '/' + relative, { waitUntil: 'networkidle0', timeout: 20000 });
                const content = await page.$eval('.markdown-section', element => element.innerText.trim());
                const width = await page.evaluate(() => ({ scroll: document.documentElement.scrollWidth, visible: window.innerWidth }));
                const images = await page.$$eval('.markdown-section img', elements => elements.every(image => image.complete && image.naturalWidth > 0));
                if (!response || response.status() !== 200 || content.length < 80 || width.scroll > width.visible + 1 || !images) {
                    throw new Error(`Página incompleta o desbordada: ${relative} a ${size.width}x${size.height}`);
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
                    const navigation = [...document.querySelectorAll('.summary a')].find(link => link.textContent.includes('Instalación y operación'));
                    if (!navigation) throw new Error('No aparece Instalación en el índice del tema.');
                    navigation.click();
                }),
            ]);
            if (!page.url().includes('/instalacion/')) throw new Error('El índice no navega a Instalación.');
            if (errors.length) throw new Error('Errores JavaScript: ' + JSON.stringify(errors));
            await page.close();
        }
        fs.writeFileSync(path.join(destination, 'resultado.json'), JSON.stringify({ gitbook: '3.2.2', ruta_base: basePath, paginas: pages.length, comprobaciones: results.length, indice_navegable: true, resultados: results }, null, 2));
        console.log(`GitBook: ${pages.length} páginas, ${results.length} comprobaciones en Chrome y navegación del índice correctas.`);
    } finally {
        await browser.close();
    }
}

run().catch(error => { console.error(error); process.exitCode = 1; }).finally(() => server.close());
