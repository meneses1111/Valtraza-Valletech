'use strict';

var path = require('path');
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
    .then(function() { console.log('GitBook generado en _book.'); })
    .catch(function(error) { console.error(error.stack || error); process.exitCode = 1; });
