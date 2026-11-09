# Preguntas frecuentes

## ¿El cliente debe registrarse?

No. El cliente abre el inicio e introduce el código de seguimiento de un servicio. Solo administrador y técnico necesitan cuenta.

## ¿Registrar un técnico crea su cuenta?

No. Primero se registra en **Técnicos**. Después, el administrador crea su acceso desde **Cuentas** y entrega el código de activación.

## ¿La contraseña de PostgreSQL sirve para entrar a la página?

No. PostgreSQL autentica la conexión del servidor a la base. La página usa una cuenta de administrador o técnico creada y activada dentro de Valtraza. Cada integrante configura sus propias credenciales de base en `.env`.

## ¿El código de activación es el mismo que el de seguimiento?

No. El de activación se consume al definir la contraseña de una cuenta. El de seguimiento permite consultar un servicio. Los códigos consecutivos de servicio y de computador son otros identificadores.

## ¿Se puede completar un servicio sin reparación?

Sí, cuando no es necesaria. Debe existir al menos un diagnóstico; también se permite indicar que no se encontraron fallas.

## ¿Se pierde información al cambiar al técnico?

No. La reasignación conserva estado, diagnósticos, actividades y repuestos. El técnico anterior pierde el acceso operativo al servicio.

## ¿El cliente ve los diagnósticos y los repuestos?

No. Ve el nombre del servicio, el computador, el estado y un resumen que indica si hay diagnósticos, mantenimientos o reparaciones. Los detalles técnicos y los datos personales quedan restringidos.

## ¿Por qué el ingreso muestra una guía de activación incluso con un usuario inexistente?

La guía es fija para ayudar a activar una cuenta sin revelar si un nombre de usuario existe o está pendiente. El error de credenciales se mantiene genérico.

## ¿Puedo ejecutar las pruebas en la base de uso diario?

No. La suite recrea tablas y tipos ENUM. Configura una base de pruebas exclusiva y confirma la conexión antes de ejecutarla.

## ¿El sistema incluye inventario, facturación o solicitudes virtuales?

No forman parte de la ERS definitiva 1.1. Registrar repuestos utilizados no equivale a gestionar existencias de inventario.

## ¿Las pruebas aprobadas significan que la entrega ya está cerrada?

No por sí solas. Se deben conservar la matriz y las evidencias, comprobar las correcciones en el ciclo 2 y revisar la versión estable y su documentación. Consulta el [estado y las evidencias](pruebas/estado-y-evidencias.md).

## ¿Este GitBook también ejecuta Valtraza?

No. Es un sitio estático de documentación. Para ejecutar la aplicación se necesita el entorno indicado en [Instalación y operación](instalacion/README.md).
