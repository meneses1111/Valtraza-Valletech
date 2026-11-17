# Prueba de aceptación: ciclo 3

| Dato | Valor |
| --- | --- |
| Fecha | 17-11-2026 |
| Ejecutó | Luis Eduardo Ramos Giraldo (responsable de pruebas) |
| Versión | `pruebas` SHA `42d7d07` |
| Navegador | Google Chrome 157 |
| Evidencias | `pruebas/evidencias/ciclo-3/aceptacion/` |

Escenario: un cliente lleva su computador al taller, el administrador registra el servicio y lo asigna, el técnico lo diagnostica y lo completa, y el cliente consulta el avance sin cuenta.

| # | Criterio de aceptación | Rol | CP | Requisito | Resultado obtenido | Estado | Evidencia |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A1 | El administrador registra un cliente nuevo y se abre su ficha | Administrador | CP019 | RF04 | Se registró el cliente «Cliente Aceptación» (ACEP-001, aceptacion@ejemplo.test) y se abrió su ficha | Aprobado | [A1_A2_CP019_CP023_C3_chrome_ficha-cliente-equipo.png](../evidencias/ciclo-3/aceptacion/A1_A2_CP019_CP023_C3_chrome_ficha-cliente-equipo.png) |
| A2 | El administrador registra un computador del cliente y recibe un código EQ-NNNN | Administrador | CP023 | RF05 | Se registró el computador EQ-0460 (Lenovo ThinkPad, 17/11/2026) en la ficha del cliente, sin servicios | Aprobado | [A1_A2_CP019_CP023_C3_chrome_ficha-cliente-equipo.png](../evidencias/ciclo-3/aceptacion/A1_A2_CP019_CP023_C3_chrome_ficha-cliente-equipo.png) |
| A3 | El administrador registra el servicio: queda pendiente, sin técnico, con código SRV-NNNN y código de seguimiento | Administrador | CP026 | RF06 | Se registró el servicio SRV-1210 «Mantenimiento de aceptación» del 17/11/2026 para EQ-0460, en estado Pendiente y con código de seguimiento para el cliente (oculto en la evidencia) | Aprobado | [A3_CP026_C3_chrome_servicio-registrado.png](../evidencias/ciclo-3/aceptacion/A3_CP026_C3_chrome_servicio-registrado.png) |
| A4 | El administrador asigna un técnico y el servicio aparece en «Mis servicios» del técnico | Administrador / Técnico | CP031, CP035 | RF07, RF08 | El administrador asignó a Técnico Carga 1. Al ingresar como carga01, el menú muestra solo «Mis servicios» y el técnico abre SRV-1210 en estado Pendiente, con las opciones para registrar el diagnóstico y cambiar el estado | Aprobado | [A4_CP031_C3_chrome_tecnico-asignado.png](../evidencias/ciclo-3/aceptacion/A4_CP031_C3_chrome_tecnico-asignado.png), [A4_CP035_C3_chrome_servicio-en-mis-servicios.png](../evidencias/ciclo-3/aceptacion/A4_CP035_C3_chrome_servicio-en-mis-servicios.png) |
| A5 | El técnico registra el diagnóstico y pasa el servicio a «En curso» | Técnico | CP039, CP047 | RF09, RF12 | Se mostró «Diagnóstico registrado.» con el resultado, las pruebas, las observaciones y la fecha 17/11/2026 13:54. Luego se mostró «Estado actualizado.» y el servicio pasó de Pendiente a En curso | Aprobado | [A5_CP039_C3_chrome_diagnostico-registrado.png](../evidencias/ciclo-3/aceptacion/A5_CP039_C3_chrome_diagnostico-registrado.png), [A5_CP047_C3_chrome_estado-en-curso.png](../evidencias/ciclo-3/aceptacion/A5_CP047_C3_chrome_estado-en-curso.png) |
| A6 | El técnico completa el servicio | Técnico | CP049 | RF12 | Con el diagnóstico ya registrado, el técnico cambió el estado y el servicio quedó en Completado | Aprobado | [A6_CP049_C3_chrome_servicio-completado.png](../evidencias/ciclo-3/aceptacion/A6_CP049_C3_chrome_servicio-completado.png) |
| A7 | El administrador ve el servicio completado y su avance en supervisión | Administrador | CP052, CP054 | RF13 | El administrador abre SRV-1210 en estado Completado, con el cliente, el computador, el técnico encargado y el diagnóstico en «Avance registrado» | Aprobado | [A7_CP054_C3_chrome_supervision-completado.png](../evidencias/ciclo-3/aceptacion/A7_CP054_C3_chrome_supervision-completado.png) |
| A8 | El cliente, sin cuenta, consulta con el código de seguimiento y ve el estado sin datos personales | Cliente | CP056, CP057 | RF14 | Sin iniciar sesión y con el código de seguimiento, la consulta muestra «Mantenimiento de aceptación», el computador EQ-0460, el estado Completado y el resumen del avance. No muestra el nombre, la identificación ni el correo del cliente, ni datos del técnico o del diagnóstico | Aprobado | [A8_CP056_CP057_C3_chrome_consulta-publica.png](../evidencias/ciclo-3/aceptacion/A8_CP056_CP057_C3_chrome_consulta-publica.png) |

Estado: Aprobado, Fallido, Bloqueado o No ejecutado.

## Resultado

8 de 8 criterios aprobados. El escenario completo funciona de principio a fin con los tres actores: administrador, técnico y cliente sin cuenta.

## Observaciones

- Las capturas que muestran el código de seguimiento lo tienen tapado.
- Datos usados: cliente ficticio ACEP-001, computador EQ-0460 y servicio SRV-1210 en la base de pruebas `valtraza_pruebas`.

