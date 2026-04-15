--
-- PostgreSQL database dump
--

\restrict PAFIvO64r25ym3Z3hUXmNtSN6aUfk6xiT1rcJ6cIQtjuGaO7R08vmdZbb0azWz0

-- Dumped from database version 15.17
-- Dumped by pg_dump version 15.17

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: categoriadonacion; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.categoriadonacion AS ENUM (
    'ROPA',
    'ALIMENTOS',
    'HIGIENE',
    'MEDICAMENTOS'
);


--
-- Name: condiciondonacion; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.condiciondonacion AS ENUM (
    'NUEVO',
    'BUEN_ESTADO',
    'REGULAR'
);


--
-- Name: estadocampana; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.estadocampana AS ENUM (
    'PROGRAMADA',
    'ACTIVA',
    'FINALIZADA'
);


--
-- Name: estadovoluntariado; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.estadovoluntariado AS ENUM (
    'ACTIVO',
    'CANCELADO',
    'FINALIZADO'
);


--
-- Name: unidadmedida; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.unidadmedida AS ENUM (
    'CAJAS',
    'PIEZAS',
    'KG',
    'MG',
    'L',
    'ML'
);


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: albergues; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.albergues (
    "Id_Albergue" integer NOT NULL,
    "Nombre_Albergue" character varying(150) NOT NULL,
    "Capacidad_Max" integer NOT NULL,
    "Tel_Contacto" bigint NOT NULL,
    "Id_Direccion" integer NOT NULL
);


--
-- Name: albergues_Id_Albergue_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."albergues_Id_Albergue_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: albergues_Id_Albergue_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."albergues_Id_Albergue_seq" OWNED BY public.albergues."Id_Albergue";


--
-- Name: campanas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.campanas (
    "id_Campana" integer NOT NULL,
    "Nombre_Campana" character varying(150) NOT NULL,
    "Fecha_Inicio" date NOT NULL,
    "Fecha_Fin" date NOT NULL,
    "id_Estado_campana" integer NOT NULL,
    "Descripcion_Objetivos" text NOT NULL
);


--
-- Name: campanas_id_Campana_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."campanas_id_Campana_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: campanas_id_Campana_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."campanas_id_Campana_seq" OWNED BY public.campanas."id_Campana";


--
-- Name: categorias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categorias (
    "Id_Categoria" integer NOT NULL,
    "Nombre_Categoria" character varying(50) NOT NULL
);


--
-- Name: categorias_Id_Categoria_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."categorias_Id_Categoria_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categorias_Id_Categoria_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."categorias_Id_Categoria_seq" OWNED BY public.categorias."Id_Categoria";


--
-- Name: colonias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.colonias (
    "Id_Colonia" integer NOT NULL,
    "Nombre_Colonia" character varying(100) NOT NULL,
    "Id_mucipio" integer NOT NULL,
    "Cp" integer NOT NULL
);


--
-- Name: colonias_Id_Colonia_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."colonias_Id_Colonia_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: colonias_Id_Colonia_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."colonias_Id_Colonia_seq" OWNED BY public.colonias."Id_Colonia";


--
-- Name: direcciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.direcciones (
    "Id_Direccion" integer NOT NULL,
    "Id_Colonia" integer NOT NULL,
    "Calle" character varying(100) NOT NULL,
    "No_exterior" character varying(20) NOT NULL
);


--
-- Name: direcciones_Id_Direccion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."direcciones_Id_Direccion_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: direcciones_Id_Direccion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."direcciones_Id_Direccion_seq" OWNED BY public.direcciones."Id_Direccion";


--
-- Name: donaciones; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.donaciones (
    "Id_Donacion" integer NOT NULL,
    "Id_Usuario" integer NOT NULL,
    "id_Categoria" integer NOT NULL,
    "Id_Condicion" character varying(50) NOT NULL,
    "Cantidad" double precision NOT NULL,
    "Id_Unidad" integer NOT NULL,
    "Marca" character varying(100),
    "Id_Albergue" integer NOT NULL,
    "Fecha_Donacion" date DEFAULT CURRENT_DATE NOT NULL
);


--
-- Name: donaciones_Id_Donacion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."donaciones_Id_Donacion_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: donaciones_Id_Donacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."donaciones_Id_Donacion_seq" OWNED BY public.donaciones."Id_Donacion";


--
-- Name: estados_campanas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.estados_campanas (
    "Id_Estado_Campana" integer NOT NULL,
    "Nombre_Estado" character varying(50) NOT NULL
);


--
-- Name: estados_campanas_Id_Estado_Campana_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."estados_campanas_Id_Estado_Campana_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: estados_campanas_Id_Estado_Campana_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."estados_campanas_Id_Estado_Campana_seq" OWNED BY public.estados_campanas."Id_Estado_Campana";


--
-- Name: estados_rep; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.estados_rep (
    "Id_Estado" integer NOT NULL,
    "Nombre_Estado" character varying(50) NOT NULL
);


--
-- Name: estados_rep_Id_Estado_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."estados_rep_Id_Estado_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: estados_rep_Id_Estado_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."estados_rep_Id_Estado_seq" OWNED BY public.estados_rep."Id_Estado";


--
-- Name: generos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.generos (
    "Id_Genero" integer NOT NULL,
    "Nombre_genero" character varying(50) NOT NULL
);


--
-- Name: generos_Id_Genero_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."generos_Id_Genero_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: generos_Id_Genero_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."generos_Id_Genero_seq" OWNED BY public.generos."Id_Genero";


--
-- Name: inscripciones_voluntariados; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.inscripciones_voluntariados (
    "Id_Inscripcion" integer NOT NULL,
    "Id_Usuario" integer NOT NULL,
    "Id_Voluntariado" integer NOT NULL,
    "Fecha_Inscripcion" timestamp with time zone DEFAULT now()
);


--
-- Name: inscripciones_voluntariados_Id_Inscripcion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."inscripciones_voluntariados_Id_Inscripcion_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inscripciones_voluntariados_Id_Inscripcion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."inscripciones_voluntariados_Id_Inscripcion_seq" OWNED BY public.inscripciones_voluntariados."Id_Inscripcion";


--
-- Name: municipios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.municipios (
    "Id_Municipio" integer NOT NULL,
    "Nombre_Municipio" character varying(50) NOT NULL,
    "Id_Estado" integer NOT NULL
);


--
-- Name: municipios_Id_Municipio_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."municipios_Id_Municipio_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: municipios_Id_Municipio_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."municipios_Id_Municipio_seq" OWNED BY public.municipios."Id_Municipio";


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    "Id_Rol" integer NOT NULL,
    "Nombre_Rol" character varying(50) NOT NULL
);


--
-- Name: roles_Id_Rol_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."roles_Id_Rol_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_Id_Rol_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."roles_Id_Rol_seq" OWNED BY public.roles."Id_Rol";


--
-- Name: unidades; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.unidades (
    "Id_Unidad" integer NOT NULL,
    "Nombre_Unidad" character varying(50) NOT NULL
);


--
-- Name: unidades_Id_Unidad_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."unidades_Id_Unidad_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: unidades_Id_Unidad_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."unidades_Id_Unidad_seq" OWNED BY public.unidades."Id_Unidad";


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuarios (
    "id_Usuario" integer NOT NULL,
    "Nombre" character varying(100) NOT NULL,
    "Apellido_P" character varying(100) NOT NULL,
    "Apellido_M" character varying(100) NOT NULL,
    "Correo" character varying(100) NOT NULL,
    "Password" character varying(250) NOT NULL,
    "Edad" integer NOT NULL,
    "Id_Direccion" integer NOT NULL,
    "Id_Albergue" integer,
    "Id_Rol" integer NOT NULL,
    "Tel" bigint NOT NULL,
    "Id_Genero" integer NOT NULL,
    "Fecha_Registro" timestamp with time zone DEFAULT now()
);


--
-- Name: usuarios_id_Usuario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."usuarios_id_Usuario_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuarios_id_Usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."usuarios_id_Usuario_seq" OWNED BY public.usuarios."id_Usuario";


--
-- Name: voluntariados; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.voluntariados (
    "Id_Voluntariado" integer NOT NULL,
    "Nombre_Voluntariado" character varying(150) NOT NULL,
    "Id_albergue" integer,
    id_campana integer,
    "Fecha_prog" date NOT NULL,
    "Cupo_Max" integer,
    "Hora_inicio" time without time zone NOT NULL,
    "Hora_Fin" time without time zone NOT NULL,
    "Estado_Voluntariado" character varying(50) NOT NULL,
    "Descripcion_Requisitos" text NOT NULL
);


--
-- Name: voluntariados_Id_Voluntariado_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public."voluntariados_Id_Voluntariado_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: voluntariados_Id_Voluntariado_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public."voluntariados_Id_Voluntariado_seq" OWNED BY public.voluntariados."Id_Voluntariado";


--
-- Name: albergues Id_Albergue; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.albergues ALTER COLUMN "Id_Albergue" SET DEFAULT nextval('public."albergues_Id_Albergue_seq"'::regclass);


--
-- Name: campanas id_Campana; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas ALTER COLUMN "id_Campana" SET DEFAULT nextval('public."campanas_id_Campana_seq"'::regclass);


--
-- Name: categorias Id_Categoria; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias ALTER COLUMN "Id_Categoria" SET DEFAULT nextval('public."categorias_Id_Categoria_seq"'::regclass);


--
-- Name: colonias Id_Colonia; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.colonias ALTER COLUMN "Id_Colonia" SET DEFAULT nextval('public."colonias_Id_Colonia_seq"'::regclass);


--
-- Name: direcciones Id_Direccion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.direcciones ALTER COLUMN "Id_Direccion" SET DEFAULT nextval('public."direcciones_Id_Direccion_seq"'::regclass);


--
-- Name: donaciones Id_Donacion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.donaciones ALTER COLUMN "Id_Donacion" SET DEFAULT nextval('public."donaciones_Id_Donacion_seq"'::regclass);


--
-- Name: estados_campanas Id_Estado_Campana; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estados_campanas ALTER COLUMN "Id_Estado_Campana" SET DEFAULT nextval('public."estados_campanas_Id_Estado_Campana_seq"'::regclass);


--
-- Name: estados_rep Id_Estado; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estados_rep ALTER COLUMN "Id_Estado" SET DEFAULT nextval('public."estados_rep_Id_Estado_seq"'::regclass);


--
-- Name: generos Id_Genero; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.generos ALTER COLUMN "Id_Genero" SET DEFAULT nextval('public."generos_Id_Genero_seq"'::regclass);


--
-- Name: inscripciones_voluntariados Id_Inscripcion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inscripciones_voluntariados ALTER COLUMN "Id_Inscripcion" SET DEFAULT nextval('public."inscripciones_voluntariados_Id_Inscripcion_seq"'::regclass);


--
-- Name: municipios Id_Municipio; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipios ALTER COLUMN "Id_Municipio" SET DEFAULT nextval('public."municipios_Id_Municipio_seq"'::regclass);


--
-- Name: roles Id_Rol; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN "Id_Rol" SET DEFAULT nextval('public."roles_Id_Rol_seq"'::regclass);


--
-- Name: unidades Id_Unidad; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.unidades ALTER COLUMN "Id_Unidad" SET DEFAULT nextval('public."unidades_Id_Unidad_seq"'::regclass);


--
-- Name: usuarios id_Usuario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN "id_Usuario" SET DEFAULT nextval('public."usuarios_id_Usuario_seq"'::regclass);


--
-- Name: voluntariados Id_Voluntariado; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.voluntariados ALTER COLUMN "Id_Voluntariado" SET DEFAULT nextval('public."voluntariados_Id_Voluntariado_seq"'::regclass);


--
-- Data for Name: albergues; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.albergues ("Id_Albergue", "Nombre_Albergue", "Capacidad_Max", "Tel_Contacto", "Id_Direccion") FROM stdin;
1	Albergue Yimpathí	80	4421234567	1
2	Centro de Día Meni	50	4429876543	3
3	Chicuarotes	34	22334455	10
\.


--
-- Data for Name: campanas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.campanas ("id_Campana", "Nombre_Campana", "Fecha_Inicio", "Fecha_Fin", "id_Estado_campana", "Descripcion_Objetivos") FROM stdin;
1	Colecta Invernal Qro 2026	2026-11-01	2026-12-31	1	Recolección de cobijas, ropa de invierno y alimentos no perecederos para personas en situación de calle durante la temporada invernal en Santiago de Querétaro.
2	Comedor Comunitario Centro	2026-03-01	2026-06-30	2	Operación de un comedor comunitario en el Centro Histórico de Querétaro, ofreciendo desayunos y comidas calientes a personas vulnerables.
3	Tazos dorado	2026-04-04	2026-03-30	1	jsj
4	Busqueda de coppers	2026-04-12	2026-04-13	1	Se necesitan rastrear coppers
5	Donaciones de cobijas	2026-04-02	2026-04-13	1	Se busca donar cobujas
\.


--
-- Data for Name: categorias; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categorias ("Id_Categoria", "Nombre_Categoria") FROM stdin;
1	Ropa
2	Alimentos
3	Cobijas
4	Higiene personal
5	Medicamentos
\.


--
-- Data for Name: colonias; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.colonias ("Id_Colonia", "Nombre_Colonia", "Id_mucipio", "Cp") FROM stdin;
1	Centro Histórico	1	76000
2	Menchaca	1	76140
3	San Francisquito	1	76030
4	Epigmenio González	1	76038
5	La Negreta	1	76020
6	Zaragoza	2	76246
\.


--
-- Data for Name: direcciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.direcciones ("Id_Direccion", "Id_Colonia", "Calle", "No_exterior") FROM stdin;
1	1	Av. Madero	45
2	2	Calle Menchaca	102
3	3	Av. San Francisquito	78
4	4	Blvd. Epigmenio Glz.	250
5	5	Calle La Negreta	18
6	1	Calle Corregidora Sur	33
7	2	33	33
8	5	jj	jj
9	3	23	223
10	5	3	121
11	5	23	34
12	5	47	2
13	2	12	332
14	2	33	21
15	3	123	34
\.


