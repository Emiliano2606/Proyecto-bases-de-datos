# Catálogo de Medicamentos Veterinarios (Modelo Relacional y ETL)

## 📌 Descripción del Catálogo y Modelo

Este catálogo digital representa la información de registro de medicamentos veterinarios, modelada bajo un **Esquema en Estrella**. Esta estructura está compuesta por una tabla de hechos (`Medicamento`) y varias tablas de dimensión o catálogo.

Los archivos CSV adjuntos son el resultado final de un riguroso proceso de Extracción, Transformación y Limpieza (ETL) que incluyó la normalización de entidades y la creación de claves sustitutas.

| Nombre del Archivo CSV | Tabla SQL Correspondiente | Tipo de Tabla | Clave Primaria (PK) |
| :--- | :--- | :--- | :--- |
| `laboratorio.csv` | `Laboratorio` | Dimensión | `id_laboratorio` |
| `producto.csv` | `Producto` | Dimensión | `id_producto` |
| `compuesto.csv` | `Compuesto` | Dimensión | `id_compuesto` |
| `especie.csv` | `Especie_Individual` | Dimensión | `id_especie` |
| `especie_agregada.csv` | `Especie_Agregada` | Dimensión | `id_especie_agregada` |
| `medicamento.csv` | **`Medicamento`** | **Hechos (Central)** | `id_medicamento` |
| `especie_individual.csv` | `Medicamento_Especie` | **Puente (N:M)** | Compuesta (`fk_medicamento`, `fk_especie_individual`) |
| `schema.sql` | `creaciond e las tablas` | **contiene el create table** |  |

---

## ⚙️ Comandos Linux para Incorporar el Catálogo en PostgreSQL

El proceso de incorporación se realiza utilizando la terminal de Linux/Bash y la herramienta `psql`. **Es crucial que los 7 archivos CSV y el archivo `schema.sql` estén en el mismo directorio de trabajo** desde donde se ejecutan estos comandos.

### Requisitos

* El servidor PostgreSQL debe estar activo.
* Reemplazar **`nombre_de_tu_db`** con el nombre de tu base de datos en todos los comandos.

### 1. Crear la Estructura de la Base de Datos (CREATE TABLE)

El archivo `schema.sql` debe contener todas las sentencias `CREATE TABLE` de las 7 tablas.

```bash
# PASO 1.1: Crear la base de datos (Ejecutar solo si es necesario)
createdb nombre_de_tu_db

```bash
# PASO 1.2: Ejecutar el script SQL para crear la estructura de las 7 tablas
psql -d nombre_de_tu_db -f schema.sql
### 2. Carga Masiva de Datos (Comandos \COPY)

Se utiliza el comando \COPY para la carga masiva. Los archivos deben cargarse en un **orden jerárquico** (Dimensiones, luego Central, y finalmente Puente) para mantener la integridad referencial (FK).

**Nota Especial sobre NULLS:** La columna `fk_compuesto` en la tabla `Medicamento` usa `9999999` para "No Aplica". El comando `\COPY` está configurado con **`NULL '9999999'`** para convertir este valor al `NULL` estándar de PostgreSQL.

```bash
# PASO 2: Conexión a PostgreSQL y ejecución de todos los comandos de copia
psql -d nombre_de_tu_db -c "

-- A. Carga de Dimensiones (Tablas Independientes)
\COPY Laboratorio FROM 'laboratorio.csv' WITH (FORMAT CSV, HEADER TRUE);
\COPY Producto FROM 'producto.csv' WITH (FORMAT CSV, HEADER TRUE);
\COPY Compuesto FROM 'compuesto.csv' WITH (FORMAT CSV, HEADER TRUE);
\COPY Especie_Individual FROM 'especie.csv' WITH (FORMAT CSV, HEADER TRUE);
\COPY Especie_Agregada FROM 'especie_agregada.csv' WITH (FORMAT CSV, HEADER TRUE);

-- B. Carga de la Tabla Central (Hechos)
\COPY Medicamento FROM 'tabla_central_identificador_fk.csv' WITH (FORMAT CSV, HEADER TRUE, NULL '9999999');

-- C. Carga de la Tabla Puente (Muchos a Muchos)
\COPY Medicamento_Especie FROM 'especie_individual_puente.csv' WITH (FORMAT CSV, HEADER TRUE);

-- Verificación de la Carga: Muestra el conteo de registros cargados
SELECT 'Registros cargados en Medicamento:', count(*) FROM Medicamento; 
"