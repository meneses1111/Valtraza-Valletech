import { mkdir, writeFile } from 'node:fs/promises';
import path from 'node:path';
import os from 'node:os';

const BASE = process.env.VALTRAZA_URL ?? 'https://valtraza.test';
const CICLO = process.env.VALTRAZA_CICLO ?? '1';
const SALIDA = path.resolve(process.env.VALTRAZA_EVIDENCIAS ?? `../evidencias/ciclo-${CICLO}/rendimiento`);
const REPETICIONES = Number(process.env.VALTRAZA_REPETICIONES ?? 20);
const USUARIOS = 15;
const LIMITE_MS = 2000;

class Sesion {
    constructor() {
        this.cookies = new Map();
    }

    async pedir(ruta, opciones = {}) {
        const inicio = performance.now();
        const respuesta = await fetch(BASE + ruta, {
            redirect: 'manual',
            ...opciones,
            headers: { cookie: [...this.cookies].map(([k, v]) => `${k}=${v}`).join('; '), ...(opciones.headers ?? {}) },
        });
        const cuerpo = await respuesta.text();
        const ms = performance.now() - inicio;
        for (const linea of respuesta.headers.getSetCookie()) {
            const [par] = linea.split(';');
            const [nombre, ...valor] = par.split('=');
            this.cookies.set(nombre.trim(), valor.join('='));
        }

        return { estado: respuesta.status, cuerpo, ms, destino: respuesta.headers.get('location') };
    }

    async token(ruta = '/ingresar') {
        const { cuerpo } = await this.pedir(ruta);

        return cuerpo.match(/name="_token" value="([^"]+)"/)?.[1];
    }

    async ingresar(usuario, clave) {
        const token = await this.token();
        const r = await this.pedir('/ingresar', {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ _token: token, nombre_usuario: usuario, password: clave }),
        });
        if (r.estado !== 302 || !r.destino?.endsWith('/panel')) {
            throw new Error(`No se pudo ingresar como ${usuario} (${r.estado})`);
        }
    }

    async consultar(codigo) {
        const token = await this.token('/');

        return this.pedir('/consultar', {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ _token: token, codigo_seguimiento: codigo }),
        });
    }
}

function resumen(nombre, muestras) {
    const tiempos = muestras.map((m) => m.ms).sort((a, b) => a - b);
    const percentil = (p) => tiempos[Math.min(tiempos.length - 1, Math.ceil((p / 100) * tiempos.length) - 1)];
    const errores = muestras.filter((m) => !m.ok).length;

    return {
        consulta: nombre,
        solicitudes: tiempos.length,
        errores,
        min_ms: +tiempos[0].toFixed(1),
        p50_ms: +percentil(50).toFixed(1),
        p95_ms: +percentil(95).toFixed(1),
        max_ms: +tiempos.at(-1).toFixed(1),
        promedio_ms: +(tiempos.reduce((a, b) => a + b, 0) / tiempos.length).toFixed(1),
        dentro_de_2s: tiempos.filter((t) => t <= LIMITE_MS).length,
    };
}

async function medir(sesion, ruta, esperado, repeticiones = REPETICIONES) {
    const muestras = [];
    for (let i = 0; i < repeticiones; i++) {
        const r = await sesion.pedir(ruta);
        muestras.push({ ms: r.ms, ok: r.estado === 200 && r.cuerpo.includes(esperado) });
    }

    return muestras;
}

const admin = process.env.VALTRAZA_ADMIN_USUARIO ?? 'admin';
const claveAdmin = process.env.VALTRAZA_ADMIN_CLAVE;
const claveCarga = process.env.VALTRAZA_CARGA_CLAVE;
const codigos = (process.env.VALTRAZA_CODIGOS ?? '').split(',').filter(Boolean);
if (!claveAdmin || !claveCarga || codigos.length < USUARIOS) {
    console.error('Faltan credenciales de prueba o al menos 15 códigos de seguimiento.');
    process.exit(2);
}

await mkdir(SALIDA, { recursive: true });
const inicioPrueba = new Date();

