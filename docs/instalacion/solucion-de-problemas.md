# Soluci�n de problemas

Antes de cambiar la instalaci�n, identifica si el fallo corresponde a conexi�n, esquema, sesi�n o cuenta. Conserva los datos y registra el mensaje y la operaci�n que lo produjo.

## La p�gina muestra Illuminate\Database\QueryException

Este encabezado identifica una excepci�n de base de datos, no su causa exacta. Revisa el SQLSTATE y el detalle de depuraci�n solo en tu entorno privado.

| Situaci�n | Qu� comprobar |
| --- | --- |
| PostgreSQL no responde | Servicio iniciado, direcci�n `DB_HOST` y puerto `DB_PORT` de la instancia correcta |
| Autenticaci�n rechazada | `DB_USERNAME` y `DB_PASSWORD` propios de esa instalaci�n |
| La base no existe | Nombre de `DB_DATABASE`; crear una base vac�a si es una instalaci�n nueva |
| Falta una tabla | Estado de las migraciones y que la conexi�n apunte a la base correcta |
| Una tabla ya existe al migrar | Puede ser un esquema creado manualmente; detenerse y revisar su adopci�n |

El archivo `.env.example` no contiene una contrase�a v�lida de PostgreSQL. Copiarlo no configura autom�ticamente la conexi�n. No se comparten contrase�as entre integrantes ni se sube `.env` al repositorio.

Despu�s de guardar una correcci�n en `.env`, ejecuta:

```powershell
php artisan config:clear
```

Si un servidor local conserva variables del proceso anterior, rein�cialo para que tome la nueva configuraci�n. No uses `migrate:fresh` para resolver un problema de conexi�n o una base con datos que deban conservarse.

## El formulario vuelve al ingreso o aparece una sesi�n vencida

La configuraci�n de referencia utiliza HTTPS y `SESSION_SECURE_COOKIE=true`. Una cookie Secure no se env�a por HTTP. Comprueba que navegas por la URL HTTPS configurada, que el certificado es confiable y que el servidor redirige HTTP a HTTPS.

Recarga el formulario si aparece **419 / Tu sesi�n venci�**. Las peticiones POST deben incluir el token CSRF de la misma sesi�n. No desactives CSRF para corregir este error.

## No puedo ingresar con mi usuario

Revisa que hayas activado la cuenta. En **Activar mi cuenta** debes usar el c�digo entregado por el administrador y definir tu propia contrase�a. Un c�digo ya consumido no vuelve a funcionar.

La gu�a de activaci�n se muestra siempre. El aviso de credenciales es gen�rico para no confirmar qu� cuentas existen. Despu�s de cinco intentos fallidos por usuario e IP, espera el intervalo indicado antes de reintentar.

El seeder no reemplaza una cuenta `admin` existente. Volver a ejecutar `db:seed` no restablece su contrase�a. La aplicaci�n no incluye recuperaci�n de contrase�as en esta versi�n.

## El cliente no encuentra su servicio

Utiliza el **c�digo de seguimiento** completo, respetando sus caracteres. El c�digo consecutivo del servicio y el c�digo del computador son identificadores distintos y no reemplazan al de seguimiento.

## Falta una extensi�n o un comando

Comprueba `php -v`, `composer --version` y `psql --version`, y que la consola utilice la instalaci�n esperada. PHP necesita `pdo_pgsql`, `mbstring` y las extensiones requeridas por Composer. Los recursos de interfaz ya est�n en `public/assets`; no se necesita ejecutar npm para iniciar Laravel.

Si el problema persiste, documenta requisito/caso, pasos, resultado esperado y obtenido, entorno y SHA. Comparte evidencias sin contrase�as, c�digos activos ni datos personales.
