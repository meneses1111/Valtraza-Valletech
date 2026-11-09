# Git, GitHub y publicación

| Rama | Uso |
| --- | --- |
| main | Versión estable de entrega |
| develop | Integración del desarrollo aceptado |
| feature/<modulo> | Construcción de cada módulo desde develop |
| correccion/<tema> | Correcciones desde develop |
| pruebas | Casos, automatización y resultados a cargo de Luis |

No programar directamente en main, develop o pruebas. Cada rama funcional parte del develop que contiene los módulos previos necesarios. Hacer commits pequeños, naturales, en primera persona y en español; por ejemplo, `Agrego los filtros de supervisión`. Separar cambios de documentación y código cuando sea posible. No cambiar fechas ni reescribir el historial para simular una jornada de trabajo.

Antes de subir: revisar el diff, excluir credenciales/contexto/caches, comprobar formato y ejecutar la validación apropiada. Abrir PR hacia develop, esperar las comprobaciones y resolver los conflictos sobre la rama de trabajo. Integrar con merge sin squash para conservar commits y la relación de ramas. Mantener main para el cierre de la versión estable.

Los reportes de Luis originan correcciones trazables: requisito/CP, Issue, commit que corrige y ejecución del segundo ciclo. Su rama y resultados no se completan en nombre del programador.

## Construcción del GitBook

Las fuentes están en `docs`. `SUMMARY.md` define la navegación y `book.json` fija GitBook 3.2.2. El Node de esta herramienta está separado del entorno PHP de la aplicación.

### Comprobación automática

El workflow **Revisar GitBook** comprueba fuentes y enlaces internos, compila el libro con GitBook 3.2.2 y Node 10.24.1 y revisa el HTML generado. Después abre las páginas con Chrome en 1366 × 768 y 1920 × 1080 y guarda el informe de navegación.

Si termina correctamente, en la ejecución de GitHub Actions aparece el artefacto **gitbook-vista-previa-SHA**, que contiene el sitio estático, y el artefacto de validación. Para abrir la vista previa, extraer el sitio en una carpeta y servirla con un servidor HTTP local; la búsqueda y la navegación del tema no deben evaluarse mediante `file://`.

### Construcción local en un entorno aislado

Con Node 10.24.1 y npm 6 disponibles únicamente para esta herramienta:

```text
npm install --prefix herramientas/gitbook --ignore-scripts --no-audit
node herramientas/gitbook/compilar.cjs
python herramientas/gitbook/verificar.py --html _book
```

La comprobación de las fuentes se puede ejecutar sin instalar GitBook:

```text
python herramientas/gitbook/verificar.py
```

El generador utiliza la biblioteca oficial GitBook 3.2.2; no se reemplaza por otro motor ni se instala de forma global. `_book` es salida generada y se mantiene fuera de las ramas de desarrollo. El workflow de revisión no despliega el sitio ni modifica la base de datos.

### Publicación de la entrega

Al cerrar la entrega se publica únicamente el contenido generado en `gh-pages` y se activa GitHub Pages sobre esa rama. Después de comprobar la URL pública se añade el enlace al README. La vista previa y los archivos de `docs` no equivalen a una publicación estable. La aceptación del ciclo 2 se registra antes del cierre de la versión de entrega.

## GitDiagram

El análisis de GitDiagram se produce sobre la versión final del repositorio. Comparar el grafo con los seis módulos y sus dependencias, registrar la rama y el SHA analizados y explicar cualquier diferencia. No presentar la vista documental de módulos como una ejecución de GitDiagram.
