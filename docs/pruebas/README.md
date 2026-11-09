# Pruebas y entrega de resultados

Luis es responsable de planificar los casos, ejecutarlos y conservar las evidencias. Trabaja en `pruebas` sobre una versi�n de `develop` acordada y registra el SHA que ejecut�. Los cambios funcionales y sus correcciones corresponden a Johan en ramas propias.

La matriz oficial se conserva en una sola hoja y mantiene su formato original. Lleva proyecto, fecha DD-MM-AAAA y autor con rol. Incluye n�mero y descripci�n del requisito; CP numerados desde CP001; pasos; entradas; resultado esperado; ambiente; usuario, fecha, resultado obtenido y estado de cada uno de los dos ciclos; y observaciones. Un ejemplo del formato no agrega funciones al alcance.

La cobertura debe relacionar RF01�RF14 y RNF01�RNF09 con casos exitosos, errores, permisos y condiciones de integridad aplicables. No asignar aprobado a casos sin ejecuci�n. Un caso bloqueado debe indicar la causa y qu� hace falta para repetirlo.

La suite automatizada integra las pruebas Unit y Feature de Luis incorporadas en el PR #16, junto con EsquemaTest, AccesoTest y PanelTest. El ciclo 1 registr� 368 pruebas y 677 aserciones, comprobadas dos veces en CI. El procedimiento para repetirlas y sus l�mites est�n en [pruebas/README.md](https://github.com/meneses1111/Valtraza-Valletech/blob/develop/pruebas/README.md). La aprobaci�n de la suite no reemplaza los resultados y las evidencias de los casos de la matriz.

## Evidencias y defectos

Cada ejecuci�n identifica fecha, rol, versi�n de PHP/PostgreSQL/navegador, SHA y datos ficticios usados. Nombrar las evidencias con CP y ciclo; retirar contrase�as, c�digos activos y datos personales de las capturas compartidas. Los resultados de pruebas sobre los scripts de base no se presentan como mediciones de la aplicaci�n web.

Cada defecto se registra con requisito y CP, pasos reproducibles, entradas, resultado esperado y obtenido, gravedad, entorno, SHA y evidencia. Las mejoras de presentaci�n se distinguen de incumplimientos y se priorizan antes de cambiar el alcance.

## Correcciones y segundo ciclo

Johan revisa el reporte, corrige en `correccion/<tema>`, valida y propone integrar a `develop`. Luis actualiza su rama sin perder sus casos, registra el nuevo SHA y repite el caso junto con las regresiones relacionadas. El ciclo 1 se conserva; el ciclo 2 documenta la nueva ejecuci�n y la referencia de la correcci�n. Un defecto se cierra cuando la correcci�n se ha comprobado.

La entrega incluye matriz original diligenciada, evidencias, resumen de aprobados/fallidos/bloqueados/no ejecutados, enlaces a defectos y PR de pruebas, e instrucciones para repetir las pruebas autom�ticas. Los resultados de HTTPS, recuperaci�n, navegador, resoluciones y rendimiento deben provenir de ejecuciones reales, con sus l�mites y m�todo registrados.

## Correcci�n de CP005 y preparaci�n del ciclo 2

La correcci�n de la [Issue #14](https://github.com/meneses1111/Valtraza-Valletech/issues/14) a�ade al formulario de ingreso una gu�a fija con enlace a /activar, visible antes y despu�s de un ingreso rechazado. El error sigue siendo �No se pudo iniciar sesi�n con estos datos.� para cuentas sin activar, credenciales incorrectas y usuarios inexistentes; no se revela qu� cuenta existe.

AvisoActivacionLoginTest comprueba la presencia inicial de la ayuda y los tres tipos de ingreso rechazado. La separaci�n de los botones de confirmaci�n de servicio atiende �nicamente el ajuste visual CP070 de la Issue #15; los textos de CP004 y CP032 se conservan.

Luis complet� el ciclo 2 sobre develop 90fa6a2 y ejecut� pruebas 6fc7b13. CP001�CP008, CP026 y CP070 quedaron aprobados; se conservaron la matriz y las evidencias del ciclo 1. El PR #18 incorpor� sus resultados y cuatro pruebas nuevas: la suite pas� 376 pruebas y 727 aserciones dos veces en CI. Luis cerr� la Issue #14 tras comprobar la correcci�n. Consulta el [estado y las evidencias](estado-y-evidencias.md).
