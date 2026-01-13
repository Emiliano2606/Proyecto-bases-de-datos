--
-- PostgreSQL database dump
--

\restrict efk8CjqYkZ93ulAMXyx9gUjeT2ZeXCwSJk2CVsJTswEVzlyaspcGCkJJN25UfnQ

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: catalogo_vacunas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.catalogo_vacunas (
    id_vacuna integer NOT NULL,
    nombre_vacuna character varying(100) NOT NULL,
    especie character varying(50) NOT NULL
);


ALTER TABLE public.catalogo_vacunas OWNER TO postgres;

--
-- Name: catalogo_vacunas_id_vacuna_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.catalogo_vacunas_id_vacuna_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.catalogo_vacunas_id_vacuna_seq OWNER TO postgres;

--
-- Name: catalogo_vacunas_id_vacuna_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.catalogo_vacunas_id_vacuna_seq OWNED BY public.catalogo_vacunas.id_vacuna;


--
-- Name: citas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.citas (
    id_cita integer NOT NULL,
    fk_id_mascota integer NOT NULL,
    fk_id_asignacion integer NOT NULL,
    fecha_cita date NOT NULL,
    hora_cita time without time zone NOT NULL,
    motivo_cliente text,
    fecha_creacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    estatus_cita character varying(20) DEFAULT 'Agendada'::character varying,
    monto_base_congelado numeric(10,2)
);


ALTER TABLE public.citas OWNER TO postgres;

--
-- Name: citas_id_cita_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.citas_id_cita_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.citas_id_cita_seq OWNER TO postgres;

--
-- Name: citas_id_cita_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.citas_id_cita_seq OWNED BY public.citas.id_cita;


--
-- Name: compuesto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.compuesto (
    id_compuesto integer NOT NULL,
    compuesto_activo character varying(255) NOT NULL
);


ALTER TABLE public.compuesto OWNER TO postgres;

--
-- Name: consultas_medicas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.consultas_medicas (
    id_consulta integer NOT NULL,
    fk_id_cita integer,
    fk_id_mascota integer,
    fk_id_doctor integer,
    fecha_consulta timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    sintomas text,
    prediagnostico text,
    diagnostico text,
    tratamiento_general text,
    peso numeric(5,2),
    temperatura numeric(4,1),
    frecuencia_cardiaca integer,
    frecuencia_respiratoria integer,
    proxima_cita date
);


ALTER TABLE public.consultas_medicas OWNER TO postgres;

--
-- Name: consultas_medicas_id_consulta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.consultas_medicas_id_consulta_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.consultas_medicas_id_consulta_seq OWNER TO postgres;

--
-- Name: consultas_medicas_id_consulta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.consultas_medicas_id_consulta_seq OWNED BY public.consultas_medicas.id_consulta;


--
-- Name: datosusuario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.datosusuario (
    fk_id_usuario integer NOT NULL,
    nombre character varying(100) NOT NULL,
    apellido1 character varying(100) NOT NULL,
    apellido2 character varying(100),
    sexo_dueno character varying(10),
    fecha_nacimiento date NOT NULL,
    procedencia_mascota character varying(20),
    telefono_principal character varying(15) NOT NULL,
    telefono_emergencia character varying(15) NOT NULL
);


ALTER TABLE public.datosusuario OWNER TO postgres;

--
-- Name: detalles_aves; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detalles_aves (
    fk_id_mascota integer NOT NULL,
    especie_ave character varying(100),
    grupo_taxonomico character varying(100),
    estatus_conservacion character varying(100),
    clasificacion_autoridades character varying(100),
    tamano_ave character varying(50),
    tipo_plumas character varying(100),
    color_principal character varying(50),
    color_secundario character varying(50),
    convive_animales character varying(10),
    tipo_alimento character varying(100),
    marca_alimento character varying(100),
    veces_come_dia character varying(50),
    tratamientos_recibidos text,
    tipo_jaula character varying(100),
    dimensiones_jaula character varying(100),
    tiene_chip character varying(10),
    numero_chip character varying(50),
    tipo_chip character varying(50)
);


ALTER TABLE public.detalles_aves OWNER TO postgres;

