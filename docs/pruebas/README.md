# Pruebas y entrega de resultados

Luis es responsable de planificar los casos, ejecutarlos y conservar las evidencias. Trabaja en `pruebas` sobre una versión de `develop` acordada y registra el SHA que ejecutó. Los cambios funcionales y sus correcciones corresponden a Johan en ramas propias.

La matriz oficial se conserva en una sola hoja y mantiene su formato original. Lleva proyecto, fecha DD-MM-AAAA y autor con rol. Incluye número y descripción del requisito; CP numerados desde CP001; pasos; entradas; resultado esperado; ambiente; usuario, fecha, resultado obtenido y estado de cada uno de los dos ciclos; y observaciones. Un ejemplo del formato no agrega funciones al alcance.

La cobertura debe relacionar RF01–RF14 y RNF01–RNF09 con casos exitosos, errores, permisos y condiciones de integridad aplicables. No asignar aprobado a casos sin ejecución. Un caso bloqueado debe indicar la causa y qué hace falta para repetirlo.

La suite automatizada integra las pruebas Unit y Feature de Luis incorporadas en el PR #16, junto con EsquemaTest, AccesoTest y PanelTest. El ciclo 1 registró 368 pruebas y 677 aserciones, comprobadas dos veces en CI. El procedimiento para repetirlas y sus límites están en [pruebas/README.md](https://github.com/meneses1111/Valtraza-Valletech/blob/develop/pruebas/README.md). La aprobación de la suite no reemplaza los resultados y las evidencias de los casos de la matriz.

## Evidencias y defectos

Cada ejecución identifica fecha, rol, versión de PHP/PostgreSQL/navegador, SHA y datos ficticios usados. Nombrar las evidencias con CP y ciclo; retirar contraseñas, códigos activos y datos personales de las capturas compartidas. Los resultados de pruebas sobre los scripts de base no se presentan como mediciones de la aplicación web.

Cada defecto se registra con requisito y CP, pasos reproducibles, entradas, resultado esperado y obtenido, gravedad, entorno, SHA y evidencia. Las mejoras de presentación se distinguen de incumplimientos y se priorizan antes de cambiar el alcance.

## Correcciones y segundo ciclo

Johan revisa el reporte, corrige en `correccion/<tema>`, valida y propone integrar a `develop`. Luis actualiza su rama sin perder sus casos, registra el nuevo SHA y repite el caso junto con las regresiones relacionadas. El ciclo 1 se conserva; el ciclo 2 documenta la nueva ejecución y la referencia de la corrección. Un defecto se cierra cuando la corrección se ha comprobado.

La entrega incluye matriz original diligenciada, evidencias, resumen de aprobados/fallidos/bloqueados/no ejecutados, enlaces a defectos y PR de pruebas, e instrucciones para repetir las pruebas automáticas. Los resultados de HTTPS, recuperación, navegador, resoluciones y rendimiento deben provenir de ejecuciones reales, con sus límites y método registrados.

## Corrección de CP005 y preparación del ciclo 2

La corrección de la [Issue #14](https://github.com/meneses1111/Valtraza-Valletech/issues/14) añade al formulario de ingreso una guía fija con enlace a /activar, visible antes y después de un ingreso rechazado. El error sigue siendo «No se pudo iniciar sesión con estos datos.» para cuentas sin activar, credenciales incorrectas y usuarios inexistentes; no se revela qué cuenta existe.

AvisoActivacionLoginTest comprueba la presencia inicial de la ayuda y los tres tipos de ingreso rechazado. La separación de los botones de confirmación de servicio atiende únicamente el ajuste visual CP070 de la Issue #15; los textos de CP004 y CP032 se conservan.

Estos cambios no modifican la matriz ni las evidencias del ciclo 1. Luis debe registrar el SHA integrado y ejecutar el ciclo 2: CP005, regresiones CP001–CP008 y CP070, y la suite completa. La Issue #14 permanece abierta hasta que Luis compruebe el resultado.
