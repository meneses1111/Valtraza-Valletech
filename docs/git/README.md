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

## GitBook

El contenido del libro está en `docs`, con README, SUMMARY, book.json y un directorio por tema. El generador previsto es GitBook 3.2.2; su CLI utiliza Node.js de una generación anterior. En un entorno compatible instalar `gitbook-cli@2.3.2`, seleccionar GitBook 3.2.2 e instalar las dependencias del libro:

```text
gitbook fetch 3.2.2
gitbook install docs
gitbook build docs _book --gitbook=3.2.2
```

Si el CLI falla por incompatibilidad de Node.js, usar un entorno aislado con una versión compatible mediante nvm; no sustituir el generador sin acordarlo. La vista local debe revisarse después de generar el libro. `_book` es salida generada y se mantiene fuera de las ramas de desarrollo.

En el cierre se publica únicamente el contenido generado en `gh-pages` y se activa GitHub Pages sobre esa rama. Después de comprobar la URL pública se añade el enlace al README. No publicar credenciales ni documentos de trabajo. La existencia de docs no equivale a una publicación realizada.

El análisis de GitDiagram se produce sobre la versión final del repositorio. Comparar el grafo con los seis módulos y sus dependencias, registrar la versión analizada y explicar cualquier diferencia. No presentar una imagen o enlace como evidencia de un análisis que no se haya ejecutado.