--
-- Name: detalles_gatos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detalles_gatos (
    fk_id_mascota integer NOT NULL,
    raza_gato character varying(100),
    grupo_gato character varying(100),
    registro_principal_gato character varying(100),
    tamano_gato character varying(100),
    peso_gato character varying(50),
    tipo_pelaje_especifico character varying(100),
    caracteristicas_fisicas text,
    color_principal character varying(50),
    color_secundario character varying(50),
    tipo_pelo character varying(50),
    patron_pelo character varying(50),
    convive_animales character varying(10),
    tipo_alimento character varying(100),
    marca_alimento character varying(100),
    veces_come_dia character varying(50),
    tratamientos_recibidos text,
    tiene_ruac character varying(10),
    ruac_valor character varying(100),
    tiene_chip character varying(10),
    numero_chip_gato character varying(100),
    tipo_chip_gato character varying(50)
);


ALTER TABLE public.detalles_gatos OWNER TO postgres;

--
-- Name: detalles_lagartos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detalles_lagartos (
    fk_id_mascota integer NOT NULL,
    especie_lagarto character varying(100) NOT NULL,
    tamano_lagarto character varying(100) NOT NULL,
    clasificacion_lagarto character varying(200) NOT NULL,
    estatus_lagarto character varying(100) NOT NULL,
    requerimientos_ambientales_lagarto character varying(100) NOT NULL,
    fuente_calor_lagarto character varying(100) NOT NULL,
    tipo_terrario_lagarto character varying(100),
    dimensiones_terrario_lagarto character varying(100),
    dieta_lagarto character varying(100),
    marca_alimento_lagarto character varying(100),
    veces_comida_lagarto character varying(50),
    tratamientos_lagarto text
);


ALTER TABLE public.detalles_lagartos OWNER TO postgres;

--
-- Name: detalles_perros; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detalles_perros (
    fk_id_mascota integer NOT NULL,
    raza_perro character varying(100),
    grupo_perro character varying(100),
    seccion_perro character varying(100),
    pais_perro character varying(100),
    color_principal character varying(50),
    color_secundario character varying(50),
    tipo_pelo character varying(50),
    patron_pelo character varying(50),
    senas_particulares text,
    tamano_perro character varying(100),
    convive_animales character varying(10),
    tipo_alimento character varying(100),
    marca_alimento character varying(100),
    veces_come_dia character varying(50),
    tratamientos_recibidos character varying(100),
    tiene_ruac character varying(10),
    ruac_valor character varying(100),
    tiene_chip character varying(10),
    numero_chip_perro character varying(100),
    tipo_chip_perro character varying(50)
);


ALTER TABLE public.detalles_perros OWNER TO postgres;

--
-- Name: detalles_serpientes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detalles_serpientes (
    fk_id_mascota integer NOT NULL,
    especie_serpiente character varying(100),
    tamano_serpiente character varying(100),
    clasificacion_serpiente character varying(200),
    estatus_serpiente character varying(100),
    fuente_calor_serpiente character varying(100),
    tipo_terrario_serpiente character varying(100),
    alimentos_serpiente character varying(100),
    marca_alimento_serpiente character varying(100),
    veces_comida_serpiente character varying(100),
    tipo_tratamiento_serpiente character varying(100),
    dimensiones_terrario_serpiente character varying(100)
);


ALTER TABLE public.detalles_serpientes OWNER TO postgres;

--
-- Name: detalles_tortugas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.detalles_tortugas (
    fk_id_mascota integer NOT NULL,
    especie_tortuga character varying(100),
    tamano_tortuga character varying(100),
    clasificacion_tortuga character varying(200),
    estatus_tortuga character varying(100),
    fuente_calor_tortuga character varying(100),
    alimentos_tortuga character varying(100),
    tratamientos_tortuga character varying(100),
    veces_comida_tortuga character varying(100),
    tipo_terrario_tortuga character varying(100),
    dimensiones_terrario_tortuga character varying(100)
);


ALTER TABLE public.detalles_tortugas OWNER TO postgres;

--
-- Name: doctor_asignacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doctor_asignacion (
    id_asignacion integer NOT NULL,
    fk_id_doctor integer,
    fk_id_sucursal integer,
    fk_id_especialidad integer
);


ALTER TABLE public.doctor_asignacion OWNER TO postgres;

--
-- Name: doctor_asignacion_id_asignacion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.doctor_asignacion_id_asignacion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.doctor_asignacion_id_asignacion_seq OWNER TO postgres;

--
-- Name: doctor_asignacion_id_asignacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.doctor_asignacion_id_asignacion_seq OWNED BY public.doctor_asignacion.id_asignacion;


