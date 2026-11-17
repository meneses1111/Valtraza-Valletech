# Prueba de usabilidad: ciclo 3

| Dato | Valor |
| --- | --- |
| Fecha | 17-11-2026 |
| Ejecutó | Luis Eduardo Ramos Giraldo (responsable de pruebas) |
| Versión | `pruebas` SHA `42d7d07` |
| Navegador | Google Chrome 157 |
| Método | Revisión manual con lista de chequeo frente a la ERS 1.1 (RNF04 y RNF08) y el mockup |
| Evidencias | `pruebas/evidencias/ciclo-3/usabilidad/` y `pruebas/evidencias/ciclo-3/aceptacion/` |

| # | Criterio | Requisito | Resultado obtenido | Estado | Evidencia |
| --- | --- | --- | --- | --- | --- |
| U1 | El menú del administrador agrupa sus opciones: Servicios, Nuevo servicio, Clientes, Técnicos y Cuentas | RNF04 | El panel muestra en la barra superior Servicios, Nuevo servicio, Clientes, Técnicos y Cuentas, además de Cerrar sesión | Aprobado | `../navegadores/CP001_C3_chrome_1366x768_panel-administrador.png` |
| U2 | El técnico ve solo las opciones de su rol («Mis servicios») | RNF04 | Al ingresar como carga01, la barra muestra solo «Mis servicios» y «Cerrar sesión» | Aprobado | `../aceptacion/A4_CP035_C3_chrome_servicio-en-mis-servicios.png` |
| U3 | Después de guardar, un mensaje confirma la acción | RNF04 | Al guardar aparecen avisos verdes: «Servicio registrado correctamente», «Diagnóstico registrado.» y «Estado actualizado.» | Aprobado | `../aceptacion/A3_CP026_C3_chrome_servicio-registrado.png`, `../aceptacion/A5_CP039_C3_chrome_diagnostico-registrado.png`, `../aceptacion/A5_CP047_C3_chrome_estado-en-curso.png` |
| U4 | Un formulario con errores indica qué campo corregir | RNF04 | Al enviar «Nuevo cliente» vacío, Nombre y Número de identificación se marcan en rojo con «Completa este campo.» y aparece el resumen «Para continuar, revisa: Nombre, Número de identificación.» No guarda | Aprobado | `U4_CP020_C3_chrome_formulario-cliente-vacio.png` |
| U5 | Los campos obligatorios están marcados con * | RNF04 | El formulario de cuentas marca con * los campos obligatorios («Técnico registrado *», «Nombre de usuario *»). El de computadores marca «Fecha de registro *» y señala «Modelo (opcional)» | Aprobado | `../navegadores/CP013_C3_chrome_1366x768_cuenta-creada.png` |
| U6 | Cada pantalla interna tiene un enlace para volver | RNF04 | Las pantallas internas tienen «Volver a clientes» o «Volver a mis servicios» | Aprobado | `../aceptacion/A1_A2_CP019_CP023_C3_chrome_ficha-cliente-equipo.png`, `../aceptacion/A4_CP035_C3_chrome_servicio-en-mis-servicios.png` |
| U7 | La consulta pública con un código inexistente muestra un aviso claro | RNF04 | Con `CODIGOFALSO123` aparece «Revisa la información antes de continuar. No encontramos un servicio con ese código.», el campo se marca en rojo y el código no se vuelve a llenar | Aprobado | `U7_CP058_C3_chrome_codigo-inexistente.png` |
| U8 | Todos los textos de la interfaz están en español | RNF04 | La paginación de Cuentas muestra «Showing 1 to 15 of 20 results» en inglés. El resto de la interfaz está en español | Fallido | `U8_CP070_C3_chrome_paginacion-en-ingles.png`, `../navegadores/primera-ejecucion/CP013_C3_firefox_1366x768_cuenta-creada.png` |
| U9 | Las pantallas se ven completas, sin desplazamiento horizontal, a 1366×768 y 1920×1080 | RNF08 | Los recorridos con Puppeteer midieron 0 px de desplazamiento horizontal en todas las pantallas, en Chrome y Firefox, a 1366×768 y 1920×1080 (CP076 y CP077) | Aprobado | `../navegadores/resultados_C3.json` |

Estado: Aprobado, Fallido, Bloqueado o No ejecutado.

## Resultado

8 de 9 criterios aprobados y 1 fallido (U8).

## Observaciones

- U8 es un defecto de severidad baja: el texto de la paginación sale en inglés. Aparece en los listados paginados que Johan agregó entre el 12 y el 14-11-2026; en Cuentas se confirmó a mano y en las capturas de Puppeteer. Se reporta a Johan.
- U1, U2, U3, U5, U6 y U9 se verificaron con evidencias de la misma jornada: los recorridos de Puppeteer y las capturas de la prueba de aceptación.

