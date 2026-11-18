# Git, GitHub y publicación

| Rama | Uso |
| --- | --- |
| main | Versión estable de entrega |
| develop | Integración del desarrollo aceptado |
| feature/<modulo> | Construcción de cada módulo desde develop |
| correccion/<tema> | Correcciones desde develop |
| pruebas | Casos, automatización y resultados a cargo de Luis |
| docs/<tema> | Documentación de una tarea concreta desde develop |
| gh-pages | Salida generada del libro; gestionada por Actions |

`develop` reúne las correcciones de aplicación según el flujo acordado por el equipo. Una rama temporal, cuando corresponde, parte de un `develop` actualizado y se asocia a una tarea. Luis entrega sus ciclos desde `pruebas` a `develop` mediante PR. La entrega estable se integra a `main` con su propio PR. Hacer commits pequeños, naturales, en primera persona y en español; por ejemplo, `Agrego los filtros de supervisión`. Separar cambios de documentación y código cuando sea posible. No cambiar fechas ni reescribir el historial para simular una jornada de trabajo.

Antes de subir: revisar el diff, excluir credenciales/contexto/caches, comprobar formato y ejecutar la validación apropiada. Abrir PR hacia develop, esperar las comprobaciones y resolver los conflictos sobre la rama de trabajo. Integrar con merge sin squash para conservar commits y la relación de ramas. Mantener main para el cierre de la versión estable.

Los reportes de Luis originan correcciones trazables: requisito y caso, issue, commit que corrige y ejecución de verificación. Su rama y resultados no se completan en nombre del programador.

## Cierre y mantenimiento de ramas

Se conservan cuatro ramas permanentes: `main`, `develop`, `pruebas` y `gh-pages`. Las demás duran lo que dure su tarea. Evitar varias ramas abiertas para la misma tarea y mantener solo las temporales que tengan trabajo activo. La cantidad de módulos de la aplicación no obliga a mantener una rama por módulo después de integrar su desarrollo.

Antes de cerrar una rama temporal:

1. Confirmar con su responsable que no conserva trabajo local pendiente ni un nuevo ciclo en curso.
2. Comprobar que su punta actual está contenida en `develop` y en la versión de entrega, y que su PR está integrado. No basta con que sus archivos parezcan iguales ni con que no haya PR abiertos.
3. Guardar un respaldo actualizado fuera del remoto y el SHA de la punta. El inventario de SHA permite identificar commits, pero no sustituye el respaldo de sus objetos Git.
4. Retirar la referencia remota solo después del cierre y la autorización correspondiente. Los commits integrados y el PR conservan el historial del trabajo.
5. Actualizar las referencias locales con `git fetch --prune`. No usar `reset --hard` para esta limpieza ni descartar cambios locales sin revisar.

Para `main` y `develop`, configurar en GitHub reglas que exijan PR y las comprobaciones aplicables de Actions, y bloqueen force push y eliminación. La revisión por otra persona debe ajustarse a los permisos y disponibilidad reales del equipo. El nombre exacto de cada comprobación requerida se obtiene de una ejecución correcta; no exigir un workflow condicionado por rutas si eso impide integrar PR donde ese workflow no se ejecuta. Los cambios de estas reglas requieren permisos de administración y todavía no se han aplicado.

GitHub Actions ejecuta las comprobaciones y la publicación configuradas. No organiza, integra ni cierra por sí solo todas las ramas del equipo. La eliminación automática de ramas al integrar PR debe mantenerse desactivada mientras el cierre y respaldo sigan siendo manuales; no afecta de forma retroactiva a las ramas antiguas.

### Integraciones del ciclo 3

| Rama | Qué contiene |
| --- | --- |
| `main` | Versión estable; incluye el cierre documental del ciclo 3. El SHA vigente se consulta en GitHub. |
| `develop` | Integración del cierre del ciclo 3; línea base de esta revisión documental: `3644709`. |
| `pruebas` | Informes, scripts y evidencias mantenidos por Luis. |
| `gh-pages` | Salida del libro gestionada por GitHub Actions. |

