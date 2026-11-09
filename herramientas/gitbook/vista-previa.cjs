'use strict';

const fs = require('node:fs');
const http = require('node:http');
const path = require('node:path');
const { spawn } = require('node:child_process');
const types = { '.html': 'text/html; charset=utf-8', '.css': 'text/css', '.js': 'application/javascript', '.json': 'application/json', '.svg': 'image/svg+xml', '.png': 'image/png', '.ico': 'image/x-icon', '.woff': 'font/woff', '.woff2': 'font/woff2', '.ttf': 'font/ttf' };

function crearServidor(directory) {
    const book = path.resolve(directory);
    return http.createServer((request, response) => {
        let url;
        let pathname;
        try {
            url = new URL(request.url, 'http://localhost');
            pathname = decodeURIComponent(url.pathname);
        } catch { response.writeHead(400); response.end(); return; }
        if (pathname.endsWith('/index.html')) {
            response.writeHead(301, { Location: pathname.slice(0, -10) + url.search });
            response.end();
            return;
        }
        let target = path.resolve(book, '.' + pathname);
        if (!target.startsWith(book + path.sep) && target !== book) { response.writeHead(403); response.end(); return; }
        if (fs.existsSync(target) && fs.statSync(target).isDirectory()) {
            if (!pathname.endsWith('/')) {
                response.writeHead(301, { Location: pathname + '/' + url.search });
                response.end();
                return;
            }
            target = path.join(target, 'index.html');
        }
        if (!fs.existsSync(target) || !fs.statSync(target).isFile()) { response.writeHead(404); response.end(); return; }
        response.setHeader('Content-Type', types[path.extname(target)] || 'application/octet-stream');
        fs.createReadStream(target).pipe(response);
    });
}

module.exports = { crearServidor };

if (require.main === module) {
    if (!fs.existsSync(path.join(__dirname, 'index.html'))) {
        console.error('Extrae primero la vista previa completa del GitBook.');
        process.exit(1);
    }
    const server = crearServidor(__dirname);
    server.on('error', error => { console.error(error.message); process.exitCode = 1; });
    server.listen(0, '127.0.0.1', () => {
        const url = `http://127.0.0.1:${server.address().port}/`;
        console.log(`GitBook de Valtraza: ${url}`);
        console.log('Deja esta ventana abierta. Ctrl+C detiene la vista previa.');
        const command = process.platform === 'win32' ? 'cmd.exe' : process.platform === 'darwin' ? 'open' : 'xdg-open';
        const args = process.platform === 'win32' ? ['/c', 'start', '', url] : [url];
        const browser = spawn(command, args, { windowsHide: true, stdio: 'ignore' });
        browser.on('error', () => console.log('Abre manualmente la direccion anterior en tu navegador.'));
        browser.unref();
    });
    process.on('SIGINT', () => server.close());
}
