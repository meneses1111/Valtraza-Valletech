# Consulta pública

RF14 está disponible en la página de inicio. El cliente introduce el código de seguimiento y pulsa Consultar; no necesita cuenta ni sesión. El código distingue mayúsculas y minúsculas. Un código desconocido muestra un mensaje y permite intentar nuevamente.

El resultado contiene únicamente nombre del servicio, identificación y modelo del computador, estado y la indicación general de si hay diagnósticos, mantenimientos o reparaciones. No muestra datos personales, nombre del técnico, problema reportado, resultados técnicos, repuestos ni otros servicios del computador.

El código se envía por POST, con protección CSRF, para mantenerlo fuera de la dirección de navegación. La respuesta no se almacena en caché. La consulta admite diez solicitudes por minuto e IP. Al superar ese límite, la pantalla 429 indica cuántos segundos esperar. **Nueva consulta** regresa al inicio.

El código permite consultar ese resultado: debe compartirse solo con la persona correspondiente. La aplicación no ofrece búsquedas públicas por cédula, cliente o número consecutivo de servicio.
