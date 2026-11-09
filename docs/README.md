# Valtraza

## Gestión y seguimiento de servicios técnicos

Valtraza es la aplicación web del equipo **ValleTech** para registrar y seguir servicios de mantenimiento y reparación de computadores. Reúne la recepción del equipo, la asignación del técnico, el trabajo realizado y la consulta del cliente en un mismo historial.

El administrador organiza la atención; el técnico registra el avance de los servicios que tiene asignados; el cliente consulta con un código de seguimiento, sin crear una cuenta.

## Empieza por tu objetivo

| Necesitas… | Lee primero |
| --- | --- |
| Entender el sistema y sus límites | [Primeros pasos](inicio/primeros-pasos.md) y [requisitos](requisitos/README.md) |
| Preparar una instalación | [Instalación y operación](instalacion/README.md) |
| Usar el sistema como administrador o técnico | [Guía de uso por rol](uso/README.md) |
| Consultar un servicio como cliente | [Consulta pública](uso/consulta-publica.md) |
| Revisar la calidad y los resultados | [Estado y evidencias](pruebas/estado-y-evidencias.md) |
| Resolver un problema de conexión o acceso | [Solución de problemas](instalacion/solucion-de-problemas.md) |

## Qué incluye

Registro de técnicos, cuentas y activación; clientes y computadores; servicios y asignación; diagnósticos, mantenimientos, reparaciones y repuestos; estados, supervisión e historial; seguimiento público por código.

El alcance acordado está en la **ERS definitiva 1.1**, con RF01–RF14 y RNF01–RNF09. No incluye cuentas de clientes, inventario, solicitudes virtuales, archivos adjuntos ni recuperación de contraseñas dentro de la aplicación.

## Base técnica

| Elemento | Tecnología |
| --- | --- |
| Aplicación | Laravel 12 y PHP 8.3 |
| Datos | PostgreSQL 18 |
| Interfaz | Blade y Bootstrap 5.3; recursos locales |
| Servidor de referencia | Nginx con HTTPS; Herd en el entorno Windows de pruebas |
| Pruebas | PHPUnit, Unit y Feature, más recorridos y evidencias de Luis |
| Documentación | GitBook 3.2.2 |

## Equipo ValleTech

| Integrante | Responsabilidad |
| --- | --- |
| Dxien Antonio Valencia Páramo | Análisis de requisitos |
| Juan Sebastián Ramos Giraldo | Diseño del sistema |
| Johan Uriel Meneses Córdoba | Programación |
| Luis Eduardo Ramos Giraldo | Pruebas de software |

## Estado de esta documentación

Esta edición acompaña al código funcional de `develop` **90fa6a2**, del 09-11-2026. El ciclo 2 fue validado por Luis e integrado mediante el PR #18 (6cd526f); la Issue #14 está cerrada. Los resultados de ambos ciclos se conservan en la matriz y sus evidencias. Una compilación correcta del libro no equivale a aprobar los requisitos ni a publicar la entrega estable.

Consulta el [índice completo](SUMMARY.md) o el [repositorio en GitHub](https://github.com/meneses1111/Valtraza-Valletech).