--
-- Data for Name: donaciones; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.donaciones ("Id_Donacion", "Id_Usuario", "id_Categoria", "Id_Condicion", "Cantidad", "Id_Unidad", "Marca", "Id_Albergue", "Fecha_Donacion") FROM stdin;
1	3	1	Buen estado (Usado)	15	1	\N	1	2026-03-30
2	3	2	Nuevo/Sellado	10	2	La Costeña	2	2026-03-30
3	3	3	Nuevo/Sellado	5	1	San Marcos	1	2026-03-30
4	3	2	Excelente	2	2	77	1	2026-03-30
5	2	2	Buen estado (Usado)	11.52	2	Registro Histórico	2	2025-03-30
6	1	2	Nuevo/Sellado	2.43	4	Registro Histórico	1	2025-03-31
7	2	4	Regular	3.19	2	Registro Histórico	1	2025-03-31
8	2	2	Nuevo/Sellado	13.65	2	Registro Histórico	2	2025-04-01
9	3	3	Buen estado (Usado)	11.27	1	Registro Histórico	2	2025-04-01
10	2	1	Regular	10.86	1	Registro Histórico	2	2025-04-01
11	1	1	Regular	4.75	1	Registro Histórico	2	2025-04-02
12	1	2	Nuevo/Sellado	12.2	4	Registro Histórico	2	2025-04-03
13	3	5	Buen estado (Usado)	6.34	4	Registro Histórico	2	2025-04-04
14	3	2	Regular	14.68	4	Registro Histórico	2	2025-04-04
15	3	1	Buen estado (Usado)	3.84	1	Registro Histórico	1	2025-04-05
16	2	2	Buen estado (Usado)	3.21	3	Registro Histórico	1	2025-04-05
17	2	1	Nuevo/Sellado	7.89	1	Registro Histórico	2	2025-04-06
18	2	2	Nuevo/Sellado	11.01	3	Registro Histórico	2	2025-04-06
19	2	2	Nuevo/Sellado	10.95	3	Registro Histórico	1	2025-04-07
20	3	2	Buen estado (Usado)	6.53	4	Registro Histórico	2	2025-04-07
21	3	4	Regular	12.94	4	Registro Histórico	1	2025-04-08
22	2	2	Regular	8.9	4	Registro Histórico	1	2025-04-08
23	2	2	Buen estado (Usado)	5.72	3	Registro Histórico	1	2025-04-09
24	3	2	Nuevo/Sellado	3.59	4	Registro Histórico	1	2025-04-10
25	3	3	Nuevo/Sellado	2.33	1	Registro Histórico	1	2025-04-10
26	1	1	Buen estado (Usado)	3.01	1	Registro Histórico	2	2025-04-11
27	3	4	Regular	14.34	2	Registro Histórico	2	2025-04-12
28	2	2	Regular	12.04	2	Registro Histórico	2	2025-04-12
29	1	5	Nuevo/Sellado	9.82	2	Registro Histórico	2	2025-04-13
30	3	2	Nuevo/Sellado	10.16	2	Registro Histórico	2	2025-04-13
31	1	3	Regular	5.98	1	Registro Histórico	1	2025-04-13
32	2	3	Regular	7.36	1	Registro Histórico	2	2025-04-14
33	1	1	Buen estado (Usado)	2.43	1	Registro Histórico	1	2025-04-14
34	3	2	Regular	7.01	3	Registro Histórico	1	2025-04-14
35	3	1	Nuevo/Sellado	14.02	1	Registro Histórico	2	2025-04-15
36	2	1	Nuevo/Sellado	7.16	1	Registro Histórico	1	2025-04-15
37	1	3	Regular	14.93	1	Registro Histórico	1	2025-04-15
38	2	3	Nuevo/Sellado	11.84	1	Registro Histórico	2	2025-04-16
39	3	3	Regular	6.15	1	Registro Histórico	1	2025-04-16
40	2	1	Regular	3.92	1	Registro Histórico	2	2025-04-16
41	2	2	Nuevo/Sellado	5.88	4	Registro Histórico	2	2025-04-17
42	1	4	Nuevo/Sellado	3.15	3	Registro Histórico	2	2025-04-17
43	2	5	Nuevo/Sellado	7.18	2	Registro Histórico	2	2025-04-17
44	3	4	Nuevo/Sellado	9.61	2	Registro Histórico	2	2025-04-18
45	1	3	Nuevo/Sellado	11.92	1	Registro Histórico	2	2025-04-18
46	3	5	Buen estado (Usado)	11.15	2	Registro Histórico	1	2025-04-18
47	1	5	Buen estado (Usado)	10.42	2	Registro Histórico	1	2025-04-19
48	2	4	Nuevo/Sellado	10.21	3	Registro Histórico	2	2025-04-19
49	1	1	Regular	13.65	1	Registro Histórico	2	2025-04-19
50	3	4	Nuevo/Sellado	14.63	3	Registro Histórico	1	2025-04-20
51	3	5	Regular	3.02	4	Registro Histórico	1	2025-04-21
52	2	5	Nuevo/Sellado	14.09	2	Registro Histórico	1	2025-04-21
53	1	2	Buen estado (Usado)	7.38	3	Registro Histórico	2	2025-04-21
54	3	3	Nuevo/Sellado	9.99	1	Registro Histórico	1	2025-04-22
55	2	1	Nuevo/Sellado	5.57	1	Registro Histórico	1	2025-04-23
56	1	2	Buen estado (Usado)	2.44	3	Registro Histórico	2	2025-04-23
57	2	2	Buen estado (Usado)	13.82	3	Registro Histórico	1	2025-04-23
58	1	4	Nuevo/Sellado	10.1	4	Registro Histórico	2	2025-04-24
59	3	2	Buen estado (Usado)	10.51	3	Registro Histórico	2	2025-04-25
60	2	2	Regular	10.05	3	Registro Histórico	1	2025-04-26
61	1	5	Regular	2.85	3	Registro Histórico	1	2025-04-26
62	2	2	Nuevo/Sellado	8.58	3	Registro Histórico	2	2025-04-26
63	1	5	Buen estado (Usado)	6.19	3	Registro Histórico	2	2025-04-27
64	1	1	Regular	9.35	1	Registro Histórico	2	2025-04-27
65	2	3	Buen estado (Usado)	8.08	1	Registro Histórico	2	2025-04-27
66	3	5	Buen estado (Usado)	13.23	2	Registro Histórico	2	2025-04-28
67	3	2	Nuevo/Sellado	9.23	3	Registro Histórico	2	2025-04-28
68	3	4	Nuevo/Sellado	2.87	4	Registro Histórico	1	2025-04-28
69	2	2	Buen estado (Usado)	10.33	4	Registro Histórico	2	2025-04-29
70	3	2	Regular	14.67	2	Registro Histórico	2	2025-04-29
71	3	3	Nuevo/Sellado	12.57	1	Registro Histórico	1	2025-04-29
72	3	2	Nuevo/Sellado	2.74	2	Registro Histórico	1	2025-04-30
73	3	1	Regular	4.73	1	Registro Histórico	2	2025-04-30
74	1	2	Buen estado (Usado)	11.69	4	Registro Histórico	1	2025-05-01
75	1	3	Regular	11.24	1	Registro Histórico	1	2025-05-01
76	2	2	Buen estado (Usado)	8.69	4	Registro Histórico	1	2025-05-02
77	3	2	Nuevo/Sellado	6.99	3	Registro Histórico	2	2025-05-02
78	2	2	Regular	10.64	3	Registro Histórico	1	2025-05-02
79	2	2	Buen estado (Usado)	4.79	2	Registro Histórico	1	2025-05-03
80	2	2	Regular	11.34	4	Registro Histórico	2	2025-05-04
81	3	2	Nuevo/Sellado	11.19	4	Registro Histórico	2	2025-05-04
82	2	4	Regular	11.6	2	Registro Histórico	2	2025-05-05
83	3	4	Nuevo/Sellado	2.22	4	Registro Histórico	2	2025-05-05
84	2	2	Nuevo/Sellado	7.51	4	Registro Histórico	1	2025-05-05
85	3	2	Regular	10.53	3	Registro Histórico	2	2025-05-06
86	2	1	Nuevo/Sellado	11.4	1	Registro Histórico	2	2025-05-06
87	3	1	Regular	3.46	1	Registro Histórico	2	2025-05-07
88	3	5	Buen estado (Usado)	2.52	2	Registro Histórico	2	2025-05-07
89	1	4	Buen estado (Usado)	11.14	2	Registro Histórico	1	2025-05-08
90	3	4	Nuevo/Sellado	9.87	3	Registro Histórico	1	2025-05-08
91	2	5	Nuevo/Sellado	7.48	2	Registro Histórico	2	2025-05-08
92	1	3	Nuevo/Sellado	2.47	1	Registro Histórico	2	2025-05-09
93	1	3	Regular	6.81	1	Registro Histórico	2	2025-05-10
94	2	2	Buen estado (Usado)	11.55	4	Registro Histórico	2	2025-05-11
95	1	2	Regular	7.16	3	Registro Histórico	1	2025-05-11
96	2	5	Nuevo/Sellado	12.53	3	Registro Histórico	1	2025-05-12
97	2	2	Regular	4.65	2	Registro Histórico	2	2025-05-13
98	1	1	Nuevo/Sellado	9.28	1	Registro Histórico	1	2025-05-13
99	1	1	Regular	9.54	1	Registro Histórico	1	2025-05-13
100	3	5	Nuevo/Sellado	6.92	3	Registro Histórico	2	2025-05-14
101	2	3	Buen estado (Usado)	4.97	1	Registro Histórico	1	2025-05-14
102	1	2	Buen estado (Usado)	5.92	3	Registro Histórico	1	2025-05-15
103	3	5	Regular	5.15	2	Registro Histórico	2	2025-05-15
104	3	2	Regular	9.13	3	Registro Histórico	1	2025-05-15
105	3	2	Regular	2.01	2	Registro Histórico	1	2025-05-16
106	3	3	Regular	7.05	1	Registro Histórico	1	2025-05-16
107	2	4	Regular	12.43	4	Registro Histórico	1	2025-05-16
108	2	5	Regular	11.35	4	Registro Histórico	2	2025-05-17
109	2	5	Buen estado (Usado)	7.22	4	Registro Histórico	2	2025-05-17
110	1	2	Buen estado (Usado)	11.25	3	Registro Histórico	1	2025-05-18
111	2	2	Buen estado (Usado)	10.97	4	Registro Histórico	1	2025-05-18
112	3	2	Buen estado (Usado)	3.06	4	Registro Histórico	2	2025-05-19
113	3	2	Regular	2.05	2	Registro Histórico	2	2025-05-20
114	1	4	Regular	11.9	4	Registro Histórico	1	2025-05-20
115	2	2	Nuevo/Sellado	11.01	4	Registro Histórico	1	2025-05-21
116	1	1	Nuevo/Sellado	13.03	1	Registro Histórico	1	2025-05-22
117	1	4	Nuevo/Sellado	5.41	4	Registro Histórico	2	2025-05-22
118	3	4	Nuevo/Sellado	14.33	4	Registro Histórico	1	2025-05-22
119	2	4	Buen estado (Usado)	2.8	3	Registro Histórico	2	2025-05-23
120	3	2	Regular	2.82	2	Registro Histórico	1	2025-05-23
121	2	1	Regular	3.74	1	Registro Histórico	2	2025-05-23
122	3	1	Nuevo/Sellado	6.88	1	Registro Histórico	2	2025-05-24
123	1	1	Regular	7.53	1	Registro Histórico	2	2025-05-25
124	2	1	Regular	7.78	1	Registro Histórico	2	2025-05-25
125	2	1	Buen estado (Usado)	9.27	1	Registro Histórico	1	2025-05-26
126	2	5	Regular	7.85	3	Registro Histórico	2	2025-05-27
127	3	5	Buen estado (Usado)	8.01	3	Registro Histórico	1	2025-05-27
128	1	5	Nuevo/Sellado	6.29	3	Registro Histórico	2	2025-05-28
129	1	2	Buen estado (Usado)	12.92	4	Registro Histórico	1	2025-05-29
130	3	3	Buen estado (Usado)	11.86	1	Registro Histórico	2	2025-05-29
131	1	5	Nuevo/Sellado	9.15	2	Registro Histórico	1	2025-05-29
132	2	2	Regular	10.31	3	Registro Histórico	1	2025-05-30
133	1	3	Nuevo/Sellado	10.39	1	Registro Histórico	1	2025-05-31
134	1	4	Buen estado (Usado)	5.08	2	Registro Histórico	2	2025-06-01
135	3	1	Nuevo/Sellado	6.02	1	Registro Histórico	1	2025-06-01
136	3	1	Regular	14.79	1	Registro Histórico	1	2025-06-01
137	1	4	Buen estado (Usado)	3.84	3	Registro Histórico	1	2025-06-02
138	2	2	Nuevo/Sellado	10.22	2	Registro Histórico	1	2025-06-02
139	3	2	Buen estado (Usado)	4.78	4	Registro Histórico	2	2025-06-03
140	3	4	Nuevo/Sellado	12.46	2	Registro Histórico	1	2025-06-03
141	3	4	Regular	8.85	4	Registro Histórico	1	2025-06-03
142	3	4	Regular	5.3	3	Registro Histórico	1	2025-06-04
143	3	1	Buen estado (Usado)	12.32	1	Registro Histórico	1	2025-06-04
144	1	3	Nuevo/Sellado	12.97	1	Registro Histórico	1	2025-06-05
145	1	2	Nuevo/Sellado	12.6	4	Registro Histórico	2	2025-06-05
146	3	2	Nuevo/Sellado	2.63	2	Registro Histórico	1	2025-06-06
147	3	5	Buen estado (Usado)	5.93	4	Registro Histórico	2	2025-06-06
148	2	2	Regular	7.06	2	Registro Histórico	1	2025-06-07
149	1	5	Regular	12.26	4	Registro Histórico	2	2025-06-08
150	1	2	Nuevo/Sellado	13.91	4	Registro Histórico	1	2025-06-09
151	3	2	Nuevo/Sellado	4.98	2	Registro Histórico	1	2025-06-09
152	3	1	Buen estado (Usado)	14.24	1	Registro Histórico	1	2025-06-10
153	2	5	Buen estado (Usado)	3.06	2	Registro Histórico	2	2025-06-10
154	2	2	Nuevo/Sellado	11.11	2	Registro Histórico	2	2025-06-10
155	2	2	Buen estado (Usado)	13.84	2	Registro Histórico	1	2025-06-11
156	3	4	Regular	11.41	3	Registro Histórico	2	2025-06-11
157	2	4	Regular	3.76	3	Registro Histórico	1	2025-06-11
158	3	2	Nuevo/Sellado	5.34	3	Registro Histórico	2	2025-06-12
159	1	4	Buen estado (Usado)	12.97	3	Registro Histórico	1	2025-06-13
160	2	1	Regular	14.69	1	Registro Histórico	1	2025-06-13
161	1	2	Buen estado (Usado)	8.42	2	Registro Histórico	2	2025-06-13
162	2	5	Regular	2.15	3	Registro Histórico	1	2025-06-14
163	1	2	Nuevo/Sellado	9.77	3	Registro Histórico	2	2025-06-14
164	1	2	Nuevo/Sellado	11.17	4	Registro Histórico	1	2025-06-15
165	2	3	Regular	12.03	1	Registro Histórico	2	2025-06-16
166	3	2	Regular	8.97	3	Registro Histórico	1	2025-06-16
167	2	4	Nuevo/Sellado	4.1	4	Registro Histórico	2	2025-06-16
168	2	5	Buen estado (Usado)	5.2	4	Registro Histórico	1	2025-06-17
169	3	4	Regular	3.62	3	Registro Histórico	2	2025-06-17
170	1	1	Buen estado (Usado)	11.69	1	Registro Histórico	1	2025-06-18
171	1	2	Regular	8.48	4	Registro Histórico	1	2025-06-18
172	2	2	Nuevo/Sellado	2.22	3	Registro Histórico	2	2025-06-18
173	1	4	Buen estado (Usado)	8.22	3	Registro Histórico	2	2025-06-19
174	2	1	Buen estado (Usado)	14.68	1	Registro Histórico	1	2025-06-19
175	3	1	Nuevo/Sellado	9.75	1	Registro Histórico	1	2025-06-19
176	3	4	Nuevo/Sellado	10.56	3	Registro Histórico	1	2025-06-20
177	1	1	Nuevo/Sellado	3.93	1	Registro Histórico	2	2025-06-20
178	3	2	Regular	2.24	3	Registro Histórico	1	2025-06-21
179	1	5	Regular	13.65	3	Registro Histórico	2	2025-06-22
180	3	5	Regular	13.49	3	Registro Histórico	2	2025-06-23
181	3	4	Nuevo/Sellado	3.11	3	Registro Histórico	2	2025-06-23
182	2	3	Buen estado (Usado)	9.18	1	Registro Histórico	1	2025-06-23
183	1	1	Regular	11.45	1	Registro Histórico	1	2025-06-24
184	3	2	Buen estado (Usado)	7.52	4	Registro Histórico	2	2025-06-24
185	3	3	Nuevo/Sellado	7.36	1	Registro Histórico	2	2025-06-25
186	2	3	Nuevo/Sellado	5.16	1	Registro Histórico	2	2025-06-25
187	1	1	Buen estado (Usado)	3.12	1	Registro Histórico	1	2025-06-26
188	2	5	Buen estado (Usado)	14.25	2	Registro Histórico	2	2025-06-26
189	3	4	Regular	8.75	3	Registro Histórico	1	2025-06-26
190	2	2	Regular	11.53	4	Registro Histórico	1	2025-06-27
191	2	1	Nuevo/Sellado	14.38	1	Registro Histórico	2	2025-06-28
192	2	4	Buen estado (Usado)	12.5	2	Registro Histórico	1	2025-06-28
193	3	2	Nuevo/Sellado	12.56	2	Registro Histórico	2	2025-06-29
194	2	2	Regular	12.14	3	Registro Histórico	1	2025-06-29
195	1	5	Regular	6.02	3	Registro Histórico	1	2025-06-29
196	2	4	Regular	12.32	4	Registro Histórico	1	2025-06-30
197	3	4	Buen estado (Usado)	13.47	3	Registro Histórico	1	2025-07-01
198	3	2	Nuevo/Sellado	5.03	4	Registro Histórico	2	2025-07-01
199	1	2	Regular	7.08	4	Registro Histórico	2	2025-07-02
200	3	1	Nuevo/Sellado	3.23	1	Registro Histórico	2	2025-07-03
201	2	1	Buen estado (Usado)	6.84	1	Registro Histórico	1	2025-07-03
202	1	4	Nuevo/Sellado	10.93	2	Registro Histórico	2	2025-07-03
203	1	3	Nuevo/Sellado	7.56	1	Registro Histórico	2	2025-07-04
204	3	2	Nuevo/Sellado	9.75	2	Registro Histórico	1	2025-07-05
205	2	2	Regular	11.31	4	Registro Histórico	1	2025-07-06
206	1	4	Buen estado (Usado)	3.49	3	Registro Histórico	2	2025-07-07
207	1	2	Nuevo/Sellado	5.47	4	Registro Histórico	2	2025-07-07
208	1	5	Buen estado (Usado)	12.22	4	Registro Histórico	2	2025-07-08
209	3	2	Nuevo/Sellado	14.64	2	Registro Histórico	1	2025-07-09
210	1	4	Buen estado (Usado)	5.02	2	Registro Histórico	2	2025-07-09
211	1	1	Regular	14.79	1	Registro Histórico	2	2025-07-09
212	2	5	Buen estado (Usado)	8.4	3	Registro Histórico	2	2025-07-10
213	3	3	Regular	8.89	1	Registro Histórico	1	2025-07-10
214	2	2	Buen estado (Usado)	2.33	4	Registro Histórico	1	2025-07-11
215	2	4	Nuevo/Sellado	10.12	3	Registro Histórico	2	2025-07-11
216	2	3	Regular	8.65	1	Registro Histórico	1	2025-07-12
217	2	1	Regular	2.97	1	Registro Histórico	2	2025-07-13
218	1	5	Buen estado (Usado)	14.06	2	Registro Histórico	2	2025-07-14
219	1	4	Regular	8.36	3	Registro Histórico	1	2025-07-14
220	3	5	Buen estado (Usado)	12.05	4	Registro Histórico	1	2025-07-14
221	3	1	Regular	8.04	1	Registro Histórico	2	2025-07-15
222	1	1	Nuevo/Sellado	14.3	1	Registro Histórico	1	2025-07-15
223	3	4	Nuevo/Sellado	4.63	4	Registro Histórico	1	2025-07-16
224	1	2	Regular	14.95	4	Registro Histórico	1	2025-07-16
225	2	2	Buen estado (Usado)	7.22	3	Registro Histórico	2	2025-07-16
226	1	5	Nuevo/Sellado	7.3	3	Registro Histórico	2	2025-07-17
227	2	3	Regular	12.32	1	Registro Histórico	1	2025-07-18
228	3	2	Buen estado (Usado)	4.29	3	Registro Histórico	2	2025-07-19
229	3	5	Buen estado (Usado)	9.91	2	Registro Histórico	1	2025-07-19
230	2	2	Buen estado (Usado)	2.84	2	Registro Histórico	2	2025-07-20
231	2	1	Nuevo/Sellado	10.06	1	Registro Histórico	1	2025-07-20
232	2	2	Regular	14.38	2	Registro Histórico	1	2025-07-20
233	2	5	Buen estado (Usado)	10.05	3	Registro Histórico	1	2025-07-21
234	3	4	Buen estado (Usado)	13.5	3	Registro Histórico	2	2025-07-21
235	1	2	Nuevo/Sellado	14.51	3	Registro Histórico	2	2025-07-22
236	2	1	Regular	8.24	1	Registro Histórico	1	2025-07-22
237	1	2	Nuevo/Sellado	12.5	4	Registro Histórico	1	2025-07-22
238	3	4	Buen estado (Usado)	12	4	Registro Histórico	1	2025-07-23
239	3	5	Buen estado (Usado)	3.67	4	Registro Histórico	2	2025-07-24
240	2	1	Buen estado (Usado)	5.57	1	Registro Histórico	1	2025-07-24
241	3	3	Regular	4.68	1	Registro Histórico	2	2025-07-24
242	2	1	Regular	7.83	1	Registro Histórico	1	2025-07-25
243	3	1	Buen estado (Usado)	6.31	1	Registro Histórico	2	2025-07-26
244	3	5	Buen estado (Usado)	6.92	4	Registro Histórico	1	2025-07-26
245	1	3	Regular	12.91	1	Registro Histórico	1	2025-07-26
246	2	3	Nuevo/Sellado	7.34	1	Registro Histórico	2	2025-07-27
247	2	2	Regular	12.98	4	Registro Histórico	1	2025-07-27
248	2	2	Regular	3.71	4	Registro Histórico	2	2025-07-28
249	2	4	Regular	13.71	3	Registro Histórico	1	2025-07-28
250	2	5	Nuevo/Sellado	13.71	2	Registro Histórico	1	2025-07-29
251	2	2	Buen estado (Usado)	4.56	3	Registro Histórico	1	2025-07-30
252	1	5	Buen estado (Usado)	11.58	2	Registro Histórico	2	2025-07-31
253	2	5	Nuevo/Sellado	11.22	4	Registro Histórico	1	2025-07-31
254	2	4	Nuevo/Sellado	5.13	4	Registro Histórico	1	2025-08-01
255	1	2	Buen estado (Usado)	14.33	4	Registro Histórico	1	2025-08-01
256	3	2	Nuevo/Sellado	8.25	2	Registro Histórico	2	2025-08-02
257	3	2	Buen estado (Usado)	4.1	2	Registro Histórico	2	2025-08-03
258	2	2	Regular	13.3	2	Registro Histórico	2	2025-08-04
259	3	2	Regular	4.52	2	Registro Histórico	1	2025-08-05
260	1	3	Buen estado (Usado)	6.46	1	Registro Histórico	1	2025-08-06
261	1	4	Regular	9.29	4	Registro Histórico	2	2025-08-06
262	1	1	Buen estado (Usado)	3.98	1	Registro Histórico	1	2025-08-07
263	3	4	Buen estado (Usado)	8.95	2	Registro Histórico	2	2025-08-07
264	1	2	Buen estado (Usado)	4.89	3	Registro Histórico	2	2025-08-08
265	2	3	Buen estado (Usado)	3.25	1	Registro Histórico	1	2025-08-08
266	2	2	Buen estado (Usado)	7.9	2	Registro Histórico	2	2025-08-08
267	3	2	Regular	5.21	4	Registro Histórico	1	2025-08-09
268	2	1	Buen estado (Usado)	11.49	1	Registro Histórico	2	2025-08-09
269	3	2	Buen estado (Usado)	11.74	3	Registro Histórico	1	2025-08-09
270	3	4	Nuevo/Sellado	11.21	3	Registro Histórico	2	2025-08-10
271	2	1	Regular	12.7	1	Registro Histórico	1	2025-08-11
272	1	4	Nuevo/Sellado	9.24	4	Registro Histórico	1	2025-08-12
273	1	1	Nuevo/Sellado	12.28	1	Registro Histórico	2	2025-08-13
274	1	4	Regular	9.51	2	Registro Histórico	2	2025-08-13
275	1	1	Buen estado (Usado)	7.9	1	Registro Histórico	2	2025-08-14
276	2	5	Regular	4.02	3	Registro Histórico	1	2025-08-14
277	2	4	Nuevo/Sellado	13.76	3	Registro Histórico	2	2025-08-14
278	1	3	Nuevo/Sellado	11.29	1	Registro Histórico	1	2025-08-15
279	2	2	Nuevo/Sellado	8.94	4	Registro Histórico	2	2025-08-15
280	3	5	Nuevo/Sellado	6.16	2	Registro Histórico	2	2025-08-16
281	2	3	Regular	13.16	1	Registro Histórico	2	2025-08-16
282	1	2	Nuevo/Sellado	5.17	3	Registro Histórico	1	2025-08-16
283	2	4	Nuevo/Sellado	3.64	2	Registro Histórico	1	2025-08-17
284	2	4	Nuevo/Sellado	13.82	3	Registro Histórico	1	2025-08-18
285	3	4	Buen estado (Usado)	2.47	3	Registro Histórico	2	2025-08-19
286	2	2	Buen estado (Usado)	12.8	4	Registro Histórico	2	2025-08-19
287	2	3	Regular	6.32	1	Registro Histórico	1	2025-08-20
288	3	5	Nuevo/Sellado	3.15	4	Registro Histórico	2	2025-08-20
289	1	1	Buen estado (Usado)	5.07	1	Registro Histórico	1	2025-08-20
290	3	2	Regular	11.03	3	Registro Histórico	2	2025-08-21
291	1	5	Nuevo/Sellado	14.77	3	Registro Histórico	1	2025-08-22
292	1	4	Regular	4.44	2	Registro Histórico	2	2025-08-22
293	1	1	Regular	14.6	1	Registro Histórico	2	2025-08-23
294	3	2	Regular	5.79	4	Registro Histórico	2	2025-08-24
295	1	2	Regular	6.27	4	Registro Histórico	1	2025-08-25
296	2	2	Nuevo/Sellado	13.91	4	Registro Histórico	1	2025-08-25
297	2	3	Nuevo/Sellado	12.17	1	Registro Histórico	1	2025-08-26
298	3	3	Regular	7.1	1	Registro Histórico	1	2025-08-27
299	2	1	Regular	12.88	1	Registro Histórico	1	2025-08-27
300	1	3	Buen estado (Usado)	8.14	1	Registro Histórico	1	2025-08-27
301	1	1	Buen estado (Usado)	13.14	1	Registro Histórico	2	2025-08-28
302	2	4	Regular	8.11	3	Registro Histórico	2	2025-08-29
303	1	3	Buen estado (Usado)	3.81	1	Registro Histórico	2	2025-08-30
304	1	2	Nuevo/Sellado	13.52	3	Registro Histórico	1	2025-08-30
305	3	2	Buen estado (Usado)	9.93	3	Registro Histórico	1	2025-08-31
306	1	4	Nuevo/Sellado	8.91	3	Registro Histórico	2	2025-08-31
307	3	3	Nuevo/Sellado	7.21	1	Registro Histórico	1	2025-09-01
308	3	2	Nuevo/Sellado	3.71	4	Registro Histórico	1	2025-09-01
309	1	4	Regular	4.4	3	Registro Histórico	2	2025-09-02
310	2	2	Regular	7.79	3	Registro Histórico	1	2025-09-02
311	1	4	Buen estado (Usado)	11.45	2	Registro Histórico	1	2025-09-02
312	2	2	Buen estado (Usado)	6.33	2	Registro Histórico	1	2025-09-03
313	2	1	Nuevo/Sellado	14.91	1	Registro Histórico	1	2025-09-04
314	1	2	Nuevo/Sellado	7.66	3	Registro Histórico	2	2025-09-05
315	1	1	Buen estado (Usado)	12.72	1	Registro Histórico	1	2025-09-05
316	1	5	Regular	14.93	3	Registro Histórico	1	2025-09-06
317	2	1	Buen estado (Usado)	8.71	1	Registro Histórico	2	2025-09-07
318	1	4	Buen estado (Usado)	8.37	2	Registro Histórico	1	2025-09-07
319	2	2	Nuevo/Sellado	5.39	4	Registro Histórico	2	2025-09-08
320	3	2	Regular	9.96	2	Registro Histórico	1	2025-09-09
321	3	2	Buen estado (Usado)	14.37	2	Registro Histórico	1	2025-09-09
322	1	2	Buen estado (Usado)	6.86	3	Registro Histórico	2	2025-09-09
323	3	4	Buen estado (Usado)	11.3	3	Registro Histórico	2	2025-09-10
324	1	4	Nuevo/Sellado	8.49	2	Registro Histórico	1	2025-09-10
325	2	2	Buen estado (Usado)	4.09	3	Registro Histórico	2	2025-09-11
326	2	3	Nuevo/Sellado	7.45	1	Registro Histórico	2	2025-09-12
327	2	1	Nuevo/Sellado	6.31	1	Registro Histórico	2	2025-09-12
328	1	2	Nuevo/Sellado	6.55	4	Registro Histórico	1	2025-09-12
329	2	1	Nuevo/Sellado	12.53	1	Registro Histórico	1	2025-09-13
330	3	2	Nuevo/Sellado	14.64	4	Registro Histórico	2	2025-09-13
331	2	4	Buen estado (Usado)	2.18	2	Registro Histórico	2	2025-09-14
332	2	2	Regular	9.8	3	Registro Histórico	2	2025-09-14
333	2	5	Buen estado (Usado)	9.97	3	Registro Histórico	2	2025-09-15
334	3	4	Regular	2.17	2	Registro Histórico	1	2025-09-16
335	1	3	Regular	13.25	1	Registro Histórico	1	2025-09-16
336	2	2	Nuevo/Sellado	12.41	3	Registro Histórico	1	2025-09-16
337	2	2	Regular	7.9	4	Registro Histórico	2	2025-09-17
338	1	2	Regular	3.01	3	Registro Histórico	1	2025-09-17
339	3	4	Regular	13.26	3	Registro Histórico	1	2025-09-18
340	1	1	Nuevo/Sellado	4.76	1	Registro Histórico	1	2025-09-18
341	2	2	Nuevo/Sellado	3.99	2	Registro Histórico	2	2025-09-18
342	3	2	Regular	10.92	2	Registro Histórico	2	2025-09-19
343	2	5	Regular	12.69	4	Registro Histórico	1	2025-09-19
344	3	5	Nuevo/Sellado	12.94	2	Registro Histórico	2	2025-09-20
345	3	5	Regular	12.03	2	Registro Histórico	2	2025-09-21
346	3	1	Buen estado (Usado)	5.86	1	Registro Histórico	2	2025-09-21
347	3	2	Buen estado (Usado)	7.78	2	Registro Histórico	2	2025-09-22
348	1	1	Buen estado (Usado)	14.72	1	Registro Histórico	1	2025-09-22
349	2	5	Buen estado (Usado)	11.26	4	Registro Histórico	1	2025-09-23
350	3	2	Regular	6.44	2	Registro Histórico	2	2025-09-24
351	2	5	Nuevo/Sellado	4.11	2	Registro Histórico	1	2025-09-24
352	3	5	Regular	4.69	4	Registro Histórico	1	2025-09-24
353	1	1	Nuevo/Sellado	4.37	1	Registro Histórico	1	2025-09-25
354	2	5	Nuevo/Sellado	13.98	2	Registro Histórico	2	2025-09-25
355	1	2	Regular	8.61	4	Registro Histórico	2	2025-09-25
356	3	2	Buen estado (Usado)	3.61	3	Registro Histórico	2	2025-09-26
357	1	4	Regular	2.43	2	Registro Histórico	1	2025-09-26
358	3	2	Buen estado (Usado)	14.26	2	Registro Histórico	1	2025-09-26
359	2	2	Buen estado (Usado)	11.65	4	Registro Histórico	2	2025-09-27
360	1	2	Regular	6.54	3	Registro Histórico	2	2025-09-27
361	2	1	Regular	2.69	1	Registro Histórico	2	2025-09-27
362	1	2	Nuevo/Sellado	3.85	2	Registro Histórico	2	2025-09-28
363	3	4	Nuevo/Sellado	12.13	3	Registro Histórico	1	2025-09-28
364	2	4	Buen estado (Usado)	4.42	4	Registro Histórico	2	2025-09-29
365	3	4	Nuevo/Sellado	7.41	3	Registro Histórico	1	2025-09-29
366	3	2	Buen estado (Usado)	14.64	4	Registro Histórico	2	2025-09-30
367	1	1	Buen estado (Usado)	7.61	1	Registro Histórico	2	2025-10-01
368	1	2	Buen estado (Usado)	13.22	4	Registro Histórico	1	2025-10-02
369	2	1	Regular	4.68	1	Registro Histórico	1	2025-10-02
370	2	2	Nuevo/Sellado	5.82	4	Registro Histórico	1	2025-10-02
371	3	4	Regular	7.05	2	Registro Histórico	1	2025-10-03
372	2	3	Buen estado (Usado)	3.81	1	Registro Histórico	1	2025-10-03
373	3	2	Nuevo/Sellado	12.94	4	Registro Histórico	2	2025-10-03
374	3	3	Regular	3.07	1	Registro Histórico	1	2025-10-04
375	1	3	Nuevo/Sellado	7.06	1	Registro Histórico	1	2025-10-05
376	3	2	Regular	8.47	4	Registro Histórico	2	2025-10-06
377	2	2	Nuevo/Sellado	2.34	4	Registro Histórico	1	2025-10-06
378	3	2	Buen estado (Usado)	2.49	3	Registro Histórico	2	2025-10-07
379	1	4	Regular	4.51	3	Registro Histórico	1	2025-10-07
380	3	4	Regular	6.43	2	Registro Histórico	1	2025-10-07
381	1	4	Regular	13.02	2	Registro Histórico	1	2025-10-08
382	1	4	Nuevo/Sellado	7.02	4	Registro Histórico	1	2025-10-08
383	1	3	Nuevo/Sellado	14.94	1	Registro Histórico	2	2025-10-09
384	3	1	Nuevo/Sellado	13.14	1	Registro Histórico	2	2025-10-09
385	1	4	Regular	14.84	3	Registro Histórico	2	2025-10-10
386	3	3	Nuevo/Sellado	8.34	1	Registro Histórico	2	2025-10-10
387	1	1	Buen estado (Usado)	11.3	1	Registro Histórico	1	2025-10-11
388	2	2	Nuevo/Sellado	14.87	4	Registro Histórico	1	2025-10-12
389	3	5	Regular	4.98	4	Registro Histórico	2	2025-10-12
390	3	2	Buen estado (Usado)	8.83	2	Registro Histórico	2	2025-10-12
391	2	1	Nuevo/Sellado	10.04	1	Registro Histórico	2	2025-10-13
392	3	2	Nuevo/Sellado	11.05	3	Registro Histórico	1	2025-10-14
393	3	2	Regular	12.4	3	Registro Histórico	2	2025-10-14
394	3	1	Regular	10.56	1	Registro Histórico	1	2025-10-15
395	1	2	Buen estado (Usado)	2.44	3	Registro Histórico	2	2025-10-15
396	3	2	Buen estado (Usado)	3.43	4	Registro Histórico	2	2025-10-16
397	3	3	Regular	3.18	1	Registro Histórico	1	2025-10-16
398	1	1	Buen estado (Usado)	2.25	1	Registro Histórico	2	2025-10-16
399	1	2	Buen estado (Usado)	7.33	3	Registro Histórico	1	2025-10-17
400	2	1	Buen estado (Usado)	3.46	1	Registro Histórico	2	2025-10-17
401	2	3	Nuevo/Sellado	4.15	1	Registro Histórico	2	2025-10-17
402	3	1	Regular	6.81	1	Registro Histórico	1	2025-10-18
403	1	2	Nuevo/Sellado	3.08	4	Registro Histórico	1	2025-10-18
404	3	4	Nuevo/Sellado	10.33	2	Registro Histórico	1	2025-10-19
405	1	3	Buen estado (Usado)	4.05	1	Registro Histórico	1	2025-10-20
406	2	1	Buen estado (Usado)	6.37	1	Registro Histórico	1	2025-10-20
407	3	4	Regular	11.37	3	Registro Histórico	2	2025-10-20
408	1	1	Regular	7.01	1	Registro Histórico	1	2025-10-21
409	3	2	Regular	2.38	4	Registro Histórico	1	2025-10-21
410	3	1	Regular	7.58	1	Registro Histórico	1	2025-10-21
411	2	2	Nuevo/Sellado	10.48	3	Registro Histórico	2	2025-10-22
412	3	3	Regular	8.34	1	Registro Histórico	2	2025-10-22
413	1	5	Buen estado (Usado)	13.99	4	Registro Histórico	1	2025-10-22
414	2	2	Regular	10.17	2	Registro Histórico	1	2025-10-23
415	2	5	Nuevo/Sellado	10.86	2	Registro Histórico	1	2025-10-24
416	3	4	Buen estado (Usado)	7.54	4	Registro Histórico	2	2025-10-25
417	2	2	Buen estado (Usado)	13.74	3	Registro Histórico	2	2025-10-25
418	3	1	Nuevo/Sellado	2.15	1	Registro Histórico	2	2025-10-26
419	1	4	Buen estado (Usado)	11.63	2	Registro Histórico	2	2025-10-27
420	1	5	Regular	10.71	3	Registro Histórico	1	2025-10-28
421	2	1	Regular	7.48	1	Registro Histórico	1	2025-10-28
422	1	1	Nuevo/Sellado	6.22	1	Registro Histórico	2	2025-10-29
423	2	5	Regular	5.45	4	Registro Histórico	1	2025-10-29
424	2	2	Buen estado (Usado)	3.48	2	Registro Histórico	2	2025-10-30
425	1	4	Regular	7	2	Registro Histórico	2	2025-10-30
426	1	2	Buen estado (Usado)	7.48	2	Registro Histórico	1	2025-10-30
427	3	2	Regular	4.91	4	Registro Histórico	2	2025-10-31
428	1	1	Nuevo/Sellado	12.74	1	Registro Histórico	2	2025-10-31
429	2	2	Nuevo/Sellado	13.45	4	Registro Histórico	1	2025-10-31
430	3	3	Regular	47.3	1	Registro Histórico	2	2025-11-01
431	1	3	Nuevo/Sellado	31.71	1	Registro Histórico	2	2025-11-01
432	1	2	Regular	7.71	3	Registro Histórico	2	2025-11-01
433	2	1	Buen estado (Usado)	13.96	1	Registro Histórico	1	2025-11-01
434	1	2	Nuevo/Sellado	14.92	3	Registro Histórico	1	2025-11-01
435	1	3	Buen estado (Usado)	32.05	1	Registro Histórico	2	2025-11-01
436	1	1	Buen estado (Usado)	34.81	1	Registro Histórico	1	2025-11-01
437	3	1	Buen estado (Usado)	21.46	1	Registro Histórico	1	2025-11-01
438	3	5	Regular	17.43	2	Registro Histórico	1	2025-11-01
439	1	1	Nuevo/Sellado	33.21	1	Registro Histórico	1	2025-11-02
440	1	1	Nuevo/Sellado	23.52	1	Registro Histórico	1	2025-11-02
441	1	3	Nuevo/Sellado	43.58	1	Registro Histórico	1	2025-11-02
442	1	2	Regular	10.14	3	Registro Histórico	1	2025-11-02
443	2	3	Buen estado (Usado)	32.74	1	Registro Histórico	1	2025-11-02
444	3	3	Regular	24.64	1	Registro Histórico	1	2025-11-02
445	3	2	Nuevo/Sellado	15.45	4	Registro Histórico	1	2025-11-02
446	2	3	Buen estado (Usado)	44.27	1	Registro Histórico	2	2025-11-02
447	3	1	Nuevo/Sellado	12.61	1	Registro Histórico	1	2025-11-03
448	1	1	Nuevo/Sellado	34.71	1	Registro Histórico	1	2025-11-03
449	1	4	Regular	6.13	3	Registro Histórico	1	2025-11-03
450	3	4	Regular	14.65	3	Registro Histórico	2	2025-11-03
451	1	1	Nuevo/Sellado	37.09	1	Registro Histórico	2	2025-11-03
452	1	1	Buen estado (Usado)	26.6	1	Registro Histórico	1	2025-11-03
453	2	2	Regular	5.74	3	Registro Histórico	2	2025-11-03
454	2	1	Nuevo/Sellado	25.04	1	Registro Histórico	2	2025-11-04
455	1	3	Regular	18.33	1	Registro Histórico	1	2025-11-04
456	2	1	Buen estado (Usado)	18.12	1	Registro Histórico	2	2025-11-04
457	1	3	Buen estado (Usado)	24.82	1	Registro Histórico	2	2025-11-04
458	1	3	Buen estado (Usado)	48.26	1	Registro Histórico	1	2025-11-04
459	1	1	Buen estado (Usado)	25.94	1	Registro Histórico	2	2025-11-05
460	1	1	Regular	14.13	1	Registro Histórico	2	2025-11-05
461	2	2	Buen estado (Usado)	18.55	4	Registro Histórico	2	2025-11-05
462	3	1	Nuevo/Sellado	19.1	1	Registro Histórico	2	2025-11-05
463	1	2	Regular	14.13	3	Registro Histórico	1	2025-11-06
464	3	2	Buen estado (Usado)	9.26	3	Registro Histórico	2	2025-11-06
465	2	3	Buen estado (Usado)	44.67	1	Registro Histórico	1	2025-11-06
466	2	1	Nuevo/Sellado	41.01	1	Registro Histórico	2	2025-11-06
467	2	1	Regular	21.96	1	Registro Histórico	2	2025-11-06
468	3	5	Regular	11.68	3	Registro Histórico	1	2025-11-06
469	2	5	Regular	12.08	3	Registro Histórico	2	2025-11-06
470	1	3	Nuevo/Sellado	10.1	1	Registro Histórico	2	2025-11-06
471	2	3	Nuevo/Sellado	49.71	1	Registro Histórico	1	2025-11-06
472	1	3	Nuevo/Sellado	35.16	1	Registro Histórico	1	2025-11-06
473	2	1	Buen estado (Usado)	14.81	1	Registro Histórico	1	2025-11-06
474	2	3	Regular	37.36	1	Registro Histórico	2	2025-11-07
475	3	1	Regular	38.43	1	Registro Histórico	1	2025-11-07
476	3	1	Regular	11.82	1	Registro Histórico	2	2025-11-07
477	2	1	Buen estado (Usado)	29.86	1	Registro Histórico	1	2025-11-07
478	1	2	Regular	19.27	2	Registro Histórico	1	2025-11-07
479	3	3	Buen estado (Usado)	38.97	1	Registro Histórico	1	2025-11-08
480	3	3	Nuevo/Sellado	10.82	1	Registro Histórico	1	2025-11-08
481	3	1	Nuevo/Sellado	17.02	1	Registro Histórico	2	2025-11-08
482	3	1	Buen estado (Usado)	22.76	1	Registro Histórico	1	2025-11-08
483	2	4	Nuevo/Sellado	10.64	4	Registro Histórico	2	2025-11-08
484	1	3	Nuevo/Sellado	19.19	1	Registro Histórico	2	2025-11-08
485	2	5	Nuevo/Sellado	17.54	3	Registro Histórico	1	2025-11-08
486	1	4	Buen estado (Usado)	17.89	3	Registro Histórico	1	2025-11-09
487	1	1	Nuevo/Sellado	23.98	1	Registro Histórico	1	2025-11-09
488	2	4	Regular	16.98	4	Registro Histórico	1	2025-11-09
489	2	1	Nuevo/Sellado	40.84	1	Registro Histórico	2	2025-11-09
490	3	1	Regular	13.26	1	Registro Histórico	2	2025-11-09
491	3	1	Buen estado (Usado)	11.75	1	Registro Histórico	1	2025-11-09
492	2	1	Regular	11.31	1	Registro Histórico	2	2025-11-09
493	1	1	Regular	38.07	1	Registro Histórico	1	2025-11-09
494	2	3	Regular	37.63	1	Registro Histórico	1	2025-11-10
495	2	2	Buen estado (Usado)	19.21	4	Registro Histórico	1	2025-11-10
496	1	3	Nuevo/Sellado	15.8	1	Registro Histórico	2	2025-11-10
497	2	2	Regular	15.26	3	Registro Histórico	2	2025-11-10
498	3	1	Nuevo/Sellado	24.49	1	Registro Histórico	1	2025-11-10
499	3	1	Buen estado (Usado)	25.15	1	Registro Histórico	1	2025-11-10
500	1	4	Buen estado (Usado)	14.05	2	Registro Histórico	2	2025-11-10
501	2	4	Regular	11.41	2	Registro Histórico	1	2025-11-10
502	3	3	Regular	29.04	1	Registro Histórico	1	2025-11-11
503	1	3	Regular	11.8	1	Registro Histórico	1	2025-11-11
504	3	1	Buen estado (Usado)	12.58	1	Registro Histórico	1	2025-11-11
505	2	5	Nuevo/Sellado	8.7	4	Registro Histórico	2	2025-11-11
506	3	3	Regular	30.52	1	Registro Histórico	2	2025-11-11
507	1	4	Buen estado (Usado)	12.64	4	Registro Histórico	1	2025-11-11
508	2	3	Buen estado (Usado)	46.17	1	Registro Histórico	2	2025-11-11
509	3	3	Buen estado (Usado)	28.31	1	Registro Histórico	1	2025-11-11
510	1	1	Buen estado (Usado)	14.11	1	Registro Histórico	2	2025-11-11
511	1	3	Regular	37.66	1	Registro Histórico	1	2025-11-11
512	3	1	Nuevo/Sellado	29.08	1	Registro Histórico	1	2025-11-12
513	2	3	Buen estado (Usado)	24.05	1	Registro Histórico	2	2025-11-12
514	3	2	Regular	17.56	4	Registro Histórico	1	2025-11-12
515	2	1	Regular	39.09	1	Registro Histórico	2	2025-11-12
516	1	3	Regular	33.05	1	Registro Histórico	2	2025-11-12
517	3	3	Regular	15.14	1	Registro Histórico	1	2025-11-12
518	1	1	Nuevo/Sellado	49.01	1	Registro Histórico	2	2025-11-12
519	1	1	Nuevo/Sellado	43.44	1	Registro Histórico	1	2025-11-12
520	1	4	Buen estado (Usado)	16.23	2	Registro Histórico	2	2025-11-13
521	2	1	Buen estado (Usado)	44.09	1	Registro Histórico	2	2025-11-13
522	3	5	Regular	7.45	4	Registro Histórico	2	2025-11-13
523	2	1	Nuevo/Sellado	34.81	1	Registro Histórico	1	2025-11-13
524	1	5	Buen estado (Usado)	15.05	4	Registro Histórico	2	2025-11-13
525	2	2	Buen estado (Usado)	6.73	2	Registro Histórico	2	2025-11-13
526	2	3	Regular	42.17	1	Registro Histórico	1	2025-11-14
527	3	3	Nuevo/Sellado	36.77	1	Registro Histórico	1	2025-11-14
528	1	1	Buen estado (Usado)	36.8	1	Registro Histórico	1	2025-11-14
529	3	3	Nuevo/Sellado	27.84	1	Registro Histórico	1	2025-11-14
530	2	1	Nuevo/Sellado	26	1	Registro Histórico	2	2025-11-14
531	1	1	Buen estado (Usado)	36.74	1	Registro Histórico	1	2025-11-15
532	3	1	Regular	17.88	1	Registro Histórico	1	2025-11-15
533	2	1	Regular	44.88	1	Registro Histórico	1	2025-11-15
534	1	1	Regular	49.7	1	Registro Histórico	1	2025-11-15
535	2	1	Nuevo/Sellado	48.15	1	Registro Histórico	1	2025-11-15
536	1	2	Regular	7.6	3	Registro Histórico	1	2025-11-15
537	1	5	Buen estado (Usado)	19.28	2	Registro Histórico	1	2025-11-15
538	1	1	Regular	19.07	1	Registro Histórico	2	2025-11-15
539	1	2	Buen estado (Usado)	6.8	4	Registro Histórico	1	2025-11-15
540	1	1	Nuevo/Sellado	12.79	1	Registro Histórico	2	2025-11-16
541	2	3	Regular	27.21	1	Registro Histórico	2	2025-11-16
542	2	3	Regular	32.36	1	Registro Histórico	1	2025-11-16
543	1	2	Buen estado (Usado)	17.49	3	Registro Histórico	2	2025-11-16
544	1	3	Nuevo/Sellado	21.42	1	Registro Histórico	1	2025-11-16
545	1	2	Nuevo/Sellado	5.79	2	Registro Histórico	1	2025-11-16
546	1	1	Regular	34.79	1	Registro Histórico	2	2025-11-16
547	2	1	Nuevo/Sellado	47.34	1	Registro Histórico	1	2025-11-16
548	2	1	Nuevo/Sellado	30.51	1	Registro Histórico	2	2025-11-16
549	3	1	Regular	29.88	1	Registro Histórico	2	2025-11-16
550	2	1	Nuevo/Sellado	46.66	1	Registro Histórico	2	2025-11-17
551	3	2	Buen estado (Usado)	13.81	4	Registro Histórico	1	2025-11-17
552	1	3	Nuevo/Sellado	33.63	1	Registro Histórico	2	2025-11-17
553	3	3	Nuevo/Sellado	38.54	1	Registro Histórico	2	2025-11-17
554	1	3	Nuevo/Sellado	19.68	1	Registro Histórico	2	2025-11-17
555	3	1	Regular	34.34	1	Registro Histórico	1	2025-11-17
556	1	3	Nuevo/Sellado	14.05	1	Registro Histórico	2	2025-11-17
557	2	1	Nuevo/Sellado	33.16	1	Registro Histórico	2	2025-11-17
558	1	1	Regular	18.97	1	Registro Histórico	2	2025-11-17
559	1	3	Nuevo/Sellado	21.75	1	Registro Histórico	2	2025-11-18
560	3	3	Regular	26.9	1	Registro Histórico	1	2025-11-18
561	1	5	Regular	7.42	4	Registro Histórico	1	2025-11-18
562	2	3	Buen estado (Usado)	25.56	1	Registro Histórico	2	2025-11-18
563	2	1	Nuevo/Sellado	16.92	1	Registro Histórico	2	2025-11-18
564	2	2	Regular	12.34	4	Registro Histórico	2	2025-11-18
565	2	1	Regular	38.01	1	Registro Histórico	1	2025-11-18
566	1	1	Buen estado (Usado)	34.02	1	Registro Histórico	2	2025-11-19
567	1	3	Buen estado (Usado)	42.19	1	Registro Histórico	2	2025-11-19
568	3	3	Nuevo/Sellado	11.1	1	Registro Histórico	1	2025-11-19
569	3	3	Nuevo/Sellado	32.2	1	Registro Histórico	1	2025-11-19
570	3	2	Regular	11.57	3	Registro Histórico	2	2025-11-19
571	1	1	Nuevo/Sellado	32.69	1	Registro Histórico	2	2025-11-20
572	3	3	Buen estado (Usado)	23.58	1	Registro Histórico	2	2025-11-20
573	1	1	Buen estado (Usado)	23.8	1	Registro Histórico	1	2025-11-20
574	2	2	Regular	18.72	3	Registro Histórico	2	2025-11-20
575	3	2	Buen estado (Usado)	14.39	2	Registro Histórico	1	2025-11-21
576	3	1	Regular	21.48	1	Registro Histórico	1	2025-11-21
577	2	3	Regular	39.58	1	Registro Histórico	2	2025-11-21
578	2	3	Regular	33.81	1	Registro Histórico	1	2025-11-21
579	1	1	Buen estado (Usado)	26.98	1	Registro Histórico	2	2025-11-21
580	2	3	Nuevo/Sellado	43.86	1	Registro Histórico	2	2025-11-21
581	3	2	Nuevo/Sellado	13.83	3	Registro Histórico	1	2025-11-21
582	2	3	Buen estado (Usado)	47.76	1	Registro Histórico	2	2025-11-21
583	1	2	Regular	11.31	4	Registro Histórico	2	2025-11-21
584	2	1	Buen estado (Usado)	22.52	1	Registro Histórico	2	2025-11-21
585	2	3	Regular	19.09	1	Registro Histórico	1	2025-11-22
586	1	1	Buen estado (Usado)	31.57	1	Registro Histórico	1	2025-11-22
587	3	1	Buen estado (Usado)	32.53	1	Registro Histórico	2	2025-11-22
588	1	3	Buen estado (Usado)	40.51	1	Registro Histórico	1	2025-11-22
589	1	3	Nuevo/Sellado	30.51	1	Registro Histórico	2	2025-11-22
590	1	2	Nuevo/Sellado	8.95	3	Registro Histórico	1	2025-11-22
591	1	1	Nuevo/Sellado	21.35	1	Registro Histórico	2	2025-11-22
592	2	2	Regular	17.64	3	Registro Histórico	2	2025-11-22
593	2	1	Nuevo/Sellado	46.28	1	Registro Histórico	2	2025-11-22
594	2	2	Regular	18.64	4	Registro Histórico	1	2025-11-22
595	1	2	Buen estado (Usado)	11.14	3	Registro Histórico	2	2025-11-22
596	2	3	Nuevo/Sellado	24.36	1	Registro Histórico	2	2025-11-23
597	1	1	Nuevo/Sellado	15.12	1	Registro Histórico	1	2025-11-23
598	1	1	Regular	20.72	1	Registro Histórico	2	2025-11-23
599	3	1	Buen estado (Usado)	14.09	1	Registro Histórico	1	2025-11-23
600	3	1	Buen estado (Usado)	44.12	1	Registro Histórico	2	2025-11-23
601	2	3	Nuevo/Sellado	37.71	1	Registro Histórico	2	2025-11-23
602	2	1	Buen estado (Usado)	11.08	1	Registro Histórico	2	2025-11-23
603	3	3	Regular	19.65	1	Registro Histórico	1	2025-11-23
604	1	1	Nuevo/Sellado	17.97	1	Registro Histórico	1	2025-11-24
605	2	4	Regular	7.7	3	Registro Histórico	2	2025-11-24
606	2	1	Nuevo/Sellado	14.9	1	Registro Histórico	1	2025-11-24
607	1	4	Regular	7.59	2	Registro Histórico	1	2025-11-24
608	3	5	Regular	13.41	2	Registro Histórico	1	2025-11-24
609	1	2	Buen estado (Usado)	14.91	2	Registro Histórico	2	2025-11-24
610	2	3	Regular	24.04	1	Registro Histórico	2	2025-11-24
611	3	2	Buen estado (Usado)	15.82	4	Registro Histórico	1	2025-11-24
612	2	2	Buen estado (Usado)	17.94	4	Registro Histórico	1	2025-11-24
613	1	1	Nuevo/Sellado	31.58	1	Registro Histórico	1	2025-11-24
614	2	2	Regular	9.63	2	Registro Histórico	1	2025-11-24
615	2	5	Buen estado (Usado)	8.32	3	Registro Histórico	2	2025-11-25
616	3	2	Nuevo/Sellado	7.88	3	Registro Histórico	2	2025-11-25
617	2	2	Regular	5	2	Registro Histórico	1	2025-11-25
618	3	3	Nuevo/Sellado	40.82	1	Registro Histórico	2	2025-11-25
619	2	2	Regular	14.63	4	Registro Histórico	1	2025-11-25
620	2	5	Regular	11.75	4	Registro Histórico	1	2025-11-25
621	1	4	Regular	5.76	4	Registro Histórico	1	2025-11-25
622	1	3	Buen estado (Usado)	25.45	1	Registro Histórico	2	2025-11-25
623	3	3	Regular	15.5	1	Registro Histórico	2	2025-11-25
624	2	2	Regular	18.19	2	Registro Histórico	2	2025-11-25
625	2	3	Buen estado (Usado)	26.38	1	Registro Histórico	2	2025-11-26
626	1	2	Nuevo/Sellado	15.78	3	Registro Histórico	2	2025-11-26
627	2	3	Nuevo/Sellado	38.43	1	Registro Histórico	2	2025-11-26
628	2	1	Nuevo/Sellado	38.98	1	Registro Histórico	1	2025-11-26
629	1	1	Nuevo/Sellado	18.04	1	Registro Histórico	1	2025-11-26
630	1	2	Regular	13.58	4	Registro Histórico	2	2025-11-26
631	3	3	Nuevo/Sellado	30.21	1	Registro Histórico	2	2025-11-26
632	2	3	Nuevo/Sellado	20.37	1	Registro Histórico	2	2025-11-26
633	3	3	Regular	14.41	1	Registro Histórico	2	2025-11-26
634	2	3	Nuevo/Sellado	46.24	1	Registro Histórico	2	2025-11-26
635	2	5	Regular	18.24	2	Registro Histórico	1	2025-11-27
636	3	1	Nuevo/Sellado	39.25	1	Registro Histórico	2	2025-11-27
637	1	3	Nuevo/Sellado	27.19	1	Registro Histórico	1	2025-11-27
638	1	5	Regular	17.03	2	Registro Histórico	2	2025-11-27
639	2	1	Buen estado (Usado)	49.62	1	Registro Histórico	2	2025-11-27
640	1	5	Buen estado (Usado)	9.45	4	Registro Histórico	2	2025-11-27
641	3	1	Regular	39.84	1	Registro Histórico	2	2025-11-27
642	3	3	Regular	22.13	1	Registro Histórico	2	2025-11-27
643	1	1	Regular	18.66	1	Registro Histórico	1	2025-11-28
644	1	3	Nuevo/Sellado	15.11	1	Registro Histórico	2	2025-11-28
645	1	2	Nuevo/Sellado	11.86	4	Registro Histórico	2	2025-11-28
646	2	1	Nuevo/Sellado	21.14	1	Registro Histórico	1	2025-11-28
647	1	3	Regular	25.44	1	Registro Histórico	1	2025-11-28
648	2	1	Buen estado (Usado)	29.46	1	Registro Histórico	1	2025-11-28
649	2	3	Buen estado (Usado)	25.17	1	Registro Histórico	1	2025-11-28
650	1	1	Regular	28.38	1	Registro Histórico	2	2025-11-29
651	1	3	Regular	39.25	1	Registro Histórico	1	2025-11-29
652	3	1	Regular	48.91	1	Registro Histórico	1	2025-11-29
653	3	1	Buen estado (Usado)	16.02	1	Registro Histórico	2	2025-11-29
654	2	3	Regular	29.01	1	Registro Histórico	2	2025-11-29
655	3	3	Buen estado (Usado)	16.98	1	Registro Histórico	2	2025-11-29
656	3	3	Nuevo/Sellado	37.08	1	Registro Histórico	1	2025-11-29
657	3	2	Buen estado (Usado)	18.59	3	Registro Histórico	1	2025-11-29
658	3	3	Buen estado (Usado)	37.36	1	Registro Histórico	1	2025-11-29
659	2	3	Regular	41.96	1	Registro Histórico	1	2025-11-29
660	3	1	Regular	31.63	1	Registro Histórico	1	2025-11-29
661	3	2	Nuevo/Sellado	6.14	4	Registro Histórico	2	2025-11-30
662	3	1	Nuevo/Sellado	14.58	1	Registro Histórico	2	2025-11-30
663	1	3	Nuevo/Sellado	26.06	1	Registro Histórico	2	2025-11-30
664	1	3	Buen estado (Usado)	45.18	1	Registro Histórico	2	2025-11-30
665	3	1	Regular	40.48	1	Registro Histórico	1	2025-11-30
666	2	2	Buen estado (Usado)	19.05	4	Registro Histórico	1	2025-11-30
667	2	1	Buen estado (Usado)	47.85	1	Registro Histórico	2	2025-11-30
668	3	1	Nuevo/Sellado	43.63	1	Registro Histórico	2	2025-12-01
669	3	3	Nuevo/Sellado	23.17	1	Registro Histórico	1	2025-12-01
670	2	1	Nuevo/Sellado	44.69	1	Registro Histórico	2	2025-12-01
671	3	2	Nuevo/Sellado	14.79	4	Registro Histórico	2	2025-12-01
672	3	1	Buen estado (Usado)	13.9	1	Registro Histórico	1	2025-12-01
673	1	5	Regular	16.01	3	Registro Histórico	2	2025-12-01
674	2	3	Nuevo/Sellado	19.94	1	Registro Histórico	2	2025-12-01
675	3	1	Buen estado (Usado)	29.57	1	Registro Histórico	1	2025-12-02
676	1	2	Buen estado (Usado)	7.03	3	Registro Histórico	1	2025-12-02
677	2	2	Nuevo/Sellado	6.22	4	Registro Histórico	2	2025-12-02
678	1	2	Regular	17.69	2	Registro Histórico	2	2025-12-02
679	1	1	Regular	24.95	1	Registro Histórico	1	2025-12-02
680	1	3	Regular	30.71	1	Registro Histórico	2	2025-12-02
681	1	3	Regular	29.92	1	Registro Histórico	2	2025-12-02
682	1	3	Nuevo/Sellado	33.21	1	Registro Histórico	1	2025-12-02
683	3	5	Buen estado (Usado)	6.49	3	Registro Histórico	2	2025-12-02
684	2	3	Buen estado (Usado)	36.78	1	Registro Histórico	1	2025-12-02
685	2	2	Nuevo/Sellado	15.62	4	Registro Histórico	2	2025-12-03
686	3	3	Nuevo/Sellado	33.73	1	Registro Histórico	2	2025-12-03
687	3	3	Buen estado (Usado)	29.35	1	Registro Histórico	1	2025-12-03
688	1	1	Nuevo/Sellado	41.86	1	Registro Histórico	2	2025-12-03
689	3	3	Nuevo/Sellado	14.87	1	Registro Histórico	2	2025-12-03
690	3	5	Buen estado (Usado)	16.94	2	Registro Histórico	2	2025-12-03
691	3	2	Regular	17.13	3	Registro Histórico	2	2025-12-03
692	1	2	Nuevo/Sellado	6.3	3	Registro Histórico	2	2025-12-03
693	2	3	Buen estado (Usado)	22.75	1	Registro Histórico	1	2025-12-04
694	1	2	Buen estado (Usado)	8.43	2	Registro Histórico	1	2025-12-04
695	1	1	Buen estado (Usado)	48.19	1	Registro Histórico	2	2025-12-04
696	1	1	Nuevo/Sellado	37	1	Registro Histórico	1	2025-12-04
697	2	1	Regular	43.82	1	Registro Histórico	1	2025-12-04
698	3	2	Regular	15.33	4	Registro Histórico	1	2025-12-04
699	2	2	Buen estado (Usado)	5.25	2	Registro Histórico	1	2025-12-05
700	1	4	Regular	16.13	4	Registro Histórico	2	2025-12-05
701	2	2	Nuevo/Sellado	11.44	4	Registro Histórico	1	2025-12-05
702	2	1	Buen estado (Usado)	30.13	1	Registro Histórico	2	2025-12-05
703	1	5	Nuevo/Sellado	10.57	3	Registro Histórico	1	2025-12-05
704	1	3	Buen estado (Usado)	32.24	1	Registro Histórico	2	2025-12-05
705	2	3	Buen estado (Usado)	16.48	1	Registro Histórico	1	2025-12-05
706	2	2	Buen estado (Usado)	8.71	2	Registro Histórico	2	2025-12-05
707	1	3	Buen estado (Usado)	44.86	1	Registro Histórico	2	2025-12-06
708	1	2	Regular	5.45	4	Registro Histórico	2	2025-12-06
709	1	2	Nuevo/Sellado	10.53	3	Registro Histórico	1	2025-12-06
710	2	1	Nuevo/Sellado	42.73	1	Registro Histórico	1	2025-12-06
711	2	3	Nuevo/Sellado	48.02	1	Registro Histórico	1	2025-12-06
712	1	3	Buen estado (Usado)	45.91	1	Registro Histórico	1	2025-12-06
713	3	1	Buen estado (Usado)	45.95	1	Registro Histórico	1	2025-12-06
714	3	1	Regular	31.98	1	Registro Histórico	1	2025-12-06
715	2	2	Nuevo/Sellado	15.59	3	Registro Histórico	1	2025-12-07
716	3	1	Nuevo/Sellado	45.33	1	Registro Histórico	1	2025-12-07
717	1	1	Buen estado (Usado)	29.69	1	Registro Histórico	1	2025-12-07
718	3	1	Buen estado (Usado)	18.12	1	Registro Histórico	1	2025-12-07
719	1	1	Nuevo/Sellado	30.44	1	Registro Histórico	1	2025-12-07
720	3	2	Regular	9.6	2	Registro Histórico	2	2025-12-07
721	3	1	Regular	25.21	1	Registro Histórico	2	2025-12-07
722	3	1	Buen estado (Usado)	14.27	1	Registro Histórico	2	2025-12-07
723	1	2	Buen estado (Usado)	16.06	3	Registro Histórico	1	2025-12-08
724	1	1	Regular	46.77	1	Registro Histórico	2	2025-12-08
725	1	1	Regular	37.67	1	Registro Histórico	1	2025-12-08
726	1	1	Buen estado (Usado)	16.21	1	Registro Histórico	1	2025-12-08
727	2	1	Regular	19.69	1	Registro Histórico	1	2025-12-08
728	1	3	Regular	36.48	1	Registro Histórico	2	2025-12-09
729	1	1	Regular	41.94	1	Registro Histórico	2	2025-12-09
730	2	3	Nuevo/Sellado	43.62	1	Registro Histórico	2	2025-12-09
731	2	2	Regular	8.84	2	Registro Histórico	2	2025-12-09
732	3	1	Regular	37.99	1	Registro Histórico	1	2025-12-10
733	2	3	Nuevo/Sellado	18.63	1	Registro Histórico	2	2025-12-10
734	1	1	Nuevo/Sellado	36.18	1	Registro Histórico	1	2025-12-10
735	2	1	Regular	48.98	1	Registro Histórico	2	2025-12-10
736	3	1	Regular	32.15	1	Registro Histórico	2	2025-12-10
737	1	1	Regular	37.91	1	Registro Histórico	1	2025-12-10
738	2	1	Buen estado (Usado)	44.61	1	Registro Histórico	2	2025-12-11
739	2	1	Nuevo/Sellado	23.88	1	Registro Histórico	2	2025-12-11
740	3	4	Nuevo/Sellado	12.06	4	Registro Histórico	1	2025-12-11
741	2	1	Nuevo/Sellado	17.8	1	Registro Histórico	1	2025-12-11
742	2	2	Buen estado (Usado)	13.81	3	Registro Histórico	2	2025-12-11
743	1	2	Buen estado (Usado)	14.9	2	Registro Histórico	2	2025-12-11
744	1	2	Regular	19.81	3	Registro Histórico	2	2025-12-11
745	2	5	Nuevo/Sellado	16.46	4	Registro Histórico	1	2025-12-11
746	3	5	Regular	19.98	4	Registro Histórico	2	2025-12-11
747	2	3	Nuevo/Sellado	27.11	1	Registro Histórico	2	2025-12-12
748	1	1	Regular	46.78	1	Registro Histórico	1	2025-12-12
749	3	2	Nuevo/Sellado	10.62	3	Registro Histórico	1	2025-12-12
750	1	3	Nuevo/Sellado	29.84	1	Registro Histórico	1	2025-12-12
751	1	1	Nuevo/Sellado	35.08	1	Registro Histórico	1	2025-12-12
752	3	2	Buen estado (Usado)	17.59	4	Registro Histórico	2	2025-12-12
753	2	2	Buen estado (Usado)	17.1	4	Registro Histórico	1	2025-12-12
754	3	1	Nuevo/Sellado	32.96	1	Registro Histórico	1	2025-12-12
755	3	1	Buen estado (Usado)	29.12	1	Registro Histórico	1	2025-12-12
756	3	3	Buen estado (Usado)	26.82	1	Registro Histórico	2	2025-12-12
757	2	3	Nuevo/Sellado	18.51	1	Registro Histórico	1	2025-12-12
758	2	1	Nuevo/Sellado	43.92	1	Registro Histórico	2	2025-12-13
759	3	1	Buen estado (Usado)	40.01	1	Registro Histórico	1	2025-12-13
760	3	3	Nuevo/Sellado	42.49	1	Registro Histórico	2	2025-12-13
761	2	3	Buen estado (Usado)	12.29	1	Registro Histórico	1	2025-12-13
762	3	3	Nuevo/Sellado	25.89	1	Registro Histórico	1	2025-12-13
763	2	2	Nuevo/Sellado	13.97	2	Registro Histórico	1	2025-12-13
764	1	2	Regular	7.25	2	Registro Histórico	1	2025-12-13
765	2	2	Nuevo/Sellado	7.29	4	Registro Histórico	1	2025-12-13
766	3	2	Regular	8.52	3	Registro Histórico	1	2025-12-14
767	2	3	Regular	46.37	1	Registro Histórico	1	2025-12-14
768	3	3	Nuevo/Sellado	18.54	1	Registro Histórico	1	2025-12-14
769	1	1	Regular	34.7	1	Registro Histórico	2	2025-12-14
770	3	2	Nuevo/Sellado	16.13	4	Registro Histórico	1	2025-12-14
771	2	1	Buen estado (Usado)	25.35	1	Registro Histórico	1	2025-12-14
772	3	3	Buen estado (Usado)	33.91	1	Registro Histórico	2	2025-12-14
773	2	2	Nuevo/Sellado	8.41	3	Registro Histórico	2	2025-12-14
774	2	1	Nuevo/Sellado	32.67	1	Registro Histórico	1	2025-12-14
775	1	1	Buen estado (Usado)	43.82	1	Registro Histórico	2	2025-12-15
776	1	3	Regular	17.44	1	Registro Histórico	1	2025-12-15
777	2	2	Buen estado (Usado)	16.91	3	Registro Histórico	2	2025-12-15
778	1	3	Nuevo/Sellado	47.09	1	Registro Histórico	1	2025-12-15
779	1	3	Buen estado (Usado)	19.9	1	Registro Histórico	2	2025-12-15
780	3	2	Buen estado (Usado)	6.66	3	Registro Histórico	2	2025-12-15
781	3	1	Nuevo/Sellado	48.75	1	Registro Histórico	1	2025-12-15
782	3	2	Buen estado (Usado)	14.79	3	Registro Histórico	2	2025-12-15
783	2	2	Buen estado (Usado)	18.33	2	Registro Histórico	1	2025-12-15
784	3	2	Regular	11.19	4	Registro Histórico	1	2025-12-15
785	1	2	Regular	17.76	3	Registro Histórico	2	2025-12-16
786	3	3	Buen estado (Usado)	40.52	1	Registro Histórico	2	2025-12-16
787	2	1	Buen estado (Usado)	25.93	1	Registro Histórico	1	2025-12-16
788	2	2	Buen estado (Usado)	19.89	4	Registro Histórico	1	2025-12-16
789	2	3	Regular	41.65	1	Registro Histórico	2	2025-12-16
790	3	3	Buen estado (Usado)	10.31	1	Registro Histórico	1	2025-12-16
791	3	2	Regular	15.12	4	Registro Histórico	2	2025-12-16
792	2	4	Nuevo/Sellado	9.83	3	Registro Histórico	2	2025-12-16
793	2	3	Nuevo/Sellado	39.22	1	Registro Histórico	2	2025-12-16
794	3	1	Buen estado (Usado)	27.58	1	Registro Histórico	1	2025-12-16
795	2	3	Nuevo/Sellado	35	1	Registro Histórico	1	2025-12-17
796	3	2	Buen estado (Usado)	11.56	4	Registro Histórico	2	2025-12-17
797	1	3	Nuevo/Sellado	11.32	1	Registro Histórico	1	2025-12-17
798	3	1	Buen estado (Usado)	32.15	1	Registro Histórico	2	2025-12-17
799	1	3	Nuevo/Sellado	49.89	1	Registro Histórico	1	2025-12-17
800	1	1	Buen estado (Usado)	40.88	1	Registro Histórico	1	2025-12-17
801	2	2	Buen estado (Usado)	6.41	3	Registro Histórico	2	2025-12-17
802	2	4	Nuevo/Sellado	7.39	2	Registro Histórico	2	2025-12-18
803	1	1	Buen estado (Usado)	44.57	1	Registro Histórico	2	2025-12-18
804	1	1	Regular	43.73	1	Registro Histórico	2	2025-12-18
805	2	1	Nuevo/Sellado	25.89	1	Registro Histórico	1	2025-12-18
806	2	2	Buen estado (Usado)	7.23	4	Registro Histórico	1	2025-12-18
807	3	1	Regular	12.44	1	Registro Histórico	2	2025-12-18
808	2	2	Regular	6.92	4	Registro Histórico	1	2025-12-19
809	2	3	Buen estado (Usado)	31.3	1	Registro Histórico	2	2025-12-19
810	2	1	Buen estado (Usado)	30.4	1	Registro Histórico	2	2025-12-19
811	2	1	Buen estado (Usado)	25.8	1	Registro Histórico	2	2025-12-19
812	1	3	Nuevo/Sellado	48.69	1	Registro Histórico	1	2025-12-19
813	3	3	Nuevo/Sellado	29.29	1	Registro Histórico	1	2025-12-19
814	3	3	Regular	28.55	1	Registro Histórico	1	2025-12-19
815	3	2	Regular	9.26	2	Registro Histórico	1	2025-12-19
816	1	3	Regular	30	1	Registro Histórico	2	2025-12-20
817	3	1	Nuevo/Sellado	49.87	1	Registro Histórico	1	2025-12-20
818	3	3	Buen estado (Usado)	29.72	1	Registro Histórico	2	2025-12-20
819	3	1	Nuevo/Sellado	16.55	1	Registro Histórico	2	2025-12-20
820	1	2	Buen estado (Usado)	5.31	4	Registro Histórico	2	2025-12-20
821	1	1	Regular	35.32	1	Registro Histórico	2	2025-12-21
822	1	2	Buen estado (Usado)	11.4	4	Registro Histórico	1	2025-12-21
823	2	1	Regular	26.12	1	Registro Histórico	1	2025-12-21
824	1	2	Regular	18.56	3	Registro Histórico	2	2025-12-21
825	3	2	Buen estado (Usado)	13.59	3	Registro Histórico	2	2025-12-21
826	3	3	Buen estado (Usado)	44.51	1	Registro Histórico	2	2025-12-22
827	1	2	Regular	17.94	2	Registro Histórico	2	2025-12-22
828	2	3	Buen estado (Usado)	48.44	1	Registro Histórico	1	2025-12-22
829	1	2	Buen estado (Usado)	13.85	2	Registro Histórico	1	2025-12-22
830	2	1	Nuevo/Sellado	35.62	1	Registro Histórico	1	2025-12-22
831	2	5	Buen estado (Usado)	12.33	4	Registro Histórico	1	2025-12-22
832	2	3	Regular	13.06	1	Registro Histórico	2	2025-12-22
833	3	5	Regular	15.6	3	Registro Histórico	1	2025-12-22
834	1	3	Buen estado (Usado)	19.52	1	Registro Histórico	2	2025-12-22
835	3	3	Nuevo/Sellado	11.02	1	Registro Histórico	1	2025-12-22
836	3	3	Nuevo/Sellado	21.51	1	Registro Histórico	2	2025-12-23
837	2	2	Buen estado (Usado)	19.8	2	Registro Histórico	1	2025-12-23
838	1	1	Regular	39.71	1	Registro Histórico	2	2025-12-23
839	3	2	Regular	9.79	3	Registro Histórico	2	2025-12-23
840	2	3	Buen estado (Usado)	48.82	1	Registro Histórico	1	2025-12-23
841	1	3	Regular	36.25	1	Registro Histórico	1	2025-12-23
842	3	3	Nuevo/Sellado	25.04	1	Registro Histórico	1	2025-12-24
843	1	1	Regular	26.74	1	Registro Histórico	2	2025-12-24
844	2	1	Nuevo/Sellado	42.31	1	Registro Histórico	1	2025-12-24
845	2	2	Buen estado (Usado)	9.8	2	Registro Histórico	2	2025-12-24
846	1	5	Nuevo/Sellado	5.95	3	Registro Histórico	1	2025-12-24
847	2	1	Nuevo/Sellado	42.07	1	Registro Histórico	2	2025-12-24
848	1	4	Nuevo/Sellado	19.34	2	Registro Histórico	2	2025-12-25
849	1	3	Nuevo/Sellado	38.46	1	Registro Histórico	2	2025-12-25
850	3	3	Regular	23.69	1	Registro Histórico	2	2025-12-25
851	1	2	Buen estado (Usado)	6.91	3	Registro Histórico	1	2025-12-25
852	2	3	Buen estado (Usado)	23.21	1	Registro Histórico	1	2025-12-25
853	1	3	Buen estado (Usado)	10.31	1	Registro Histórico	1	2025-12-25
854	3	2	Nuevo/Sellado	8.45	2	Registro Histórico	1	2025-12-26
855	2	3	Nuevo/Sellado	49.51	1	Registro Histórico	1	2025-12-26
856	3	1	Nuevo/Sellado	30.53	1	Registro Histórico	2	2025-12-26
857	2	3	Buen estado (Usado)	37.25	1	Registro Histórico	1	2025-12-26
858	1	2	Nuevo/Sellado	18.08	4	Registro Histórico	2	2025-12-26
859	3	2	Regular	11.3	3	Registro Histórico	1	2025-12-26
860	3	2	Nuevo/Sellado	11.08	2	Registro Histórico	2	2025-12-26
861	2	1	Nuevo/Sellado	30.51	1	Registro Histórico	1	2025-12-26
862	1	3	Nuevo/Sellado	14.36	1	Registro Histórico	2	2025-12-26
863	3	3	Buen estado (Usado)	28.21	1	Registro Histórico	2	2025-12-27
864	2	2	Regular	9.68	2	Registro Histórico	2	2025-12-27
865	1	5	Buen estado (Usado)	13.01	4	Registro Histórico	1	2025-12-27
866	1	2	Buen estado (Usado)	11.76	4	Registro Histórico	2	2025-12-27
867	2	4	Nuevo/Sellado	7.76	3	Registro Histórico	1	2025-12-27
868	2	2	Buen estado (Usado)	15.4	2	Registro Histórico	2	2025-12-27
869	2	1	Nuevo/Sellado	45.86	1	Registro Histórico	2	2025-12-28
870	3	2	Buen estado (Usado)	11.7	2	Registro Histórico	1	2025-12-28
871	1	1	Regular	42.91	1	Registro Histórico	1	2025-12-28
872	3	1	Regular	20.44	1	Registro Histórico	1	2025-12-28
873	1	2	Buen estado (Usado)	7.11	3	Registro Histórico	2	2025-12-28
874	3	3	Regular	32.75	1	Registro Histórico	1	2025-12-28
875	2	4	Regular	14.8	2	Registro Histórico	1	2025-12-28
876	3	4	Nuevo/Sellado	15.05	3	Registro Histórico	2	2025-12-29
877	1	1	Buen estado (Usado)	13.06	1	Registro Histórico	1	2025-12-29
878	2	1	Nuevo/Sellado	34.38	1	Registro Histórico	2	2025-12-29
879	2	3	Regular	33.13	1	Registro Histórico	1	2025-12-29
880	3	3	Buen estado (Usado)	47.38	1	Registro Histórico	1	2025-12-29
881	2	1	Nuevo/Sellado	35.82	1	Registro Histórico	2	2025-12-29
882	3	3	Regular	16.37	1	Registro Histórico	1	2025-12-30
883	2	3	Regular	43.79	1	Registro Histórico	1	2025-12-30
884	2	3	Buen estado (Usado)	48.95	1	Registro Histórico	2	2025-12-30
885	2	2	Regular	17.31	4	Registro Histórico	1	2025-12-30
886	1	2	Nuevo/Sellado	13.5	4	Registro Histórico	2	2025-12-30
887	1	3	Buen estado (Usado)	32.6	1	Registro Histórico	1	2025-12-30
888	1	1	Regular	41.21	1	Registro Histórico	2	2025-12-30
889	2	2	Buen estado (Usado)	11.27	4	Registro Histórico	2	2025-12-30
890	3	3	Buen estado (Usado)	34.73	1	Registro Histórico	1	2025-12-31
891	2	2	Nuevo/Sellado	15.09	3	Registro Histórico	1	2025-12-31
892	3	1	Buen estado (Usado)	25.19	1	Registro Histórico	1	2025-12-31
893	1	3	Nuevo/Sellado	41.43	1	Registro Histórico	1	2025-12-31
894	1	3	Buen estado (Usado)	12.88	1	Registro Histórico	2	2025-12-31
895	3	3	Nuevo/Sellado	13.73	1	Registro Histórico	2	2025-12-31
896	3	3	Regular	12.41	1	Registro Histórico	2	2025-12-31
897	2	1	Buen estado (Usado)	21.76	1	Registro Histórico	1	2026-01-01
898	3	1	Regular	34.19	1	Registro Histórico	2	2026-01-01
899	2	3	Regular	14.25	1	Registro Histórico	1	2026-01-01
900	3	2	Nuevo/Sellado	11.18	3	Registro Histórico	1	2026-01-01
901	3	3	Buen estado (Usado)	23.44	1	Registro Histórico	2	2026-01-01
902	3	5	Buen estado (Usado)	14.47	2	Registro Histórico	2	2026-01-01
903	1	1	Buen estado (Usado)	14.81	1	Registro Histórico	1	2026-01-02
904	2	4	Nuevo/Sellado	14.48	2	Registro Histórico	2	2026-01-02
905	3	3	Nuevo/Sellado	47.54	1	Registro Histórico	1	2026-01-02
906	1	5	Buen estado (Usado)	6.71	2	Registro Histórico	1	2026-01-02
907	2	1	Regular	24.92	1	Registro Histórico	2	2026-01-02
908	3	2	Buen estado (Usado)	9.46	2	Registro Histórico	2	2026-01-02
909	2	1	Regular	11.72	1	Registro Histórico	1	2026-01-02
910	1	2	Nuevo/Sellado	13.06	2	Registro Histórico	1	2026-01-02
911	3	1	Regular	44.16	1	Registro Histórico	1	2026-01-02
912	3	3	Nuevo/Sellado	15.77	1	Registro Histórico	1	2026-01-03
913	3	2	Buen estado (Usado)	5.65	3	Registro Histórico	1	2026-01-03
914	2	1	Regular	32.89	1	Registro Histórico	2	2026-01-03
915	3	2	Buen estado (Usado)	11.78	3	Registro Histórico	2	2026-01-03
916	3	1	Buen estado (Usado)	42.13	1	Registro Histórico	1	2026-01-03
917	1	2	Regular	8.22	4	Registro Histórico	2	2026-01-03
918	3	3	Nuevo/Sellado	12.32	1	Registro Histórico	1	2026-01-03
919	2	1	Buen estado (Usado)	10.15	1	Registro Histórico	2	2026-01-03
920	2	3	Buen estado (Usado)	48.95	1	Registro Histórico	2	2026-01-03
921	3	1	Regular	44.29	1	Registro Histórico	2	2026-01-04
922	1	3	Nuevo/Sellado	42.13	1	Registro Histórico	2	2026-01-04
923	2	1	Buen estado (Usado)	16.88	1	Registro Histórico	2	2026-01-04
924	3	3	Regular	13.84	1	Registro Histórico	2	2026-01-04
925	2	3	Buen estado (Usado)	46.17	1	Registro Histórico	1	2026-01-05
926	2	1	Regular	29.76	1	Registro Histórico	1	2026-01-05
927	3	3	Regular	29.3	1	Registro Histórico	2	2026-01-05
928	2	1	Buen estado (Usado)	40.35	1	Registro Histórico	2	2026-01-05
929	1	3	Nuevo/Sellado	38.6	1	Registro Histórico	2	2026-01-05
930	2	3	Regular	28.16	1	Registro Histórico	1	2026-01-05
931	2	3	Buen estado (Usado)	39.14	1	Registro Histórico	1	2026-01-05
932	1	1	Nuevo/Sellado	10.9	1	Registro Histórico	2	2026-01-05
933	1	3	Buen estado (Usado)	37.62	1	Registro Histórico	1	2026-01-06
934	1	4	Regular	5.77	4	Registro Histórico	1	2026-01-06
935	1	1	Regular	34.89	1	Registro Histórico	2	2026-01-06
936	3	1	Buen estado (Usado)	13.36	1	Registro Histórico	2	2026-01-06
937	3	1	Buen estado (Usado)	43.48	1	Registro Histórico	1	2026-01-06
938	2	1	Buen estado (Usado)	32.76	1	Registro Histórico	2	2026-01-06
939	3	3	Buen estado (Usado)	34.57	1	Registro Histórico	2	2026-01-07
940	3	2	Nuevo/Sellado	5.27	3	Registro Histórico	2	2026-01-07
941	3	3	Nuevo/Sellado	36.53	1	Registro Histórico	1	2026-01-07
942	3	1	Regular	16.82	1	Registro Histórico	1	2026-01-07
943	3	1	Regular	31.45	1	Registro Histórico	1	2026-01-07
944	2	3	Regular	24.8	1	Registro Histórico	2	2026-01-07
945	2	1	Nuevo/Sellado	12.14	1	Registro Histórico	1	2026-01-07
946	3	3	Regular	42.89	1	Registro Histórico	2	2026-01-07
947	1	2	Regular	5.52	2	Registro Histórico	1	2026-01-08
948	3	1	Nuevo/Sellado	39.57	1	Registro Histórico	1	2026-01-08
949	1	1	Nuevo/Sellado	44.97	1	Registro Histórico	1	2026-01-08
950	2	2	Regular	18.36	2	Registro Histórico	1	2026-01-08
951	1	3	Regular	26.23	1	Registro Histórico	2	2026-01-08
952	2	1	Buen estado (Usado)	21.24	1	Registro Histórico	2	2026-01-09
953	3	3	Buen estado (Usado)	13.1	1	Registro Histórico	2	2026-01-09
954	2	3	Buen estado (Usado)	45.11	1	Registro Histórico	2	2026-01-09
955	1	1	Buen estado (Usado)	40.22	1	Registro Histórico	1	2026-01-09
956	2	3	Nuevo/Sellado	26.24	1	Registro Histórico	1	2026-01-09
957	3	2	Regular	14.14	2	Registro Histórico	2	2026-01-09
958	3	3	Buen estado (Usado)	33.66	1	Registro Histórico	1	2026-01-10
959	2	1	Nuevo/Sellado	44.38	1	Registro Histórico	1	2026-01-10
960	2	5	Regular	11.17	2	Registro Histórico	2	2026-01-10
961	3	3	Nuevo/Sellado	33.52	1	Registro Histórico	1	2026-01-10
962	2	5	Regular	19.73	3	Registro Histórico	1	2026-01-10
963	1	4	Nuevo/Sellado	10.32	3	Registro Histórico	2	2026-01-10
964	3	2	Nuevo/Sellado	18.27	2	Registro Histórico	1	2026-01-10
965	3	1	Nuevo/Sellado	25.49	1	Registro Histórico	1	2026-01-10
966	1	2	Regular	16.91	3	Registro Histórico	1	2026-01-10
967	1	3	Regular	11.27	1	Registro Histórico	2	2026-01-11
968	1	1	Regular	46.24	1	Registro Histórico	1	2026-01-11
969	1	3	Buen estado (Usado)	18.94	1	Registro Histórico	1	2026-01-11
970	2	1	Buen estado (Usado)	38.47	1	Registro Histórico	2	2026-01-11
971	1	3	Nuevo/Sellado	16.34	1	Registro Histórico	2	2026-01-11
972	2	2	Nuevo/Sellado	14.2	3	Registro Histórico	2	2026-01-11
973	1	3	Buen estado (Usado)	29.08	1	Registro Histórico	2	2026-01-11
974	3	2	Regular	6.16	4	Registro Histórico	2	2026-01-11
975	2	2	Regular	14.09	3	Registro Histórico	2	2026-01-11
976	2	2	Nuevo/Sellado	12.85	2	Registro Histórico	1	2026-01-12
977	1	3	Buen estado (Usado)	29.12	1	Registro Histórico	2	2026-01-12
978	3	3	Nuevo/Sellado	41.47	1	Registro Histórico	2	2026-01-12
979	1	3	Buen estado (Usado)	48.1	1	Registro Histórico	1	2026-01-12
980	1	1	Nuevo/Sellado	11.22	1	Registro Histórico	2	2026-01-12
981	2	1	Buen estado (Usado)	40.59	1	Registro Histórico	1	2026-01-13
982	3	5	Nuevo/Sellado	12.57	4	Registro Histórico	2	2026-01-13
983	2	3	Buen estado (Usado)	30.62	1	Registro Histórico	2	2026-01-13
984	2	1	Nuevo/Sellado	15.55	1	Registro Histórico	1	2026-01-13
985	2	1	Nuevo/Sellado	26.28	1	Registro Histórico	2	2026-01-13
986	1	3	Regular	21.91	1	Registro Histórico	2	2026-01-13
987	1	1	Regular	21.71	1	Registro Histórico	1	2026-01-13
988	1	1	Buen estado (Usado)	43.03	1	Registro Histórico	1	2026-01-13
989	2	3	Regular	49.92	1	Registro Histórico	2	2026-01-13
990	1	2	Buen estado (Usado)	7.41	4	Registro Histórico	2	2026-01-13
991	3	1	Regular	18.01	1	Registro Histórico	2	2026-01-14
992	3	4	Regular	12.87	3	Registro Histórico	1	2026-01-14
993	2	1	Regular	26.3	1	Registro Histórico	1	2026-01-14
994	3	1	Regular	26.43	1	Registro Histórico	2	2026-01-14
995	3	1	Buen estado (Usado)	42.15	1	Registro Histórico	2	2026-01-14
996	1	1	Nuevo/Sellado	18.44	1	Registro Histórico	2	2026-01-14
997	1	5	Buen estado (Usado)	8.36	4	Registro Histórico	2	2026-01-14
998	3	1	Nuevo/Sellado	23.26	1	Registro Histórico	2	2026-01-14
999	3	3	Buen estado (Usado)	33.5	1	Registro Histórico	2	2026-01-15
1000	2	3	Regular	21.03	1	Registro Histórico	2	2026-01-15
1001	1	1	Regular	49.14	1	Registro Histórico	2	2026-01-15
1002	2	2	Nuevo/Sellado	14.06	3	Registro Histórico	1	2026-01-15
1003	3	1	Buen estado (Usado)	44.31	1	Registro Histórico	2	2026-01-15
1004	3	2	Regular	5.31	2	Registro Histórico	2	2026-01-15
1005	2	1	Regular	49.89	1	Registro Histórico	2	2026-01-15
1006	1	1	Regular	47.06	1	Registro Histórico	1	2026-01-16
1007	3	3	Nuevo/Sellado	11.46	1	Registro Histórico	2	2026-01-16
1008	2	1	Buen estado (Usado)	47.34	1	Registro Histórico	1	2026-01-16
1009	1	5	Nuevo/Sellado	9.44	2	Registro Histórico	1	2026-01-16
1010	3	4	Buen estado (Usado)	14.42	4	Registro Histórico	1	2026-01-16
1011	3	1	Buen estado (Usado)	18.17	1	Registro Histórico	1	2026-01-16
1012	3	3	Buen estado (Usado)	48.75	1	Registro Histórico	1	2026-01-16
1013	3	1	Regular	29.18	1	Registro Histórico	2	2026-01-16
1014	2	1	Buen estado (Usado)	34.58	1	Registro Histórico	2	2026-01-16
1015	1	3	Buen estado (Usado)	37.76	1	Registro Histórico	1	2026-01-17
1016	2	3	Buen estado (Usado)	44.73	1	Registro Histórico	1	2026-01-17
1017	3	4	Buen estado (Usado)	18.11	4	Registro Histórico	1	2026-01-17
1018	1	2	Regular	11.41	3	Registro Histórico	1	2026-01-17
1019	2	1	Regular	35.81	1	Registro Histórico	2	2026-01-17
1020	1	3	Regular	26.28	1	Registro Histórico	1	2026-01-17
1021	3	1	Nuevo/Sellado	30.47	1	Registro Histórico	1	2026-01-17
1022	2	1	Nuevo/Sellado	39.21	1	Registro Histórico	1	2026-01-18
1023	3	4	Regular	18.75	4	Registro Histórico	2	2026-01-18
1024	2	2	Buen estado (Usado)	12.53	3	Registro Histórico	1	2026-01-18
1025	2	3	Regular	46.17	1	Registro Histórico	2	2026-01-18
1026	1	3	Nuevo/Sellado	22.6	1	Registro Histórico	1	2026-01-18
1027	2	3	Buen estado (Usado)	47.73	1	Registro Histórico	2	2026-01-18
1028	2	1	Regular	16.33	1	Registro Histórico	2	2026-01-18
1029	2	3	Buen estado (Usado)	18.6	1	Registro Histórico	2	2026-01-19
1030	1	3	Buen estado (Usado)	20.59	1	Registro Histórico	2	2026-01-19
1031	1	1	Regular	11.07	1	Registro Histórico	2	2026-01-19
1032	3	2	Nuevo/Sellado	16.13	4	Registro Histórico	2	2026-01-19
1033	3	1	Regular	47.74	1	Registro Histórico	2	2026-01-19
1034	2	3	Regular	35.74	1	Registro Histórico	1	2026-01-19
1035	2	3	Regular	47.34	1	Registro Histórico	1	2026-01-19
1036	2	3	Buen estado (Usado)	34.88	1	Registro Histórico	2	2026-01-19
1037	1	2	Nuevo/Sellado	16.35	3	Registro Histórico	2	2026-01-19
1038	1	3	Regular	15.59	1	Registro Histórico	2	2026-01-20
1039	1	1	Buen estado (Usado)	44.95	1	Registro Histórico	1	2026-01-20
1040	1	1	Nuevo/Sellado	45.4	1	Registro Histórico	1	2026-01-20
1041	3	3	Regular	12.96	1	Registro Histórico	1	2026-01-20
1042	3	3	Buen estado (Usado)	18.77	1	Registro Histórico	2	2026-01-20
1043	3	3	Nuevo/Sellado	43.72	1	Registro Histórico	1	2026-01-20
1044	1	2	Buen estado (Usado)	16.26	4	Registro Histórico	1	2026-01-21
1045	2	1	Buen estado (Usado)	13.33	1	Registro Histórico	2	2026-01-21
1046	2	3	Regular	42.66	1	Registro Histórico	1	2026-01-21
1047	1	3	Buen estado (Usado)	23.5	1	Registro Histórico	2	2026-01-21
1048	1	2	Buen estado (Usado)	15.22	2	Registro Histórico	2	2026-01-21
1049	1	2	Buen estado (Usado)	13.21	4	Registro Histórico	1	2026-01-21
1050	3	2	Regular	8.81	2	Registro Histórico	1	2026-01-22
1051	1	3	Buen estado (Usado)	12.73	1	Registro Histórico	2	2026-01-22
1052	2	2	Regular	15.25	2	Registro Histórico	1	2026-01-22
1053	1	3	Buen estado (Usado)	48.62	1	Registro Histórico	1	2026-01-22
1054	2	1	Nuevo/Sellado	27.59	1	Registro Histórico	1	2026-01-22
1055	2	1	Buen estado (Usado)	32.59	1	Registro Histórico	1	2026-01-23
1056	3	1	Nuevo/Sellado	16.2	1	Registro Histórico	1	2026-01-23
1057	2	1	Regular	28.84	1	Registro Histórico	1	2026-01-23
1058	1	3	Buen estado (Usado)	33.71	1	Registro Histórico	2	2026-01-23
1059	1	3	Nuevo/Sellado	10.78	1	Registro Histórico	2	2026-01-23
1060	2	3	Buen estado (Usado)	19.64	1	Registro Histórico	1	2026-01-23
1061	1	1	Buen estado (Usado)	25.71	1	Registro Histórico	2	2026-01-23
1062	2	3	Regular	28.24	1	Registro Histórico	1	2026-01-23
1063	3	1	Regular	42.58	1	Registro Histórico	1	2026-01-23
1064	3	3	Nuevo/Sellado	34.9	1	Registro Histórico	1	2026-01-23
1065	3	1	Buen estado (Usado)	14.02	1	Registro Histórico	2	2026-01-24
1066	3	3	Buen estado (Usado)	22.97	1	Registro Histórico	1	2026-01-24
1067	2	5	Buen estado (Usado)	14.29	3	Registro Histórico	1	2026-01-24
1068	2	5	Regular	6.25	3	Registro Histórico	1	2026-01-24
1069	3	2	Regular	11.69	3	Registro Histórico	2	2026-01-24
1070	3	4	Regular	19.66	2	Registro Histórico	1	2026-01-24
1071	2	3	Regular	17.23	1	Registro Histórico	2	2026-01-24
1072	1	3	Regular	37.02	1	Registro Histórico	2	2026-01-24
1073	1	2	Regular	15.24	2	Registro Histórico	1	2026-01-24
1074	1	3	Nuevo/Sellado	12.66	1	Registro Histórico	2	2026-01-24
1075	2	3	Nuevo/Sellado	16.68	1	Registro Histórico	1	2026-01-24
1076	3	4	Buen estado (Usado)	8.52	3	Registro Histórico	2	2026-01-25
1077	1	5	Nuevo/Sellado	11.87	3	Registro Histórico	1	2026-01-25
1078	3	4	Buen estado (Usado)	6.21	4	Registro Histórico	1	2026-01-25
1079	1	1	Buen estado (Usado)	24.34	1	Registro Histórico	2	2026-01-25
1080	3	1	Buen estado (Usado)	16.66	1	Registro Histórico	1	2026-01-25
1081	3	1	Nuevo/Sellado	36.28	1	Registro Histórico	1	2026-01-25
1082	3	2	Buen estado (Usado)	11.11	2	Registro Histórico	2	2026-01-25
1083	2	3	Regular	26.51	1	Registro Histórico	2	2026-01-25
1084	1	5	Regular	9.98	4	Registro Histórico	2	2026-01-25
1085	2	3	Regular	43.24	1	Registro Histórico	2	2026-01-25
1086	1	3	Nuevo/Sellado	26.2	1	Registro Histórico	2	2026-01-26
1087	2	3	Nuevo/Sellado	18.07	1	Registro Histórico	2	2026-01-26
1088	3	2	Buen estado (Usado)	18.65	4	Registro Histórico	2	2026-01-26
1089	2	1	Buen estado (Usado)	25.89	1	Registro Histórico	1	2026-01-26
1090	2	1	Buen estado (Usado)	18.02	1	Registro Histórico	2	2026-01-26
1091	3	1	Regular	24.52	1	Registro Histórico	2	2026-01-26
1092	3	1	Nuevo/Sellado	47.75	1	Registro Histórico	1	2026-01-26
1093	2	3	Regular	43.39	1	Registro Histórico	2	2026-01-26
1094	3	3	Nuevo/Sellado	37.08	1	Registro Histórico	2	2026-01-26
1095	2	3	Regular	37.08	1	Registro Histórico	2	2026-01-27
1096	1	1	Buen estado (Usado)	13.46	1	Registro Histórico	1	2026-01-27
1097	3	3	Regular	11.47	1	Registro Histórico	2	2026-01-27
1098	2	1	Nuevo/Sellado	22.52	1	Registro Histórico	2	2026-01-27
1099	2	5	Nuevo/Sellado	9.23	3	Registro Histórico	2	2026-01-27
1100	1	3	Nuevo/Sellado	16.71	1	Registro Histórico	2	2026-01-28
1101	1	1	Buen estado (Usado)	19.18	1	Registro Histórico	2	2026-01-28
1102	2	2	Regular	12.18	3	Registro Histórico	2	2026-01-28
1103	3	2	Buen estado (Usado)	9.86	4	Registro Histórico	2	2026-01-28
1104	2	3	Buen estado (Usado)	15.94	1	Registro Histórico	1	2026-01-28
1105	3	2	Nuevo/Sellado	16.3	3	Registro Histórico	2	2026-01-28
1106	2	1	Regular	30.69	1	Registro Histórico	1	2026-01-29
1107	3	3	Regular	42.63	1	Registro Histórico	1	2026-01-29
1108	1	1	Nuevo/Sellado	41.87	1	Registro Histórico	1	2026-01-29
1109	1	1	Regular	34.57	1	Registro Histórico	1	2026-01-29
1110	2	2	Buen estado (Usado)	8.53	2	Registro Histórico	1	2026-01-29
1111	1	5	Regular	17.51	3	Registro Histórico	1	2026-01-30
1112	3	2	Nuevo/Sellado	13.88	4	Registro Histórico	1	2026-01-30
1113	2	5	Regular	14.54	2	Registro Histórico	1	2026-01-30
1114	3	2	Regular	11.99	2	Registro Histórico	2	2026-01-30
1115	3	1	Nuevo/Sellado	20.61	1	Registro Histórico	2	2026-01-30
1116	1	2	Nuevo/Sellado	18.61	3	Registro Histórico	1	2026-01-31
1117	3	3	Regular	34.18	1	Registro Histórico	1	2026-01-31
1118	3	5	Regular	14.36	2	Registro Histórico	1	2026-01-31
1119	3	1	Regular	37.95	1	Registro Histórico	1	2026-01-31
1120	1	3	Nuevo/Sellado	50	1	Registro Histórico	1	2026-01-31
1121	2	3	Buen estado (Usado)	32.35	1	Registro Histórico	1	2026-01-31
1122	3	3	Regular	20.53	1	Registro Histórico	1	2026-01-31
1123	1	4	Buen estado (Usado)	11.5	2	Registro Histórico	1	2026-01-31
1124	1	3	Regular	37.38	1	Registro Histórico	1	2026-01-31
1125	1	2	Regular	17.96	3	Registro Histórico	1	2026-01-31
1126	3	1	Nuevo/Sellado	18.87	1	Registro Histórico	1	2026-02-01
1127	2	2	Buen estado (Usado)	5.75	2	Registro Histórico	2	2026-02-01
1128	2	3	Regular	19.33	1	Registro Histórico	2	2026-02-01
1129	1	2	Buen estado (Usado)	7.69	3	Registro Histórico	2	2026-02-01
1130	1	3	Regular	36.79	1	Registro Histórico	1	2026-02-01
1131	1	1	Nuevo/Sellado	23.54	1	Registro Histórico	1	2026-02-01
1132	1	4	Regular	15.66	2	Registro Histórico	1	2026-02-01
1133	3	1	Nuevo/Sellado	46.75	1	Registro Histórico	2	2026-02-01
1134	1	3	Nuevo/Sellado	31.84	1	Registro Histórico	2	2026-02-02
1135	1	3	Regular	48.96	1	Registro Histórico	2	2026-02-02
1136	3	3	Buen estado (Usado)	38.66	1	Registro Histórico	1	2026-02-02
1137	3	3	Nuevo/Sellado	46.59	1	Registro Histórico	1	2026-02-02
1138	3	1	Buen estado (Usado)	23.35	1	Registro Histórico	1	2026-02-02
1139	2	3	Regular	45.23	1	Registro Histórico	1	2026-02-02
1140	2	1	Buen estado (Usado)	22.38	1	Registro Histórico	1	2026-02-03
1141	2	1	Regular	19.92	1	Registro Histórico	2	2026-02-03
1142	3	3	Nuevo/Sellado	26.69	1	Registro Histórico	1	2026-02-03
1143	1	3	Buen estado (Usado)	34.92	1	Registro Histórico	1	2026-02-03
1144	1	3	Nuevo/Sellado	28.82	1	Registro Histórico	1	2026-02-03
1145	3	5	Nuevo/Sellado	10.4	3	Registro Histórico	2	2026-02-03
1146	1	1	Regular	48.5	1	Registro Histórico	2	2026-02-03
1147	3	2	Regular	15.86	3	Registro Histórico	1	2026-02-04
1148	3	3	Nuevo/Sellado	30.67	1	Registro Histórico	1	2026-02-04
1149	3	4	Buen estado (Usado)	16.21	3	Registro Histórico	1	2026-02-04
1150	3	1	Buen estado (Usado)	35.19	1	Registro Histórico	1	2026-02-04
1151	2	3	Regular	47.95	1	Registro Histórico	1	2026-02-04
1152	1	1	Buen estado (Usado)	25.71	1	Registro Histórico	2	2026-02-04
1153	1	3	Nuevo/Sellado	36.95	1	Registro Histórico	2	2026-02-04
1154	3	1	Nuevo/Sellado	48.85	1	Registro Histórico	2	2026-02-04
1155	3	2	Regular	13.91	4	Registro Histórico	2	2026-02-05
1156	1	1	Nuevo/Sellado	40.1	1	Registro Histórico	2	2026-02-05
1157	1	1	Buen estado (Usado)	10.02	1	Registro Histórico	1	2026-02-05
1158	3	1	Nuevo/Sellado	36.14	1	Registro Histórico	1	2026-02-05
1159	1	4	Buen estado (Usado)	13.67	3	Registro Histórico	2	2026-02-05
1160	1	2	Regular	19.05	4	Registro Histórico	1	2026-02-05
1161	2	3	Buen estado (Usado)	16.39	1	Registro Histórico	2	2026-02-05
1162	2	3	Regular	23.97	1	Registro Histórico	1	2026-02-06
1163	1	1	Regular	46.26	1	Registro Histórico	2	2026-02-06
1164	3	3	Regular	21.44	1	Registro Histórico	2	2026-02-06
1165	1	1	Nuevo/Sellado	27.13	1	Registro Histórico	1	2026-02-06
1166	1	1	Regular	30.71	1	Registro Histórico	1	2026-02-07
1167	2	2	Buen estado (Usado)	9.85	4	Registro Histórico	2	2026-02-07
1168	1	1	Buen estado (Usado)	49.87	1	Registro Histórico	1	2026-02-07
1169	3	3	Buen estado (Usado)	43.96	1	Registro Histórico	1	2026-02-07
1170	2	1	Buen estado (Usado)	18.71	1	Registro Histórico	2	2026-02-07
1171	1	4	Nuevo/Sellado	14.2	4	Registro Histórico	1	2026-02-07
1172	3	2	Regular	11.12	2	Registro Histórico	1	2026-02-07
1173	3	3	Nuevo/Sellado	21.52	1	Registro Histórico	1	2026-02-07
1174	2	3	Nuevo/Sellado	35.58	1	Registro Histórico	1	2026-02-07
1175	2	3	Buen estado (Usado)	22.24	1	Registro Histórico	2	2026-02-07
1176	2	1	Regular	15.59	1	Registro Histórico	1	2026-02-08
1177	2	5	Regular	12.51	2	Registro Histórico	2	2026-02-08
1178	2	2	Regular	15.4	2	Registro Histórico	2	2026-02-08
1179	2	1	Nuevo/Sellado	16.95	1	Registro Histórico	1	2026-02-08
1180	1	5	Regular	17.68	4	Registro Histórico	2	2026-02-08
1181	2	1	Nuevo/Sellado	39.08	1	Registro Histórico	1	2026-02-08
1182	1	3	Regular	36.12	1	Registro Histórico	2	2026-02-08
1183	3	2	Regular	8.64	3	Registro Histórico	2	2026-02-09
1184	3	2	Buen estado (Usado)	11.4	2	Registro Histórico	2	2026-02-09
1185	3	4	Nuevo/Sellado	19.18	4	Registro Histórico	1	2026-02-09
1186	1	3	Buen estado (Usado)	24.3	1	Registro Histórico	2	2026-02-09
1187	2	1	Buen estado (Usado)	47.43	1	Registro Histórico	1	2026-02-09
1188	1	2	Nuevo/Sellado	10.35	2	Registro Histórico	1	2026-02-09
1189	3	2	Nuevo/Sellado	15.26	3	Registro Histórico	1	2026-02-09
1190	3	1	Nuevo/Sellado	36.57	1	Registro Histórico	2	2026-02-10
1191	2	3	Buen estado (Usado)	39.32	1	Registro Histórico	1	2026-02-10
1192	2	1	Nuevo/Sellado	34.26	1	Registro Histórico	1	2026-02-10
1193	1	2	Buen estado (Usado)	18.8	2	Registro Histórico	2	2026-02-10
1194	3	1	Regular	30.93	1	Registro Histórico	2	2026-02-10
1195	1	3	Regular	13.15	1	Registro Histórico	2	2026-02-10
1196	3	1	Nuevo/Sellado	49.08	1	Registro Histórico	2	2026-02-10
1197	2	2	Regular	6.77	2	Registro Histórico	1	2026-02-11
1198	2	5	Regular	13.95	3	Registro Histórico	2	2026-02-11
1199	3	3	Nuevo/Sellado	19.09	1	Registro Histórico	1	2026-02-11
1200	3	5	Regular	7.19	2	Registro Histórico	2	2026-02-11
1201	1	1	Buen estado (Usado)	16.16	1	Registro Histórico	2	2026-02-11
1202	1	3	Regular	37.74	1	Registro Histórico	2	2026-02-11
1203	1	1	Buen estado (Usado)	14.81	1	Registro Histórico	2	2026-02-12
1204	1	3	Buen estado (Usado)	23.8	1	Registro Histórico	2	2026-02-12
1205	2	5	Nuevo/Sellado	13.32	4	Registro Histórico	1	2026-02-12
1206	1	2	Buen estado (Usado)	11.33	2	Registro Histórico	2	2026-02-12
1207	2	1	Regular	20	1	Registro Histórico	1	2026-02-12
1208	3	3	Nuevo/Sellado	35.96	1	Registro Histórico	1	2026-02-12
1209	2	4	Buen estado (Usado)	17.22	4	Registro Histórico	2	2026-02-12
1210	2	3	Nuevo/Sellado	11.73	1	Registro Histórico	1	2026-02-13
1211	3	1	Buen estado (Usado)	23.14	1	Registro Histórico	2	2026-02-13
1212	3	3	Regular	31.09	1	Registro Histórico	1	2026-02-13
1213	2	2	Regular	18.41	3	Registro Histórico	1	2026-02-13
1214	1	1	Regular	20.47	1	Registro Histórico	2	2026-02-13
1215	3	2	Buen estado (Usado)	8.29	3	Registro Histórico	2	2026-02-13
1216	2	1	Buen estado (Usado)	22.53	1	Registro Histórico	1	2026-02-13
1217	1	1	Nuevo/Sellado	12.33	1	Registro Histórico	2	2026-02-14
1218	1	3	Buen estado (Usado)	19.13	1	Registro Histórico	2	2026-02-14
1219	3	5	Regular	16.14	3	Registro Histórico	1	2026-02-14
1220	2	1	Buen estado (Usado)	37.33	1	Registro Histórico	1	2026-02-14
1221	3	1	Regular	16.08	1	Registro Histórico	2	2026-02-14
1222	3	3	Regular	25.57	1	Registro Histórico	1	2026-02-14
1223	3	2	Buen estado (Usado)	12.75	2	Registro Histórico	1	2026-02-14
1224	3	1	Buen estado (Usado)	32	1	Registro Histórico	2	2026-02-14
1225	3	1	Buen estado (Usado)	36.05	1	Registro Histórico	2	2026-02-15
1226	1	2	Nuevo/Sellado	6.13	3	Registro Histórico	2	2026-02-15
1227	1	3	Regular	37.6	1	Registro Histórico	2	2026-02-15
1228	2	2	Nuevo/Sellado	8.43	4	Registro Histórico	1	2026-02-15
1229	1	1	Nuevo/Sellado	20.29	1	Registro Histórico	1	2026-02-15
1230	3	1	Nuevo/Sellado	15.58	1	Registro Histórico	2	2026-02-15
1231	3	1	Regular	39.82	1	Registro Histórico	1	2026-02-15
1232	2	2	Buen estado (Usado)	15.21	3	Registro Histórico	2	2026-02-15
1233	3	3	Nuevo/Sellado	16.74	1	Registro Histórico	2	2026-02-15
1234	3	1	Buen estado (Usado)	32.17	1	Registro Histórico	2	2026-02-15
1235	2	4	Buen estado (Usado)	8	2	Registro Histórico	2	2026-02-15
1236	2	1	Regular	28.97	1	Registro Histórico	2	2026-02-16
1237	1	1	Nuevo/Sellado	35.7	1	Registro Histórico	1	2026-02-16
1238	2	4	Regular	5.74	4	Registro Histórico	1	2026-02-16
1239	2	2	Regular	18.02	2	Registro Histórico	1	2026-02-16
1240	2	1	Nuevo/Sellado	40.33	1	Registro Histórico	2	2026-02-16
1241	2	4	Regular	7.03	3	Registro Histórico	2	2026-02-16
1242	3	2	Regular	8.44	2	Registro Histórico	2	2026-02-16
1243	2	4	Regular	12.51	4	Registro Histórico	2	2026-02-16
1244	3	2	Regular	9.79	3	Registro Histórico	2	2026-02-16
1245	2	2	Buen estado (Usado)	7.91	4	Registro Histórico	2	2026-02-17
1246	1	2	Nuevo/Sellado	6.29	2	Registro Histórico	1	2026-02-17
1247	3	2	Nuevo/Sellado	15.31	3	Registro Histórico	2	2026-02-17
1248	3	2	Nuevo/Sellado	5.89	3	Registro Histórico	1	2026-02-17
1249	2	2	Regular	16.12	3	Registro Histórico	2	2026-02-17
1250	1	2	Nuevo/Sellado	11.7	4	Registro Histórico	2	2026-02-17
1251	1	2	Nuevo/Sellado	15.85	4	Registro Histórico	2	2026-02-18
1252	1	4	Nuevo/Sellado	13	4	Registro Histórico	1	2026-02-18
1253	1	5	Buen estado (Usado)	19.87	3	Registro Histórico	2	2026-02-18
1254	3	1	Buen estado (Usado)	16.95	1	Registro Histórico	1	2026-02-18
1255	2	3	Buen estado (Usado)	17.46	1	Registro Histórico	1	2026-02-18
1256	3	1	Buen estado (Usado)	35.88	1	Registro Histórico	2	2026-02-18
1257	1	2	Regular	10.21	2	Registro Histórico	1	2026-02-19
1258	3	3	Regular	23	1	Registro Histórico	2	2026-02-19
1259	3	4	Regular	9.4	4	Registro Histórico	1	2026-02-19
1260	2	4	Nuevo/Sellado	10.45	3	Registro Histórico	1	2026-02-19
1261	2	1	Buen estado (Usado)	35.51	1	Registro Histórico	2	2026-02-19
1262	3	3	Regular	16.95	1	Registro Histórico	2	2026-02-19
1263	3	1	Nuevo/Sellado	11.85	1	Registro Histórico	2	2026-02-19
1264	2	3	Buen estado (Usado)	30.27	1	Registro Histórico	2	2026-02-19
1265	1	1	Regular	41.18	1	Registro Histórico	2	2026-02-19
1266	1	3	Buen estado (Usado)	20.48	1	Registro Histórico	2	2026-02-19
1267	3	1	Nuevo/Sellado	40.89	1	Registro Histórico	1	2026-02-19
1268	1	1	Regular	35.52	1	Registro Histórico	1	2026-02-20
1269	1	3	Regular	20.3	1	Registro Histórico	1	2026-02-20
1270	2	3	Buen estado (Usado)	44.23	1	Registro Histórico	1	2026-02-20
1271	3	1	Nuevo/Sellado	23.29	1	Registro Histórico	2	2026-02-20
1272	3	5	Nuevo/Sellado	11.92	2	Registro Histórico	1	2026-02-20
1273	3	1	Regular	26.68	1	Registro Histórico	1	2026-02-20
1274	3	1	Nuevo/Sellado	20.82	1	Registro Histórico	2	2026-02-21
1275	2	3	Buen estado (Usado)	49.92	1	Registro Histórico	1	2026-02-21
1276	1	1	Regular	25.89	1	Registro Histórico	2	2026-02-21
1277	1	3	Nuevo/Sellado	46.56	1	Registro Histórico	1	2026-02-21
1278	3	1	Regular	12.08	1	Registro Histórico	1	2026-02-21
1279	2	2	Nuevo/Sellado	17.81	4	Registro Histórico	1	2026-02-21
1280	1	3	Nuevo/Sellado	24.19	1	Registro Histórico	1	2026-02-21
1281	1	3	Buen estado (Usado)	11.71	1	Registro Histórico	1	2026-02-21
1282	1	1	Buen estado (Usado)	23.04	1	Registro Histórico	1	2026-02-22
1283	2	4	Nuevo/Sellado	10.85	4	Registro Histórico	1	2026-02-22
1284	2	1	Regular	45.6	1	Registro Histórico	1	2026-02-22
1285	3	3	Nuevo/Sellado	32.73	1	Registro Histórico	1	2026-02-22
1286	2	1	Regular	46.54	1	Registro Histórico	1	2026-02-22
1287	3	3	Buen estado (Usado)	24.35	1	Registro Histórico	1	2026-02-22
1288	3	2	Regular	16.35	4	Registro Histórico	1	2026-02-22
1289	1	3	Nuevo/Sellado	14.63	1	Registro Histórico	1	2026-02-22
1290	2	3	Buen estado (Usado)	19.21	1	Registro Histórico	1	2026-02-23
1291	2	5	Buen estado (Usado)	8.72	3	Registro Histórico	2	2026-02-23
1292	3	2	Buen estado (Usado)	18.07	4	Registro Histórico	2	2026-02-23
1293	2	1	Buen estado (Usado)	22.47	1	Registro Histórico	1	2026-02-23
1294	1	2	Buen estado (Usado)	8.99	3	Registro Histórico	1	2026-02-23
1295	1	2	Regular	17.42	2	Registro Histórico	1	2026-02-23
1296	2	3	Nuevo/Sellado	35.48	1	Registro Histórico	1	2026-02-24
1297	3	3	Regular	23.8	1	Registro Histórico	1	2026-02-24
1298	1	3	Regular	39.41	1	Registro Histórico	2	2026-02-24
1299	2	3	Buen estado (Usado)	24.29	1	Registro Histórico	2	2026-02-24
1300	1	1	Regular	20.88	1	Registro Histórico	2	2026-02-25
1301	2	3	Nuevo/Sellado	38.65	1	Registro Histórico	1	2026-02-25
1302	2	1	Nuevo/Sellado	27.38	1	Registro Histórico	2	2026-02-25
1303	2	3	Buen estado (Usado)	44.27	1	Registro Histórico	1	2026-02-25
1304	3	1	Buen estado (Usado)	31.95	1	Registro Histórico	1	2026-02-25
1305	3	2	Nuevo/Sellado	16.52	2	Registro Histórico	1	2026-02-26
1306	3	1	Nuevo/Sellado	26.26	1	Registro Histórico	1	2026-02-26
1307	3	2	Nuevo/Sellado	8.62	4	Registro Histórico	1	2026-02-26
1308	1	1	Buen estado (Usado)	40.32	1	Registro Histórico	2	2026-02-26
1309	3	2	Buen estado (Usado)	7.32	3	Registro Histórico	2	2026-02-26
1310	3	2	Nuevo/Sellado	5.05	3	Registro Histórico	1	2026-02-26
1311	3	1	Regular	32.34	1	Registro Histórico	2	2026-02-26
1312	1	2	Nuevo/Sellado	19.64	4	Registro Histórico	1	2026-02-27
1313	1	2	Regular	15.81	3	Registro Histórico	2	2026-02-27
1314	3	3	Regular	48.88	1	Registro Histórico	2	2026-02-27
1315	2	3	Regular	32.3	1	Registro Histórico	1	2026-02-27
1316	2	1	Regular	22.1	1	Registro Histórico	1	2026-02-27
1317	1	4	Buen estado (Usado)	11.06	2	Registro Histórico	1	2026-02-27
1318	3	1	Nuevo/Sellado	33.35	1	Registro Histórico	1	2026-02-27
1319	2	1	Regular	47.11	1	Registro Histórico	1	2026-02-27
1320	1	3	Buen estado (Usado)	27.58	1	Registro Histórico	2	2026-02-27
1321	1	1	Regular	33.33	1	Registro Histórico	2	2026-02-28
1322	3	3	Buen estado (Usado)	43.68	1	Registro Histórico	2	2026-02-28
1323	3	1	Nuevo/Sellado	17.14	1	Registro Histórico	1	2026-02-28
1324	3	3	Buen estado (Usado)	32.77	1	Registro Histórico	2	2026-02-28
1325	2	3	Buen estado (Usado)	24.95	1	Registro Histórico	2	2026-02-28
1326	1	2	Regular	14.25	4	Registro Histórico	2	2026-03-01
1327	1	4	Buen estado (Usado)	8.58	4	Registro Histórico	1	2026-03-02
1328	1	1	Regular	6.15	1	Registro Histórico	2	2026-03-03
1329	1	2	Nuevo/Sellado	4.77	3	Registro Histórico	2	2026-03-04
1330	3	5	Regular	9.58	2	Registro Histórico	2	2026-03-05
1331	1	3	Regular	12.18	1	Registro Histórico	1	2026-03-06
1332	2	5	Regular	4.51	2	Registro Histórico	1	2026-03-06
1333	3	5	Nuevo/Sellado	13.88	2	Registro Histórico	2	2026-03-07
1334	3	5	Regular	7.21	3	Registro Histórico	1	2026-03-08
1335	2	3	Nuevo/Sellado	8.21	1	Registro Histórico	1	2026-03-08
1336	1	3	Regular	2.5	1	Registro Histórico	2	2026-03-09
1337	2	2	Buen estado (Usado)	5.39	2	Registro Histórico	2	2026-03-10
1338	1	2	Nuevo/Sellado	13.68	2	Registro Histórico	2	2026-03-10
1339	2	1	Nuevo/Sellado	10.57	1	Registro Histórico	1	2026-03-10
1340	3	2	Buen estado (Usado)	10.66	2	Registro Histórico	1	2026-03-11
1341	3	4	Nuevo/Sellado	11.87	2	Registro Histórico	2	2026-03-11
1342	1	2	Buen estado (Usado)	11.34	3	Registro Histórico	1	2026-03-12
1343	2	3	Nuevo/Sellado	4.62	1	Registro Histórico	1	2026-03-12
1344	3	2	Nuevo/Sellado	3.72	3	Registro Histórico	1	2026-03-12
1345	3	5	Buen estado (Usado)	8.8	4	Registro Histórico	1	2026-03-13
1346	2	2	Nuevo/Sellado	3.57	2	Registro Histórico	2	2026-03-13
1347	1	4	Nuevo/Sellado	9.13	4	Registro Histórico	2	2026-03-13
1348	3	2	Buen estado (Usado)	9.19	4	Registro Histórico	1	2026-03-14
1349	1	1	Nuevo/Sellado	11.71	1	Registro Histórico	1	2026-03-15
1350	2	4	Regular	5.94	4	Registro Histórico	2	2026-03-15
1351	2	3	Nuevo/Sellado	11.57	1	Registro Histórico	2	2026-03-16
1352	2	4	Nuevo/Sellado	2.09	4	Registro Histórico	2	2026-03-16
1353	3	2	Regular	14.75	2	Registro Histórico	1	2026-03-16
1354	1	1	Regular	12.93	1	Registro Histórico	1	2026-03-17
1355	3	5	Regular	14.05	4	Registro Histórico	1	2026-03-17
1356	2	1	Regular	14.33	1	Registro Histórico	1	2026-03-17
1357	2	5	Nuevo/Sellado	9.24	2	Registro Histórico	1	2026-03-18
1358	2	1	Regular	9.67	1	Registro Histórico	2	2026-03-18
1359	3	1	Nuevo/Sellado	3.62	1	Registro Histórico	2	2026-03-18
1360	3	2	Nuevo/Sellado	5.76	3	Registro Histórico	2	2026-03-19
1361	2	5	Regular	12.24	4	Registro Histórico	1	2026-03-19
1362	2	1	Regular	12.25	1	Registro Histórico	1	2026-03-20
1363	3	4	Buen estado (Usado)	4.88	4	Registro Histórico	1	2026-03-20
1364	1	2	Regular	8.43	3	Registro Histórico	1	2026-03-20
1365	1	1	Buen estado (Usado)	10.88	1	Registro Histórico	2	2026-03-21
1366	3	2	Nuevo/Sellado	3.16	4	Registro Histórico	1	2026-03-21
1367	2	2	Nuevo/Sellado	5.14	3	Registro Histórico	1	2026-03-21
1368	2	1	Buen estado (Usado)	6.03	1	Registro Histórico	1	2026-03-22
1369	3	2	Regular	14.25	2	Registro Histórico	2	2026-03-22
1370	2	4	Buen estado (Usado)	12.23	3	Registro Histórico	2	2026-03-22
1371	2	5	Nuevo/Sellado	6.57	2	Registro Histórico	1	2026-03-23
1372	1	4	Regular	11.83	3	Registro Histórico	1	2026-03-23
1373	1	5	Buen estado (Usado)	13.64	3	Registro Histórico	2	2026-03-24
1374	2	2	Nuevo/Sellado	8.56	2	Registro Histórico	1	2026-03-25
1375	3	3	Regular	10.42	1	Registro Histórico	2	2026-03-25
1376	3	1	Nuevo/Sellado	2.74	1	Registro Histórico	1	2026-03-25
1377	2	5	Nuevo/Sellado	6.67	4	Registro Histórico	1	2026-03-26
1378	2	2	Regular	4	2	Registro Histórico	1	2026-03-26
1379	1	5	Nuevo/Sellado	10.41	2	Registro Histórico	2	2026-03-27
1380	2	2	Regular	5.88	3	Registro Histórico	2	2026-03-28
1381	1	3	Nuevo/Sellado	11.31	1	Registro Histórico	2	2026-03-28
1382	2	2	Buen estado (Usado)	7.71	3	Registro Histórico	2	2026-03-28
1383	3	5	Nuevo/Sellado	10.56	2	Registro Histórico	1	2026-03-29
1384	6	1	Nuevo	12	2	Gucci	1	2026-04-01
1385	9	2	Nuevo	2	2	La costeña	1	2026-04-01
1386	9	3	Nuevo	5	1	La paca	1	2026-04-01
\.