--
-- Name: doctores; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doctores (
    id_doctor integer NOT NULL,
    nombre_doctor character varying(150) NOT NULL,
    cedula_profesional character varying(50) NOT NULL,
    telefono_doctor character varying(20),
    email_doctor character varying(100) NOT NULL,
    password_doctor text NOT NULL
);


ALTER TABLE public.doctores OWNER TO postgres;

--
-- Name: doctores_id_doctor_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.doctores_id_doctor_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.doctores_id_doctor_seq OWNER TO postgres;

--
-- Name: doctores_id_doctor_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.doctores_id_doctor_seq OWNED BY public.doctores.id_doctor;


--
-- Name: domicilio; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.domicilio (
    fk_usuario_id integer NOT NULL,
    calle character varying(100) NOT NULL,
    numero_exterior character varying(10) NOT NULL,
    numero_interior character varying(10),
    colonia character varying(100) NOT NULL,
    municipio character varying(100) NOT NULL,
    estado character varying(100) NOT NULL,
    cp character varying(5) NOT NULL,
    referencias text
);


ALTER TABLE public.domicilio OWNER TO postgres;

--
-- Name: especialidad_por_sucursal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.especialidad_por_sucursal (
    fk_id_sucursal integer NOT NULL,
    fk_id_especialidad integer NOT NULL
);


ALTER TABLE public.especialidad_por_sucursal OWNER TO postgres;

--
-- Name: especialidades; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.especialidades (
    id_especialidad integer NOT NULL,
    nombre_especialidad character varying(100) NOT NULL,
    precio_base numeric(10,2),
    duracion_minutos integer
);


ALTER TABLE public.especialidades OWNER TO postgres;

--
-- Name: especialidades_id_especialidad_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.especialidades_id_especialidad_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.especialidades_id_especialidad_seq OWNER TO postgres;

--
-- Name: especialidades_id_especialidad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.especialidades_id_especialidad_seq OWNED BY public.especialidades.id_especialidad;


--
-- Name: especie_agregada; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.especie_agregada (
    id_especie_agregada integer NOT NULL,
    especie_agregada character varying(500) NOT NULL
);


ALTER TABLE public.especie_agregada OWNER TO postgres;

--
-- Name: especie_individual; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.especie_individual (
    id_especie integer NOT NULL,
    nombre_especie character varying(100) NOT NULL
);


ALTER TABLE public.especie_individual OWNER TO postgres;

--
-- Name: historial_vacunacion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historial_vacunacion (
    id_historial integer NOT NULL,
    fk_id_mascota integer NOT NULL,
    fk_id_vacuna integer NOT NULL,
    fecha_aplicacion date NOT NULL
);


ALTER TABLE public.historial_vacunacion OWNER TO postgres;

--
-- Name: historial_vacunacion_id_historial_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.historial_vacunacion_id_historial_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.historial_vacunacion_id_historial_seq OWNER TO postgres;

--
-- Name: historial_vacunacion_id_historial_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.historial_vacunacion_id_historial_seq OWNED BY public.historial_vacunacion.id_historial;


--
-- Name: horarios_doctor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.horarios_doctor (
    id_horario_doc integer NOT NULL,
    fk_id_asignacion integer,
    dia_semana character varying(20) NOT NULL,
    hora_entrada time without time zone NOT NULL,
    hora_salida time without time zone NOT NULL
);


ALTER TABLE public.horarios_doctor OWNER TO postgres;

--
-- Name: horarios_doctor_id_horario_doc_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.horarios_doctor_id_horario_doc_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.horarios_doctor_id_horario_doc_seq OWNER TO postgres;

--
-- Name: horarios_doctor_id_horario_doc_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.horarios_doctor_id_horario_doc_seq OWNED BY public.horarios_doctor.id_horario_doc;


--
-- Name: horarios_sucursal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.horarios_sucursal (
    id_horario integer NOT NULL,
    fk_id_sucursal integer NOT NULL,
    dia_semana character varying(50) NOT NULL,
    hora_apertura time without time zone NOT NULL,
    hora_cierre time without time zone NOT NULL
);


ALTER TABLE public.horarios_sucursal OWNER TO postgres;

--
-- Name: horarios_sucursal_id_horario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.horarios_sucursal_id_horario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.horarios_sucursal_id_horario_seq OWNER TO postgres;

