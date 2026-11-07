# Trabajo del técnico

RF08 muestra únicamente los servicios asignados al técnico autenticado, del más reciente al más antiguo, con filtro por estado. El detalle reúne diagnósticos, mantenimientos y reparaciones, repuestos e historial. El historial relacionado muestra servicios anteriores completados del mismo computador y sus actividades, en solo lectura.

RF09 permite registrar un resultado de diagnóstico o indicar que no se encontraron fallas. Las pruebas realizadas y las observaciones son opcionales. RF10 permite agregar actividades de tipo mantenimiento o reparación con descripción obligatoria. RF11 permite guardar varios repuestos con nombre y cantidad entera positiva; las observaciones son opcionales.

Los registros se agregan al servicio y no se ofrecen operaciones para editarlos o borrarlos. Quitar una fila del formulario de repuestos solo retira una fila todavía no guardada.

RF12 permite cambiar entre pendiente, en curso y completado. Las clases del patrón State aplican el estado seleccionado. La comprobación de diagnóstico se realiza en la lógica de trabajo técnico: completar exige al menos un diagnóstico y no exige una reparación.

Las operaciones bloquean la fila del servicio y vuelven a comprobar al encargado dentro de la transacción. Una reasignación impide que el técnico anterior siga guardando información. Los observers notifican los cambios después de confirmar la transacción; las vistas muestran los datos actuales al volver a consultarlos.
