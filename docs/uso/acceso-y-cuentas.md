# Acceso y cuentas

Este bloque implementa RF01 y RF03. El administrador crea la cuenta de un técnico previamente registrado y le entrega un código de activación. El técnico abre /activar, introduce el código y define su contraseña, con un mínimo de ocho caracteres. Una cuenta ya activada rechaza otro intento de activación con ese código.

En `/ingresar`, administrador y técnico utilizan su nombre de usuario y contraseña. Una cuenta inactiva no puede iniciar sesión. El aviso «Usuario o contraseña incorrectos.» es el mismo para cuenta inactiva, usuario inexistente o contraseña errada. La página ofrece siempre la guía de activación con enlace a `/activar`. El acceso correcto regenera la sesión y lleva a `/panel`. Quien ya tiene sesión también llega al panel al abrir `/ingresar` o `/activar`. Un visitante debe iniciar sesión antes de abrir el panel.

Las rutas de creación de cuentas exigen rol administrador en el servidor. Las cuentas creadas desde **Cuentas** reciben el rol fijo de técnico. Las contraseñas se guardan como hash. Los formularios usan validación, protección CSRF y cuotas separadas para ingreso y activación. Al superar una cuota, la pantalla indica cuántos segundos faltan para volver a intentar.

El administrador ve las cuentas registradas, su rol, el técnico vinculado y si están activadas. El código se entrega al crear la cuenta; la lista no expone códigos, contraseñas ni hashes. La activación consume el código y lo reemplaza por otro valor aleatorio.

Las contraseñas no reaparecen al corregir un formulario. Se rechazan valores que excedan los 72 bytes admitidos por bcrypt, incluidos caracteres multibyte que sobrepasen ese límite.

AccesoTest verifica activación, permisos e inicio de sesión; PanelTest verifica las redirecciones y el acceso al panel. Las pruebas automáticas existentes no sustituyen la matriz, los casos ni los ciclos del responsable de pruebas. Cada ejecución debe indicar qué versión del código verificó.