El [PR #47](https://github.com/meneses1111/Valtraza-Valletech/pull/47) incorporó la primera entrega y el [PR #49](https://github.com/meneses1111/Valtraza-Valletech/pull/49) incorporó el cierre. Luis cerró el [issue #48](https://github.com/meneses1111/Valtraza-Valletech/issues/48) al verificar la paginación en español. El [estado de pruebas](../pruebas/estado-y-evidencias.md) conserva ambas ejecuciones.

## Construcción del GitBook

Las fuentes están en `docs`. `SUMMARY.md` define la navegación y `book.json` configura el contenido. El generador es HonKit 6.2.5, fijado en `herramientas/gitbook/package-lock.json`, compatible con la estructura de GitBook. El Node de esta herramienta está separado del entorno PHP de la aplicación.

### Comprobación automática

El workflow **Revisar GitBook** comprueba fuentes y enlaces internos, compila el libro con HonKit 6.2.5 y Node 24 LTS y revisa el HTML generado. Después abre las páginas con Chrome en 1366 × 768 y 1920 × 1080, tanto en la raíz como bajo `/Valtraza-Valletech/`, y guarda el informe de navegación. Se comprueba también el acceso directo a `index.html` sin depender de redirecciones del servidor.

Si termina correctamente, en la ejecución de GitHub Actions aparece el artefacto **gitbook-vista-previa-SHA**, que contiene el sitio estático, y el artefacto de validación. Para abrir la vista previa, extraer el sitio en una carpeta y servirla con un servidor HTTP local; la búsqueda y la navegación del tema no deben evaluarse mediante `file://`.

En Windows, con Node.js 18 o posterior, extrae todo el ZIP y abre **ABRIR-GITBOOK.cmd**. El iniciador utiliza un puerto local disponible, abre el navegador y conserva el servidor mientras la ventana permanezca abierta. No requiere instalar GitBook ni sus dependencias. También se puede ejecutar `node abrir-vista-previa.cjs` desde la carpeta extraída.

### Construcción local en un entorno aislado

Con Node 24 LTS y npm disponibles para esta herramienta:

```text
npm ci --prefix herramientas/gitbook --ignore-scripts --no-fund
node herramientas/gitbook/compilar.cjs
python herramientas/gitbook/verificar.py --html _book
```

La comprobación de las fuentes se puede ejecutar sin instalar GitBook:

```text
python herramientas/gitbook/verificar.py
```

Se conserva el nombre GitBook para el libro y su estructura de capítulos; la compilación utiliza HonKit en lugar del generador Legacy 3.2.2. Las dependencias se auditan en CI y no se instalan de forma global. `_book` es salida generada y se mantiene fuera de las ramas de desarrollo. Los PR y los cambios de `develop` generan solo vistas previas. El despliegue se habilita únicamente para `main`; no modifica la base de datos.

### Publicación de la entrega

Después de integrar a `main`, **Revisar GitBook** conserva únicamente el sitio generado en `gh-pages` y prepara el artefacto oficial de Pages. Excluye los iniciadores locales y añade `.nojekyll`. No se publican el código PHP, `.env`, dependencias ni archivos de contexto.

La primera vez, un administrador debe abrir **Settings → Pages → Build and deployment → Source** y seleccionar **GitHub Actions**. Esta activación necesita permisos de administración; no se sustituye añadiendo permisos al token del workflow. Después, abrir **Actions → Revisar GitBook → Run workflow**, seleccionar `main` y ejecutar. El workflow despliega el libro comprobado mediante las acciones oficiales de Pages. `gh-pages` conserva la salida generada; el despliegue utiliza el artefacto de Actions para que las actualizaciones no dependan de que un push del token automático active otro workflow.

Si Pages no está activado, se conserva el libro y se muestra la instrucción pendiente; una compilación correcta no se anuncia como sitio publicado. Tras desplegar, comprobar la URL indicada, el índice, la búsqueda, imágenes y enlaces internos, y solo entonces añadir el enlace público al README. La vista previa y los archivos de `docs` no equivalen a una publicación estable.

## GitDiagram

El análisis de GitDiagram se produce sobre la versión final del repositorio. Comparar el grafo con los seis módulos y sus dependencias, registrar la rama y el SHA analizados y explicar cualquier diferencia. No presentar la vista documental de módulos como una ejecución de GitDiagram.