--
-- Data for Name: estados_campanas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.estados_campanas ("Id_Estado_Campana", "Nombre_Estado") FROM stdin;
1	Programada (Futura)
2	Activa
3	Finalizada
\.


--
-- Data for Name: estados_rep; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.estados_rep ("Id_Estado", "Nombre_Estado") FROM stdin;
1	Querétaro
2	Ciudad de México
3	Guanajuato
\.


--
-- Data for Name: generos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.generos ("Id_Genero", "Nombre_genero") FROM stdin;
1	Masculino
2	Femenino
3	Otro
\.


--
-- Data for Name: inscripciones_voluntariados; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.inscripciones_voluntariados ("Id_Inscripcion", "Id_Usuario", "Id_Voluntariado", "Fecha_Inscripcion") FROM stdin;
2	6	2	2026-04-01 20:47:08.736266+00
6	6	3	2026-04-01 21:00:53.960048+00
8	9	3	2026-04-01 23:52:23.828679+00
\.


--
-- Data for Name: municipios; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.municipios ("Id_Municipio", "Nombre_Municipio", "Id_Estado") FROM stdin;
1	Santiago de Querétaro	1
2	El Marqués	1
3	Corregidora	1
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.roles ("Id_Rol", "Nombre_Rol") FROM stdin;
1	Admin
2	Recepcionista
3	Ciudadano
\.