--
-- Name: horarios_sucursal_id_horario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.horarios_sucursal_id_horario_seq OWNED BY public.horarios_sucursal.id_horario;


--
-- Name: laboratorio; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.laboratorio (
    id_laboratorio integer NOT NULL,
    razon_social character varying(255) NOT NULL
);


ALTER TABLE public.laboratorio OWNER TO postgres;

--
-- Name: mascotas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mascotas (
    idmascota integer NOT NULL,
    fk_id_dueno integer NOT NULL,
    nombre character varying(100) NOT NULL,
    fecha_nacimiento date,
    sexo character varying(10),
    tipo_mascota character varying(50) NOT NULL,
    foto_url character varying(255)
);


ALTER TABLE public.mascotas OWNER TO postgres;

--
-- Name: mascotas_idmascota_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mascotas_idmascota_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mascotas_idmascota_seq OWNER TO postgres;

--
-- Name: mascotas_idmascota_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mascotas_idmascota_seq OWNED BY public.mascotas.idmascota;


--
-- Name: medicamento; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.medicamento (
    id_medicamento integer NOT NULL,
    numero_registro character varying(50) NOT NULL,
    fk_laboratorio integer NOT NULL,
    fk_producto integer NOT NULL,
    fk_compuesto integer,
    fk_especie_agregada integer NOT NULL
);


ALTER TABLE public.medicamento OWNER TO postgres;

--
-- Name: medicamento_especie; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.medicamento_especie (
    fk_medicamento integer NOT NULL,
    fk_especie_individual integer NOT NULL
);


ALTER TABLE public.medicamento_especie OWNER TO postgres;

--
-- Name: producto; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.producto (
    id_producto integer NOT NULL,
    nombre_producto character varying(255) NOT NULL
);


ALTER TABLE public.producto OWNER TO postgres;

--
-- Name: recepcionistas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recepcionistas (
    id_recepcionista integer NOT NULL,
    nombre character varying(100) NOT NULL,
    apellido character varying(100),
    correo character varying(150) NOT NULL,
    password character varying(255) NOT NULL,
    telefono character varying(20),
    fecha_creacion timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    estatus boolean DEFAULT true
);


ALTER TABLE public.recepcionistas OWNER TO postgres;

--
-- Name: recepcionistas_id_recepcionista_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recepcionistas_id_recepcionista_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recepcionistas_id_recepcionista_seq OWNER TO postgres;

--
-- Name: recepcionistas_id_recepcionista_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recepcionistas_id_recepcionista_seq OWNED BY public.recepcionistas.id_recepcionista;


--
-- Name: recetas_medicamentos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.recetas_medicamentos (
    id_receta integer NOT NULL,
    fk_id_consulta integer,
    fk_id_producto integer,
    dosis_instrucciones text
);


ALTER TABLE public.recetas_medicamentos OWNER TO postgres;

--
-- Name: recetas_medicamentos_id_receta_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.recetas_medicamentos_id_receta_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.recetas_medicamentos_id_receta_seq OWNER TO postgres;

--
-- Name: recetas_medicamentos_id_receta_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.recetas_medicamentos_id_receta_seq OWNED BY public.recetas_medicamentos.id_receta;


--
-- Name: sucursales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sucursales (
    id_sucursal integer NOT NULL,
    nombre_sucursal character varying(100) NOT NULL,
    direccion_sucursal text NOT NULL,
    telefono_sucursal character varying(20) NOT NULL
);


ALTER TABLE public.sucursales OWNER TO postgres;

--
-- Name: sucursales_id_sucursal_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sucursales_id_sucursal_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sucursales_id_sucursal_seq OWNER TO postgres;

--
-- Name: sucursales_id_sucursal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sucursales_id_sucursal_seq OWNED BY public.sucursales.id_sucursal;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    idusuario integer NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    fecharegistro timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- Name: usuarios_idusuario_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_idusuario_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_idusuario_seq OWNER TO postgres;

--
-- Name: usuarios_idusuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_idusuario_seq OWNED BY public.usuarios.idusuario;


--
-- Name: catalogo_vacunas id_vacuna; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalogo_vacunas ALTER COLUMN id_vacuna SET DEFAULT nextval('public.catalogo_vacunas_id_vacuna_seq'::regclass);


--
-- Name: citas id_cita; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.citas ALTER COLUMN id_cita SET DEFAULT nextval('public.citas_id_cita_seq'::regclass);


