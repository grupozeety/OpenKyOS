--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: geminis; Type: COMMENT; Schema: -; Owner: saradba
--

COMMENT ON DATABASE geminis IS 'Base de datos principal del aplicativo Geminis';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: geminis_notificacion; Type: TABLE; Schema: public; Owner: saradba; Tablespace: 
--

CREATE TABLE geminis_notificacion (
    "idNotificacion" integer NOT NULL,
    "idProceso" integer,
    "idRemitente" integer,
    "idDestinatario" integer,
    asunto text,
    descripcion text,
    criticidad integer,
    "tipoMecanismo" integer,
    "eMail" text,
    celular text,
    sms text,
    fecha date,
    estado integer,
    "observacionEstado" text
);


ALTER TABLE public.geminis_notificacion OWNER TO saradba;

--
-- Name: TABLE geminis_notificacion; Type: COMMENT; Schema: public; Owner: saradba
--

COMMENT ON TABLE geminis_notificacion IS 'Registro de notificaciones';


--
-- Name: geminis_notificacion_idNotificacion_seq; Type: SEQUENCE; Schema: public; Owner: saradba
--

CREATE SEQUENCE "geminis_notificacion_idNotificacion_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."geminis_notificacion_idNotificacion_seq" OWNER TO saradba;

--
-- Name: geminis_notificacion_idNotificacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: saradba
--

ALTER SEQUENCE "geminis_notificacion_idNotificacion_seq" OWNED BY geminis_notificacion."idNotificacion";


--
-- Name: idNotificacion; Type: DEFAULT; Schema: public; Owner: saradba
--

ALTER TABLE ONLY geminis_notificacion ALTER COLUMN "idNotificacion" SET DEFAULT nextval('"geminis_notificacion_idNotificacion_seq"'::regclass);


--
-- Data for Name: geminis_notificacion; Type: TABLE DATA; Schema: public; Owner: saradba
--

COPY geminis_notificacion ("idNotificacion", "idProceso", "idRemitente", "idDestinatario", asunto, descripcion, criticidad, "tipoMecanismo", "eMail", celular, sms, fecha, estado, "observacionEstado") FROM stdin;
\.


--
-- Name: geminis_notificacion_idNotificacion_seq; Type: SEQUENCE SET; Schema: public; Owner: saradba
--

SELECT pg_catalog.setval('"geminis_notificacion_idNotificacion_seq"', 1, false);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

