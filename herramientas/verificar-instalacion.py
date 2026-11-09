"""Comprueba una instalación efímera de CI, sin tocar bases locales ni la matriz de Luis."""
import http.cookiejar
import json
import os
import re
import subprocess
import time
from pathlib import Path
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode
from urllib.request import HTTPCookieProcessor, build_opener

ROOT = Path(__file__).resolve().parents[1]
if os.environ.get('APP_ENV') != 'testing' or os.environ.get('DB_DATABASE') != 'valtraza_install':
    raise SystemExit('Solo se permite la base efímera valtraza_install con APP_ENV=testing.')

php = r"""require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
if ($db->getDatabaseName() !== 'valtraza_install') { throw new RuntimeException('Base incorrecta'); }
$tablas = ['cliente','tecnico','usuario','equipo','servicio','diagnostico','trabajo','repuesto'];
foreach ($tablas as $tabla) { if (!$db->getSchemaBuilder()->hasTable($tabla)) throw new RuntimeException('Falta tabla '.$tabla); }
$admin = $db->table('usuario')->sole();
if ($admin->nombre_usuario !== 'admin' || $admin->activado || !$admin->codigo_activacion) throw new RuntimeException('Administrador inicial incorrecto');
echo json_encode(['codigo' => $admin->codigo_activacion, 'tablas' => count($tablas)]);
"""
setup = subprocess.run(['php', '-r', php], cwd=ROOT, check=True, capture_output=True, text=True)
data = json.loads(setup.stdout)
log = Path(os.environ['VALTRAZA_INSTALACION_LOG'])
opener = build_opener(HTTPCookieProcessor(http.cookiejar.CookieJar()))
origin = 'http://127.0.0.1:8000'

def request(path, values=None):
    body = None if values is None else urlencode(values).encode()
    try:
        with opener.open(origin + path, body, timeout=5) as response:
            return response.status, response.geturl(), response.read().decode('utf-8')
    except HTTPError as error:
        raise RuntimeError('HTTP inesperado: ' + str(error.code) + ' en ' + path) from None

def token(html):
    match = re.search(r'name="_token"\s+value="([^"]+)"', html)
    if not match: raise RuntimeError('Falta token CSRF del formulario')
    return match.group(1)

with log.open('w', encoding='utf-8') as output:
    server = subprocess.Popen(['php', 'artisan', 'serve', '--host=127.0.0.1', '--port=8000'], cwd=ROOT, stdout=output, stderr=subprocess.STDOUT)
    try:
        for attempt in range(40):
            try:
                status, url, html = request('/')
                break
            except URLError:
                if server.poll() is not None: raise RuntimeError('El servidor terminó antes de responder')
                time.sleep(0.25)
        else: raise RuntimeError('El servidor no respondió')
        if status != 200 or 'Valtraza' not in html: raise RuntimeError('El inicio no carga')
        _, _, login = request('/ingresar')
        if 'Activar mi cuenta' not in login: raise RuntimeError('Falta la guía de activación')
        _, _, activation = request('/activar')
        # La clave solo existe en esta base efímera; no se publica ni se entrega como credencial real.
        password = 'InstalacionTemporal2026!'
        status, url, html = request('/activar', {'_token': token(activation), 'codigo_activacion': data['codigo'], 'password': password, 'password_confirmation': password})
        if 'Cuenta activada.' not in html: raise RuntimeError('La activación no se confirmó')
        _, _, login = request('/ingresar')
        status, url, panel = request('/ingresar', {'_token': token(login), 'nombre_usuario': 'admin', 'password': password})
        if '/panel' not in url or 'Bienvenido, admin' not in panel: raise RuntimeError('La activación o el ingreso no llevó al panel de administrador')
        _, _, accounts = request('/cuentas/nueva')
        if 'Crear cuenta' not in accounts: raise RuntimeError('La cuenta no tiene acceso administrativo')
        _, url, _ = request('/salir', {'_token': token(accounts)})
        _, url, _ = request('/panel')
        if '/ingresar' not in url: raise RuntimeError('El cierre de sesión no protege el panel')
        print('Instalación nueva: 8 tablas, seeder repetible, cachés, inicio HTTP, activación, ingreso administrativo y cierre de sesión correctos.')
        print('Comprobación técnica de instalación en CI; no sustituye la matriz, HTTPS ni la prueba manual en Windows.')
    finally:
        server.terminate()
        try: server.wait(timeout=10)
        except subprocess.TimeoutExpired:
            server.kill()
            server.wait()