--
-- Name: consultas_medicas id_consulta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consultas_medicas ALTER COLUMN id_consulta SET DEFAULT nextval('public.consultas_medicas_id_consulta_seq'::regclass);


--
-- Name: doctor_asignacion id_asignacion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctor_asignacion ALTER COLUMN id_asignacion SET DEFAULT nextval('public.doctor_asignacion_id_asignacion_seq'::regclass);


--
-- Name: doctores id_doctor; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctores ALTER COLUMN id_doctor SET DEFAULT nextval('public.doctores_id_doctor_seq'::regclass);


--
-- Name: especialidades id_especialidad; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especialidades ALTER COLUMN id_especialidad SET DEFAULT nextval('public.especialidades_id_especialidad_seq'::regclass);


--
-- Name: historial_vacunacion id_historial; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_vacunacion ALTER COLUMN id_historial SET DEFAULT nextval('public.historial_vacunacion_id_historial_seq'::regclass);


--
-- Name: horarios_doctor id_horario_doc; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_doctor ALTER COLUMN id_horario_doc SET DEFAULT nextval('public.horarios_doctor_id_horario_doc_seq'::regclass);


--
-- Name: horarios_sucursal id_horario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_sucursal ALTER COLUMN id_horario SET DEFAULT nextval('public.horarios_sucursal_id_horario_seq'::regclass);


--
-- Name: mascotas idmascota; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mascotas ALTER COLUMN idmascota SET DEFAULT nextval('public.mascotas_idmascota_seq'::regclass);


--
-- Name: recepcionistas id_recepcionista; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recepcionistas ALTER COLUMN id_recepcionista SET DEFAULT nextval('public.recepcionistas_id_recepcionista_seq'::regclass);


--
-- Name: recetas_medicamentos id_receta; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas_medicamentos ALTER COLUMN id_receta SET DEFAULT nextval('public.recetas_medicamentos_id_receta_seq'::regclass);


--
-- Name: sucursales id_sucursal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursales ALTER COLUMN id_sucursal SET DEFAULT nextval('public.sucursales_id_sucursal_seq'::regclass);


--
-- Name: usuarios idusuario; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN idusuario SET DEFAULT nextval('public.usuarios_idusuario_seq'::regclass);


--
-- Name: catalogo_vacunas catalogo_vacunas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.catalogo_vacunas
    ADD CONSTRAINT catalogo_vacunas_pkey PRIMARY KEY (id_vacuna);


--
-- Name: citas citas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.citas
    ADD CONSTRAINT citas_pkey PRIMARY KEY (id_cita);


--
-- Name: compuesto compuesto_compuesto_activo_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compuesto
    ADD CONSTRAINT compuesto_compuesto_activo_key UNIQUE (compuesto_activo);


--
-- Name: compuesto compuesto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.compuesto
    ADD CONSTRAINT compuesto_pkey PRIMARY KEY (id_compuesto);


--
-- Name: consultas_medicas consultas_medicas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consultas_medicas
    ADD CONSTRAINT consultas_medicas_pkey PRIMARY KEY (id_consulta);


--
-- Name: datosusuario datosusuario_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.datosusuario
    ADD CONSTRAINT datosusuario_pkey PRIMARY KEY (fk_id_usuario);


--
-- Name: detalles_aves detalles_aves_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_aves
    ADD CONSTRAINT detalles_aves_pkey PRIMARY KEY (fk_id_mascota);


--
-- Name: detalles_gatos detalles_gatos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_gatos
    ADD CONSTRAINT detalles_gatos_pkey PRIMARY KEY (fk_id_mascota);


--
-- Name: detalles_lagartos detalles_lagartos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_lagartos
    ADD CONSTRAINT detalles_lagartos_pkey PRIMARY KEY (fk_id_mascota);


--
-- Name: detalles_perros detalles_perros_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_perros
    ADD CONSTRAINT detalles_perros_pkey PRIMARY KEY (fk_id_mascota);


--
-- Name: detalles_serpientes detalles_serpientes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_serpientes
    ADD CONSTRAINT detalles_serpientes_pkey PRIMARY KEY (fk_id_mascota);


--
-- Name: detalles_tortugas detalles_tortugas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_tortugas
    ADD CONSTRAINT detalles_tortugas_pkey PRIMARY KEY (fk_id_mascota);


