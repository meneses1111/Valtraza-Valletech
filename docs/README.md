# Valtraza

## Gesti�n y seguimiento de servicios t�cnicos

Valtraza es la aplicaci�n web del equipo **ValleTech** para registrar y seguir servicios de mantenimiento y reparaci�n de computadores. Re�ne la recepci�n del equipo, la asignaci�n del t�cnico, el trabajo realizado y la consulta del cliente en un mismo historial.

El administrador organiza la atenci�n; el t�cnico registra el avance de los servicios que tiene asignados; el cliente consulta con un c�digo de seguimiento, sin crear una cuenta.

## Empieza por tu objetivo

| Necesitas� | Lee primero |
| --- | --- |
| Entender el sistema y sus l�mites | [Primeros pasos](inicio/primeros-pasos.md) y [requisitos](requisitos/README.md) |
| Preparar una instalaci�n | [Instalaci�n y operaci�n](instalacion/README.md) |
| Usar el sistema como administrador o t�cnico | [Gu�a de uso por rol](uso/README.md) |
| Consultar un servicio como cliente | [Consulta p�blica](uso/consulta-publica.md) |
| Revisar la calidad y los resultados | [Estado y evidencias](pruebas/estado-y-evidencias.md) |
| Resolver un problema de conexi�n o acceso | [Soluci�n de problemas](instalacion/solucion-de-problemas.md) |

## Qu� incluye

Registro de t�cnicos, cuentas y activaci�n; clientes y computadores; servicios y asignaci�n; diagn�sticos, mantenimientos, reparaciones y repuestos; estados, supervisi�n e historial; seguimiento p�blico por c�digo.

El alcance acordado est� en la **ERS definitiva 1.1**, con RF01�RF14 y RNF01�RNF09. No incluye cuentas de clientes, inventario, solicitudes virtuales, archivos adjuntos ni recuperaci�n de contrase�as dentro de la aplicaci�n.

## Base t�cnica

| Elemento | Tecnolog�a |
| --- | --- |
| Aplicaci�n | Laravel 12 y PHP 8.3 |
| Datos | PostgreSQL 18 |
| Interfaz | Blade y Bootstrap 5.3; recursos locales |
| Servidor de referencia | Nginx con HTTPS; Herd en el entorno Windows de pruebas |
| Pruebas | PHPUnit, Unit y Feature, m�s recorridos y evidencias de Luis |
| Documentaci�n | GitBook 3.2.2 |

## Equipo ValleTech

| Integrante | Responsabilidad |
| --- | --- |
| Dxien Antonio Valencia P�ramo | An�lisis de requisitos |
| Juan Sebasti�n Ramos Giraldo | Dise�o del sistema |
| Johan Uriel Meneses C�rdoba | Programaci�n |
| Luis Eduardo Ramos Giraldo | Pruebas de software |

## Estado de esta documentaci�n

Esta edici�n acompa�a al c�digo funcional de `develop` **90fa6a2**, del 09-11-2026. El ciclo 2 fue validado por Luis e integrado mediante el PR #18 (6cd526f); la Issue #14 est� cerrada. Los resultados de ambos ciclos se conservan en la matriz y sus evidencias. Una compilaci�n correcta del libro no equivale a aprobar los requisitos ni a publicar la entrega estable.

Consulta el [�ndice completo](SUMMARY.md) o el [repositorio en GitHub](https://github.com/meneses1111/Valtraza-Valletech).
