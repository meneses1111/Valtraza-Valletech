# Preguntas frecuentes

## �El cliente debe registrarse?

No. El cliente abre el inicio e introduce el c�digo de seguimiento de un servicio. Solo administrador y t�cnico necesitan cuenta.

## �Registrar un t�cnico crea su cuenta?

No. Primero se registra en **T�cnicos**. Despu�s, el administrador crea su acceso desde **Cuentas** y entrega el c�digo de activaci�n.

## �La contrase�a de PostgreSQL sirve para entrar a la p�gina?

No. PostgreSQL autentica la conexi�n del servidor a la base. La p�gina usa una cuenta de administrador o t�cnico creada y activada dentro de Valtraza. Cada integrante configura sus propias credenciales de base en `.env`.

## �El c�digo de activaci�n es el mismo que el de seguimiento?

No. El de activaci�n se consume al definir la contrase�a de una cuenta. El de seguimiento permite consultar un servicio. Los c�digos consecutivos de servicio y de computador son otros identificadores.

## �Se puede completar un servicio sin reparaci�n?

S�, cuando no es necesaria. Debe existir al menos un diagn�stico; tambi�n se permite indicar que no se encontraron fallas.

## �Se pierde informaci�n al cambiar al t�cnico?

No. La reasignaci�n conserva estado, diagn�sticos, actividades y repuestos. El t�cnico anterior pierde el acceso operativo al servicio.

## �El cliente ve los diagn�sticos y los repuestos?

No. Ve el nombre del servicio, el computador, el estado y un resumen que indica si hay diagn�sticos, mantenimientos o reparaciones. Los detalles t�cnicos y los datos personales quedan restringidos.

## �Por qu� el ingreso muestra una gu�a de activaci�n incluso con un usuario inexistente?

La gu�a es fija para ayudar a activar una cuenta sin revelar si un nombre de usuario existe o est� pendiente. El error de credenciales se mantiene gen�rico.

## �Puedo ejecutar las pruebas en la base de uso diario?

No. La suite recrea tablas y tipos ENUM. Configura una base de pruebas exclusiva y confirma la conexi�n antes de ejecutarla.

## �El sistema incluye inventario, facturaci�n o solicitudes virtuales?

No forman parte de la ERS definitiva 1.1. Registrar repuestos utilizados no equivale a gestionar existencias de inventario.

## �Las pruebas aprobadas significan que la entrega ya est� cerrada?

No por s� solas. Se deben conservar la matriz y las evidencias, comprobar las correcciones en el ciclo 2 y revisar la versi�n estable y su documentaci�n. Consulta el [estado y las evidencias](pruebas/estado-y-evidencias.md).

## �Este GitBook tambi�n ejecuta Valtraza?

No. Es un sitio est�tico de documentaci�n. Para ejecutar la aplicaci�n se necesita el entorno indicado en [Instalaci�n y operaci�n](instalacion/README.md).
