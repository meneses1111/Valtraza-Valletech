# Requisitos y alcance

La línea base funcional es la ERS 1.1. Los tres actores son administrador, técnico y cliente sin cuenta.

| Requisito | Comportamiento |
| --- | --- |
| RF01 | Ingreso único para administrador y técnico; cierre de sesión |
| RF02 | Registro y consulta de técnicos |
| RF03 | Creación de cuentas de técnicos y activación por código |
| RF04 | Registro y consulta de clientes |
| RF05 | Registro de computadores e historial por equipo |
| RF06 | Registro de servicios y generación de códigos |
| RF07 | Asignación y reasignación de técnico |
| RF08 | Consulta del técnico de sus servicios asignados y su historial relacionado |
| RF09 | Registro de diagnósticos |
| RF10 | Registro de actividades de mantenimiento o reparación |
| RF11 | Registro de repuestos utilizados |
| RF12 | Cambio a pendiente, en curso o completado |
| RF13 | Supervisión de todos los servicios por el administrador |
| RF14 | Consulta pública de un servicio mediante su código de seguimiento |

Completar exige al menos un diagnóstico; no exige una reparación. Un técnico puede atender varios servicios. La reasignación conserva el estado y los registros anteriores, y retira el acceso operativo al técnico anterior. Los registros de avance se agregan al historial; no se ofrecen operaciones para reescribirlos o borrarlos.

| Requisito | Condición que debe verificarse |
| --- | --- |
| RNF01 | HTTPS y contraseñas de al menos ocho caracteres, guardadas con hash y sal |
| RNF02 | Permisos por rol y servicio comprobados en el servidor |
| RNF03 | Integridad de datos, transacciones y copia/restauración |
| RNF04 | Interfaz clara y mensajes comprensibles |
| RNF05 | Consultas simples en un máximo de dos segundos y operación con quince usuarios concurrentes |
| RNF06 | MVC y documentación técnica |
| RNF07 | Uso en Chrome y Firefox estables |
| RNF08 | Presentación a 1366 × 768 y 1920 × 1080, sin desplazamiento horizontal innecesario |
| RNF09 | Disponibilidad mientras servidor y base estén activos; recuperación sin reinstalar |

La existencia de un control en el código no acredita por sí sola el cumplimiento del requisito. Los resultados de ejecución y sus evidencias se registran en la matriz oficial.
