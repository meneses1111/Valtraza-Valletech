# Base de datos

PostgreSQL guarda ocho tablas: cliente, tecnico, usuario, equipo, servicio, diagnostico, trabajo y repuesto. El cliente de un servicio se obtiene mediante su equipo.

El esquema utiliza tres tipos ENUM: rol_usuario (administrador, tecnico), estado_servicio (pendiente, en_curso, completado) y tipo_trabajo (mantenimiento, reparacion). La migración lee database/sql/esquema_valtraza.sql. Los modelos conservan las claves propias del diseño y no utilizan created_at ni updated_at.

Después de configurar una base vacía llamada valtraza, se ejecuta `php artisan migrate` y después `php artisan db:seed`. El seeder crea admin sin activar y muestra su código de activación en consola.

Las comprobaciones automáticas utilizan una base separada llamada valtraza_test. RefreshDatabase elimina también los tipos ENUM antes de volver a migrar. EsquemaTest comprueba los valores permitidos y el rechazo de valores desconocidos. Sus seis pruebas pasaron en dos ejecuciones consecutivas en GitHub, con PHP 8.3 y PostgreSQL 18. Los casos, la matriz y los ciclos de pruebas del responsable de pruebas se realizan por separado.