--
-- Data for Name: unidades; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.unidades ("Id_Unidad", "Nombre_Unidad") FROM stdin;
1	Piezas
2	Kilogramos (kg)
3	Litros (L)
4	Cajas
5	Paquetes
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.usuarios ("id_Usuario", "Nombre", "Apellido_P", "Apellido_M", "Correo", "Password", "Edad", "Id_Direccion", "Id_Albergue", "Id_Rol", "Tel", "Id_Genero", "Fecha_Registro") FROM stdin;
1	Carlos	Ramírez	López	admin@bec.com	$2b$12$r3TwBfmN6ZfrB.UkNkeBFOM2.zCtjsTD38K5EIJwHTmOvXJSBV4fa	35	2	\N	1	4421110000	1	2026-03-28 23:43:41.927744+00
2	María	González	Hernández	recepcion@yimpathi.org	$2b$12$e0waiAV8JxKQtG2wlX02zebc8TimHGpJaUUeRMHxKi9raQ0PJ9jfW	28	4	1	2	4422220000	2	2026-03-28 23:43:41.927744+00
5	ff	sd	qwer	add@gmai.com	$2b$12$/fgo7oty7feg9Kz5Q/pvDu7SyAOyb5c/7iObGYToVIYujTz6rwYEu	1	8	1	2	22334455	1	2026-04-01 00:48:22.551057+00
3	Juan	Pérez	Sánchez	juan.perez@gmail.com	$2b$12$qDdDnZIm4YUCC1M0CU8BGuA8hbuCIFzXP/YfnpokframX2ajPyzs6	55	5	\N	3	4423330002	1	2026-03-28 23:43:41.927744+00
6	santy	f	s	a@gmail.com	$2b$12$Si18lh/oQ6Z38.AxcvN1Le9qXrI2/V69EBuNuDw1il7MHHvbXnlxS	18	1	\N	3	0	1	2026-04-01 05:35:36.468323+00
7	Alberto	Luna	Anaya	alberto@gmail.com	$2b$12$sZF1B27ZtKDU5k3x82S5feZCg9bVp0bCNmQy7Xv4Z.fhI8TqMYSsm	16	11	\N	3	4427456632	2	2026-04-01 22:35:37.190932+00
8	Jorge	Lopez	Morales	jorge@gmail.com	$2b$12$RDYd0LV/SL4uoVN6gztMpuS6GN2ZGaa0z0U4ubF9wQxsz.875Ne0y	20	12	1	2	446352883	1	2026-04-01 23:19:18.030561+00
9	Angel	Sanchez	Linares	angel@gmail.com	$2b$12$X0r5lshkvexagzhZ3qYr9u1qeDdx/W4zsVFUrFEEvhfsWNkuP611u	64	13	1	3	4465237753	1	2026-04-01 23:23:15.823089+00
10	Albewrto	Morales	gonzales	gonzales@gmail.com	$2b$12$DSeu/y6f44XAHAiCGgMvY.TRFlVcW4DgEOzBpHHUwZt2GtlocoP/W	20	15	1	2	6634553321	1	2026-04-01 23:57:48.518048+00
\.


