# Instalación y operación

## Entorno

Se necesita Git, Composer 2, PHP 8.3 con pdo_pgsql, mbstring y las extensiones requeridas por Composer, PostgreSQL 18 y un servidor Nginx con HTTPS. En Windows se utiliza Laravel Herd para servir la carpeta del proyecto mediante `https://valtraza.test`. Bootstrap y los recursos de la aplicación están en `public/assets`; no se necesita compilar JavaScript para iniciar el sistema.

Usar una consola normal de PowerShell, con PHP, Composer y PostgreSQL disponibles. Confirmar `php -v`, `composer --version` y `psql --version`. En Herd seleccionar PHP 8.3 y comprobar que la consola emplee esa misma versión.

## Instalación nueva

```powershell
git clone https://github.com/meneses1111/Valtraza-Valletech.git
cd Valtraza-Valletech
git switch main
git rev-parse HEAD
composer install
Copy-Item .env.example .env
php artisan key:generate
```

La entrega estable se instala desde `main`; registrar el SHA mostrado para identificar la versión utilizada.

La copia del entorno se hace solo cuando `.env` no existe; una instalación existente conserva su archivo y su APP_KEY. Editar `.env` con los datos locales de PostgreSQL y la URL HTTPS. No subir este archivo. Crear una base vacía `valtraza` con UTF8 y configurar DB_DATABASE, DB_USERNAME y DB_PASSWORD. No reutilizar bases de otros proyectos.

Antes de migrar, verificar que la base esté vacía. Si ya tiene las ocho tablas creadas manualmente, detener esta instalación nueva: la primera migración intenta crearlas y no adopta automáticamente un esquema existente. Conservar sus datos y revisar una estrategia de adopción o usar una instalación independiente; no borrar tablas para solucionar el error.

```powershell
php artisan migrate
php artisan db:seed
```

El seeder crea `admin` sin activar y muestra una sola vez el código nuevo. Si admin ya existe no lo reemplaza ni muestra un código nuevo. Abrir `/activar` y establecer una contraseña propia. Guardar el código de forma privada hasta utilizarlo.

En Herd enlazar la carpeta con el nombre `valtraza` y activar Secure. El sitio debe apuntar a `public`, nunca a la raíz del proyecto. Confiar en el certificado local mediante Herd y abrir `https://valtraza.test`. Con SESSION_SECURE_COOKIE=true, acceder por HTTP impide que el navegador envíe la cookie de sesión. El servidor debe redirigir HTTP a HTTPS.

Verificar permisos de escritura de `storage` y `bootstrap/cache`. Si hay configuración anterior en caché, ejecutar `php artisan config:clear`. Comprobar inicio, activación, ingreso al panel por rol y consulta pública con datos ficticios. Para producción usar APP_ENV=production y APP_DEBUG=false, configurar HTTPS en Nginx y no exponer `.env`.

## Actualización

En una copia sin cambios locales, descargar la versión acordada y registrar su SHA. Conservar `.env` y APP_KEY. Instalar dependencias desde composer.lock, realizar una copia de seguridad y revisar `php artisan migrate:status` antes de ejecutar `php artisan migrate`. No usar `migrate:fresh` sobre datos que deban conservarse.

## Pruebas automáticas

Crear una base vacía separada `valtraza_test` y dar acceso al usuario de pruebas. Configurar credenciales de pruebas mediante `.env.testing` privado. `phpunit.xml` selecciona esa base: confirmar la conexión antes de ejecutar. La suite recrea sus tablas y tipos ENUM.

```powershell
php vendor/bin/pint --test
php artisan test
php artisan test
composer audit --locked
```

Las dos ejecuciones permiten detectar errores al recrear ENUM. Estos comandos no equivalen a ejecutar la matriz oficial ni verifican por sí solos el navegador, HTTPS o quince usuarios concurrentes.

## Copia y restauración

Usar herramientas de PostgreSQL 18 desde una consola normal. Elegir una ubicación privada fuera del repositorio. Los comandos siguientes piden la contraseña; no escribirla en el comando ni en el historial.

```powershell
pg_dump -h 127.0.0.1 -p 5432 -U postgres -W -Fc -f C:\respaldo-privado\valtraza.backup valtraza
createdb -h 127.0.0.1 -p 5432 -U postgres -W valtraza_restauracion
pg_restore -h 127.0.0.1 -p 5432 -U postgres -W --no-owner --exit-on-error -d valtraza_restauracion C:\respaldo-privado\valtraza.backup
```

Crear antes la carpeta privada y utilizar una base de restauración vacía que no contenga información necesaria. Comprobar el código de salida de cada comando antes de continuar. Verificar tablas, conteos y una muestra de datos; comprobar el funcionamiento de una copia de la aplicación apuntada a la base restaurada. Registrar fecha, versión, alcance y resultado. Conservar también APP_KEY y configuración de forma privada: el volcado de PostgreSQL no respalda esos archivos. No sobrescribir la base activa para comprobar una restauración.

## Recuperación

Si el sitio no responde, comprobar primero los servicios de Nginx/PHP y PostgreSQL, certificado, conexión y permisos de almacenamiento. Iniciar los servicios y volver a consultar sin reinstalar ni recrear tablas. Una caída durante una operación debe dejar la transacción completa o revertida. Registrar errores y consultar los logs privados sin publicar credenciales o datos personales.
