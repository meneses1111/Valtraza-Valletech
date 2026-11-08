# Pruebas y entrega de resultados

Luis es responsable de planificar los casos, ejecutarlos y conservar las evidencias. Trabaja en `pruebas` sobre una versión de `develop` acordada y registra el SHA que ejecutó. Los cambios funcionales y sus correcciones corresponden a Johan en ramas propias.

La matriz oficial se conserva en una sola hoja y mantiene su formato original. Lleva proyecto, fecha DD-MM-AAAA y autor con rol. Incluye número y descripción del requisito; CP numerados desde CP001; pasos; entradas; resultado esperado; ambiente; usuario, fecha, resultado obtenido y estado de cada uno de los dos ciclos; y observaciones. Un ejemplo del formato no agrega funciones al alcance.

La cobertura debe relacionar RF01–RF14 y RNF01–RNF09 con casos exitosos, errores, permisos y condiciones de integridad aplicables. No asignar aprobado a casos sin ejecución. Un caso bloqueado debe indicar la causa y qué hace falta para repetirlo.

La suite automatizada existente comprende EsquemaTest, AccesoTest y PanelTest. Su ejecución controla esquema/ENUM, acceso y redirección al panel; no acredita cobertura completa de todos los módulos. Las comprobaciones locales de renderizado o validación del desarrollador tampoco completan los ciclos de la matriz.

## Evidencias y defectos

Cada ejecución identifica fecha, rol, versión de PHP/PostgreSQL/navegador, SHA y datos ficticios usados. Nombrar las evidencias con CP y ciclo; retirar contraseñas, códigos activos y datos personales de las capturas compartidas. Los resultados de pruebas sobre los scripts de base no se presentan como mediciones de la aplicación web.

Cada defecto se registra con requisito y CP, pasos reproducibles, entradas, resultado esperado y obtenido, gravedad, entorno, SHA y evidencia. Las mejoras de presentación se distinguen de incumplimientos y se priorizan antes de cambiar el alcance.

## Correcciones y segundo ciclo

Johan revisa el reporte, corrige en `correccion/<tema>`, valida y propone integrar a `develop`. Luis actualiza su rama sin perder sus casos, registra el nuevo SHA y repite el caso junto con las regresiones relacionadas. El ciclo 1 se conserva; el ciclo 2 documenta la nueva ejecución y la referencia de la corrección. Un defecto se cierra cuando la corrección se ha comprobado.

La entrega incluye matriz original diligenciada, evidencias, resumen de aprobados/fallidos/bloqueados/no ejecutados, enlaces a defectos y PR de pruebas, e instrucciones para repetir las pruebas automáticas. Los resultados de HTTPS, recuperación, navegador, resoluciones y rendimiento deben provenir de ejecuciones reales, con sus límites y método registrados.
