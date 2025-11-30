import csv
import json
from collections import defaultdict

archivo_csv = 'sepomex_base.csv'   # tu archivo CSV
archivo_json = 'cp.json' # archivo JSON que se generará

datos = defaultdict(lambda: {"colonias": []})

with open(archivo_csv, newline='', encoding='utf-8') as csvfile:
    reader = csv.DictReader(csvfile)
    for row in reader:
        cp = row['codigo_postal'].strip()
        if 'estado' not in datos[cp]:
            datos[cp]['cp'] = cp
            datos[cp]['estado'] = row['estado'].strip()
            datos[cp]['municipio'] = row['municipio'].strip()
        colonia = row['asentamiento'].strip()
        if colonia not in datos[cp]['colonias']:  # evita duplicados
            datos[cp]['colonias'].append(colonia)

# Convertir a lista y guardar JSON
json_lista = list(datos.values())

with open(archivo_json, 'w', encoding='utf-8') as f:
    json.dump(json_lista, f, ensure_ascii=False, indent=2)

print("Archivo JSON generado con éxito: cp.json")
