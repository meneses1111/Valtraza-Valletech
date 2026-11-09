# Base de datos

PostgreSQL guarda ocho tablas: cliente, tecnico, usuario, equipo, servicio, diagnostico, trabajo y repuesto. El cliente de un servicio se obtiene mediante su equipo.

![Relaciones principales entre las ocho tablas](../assets/relaciones-datos.svg)

El esquema utiliza tres tipos ENUM: rol_usuario (administrador, tecnico), estado_servicio (pendiente, en_curso, completado) y tipo_trabajo (mantenimiento, reparacion). La migraci�n lee database/sql/esquema_valtraza.sql. Los modelos conservan las claves propias del dise�o y no utilizan created_at ni updated_at.

Despu�s de configurar una base vac�a llamada valtraza, se ejecuta `php artisan migrate` y despu�s `php artisan db:seed`. El seeder crea admin sin activar y muestra su c�digo de activaci�n en consola.

| Tabla | Clave y datos principales |
| --- | --- |
| cliente | id_cliente; nombre, numero_identificacion �nico, correo opcional |
| tecnico | id_tecnico; nombre, numero_identificacion �nico, correo opcional |
| usuario | id_usuario; nombre_usuario �nico, contrasena_hash, sal, rol, codigo_activacion �nico, activado, id_tecnico opcional y �nico |
| equipo | id_equipo; codigo_equipo �nico, id_cliente, fecha_registro, modelo y otros_datos_tecnicos opcionales |
| servicio | id_servicio; codigo_servicio y codigo_seguimiento �nicos, id_equipo, id_tecnico opcional, fecha, nombre, descripcion_problema y estado |
| diagnostico | id_diagnostico; id_servicio, resultado, fallas_identificadas, pruebas_realizadas, observaciones y fecha_registro |
| trabajo | id_trabajo; id_servicio, tipo, descripcion, observaciones y fecha_registro |
| repuesto | id_repuesto; id_servicio, nombre, cantidad positiva, observacion y fecha_registro |

Las claves primarias son enteros de identidad. Un cliente tiene varios equipos; cada equipo puede tener varios servicios. Cada servicio tiene cero o un t�cnico y varios diagn�sticos, trabajos y repuestos. Las siete claves for�neas usan RESTRICT. La relaci�n usuario-t�cnico es obligatoria para el rol t�cnico y no se permite para administrador. Un servicio sin t�cnico solo puede estar pendiente.

El archivo SQL versionado contiene los tipos, longitudes, nulabilidad y restricciones completas. La segunda migraci�n incorpora ocho �ndices operativos: equipo por cliente; servicio por equipo; diagn�stico, trabajo y repuesto por servicio; servicio por t�cnico/estado/fecha y por estado/fecha; y el �ndice parcial de servicios sin t�cnico. Su creaci�n usa IF NOT EXISTS para admitir �ndices previamente existentes; el rollback de esa migraci�n retira los �ndices con esos nombres, incluso si ya exist�an. No hacer rollback en una base compartida sin revisar su efecto y tener copia.

Las comprobaciones autom�ticas utilizan una base separada llamada valtraza_test. RefreshDatabase elimina tambi�n los tipos ENUM antes de volver a migrar. EsquemaTest comprueba los valores permitidos y el rechazo de valores desconocidos. Nunca debe ejecutarse esa suite sobre una base con informaci�n que se necesite conservar. Los casos, la matriz y los ciclos del responsable de pruebas se realizan por separado.
