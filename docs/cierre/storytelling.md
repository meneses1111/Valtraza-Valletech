# Guion de storytelling de Valtraza

Equipo ValleTech · Administración de la Información · Universidad Santiago de Cali

Versión 1.0 · 18 de noviembre de 2026

Este guion presenta el problema que resuelve Valtraza mediante la atención de un computador. Se reparte entre los cuatro integrantes y acompaña la narración con la aplicación real. La historia de Laura es un caso ficticio de demostración; no representa el testimonio de una clienta real.

La duración propuesta es de cinco minutos, ajustable al ensayar. No es un límite establecido en la consigna. Se siguen los siete elementos de la guía del curso: personaje, problema, guía, secuencia de pasos, desafío, fracaso que se evita y final. Las cifras del cierre se atribuyen al ciclo de pruebas y no se presentan como mediciones realizadas durante el video.

## Preparación de la grabación

Utilicen una instalación de demostración y datos ficticios. Preparen un cliente, un computador y un servicio con un técnico asignado; reserven otro servicio sin diagnóstico para mostrar la validación. Cada integrante graba su narración y, cuando corresponda, el recorrido de pantalla indicado. Todos deben verse o identificarse con su nombre al comenzar su intervención.

Graben la aplicación a 1366 × 768 o 1920 × 1080, con texto legible y sonido claro. Oculten contraseñas, códigos de activación y códigos de seguimiento. Para la consulta pública, introduzcan el código fuera de la toma y graben el resultado. Añadan subtítulos y comprueben los cortes antes de exportar.

## Escena 1 El personaje y su necesidad

Tiempo propuesto: 0:00 a 0:35. Elemento: personaje. Sección académica: introducción. Interviene Dxien Antonio Valencia Páramo.

Imagen: integrante frente a cámara; luego un computador y la portada de Valtraza. Rótulo discreto: Caso ficticio de demostración.

Narración:

«Laura necesita su computador para estudiar y entregar sus trabajos. Un día empieza a fallar y lo lleva a un servicio técnico. Dejar el equipo es solo el comienzo: necesita saber qué está pasando, quién lo está atendiendo y si ya puede recogerlo. Para contar cómo responde Valtraza a esa necesidad, seguiremos su atención desde la recepción hasta la consulta del resultado. Somos ValleTech y este es nuestro proyecto.»

## Escena 2 El problema que se quiere resolver

Tiempo propuesto: 0:35 a 1:10. Elemento: problema. Sección académica: problema de investigación. Continúa Dxien Antonio Valencia Páramo.

Imagen: recreación sencilla de notas separadas; después ficha del cliente y listado de servicios. No presentar la recreación como una investigación de campo.

Narración:

«Cuando los datos del cliente, el diagnóstico y el avance quedan dispersos, responder una pregunta sencilla puede requerir revisar varias notas. ¿A qué computador pertenece esta reparación? ¿Qué se hizo antes? ¿Quién tiene el servicio asignado? Nuestro problema de trabajo fue cómo organizar esa información para que cada persona pudiera consultar lo que le corresponde. A partir de esa necesidad definimos catorce requisitos funcionales y tres actores: administrador, técnico y cliente.»

## Escena 3 El equipo como guía

Tiempo propuesto: 1:10 a 1:45. Elemento: guía. Sección académica: objetivos. Interviene Juan Sebastián Ramos Giraldo.

Imagen: diagrama de los seis módulos y luego pantalla de ingreso. Resaltar la entrada pública del cliente separada del ingreso de cuentas.

Narración:

«Nuestro objetivo fue reunir el registro y el seguimiento de los servicios en una aplicación web. Diseñamos seis módulos y separamos sus responsabilidades: clientes y equipos, técnicos, cuentas y acceso, servicios, mantenimientos y reparaciones, y consulta y seguimiento. El administrador organiza la atención; el técnico registra el trabajo. Laura no tiene que crear una cuenta: recibe un código para consultar su servicio. Esta diferencia guía tanto los permisos como las pantallas del sistema.»

## Escena 4 Los pasos para organizar la atención

Tiempo propuesto: 1:45 a 2:35. Elemento: secuencia de pasos. Sección académica: metodología. Juan Sebastián Ramos Giraldo inicia y Johan Uriel Meneses Córdoba continúa después del cambio de plano.

Imagen: correspondencia de requisitos, diseño y código; ficha del cliente; nuevo servicio; confirmación y asignación. Ocultar el código de seguimiento.

Narración de Juan Sebastián:

«Trabajamos a partir de la especificación de requisitos. Después definimos los módulos, las cinco capas de la arquitectura MVC y las relaciones de los datos. La implementación y las pruebas permiten revisar esa correspondencia. El repositorio conserva el código y las evidencias, y el GitBook reúne la documentación para consultarlos.»

Narración de Johan:

«En el caso de Laura, el administrador registra primero sus datos y su computador. Luego crea el servicio, describe el problema y entrega el código de seguimiento. El servicio empieza pendiente. Desde la asignación selecciona al técnico encargado. Registrar a un técnico y crear su cuenta son pasos separados; cada técnico activa su cuenta y define su contraseña.»