--
-- Name: doctor_asignacion doctor_asignacion_fk_id_doctor_fk_id_sucursal_fk_id_especia_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctor_asignacion
    ADD CONSTRAINT doctor_asignacion_fk_id_doctor_fk_id_sucursal_fk_id_especia_key UNIQUE (fk_id_doctor, fk_id_sucursal, fk_id_especialidad);


--
-- Name: doctor_asignacion doctor_asignacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctor_asignacion
    ADD CONSTRAINT doctor_asignacion_pkey PRIMARY KEY (id_asignacion);


--
-- Name: doctores doctores_cedula_profesional_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctores
    ADD CONSTRAINT doctores_cedula_profesional_key UNIQUE (cedula_profesional);


--
-- Name: doctores doctores_email_doctor_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctores
    ADD CONSTRAINT doctores_email_doctor_key UNIQUE (email_doctor);


--
-- Name: doctores doctores_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctores
    ADD CONSTRAINT doctores_pkey PRIMARY KEY (id_doctor);


--
-- Name: domicilio domicilio_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.domicilio
    ADD CONSTRAINT domicilio_pkey PRIMARY KEY (fk_usuario_id);


--
-- Name: especialidad_por_sucursal especialidad_por_sucursal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especialidad_por_sucursal
    ADD CONSTRAINT especialidad_por_sucursal_pkey PRIMARY KEY (fk_id_sucursal, fk_id_especialidad);


--
-- Name: especialidades especialidades_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especialidades
    ADD CONSTRAINT especialidades_pkey PRIMARY KEY (id_especialidad);


--
-- Name: especie_agregada especie_agregada_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especie_agregada
    ADD CONSTRAINT especie_agregada_pkey PRIMARY KEY (id_especie_agregada);


--
-- Name: especie_individual especie_individual_nombre_especie_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especie_individual
    ADD CONSTRAINT especie_individual_nombre_especie_key UNIQUE (nombre_especie);


--
-- Name: especie_individual especie_individual_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especie_individual
    ADD CONSTRAINT especie_individual_pkey PRIMARY KEY (id_especie);


--
-- Name: historial_vacunacion historial_vacunacion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_vacunacion
    ADD CONSTRAINT historial_vacunacion_pkey PRIMARY KEY (id_historial);


--
-- Name: horarios_doctor horarios_doctor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_doctor
    ADD CONSTRAINT horarios_doctor_pkey PRIMARY KEY (id_horario_doc);


--
-- Name: horarios_sucursal horarios_sucursal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_sucursal
    ADD CONSTRAINT horarios_sucursal_pkey PRIMARY KEY (id_horario);


--
-- Name: laboratorio laboratorio_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.laboratorio
    ADD CONSTRAINT laboratorio_pkey PRIMARY KEY (id_laboratorio);


--
-- Name: laboratorio laboratorio_razon_social_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.laboratorio
    ADD CONSTRAINT laboratorio_razon_social_key UNIQUE (razon_social);


--
-- Name: mascotas mascotas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mascotas
    ADD CONSTRAINT mascotas_pkey PRIMARY KEY (idmascota);


--
-- Name: medicamento_especie medicamento_especie_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento_especie
    ADD CONSTRAINT medicamento_especie_pkey PRIMARY KEY (fk_medicamento, fk_especie_individual);


--
-- Name: medicamento medicamento_numero_registro_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento
    ADD CONSTRAINT medicamento_numero_registro_key UNIQUE (numero_registro);


--
-- Name: medicamento medicamento_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento
    ADD CONSTRAINT medicamento_pkey PRIMARY KEY (id_medicamento);


--
-- Name: producto producto_nombre_producto_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_nombre_producto_key UNIQUE (nombre_producto);


--
-- Name: producto producto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.producto
    ADD CONSTRAINT producto_pkey PRIMARY KEY (id_producto);


--
-- Name: recepcionistas recepcionistas_correo_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recepcionistas
    ADD CONSTRAINT recepcionistas_correo_key UNIQUE (correo);


--
-- Name: recepcionistas recepcionistas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recepcionistas
    ADD CONSTRAINT recepcionistas_pkey PRIMARY KEY (id_recepcionista);


--
-- Name: recetas_medicamentos recetas_medicamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas_medicamentos
    ADD CONSTRAINT recetas_medicamentos_pkey PRIMARY KEY (id_receta);


--
-- Name: sucursales sucursales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursales
    ADD CONSTRAINT sucursales_pkey PRIMARY KEY (id_sucursal);


