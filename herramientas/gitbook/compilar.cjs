'use strict';

var path = require('path');
var fs = require('fs');
var root = path.resolve(__dirname, '../..');
if (process.versions.node !== '10.24.1') {
    console.error('El generador aislado requiere Node 10.24.1. La aplicación Laravel no requiere cambiar su entorno.');
    process.exit(1);
}
var gitbook = require('gitbook');
var version = require('gitbook/package.json').version;
if (version !== '3.2.2') {
    throw new Error('Se requiere GitBook 3.2.2.');
}
var build = gitbook.commands.filter(function(command) { return command.name === 'build [book] [output]'; })[0];
console.log('Compilando con GitBook ' + version + ' / Node ' + process.versions.node);
build.exec([path.join(root, 'docs'), path.join(root, '_book')], { format: 'website', log: 'info' })
    .then(function() {
        function prepararHTML(directory) {
            fs.readdirSync(directory).forEach(function(name) {
                var file = path.join(directory, name);
                if (fs.statSync(file).isDirectory()) return prepararHTML(file);
                if (!name.endsWith('.html')) return;
                var html = fs.readFileSync(file, 'utf8');
                var canonical = "<script>if(location.pathname.slice(-11)==='/index.html'){history.replaceState(null,'',location.pathname.slice(0,-10)+location.search+location.hash);}</script>";
                fs.writeFileSync(file, html.replace('<head>', '<head>' + canonical));
            });
        }
        prepararHTML(path.join(root, '_book'));
        fs.copyFileSync(path.join(__dirname, 'vista-previa.cjs'), path.join(root, '_book', 'abrir-vista-previa.cjs'));
        fs.copyFileSync(path.join(__dirname, 'ABRIR-GITBOOK.cmd'), path.join(root, '_book', 'ABRIR-GITBOOK.cmd'));
        console.log('GitBook generado en _book, con iniciador de vista previa.');
    })
    .catch(function(error) { console.error(error.stack || error); process.exitCode = 1; });
