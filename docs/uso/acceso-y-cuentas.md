# Acceso y cuentas

Este bloque implementa RF01 y RF03. El administrador crea la cuenta de un técnico previamente registrado y le entrega un código de activación. El técnico abre /activar, introduce el código y define su contraseña, con un mínimo de ocho caracteres. Una cuenta ya activada rechaza otro intento de activación con ese código.

En /ingresar, administrador y técnico utilizan su nombre de usuario y contraseña. Una cuenta inactiva no puede iniciar sesión. El acceso correcto regenera la sesión y lleva a /panel. Quien ya tiene sesión también llega al panel al abrir /ingresar o /activar. Un visitante debe iniciar sesión antes de abrir el panel.

Las rutas de creación de cuentas exigen el rol administrador en el servidor. Las contraseñas se guardan como hash. Los formularios utilizan validación, protección CSRF y límites de intentos para acceso y activación.

AccesoTest verifica activación, permisos e inicio de sesión; PanelTest verifica las redirecciones y el acceso al panel. La validación automática del bloque completo pasó en GitHub con PHP 8.3 y PostgreSQL 18: dieciocho pruebas y treinta y ocho comprobaciones en dos ejecuciones consecutivas. Esta validación del desarrollo no completa los casos, la matriz ni los ciclos a cargo de Luis. El resto de módulos aún no forma parte de esta entrega.
