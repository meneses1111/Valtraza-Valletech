import json
import sys
from collections import Counter
from pathlib import Path

from openpyxl import load_workbook
from openpyxl.styles import Alignment, Border, Font, PatternFill, Side

RAIZ = Path(__file__).resolve().parents[2]
CASOS = RAIZ / "pruebas" / "matriz" / "casos.json"
INFORME = Path(sys.argv[1]) if len(sys.argv) > 1 else RAIZ / "pruebas" / "plan" / "Informe_ejecucion_pruebas_Valtraza_C3.xlsx"
HOJA = "Detalle ciclo 3"
URL_EVIDENCIAS = "https://github.com/meneses1111/Valtraza-Valletech/tree/pruebas/pruebas/evidencias/ciclo-3"

COLUMNAS = [
    ("CP", 8),
    ("Requisito", 10),
    ("Descripción del requisito", 30),
    ("Resultado esperado", 40),
    ("Fecha de ejecución (ciclo 3)", 16),
    ("Versión probada (SHA)", 14),
    ("Herramientas", 22),
    ("Resultado obtenido (ciclo 3)", 70),
    ("Estado (ciclo 3)", 12),
    ("Evidencia", 45),
    ("Observaciones", 40),
]

datos = json.loads(CASOS.read_text(encoding="utf-8"))
casos = datos["casos"]
libro = load_workbook(INFORME)
if HOJA in libro.sheetnames:
    del libro[HOJA]
hoja = libro.create_sheet(HOJA, 1)

titulo = Font(bold=True, size=14)
negrita = Font(bold=True, color="FFFFFF")
relleno = PatternFill("solid", fgColor="1F4E78")
borde = Border(*(Side(style="thin", color="A6A6A6"),) * 4)
ajuste = Alignment(wrap_text=True, vertical="top")

hoja["A1"] = "Detalle por caso de prueba: ciclo 3"
hoja["A1"].font = titulo
estados = Counter(caso["ciclo3"]["estado"] for caso in casos)
hoja["A2"] = (
    f"Proyecto: Valtraza (equipo ValleTech) · Ejecutó: {datos['ciclo3']['usuario']} (responsable de pruebas) · "
    f"Fechas: {datos['ciclo3']['fechas']} · Versiones: {datos['ciclo3']['sha']}"
)
hoja["A3"] = (
    f"Casos: {len(casos)} · "
    + " · ".join(f"{estado}: {cantidad}" for estado, cantidad in sorted(estados.items()))
    + f" · Las rutas de evidencia parten de pruebas/evidencias/ciclo-3/ ({URL_EVIDENCIAS})"
)
for celda in ("A2", "A3"):
    hoja[celda].alignment = Alignment(wrap_text=False)

FILA = 5
for indice, (nombre, ancho) in enumerate(COLUMNAS, start=1):
    celda = hoja.cell(row=FILA, column=indice, value=nombre)
    celda.font = negrita
    celda.fill = relleno
    celda.alignment = Alignment(wrap_text=True, vertical="center", horizontal="center")
    celda.border = borde
    hoja.column_dimensions[celda.column_letter].width = ancho

for desplazamiento, caso in enumerate(casos, start=1):
    ciclo3 = caso["ciclo3"]
    valores = [
        caso["cp"],
        caso["req"],
        datos["requisitos"].get(caso["req"], ""),
        caso["esperado"],
        ciclo3["fecha"],
        ciclo3["sha"],
        ciclo3["herramientas"],
        ciclo3["obtenido"],
        ciclo3["estado"],
        "\n".join(ciclo3["evidencia"]),
        ciclo3["obs"],
    ]
    for indice, valor in enumerate(valores, start=1):
        celda = hoja.cell(row=FILA + desplazamiento, column=indice, value=valor)
        celda.alignment = ajuste
        celda.border = borde

hoja.freeze_panes = hoja.cell(row=FILA + 1, column=2)
hoja.auto_filter.ref = f"A{FILA}:{hoja.cell(row=FILA + len(casos), column=len(COLUMNAS)).coordinate}"
hoja.page_setup.orientation = "landscape"
hoja.page_setup.fitToWidth = 1
hoja.page_setup.fitToHeight = 0
hoja.sheet_properties.pageSetUpPr.fitToPage = True
libro.active = 0
libro.save(INFORME)
print(f"{HOJA}: {len(casos)} casos · {dict(estados)} · {INFORME.name}")
