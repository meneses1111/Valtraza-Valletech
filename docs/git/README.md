# Git, GitHub y publicaci�n

| Rama | Uso |
| --- | --- |
| main | Versi�n estable de entrega |
| develop | Integraci�n del desarrollo aceptado |
| feature/<modulo> | Construcci�n de cada m�dulo desde develop |
| correccion/<tema> | Correcciones desde develop |
| pruebas | Casos, automatizaci�n y resultados a cargo de Luis |

No programar directamente en main, develop o pruebas. Cada rama funcional parte del develop que contiene los m�dulos previos necesarios. Hacer commits peque�os, naturales, en primera persona y en espa�ol; por ejemplo, `Agrego los filtros de supervisi�n`. Separar cambios de documentaci�n y c�digo cuando sea posible. No cambiar fechas ni reescribir el historial para simular una jornada de trabajo.

Antes de subir: revisar el diff, excluir credenciales/contexto/caches, comprobar formato y ejecutar la validaci�n apropiada. Abrir PR hacia develop, esperar las comprobaciones y resolver los conflictos sobre la rama de trabajo. Integrar con merge sin squash para conservar commits y la relaci�n de ramas. Mantener main para el cierre de la versi�n estable.

Los reportes de Luis originan correcciones trazables: requisito/CP, Issue, commit que corrige y ejecuci�n del segundo ciclo. Su rama y resultados no se completan en nombre del programador.

## Construcci�n del GitBook

Las fuentes est�n en `docs`. `SUMMARY.md` define la navegaci�n y `book.json` fija GitBook 3.2.2. El Node de esta herramienta est� separado del entorno PHP de la aplicaci�n.

### Comprobaci�n autom�tica

El workflow **Revisar GitBook** comprueba fuentes y enlaces internos, compila el libro con GitBook 3.2.2 y Node 10.24.1 y revisa el HTML generado. Despu�s abre las p�ginas con Chrome en 1366 � 768 y 1920 � 1080 y guarda el informe de navegaci�n.

Si termina correctamente, en la ejecuci�n de GitHub Actions aparece el artefacto **gitbook-vista-previa-SHA**, que contiene el sitio est�tico, y el artefacto de validaci�n. Para abrir la vista previa, extraer el sitio en una carpeta y servirla con un servidor HTTP local; la b�squeda y la navegaci�n del tema no deben evaluarse mediante `file://`.

### Construcci�n local en un entorno aislado

Con Node 10.24.1 y npm 6 disponibles �nicamente para esta herramienta:

```text
npm install --prefix herramientas/gitbook --ignore-scripts --no-audit
node herramientas/gitbook/compilar.cjs
python herramientas/gitbook/verificar.py --html _book
```

La comprobaci�n de las fuentes se puede ejecutar sin instalar GitBook:

```text
python herramientas/gitbook/verificar.py
```

El generador utiliza la biblioteca oficial GitBook 3.2.2; no se reemplaza por otro motor ni se instala de forma global. `_book` es salida generada y se mantiene fuera de las ramas de desarrollo. El workflow de revisi�n no despliega el sitio ni modifica la base de datos.

### Publicaci�n de la entrega

Al cerrar la entrega se publica �nicamente el contenido generado en `gh-pages` y se activa GitHub Pages sobre esa rama. Despu�s de comprobar la URL p�blica se a�ade el enlace al README. La vista previa y los archivos de `docs` no equivalen a una publicaci�n estable. La aceptaci�n del ciclo 2 se registra antes del cierre de la versi�n de entrega.

## GitDiagram

El an�lisis de GitDiagram se produce sobre la versi�n final del repositorio. Comparar el grafo con los seis m�dulos y sus dependencias, registrar la rama y el SHA analizados y explicar cualquier diferencia. No presentar la vista documental de m�dulos como una ejecuci�n de GitDiagram.
