# Seguridad

Las rutas internas exigen autenticación y rol en el servidor. Las operaciones técnicas comprueban además que el usuario sea el técnico encargado. La comprobación de pertenencia se repite dentro de la transacción con la fila del servicio bloqueada, para coordinar cambios con una reasignación.

Las contraseñas usan bcrypt, con sal incorporada al hash; la columna histórica `sal` permanece vacía. La activación requiere confirmación y al menos ocho caracteres. Se rechazan más de 72 bytes y el carácter nulo, evitando truncamiento silencioso. Una cuenta inactiva no inicia sesión. La activación es transaccional y reemplaza el código utilizado por un valor aleatorio.

El ingreso regenera la sesión; la salida la invalida y renueva el token CSRF. Las cookies son HttpOnly, SameSite=Lax y Secure cuando se usa la configuración HTTPS suministrada; producción fuerza Secure y enlaces HTTPS. El servidor debe tener certificado y redirección HTTP a HTTPS. Configurar enlaces seguros en PHP no reemplaza esa configuración del servidor.

Ingreso y activación tienen un límite general de treinta solicitudes por minuto e IP. Los intentos fallidos se limitan adicionalmente a cinco por minuto: por usuario e IP en ingreso y por IP en activación. La consulta pública admite treinta solicitudes por minuto e IP. Los límites son temporales; no se implementa un bloqueo permanente de cuentas.

Los formularios POST conservan CSRF. Blade escapa los datos mostrados y Eloquent parametriza las consultas. Las respuestas incluyen CSP, protección contra marcos, `nosniff`, política de referencia y restricciones de cámara, micrófono y geolocalización. No se permite almacenar las páginas dinámicas en caché. Producción desactiva la depuración y añade HSTS a respuestas seguras.

La consulta pública selecciona campos expresamente permitidos. Los códigos no se incorporan a la URL ni se recuperan como entrada anterior. Los errores de persistencia se registran con mensaje genérico y SQLSTATE, sin SQL, parámetros o códigos. Las páginas de error no dependen de una consulta adicional a la base.

Las credenciales, claves, copias de seguridad y archivos de entorno se mantienen fuera del repositorio. Las copias deben guardarse en un lugar de acceso restringido. La validación de permisos, cookies, CSRF, HTTPS y datos expuestos requiere ejecución real sobre el entorno de entrega.