--
-- Name: horarios_sucursal unique_horario_dia; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_sucursal
    ADD CONSTRAINT unique_horario_dia UNIQUE (fk_id_sucursal, dia_semana);


--
-- Name: especialidades unique_nombre_especialidad; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especialidades
    ADD CONSTRAINT unique_nombre_especialidad UNIQUE (nombre_especialidad);


--
-- Name: sucursales unique_nombre_sucursal; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sucursales
    ADD CONSTRAINT unique_nombre_sucursal UNIQUE (nombre_sucursal);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (idusuario);


--
-- Name: idx_cita_disponibilidad_medico; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX idx_cita_disponibilidad_medico ON public.citas USING btree (fk_id_asignacion, fecha_cita, hora_cita) WHERE ((estatus_cita)::text <> ALL ((ARRAY['Finalizada'::character varying, 'Cancelada'::character varying])::text[]));


--
-- Name: idx_fk_id_dueno; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_fk_id_dueno ON public.mascotas USING btree (fk_id_dueno);


--
-- Name: idx_historial_mascota; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_historial_mascota ON public.historial_vacunacion USING btree (fk_id_mascota);


--
-- Name: consultas_medicas consultas_medicas_fk_id_cita_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consultas_medicas
    ADD CONSTRAINT consultas_medicas_fk_id_cita_fkey FOREIGN KEY (fk_id_cita) REFERENCES public.citas(id_cita);


--
-- Name: consultas_medicas consultas_medicas_fk_id_mascota_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.consultas_medicas
    ADD CONSTRAINT consultas_medicas_fk_id_mascota_fkey FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota);


--
-- Name: detalles_lagartos detalles_lagartos_fk_id_mascota_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_lagartos
    ADD CONSTRAINT detalles_lagartos_fk_id_mascota_fkey FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON DELETE CASCADE;


--
-- Name: detalles_serpientes detalles_serpientes_fk_id_mascota_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_serpientes
    ADD CONSTRAINT detalles_serpientes_fk_id_mascota_fkey FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON DELETE CASCADE;


--
-- Name: detalles_tortugas detalles_tortugas_fk_id_mascota_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_tortugas
    ADD CONSTRAINT detalles_tortugas_fk_id_mascota_fkey FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON DELETE CASCADE;


--
-- Name: doctor_asignacion doctor_asignacion_fk_id_doctor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctor_asignacion
    ADD CONSTRAINT doctor_asignacion_fk_id_doctor_fkey FOREIGN KEY (fk_id_doctor) REFERENCES public.doctores(id_doctor) ON DELETE CASCADE;


--
-- Name: doctor_asignacion doctor_asignacion_fk_id_especialidad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctor_asignacion
    ADD CONSTRAINT doctor_asignacion_fk_id_especialidad_fkey FOREIGN KEY (fk_id_especialidad) REFERENCES public.especialidades(id_especialidad);


--
-- Name: doctor_asignacion doctor_asignacion_fk_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctor_asignacion
    ADD CONSTRAINT doctor_asignacion_fk_id_sucursal_fkey FOREIGN KEY (fk_id_sucursal) REFERENCES public.sucursales(id_sucursal) ON DELETE CASCADE;


--
-- Name: especialidad_por_sucursal especialidad_por_sucursal_fk_id_especialidad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especialidad_por_sucursal
    ADD CONSTRAINT especialidad_por_sucursal_fk_id_especialidad_fkey FOREIGN KEY (fk_id_especialidad) REFERENCES public.especialidades(id_especialidad) ON DELETE CASCADE;


--
-- Name: especialidad_por_sucursal especialidad_por_sucursal_fk_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.especialidad_por_sucursal
    ADD CONSTRAINT especialidad_por_sucursal_fk_id_sucursal_fkey FOREIGN KEY (fk_id_sucursal) REFERENCES public.sucursales(id_sucursal) ON DELETE CASCADE;


--
-- Name: citas fk_asignacion_cita; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.citas
    ADD CONSTRAINT fk_asignacion_cita FOREIGN KEY (fk_id_asignacion) REFERENCES public.doctor_asignacion(id_asignacion) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: detalles_aves fk_detalles_aves_mascota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_aves
    ADD CONSTRAINT fk_detalles_aves_mascota FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: historial_vacunacion fk_historial_mascota; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_vacunacion
    ADD CONSTRAINT fk_historial_mascota FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON DELETE CASCADE;