--
-- Data for Name: voluntariados; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.voluntariados ("Id_Voluntariado", "Nombre_Voluntariado", "Id_albergue", id_campana, "Fecha_prog", "Cupo_Max", "Hora_inicio", "Hora_Fin", "Estado_Voluntariado", "Descripcion_Requisitos") FROM stdin;
1	Reparto de cobijas en el Centro	1	1	2026-12-15	30	09:00:00	14:00:00	Activo/Próximo	Mayores de 18 años. Traer ropa cómoda y ganas de ayudar. Se proporcionará chaleco identificador.
3	Busqueda de tazos dorados	2	3	2026-03-31	34	20:23:00	23:23:00	Activo	Buscar tazos dorados
2	Apoyo en Comedor Comunitario	2	2	2026-04-05	15	07:00:00	12:00:00	Activo	Se requiere certificado médico vigente. Actividades: preparación de alimentos, servicio en mesas y limpieza.
\.


--
-- Name: albergues_Id_Albergue_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."albergues_Id_Albergue_seq"', 3, true);


--
-- Name: campanas_id_Campana_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."campanas_id_Campana_seq"', 5, true);


--
-- Name: categorias_Id_Categoria_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."categorias_Id_Categoria_seq"', 1, false);


--
-- Name: colonias_Id_Colonia_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."colonias_Id_Colonia_seq"', 1, false);


