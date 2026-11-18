# Valtraza · ValleTech

Aplicación web para gestionar servicios de mantenimiento y reparación de computadores: recepción del equipo, asignación del técnico, diagnósticos, actividades, repuestos, supervisión y seguimiento del cliente.

El cliente consulta con un código de seguimiento y no necesita cuenta. Administrador y técnico ingresan con cuentas activadas y permisos propios.

## Documentación

La [ERS definitiva 1.1 y los documentos de diseño](documentacion/README.md) están disponibles en el repositorio para contrastarlos con la implementación.

**[Abrir el GitBook de Valtraza](https://meneses1111.github.io/Valtraza-Valletech/)** — documentación pública con índice y búsqueda.

- [Inicio de la documentación](docs/README.md) y [índice completo](docs/SUMMARY.md).
- [Instalación y operación](docs/instalacion/README.md).
- [Guía de uso por rol](docs/uso/README.md).
- [Solución de problemas](docs/instalacion/solucion-de-problemas.md).
- [Estado y evidencias de pruebas](docs/pruebas/estado-y-evidencias.md).

El GitBook está publicado en GitHub Pages. El workflow **Revisar GitBook** comprueba las páginas, la navegación y los recursos antes de desplegar desde `main`; después verifica por HTTPS las páginas públicas y sus recursos. Las ejecuciones también conservan una vista previa descargable.

## Arquitectura

El sistema se organiza en seis módulos: Clientes y equipos, Técnicos, Cuentas y acceso, Servicios, Mantenimientos y reparaciones, y Consulta y seguimiento.

### Correspondencia entre requisitos y código

Esta tabla identifica los seis módulos del diseño. Los controladores de cuentas y de técnicos tienen responsabilidades diferentes. El cliente no inicia sesión.

| Módulo del diseño | Requisitos | Controladores | Servicios y modelos principales |
| --- | --- | --- | --- |
| Clientes y equipos | RF04, RF05 | `ClientesEquipos/ClientesController`, `ClientesEquipos/EquiposController`, `Servicios/FichaClienteController` | `RegistroClientesEquipos`; `Cliente`, `Equipo`. La ficha utiliza `HistorialEquipos` para leer servicios anteriores. |
| Técnicos | RF02 | `Tecnicos/TecnicosController` | `RegistroTecnicos`; `Tecnico`. Registra personas; no crea cuentas. |
| Cuentas y acceso | RF01, RF03 | `AccesoController`, `CuentasController` | `Acceso`; `Usuario`. El administrador crea cuentas de técnicos; ambos roles activan e ingresan por el mismo acceso. |
| Servicios | RF06, RF07 | `Servicios/ServiciosController` | `GestionServicios`, `HistorialEquipos`; `Servicio`. Registra, genera códigos, asigna y reasigna; conserva estado y avances. |
| Mantenimientos y reparaciones | RF08–RF12 | `TrabajoTecnico/MisServiciosController`, `DiagnosticosController`, `TrabajosController`, `RepuestosController`, `EstadosController` | `RegistroTrabajoTecnico`, `LecturaAvance`, estados `Pendiente`, `EnCurso`, `Completado`; modelos distintos `Diagnostico`, `Trabajo`, `Repuesto`. Completar exige diagnóstico. |
| Consulta y seguimiento | RF13, RF14 | `Supervision/SupervisionController`, `Consulta/ConsultaController` | `SupervisionServicios`, `ConsultaPublica`; consulta `GestionServicios` y `LecturaAvance`. El cliente solo obtiene un resumen permitido por código. |

Las dependencias entre servicios de módulos van del consumidor al proveedor: Cuentas y acceso → Técnicos; Servicios → Clientes y equipos y Técnicos; Mantenimientos y reparaciones → Servicios; Consulta y seguimiento → Servicios y Mantenimientos y reparaciones. El computador de la consulta pública se obtiene a través de Servicios. El controlador de supervisión compone además el filtro de técnicos. La regla de diagnóstico pertenece a Mantenimientos y reparaciones; Servicios no depende de ese módulo.

Las ocho entidades de negocio son `Usuario`, `Tecnico`, `Cliente`, `Equipo`, `Servicio`, `Diagnostico`, `Trabajo` y `Repuesto`, en una sola base PostgreSQL. `Trabajo.tipo` distingue mantenimiento de reparación; no existen dos tablas de actividades. El cliente del servicio se obtiene por `Servicio → Equipo → Cliente`. `sessions` es infraestructura de Laravel, no una novena entidad de negocio. Las capas son Presentación, Control, Lógica de negocio, Acceso a datos y Datos; un módulo puede tener varios controladores.

`CuentasController` usa `Acceso` para crear cuentas de técnicos. `RegistroTecnicos` registra a las personas y Servicios consulta ese registro para asignarlas. `FichaClienteController` usa `HistorialEquipos` para la ficha administrativa. El técnico modifica sus servicios asignados y puede leer las actividades completadas del mismo equipo. Diagnósticos, trabajos y repuestos se guardan en modelos Eloquent distintos.

Consulta el [diagrama revisado y la comparación con GitDiagram](docs/arquitectura/analisis-gitdiagram.md).

## Entrega y sustentación

- [Concordancia entre ERS, diseño, código y entregas](docs/cierre/concordancia.md).
- [Manual de usuario](docs/uso/manual-de-usuario.md).
- [Guion del video de storytelling y recorrido de sustentación](docs/cierre/storytelling.md).
- [Enlaces públicos y archivos de entrega](docs/cierre/README.md).

## Entorno de la aplicación

Laravel 12, PHP 8.3, PostgreSQL 18, Blade y Bootstrap 5.3. El entorno de pruebas de referencia usa Nginx con HTTPS (Herd en Windows). Los recursos de interfaz están incluidos en `public/assets`; no hace falta compilar JavaScript para iniciar la aplicación.

Cada integrante configura su propio `.env`. Las pruebas automáticas requieren una base exclusiva porque recrean tablas y tipos ENUM. Seguir la [guía de instalación](docs/instalacion/README.md) antes de ejecutar migraciones o pruebas.

## Estado y contribuciones

`develop` reúne el avance del equipo y la versión estable se integra a `main` mediante un PR. Luis mantiene la matriz y las evidencias de los ciclos de pruebas en `pruebas`.

El alcance corresponde a la ERS definitiva 1.1, de RF01 a RF14 y de RNF01 a RNF09. El [estado de pruebas](docs/pruebas/estado-y-evidencias.md) reúne los tres ciclos. El [PR #47](https://github.com/meneses1111/Valtraza-Valletech/pull/47) contiene el plan, el informe y la evidencia del ciclo 3.
