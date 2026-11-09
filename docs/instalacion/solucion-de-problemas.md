# Solución de problemas

Antes de cambiar la instalación, identifica si el fallo corresponde a conexión, esquema, sesión o cuenta. Conserva los datos y registra el mensaje y la operación que lo produjo.

## La página muestra Illuminate\Database\QueryException

Este encabezado identifica una excepción de base de datos, no su causa exacta. Revisa el SQLSTATE y el detalle de depuración solo en tu entorno privado.

| Situación | Qué comprobar |
| --- | --- |
| PostgreSQL no responde | Servicio iniciado, dirección `DB_HOST` y puerto `DB_PORT` de la instancia correcta |
| Autenticación rechazada | `DB_USERNAME` y `DB_PASSWORD` propios de esa instalación |
| La base no existe | Nombre de `DB_DATABASE`; crear una base vacía si es una instalación nueva |
| Falta una tabla | Estado de las migraciones y que la conexión apunte a la base correcta |
| Una tabla ya existe al migrar | Puede ser un esquema creado manualmente; detenerse y revisar su adopción |

El archivo `.env.example` no contiene una contraseña válida de PostgreSQL. Copiarlo no configura automáticamente la conexión. No se comparten contraseñas entre integrantes ni se sube `.env` al repositorio.

Después de guardar una corrección en `.env`, ejecuta:

```powershell
php artisan config:clear
```

Si un servidor local conserva variables del proceso anterior, reinícialo para que tome la nueva configuración. No uses `migrate:fresh` para resolver un problema de conexión o una base con datos que deban conservarse.

## El formulario vuelve al ingreso o aparece una sesión vencida

La configuración de referencia utiliza HTTPS y `SESSION_SECURE_COOKIE=true`. Una cookie Secure no se envía por HTTP. Comprueba que navegas por la URL HTTPS configurada, que el certificado es confiable y que el servidor redirige HTTP a HTTPS.

Recarga el formulario si aparece **419 / Tu sesión venció**. Las peticiones POST deben incluir el token CSRF de la misma sesión. No desactives CSRF para corregir este error.

## No puedo ingresar con mi usuario

Revisa que hayas activado la cuenta. En **Activar mi cuenta** debes usar el código entregado por el administrador y definir tu propia contraseña. Un código ya consumido no vuelve a funcionar.

La guía de activación se muestra siempre. El aviso de credenciales es genérico para no confirmar qué cuentas existen. Después de cinco intentos fallidos por usuario e IP, espera el intervalo indicado antes de reintentar.

El seeder no reemplaza una cuenta `admin` existente. Volver a ejecutar `db:seed` no restablece su contraseña. La aplicación no incluye recuperación de contraseñas en esta versión.

## El cliente no encuentra su servicio

Utiliza el **código de seguimiento** completo, respetando sus caracteres. El código consecutivo del servicio y el código del computador son identificadores distintos y no reemplazan al de seguimiento.

## Falta una extensión o un comando

Comprueba `php -v`, `composer --version` y `psql --version`, y que la consola utilice la instalación esperada. PHP necesita `pdo_pgsql`, `mbstring` y las extensiones requeridas por Composer. Los recursos de interfaz ya están en `public/assets`; no se necesita ejecutar npm para iniciar Laravel.

Si el problema persiste, documenta requisito/caso, pasos, resultado esperado y obtenido, entorno y SHA. Comparte evidencias sin contraseñas, códigos activos ni datos personales.
