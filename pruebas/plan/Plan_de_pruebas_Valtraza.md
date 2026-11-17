# Plan de pruebas de software: Valtraza

## Historial de versiones

| Versión | Fecha | Elaboró | Cambios |
| --- | --- | --- | --- |
| 1.0 | 16-11-2026 | Luis Eduardo Ramos Giraldo | Versión inicial del plan para el ciclo 3 |
| 1.1 | 17-11-2026 | Luis Eduardo Ramos Giraldo | Ajuste a las secciones de la plantilla de pmoinformatica y resultados de la ejecución |
| 1.2 | 17-11-2026 | Luis Eduardo Ramos Giraldo | Ajustes por la revisión del analista: nuevo SHA `73b5190` con la corrección de la Issue #48, nueva prueba de CP070, ejecución de CP073, prueba complementaria de CP072, salida de PHPUnit guardada, detalle por caso y enlaces |

## Ficha del proyecto

| Dato | Valor |
| --- | --- |
| Proyecto | Valtraza: sistema web para registrar y seguir los servicios de mantenimiento y reparación de computadores (equipo ValleTech) |
| Área | Universidad Santiago de Cali, asignatura Administración de la Información (ADI01), periodo 2026B, Proyecto Final de SW |
| Líder del proyecto | Dxien Antonio Valencia Páramo |
| Patrocinador | Jair Enrique Sanclemente Castro, profesor de la asignatura |
| Líder de pruebas | Luis Eduardo Ramos Giraldo (responsable de pruebas) |
| Versión del sistema probada | rama `pruebas`: SHA `42d7d07` (develop `300ed7b`) y, para la nueva prueba y la regresión, SHA `73b5190` (develop con la corrección de la Issue #48) |
| Ciclo | Ciclo 3 (16 y 17-11-2026) |

## Aprobaciones

| Nombre | Cargo | Área | Firma | Fecha |
| --- | --- | --- | --- | --- |
| Luis Eduardo Ramos Giraldo | Responsable de pruebas (elabora) | Pruebas | | |
| Dxien Antonio Valencia Páramo | Líder del proyecto y analista (aprueba el plan y los criterios de aceptación) | Análisis | | |
| Johan Uriel Meneses Córdoba | Programador (revisa ambientes y correcciones) | Desarrollo | | |

## 1. Resumen ejecutivo

Este plan detallado define las pruebas del ciclo 3 de Valtraza, un sistema web en Laravel 12, PHP 8.3 y PostgreSQL 18 con tres actores: administrador, técnico y cliente sin cuenta. El ciclo verifica la versión que integra los cambios del programador del 12 al 14-11-2026 (develop `300ed7b`). Cubre RF01–RF14 y RNF01–RNF09 de la ERS 1.1 con seis tipos de prueba: de sistema, de integración y de aceptación (funcionales), y de rendimiento, de seguridad y de usabilidad (no funcionales). Restricciones: un solo responsable de pruebas, ambiente local, datos ficticios y dos días de ejecución. Objetivo: decidir si la versión cumple los criterios de aceptación y reportar los defectos encontrados.

## 2. Alcance de las pruebas

### 2.1 Elementos de prueba

| Módulo | Requisitos |
| --- | --- |
| Acceso y cuentas | RF01, RF03 |
| Técnicos | RF02 |
| Clientes y computadores | RF04, RF05 |
| Servicios y asignación | RF06, RF07 |
| Trabajo del técnico | RF08–RF12 |
| Supervisión | RF13 |
| Consulta pública | RF14 |

### 2.2 Nuevas funcionalidades a probar

Cambios del programador del 12 al 14-11-2026, vistos desde el usuario:

| Cambio | Requisito | Casos (CP) |
| --- | --- | --- |
| Listados con paginación de 15 | RF02–RF05, RF13 | CP013, CP052 |
| Mensajes de ingreso y asignación ajustados al mockup | RF01, RF07 | CP004, CP005, CP032 |
| Límites de intentos: ingreso, activación (5 por minuto) y consulta pública (10 por minuto) | RF01, RF03, RF14 | CP008, CP016, CP059 |
| Historial completo del computador para el técnico | RF08 | CP037 |
| Ayudas mientras se llena un formulario | RNF04 | CP020 |
| Mejoras en diagnóstico, avisos y comprobante | RF06, RF09 | CP026, CP039 |
| Control de acceso y sesiones reforzado; cabeceras de seguridad | RNF01–RNF03 | CP006, CP038, CP069 |

### 2.3 Pruebas de regresión

Todo lo demás se vuelve a probar con las suites completas: PHPUnit (408 pruebas), los recorridos con Puppeteer (224 comprobaciones), la seguridad HTTP (25) y la carga.

Como el programador cambió la configuración de la base de datos (tiempos de espera) y la gestión de sesiones, también se repiten la copia y restauración de la base (CP066) y la recuperación tras la caída del servidor web (CP078) y de la base de datos (CP079).

Si `develop` recibe una corrección durante el ciclo, se registra el nuevo SHA y se repiten el caso afectado y la regresión (criterio 3.3). Así ocurrió con la Issue #48: en `73b5190` se repitieron CP070, la suite completa de PHPUnit, la carga (CP071 y CP072) y CP073.

El ciclo usa como unidad de medida los 79 casos de la matriz oficial (CP001–CP079).

### 2.4 Funcionalidades a no probar

| Qué | Por qué | Riesgo |
| --- | --- | --- |
| Navegadores distintos de Chrome y Firefox, y celulares | La ERS no los pide | Bajo |
| El comprobante impreso en papel | No está en la ERS | Bajo |

### 2.5 Enfoque de pruebas (estrategia)

| Grupo | Tipo | Qué verifica | Herramienta | Requisitos |
| --- | --- | --- | --- | --- |
| Funcional | Del sistema | Recorridos completos por la aplicación, como un usuario real | Puppeteer (`recorridos.mjs`) en Chrome y Firefox | RF01–RF14, RNF07, RNF08 |
| Funcional | De integración | Rutas, controladores y base de datos trabajando juntos | PHPUnit (`tests/Feature`) | RF01–RF14, RNF02 |
| Funcional | De aceptación | Que cada función cumple lo que pide la ERS y el mockup | Ejecución manual en el navegador, con la matriz | RF01–RF14 |
| No funcional | De rendimiento | Consulta ≤ 2 s y 15 usuarios a la vez | Node (`carga.mjs`) | RNF05 |
| No funcional | De seguridad | Contraseñas, roles, HTTPS, CSRF y cookies | PHPUnit y Node (`seguridad-http.mjs`) | RNF01, RNF02, RNF03 |
| No funcional | De usabilidad | Opciones ordenadas y mensajes claros | Revisión manual con lista de chequeo frente al mockup | RNF04, RNF08 |

## 3. Criterios de aceptación o rechazo

### 3.1 Criterios de aceptación o rechazo

Se acepta la versión si se cumplen todos estos criterios:

- 100 % de los casos principales aprobados: el escenario de aceptación A1–A8 y los RF01–RF14.
- Suites automáticas 100 % en verde: PHPUnit, Puppeteer y seguridad HTTP.
- RNF05 cumplido: consulta simple en 2 s o menos y 15 usuarios a la vez sin errores.
- Ningún defecto crítico ni alto abierto. Los de severidad media o baja se aceptan si quedan reportados en una Issue y no bloquean el uso.

Se rechaza la versión si falla un caso principal, si hay un defecto crítico o alto, si no se cumple RNF05 o si la restauración pierde datos.

| Severidad | Significado |
| --- | --- |
| Crítica | El sistema no funciona o se pierden datos |
| Alta | Falla una función principal |
| Media | Falla una función secundaria y hay forma de rodearla |
| Baja | Detalle visual o de texto |

### 3.2 Criterios de suspensión

- Herd o PostgreSQL caídos más de 30 minutos.
- Un defecto que bloquee más del 20 % de los casos.
- Una versión nueva en `develop` durante la ejecución: se reinicia con el nuevo SHA.

### 3.3 Criterios de reanudación

- El ambiente vuelve a estar bien: PHPUnit en verde y la página de inicio responde 200.
- El defecto bloqueante está corregido e integrado en `develop`.
- Se registra el nuevo SHA y se repiten los casos afectados más la regresión.

## 4. Entregables

1. Este plan de pruebas, en Markdown y PDF.
2. El informe de ejecución en el Excel de la plantilla, con la hoja «Detalle ciclo 3» (los 79 casos con fecha, versión, herramienta, resultado, estado y evidencia).
3. Las hojas de aceptación (A1–A8) y de usabilidad (U1–U9).
4. Las evidencias del ciclo 3: capturas, JSON, TXT y la salida de PHPUnit (JUnit y testdox).
5. Los scripts de prueba ajustados: Puppeteer, seguridad y carga.
6. El reporte del defecto U8 (Issue #48) y su verificación después de la corrección.

## 5. Recursos

### 5.1 Requerimientos de entornos: hardware

Mínimo para ejecutar las pruebas:

| Componente | Mínimo | Recomendado |
| --- | --- | --- |
| Procesador | 4 núcleos, 64 bits | 6 núcleos o más |
| Memoria RAM | 8 GB | 16 GB |
| Disco libre | 10 GB | 20 GB, en SSD |
| Sistema operativo | Windows 10 u 11 de 64 bits | — |
| Red | Local: la aplicación, la base y los navegadores corren en el mismo equipo | — |

Equipo donde se ejecutó el ciclo 3: AMD Ryzen 5 5600X (6 núcleos), 32 GB de RAM y Windows 10 Pro (19045).

Los tiempos de rendimiento se midieron en ese equipo. En uno de menor capacidad pueden ser más altos, así que RNF05 debe volver a medirse en el servidor definitivo antes de pasar a producción.

### 5.2 Requerimientos de entornos: software

| Componente | Versión |
| --- | --- |
| PHP | 8.3.36 |
| Laravel | 12.71.0 |
| PostgreSQL | 18.6 |
| Laravel Herd (Nginx y HTTPS local) | 2.1.1 |
| Node.js | 24.22.0 |
| Google Chrome | 157 |
| Mozilla Firefox | 160 |
| Git | 2.53.0 |

### 5.3 Herramientas de pruebas requeridas

| Herramienta | Uso |
| --- | --- |
| PHPUnit 11.5 | Pruebas unitarias (92) y de integración (316) |
| Puppeteer 24.10 | Pruebas de sistema en Chrome y Firefox, con capturas |
| Scripts en Node.js | Seguridad HTTP (`seguridad-http.mjs`) y rendimiento (`carga.mjs`) |
| pg_dump, pg_restore y psql | Copia, restauración y consultas a la base |
| GitHub Actions | Integración continua: PHPUnit en cada subida |
| GitHub Issues | Registro y seguimiento de defectos |
| Herramientas de desarrollador del navegador (F12) | Revisión manual de cookies, cabeceras y redirecciones |

### 5.4 Personal

| Integrante | Rol en las pruebas |
| --- | --- |
| Luis Eduardo Ramos Giraldo | Responsable de pruebas: diseña, ejecuta y reporta |
| Dxien Antonio Valencia Páramo | Líder y analista: aprueba los criterios de aceptación |
| Johan Uriel Meneses Córdoba | Programador: corrige los defectos y mantiene el ambiente |
| Juan Sebastián Ramos Giraldo | Diseñador: aclara las dudas de usabilidad frente al mockup |

### 5.5 Entrenamiento

- Responsable de pruebas: manejo de PHPUnit, Puppeteer y PostgreSQL. Se cubrió con un repaso práctico el 16-11-2026.
- Todo el equipo: conocer la ERS 1.1 y el mockup.
- Programador: leer los reportes de defectos (Issues) y seguir el flujo de corrección en ramas `correccion/<tema>`.

## 6. Planificación y organización

### 6.1 Procedimientos para las pruebas

1. Preparar: actualizar la rama `pruebas` desde `develop`, aplicar las migraciones y registrar el SHA.
2. Verificar el ambiente: PHPUnit en verde y la página de inicio responde 200.
3. Ejecutar las pruebas automáticas: integración (PHPUnit), sistema (Puppeteer), seguridad, rendimiento y recuperación.
4. Ejecutar las pruebas manuales: aceptación y usabilidad, con su hoja y capturas, con los códigos de seguimiento tapados.
5. Registrar cada resultado como Aprobado, Fallido, Bloqueado o No ejecutado. La primera ejecución se conserva.
6. Analizar cada fallo. Si es del sistema, se abre una Issue `[CPxxx][gravedad]` para el programador. Si es del script de prueba, se ajusta el script sin tocar el código de la aplicación y se repite.
7. Volver a probar: cuando el programador integra la corrección, se repiten el caso afectado y la regresión.
8. Informar: se llena el informe de ejecución.

### 6.2 Matriz de responsabilidades

| Actividad | Luis (pruebas) | Johan (programador) | Dxien (líder y analista) | Juan Sebastián (diseñador) |
| --- | --- | --- | --- | --- |
| Elaborar el plan | R | C | A | I |
| Preparar el ambiente | R | C | I | — |
| Pruebas automáticas | R | I | I | — |
| Prueba de aceptación | R | I | A | — |
| Prueba de usabilidad | R | I | I | C |
| Reportar defectos | R | I | I | I |
| Corregir defectos | C | R | A | — |
| Volver a probar | R | I | I | — |
| Informe de ejecución | R | I | A | I |

_R: responsable, A: aprueba, C: consultado, I: informado._

### 6.3 Cronograma

| Actividad | Fecha | Responsable |
| --- | --- | --- |
| Preparar el ambiente (rama, migraciones y SHA) | 16-11-2026 | Luis |
| Integración (PHPUnit) y sistema (Puppeteer, 22:14) | 16-11-2026 | Luis |
| Seguridad HTTP (00:20) y rendimiento (02:51) | 17-11-2026 | Luis |
| Aceptación, usabilidad y recuperación | 17-11-2026 | Luis |
| Reporte de defectos | 17-11-2026 | Luis |
| Plan e informe de ejecución | 17-11-2026 | Luis |
| Revisión del analista | 17-11-2026 | Dxien |
| Corrección del defecto U8 (15:13) y nueva prueba con regresión (desde las 16:57) | 17-11-2026 | Johan y Luis |
| Entrega | 17-11-2026, 23:59 | Luis |

### 6.4 Premisas

- Herd y PostgreSQL disponibles durante los dos días.
- `develop` no cambia durante la ejecución.
- Base de pruebas separada, con datos ficticios.
- La ERS 1.1 y el mockup son la referencia vigente.
- Un responsable de pruebas disponible los dos días, y el programador disponible para dudas.

### 6.5 Dependencias y riesgos

| Riesgo | Probabilidad | Impacto | Respuesta |
| --- | --- | --- | --- |
| Que los scripts de prueba queden desactualizados frente a cambios del código (ocurrió con 3 scripts) | Alta | Medio | Revisar los cambios antes de ejecutar, conservar la primera ejecución y ajustar solo los scripts |
| Tiempo corto con un solo responsable de pruebas | Alta | Alto | Priorizar los casos principales y automatizar todo lo posible |
| El rendimiento medido en local no representa el servidor real | Media | Medio | Repetir RNF05 en el servidor definitivo |
| Cambios en `develop` en medio de la ejecución | Media | Alto | Registrar el SHA y reiniciar si cambia |
| El límite de 10 consultas por minuto por IP bloquea a clientes que comparten red | Media | Medio | Reportarlo al programador como observación |
| Caída de Herd o de PostgreSQL | Baja | Alto | Recuperación probada (2 s) y copias con pg_dump |

## 7. Referencias

1. Valtraza: Especificación de Requisitos de Software (ERS), versión 1.1: https://drive.google.com/file/d/16QrJTRN7Bl9uvE1IO0KTaHA1Q66vNsWw/view
2. Valtraza: Mockup y patrones de interfaz: https://drive.google.com/file/d/1Rakk1tbUJEyloWoOoCMdHuMjAlCAFkwL/view
3. Valtraza: Arquitectura del sistema (https://drive.google.com/file/d/1ZzelE_Phl4G1ydPyd47DiINa9cCIak4z/view) y arquitectura de software MVC (https://drive.google.com/file/d/1gzQBSmN-L1IleR85OYAR-cnyCtYzTbri/view).
4. Valtraza: Matriz de pruebas, ciclos 1 y 2: https://github.com/meneses1111/Valtraza-Valletech/blob/pruebas/pruebas/matriz/Matriz_Pruebas_Valtraza.xlsx. El ciclo 3 está en `pruebas/matriz/casos.json` y en la hoja «Detalle ciclo 3» del informe.
5. Valtraza: Informe de ejecución del ciclo 3: https://github.com/meneses1111/Valtraza-Valletech/blob/pruebas/pruebas/plan/Informe_ejecucion_pruebas_Valtraza_C3.xlsx
6. Valtraza: Hojas de aceptación y usabilidad del ciclo 3 (anexos A y B, y `pruebas/plan/`).
7. Valtraza: Evidencias del ciclo 3: https://github.com/meneses1111/Valtraza-Valletech/tree/pruebas/pruebas/evidencias/ciclo-3
8. Issue #48, defecto U8: https://github.com/meneses1111/Valtraza-Valletech/issues/48
9. Repositorio del proyecto: https://github.com/meneses1111/Valtraza-Valletech, rama `pruebas`.
10. PMOInformatica, «Plantilla del plan de pruebas de software»: https://www.pmoinformatica.com/2014/05/plan-de-pruebas-de-software.html

## 8. Glosario

| Término | Definición |
| --- | --- |
| Caso de prueba (CP) | Pasos, datos y resultado esperado para comprobar una regla. Se numeran desde CP001 |
| Ciclo de pruebas | Una ronda completa de ejecución sobre una versión fija del sistema |
| Defecto | Diferencia entre lo que hace el sistema y lo que pide la ERS o el mockup |
| Severidad | Gravedad de un defecto: crítica, alta, media o baja |
| Regresión | Volver a probar lo que ya funcionaba, para ver que un cambio no lo dañó |
| Prueba de integración | Comprueba que rutas, controladores y base de datos funcionan juntos |
| Prueba de sistema | Comprueba la aplicación completa, como la usaría una persona |
| Prueba de aceptación | Comprueba que el sistema sirve para el proceso real del cliente |
| Evidencia | Captura o archivo que demuestra el resultado de una prueba |
| SHA | Identificador único de una versión del código en Git |
| Integración continua (CI) | Ejecución automática de las pruebas en cada subida a GitHub |
| HTTPS | Conexión cifrada entre el navegador y el servidor |
| CSRF | Ataque que envía formularios en nombre del usuario; se bloquea con un token (error 419) |
| 429 | Respuesta «Demasiados intentos»: el límite de seguridad frena las repeticiones |
| p50 / p95 | Tiempo dentro del cual responde el 50 % / el 95 % de las solicitudes |
| Huella (md5) | Resumen de los datos que sirve para comprobar que la copia y el original son idénticos |
