# Estado y evidencias

Esta página distingue lo reportado por Luis en el ciclo 1, la comprobación automática de la corrección y la aceptación del ciclo 2. No reemplaza su matriz ni modifica los resultados originales.

## Versiones de referencia

| Hito | Referencia |
| --- | --- |
| Código funcional del ciclo 1 | `1f2f3a2aa4647dfa537b2b40535ebe653b620431` |
| SHA ejecutado por Luis para el ciclo 1 | `db1221d1fdc39e70c906896c4d36a9e03f7ddab3` |
| Entrega de pruebas integrada mediante PR #16 | `381394c14f9653b7bcb04507d28ef5142d1d496f` |
| Corrección integrada mediante PR #17 | `90fa6a2e3e9a16ce0ec7acc432bd7283977e460e` |
| SHA ejecutado por Luis para el ciclo 2 | `6fc7b13ebc29c474b7c02a332b60c588a04107ac` |
| Entrega del ciclo 2 integrada mediante PR #18 | `6cd526f1a696ad2e35f9955e0fd444cd643980f7` |

## Ciclo 1, realizado por Luis

| Estado | Casos |
| --- | ---: |
| Aprobados | 78 |
| Fallidos | 1: CP005 |
| Bloqueados | 0 |
| Sin ejecutar | 0 |
| Total | 79: CP001–CP079 |

Los casos relacionan RF01–RF14 y RNF01–RNF09. La suite de ese ciclo contiene **368 pruebas y 677 aserciones**; sus dos ejecuciones en CI se conservaron junto con la matriz.

Los recorridos reportados registran 204 comprobaciones en Chrome y Firefox, a 1366 × 768 y 1920 × 1080. Las evidencias incluyen seguridad HTTP/HTTPS, cookies, CSRF, copia/restauración y recuperación de servicios.

## Rendimiento medido en el ciclo 1

| Medición | Resultado registrado |
| --- | --- |
| Consulta simple más lenta | 205,6 ms |
| Ráfaga de 15 consultas públicas | 0 errores; máximo 479,5 ms |
| 15 sesiones autenticadas, 20 consultas por sesión | 300 solicitudes; 0 errores; máximo 579,7 ms |

Cliente y servidor compartían el equipo de pruebas de Luis. Los tiempos miden la recepción del HTML completo, no su renderizado ni la latencia de una red externa. La consulta pública se midió respetando su limitador por IP. Estos resultados no se presentan como una garantía de capacidad en otro hardware.

## Corrección de CP005

La [Issue #14](https://github.com/meneses1111/Valtraza-Valletech/issues/14) se corrigió agregando una guía fija de activación con enlace a `/activar`, sin cambiar el error genérico ni revelar la existencia o estado de una cuenta.

El ajuste visual CP070 separa los botones de confirmación. Las otras sugerencias de la [Issue #15](https://github.com/meneses1111/Valtraza-Valletech/issues/15), CP004 y CP032, mantienen sus textos.

El CI de la corrección integrada pasó **372 pruebas y 711 aserciones, dos veces seguidas**, además de Pint y la auditoría de dependencias. Son las 368 pruebas previas más cuatro comprobaciones de regresión del aviso.

## Ciclo 2, validado por Luis

| Estado en el ciclo 2 | Casos |
| --- | ---: |
| Aprobados | 10: CP001–CP008, CP026 y CP070 |
| Fallidos | 0 |
| Bloqueados | 0 |
| No repetidos en este ciclo | 69; conservan su resultado del ciclo 1 |

CP005 pasó de fallido a aprobado. La guía enlaza a la activación y la respuesta no distingue cuentas sin activar, usuarios inexistentes o contraseñas incorrectas. CP070 conserva una separación de 8 px entre botones en ambos navegadores y resoluciones. También se repitió CP026 porque cambió su pantalla.

La suite pasó **376 pruebas y 727 aserciones, dos veces seguidas**. Los recorridos registran 224 comprobaciones correctas en Chrome y Firefox, y las comprobaciones HTTP/HTTPS de seguridad suman 24 resultados correctos. El CI del PR #18 confirmó la suite dos veces sobre su commit 81b9edc.

Luis cerró la Issue #14 tras comprobar la corrección; la #15 conserva las mejoras opcionales de texto CP004 y CP032. Los resultados originales del ciclo 1 permanecen intactos. El ciclo 2 no se presenta como una repetición de los 79 casos: se reejecutaron los 10 indicados y la suite automatizada completa.

## Dónde consultar las fuentes

- [PR #16: entrega del ciclo 1](https://github.com/meneses1111/Valtraza-Valletech/pull/16).
- [Matriz oficial diligenciada](https://github.com/meneses1111/Valtraza-Valletech/blob/6cd526f1a696ad2e35f9955e0fd444cd643980f7/pruebas/matriz/Matriz_Pruebas_Valtraza.xlsx).
- [Casos y resultados del ciclo 1](https://github.com/meneses1111/Valtraza-Valletech/blob/381394c14f9653b7bcb04507d28ef5142d1d496f/pruebas/matriz/casos.json).
- [Evidencias del ciclo 1](https://github.com/meneses1111/Valtraza-Valletech/tree/381394c14f9653b7bcb04507d28ef5142d1d496f/pruebas/evidencias/ciclo-1).
- [Medición de rendimiento](https://github.com/meneses1111/Valtraza-Valletech/blob/381394c14f9653b7bcb04507d28ef5142d1d496f/pruebas/evidencias/ciclo-1/rendimiento/CP071_CP072_C1_rendimiento.json).
- [PR #17: corrección](https://github.com/meneses1111/Valtraza-Valletech/pull/17).
- [CI de la corrección integrada](https://github.com/meneses1111/Valtraza-Valletech/actions/runs/42683491546).
- [Cómo repetir las pruebas](https://github.com/meneses1111/Valtraza-Valletech/blob/90fa6a2e3e9a16ce0ec7acc432bd7283977e460e/pruebas/README.md).

- [PR #18: entrega del ciclo 2](https://github.com/meneses1111/Valtraza-Valletech/pull/18).
- [Evidencias del ciclo 2](https://github.com/meneses1111/Valtraza-Valletech/tree/6cd526f1a696ad2e35f9955e0fd444cd643980f7/pruebas/evidencias/ciclo-2).
- [CI del ciclo 2](https://github.com/meneses1111/Valtraza-Valletech/actions/runs/42687598679).