--
-- Name: direcciones_Id_Direccion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."direcciones_Id_Direccion_seq"', 15, true);


--
-- Name: donaciones_Id_Donacion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."donaciones_Id_Donacion_seq"', 1386, true);


--
-- Name: estados_campanas_Id_Estado_Campana_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."estados_campanas_Id_Estado_Campana_seq"', 1, false);


--
-- Name: estados_rep_Id_Estado_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."estados_rep_Id_Estado_seq"', 1, false);


--
-- Name: generos_Id_Genero_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."generos_Id_Genero_seq"', 1, false);


--
-- Name: inscripciones_voluntariados_Id_Inscripcion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."inscripciones_voluntariados_Id_Inscripcion_seq"', 8, true);


--
-- Name: municipios_Id_Municipio_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."municipios_Id_Municipio_seq"', 1, false);


--
-- Name: roles_Id_Rol_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."roles_Id_Rol_seq"', 1, false);


--
-- Name: unidades_Id_Unidad_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."unidades_Id_Unidad_seq"', 1, false);


--
-- Name: usuarios_id_Usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."usuarios_id_Usuario_seq"', 10, true);


--
-- Name: voluntariados_Id_Voluntariado_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public."voluntariados_Id_Voluntariado_seq"', 3, true);


--
-- Name: albergues albergues_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.albergues
    ADD CONSTRAINT albergues_pkey PRIMARY KEY ("Id_Albergue");


