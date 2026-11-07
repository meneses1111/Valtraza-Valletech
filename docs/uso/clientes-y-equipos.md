# Clientes y equipos

RF04 permite registrar clientes con nombre e identificación obligatorios y correo opcional. La identificación es única. Si ya existe, se muestra un aviso y un enlace a la ficha existente, sin duplicar el registro. Los clientes no tienen cuenta de acceso.

RF05 permite registrar varios computadores para un cliente existente. La fecha es obligatoria y debe ser válida; el modelo y los otros datos técnicos son opcionales. El sistema genera códigos como EQ-0001 mediante un observer, dentro de la transacción de registro.

La ficha muestra los computadores del cliente y su historial de servicios. Desde ella se puede registrar otro computador o iniciar un nuevo servicio para un equipo existente.

RegistroClientesEquipos gestiona clientes y computadores. FichaClienteController compone la ficha utilizando esa interfaz y la consulta HistorialEquipos del módulo de servicios; el servicio no guarda id_cliente.
