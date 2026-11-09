# Primeros pasos

Valtraza conserva la trazabilidad de la atenci�n de un computador. Cada servicio tiene su propio c�digo de seguimiento y puede incluir varios diagn�sticos, actividades y repuestos.

## 1. Elige tu recorrido

| Actor | Punto de entrada | Funciones principales |
| --- | --- | --- |
| Administrador | `/ingresar`, despu�s de activar su cuenta | Registrar personas, equipos y servicios; asignar t�cnicos; supervisar |
| T�cnico | `/ingresar`, despu�s de activar su cuenta | Consultar sus servicios; registrar diagn�sticos, trabajo y repuestos; cambiar estado |
| Cliente | `/` | Consultar un servicio con el c�digo que recibi� |

Administrador y t�cnico usan el mismo formulario. El rol de la cuenta determina sus funciones; elegir un enlace no concede permisos adicionales.

## 2. Prepara el entorno

Si vas a instalar el sistema, sigue [Instalaci�n y operaci�n](../instalacion/README.md). Cada integrante necesita su propia base PostgreSQL y su `.env` privado. Las credenciales de la base de datos no son las credenciales para ingresar a la p�gina.

Si el sistema ya est� instalado, solicita al administrador tu usuario y c�digo de activaci�n. Abre `/activar`, establece y confirma una contrase�a de al menos ocho caracteres y despu�s ingresa en `/ingresar`.

El cliente no necesita este paso: utiliza directamente su c�digo de seguimiento.

## 3. Recorre un servicio completo

1. El administrador registra un cliente y su computador, o selecciona los que ya existen.
2. Crea el servicio con fecha, nombre y problema reportado. El servicio nace pendiente.
3. Entrega al cliente el c�digo de seguimiento y asigna un t�cnico registrado.
4. El t�cnico abre **Mis servicios** y registra el diagn�stico y el trabajo que corresponda.
5. Si utiliza repuestos, guarda sus nombres y cantidades.
6. Actualiza el estado. Para completar el servicio debe existir al menos un diagn�stico; una reparaci�n no es obligatoria.
7. El administrador supervisa el detalle y el cliente consulta el estado y un resumen general.

## 4. Comprueba los l�mites

La reasignaci�n conserva la informaci�n y retira el acceso operativo al t�cnico anterior. Los registros t�cnicos guardados se agregan al historial: no se ofrecen opciones para editarlos o borrarlos.

Para probar el recorrido utiliza datos ficticios y un entorno separado. La [gu�a de pruebas](../pruebas/README.md) explica qu� debe registrar el responsable de calidad.

Contin�a con la [gu�a de uso por rol](../uso/README.md).
