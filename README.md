# Valtraza · ValleTech

Aplicación web para gestionar servicios de mantenimiento y reparación de computadores: recepción del equipo, asignación del técnico, diagnósticos, actividades, repuestos, supervisión y seguimiento del cliente.

El cliente consulta con un código de seguimiento y no necesita cuenta. Administrador y técnico ingresan con cuentas activadas y permisos propios.

## Documentación

**[Abrir el GitBook de Valtraza](https://meneses1111.github.io/Valtraza-Valletech/)** — documentación pública con índice y búsqueda.

- [Inicio de la documentación](docs/README.md) y [índice completo](docs/SUMMARY.md).
- [Instalación y operación](docs/instalacion/README.md).
- [Guía de uso por rol](docs/uso/README.md).
- [Solución de problemas](docs/instalacion/solucion-de-problemas.md).
- [Estado y evidencias de pruebas](docs/pruebas/estado-y-evidencias.md).

El GitBook está publicado en GitHub Pages. El workflow **Revisar GitBook** comprueba las páginas, la navegación y los recursos antes de desplegar desde `main`; después verifica por HTTPS las páginas públicas y sus recursos. Las ejecuciones también conservan una vista previa descargable.

## Arquitectura

El sistema se organiza en seis módulos: Clientes y equipos, Técnicos, Cuentas y acceso, Servicios, Mantenimientos y reparaciones, y Consulta y seguimiento.

`CuentasController` usa `Acceso` para crear cuentas de técnicos. `RegistroTecnicos` registra a las personas y Servicios consulta ese registro para asignarlas. `FichaClienteController` usa `HistorialEquipos` para la ficha administrativa. El técnico modifica sus servicios asignados y puede leer las actividades completadas del mismo equipo. Diagnósticos, trabajos y repuestos se guardan en modelos Eloquent distintos.

Consulta el [diagrama revisado y la comparación con GitDiagram](docs/arquitectura/analisis-gitdiagram.md).

## Entorno de la aplicación

Laravel 12, PHP 8.3, PostgreSQL 18, Blade y Bootstrap 5.3. El entorno de pruebas de referencia usa Nginx con HTTPS (Herd en Windows). Los recursos de interfaz están incluidos en `public/assets`; no hace falta compilar JavaScript para iniciar la aplicación.

Cada integrante configura su propio `.env`. Las pruebas automáticas requieren una base exclusiva porque recrean tablas y tipos ENUM. Seguir la [guía de instalación](docs/instalacion/README.md) antes de ejecutar migraciones o pruebas.

## Estado y contribuciones

`develop` reúne el avance del equipo y la versión estable se integra a `main` mediante un PR. Luis mantiene la matriz y las evidencias de los ciclos de pruebas en `pruebas`.

El alcance corresponde a la ERS definitiva 1.1, de RF01 a RF14 y de RNF01 a RNF09. El [estado de pruebas](docs/pruebas/estado-y-evidencias.md) reúne los tres ciclos. El [PR #47](https://github.com/meneses1111/Valtraza-Valletech/pull/47) contiene el plan, el informe y la evidencia del ciclo 3.
