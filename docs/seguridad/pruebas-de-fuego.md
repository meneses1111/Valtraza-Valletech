# Resultado de las pruebas de fuego

## Revalidación local del 14 de noviembre

Las pruebas de desarrollo se realizaron en una copia aislada basada en `300ed7b`, con datos ficticios en `valtraza_test`. En la repetición posterior a los ajustes locales se verificaron **409 pruebas y 918 aserciones**, **11/11** comprobaciones de negocio por HTTP y **76/76** comprobaciones de Chrome en cuatro resoluciones. El selector de cliente ya cabía a 390 × 844. Los cambios de móvil, listados y bloqueo se probaron en desarrollo y no forman parte del ciclo 3 de Luis.

| Escenario posterior a los ajustes locales | Resultado |
| --- | --- |
| 15 usuarios autenticados | 300/300 correctas, p95 362,68 ms, máximo 564,94 ms. Pasa la ERS y supera la meta adicional de 200 ms. |
| Carga sostenida de 60 segundos | 2.700/2.700 correctas, p95 190,17 ms. |
| Cuatro picos de 200 solicitudes | 800/800 correctas, p95 entre 714,73 y 812,36 ms. Pasa un segundo y supera 200 ms. |
| Bloqueo de registro | 503 controlado a los 811,55 ms, con recuperación 200. Las pruebas de cortes y transacciones no dejaron filas parciales. |

El ciclo 3 es una ejecución separada en el equipo de Luis, con métricas y alcance propios. Consulta sus [resultados y evidencias](../pruebas/estado-y-evidencias.md). Los apartados siguientes conservan la cronología anterior.