--
-- Name: campanas campanas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas
    ADD CONSTRAINT campanas_pkey PRIMARY KEY ("id_Campana");


--
-- Name: categorias categorias_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY ("Id_Categoria");


--
-- Name: colonias colonias_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.colonias
    ADD CONSTRAINT colonias_pkey PRIMARY KEY ("Id_Colonia");


--
-- Name: direcciones direcciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.direcciones
    ADD CONSTRAINT direcciones_pkey PRIMARY KEY ("Id_Direccion");


--
-- Name: donaciones donaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.donaciones
    ADD CONSTRAINT donaciones_pkey PRIMARY KEY ("Id_Donacion");


--
-- Name: estados_campanas estados_campanas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estados_campanas
    ADD CONSTRAINT estados_campanas_pkey PRIMARY KEY ("Id_Estado_Campana");


--
-- Name: estados_rep estados_rep_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.estados_rep
    ADD CONSTRAINT estados_rep_pkey PRIMARY KEY ("Id_Estado");


--
-- Name: generos generos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.generos
    ADD CONSTRAINT generos_pkey PRIMARY KEY ("Id_Genero");


--
-- Name: inscripciones_voluntariados inscripciones_voluntariados_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inscripciones_voluntariados
    ADD CONSTRAINT inscripciones_voluntariados_pkey PRIMARY KEY ("Id_Inscripcion");


