import puppeteer from 'puppeteer-core';
import { mkdir, writeFile } from 'node:fs/promises';
import { randomBytes } from 'node:crypto';
import path from 'node:path';

const BASE = process.env.VALTRAZA_URL ?? 'https://valtraza.test';
const CICLO = process.env.VALTRAZA_CICLO ?? '1';
const SALIDA = path.resolve(process.env.VALTRAZA_EVIDENCIAS ?? `../evidencias/ciclo-${CICLO}/navegadores`);
const ADMIN = process.env.VALTRAZA_ADMIN_USUARIO ?? 'admin';
const ADMIN_CLAVE = process.env.VALTRAZA_ADMIN_CLAVE;
const ADMIN_CODIGO = process.env.VALTRAZA_ADMIN_CODIGO;
const SOLO = process.env.VALTRAZA_NAVEGADORES?.split(',') ?? ['chrome', 'firefox'];

const NAVEGADORES = {
    chrome: { browser: 'chrome', executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe' },
    firefox: {
        browser: 'firefox',
        executablePath: 'C:\\Program Files\\Mozilla Firefox\\firefox.exe',
        extraPrefsFirefox: { 'security.enterprise_roots.enabled': true, 'intl.accept_languages': 'es-CO, es' },
    },
};

const RESOLUCIONES = [
    { nombre: '1366x768', width: 1366, height: 768 },
    { nombre: '1920x1080', width: 1920, height: 1080 },
];

const MOCKUP = {
    login_incorrecto: 'Usuario o contraseña incorrectos',
    login_sin_activar: 'todavía no está activa',
    consulta_no_encontrada: 'No encontramos un servicio con ese código',
    sin_tecnico: 'Falta elegir un técnico',
    cliente_repetido: 'Este cliente ya existe',
    sin_diagnostico: 'No puedes completar el servicio sin registrar al menos un diagnóstico',
    sin_servicios: 'Todavía no tienes servicios asignados',
    filtro_sin_resultados_tecnico: 'No hay servicios con ese filtro',
    filtros_sin_resultados_admin: 'No hay servicios con esos filtros',
    acceso_no_autorizado: 'No tienes permiso',
};

if (!ADMIN_CLAVE) {
    console.error('Falta VALTRAZA_ADMIN_CLAVE.');
    process.exit(2);
}

const resultados = [];

function registrar(navegador, cp, paso, ok, detalle = {}) {
    resultados.push({ navegador, cp, paso, ok, ...detalle });
    console.log(`${ok ? 'OK   ' : 'FALLA'} [${navegador}] ${cp} ${paso}${detalle.nota ? ' · ' + detalle.nota : ''}`);
}

async function ocultarSecretos(pagina) {
    await pagina.evaluate(() => {
        document.querySelectorAll('.codigo-seguimiento').forEach((nodo) => { nodo.textContent = '[código oculto en la evidencia]'; });
        document.querySelectorAll('[data-copiar]').forEach((nodo) => nodo.setAttribute('data-copiar', ''));
        document.querySelectorAll('input[name="codigo_activacion"], input[name="codigo_seguimiento"]').forEach((nodo) => { nodo.value = nodo.value ? '••••••' : ''; });
    });
}

async function textos(pagina) {
    return pagina.evaluate(() => Array.from(document.querySelectorAll('.alert, .invalid-feedback, td[colspan], .text-danger'))
        .map((nodo) => nodo.innerText.trim()).filter(Boolean));
}

async function capturar(navegador, pagina, nombre, cps) {
    const medidas = [];
    for (const resolucion of RESOLUCIONES) {
        await pagina.setViewport({ width: resolucion.width, height: resolucion.height });
        await new Promise((r) => setTimeout(r, 250));
        await ocultarSecretos(pagina);
        const desborde = await pagina.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
        const archivo = `${cps}_C${CICLO}_${navegador}_${resolucion.nombre}_${nombre}.png`;
        await pagina.screenshot({ path: path.join(SALIDA, archivo) });
        medidas.push({ resolucion: resolucion.nombre, desborde_horizontal_px: desborde, archivo });
        registrar(navegador, resolucion.nombre === '1366x768' ? 'CP076' : 'CP077', `sin desplazamiento horizontal en ${nombre}`, desborde <= 0, { nota: `${desborde}px`, archivo });
    }
    await pagina.setViewport({ width: 1366, height: 768 });

    return medidas;
}

async function ir(pagina, ruta) {
    const inicio = Date.now();
    const respuesta = await pagina.goto(BASE + ruta, { waitUntil: 'load' });

    return { estado: respuesta?.status(), ms: Date.now() - inicio };
}

const avisosConfirmacion = [];

async function cerrarConfirmacion(pagina) {
    const anunciado = await pagina.evaluate(() => document.getElementById('confirmacion')?.dataset.mostrar === 'si');

    if (!anunciado) {
        return null;
    }

    await pagina.waitForFunction(() => document.getElementById('confirmacion')?.classList.contains('show'), { timeout: 5000 });
    await new Promise((r) => setTimeout(r, 400));
    const titulo = await pagina.$eval('#confirmacion-titulo', (nodo) => nodo.textContent.trim());
    avisosConfirmacion.push(titulo);
    await pagina.click('#confirmacion .modal-footer button');
    await pagina.waitForFunction(() => !document.querySelector('.modal-backdrop') && !document.getElementById('confirmacion').classList.contains('show'), { timeout: 5000 });
    await new Promise((r) => setTimeout(r, 300));

    return titulo;
}

async function enviar(pagina, selectorBoton = 'main form[method="post"] button[type="submit"]') {
    await Promise.all([pagina.waitForNavigation({ waitUntil: 'load' }), pagina.click(selectorBoton)]);
    await cerrarConfirmacion(pagina);
}

async function llenar(pagina, campos) {
    for (const [selector, valor] of Object.entries(campos)) {
        await pagina.$eval(selector, (nodo) => { nodo.value = ''; });
        if (valor !== '') {
            await pagina.type(selector, String(valor));
        }
    }
}

async function sinValidacionNativa(pagina) {
    await pagina.evaluate(() => document.querySelectorAll('form').forEach((formulario) => formulario.setAttribute('novalidate', '')));
}

async function ingresar(pagina, usuario, clave) {
    await ir(pagina, '/ingresar');
    await llenar(pagina, { '#nombre_usuario': usuario, '#password': clave });
    await enviar(pagina);
}

async function salir(pagina) {
    await Promise.all([pagina.waitForNavigation({ waitUntil: 'load' }), pagina.click('header form button[type="submit"]')]);
}

function contiene(lista, texto) {
    return lista.some((linea) => linea.includes(texto));
}

async function activarAdministrador(navegador, pagina) {
    await ir(pagina, '/activar');
    await llenar(pagina, { '#codigo_activacion': ADMIN_CODIGO, '#password': ADMIN_CLAVE, '#password_confirmation': ADMIN_CLAVE });
    await enviar(pagina);
    registrar(navegador, 'CP015', 'activación del administrador con el código del seeder', pagina.url().endsWith('/ingresar'));
}

async function recorrido(navegador) {
    const navegadorAbierto = await puppeteer.launch({ ...NAVEGADORES[navegador], headless: true, acceptInsecureCerts: false, args: navegador === 'chrome' ? ['--lang=es-CO'] : [] });
    const version = await navegadorAbierto.version();
    const pagina = await navegadorAbierto.newPage();
    await pagina.setViewport({ width: 1366, height: 768 });
    const sufijo = randomBytes(3).toString('hex');
    const pref = navegador === 'chrome' ? 'CH' : 'FF';
    const claveTecnico = 'Prueba-' + randomBytes(6).toString('hex');
    const medidas = {};

    registrar(navegador, 'CP074/CP075', `versión ${version}`, true);

    try {
        if (ADMIN_CODIGO && navegador === SOLO[0]) {
            await activarAdministrador(navegador, pagina);
        }

        const inicio = await ir(pagina, '/');
        registrar(navegador, 'CP056', 'página de inicio pública por HTTPS', inicio.estado === 200 && pagina.url().startsWith('https://'), { nota: `${inicio.ms} ms` });
        medidas.inicio = await capturar(navegador, pagina, 'inicio-publico', 'CP056');

        await ir(pagina, '/ingresar');
        const nativoVacio = await pagina.evaluate(() => document.querySelector('main form').checkValidity());
        registrar(navegador, 'CP003', 'el navegador impide enviar el ingreso vacío', nativoVacio === false);
        await sinValidacionNativa(pagina);
        await enviar(pagina);
        const avisoVacio = await textos(pagina);
        registrar(navegador, 'CP003', 'el servidor avisa campos vacíos', avisoVacio.length > 0, { mensajes: avisoVacio });
        await capturar(navegador, pagina, 'ingreso-vacio', 'CP003');

        await llenar(pagina, { '#nombre_usuario': ADMIN, '#password': 'Clave-equivocada' });
        await enviar(pagina);
        const avisoIncorrecto = await textos(pagina);
        registrar(navegador, 'CP004', 'credenciales incorrectas muestran aviso y no ingresan', pagina.url().endsWith('/ingresar') && avisoIncorrecto.length > 0, {
            mensajes: avisoIncorrecto,
            mockup: MOCKUP.login_incorrecto,
            coincide_mockup: contiene(avisoIncorrecto, MOCKUP.login_incorrecto),
        });
        await capturar(navegador, pagina, 'ingreso-incorrecto', 'CP004');

        await ingresar(pagina, ADMIN, ADMIN_CLAVE);
        registrar(navegador, 'CP001', 'administrador ingresa y llega al panel', pagina.url().endsWith('/panel'));
        await capturar(navegador, pagina, 'panel-administrador', 'CP001');

        await ir(pagina, '/tecnicos');
        await llenar(pagina, { '#nombre': `Técnica ${pref} ${sufijo}`, '#numero_identificacion': `${pref}T${sufijo}` });
        await enviar(pagina);
        registrar(navegador, 'CP009', 'registro de técnico', (await pagina.content()).includes(`${pref}T${sufijo}`));
        await capturar(navegador, pagina, 'tecnico-registrado', 'CP009');
        await llenar(pagina, { '#nombre': 'Duplicado', '#numero_identificacion': `${pref}T${sufijo}` });
        await enviar(pagina);
        const avisoTecnicoRepetido = await textos(pagina);
        registrar(navegador, 'CP011', 'identificación de técnico repetida', avisoTecnicoRepetido.length > 0, { mensajes: avisoTecnicoRepetido });
        await capturar(navegador, pagina, 'tecnico-repetido', 'CP011');

        await ir(pagina, '/cuentas/nueva');
        const idTecnico = await pagina.$$eval('#id_tecnico option', (opciones, nombre) => opciones.find((o) => o.textContent.includes(nombre))?.value, `Técnica ${pref} ${sufijo}`);
        await pagina.select('#id_tecnico', idTecnico);
        await llenar(pagina, { '#nombre_usuario': `tec${pref.toLowerCase()}${sufijo}` });
        await enviar(pagina);
        const codigoActivacion = await pagina.$eval('.alert .codigo-seguimiento', (nodo) => nodo.textContent.trim()).catch(() => null);
        registrar(navegador, 'CP013', 'cuenta creada con código de activación y estado pendiente', Boolean(codigoActivacion) && (await pagina.content()).includes('Pendiente de activar'));
        await capturar(navegador, pagina, 'cuenta-creada', 'CP013');

        await ir(pagina, '/clientes');
        await llenar(pagina, { '#nombre': `Cliente ${pref} ${sufijo}`, '#numero_identificacion': `${pref}C${sufijo}`, '#correo': `cliente.${sufijo}@ejemplo.test` });
        await enviar(pagina);
        const urlFicha = pagina.url();
        registrar(navegador, 'CP019', 'registro de cliente abre su ficha', /\/clientes\/\d+$/.test(urlFicha));
        await capturar(navegador, pagina, 'cliente-registrado', 'CP019');
        await ir(pagina, '/clientes');
        await llenar(pagina, { '#nombre': 'Repetido', '#numero_identificacion': `${pref}C${sufijo}` });
        await enviar(pagina);
        const avisoClienteRepetido = await textos(pagina);
        registrar(navegador, 'CP021', 'cliente repetido avisa y ofrece la ficha', contiene(avisoClienteRepetido, MOCKUP.cliente_repetido), { mensajes: avisoClienteRepetido });
        await capturar(navegador, pagina, 'cliente-repetido', 'CP021');

        await pagina.goto(urlFicha, { waitUntil: 'load' });
        await pagina.$eval('#fecha_registro', (nodo) => { nodo.value = '2026-11-09'; });
        await llenar(pagina, { '#modelo': `Portátil ${pref} ${sufijo}` });
        await enviar(pagina, 'form[action$="/equipos"] button[type="submit"]');
        registrar(navegador, 'CP023', 'registro de equipo con código automático', /EQ-\d{4,}/.test(await pagina.content()));
        await capturar(navegador, pagina, 'equipo-registrado', 'CP023');

        const idCliente = urlFicha.split('/').pop();
        await ir(pagina, `/servicios/nuevo?id_cliente=${idCliente}`);
        const idEquipo = await pagina.$$eval('#id_equipo option', (opciones) => opciones.find((o) => o.value)?.value);
        await pagina.select('#id_equipo', idEquipo);
        await pagina.$eval('#fecha', (nodo) => { nodo.value = '2026-11-09'; });
        await llenar(pagina, { '#nombre': `Servicio ${pref} ${sufijo}`, '#descripcion_problema': 'Se reinicia al abrir programas' });
        await enviar(pagina);
        const codigoSeguimiento = await pagina.$eval('.codigo-seguimiento', (nodo) => nodo.textContent.trim()).catch(() => null);
        const idServicio = pagina.url().match(/servicios\/(\d+)\/registrado/)?.[1];
        registrar(navegador, 'CP026', 'servicio registrado pendiente con códigos', Boolean(codigoSeguimiento) && (await pagina.content()).includes('Pendiente'));
        await capturar(navegador, pagina, 'servicio-registrado', 'CP026');

        await ir(pagina, '/servicios?asignacion=sin_tecnico');
        registrar(navegador, 'CP053', 'filtro sin técnico muestra el servicio nuevo', (await pagina.content()).includes(`Servicio ${pref} ${sufijo}`));
        await capturar(navegador, pagina, 'supervision-sin-tecnico', 'CP053');

        await ir(pagina, `/servicios/${idServicio}/asignar`);
        await sinValidacionNativa(pagina);
        await enviar(pagina);
        const avisoSinTecnico = await textos(pagina);
        registrar(navegador, 'CP032', 'asignar sin técnico muestra aviso', avisoSinTecnico.length > 0, { mensajes: avisoSinTecnico, mockup: MOCKUP.sin_tecnico, coincide_mockup: contiene(avisoSinTecnico, MOCKUP.sin_tecnico) });
        await capturar(navegador, pagina, 'asignar-sin-tecnico', 'CP032');
        await ir(pagina, `/servicios/${idServicio}/asignar`);
        await pagina.select('#id_tecnico', idTecnico);
        await enviar(pagina);
        registrar(navegador, 'CP031', 'asignación de técnico', (await pagina.content()).includes(`Técnica ${pref} ${sufijo}`));
        await capturar(navegador, pagina, 'servicio-asignado', 'CP031');

        await ir(pagina, '/servicios?estado=completado&id_tecnico=' + idTecnico);
        const avisoFiltros = await textos(pagina);
        registrar(navegador, 'CP053', 'filtros sin resultados muestran aviso', contiene(avisoFiltros, MOCKUP.filtros_sin_resultados_admin), { mensajes: avisoFiltros });
        await capturar(navegador, pagina, 'supervision-sin-resultados', 'CP053');

        await salir(pagina);
        registrar(navegador, 'CP006', 'cerrar sesión vuelve al ingreso', pagina.url().endsWith('/ingresar'));
        const panelTrasSalir = await ir(pagina, '/panel');
        registrar(navegador, 'CP006', 'después de salir el panel pide ingresar', pagina.url().endsWith('/ingresar'), { nota: String(panelTrasSalir.estado) });

        await ingresar(pagina, `tec${pref.toLowerCase()}${sufijo}`, claveTecnico);
        const avisoSinActivar = await textos(pagina);
        registrar(navegador, 'CP005', 'cuenta sin activar no ingresa', pagina.url().endsWith('/ingresar'), { mensajes: avisoSinActivar, mockup: MOCKUP.login_sin_activar, coincide_mockup: contiene(avisoSinActivar, MOCKUP.login_sin_activar) });
        await capturar(navegador, pagina, 'ingreso-sin-activar', 'CP005');

        await ir(pagina, '/activar');
        await llenar(pagina, { '#codigo_activacion': codigoActivacion, '#password': 'corta1', '#password_confirmation': 'corta1' });
        await sinValidacionNativa(pagina);
        await enviar(pagina);
        const avisoCorta = await textos(pagina);
        registrar(navegador, 'CP017', 'contraseña de menos de 8 caracteres es rechazada', avisoCorta.length > 0 && pagina.url().endsWith('/activar'), { mensajes: avisoCorta });
        await capturar(navegador, pagina, 'activacion-corta', 'CP017');
        await llenar(pagina, { '#codigo_activacion': codigoActivacion, '#password': claveTecnico, '#password_confirmation': claveTecnico });
        await enviar(pagina);
        registrar(navegador, 'CP015', 'activación con código válido', pagina.url().endsWith('/ingresar'), { mensajes: await textos(pagina) });
        await capturar(navegador, pagina, 'activacion-correcta', 'CP015');
        await ir(pagina, '/activar');
        await llenar(pagina, { '#codigo_activacion': codigoActivacion, '#password': claveTecnico, '#password_confirmation': claveTecnico });
        await enviar(pagina);
        const avisoReuso = await textos(pagina);
        registrar(navegador, 'CP016', 'el código ya usado es rechazado', contiene(avisoReuso, 'ya utilizado'), { mensajes: avisoReuso });
        await capturar(navegador, pagina, 'activacion-reutilizada', 'CP016');

        await ingresar(pagina, `tec${pref.toLowerCase()}${sufijo}`, claveTecnico);
        registrar(navegador, 'CP002', 'técnico ingresa y llega al panel', pagina.url().endsWith('/panel'));
        await capturar(navegador, pagina, 'panel-tecnico', 'CP002');

        await ir(pagina, '/mis-servicios');
        registrar(navegador, 'CP035', 'mis servicios muestra el asignado', (await pagina.content()).includes(`Servicio ${pref} ${sufijo}`));
        await capturar(navegador, pagina, 'mis-servicios', 'CP035');
        await ir(pagina, '/mis-servicios?estado=completado');
        const avisoFiltroTecnico = await textos(pagina);
        registrar(navegador, 'CP036', 'filtro sin resultados del técnico', contiene(avisoFiltroTecnico, MOCKUP.filtro_sin_resultados_tecnico), { mensajes: avisoFiltroTecnico });
        await capturar(navegador, pagina, 'mis-servicios-filtro-vacio', 'CP036');

        await ir(pagina, `/mis-servicios/${idServicio}/estado`);
        await pagina.select('#estado', 'completado');
        await enviar(pagina);
        const avisoSinDiagnostico = await textos(pagina);
        registrar(navegador, 'CP048', 'completar sin diagnóstico es rechazado', contiene(avisoSinDiagnostico, MOCKUP.sin_diagnostico), { mensajes: avisoSinDiagnostico });
        await capturar(navegador, pagina, 'estado-sin-diagnostico', 'CP048');

        await ir(pagina, `/mis-servicios/${idServicio}/diagnosticos`);
        await sinValidacionNativa(pagina);
        await enviar(pagina);
        const avisoDiagnosticoVacio = await textos(pagina);
        registrar(navegador, 'CP040', 'diagnóstico sin resultado muestra aviso', avisoDiagnosticoVacio.length > 0, { mensajes: avisoDiagnosticoVacio });
        await capturar(navegador, pagina, 'diagnostico-vacio', 'CP040');
        await ir(pagina, `/mis-servicios/${idServicio}/diagnosticos`);
        await llenar(pagina, { '#resultado': 'Memoria RAM con errores', '#pruebas_realizadas': 'Prueba de memoria' });
        await enviar(pagina);
        registrar(navegador, 'CP039', 'diagnóstico registrado', (await pagina.content()).includes('Memoria RAM con errores'));
        await capturar(navegador, pagina, 'diagnostico-registrado', 'CP039');

        await ir(pagina, `/mis-servicios/${idServicio}/trabajos`);
        await pagina.select('#tipo', 'reparacion');
        await llenar(pagina, { '#descripcion': 'Cambio del módulo de memoria' });
        await enviar(pagina);
        registrar(navegador, 'CP043', 'reparación registrada', (await pagina.content()).includes('Cambio del módulo de memoria'));
        await capturar(navegador, pagina, 'actividad-registrada', 'CP043');

        await ir(pagina, `/mis-servicios/${idServicio}/repuestos`);
        const campoNombre = await pagina.$('input[name$="[nombre]"]');
        const campoCantidad = await pagina.$('input[name$="[cantidad]"]');
        await campoNombre.type('Memoria RAM 8 GB');
        await campoCantidad.click({ clickCount: 3 });
        await campoCantidad.type('0');
        await sinValidacionNativa(pagina);
        await enviar(pagina);
        const avisoCantidad = await textos(pagina);
        registrar(navegador, 'CP046', 'cantidad cero es rechazada', avisoCantidad.length > 0, { mensajes: avisoCantidad });
        await capturar(navegador, pagina, 'repuesto-cantidad-cero', 'CP046');
        await ir(pagina, `/mis-servicios/${idServicio}/repuestos`);
        await (await pagina.$('input[name$="[nombre]"]')).type('Memoria RAM 8 GB');
        const cantidad = await pagina.$('input[name$="[cantidad]"]');
        await cantidad.click({ clickCount: 3 });
        await cantidad.type('1');
        await enviar(pagina);
        registrar(navegador, 'CP045', 'repuesto registrado', (await pagina.content()).includes('Memoria RAM 8 GB'));
        await capturar(navegador, pagina, 'repuesto-registrado', 'CP045');

        await ir(pagina, `/mis-servicios/${idServicio}/estado`);
        await pagina.select('#estado', 'completado');
        await enviar(pagina);
        registrar(navegador, 'CP049', 'completar con diagnóstico', (await pagina.content()).includes('Completado'));
        await capturar(navegador, pagina, 'estado-completado', 'CP049');

        const directo = await ir(pagina, '/servicios');
        registrar(navegador, 'CP063', 'técnico bloqueado por URL directa en supervisión', directo.estado === 403 && contiene(await pagina.evaluate(() => [document.body.innerText]), MOCKUP.acceso_no_autorizado), { nota: String(directo.estado) });
        await capturar(navegador, pagina, 'acceso-no-autorizado', 'CP063');

        await ir(pagina, '/mis-servicios');
        await salir(pagina);

        await ir(pagina, '/');
        await llenar(pagina, { '#codigo_seguimiento': codigoSeguimiento });
        await enviar(pagina, 'form[action$="/consultar"] button[type="submit"]');
        const contenidoConsulta = await pagina.evaluate(() => document.body.innerText);
        registrar(navegador, 'CP056', 'consulta pública con código válido', contenidoConsulta.includes(`Servicio ${pref} ${sufijo}`) && contenidoConsulta.includes('Completado'));
        registrar(navegador, 'CP057', 'consulta pública sin datos personales ni detalles técnicos', !contenidoConsulta.includes(`Cliente ${pref} ${sufijo}`) && !contenidoConsulta.includes('Memoria RAM con errores') && !contenidoConsulta.includes('Memoria RAM 8 GB') && !contenidoConsulta.includes(`Técnica ${pref}`));
        registrar(navegador, 'CP059', 'el código no aparece en la dirección', !pagina.url().includes(codigoSeguimiento));
        await capturar(navegador, pagina, 'consulta-resultado', 'CP056');

        await ir(pagina, '/');
        await llenar(pagina, { '#codigo_seguimiento': '0'.repeat(32) });
        await enviar(pagina, 'form[action$="/consultar"] button[type="submit"]');
        const avisoConsulta = await textos(pagina);
        registrar(navegador, 'CP058', 'código inexistente muestra aviso', contiene(avisoConsulta, MOCKUP.consulta_no_encontrada), { mensajes: avisoConsulta });
        await capturar(navegador, pagina, 'consulta-no-encontrada', 'CP058');
    } catch (error) {
        registrar(navegador, 'ERROR', 'el recorrido se interrumpió', false, { nota: error.message });
        await pagina.screenshot({ path: path.join(SALIDA, `ERROR_C${CICLO}_${navegador}.png`) }).catch(() => {});
    } finally {
        await navegadorAbierto.close();
    }

    return { navegador, version, medidas };
}

await mkdir(SALIDA, { recursive: true });
const versiones = [];
for (const navegador of SOLO) {
    versiones.push(await recorrido(navegador));
}
await writeFile(path.join(SALIDA, `resultados_C${CICLO}.json`), JSON.stringify({ fecha: new Date().toISOString(), base: BASE, versiones: versiones.map(({ navegador, version }) => ({ navegador, version })), avisos_confirmacion: [...new Set(avisosConfirmacion)], resultados }, null, 2));
const fallas = resultados.filter((r) => !r.ok).length;
console.log(`\n${resultados.length - fallas} comprobaciones correctas, ${fallas} fallidas.`);
process.exit(fallas ? 1 : 0);
