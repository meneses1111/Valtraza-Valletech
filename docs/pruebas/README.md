# Pruebas y entrega de resultados

Luis planifica y ejecuta los casos y conserva las evidencias. Trabaja en `pruebas` sobre una versión acordada de `develop` y registra el SHA que ejecutó. Johan prepara las correcciones funcionales e identifica la versión que Luis debe verificar.

La matriz oficial conserva su formato original, con requisito, caso, pasos, entradas, resultado esperado, ambiente, resultado observado y estado. El ciclo 3 se documenta en un informe de ejecución aparte, con su plan, las hojas de aceptación y usabilidad y sus evidencias. Consulta el [estado de los tres ciclos](estado-y-evidencias.md).

La cobertura debe relacionar RF01–RF14 y RNF01–RNF09 con casos exitosos, errores, permisos y condiciones de integridad aplicables. No asignar aprobado a casos sin ejecución. Un caso bloqueado debe indicar la causa y qué hace falta para repetirlo.

La suite automatizada integra las pruebas Unit y Feature de Luis incorporadas en el PR #16, junto con EsquemaTest, AccesoTest y PanelTest. El ciclo 1 registró 368 pruebas y 677 aserciones, comprobadas dos veces en CI. El procedimiento para repetirlas y sus límites están en [pruebas/README.md](https://github.com/meneses1111/Valtraza-Valletech/blob/develop/pruebas/README.md). La aprobación de la suite no reemplaza los resultados y las evidencias de los casos de la matriz.

## Evidencias y defectos

Cada ejecución identifica fecha, rol, versión de PHP/PostgreSQL/navegador, SHA y datos ficticios usados. Nombrar las evidencias con CP y ciclo; retirar contraseñas, códigos activos y datos personales de las capturas compartidas. Los resultados de pruebas sobre los scripts de base no se presentan como mediciones de la aplicación web.

Cada defecto se registra con requisito y CP, pasos reproducibles, entradas, resultado esperado y obtenido, gravedad, entorno, SHA y evidencia. Las mejoras de presentación se distinguen de incumplimientos y se priorizan antes de cambiar el alcance.

## Correcciones y repetición de casos

Johan revisa el reporte, corrige y valida en `develop` según el flujo acordado. Luis actualiza su rama sin perder sus casos, registra el nuevo SHA y repite el caso junto con las regresiones relacionadas. Cada repetición indica la versión y conserva el resultado original. Un defecto se cierra cuando la corrección se ha comprobado.

La entrega incluye matriz original diligenciada, evidencias, resumen de aprobados/fallidos/bloqueados/no ejecutados, enlaces a defectos y PR de pruebas, e instrucciones para repetir las pruebas automáticas. Los resultados de HTTPS, recuperación, navegador, resoluciones y rendimiento deben provenir de ejecuciones reales, con sus límites y método registrados.

## Corrección de CP005 y preparación del ciclo 2

La corrección de la [Issue #14](https://github.com/meneses1111/Valtraza-Valletech/issues/14) añadió al formulario de ingreso una guía fija con enlace a `/activar`, visible antes y después de un ingreso rechazado. En el ciclo 2, el error decía «No se pudo iniciar sesión con estos datos.». Desde el PR #36, el aviso es «Usuario o contraseña incorrectos.» para los mismos casos, de modo que no revela qué cuenta existe.

`AvisoActivacionLoginTest` comprueba la ayuda y los tres tipos de ingreso rechazado. En el ciclo 2, el ajuste de CP070 separó los botones de confirmación. Los textos de CP004 y CP032 se cambiaron posteriormente en el PR #36.

Luis completó el ciclo 2 sobre develop 90fa6a2 y ejecutó pruebas 6fc7b13. CP001–CP008, CP026 y CP070 quedaron aprobados; se conservaron la matriz y las evidencias del ciclo 1. El PR #18 incorporó sus resultados y cuatro pruebas nuevas: la suite pasó 376 pruebas y 727 aserciones dos veces en CI. Luis cerró la Issue #14 tras comprobar la corrección. Consulta el [estado y las evidencias](estado-y-evidencias.md).
