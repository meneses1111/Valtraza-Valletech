# Valtraza � ValleTech

Aplicaci�n web para gestionar servicios de mantenimiento y reparaci�n de computadores: recepci�n del equipo, asignaci�n del t�cnico, diagn�sticos, actividades, repuestos, supervisi�n y seguimiento del cliente.

El cliente consulta con un c�digo de seguimiento y no necesita cuenta. Administrador y t�cnico ingresan con cuentas activadas y permisos propios.

## Documentaci�n

- [Inicio de la documentaci�n](docs/README.md) y [�ndice completo](docs/SUMMARY.md).
- [Instalaci�n y operaci�n](docs/instalacion/README.md).
- [Gu�a de uso por rol](docs/uso/README.md).
- [Soluci�n de problemas](docs/instalacion/solucion-de-problemas.md).
- [Estado y evidencias de pruebas](docs/pruebas/estado-y-evidencias.md).

El GitBook se compila y revisa en el workflow **Revisar GitBook**. Sus ejecuciones correctas ofrecen una vista previa descargable; la publicaci�n estable queda para el cierre de entrega.

## Entorno de la aplicaci�n

Laravel 12, PHP 8.3, PostgreSQL 18, Blade y Bootstrap 5.3. El entorno de pruebas de referencia usa Nginx con HTTPS (Herd en Windows). Los recursos de interfaz est�n incluidos en `public/assets`; no hace falta compilar JavaScript para iniciar la aplicaci�n.

Cada integrante configura su propio `.env`. Las pruebas autom�ticas requieren una base exclusiva porque recrean tablas y tipos ENUM. Seguir la [gu�a de instalaci�n](docs/instalacion/README.md) antes de ejecutar migraciones o pruebas.

## Estado y contribuciones

`develop` re�ne el avance aceptado. La versi�n estable se integra a `main` mediante un PR. Luis mantiene la matriz y las evidencias de los ciclos de pruebas; las correcciones funcionales se realizan en ramas propias.

El alcance corresponde a la ERS definitiva 1.1: RF01�RF14 y RNF01�RNF09. Consultar el [estado de pruebas](docs/pruebas/estado-y-evidencias.md) para consultar los resultados de ambos ciclos y las mejoras opcionales registradas.
