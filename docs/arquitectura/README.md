# Arquitectura y patrones

Valtraza es un monolito modular con una sola base PostgreSQL. Nginx recibe la solicitud, `public/index.php` inicia Laravel y las rutas despachan a controladores. Blade presenta el resultado; los controladores coordinan la validaci�n y los servicios; Eloquent conserva los datos.

| Capa | Ubicaci�n y funci�n |
| --- | --- |
| Presentaci�n | `resources/views`, componentes Blade y recursos de `public/assets` |
| Control | `routes`, `app/Http/Controllers`, middleware y Form Requests |
| L�gica de negocio | `app/Services`, `app/States`, observers y reglas de la operaci�n |
| Acceso a datos | Modelos Eloquent de `app/Models` y consultas encapsuladas |
| Datos | PostgreSQL; esquema, restricciones e �ndices de `database` |

Las capas de l�gica y acceso a datos forman el modelo de MVC. La autorizaci�n se exige en rutas y solicitudes; las operaciones sobre un servicio vuelven a comprobar al encargado dentro de la transacci�n.

| M�dulo | Responsabilidad e interfaces utilizadas |
| --- | --- |
| Clientes y equipos | Clientes, computadores e historial; ofrece RegistroClientesEquipos |
| T�cnicos | Registro de t�cnicos; ofrece RegistroTecnicos |
| Cuentas y acceso | Usuarios, activaci�n y sesi�n; utiliza RegistroTecnicos |
| Servicios | Registro, c�digos, asignaci�n y pertenencia; utiliza Clientes y equipos y T�cnicos |
| Mantenimientos y reparaciones | Diagn�sticos, actividades, repuestos y cambio de estado; utiliza Servicios |
| Consulta y seguimiento | SupervisionServicios y ConsultaPublica agregan lecturas de Servicios y LecturaAvance |

Servicios no depende de Mantenimientos y reparaciones. La condici�n de completar con diagn�stico se resuelve en la operaci�n de trabajo t�cnico, evitando una dependencia circular. La consulta p�blica construye una salida con campos permitidos, sin entregar modelos completos a la vista.

| Patr�n | Aplicaci�n concreta |
| --- | --- |
| MVC | Separaci�n de vistas, controladores y modelo |
| Controlador frontal | Entrada por `public/index.php` |
| Plantilla maestra | Layouts base, administrador, t�cnico y p�blico |
| Cadena de filtros | Middleware de sesi�n, autenticaci�n, rol, CSRF y cabeceras |
| Validaci�n de formularios | Form Requests con reglas y mensajes en espa�ol |
| Active Record | Modelos Eloquent con sus claves y relaciones |
| Capa de servicios | RegistroClientesEquipos, GestionServicios, RegistroTrabajoTecnico y servicios de consulta |
| Observador | Generaci�n de c�digos y notificaci�n de nuevos registros de avance |
| Estado | Clases Pendiente, EnCurso y Completado para aplicar el estado del servicio |

Los detalles del cambio de estado y la asignaci�n est�n descritos en [Trabajo t�cnico](../uso/trabajo-tecnico.md). La revisi�n final del grafo del repositorio con GitDiagram se realiza sobre la versi�n de entrega y se contrasta con estas dependencias; no debe registrarse como realizada sin conservar su evidencia.

## Vista de m�dulos

![Seis m�dulos y sus dependencias de consumo](../assets/modulos.svg)

La vista resume las dependencias entre los servicios de los seis m�dulos. No sustituye el an�lisis final con GitDiagram ni representa cada llamada a un controlador o modelo. Los tres actores ingresan por rutas con los permisos correspondientes.