## Escena 5 El desafío durante el trabajo

Tiempo propuesto: 2:35 a 3:25. Elemento: desafío. Sección académica: desarrollo. Continúa Johan Uriel Meneses Córdoba.

Imagen: Mis servicios; historial del equipo; formulario y registro del diagnóstico; mantenimiento o reparación y repuestos cuando correspondan.

Narración:

«El desafío es conservar la continuidad de la atención. El técnico encuentra el servicio en Mis servicios y consulta el historial de actividades anteriores completadas del computador. Registra el diagnóstico y, cuando corresponde, añade mantenimientos, reparaciones y repuestos utilizados. Cada registro queda asociado al mismo servicio. Si cambia el encargado, la información y el estado se conservan y el técnico anterior pierde el acceso operativo. La aplicación utiliza Laravel, vistas Blade y una base PostgreSQL; las reglas se comprueban en el servidor.»

## Escena 6 El riesgo y la comprobación

Tiempo propuesto: 3:25 a 4:15. Elemento: fracaso que se evita. Sección académica: resultados. Interviene Luis Eduardo Ramos Giraldo.

Imagen: intento de completar un servicio sin diagnóstico; aviso de validación; diagnóstico registrado y cambio de estado correcto. Después, breve vista del informe final de pruebas.

Narración:

«¿Qué ocurre si alguien intenta terminar la atención sin registrar una revisión? Valtraza impide marcar el servicio como completado mientras no tenga un diagnóstico. Si no se encontraron fallas, también puede registrarse esa conclusión: no se exige inventar una reparación. Para comprobar el sistema ejecutamos ciclos de pruebas con resultados y evidencias por versión. El cierre del ciclo tres registró setenta y nueve casos exitosos de setenta y nueve, además de cuatrocientas ocho pruebas automáticas y novecientas doce aserciones. Estas cifras corresponden al entorno y las versiones documentados.»

## Escena 7 El resultado para el cliente

Tiempo propuesto: 4:15 a 5:00. Elemento: final. Sección académica: conclusiones. Luis Eduardo Ramos Giraldo inicia y cierra Juan Sebastián Ramos Giraldo.

Imagen: supervisión del servicio completado; resultado de la consulta pública; cierre con los cuatro integrantes y enlaces del repositorio y GitBook.

Narración de Luis:

«El administrador consulta la atención completa desde supervisión. Laura utiliza su código y ve el estado y un resumen general, sin iniciar sesión y sin exponer los datos personales o el diagnóstico detallado. La historia vuelve así a su necesidad inicial: encontrar una respuesta clara sobre la atención de su computador.»

Narración de Juan Sebastián:

«Valtraza reúne el registro, la asignación y el seguimiento en un mismo recorrido. El código, el diseño, las pruebas y la documentación permiten explicar cómo se construyó y cómo se utiliza. Somos ValleTech: Dxien, Juan Sebastián, Johan y Luis. Este es el resultado de nuestro proyecto.»

## Montaje y revisión

Los tiempos son una guía de edición. Ensayen a ritmo natural, con pausas para que el público alcance a leer cada pantalla. Si una intervención sobrepasa su bloque, ajusten el montaje sin acelerar la voz hasta volverla difícil de entender. Mantengan el mismo nombre y estado del servicio entre las tomas del caso; el ejemplo de validación debe identificarse como una comprobación aparte si utiliza otro servicio.

No afirmen que el producto recupera contraseñas, administra inventario, factura, envía notificaciones automáticas al cliente o actualiza todas las pantallas en tiempo real. Esas funciones no pertenecen al alcance entregado. Los enlaces de documentación pública no sustituyen la dirección de la aplicación de demostración.

El archivo que se carga como video debe ser la grabación final editada. Este guion permite producirla y no equivale a un video ya grabado ni a una entrega realizada en Moodle.

## Recorrido de la sustentación de veinte minutos

El aviso del curso asigna veinte minutos de presentación y diez de preguntas al turno de las 4:30 p. m. del 25 de noviembre. El siguiente reparto es una propuesta interna; el video y la sustentación son actividades distintas.

| Tiempo | Responsable | Qué explicar o demostrar |
| --- | --- | --- |
| 0 a 3 min | Dxien | Necesidad, actores, alcance RF01–RF14 y exclusiones. |
| 3 a 7 min | Juan Sebastián | Seis módulos, cinco capas, ocho entidades y correspondencia con el código y GitDiagram. |
| 7 a 14 min | Johan | Cliente y computador, servicio, asignación, ingreso técnico, diagnóstico, actividad, repuesto cuando aplique y estado. Usar registros preparados para evitar tiempos de digitación. |
| 14 a 18 min | Luis | Validación sin diagnóstico, permisos, resumen de los tres ciclos, resultados finales y límites de las mediciones. |
| 18 a 20 min | Juan Sebastián con el equipo | Consulta pública, resultado del caso y ubicación del manual, GitBook y repositorio. |

Preparen una copia de la base de demostración y verifiquen servidor, navegador, cuentas y conectividad antes de presentar. Lleven las evidencias como respaldo si se interrumpe la demostración; indiquen expresamente cuándo muestran una captura en lugar de una operación en vivo.
