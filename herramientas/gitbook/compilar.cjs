'use strict';

const path = require('node:path');
const fs = require('node:fs');
const { spawnSync } = require('node:child_process');
const root = path.resolve(__dirname, '../..');
const major = Number(process.versions.node.split('.')[0]);
if (major < 24 || major >= 27) throw new Error('Se requiere Node LTS 24 o 26.');
const metadataPath = require.resolve('honkit/package.json');
const metadata = require(metadataPath);
if (metadata.version !== '6.2.5') throw new Error('Se requiere HonKit 6.2.5, fijado en el lockfile.');
const bin = typeof metadata.bin === 'string' ? metadata.bin : metadata.bin.honkit;
const result = spawnSync(process.execPath, [path.resolve(path.dirname(metadataPath), bin), 'build', path.join(root, 'docs'), path.join(root, '_book')], { cwd: root, stdio: 'inherit' });
if (result.error) throw result.error;
if (result.status !== 0) process.exit(result.status || 1);
function prepararHTML(directory) {
    for (const name of fs.readdirSync(directory)) {
        const file = path.join(directory, name);
        if (fs.statSync(file).isDirectory()) { prepararHTML(file); continue; }
        if (!name.endsWith('.html')) continue;
        const html = fs.readFileSync(file, 'utf8');
        const canonical = "<script>if(location.pathname.slice(-11)==='/index.html'){history.replaceState(null,'',location.pathname.slice(0,-10)+location.search+location.hash);}</script>";
        fs.writeFileSync(file, html.replace('<head>', '<head>' + canonical));
    }
}
prepararHTML(path.join(root, '_book'));
fs.copyFileSync(path.join(__dirname, 'vista-previa.cjs'), path.join(root, '_book', 'abrir-vista-previa.cjs'));
fs.copyFileSync(path.join(__dirname, 'ABRIR-GITBOOK.cmd'), path.join(root, '_book', 'ABRIR-GITBOOK.cmd'));
console.log(`Libro compatible con GitBook generado con HonKit ${metadata.version} / Node ${process.versions.node}.`);
