import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';

const BASE = process.env.VALTRAZA_URL ?? 'https://valtraza.test';
const CICLO = process.env.VALTRAZA_CICLO ?? '1';
const SALIDA = path.resolve(process.env.VALTRAZA_EVIDENCIAS ?? `../evidencias/ciclo-${CICLO}/seguridad`);
const ADMIN = process.env.VALTRAZA_ADMIN_USUARIO ?? 'admin';
const CLAVE = process.env.VALTRAZA_ADMIN_CLAVE;
const resultados = [];

function registrar(cp, comprobacion, ok, obtenido) {
    resultados.push({ cp, comprobacion, ok, obtenido });
    console.log(`${ok ? 'OK   ' : 'FALLA'} ${cp} ${comprobacion} · ${obtenido}`);
}

class Sesion {
    constructor() {
        this.cookies = new Map();
        this.ultimasCookies = [];
    }

    async pedir(ruta, opciones = {}, base = BASE) {
        const respuesta = await fetch(base + ruta, {
            redirect: 'manual',
            ...opciones,
            headers: { cookie: [...this.cookies].map(([k, v]) => `${k}=${v}`).join('; '), ...(opciones.headers ?? {}) },
        });
        const cuerpo = await respuesta.text();
        this.ultimasCookies = respuesta.headers.getSetCookie();
        for (const linea of this.ultimasCookies) {
            const [par] = linea.split(';');
            const [nombre, ...valor] = par.split('=');
            this.cookies.set(nombre.trim(), valor.join('='));
        }

        return { estado: respuesta.status, cuerpo, destino: respuesta.headers.get('location'), cabeceras: respuesta.headers };
    }

    async token(ruta = '/ingresar') {
        return (await this.pedir(ruta)).cuerpo.match(/name="_token" value="([^"]+)"/)?.[1];
    }

    formulario(ruta, datos) {
        return this.pedir(ruta, { method: 'POST', headers: { 'content-type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(datos) });
    }
}

const sesionCookie = (s) => [...s.cookies.keys()].find((k) => k.endsWith('session'));

const http = await fetch(BASE.replace('https://', 'http://') + '/ingresar', { redirect: 'manual' });
registrar('CP061', 'HTTP redirige a HTTPS', http.status === 301 && http.headers.get('location')?.startsWith('https://'), `${http.status} -> ${http.headers.get('location')}`);

const visitante = new Sesion();
const ingreso = await visitante.pedir('/ingresar');
registrar('CP061', 'el formulario de ingreso envía a una dirección HTTPS', /action="https:\/\/[^"]+\/ingresar"/.test(ingreso.cuerpo), ingreso.cuerpo.match(/action="([^"]+\/ingresar)"/)?.[1]);
const cookieSesion = visitante.ultimasCookies.find((c) => /session=/.test(c)) ?? '';
registrar('CP069', 'cookie de sesión Secure', /;\s*secure/i.test(cookieSesion), cookieSesion.replace(/=[^;]+/, '=<oculto>'));
registrar('CP069', 'cookie de sesión HttpOnly', /;\s*httponly/i.test(cookieSesion), 'httponly');
registrar('CP069', 'cookie de sesión SameSite=Lax', /;\s*samesite=lax/i.test(cookieSesion), 'samesite=lax');
const expira = new Date(cookieSesion.match(/expires=([^;]+)/i)?.[1]);
const minutos = Math.round((expira - Date.now()) / 60000);
registrar('CP069', 'la sesión expira a las 2 horas de inactividad (SESSION_LIFETIME=120)', minutos >= 118 && minutos <= 121, `${minutos} minutos`);
for (const [cabecera, esperado] of [['x-frame-options', 'DENY'], ['x-content-type-options', 'nosniff'], ['cache-control', 'no-store'], ['content-security-policy', "frame-ancestors 'none'"]]) {
    const valor = ingreso.cabeceras.get(cabecera) ?? '';
    registrar('CP069', `cabecera ${cabecera}`, valor.includes(esperado), valor);
}

const sinToken = await new Sesion().formulario('/ingresar', { nombre_usuario: ADMIN, password: CLAVE });
registrar('CP067', 'ingreso sin token CSRF es rechazado', sinToken.estado === 419, String(sinToken.estado));
const otra = new Sesion();
await otra.pedir('/ingresar');
const tokenAjeno = await new Sesion().token();
const tokenCruzado = await otra.formulario('/ingresar', { _token: tokenAjeno, nombre_usuario: ADMIN, password: CLAVE });
registrar('CP067', 'token CSRF de otra sesión es rechazado', tokenCruzado.estado === 419, String(tokenCruzado.estado));
const consultaSinToken = await new Sesion().formulario('/consultar', { codigo_seguimiento: 'x' });
registrar('CP067', 'consulta pública sin token CSRF es rechazada', consultaSinToken.estado === 419, String(consultaSinToken.estado));
registrar('CP067', 'la página 419 explica que la sesión expiró', /expir|venc|caduc/i.test(consultaSinToken.cuerpo), consultaSinToken.cuerpo.match(/<h1[^>]*>([^<]+)/)?.[1]);

const admin = new Sesion();
const token = await admin.token();
const antes = admin.cookies.get(sesionCookie(admin));
const login = await admin.formulario('/ingresar', { _token: token, nombre_usuario: ADMIN, password: CLAVE });
registrar('CP001', 'ingreso real del administrador', login.estado === 302 && login.destino?.endsWith('/panel'), `${login.estado} -> ${login.destino}`);
const despues = admin.cookies.get(sesionCookie(admin));
registrar('CP069', 'el ingreso renueva el identificador de sesión', antes !== despues, 'identificador distinto');
const copiaSesion = new Sesion();
copiaSesion.cookies = new Map(admin.cookies);
const panel = await admin.pedir('/panel');
const tokenSalir = panel.cuerpo.match(/name="_token" value="([^"]+)"/)?.[1];
await admin.formulario('/salir', { _token: tokenSalir });
const reuso = await copiaSesion.pedir('/panel');
registrar('CP006', 'la cookie anterior al cierre de sesión ya no abre el panel', reuso.estado === 302 && reuso.destino?.endsWith('/ingresar'), `${reuso.estado} -> ${reuso.destino}`);

for (const ruta of ['/.env', '/../.env', '/composer.json', '/storage/logs/laravel.log', '/database/sql/esquema_valtraza.sql', '/vendor/autoload.php']) {
    const r = await new Sesion().pedir(ruta);
    registrar('CP069', `archivo interno no expuesto ${ruta}`, r.estado === 404 || r.estado === 403 || (r.estado === 200 && !/DB_PASSWORD|APP_KEY|require|CREATE TABLE|"laravel\/framework"/.test(r.cuerpo)), String(r.estado));
}

const atacante = new Sesion();
const estados = [];
for (let i = 1; i <= 6; i++) {
    const t = await atacante.token();
    estados.push((await atacante.formulario('/ingresar', { _token: t, nombre_usuario: 'usuario-inexistente', password: 'Clave-equivocada' })).estado);
}
registrar('CP008', 'el sexto intento fallido seguido recibe 429', estados[5] === 429, estados.join(','));

await mkdir(SALIDA, { recursive: true });
await writeFile(path.join(SALIDA, `CP061_CP067_CP069_C${CICLO}_seguridad_http.json`), JSON.stringify({ fecha: new Date().toISOString(), destino: BASE, resultados }, null, 2));
const fallas = resultados.filter((r) => !r.ok).length;
console.log(`\n${resultados.length - fallas} correctas, ${fallas} fallidas.`);
