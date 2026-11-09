# Pruebas de Valtraza

Responsable: Luis Eduardo Ramos Giraldo, responsable de pruebas de software (equipo ValleTech).

## Versión probada

| Ciclo | Base de `develop` | SHA de `pruebas` ejecutado | Fecha |
| --- | --- | --- | --- |
| 1 | `1f2f3a2aa4647dfa537b2b40535ebe653b620431` | `db1221d1fdc39e70c906896c4d36a9e03f7ddab3` (código funcional idéntico a la base) | 09-11-2026 |
| 2 | `90fa6a2e3e9a16ce0ec7acc432bd7283977e460e` (corrección del PR #17) | `6fc7b13ebc29c474b7c02a332b60c588a04107ac` (código funcional idéntico a la base) | 09-11-2026 |

Entorno de los dos ciclos: Windows 10, Laravel Herd 2.1.1 (Nginx + PHP 8.3.36) con HTTPS en `https://valtraza.test`, PostgreSQL 18.6, Chrome 156.0.8078.81 y Firefox 159.0. Todos los datos son ficticios.

## Resumen del ciclo 1

79 casos (CP001–CP079) cubren RF01–RF14 y RNF01–RNF09.

| Estado | Casos |
| --- | --- |
| Aprobado | 78 |
| Fallido | 1 (CP005) |
| Bloqueado | 0 |
| No ejecutado | 0 |

- Defecto: [#14 [CP005][Baja] La cuenta sin activar recibe el aviso genérico y no indica cómo activarla](https://github.com/meneses1111/Valtraza-Valletech/issues/14).
- Mejoras sugeridas (no son defectos): [#15 Ajustes de texto y presentación frente al mockup](https://github.com/meneses1111/Valtraza-Valletech/issues/15).

Pruebas automáticas del ciclo 1: 368 pruebas y 677 aserciones en verde, en dos ejecuciones seguidas (Unit + Feature, incluidas las 18 existentes). Recorridos en navegador: 204 comprobaciones correctas en Chrome y Firefox, a 1366 × 768 y 1920 × 1080.

## Resumen del ciclo 2

Johan corrigió la Issue #14 y separó los botones de CP070 (Issue #15) en el [PR #17](https://github.com/meneses1111/Valtraza-Valletech/pull/17). Repetí CP005 y sus regresiones: CP001–CP008, CP026 (la pantalla «Servicio registrado» cambió) y CP070.

| Estado en el ciclo 2 | Casos |
| --- | --- |
| Aprobado | 10 (CP001–CP008, CP026 y CP070) |
| Fallido | 0 |
| Bloqueado | 0 |
| No ejecutado en el ciclo 2 | 69 (sin cambios que los afecten; conservan el resultado del ciclo 1) |

- CP005: la cuenta sin activar sigue sin ingresar, con el aviso genérico y la guía fija con enlace a `/activar`. La página es idéntica para una cuenta sin activar, un usuario inexistente y una contraseña incorrecta.
- CP070: «Asignar técnico» y «Ver servicios» quedan separados por 8 px en los dos navegadores y las dos resoluciones. Los textos de CP004 y CP032 se conservan; siguen como mejora opcional de la #15.
- Pruebas automáticas: 376 pruebas y 727 aserciones en verde, en dos ejecuciones seguidas sobre `valtraza_test`. Incluyen las 4 de Johan (`AvisoActivacionLoginTest`) y 4 nuevas mías en `InicioSesionTest`.
- Recorridos en navegador: 224 comprobaciones correctas y 0 fallidas, en Chrome y Firefox a 1366 × 768 y 1920 × 1080. Comprobaciones HTTP: 24 correctas.
- Las evidencias del ciclo 1 no cambiaron. Las del ciclo 2 están en `pruebas/evidencias/ciclo-2`.

## Contenido

| Ruta | Contenido |
| --- | --- |
| `tests/Unit` | Reglas aisladas: estados, contraseñas, validaciones, códigos, filtros de rol y cabeceras. |
| `tests/Feature` | Rutas, roles e integración con PostgreSQL por requisito. |
| `tests/Concerns/DatosDePrueba.php` | Datos ficticios compartidos por las pruebas. |
| `pruebas/navegadores` | Recorridos en Chrome y Firefox, medición de rendimiento y comprobaciones HTTPS. |
| `pruebas/recuperacion` | Copia y restauración, interrupción del servidor web y de PostgreSQL. |
| `pruebas/datos` | Carga de datos ficticios para medir rendimiento. |
| `pruebas/matriz` | Casos de la matriz (`casos.json`), script de llenado y matriz oficial diligenciada. |
| `pruebas/evidencias/ciclo-N` | Evidencias nombradas `CPxxx_CN_…`, sin contraseñas ni códigos activos. |

## Cómo repetir las pruebas

### 1. Pruebas automáticas (Unit y Feature)

Necesitan PHP 8.3 con `pdo_pgsql`, Composer 2 y una base PostgreSQL **exclusiva** para pruebas, porque la suite recrea tablas y tipos ENUM.

1. Crear un rol y una base vacía `valtraza_test` solo para pruebas.
2. Crear `.env.testing` (privado, ignorado por Git) con `DB_USERNAME` y `DB_PASSWORD` de ese rol. `phpunit.xml` ya selecciona `valtraza_test`.
3. Ejecutar:

```powershell
composer install
php vendor/bin/pint --test
php artisan test
php artisan test
```

Para guardar la evidencia del ciclo:

```powershell
php vendor/bin/phpunit --testdox-text pruebas/evidencias/ciclo-2/automatizadas/CP_todos_C2_testdox.txt --log-junit pruebas/evidencias/ciclo-2/automatizadas/CP_todos_C2_junit.xml
```

### 2. Recorridos en navegador, rendimiento y HTTPS

Requieren el sitio servido por HTTPS (Herd: `herd link valtraza --secure` y `herd isolate --site=valtraza 8.3`), Node.js 24, Chrome y Firefox estables, y una base de aplicación separada (por ejemplo `valtraza_pruebas`), migrada y con el administrador activado.

Las credenciales se pasan por variables de entorno desde un archivo privado fuera del repositorio:

```text
VALTRAZA_ADMIN_USUARIO=admin
VALTRAZA_ADMIN_CLAVE=…
VALTRAZA_CARGA_CLAVE=…
```

```powershell
cd pruebas/navegadores
npm ci
node recorridos.mjs
php ../datos/cargar_datos_ficticios.php
node --use-system-ca carga.mjs
node --use-system-ca seguridad-http.mjs
```

- `recorridos.mjs` usa `VALTRAZA_ADMIN_CODIGO` solo la primera vez, para activar al administrador creado por el seeder. Oculta los códigos antes de cada captura.
- `carga.mjs` necesita `VALTRAZA_CODIGOS` con al menos 15 códigos de seguimiento separados por comas. Se toman de la base de pruebas y no se guardan.
- `VALTRAZA_CICLO=2` dirige todas las evidencias a `pruebas/evidencias/ciclo-2`.

### 3. Copia, restauración y recuperación

Desde Git Bash, en la raíz del proyecto:

```bash
bash pruebas/recuperacion/copia_y_restauracion.sh
bash pruebas/recuperacion/interrupcion_y_recuperacion.sh web
bash pruebas/recuperacion/interrupcion_y_recuperacion.sh bd
```

- El respaldo se guarda en una carpeta privada fuera del repositorio.
- La restauración usa una base temporal `valtraza_restauracion`, que se elimina al final; la base activa no se modifica.
- El modo `bd` espera a que el servicio `postgresql-x64-18` se detenga y se reinicie, lo que requiere una ventana de PowerShell con permisos de administrador.

### 4. Matriz oficial

```powershell
python pruebas/matriz/llenar_matriz.py "<ruta al formato original del profesor>.xlsx" pruebas/matriz/Matriz_Pruebas_Valtraza.xlsx
```

El script conserva la hoja, las columnas y los estilos del formato original, reemplaza las filas de ejemplo por los casos de `casos.json` y completa proyecto, fecha y autor.

## Límites de las mediciones

- Rendimiento: cliente y servidor en el mismo equipo y una sola IP de origen. La consulta pública admite 30 solicitudes por minuto por IP, por eso la ráfaga simultánea usa una consulta por visitante.
- Recuperación de la base: se reinició el servicio local de PostgreSQL. La página de error genérica se comprobó con `APP_ENV=production` y `APP_DEBUG=false`; en local aparece la página de depuración de Laravel.
- La barra «Edit with Herd» que se ve en las capturas pertenece al entorno local de Herd, no a la aplicación.
