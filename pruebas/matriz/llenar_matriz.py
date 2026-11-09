import json
import sys
from copy import copy
from pathlib import Path

from openpyxl import load_workbook
from openpyxl.styles import Alignment

AQUI = Path(__file__).resolve().parent
ORIGEN = Path(sys.argv[1]) if len(sys.argv) > 1 else AQUI / "Formato Matriz de Pruebas Unitarias (2).xlsx"
DESTINO = Path(sys.argv[2]) if len(sys.argv) > 2 else AQUI / "Matriz_Pruebas_Valtraza.xlsx"
DATOS = json.loads((AQUI / "casos.json").read_text(encoding="utf-8"))

libro = load_workbook(ORIGEN)
if len(libro.worksheets) != 1:
    raise SystemExit("La matriz original debe tener una sola hoja.")
hoja = libro.worksheets[0]


def buscar(texto):
    for fila in hoja.iter_rows():
        for celda in fila:
            if isinstance(celda.value, str) and celda.value.strip().lower().startswith(texto.lower()):
                return celda
    raise SystemExit(f"No se encontró «{texto}» en la matriz original.")


def valor_a_la_derecha(etiqueta, valor):
    celda = buscar(etiqueta)
    for columna in range(celda.column + 1, hoja.max_column + 1):
        destino = hoja.cell(row=celda.row, column=columna)
        if destino.value not in (None, ""):
            destino.value = valor
            return
    raise SystemExit(f"No se encontró la celda de valor para «{etiqueta}».")


valor_a_la_derecha("Nombre del Proyecto", DATOS["proyecto"])
valor_a_la_derecha("Fecha de revision", DATOS["fecha_revision"])
valor_a_la_derecha("Elaboró", DATOS["elaboro"])

encabezado = buscar("Requerimiento #").row
columnas = {hoja.cell(row=encabezado, column=c).value.strip(): c for c in range(1, hoja.max_column + 1) if hoja.cell(row=encabezado, column=c).value}
esperadas = [
    "Requerimiento #", "Descripcion del requerimiento", "Caso de Prueba #", "Descripción #", "Datos de entrada",
    "Resultado Esperado", "Requisitos de Ambiente/sistema",
    "Usuario que ejecuta la prueba - Ciclo 1", "Fecha Ejecucion Prueba - Ciclo 1", "Resultado Obtenido -Ciclo 1", "Estado de la prueba - Ciclo 1",
    "Usuario que ejecuta la prueba - Ciclo 2", "Fecha Ejecucion Prueba - Ciclo 2", "Resultado Obtenido -Ciclo 2", "Estado de la prueba - Ciclo 2",
    "Observaciones",
]
faltantes = [nombre for nombre in esperadas if nombre not in columnas]
if faltantes:
    raise SystemExit(f"Columnas originales no encontradas: {faltantes}")

primera = encabezado + 1
estilos = {c: copy(hoja.cell(row=primera, column=c)._style) for c in range(1, hoja.max_column + 1)}
altura = hoja.row_dimensions[primera].height

ultima_ejemplo = primera
while any(hoja.cell(row=ultima_ejemplo + 1, column=c).value for c in range(1, 4)):
    ultima_ejemplo += 1
for fila in range(primera, ultima_ejemplo + 1):
    for columna in range(1, hoja.max_column + 1):
        hoja.cell(row=fila, column=columna).value = None

ciclo1 = DATOS["ciclo1"]
for indice, caso in enumerate(DATOS["casos"]):
    fila = primera + indice
    ciclo2 = caso.get("ciclo2", {})
    ambiente = DATOS["ambiente"] + (f". Prerrequisito: {caso['prerrequisitos']}" if caso.get("prerrequisitos") else "")
    valores = {
        "Requerimiento #": caso["req"],
        "Descripcion del requerimiento": DATOS["requisitos"][caso["req"]],
        "Caso de Prueba #": caso["cp"],
        "Descripción #": caso["pasos"],
        "Datos de entrada": caso["entradas"],
        "Resultado Esperado": caso["esperado"],
        "Requisitos de Ambiente/sistema": ambiente,
        "Usuario que ejecuta la prueba - Ciclo 1": ciclo1["usuario"] if caso["estado"] != "No ejecutado" else None,
        "Fecha Ejecucion Prueba - Ciclo 1": ciclo1["fecha"] if caso["estado"] != "No ejecutado" else None,
        "Resultado Obtenido -Ciclo 1": caso["obtenido"],
        "Estado de la prueba - Ciclo 1": caso["estado"],
        "Usuario que ejecuta la prueba - Ciclo 2": ciclo2.get("usuario"),
        "Fecha Ejecucion Prueba - Ciclo 2": ciclo2.get("fecha"),
        "Resultado Obtenido -Ciclo 2": ciclo2.get("obtenido"),
        "Estado de la prueba - Ciclo 2": ciclo2.get("estado"),
        "Observaciones": caso["obs"] + (f" Ciclo 2: {ciclo2['obs']}" if ciclo2.get("obs") else ""),
    }
    for nombre, columna in columnas.items():
        celda = hoja.cell(row=fila, column=columna)
        celda._style = copy(estilos[columna])
        celda.value = valores.get(nombre)
        celda.alignment = Alignment(wrap_text=True, vertical="top")
    if altura:
        hoja.row_dimensions[fila].height = None

libro.save(DESTINO)
estados = {}
for caso in DATOS["casos"]:
    estados[caso["estado"]] = estados.get(caso["estado"], 0) + 1
print(f"Matriz guardada en {DESTINO.name}: {len(DATOS['casos'])} casos · ciclo 1 {estados}")
