# Concordancia de Valtraza

La referencia funcional es la ERS definitiva 1.1 del 26 de octubre de 2026. Esta revisión del 18 de noviembre relaciona el diseño con las rutas, controladores, servicios y modelos del repositorio. No reemplaza los resultados de Luis ni certifica una ejecución nueva.

## Requisitos funcionales

Las ubicaciones de controladores parten de `app/Http/Controllers` y las de servicios de `app/Services`.

| ERS | Módulo del diseño | Implementación de referencia |
| --- | --- | --- |
| RF01 | Cuentas y acceso | `AccesoController`, `Cuentas/Acceso` |
| RF02 | Técnicos | `Tecnicos/TecnicosController`, `Tecnicos/RegistroTecnicos` |
| RF03 | Cuentas y acceso | `CuentasController`, `Cuentas/Acceso` |
| RF04 | Clientes y equipos | `ClientesEquipos/ClientesController`, `RegistroClientesEquipos` |
| RF05 | Clientes y equipos | `ClientesEquipos/EquiposController`, `Servicios/FichaClienteController`, `Servicios/HistorialEquipos` |
| RF06 | Servicios | `Servicios/ServiciosController`, `Servicios/GestionServicios` |
| RF07 | Servicios | `Servicios/ServiciosController`, `Servicios/GestionServicios` |
| RF08 | Mantenimientos y reparaciones | `TrabajoTecnico/MisServiciosController`, `RegistroTrabajoTecnico`, `GestionServicios` |
| RF09 | Mantenimientos y reparaciones | `TrabajoTecnico/DiagnosticosController`, `RegistroTrabajoTecnico` |
| RF10 | Mantenimientos y reparaciones | `TrabajoTecnico/TrabajosController`, `RegistroTrabajoTecnico` |
| RF11 | Mantenimientos y reparaciones | `TrabajoTecnico/RepuestosController`, `RegistroTrabajoTecnico` |
| RF12 | Mantenimientos y reparaciones | `TrabajoTecnico/EstadosController`, `RegistroTrabajoTecnico`, `app/States/Servicios` |
| RF13 | Consulta y seguimiento | `Supervision/SupervisionController`, `Consulta/SupervisionServicios` |
| RF14 | Consulta y seguimiento | `Consulta/ConsultaController`, `Consulta/ConsultaPublica` |

## Lectura del diseño y del código

El Taller 1 representa seis módulos conceptuales. La implementación reparte sus operaciones entre varios controladores; la carpeta de un controlador no determina por sí sola la responsabilidad del módulo. `FichaClienteController`, ubicado en Servicios, compone la ficha del cliente con el historial. Los diagramas de módulos, capas, actores y datos responden preguntas distintas y no necesitan tener el mismo número de cajas.

El Taller 1 se revisó el 18 de noviembre para mostrar la supervisión administrativa en Consulta y seguimiento y las seis dependencias directas de consumo. El computador de la consulta pública se obtiene **a través de Servicios**; no existe una llamada directa desde `ConsultaPublica` a `RegistroClientesEquipos`. Supervisión obtiene también el registro de técnicos desde el controlador para su filtro.

El Taller 2 se revisó para indicar varios controladores por módulo y nombrar al modelo `Trabajo`. Sus cinco capas se conservan. Los códigos se generan durante el registro. Los observers de avances emiten eventos después de confirmar la transacción. La petición que guarda redirige al detalle; las otras pantallas ven los cambios al consultarse de nuevo, sin prometer actualización automática en tiempo real.

En los diseños de datos, la entidad «Mantenimiento y reparación» corresponde al modelo `Trabajo` y la tabla `trabajo`, con el atributo `tipo`. El servicio conserva `id_equipo`; el cliente se obtiene a través del computador. La tabla técnica `sessions` no cambia las ocho entidades del dominio.

## Reglas que deben coincidir en todas las entregas

- El cliente no crea una cuenta; utiliza el código de seguimiento.
- Administrador y técnico utilizan el mismo formulario de ingreso. Registrar una persona como técnico no crea su cuenta.
- Un técnico puede atender varios servicios. Solo opera los asignados y lee el historial de actividades anteriores completadas del mismo computador.
- Completar requiere un diagnóstico; no exige una reparación ni repuestos.
- Reasignar conserva estado, diagnósticos, trabajos y repuestos; el técnico anterior pierde el acceso operativo.
- La consulta pública muestra estado y un resumen general, sin datos personales ni descripciones técnicas.
- Los registros de avance se agregan; la interfaz no ofrece editarlos o borrarlos.

## Requisitos no funcionales y evidencia

RNF01 y RNF02 se relacionan con HTTPS, contraseñas y permisos; RNF03 con integridad y recuperación; RNF04 con usabilidad; RNF05 con dos segundos y quince usuarios concurrentes; RNF06 con MVC y documentación; RNF07 y RNF08 con navegadores y resoluciones; RNF09 con disponibilidad y recuperación. Los resultados y sus límites se conservan en [Estado y evidencias](../pruebas/estado-y-evidencias.md). No se trasladan mediciones locales a un servidor público sin verificarlas.

El cierre oficial del ciclo 3 registra 79/79 casos exitosos y 408 pruebas automáticas con 912 aserciones. Las cifras corresponden a las versiones registradas por Luis. Los cambios locales posteriores de rendimiento o manejo de bloqueos no quedan aceptados por esa evidencia automáticamente.

## Documentos de referencia

La [ERS y los talleres de diseño](https://github.com/meneses1111/Valtraza-Valletech/tree/main/documentacion) están publicados en `documentacion` del repositorio. La [arquitectura](../arquitectura/README.md), el [modelo de datos](../base-de-datos/modelo.md) y el [análisis de GitDiagram](../arquitectura/analisis-gitdiagram.md) explican su correspondencia con la implementación.
