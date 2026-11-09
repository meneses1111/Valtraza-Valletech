# Primeros pasos

Valtraza conserva la trazabilidad de la atención de un computador. Cada servicio tiene su propio código de seguimiento y puede incluir varios diagnósticos, actividades y repuestos.

## 1. Elige tu recorrido

| Actor | Punto de entrada | Funciones principales |
| --- | --- | --- |
| Administrador | `/ingresar`, después de activar su cuenta | Registrar personas, equipos y servicios; asignar técnicos; supervisar |
| Técnico | `/ingresar`, después de activar su cuenta | Consultar sus servicios; registrar diagnósticos, trabajo y repuestos; cambiar estado |
| Cliente | `/` | Consultar un servicio con el código que recibió |

Administrador y técnico usan el mismo formulario. El rol de la cuenta determina sus funciones; elegir un enlace no concede permisos adicionales.

## 2. Prepara el entorno

Si vas a instalar el sistema, sigue [Instalación y operación](../instalacion/README.md). Cada integrante necesita su propia base PostgreSQL y su `.env` privado. Las credenciales de la base de datos no son las credenciales para ingresar a la página.

Si el sistema ya está instalado, solicita al administrador tu usuario y código de activación. Abre `/activar`, establece y confirma una contraseña de al menos ocho caracteres y después ingresa en `/ingresar`.

El cliente no necesita este paso: utiliza directamente su código de seguimiento.

## 3. Recorre un servicio completo

1. El administrador registra un cliente y su computador, o selecciona los que ya existen.
2. Crea el servicio con fecha, nombre y problema reportado. El servicio nace pendiente.
3. Entrega al cliente el código de seguimiento y asigna un técnico registrado.
4. El técnico abre **Mis servicios** y registra el diagnóstico y el trabajo que corresponda.
5. Si utiliza repuestos, guarda sus nombres y cantidades.
6. Actualiza el estado. Para completar el servicio debe existir al menos un diagnóstico; una reparación no es obligatoria.
7. El administrador supervisa el detalle y el cliente consulta el estado y un resumen general.

## 4. Comprueba los límites

La reasignación conserva la información y retira el acceso operativo al técnico anterior. Los registros técnicos guardados se agregan al historial: no se ofrecen opciones para editarlos o borrarlos.

Para probar el recorrido utiliza datos ficticios y un entorno separado. La [guía de pruebas](../pruebas/README.md) explica qué debe registrar el responsable de calidad.

Continúa con la [guía de uso por rol](../uso/README.md).