const administrador = new Sesion();
await administrador.ingresar(admin, claveAdmin);
const tecnico = new Sesion();
await tecnico.ingresar('carga01', claveCarga);
const lista = await tecnico.pedir('/mis-servicios');
const idPropio = lista.cuerpo.match(/mis-servicios\/(\d+)"/)?.[1];
const ficha = await administrador.pedir('/servicios');
const idServicio = ficha.cuerpo.match(/servicios\/(\d+)"/)?.[1];
const visitante = new Sesion();

const simples = [];
simples.push(resumen('GET / (inicio pública)', await medir(visitante, '/', 'Consultar mi servicio')));
simples.push(resumen('GET /servicios (supervisión, 1203 servicios)', await medir(administrador, '/servicios', 'Ver servicio')));
simples.push(resumen('GET /servicios?estado=en_curso', await medir(administrador, '/servicios?estado=en_curso', 'Ver servicio')));
simples.push(resumen('GET /servicios?asignacion=sin_tecnico', await medir(administrador, '/servicios?asignacion=sin_tecnico', 'Ver servicio')));
simples.push(resumen('GET /servicios/{id} (detalle supervisión)', await medir(administrador, `/servicios/${idServicio}`, 'Avance registrado')));
simples.push(resumen('GET /clientes?numero_identificacion=CARGA-C00150', await medir(administrador, '/clientes?numero_identificacion=CARGA-C00150', 'CARGA-C00150')));
simples.push(resumen('GET /mis-servicios (técnico)', await medir(tecnico, '/mis-servicios', 'mis-servicios/')));
simples.push(resumen('GET /mis-servicios/{id} (detalle técnico)', await medir(tecnico, `/mis-servicios/${idPropio}`, 'Historial')));
const consultasSecuenciales = [];
for (const codigo of codigos.slice(0, 10)) {
    const r = await new Sesion().consultar(codigo);
    consultasSecuenciales.push({ ms: r.ms, ok: r.estado === 200 && r.cuerpo.includes('Resumen del avance') });
}
simples.push(resumen('POST /consultar (consulta pública por código)', consultasSecuenciales));

await new Promise((r) => setTimeout(r, 61000));

const sesiones = [];
for (let n = 1; n <= 10; n++) {
    const s = new Sesion();
    await s.ingresar(`carga${String(n).padStart(2, '0')}`, claveCarga);
    sesiones.push({ s, rol: 'tecnico' });
}
for (let n = 1; n <= 5; n++) {
    const s = new Sesion();
    await s.ingresar(admin, claveAdmin);
    sesiones.push({ s, rol: 'administrador' });
}

const visitantes = Array.from({ length: USUARIOS }, () => new Sesion());
const tokens = await Promise.all(visitantes.map((v) => v.token('/')));
const inicioPublicas = performance.now();
const publicas = await Promise.all(visitantes.map((v, i) => v.pedir('/consultar', {
    method: 'POST',
    headers: { 'content-type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ _token: tokens[i], codigo_seguimiento: codigos[i] }),
}).then((r) => ({ ms: r.ms, ok: r.estado === 200 && r.cuerpo.includes('Resumen del avance'), estado: r.estado }))));
const duracionPublicas = performance.now() - inicioPublicas;

const rutasTecnico = ['/mis-servicios', '/mis-servicios?estado=en_curso', '/mis-servicios?estado=completado'];
const rutasAdmin = ['/servicios', '/servicios?estado=pendiente', '/servicios?asignacion=sin_tecnico', `/servicios/${idServicio}`, '/clientes'];
const inicioMixtas = performance.now();
const mixtas = (await Promise.all(sesiones.map(async ({ s, rol }, indice) => {
    const muestras = [];
    for (let i = 0; i < REPETICIONES; i++) {
        const rutas = rol === 'tecnico' ? rutasTecnico : rutasAdmin;
        const r = await s.pedir(rutas[(indice + i) % rutas.length]);
        muestras.push({ ms: r.ms, ok: r.estado === 200, estado: r.estado });
    }

    return muestras;
}))).flat();
const duracionMixtas = performance.now() - inicioMixtas;

const informe = {
    fecha_inicio: inicioPrueba.toISOString(),
    fecha_fin: new Date().toISOString(),
    destino: BASE,
    metodo: 'Cliente HTTP de Node.js (fetch) contra el sitio HTTPS servido por Herd (Nginx + PHP-FPM 8.3) y PostgreSQL 18 en el mismo equipo. Cada tiempo mide desde el envío de la solicitud hasta recibir el cuerpo completo del HTML. No incluye el renderizado del navegador.',
    equipo: { sistema: `${os.type()} ${os.release()}`, cpu: os.cpus()[0].model, nucleos: os.cpus().length, memoria_gb: +(os.totalmem() / 1024 ** 3).toFixed(1), node: process.version },
    limite_ms: LIMITE_MS,
    consultas_simples_un_usuario: simples,
    concurrencia: {
        usuarios_simultaneos: USUARIOS,
        consultas_publicas_simultaneas: { ...resumen(`${USUARIOS} POST /consultar al mismo tiempo, una por visitante`, publicas), duracion_total_ms: +duracionPublicas.toFixed(1), estados: [...new Set(publicas.map((p) => p.estado))] },
        consultas_autenticadas_simultaneas: { ...resumen(`${USUARIOS} sesiones (10 técnicos y 5 administradores) con ${REPETICIONES} consultas cada una en paralelo`, mixtas), duracion_total_ms: +duracionMixtas.toFixed(1), estados: [...new Set(mixtas.map((p) => p.estado))] },
    },
    limitaciones: [
        'Cliente y servidor comparten el mismo equipo; no se mide la latencia de red real.',
        'Todas las solicitudes salen de la misma IP; la consulta pública admite 30 solicitudes por minuto por IP, por eso la ráfaga pública usa una consulta por visitante y se espera un minuto antes de la prueba simultánea.',
        'Se usa el entorno local de pruebas (APP_ENV=local, APP_DEBUG=true) con datos ficticios.',
    ],
};

await writeFile(path.join(SALIDA, `CP071_CP072_C${CICLO}_rendimiento.json`), JSON.stringify(informe, null, 2));
console.table(simples.map(({ consulta, solicitudes, errores, p50_ms, p95_ms, max_ms }) => ({ consulta, solicitudes, errores, p50_ms, p95_ms, max_ms })));
console.table([informe.concurrencia.consultas_publicas_simultaneas, informe.concurrencia.consultas_autenticadas_simultaneas].map(({ consulta, solicitudes, errores, p50_ms, p95_ms, max_ms, duracion_total_ms }) => ({ consulta, solicitudes, errores, p50_ms, p95_ms, max_ms, duracion_total_ms })));
