

-- ----------------------------------------------------
-- 1. TABLAS DE CATÁLOGO 
-- ----------------------------------------------------
CREATE TABLE Laboratorio (
    id_laboratorio INTEGER PRIMARY KEY,
    razon_social VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE Producto (
    id_producto INTEGER PRIMARY KEY,
    nombre_producto VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE Compuesto (
    id_compuesto INTEGER PRIMARY KEY,
    compuesto_activo VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE Especie_Individual (
    id_especie INTEGER PRIMARY KEY,
    nombre_especie VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Especie_Agregada (
    id_especie_agregada INTEGER PRIMARY KEY,
    especie_agregada VARCHAR(100) NOT NULL
);

-- ----------------------------------------------------
-- 2. TABLA CENTRAL (HECHOS)
-- ----------------------------------------------------
CREATE TABLE Medicamento (
    id_medicamento INTEGER PRIMARY KEY,
    numero_registro VARCHAR(50) UNIQUE NOT NULL,
    fk_laboratorio INTEGER NOT NULL,
    fk_producto INTEGER NOT NULL,
    fk_compuesto INTEGER, 
    fk_especie_agregada INTEGER NOT NULL,

    -- Definición de Claves Foráneas
    FOREIGN KEY (fk_laboratorio) REFERENCES Laboratorio(id_laboratorio),
    FOREIGN KEY (fk_producto) REFERENCES Producto(id_producto),
    -- fk_compuesto puede ser NULL, ya que en el CSV se usa 9999999 para "No Aplica"
    FOREIGN KEY (fk_compuesto) REFERENCES Compuesto(id_compuesto),
    FOREIGN KEY (fk_especie_agregada) REFERENCES Especie_Agregada(id_especie_agregada)
);

-- ----------------------------------------------------
-- 3. TABLA PUENTE (MUCHOS A MUCHOS)
-- ----------------------------------------------------
CREATE TABLE Medicamento_Especie (
    fk_medicamento INTEGER NOT NULL,
    fk_especie_individual INTEGER NOT NULL,
    
    -- Clave Primaria Compuesta
    PRIMARY KEY (fk_medicamento, fk_especie_individual),
    
    -- Claves Foráneas
    FOREIGN KEY (fk_medicamento) REFERENCES Medicamento(id_medicamento),
    FOREIGN KEY (fk_especie_individual) REFERENCES Especie_Individual(id_especie)
);