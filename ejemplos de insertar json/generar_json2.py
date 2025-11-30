import pandas as pd
import json

# Cargar los CSV
grupos = pd.read_csv(r"C:\Users\matas\OneDrive\Documentos\Bases de Datos Ulices\litRelational\proyecto bases de datos\grupos.csv")
razas = pd.read_csv(r'C:\Users\matas\OneDrive\Documentos\Bases de Datos Ulices\litRelational\proyecto bases de datos\razas.csv')
tamano = pd.read_csv(r'C:\Users\matas\OneDrive\Documentos\Bases de Datos Ulices\litRelational\proyecto bases de datos\tamano.csv')
raza_tamano = pd.read_csv(r'C:\Users\matas\OneDrive\Documentos\Bases de Datos Ulices\litRelational\proyecto bases de datos\raza_tamano.csv')


# Primero, agregamos la categoría de tamaño a cada raza usando raza_tamano
# Unimos raza_tamano con tamano para obtener la categoría
raza_tamano = raza_tamano.merge(tamano, on='id_tamano', how='left')

# Ahora creamos un diccionario por raza con todos sus tamaños
raza_sizes = raza_tamano.groupby('id_raza')['categoria'].apply(list).to_dict()

# Luego, agregamos info del grupo y tamaños a cada raza
razas_list = []
for _, row in razas.iterrows():
    id_grupo = row['id_grupo']
    grupo = grupos.loc[grupos['id_grupo'] == id_grupo, 'grupo'].values[0]
    
    raza_dict = {
        "id_raza": int(row['id_raza']),
        "nombre": row['nombre'],
        "grupo": grupo,
        "seccion": row['seccion'],
        "pais": row['pais'],
        "tamanos": raza_sizes.get(row['id_raza'], [])
    }
    
    razas_list.append(raza_dict)

# Guardar el JSON
# Guardar el JSON en la carpeta que quieres
with open(r'C:\Users\matas\OneDrive\Documentos\Bases de Datos Ulices\litRelational\proyecto bases de datos\razas.json', 'w', encoding='utf-8') as f:
    json.dump(razas_list, f, ensure_ascii=False, indent=4)

print("JSON generado correctamente: razas.json")