--
-- Name: historial_vacunacion fk_historial_vacuna; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historial_vacunacion
    ADD CONSTRAINT fk_historial_vacuna FOREIGN KEY (fk_id_vacuna) REFERENCES public.catalogo_vacunas(id_vacuna) ON DELETE CASCADE;


--
-- Name: mascotas fk_id_dueno; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mascotas
    ADD CONSTRAINT fk_id_dueno FOREIGN KEY (fk_id_dueno) REFERENCES public.usuarios(idusuario) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: datosusuario fk_id_usuario; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.datosusuario
    ADD CONSTRAINT fk_id_usuario FOREIGN KEY (fk_id_usuario) REFERENCES public.usuarios(idusuario);


--
-- Name: citas fk_mascota_cita; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.citas
    ADD CONSTRAINT fk_mascota_cita FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: detalles_perros fk_mascota_general; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_perros
    ADD CONSTRAINT fk_mascota_general FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: detalles_gatos fk_mascota_general_gato; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.detalles_gatos
    ADD CONSTRAINT fk_mascota_general_gato FOREIGN KEY (fk_id_mascota) REFERENCES public.mascotas(idmascota) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: domicilio fk_usuario_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.domicilio
    ADD CONSTRAINT fk_usuario_id FOREIGN KEY (fk_usuario_id) REFERENCES public.datosusuario(fk_id_usuario);


--
-- Name: horarios_doctor horarios_doctor_fk_id_asignacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_doctor
    ADD CONSTRAINT horarios_doctor_fk_id_asignacion_fkey FOREIGN KEY (fk_id_asignacion) REFERENCES public.doctor_asignacion(id_asignacion) ON DELETE CASCADE;


--
-- Name: horarios_sucursal horarios_sucursal_fk_id_sucursal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_sucursal
    ADD CONSTRAINT horarios_sucursal_fk_id_sucursal_fkey FOREIGN KEY (fk_id_sucursal) REFERENCES public.sucursales(id_sucursal) ON DELETE CASCADE;


--
-- Name: medicamento_especie medicamento_especie_fk_especie_individual_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento_especie
    ADD CONSTRAINT medicamento_especie_fk_especie_individual_fkey FOREIGN KEY (fk_especie_individual) REFERENCES public.especie_individual(id_especie);


--
-- Name: medicamento_especie medicamento_especie_fk_medicamento_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento_especie
    ADD CONSTRAINT medicamento_especie_fk_medicamento_fkey FOREIGN KEY (fk_medicamento) REFERENCES public.medicamento(id_medicamento);


--
-- Name: medicamento medicamento_fk_compuesto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento
    ADD CONSTRAINT medicamento_fk_compuesto_fkey FOREIGN KEY (fk_compuesto) REFERENCES public.compuesto(id_compuesto);


--
-- Name: medicamento medicamento_fk_especie_agregada_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento
    ADD CONSTRAINT medicamento_fk_especie_agregada_fkey FOREIGN KEY (fk_especie_agregada) REFERENCES public.especie_agregada(id_especie_agregada);


--
-- Name: medicamento medicamento_fk_laboratorio_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento
    ADD CONSTRAINT medicamento_fk_laboratorio_fkey FOREIGN KEY (fk_laboratorio) REFERENCES public.laboratorio(id_laboratorio);


--
-- Name: medicamento medicamento_fk_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.medicamento
    ADD CONSTRAINT medicamento_fk_producto_fkey FOREIGN KEY (fk_producto) REFERENCES public.producto(id_producto);


--
-- Name: recetas_medicamentos recetas_medicamentos_fk_id_consulta_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas_medicamentos
    ADD CONSTRAINT recetas_medicamentos_fk_id_consulta_fkey FOREIGN KEY (fk_id_consulta) REFERENCES public.consultas_medicas(id_consulta);


--
-- Name: recetas_medicamentos recetas_medicamentos_fk_id_producto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.recetas_medicamentos
    ADD CONSTRAINT recetas_medicamentos_fk_id_producto_fkey FOREIGN KEY (fk_id_producto) REFERENCES public.producto(id_producto);


--
-- PostgreSQL database dump complete
--

\unrestrict efk8CjqYkZ93ulAMXyx9gUjeT2ZeXCwSJk2CVsJTswEVzlyaspcGCkJJN25UfnQ