--
-- Name: municipios municipios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipios
    ADD CONSTRAINT municipios_pkey PRIMARY KEY ("Id_Municipio");


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY ("Id_Rol");


--
-- Name: unidades unidades_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.unidades
    ADD CONSTRAINT unidades_pkey PRIMARY KEY ("Id_Unidad");


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY ("id_Usuario");


--
-- Name: voluntariados voluntariados_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.voluntariados
    ADD CONSTRAINT voluntariados_pkey PRIMARY KEY ("Id_Voluntariado");


--
-- Name: ix_albergues_Id_Albergue; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_albergues_Id_Albergue" ON public.albergues USING btree ("Id_Albergue");


--
-- Name: ix_campanas_id_Campana; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_campanas_id_Campana" ON public.campanas USING btree ("id_Campana");


--
-- Name: ix_categorias_Id_Categoria; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_categorias_Id_Categoria" ON public.categorias USING btree ("Id_Categoria");


--
-- Name: ix_colonias_Id_Colonia; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_colonias_Id_Colonia" ON public.colonias USING btree ("Id_Colonia");


--
-- Name: ix_direcciones_Id_Direccion; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_direcciones_Id_Direccion" ON public.direcciones USING btree ("Id_Direccion");


--
-- Name: ix_donaciones_Id_Donacion; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_donaciones_Id_Donacion" ON public.donaciones USING btree ("Id_Donacion");


--
-- Name: ix_estados_campanas_Id_Estado_Campana; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_estados_campanas_Id_Estado_Campana" ON public.estados_campanas USING btree ("Id_Estado_Campana");


--
-- Name: ix_estados_rep_Id_Estado; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_estados_rep_Id_Estado" ON public.estados_rep USING btree ("Id_Estado");


--
-- Name: ix_generos_Id_Genero; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_generos_Id_Genero" ON public.generos USING btree ("Id_Genero");


--
-- Name: ix_inscripciones_voluntariados_Id_Inscripcion; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_inscripciones_voluntariados_Id_Inscripcion" ON public.inscripciones_voluntariados USING btree ("Id_Inscripcion");


--
-- Name: ix_municipios_Id_Municipio; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_municipios_Id_Municipio" ON public.municipios USING btree ("Id_Municipio");


--
-- Name: ix_roles_Id_Rol; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_roles_Id_Rol" ON public.roles USING btree ("Id_Rol");


--
-- Name: ix_unidades_Id_Unidad; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_unidades_Id_Unidad" ON public.unidades USING btree ("Id_Unidad");


--
-- Name: ix_usuarios_Correo; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "ix_usuarios_Correo" ON public.usuarios USING btree ("Correo");


--
-- Name: ix_usuarios_id_Usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_usuarios_id_Usuario" ON public.usuarios USING btree ("id_Usuario");


--
-- Name: ix_voluntariados_Id_Voluntariado; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "ix_voluntariados_Id_Voluntariado" ON public.voluntariados USING btree ("Id_Voluntariado");


--
-- Name: campanas campanas_id_Estado_campana_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.campanas
    ADD CONSTRAINT "campanas_id_Estado_campana_fkey" FOREIGN KEY ("id_Estado_campana") REFERENCES public.estados_campanas("Id_Estado_Campana");


--
-- Name: colonias colonias_Id_mucipio_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.colonias
    ADD CONSTRAINT "colonias_Id_mucipio_fkey" FOREIGN KEY ("Id_mucipio") REFERENCES public.municipios("Id_Municipio");


--
-- Name: direcciones direcciones_Id_Colonia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.direcciones
    ADD CONSTRAINT "direcciones_Id_Colonia_fkey" FOREIGN KEY ("Id_Colonia") REFERENCES public.colonias("Id_Colonia");


--
-- Name: donaciones donaciones_Id_Albergue_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.donaciones
    ADD CONSTRAINT "donaciones_Id_Albergue_fkey" FOREIGN KEY ("Id_Albergue") REFERENCES public.albergues("Id_Albergue");


--
-- Name: donaciones donaciones_Id_Unidad_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.donaciones
    ADD CONSTRAINT "donaciones_Id_Unidad_fkey" FOREIGN KEY ("Id_Unidad") REFERENCES public.unidades("Id_Unidad");


--
-- Name: donaciones donaciones_Id_Usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.donaciones
    ADD CONSTRAINT "donaciones_Id_Usuario_fkey" FOREIGN KEY ("Id_Usuario") REFERENCES public.usuarios("id_Usuario");


--
-- Name: donaciones donaciones_id_Categoria_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.donaciones
    ADD CONSTRAINT "donaciones_id_Categoria_fkey" FOREIGN KEY ("id_Categoria") REFERENCES public.categorias("Id_Categoria");


--
-- Name: inscripciones_voluntariados inscripciones_voluntariados_Id_Usuario_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inscripciones_voluntariados
    ADD CONSTRAINT "inscripciones_voluntariados_Id_Usuario_fkey" FOREIGN KEY ("Id_Usuario") REFERENCES public.usuarios("id_Usuario");


--
-- Name: inscripciones_voluntariados inscripciones_voluntariados_Id_Voluntariado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.inscripciones_voluntariados
    ADD CONSTRAINT "inscripciones_voluntariados_Id_Voluntariado_fkey" FOREIGN KEY ("Id_Voluntariado") REFERENCES public.voluntariados("Id_Voluntariado");


--
-- Name: municipios municipios_Id_Estado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.municipios
    ADD CONSTRAINT "municipios_Id_Estado_fkey" FOREIGN KEY ("Id_Estado") REFERENCES public.estados_rep("Id_Estado");


--
-- Name: usuarios usuarios_Id_Albergue_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT "usuarios_Id_Albergue_fkey" FOREIGN KEY ("Id_Albergue") REFERENCES public.albergues("Id_Albergue");


--
-- Name: voluntariados voluntariados_Id_albergue_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.voluntariados
    ADD CONSTRAINT "voluntariados_Id_albergue_fkey" FOREIGN KEY ("Id_albergue") REFERENCES public.albergues("Id_Albergue");


--
-- Name: voluntariados voluntariados_id_campana_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.voluntariados
    ADD CONSTRAINT voluntariados_id_campana_fkey FOREIGN KEY (id_campana) REFERENCES public.campanas("id_Campana");


--
-- PostgreSQL database dump complete
--

\unrestrict PAFIvO64r25ym3Z3hUXmNtSN6aUfk6xiT1rcJ6cIQtjuGaO7R08vmdZbb0azWz0

